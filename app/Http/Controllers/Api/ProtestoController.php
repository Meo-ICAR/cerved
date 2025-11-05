<?php

namespace App\Http\Controllers\Api;

use App\Models\Protesto;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProtestoController extends BaseApiController
{
    /**
     * Visualizza l'elenco dei protesti
     */
    public function index(Request $request)
    {
        $query = Protesto::with('persona');

        // Filtri di ricerca
        if ($request->has('persona_id')) {
            $query->where('persona_id', $request->persona_id);
        }

        if ($request->has('tipo_protesto')) {
            $query->where('tipo_protesto', $request->tipo_protesto);
        }

        if ($request->has('data_da')) {
            $query->whereDate('data_evento', '>=', $request->data_da);
        }

        if ($request->has('data_a')) {
            $query->whereDate('data_evento', '<=', $request->data_a);
        }

        if ($request->has('importo_min')) {
            $query->where('importo', '>=', $request->importo_min);
        }

        if ($request->has('importo_max')) {
            $query->where('importo', '<=', $request->importo_max);
        }

        if ($request->has('camera_commercio')) {
            $query->where('camera_commercio', 'like', '%' . $request->camera_commercio . '%');
        }

        // Ordinamento
        $sortField = $request->input('sort_by', 'data_evento');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginazione
        $perPage = $request->input('per_page', 15);
        $protesti = $query->paginate($perPage);

        return $this->successResponse($protesti);
    }

    /**
     * Salva un nuovo protesto
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'persona_id' => 'required|exists:persone,id',
            'tipo_protesto' => 'required|string|max:50',
            'data_evento' => 'required|date',
            'importo' => 'nullable|numeric|min:0',
            'camera_commercio' => 'nullable|string|max:100',
            'dati_protesto_completi' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        // Verifica che la persona esista
        $persona = Persona::find($request->persona_id);
        if (!$persona) {
            return $this->errorResponse('Persona non trovata', 404);
        }

        try {
            $protesto = Protesto::create($request->all());
            return $this->successResponse($protesto, 'Protesto registrato con successo', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante la registrazione del protesto: ' . $e->getMessage());
        }
    }

    /**
     * Mostra i dettagli di un protesto specifico
     */
    public function show($id)
    {
        $protesto = Protesto::with('persona')->find($id);
            
        if (!$protesto) {
            return $this->errorResponse('Protesto non trovato', 404);
        }

        return $this->successResponse($protesto);
    }

    /**
     * Aggiorna un protesto esistente
     */
    public function update(Request $request, $id)
    {
        $protesto = Protesto::find($id);
            
        if (!$protesto) {
            return $this->errorResponse('Protesto non trovato', 404);
        }

        $validator = Validator::make($request->all(), [
            'persona_id' => 'exists:persone,id',
            'tipo_protesto' => 'string|max:50',
            'data_evento' => 'date',
            'importo' => 'nullable|numeric|min:0',
            'camera_commercio' => 'nullable|string|max:100',
            'dati_protesto_completi' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $protesto->update($request->all());
            return $this->successResponse($protesto, 'Protesto aggiornato con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'aggiornamento del protesto: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un protesto
     */
    public function destroy($id)
    {
        $protesto = Protesto::find($id);
            
        if (!$protesto) {
            return $this->errorResponse('Protesto non trovato', 404);
        }

        try {
            $protesto->delete();
            return $this->successResponse(null, 'Protesto eliminato con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'eliminazione del protesto: ' . $e->getMessage());
        }
    }

    /**
     * Ottieni i protesti di una persona
     */
    public function protestiPersona($personaId, Request $request)
    {
        $persona = Persona::find($personaId);
            
        if (!$persona) {
            return $this->errorResponse('Persona non trovata', 404);
        }

        $query = $persona->protesti();

        // Filtri aggiuntivi
        if ($request->has('tipo_protesto')) {
            $query->where('tipo_protesto', $request->tipo_protesto);
        }

        if ($request->has('anno')) {
            $query->whereYear('data_evento', $request->anno);
        }

        if ($request->has('importo_min')) {
            $query->where('importo', '>=', $request->importo_min);
        }

        if ($request->has('importo_max')) {
            $query->where('importo', '<=', $request->importo_max);
        }

        // Ordinamento
        $sortField = $request->input('sort_by', 'data_evento');
        $sortDirection = $request->input('sort_direction', 'desc');
        $protesti = $query->orderBy($sortField, $sortDirection)->get();

        return $this->successResponse([
            'persona' => $persona->only(['id', 'codice_fiscale', 'nome', 'cognome']),
            'protesti' => $protesti,
            'totale_importo' => $protesti->sum('importo'),
            'conteggio' => $protesti->count()
        ]);
    }

    /**
     * Statistiche sui protesti
     */
    public function statistiche(Request $request)
    {
        $query = Protesto::query();

        // Filtri per data
        if ($request->has('anno')) {
            $query->whereYear('data_evento', $request->anno);
        } elseif ($request->has('data_da') && $request->has('data_a')) {
            $query->whereBetween('data_evento', [$request->data_da, $request->data_a]);
        } else {
            // Ultimi 12 mesi di default
            $query->where('data_evento', '>=', now()->subYear());
        }

        // Statistiche per tipo di protesto
        $perTipo = (clone $query)
            ->selectRaw('tipo_protesto, COUNT(*) as conteggio, SUM(importo) as importo_totale')
            ->groupBy('tipo_protesto')
            ->get();

        // Statistiche mensili
        $mensili = (clone $query)
            ->selectRaw('YEAR(data_evento) as anno, MONTH(data_evento) as mese, COUNT(*) as conteggio, SUM(importo) as importo_totale')
            ->groupBy('anno', 'mese')
            ->orderBy('anno')
            ->orderBy('mese')
            ->get();

        // Statistiche per camera di commercio
        $perCamera = (clone $query)
            ->whereNotNull('camera_commercio')
            ->selectRaw('camera_commercio, COUNT(*) as conteggio, SUM(importo) as importo_totale')
            ->groupBy('camera_commercio')
            ->orderBy('conteggio', 'desc')
            ->limit(10)
            ->get();

        return $this->successResponse([
            'totali' => [
                'conteggio' => $query->count(),
                'importo_totale' => $query->sum('importo'),
                'importo_medio' => $query->avg('importo')
            ],
            'per_tipo' => $perTipo,
            'mensili' => $mensili,
            'per_camera' => $perCamera
        ]);
    }
}
