<?php

namespace App\Http\Controllers\Api;

use App\Models\Bilancio;
use App\Models\Azienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BilancioController extends BaseApiController
{
    /**
     * Visualizza l'elenco dei bilanci di un'azienda
     */
    public function index($aziendaId)
    {
        $azienda = Azienda::find($aziendaId);
        
        if (!$azienda) {
            return $this->errorResponse('Azienda non trovata', 404);
        }

        $bilanci = $azienda->bilanci()
            ->orderBy('anno', 'desc')
            ->get();
            
        return $this->successResponse($bilanci);
    }

    /**
     * Salva un nuovo bilancio per un'azienda
     */
    public function store(Request $request, $aziendaId)
    {
        $azienda = Azienda::find($aziendaId);
        
        if (!$azienda) {
            return $this->errorResponse('Azienda non trovata', 404);
        }

        $validator = Validator::make($request->all(), [
            'anno' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'fatturato' => 'nullable|numeric|min:0',
            'ebitda' => 'nullable|numeric',
            'utile_netto' => 'nullable|numeric',
            'numero_dipendenti' => 'nullable|integer|min:0',
            'bilancio_completo' => 'nullable|array',
        ]);

        // Verifica che non esista già un bilancio per lo stesso anno
        $validator->after(function ($validator) use ($azienda, $request) {
            if ($azienda->bilanci()->where('anno', $request->anno)->exists()) {
                $validator->errors()->add('anno', 'Esiste già un bilancio per questo anno.');
            }
        });

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $bilancio = new Bilancio($request->all());
            $bilancio->azienda_id = $azienda->id;
            $bilancio->save();
            
            return $this->successResponse($bilancio, 'Bilancio creato con successo', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante la creazione del bilancio: ' . $e->getMessage());
        }
    }

    /**
     * Mostra i dettagli di un bilancio specifico
     */
    public function show($aziendaId, $bilancioId)
    {
        $bilancio = Bilancio::where('azienda_id', $aziendaId)
            ->where('id', $bilancioId)
            ->first();
            
        if (!$bilancio) {
            return $this->errorResponse('Bilancio non trovato', 404);
        }

        return $this->successResponse($bilancio);
    }

    /**
     * Aggiorna un bilancio esistente
     */
    public function update(Request $request, $aziendaId, $bilancioId)
    {
        $bilancio = Bilancio::where('azienda_id', $aziendaId)
            ->where('id', $bilancioId)
            ->first();
            
        if (!$bilancio) {
            return $this->errorResponse('Bilancio non trovato', 404);
        }

        $validator = Validator::make($request->all(), [
            'anno' => 'integer|min:1900|max:' . (date('Y') + 1),
            'fatturato' => 'nullable|numeric|min:0',
            'ebitda' => 'nullable|numeric',
            'utile_netto' => 'nullable|numeric',
            'numero_dipendenti' => 'nullable|integer|min:0',
            'bilancio_completo' => 'nullable|array',
        ]);

        // Verifica che non esista già un altro bilancio per lo stesso anno
        if ($request->has('anno') && $request->anno != $bilancio->anno) {
            $validator->after(function ($validator) use ($bilancio, $request) {
                if ($bilancio->azienda->bilanci()
                    ->where('anno', $request->anno)
                    ->where('id', '!=', $bilancio->id)
                    ->exists()) {
                    $validator->errors()->add('anno', 'Esiste già un altro bilancio per questo anno.');
                }
            });
        }

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $bilancio->update($request->all());
            return $this->successResponse($bilancio, 'Bilancio aggiornato con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'aggiornamento del bilancio: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un bilancio
     */
    public function destroy($aziendaId, $bilancioId)
    {
        $bilancio = Bilancio::where('azienda_id', $aziendaId)
            ->where('id', $bilancioId)
            ->first();
            
        if (!$bilancio) {
            return $this->errorResponse('Bilancio non trovato', 404);
        }

        try {
            $bilancio->delete();
            return $this->successResponse(null, 'Bilancio eliminato con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'eliminazione del bilancio: ' . $e->getMessage());
        }
    }
}
