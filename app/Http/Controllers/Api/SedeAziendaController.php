<?php

namespace App\Http\Controllers\Api;

use App\Models\SedeAzienda;
use App\Models\Azienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SedeAziendaController extends BaseApiController
{
    /**
     * Visualizza l'elenco delle sedi di un'azienda
     */
    public function index($aziendaId)
    {
        $azienda = Azienda::find($aziendaId);
        
        if (!$azienda) {
            return $this->errorResponse('Azienda non trovata', 404);
        }

        $sedi = $azienda->sedi()->get();
        return $this->successResponse($sedi);
    }

    /**
     * Crea una nuova sede per un'azienda
     */
    public function store(Request $request, $aziendaId)
    {
        $azienda = Azienda::find($aziendaId);
        
        if (!$azienda) {
            return $this->errorResponse('Azienda non trovata', 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo_sede' => 'required|string|max:50',
            'indirizzo' => 'required|string|max:255',
            'cap' => 'required|string|size:5',
            'comune' => 'required|string|max:100',
            'provincia' => 'required|string|size:2',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $sede = new SedeAzienda($request->only([
                'tipo_sede',
                'indirizzo',
                'cap',
                'comune',
                'provincia'
            ]));
            
            $sede->azienda_id = $azienda->id;
            $sede->save();
            
            return $this->successResponse($sede, 'Sede creata con successo', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante la creazione della sede: ' . $e->getMessage());
        }
    }

    /**
     * Mostra i dettagli di una specifica sede
     */
    public function show($aziendaId, $sedeId)
    {
        $sede = SedeAzienda::where('azienda_id', $aziendaId)
            ->where('id', $sedeId)
            ->first();
            
        if (!$sede) {
            return $this->errorResponse('Sede non trovata', 404);
        }

        return $this->successResponse($sede);
    }

    /**
     * Aggiorna una sede esistente
     */
    public function update(Request $request, $aziendaId, $sedeId)
    {
        $sede = SedeAzienda::where('azienda_id', $aziendaId)
            ->where('id', $sedeId)
            ->first();
            
        if (!$sede) {
            return $this->errorResponse('Sede non trovata', 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo_sede' => 'string|max:50',
            'indirizzo' => 'string|max:255',
            'cap' => 'string|size:5',
            'comune' => 'string|max:100',
            'provincia' => 'string|size:2',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $sede->update($request->only([
                'tipo_sede',
                'indirizzo',
                'cap',
                'comune',
                'provincia'
            ]));
            
            return $this->successResponse($sede, 'Sede aggiornata con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'aggiornamento della sede: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una sede
     */
    public function destroy($aziendaId, $sedeId)
    {
        $sede = SedeAzienda::where('azienda_id', $aziendaId)
            ->where('id', $sedeId)
            ->first();
            
        if (!$sede) {
            return $this->errorResponse('Sede non trovata', 404);
        }

        try {
            $sede->delete();
            return $this->successResponse(null, 'Sede eliminata con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'eliminazione della sede: ' . $e->getMessage());
        }
    }
}
