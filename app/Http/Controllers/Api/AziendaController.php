<?php

namespace App\Http\Controllers\Api;

use App\Models\Azienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AziendaController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Azienda::query();

        // Filtri di ricerca
        if ($request->has('ragione_sociale')) {
            $query->where('ragione_sociale', 'like', '%' . $request->ragione_sociale . '%');
        }

        if ($request->has('partita_iva')) {
            $query->where('partita_iva', $request->partita_iva);
        }

        if ($request->has('codice_fiscale')) {
            $query->where('codice_fiscale', $request->codice_fiscale);
        }

        // Ordinamento
        $sortField = $request->input('sort_by', 'ragione_sociale');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginazione
        $perPage = $request->input('per_page', 15);
        $aziende = $query->with(['sedi', 'bilanci', 'scoring'])->paginate($perPage);

        return $this->successResponse($aziende);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'partita_iva' => 'required|string|size:11|unique:aziende',
            'codice_fiscale' => 'nullable|string|size:16|unique:aziende',
            'ragione_sociale' => 'required|string|max:255',
            'natura_giuridica' => 'nullable|string|max:100',
            'stato_attivita' => 'nullable|string|max:50',
            'codice_ateco' => 'nullable|string|max:10',
            'provincia_rea' => 'nullable|string|size:2',
            'dati_anagrafici_completi' => 'nullable|array',
            'dati_societa_controllanti' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $azienda = Azienda::create($request->all());
            return $this->successResponse($azienda, 'Azienda creata con successo', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante la creazione dell\'azienda: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $azienda = Azienda::with(['sedi', 'bilanci', 'scoring'])->find($id);
        
        if (!$azienda) {
            return $this->errorResponse('Azienda non trovata', 404);
        }

        return $this->successResponse($azienda);
    }

    public function update(Request $request, $id)
    {
        $azienda = Azienda::find($id);
        
        if (!$azienda) {
            return $this->errorResponse('Azienda non trovata', 404);
        }

        $validator = Validator::make($request->all(), [
            'partita_iva' => [
                'string',
                'size:11',
                Rule::unique('aziende')->ignore($azienda->id)
            ],
            'codice_fiscale' => [
                'nullable',
                'string',
                'size:16',
                Rule::unique('aziende')->ignore($azienda->id)
            ],
            'ragione_sociale' => 'string|max:255',
            'natura_giuridica' => 'nullable|string|max:100',
            'stato_attivita' => 'nullable|string|max:50',
            'codice_ateco' => 'nullable|string|max:10',
            'provincia_rea' => 'nullable|string|size:2',
            'dati_anagrafici_completi' => 'nullable|array',
            'dati_societa_controllanti' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $azienda->update($request->all());
            return $this->successResponse($azienda, 'Azienda aggiornata con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'aggiornamento dell\'azienda: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $azienda = Azienda::find($id);
        
        if (!$azienda) {
            return $this->errorResponse('Azienda non trovata', 404);
        }

        try {
            $azienda->delete();
            return $this->successResponse(null, 'Azienda eliminata con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'eliminazione dell\'azienda: ' . $e->getMessage());
        }
    }
}
