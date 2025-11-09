<?php

namespace App\Http\Controllers\Api;

use App\Models\Carica;
use App\Models\Person;
use App\Models\Azienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CaricaController extends BaseApiController
{
    /**
     * Visualizza l'elenco delle cariche
     */
    public function index(Request $request)
    {
        $query = Carica::with(['persona', 'azienda']);

        // Filtri di ricerca
        if ($request->has('persona_id')) {
            $query->where('persona_id', $request->persona_id);
        }

        if ($request->has('azienda_id')) {
            $query->where('azienda_id', $request->azienda_id);
        }

        if ($request->has('tipo_carica')) {
            $query->where('tipo_carica', 'like', $request->tipo_carica . '%');
        }

        if ($request->has('attive')) {
            $query->whereNull('data_fine_carica');
        }

        // Ordinamento
        $sortField = $request->input('sort_by', 'data_inizio_carica');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginazione
        $perPage = $request->input('per_page', 15);
        $cariche = $query->paginate($perPage);

        return $this->successResponse($cariche);
    }

    /**
     * Salva una nuova carica
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'persona_id' => 'required|exists:persone,id',
            'azienda_id' => 'required|exists:aziende,id',
            'tipo_carica' => 'required|string|max:100',
            'descrizione_carica' => 'nullable|string',
            'data_inizio_carica' => 'nullable|date',
            'data_fine_carica' => 'nullable|date|after_or_equal:data_inizio_carica',
            'dati_carica_completi' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        // Verifica che la persona esista
        $persona = Person::find($request->persona_id);
        if (!$persona) {
            return $this->errorResponse('Persona non trovata', 404);
        }

        // Verifica che l'azienda esista
        $azienda = Azienda::find($request->azienda_id);
        if (!$azienda) {
            return $this->errorResponse('Azienda non trovata', 404);
        }

        try {
            $carica = Carica::create($request->all());
            return $this->successResponse($carica, 'Carica creata con successo', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante la creazione della carica: ' . $e->getMessage());
        }
    }

    /**
     * Mostra i dettagli di una carica specifica
     */
    public function show($id)
    {
        $carica = Carica::with(['persona', 'azienda'])->find($id);
            
        if (!$carica) {
            return $this->errorResponse('Carica non trovata', 404);
        }

        return $this->successResponse($carica);
    }

    /**
     * Aggiorna una carica esistente
     */
    public function update(Request $request, $id)
    {
        $carica = Carica::find($id);
            
        if (!$carica) {
            return $this->errorResponse('Carica non trovata', 404);
        }

        $validator = Validator::make($request->all(), [
            'persona_id' => 'exists:persone,id',
            'azienda_id' => 'exists:aziende,id',
            'tipo_carica' => 'string|max:100',
            'descrizione_carica' => 'nullable|string',
            'data_inizio_carica' => 'nullable|date',
            'data_fine_carica' => 'nullable|date|after_or_equal:data_inizio_carica',
            'dati_carica_completi' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $carica->update($request->all());
            return $this->successResponse($carica, 'Carica aggiornata con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'aggiornamento della carica: ' . $e->getMessage());
        }
    }

    /**
     * Termina una carica (imposta data_fine_carica a oggi)
     */
    public function termina($id)
    {
        $carica = Carica::find($id);
            
        if (!$carica) {
            return $this->errorResponse('Carica non trovata', 404);
        }

        if ($carica->data_fine_carica) {
            return $this->errorResponse('La carica è già terminata', 400);
        }

        try {
            $carica->update([
                'data_fine_carica' => now()->format('Y-m-d')
            ]);
            
            return $this->successResponse($carica, 'Carica terminata con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante la terminazione della carica: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una carica
     */
    public function destroy($id)
    {
        $carica = Carica::find($id);
            
        if (!$carica) {
            return $this->errorResponse('Carica non trovata', 404);
        }

        try {
            $carica->delete();
            return $this->successResponse(null, 'Carica eliminata con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'eliminazione della carica: ' . $e->getMessage());
        }
    }

    /**
     * Ottieni le cariche attive di una persona
     */
    public function caricheAttivePersona($personaId)
    {
        $persona = Person::find($personaId);
            
        if (!$persona) {
            return $this->errorResponse('Persona non trovata', 404);
        }

        $cariche = $persona->cariche()
            ->whereNull('data_fine_carica')
            ->with('azienda')
            ->get();

        return $this->successResponse($cariche);
    }

    /**
     * Ottieni le cariche attive di un'azienda
     */
    public function caricheAzienda($aziendaId, Request $request)
    {
        $azienda = Azienda::find($aziendaId);
            
        if (!$azienda) {
            return $this->errorResponse('Azienda non trovata', 404);
        }

        $query = $azienda->cariche()
            ->with('persona');

        // Filtro per cariche attive
        if ($request->has('attive') && $request->attive) {
            $query->whereNull('data_fine_carica');
        }

        // Filtro per tipo di carica
        if ($request->has('tipo_carica')) {
            $query->where('tipo_carica', $request->tipo_carica);
        }

        $cariche = $query->get();

        return $this->successResponse($cariche);
    }
}
