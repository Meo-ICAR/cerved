<?php

namespace App\Http\Controllers\Api;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PersonaController extends BaseApiController
{
    /**
     * Visualizza l'elenco delle persone
     */
    public function index(Request $request)
    {
        $query = Persona::query();

        // Filtri di ricerca
        if ($request->has('codice_fiscale')) {
            $query->where('codice_fiscale', $request->codice_fiscale);
        }

        if ($request->has('cognome')) {
            $query->where('cognome', 'like', $request->cognome . '%');
        }

        if ($request->has('nome')) {
            $query->where('nome', 'like', $request->nome . '%');
        }

        if ($request->has('data_nascita')) {
            $query->whereDate('data_nascita', $request->data_nascita);
        }

        // Ordinamento
        $sortField = $request->input('sort_by', 'cognome');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginazione
        $perPage = $request->input('per_page', 15);
        $persone = $query->paginate($perPage);

        return $this->successResponse($persone);
    }

    /**
     * Salva una nuova persona
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codice_fiscale' => 'required|string|size:16|unique:persone',
            'nome' => 'nullable|string|max:100',
            'cognome' => 'nullable|string|max:100',
            'data_nascita' => 'nullable|date',
            'comune_nascita' => 'nullable|string|max:100',
            'provincia_nascita' => 'nullable|string|size:2',
            'dati_anagrafici_completi' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $persona = Persona::create($request->all());
            return $this->successResponse($persona, 'Persona creata con successo', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante la creazione della persona: ' . $e->getMessage());
        }
    }

    /**
     * Mostra i dettagli di una persona specifica
     */
    public function show($id)
    {
        $persona = Persona::with(['cariche', 'aziende', 'protesti'])->find($id);
            
        if (!$persona) {
            return $this->errorResponse('Persona non trovata', 404);
        }

        return $this->successResponse($persona);
    }

    /**
     * Aggiorna una persona esistente
     */
    public function update(Request $request, $id)
    {
        $persona = Persona::find($id);
            
        if (!$persona) {
            return $this->errorResponse('Persona non trovata', 404);
        }

        $validator = Validator::make($request->all(), [
            'codice_fiscale' => 'string|size:16|unique:persone,codice_fiscale,' . $id,
            'nome' => 'nullable|string|max:100',
            'cognome' => 'nullable|string|max:100',
            'data_nascita' => 'nullable|date',
            'comune_nascita' => 'nullable|string|max:100',
            'provincia_nascita' => 'nullable|string|size:2',
            'dati_anagrafici_completi' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $persona->update($request->all());
            return $this->successResponse($persona, 'Persona aggiornata con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'aggiornamento della persona: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una persona
     */
    public function destroy($id)
    {
        $persona = Persona::find($id);
            
        if (!$persona) {
            return $this->errorResponse('Persona non trovata', 404);
        }

        try {
            // Inizia una transazione per assicurare l'integrità dei dati
            DB::beginTransaction();
            
            // Elimina le relazioni prima di eliminare la persona
            $persona->cariche()->delete();
            $persona->protesti()->delete();
            
            // Elimina la persona
            $persona->delete();
            
            DB::commit();
            
            return $this->successResponse(null, 'Persona eliminata con successo');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Errore durante l\'eliminazione della persona: ' . $e->getMessage());
        }
    }

    /**
     * Cerca una persona per codice fiscale
     */
    public function cercaPerCodiceFiscale($codiceFiscale)
    {
        $persona = Persona::where('codice_fiscale', $codiceFiscale)
            ->with(['cariche', 'aziende', 'protesti'])
            ->first();
            
        if (!$persona) {
            return $this->errorResponse('Nessuna persona trovata con questo codice fiscale', 404);
        }

        return $this->successResponse($persona);
    }

    /**
     * Aggiorna i dati di una persona da Cerved
     */
    public function aggiornaDaCerved($id)
    {
        $persona = Persona::find($id);
            
        if (!$persona) {
            return $this->errorResponse('Persona non trovata', 404);
        }

        try {
            // Qui andrebbe la logica per richiedere i dati a Cerved
            // Per ora simuliamo un aggiornamento
            $persona->update([
                'ultimo_aggiornamento_cerved' => now(),
                // Altri campi aggiornati da Cerved
            ]);
            
            return $this->successResponse($persona, 'Dati aggiornati da Cerved con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'aggiornamento da Cerved: ' . $e->getMessage());
        }
    }
}
