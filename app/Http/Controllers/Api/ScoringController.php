<?php

namespace App\Http\Controllers\Api;

use App\Models\Scoring;
use App\Models\Azienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ScoringController extends BaseApiController
{
    public function index($aziendaId)
    {
        $azienda = Azienda::find($aziendaId);
        
        if (!$azienda) {
            return $this->errorResponse('Azienda non trovata', 404);
        }

        $scorings = $azienda->scoring()
            ->orderBy('data_elaborazione', 'desc')
            ->get();
            
        return $this->successResponse($scorings);
    }

    public function store(Request $request, $aziendaId)
    {
        $azienda = Azienda::find($aziendaId);
        
        if (!$azienda) {
            return $this->errorResponse('Azienda non trovata', 404);
        }

        $validator = Validator::make($request->all(), [
            'data_elaborazione' => 'required|date',
            'punteggio' => 'required|integer|min:0|max:100',
            'classe_di_rischio' => 'required|string|max:10',
            'probabile_fallimento' => 'nullable|numeric|between:0,100',
            'limite_credito_consigliato' => 'nullable|numeric|min:0',
            'fattori_rischio' => 'nullable|array',
            'dettagli_analisi' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $scoring = new Scoring($request->all());
            $scoring->azienda_id = $azienda->id;
            
            // Se non è stata fornita la data di elaborazione, usa la data corrente
            if (empty($scoring->data_elaborazione)) {
                $scoring->data_elaborazione = Carbon::now();
            }
            
            $scoring->save();
            
            return $this->successResponse($scoring, 'Scoring creato con successo', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante la creazione dello scoring: ' . $e->getMessage());
        }
    }

    public function show($aziendaId, $scoringId)
    {
        $scoring = Scoring::where('azienda_id', $aziendaId)
            ->where('id', $scoringId)
            ->first();
            
        if (!$scoring) {
            return $this->errorResponse('Scoring non trovato', 404);
        }

        return $this->successResponse($scoring);
    }

    public function update(Request $request, $aziendaId, $scoringId)
    {
        $scoring = Scoring::where('azienda_id', $aziendaId)
            ->where('id', $scoringId)
            ->first();
            
        if (!$scoring) {
            return $this->errorResponse('Scoring non trovato', 404);
        }

        $validator = Validator::make($request->all(), [
            'data_elaborazione' => 'date',
            'punteggio' => 'integer|min:0|max:100',
            'classe_di_rischio' => 'string|max:10',
            'probabile_fallimento' => 'nullable|numeric|between:0,100',
            'limite_credito_consigliato' => 'nullable|numeric|min:0',
            'fattori_rischio' => 'nullable|array',
            'dettagli_analisi' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $scoring->update($request->all());
            return $this->successResponse($scoring, 'Scoring aggiornato con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'aggiornamento dello scoring: ' . $e->getMessage());
        }
    }

    public function destroy($aziendaId, $scoringId)
    {
        $scoring = Scoring::where('azienda_id', $aziendaId)
            ->where('id', $scoringId)
            ->first();
            
        if (!$scoring) {
            return $this->errorResponse('Scoring non trovato', 404);
        }

        try {
            $scoring->delete();
            return $this->successResponse(null, 'Scoring eliminato con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'eliminazione dello scoring: ' . $e->getMessage());
        }
    }
    
    public function ultimo($aziendaId)
    {
        $azienda = Azienda::find($aziendaId);
        
        if (!$azienda) {
            return $this->errorResponse('Azienda non trovata', 404);
        }

        $ultimoScoring = $azienda->scoring()
            ->orderBy('data_elaborazione', 'desc')
            ->first();
            
        if (!$ultimoScoring) {
            return $this->errorResponse('Nessuno scoring trovato per questa azienda', 404);
        }

        return $this->successResponse($ultimoScoring);
    }
}
