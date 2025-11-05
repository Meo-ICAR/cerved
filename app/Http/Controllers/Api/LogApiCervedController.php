<?php

namespace App\Http\Controllers\Api;

use App\Models\LogApiCerved;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LogApiCervedController extends BaseApiController
{
    /**
     * Visualizza l'elenco dei log
     */
    public function index(Request $request)
    {
        $query = LogApiCerved::query()
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc');

        // Filtri di ricerca
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('endpoint')) {
            $query->where('endpoint_chiamato', 'like', '%' . $request->endpoint . '%');
        }

        if ($request->has('partita_iva')) {
            $query->where('partita_iva_input', $request->partita_iva);
        }

        if ($request->has('status_code')) {
            $query->where('status_code_risposta', $request->status_code);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Paginazione
        $perPage = $request->input('per_page', 20);
        $logs = $query->paginate($perPage);

        return $this->successResponse($logs);
    }

    /**
     * Salva un nuovo log
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'endpoint_chiamato' => 'required|string|max:255',
            'partita_iva_input' => 'nullable|string|size:11',
            'status_code_risposta' => 'required|integer|min:100|max:599',
            'request_payload' => 'nullable|string',
            'response_payload' => 'nullable|string',
            'costo_chiamata' => 'nullable|numeric|min:0',
            'user_id' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $logData = $request->only([
                'endpoint_chiamato',
                'partita_iva_input',
                'status_code_risposta',
                'request_payload',
                'response_payload',
                'costo_chiamata',
                'user_id'
            ]);

            // Se non è specificato un user_id e c'è un utente autenticato, usiamo quello
            if (!isset($logData['user_id']) && Auth::check()) {
                $logData['user_id'] = Auth::id();
            }

            $log = LogApiCerved::create($logData);
            
            return $this->successResponse($log, 'Log creato con successo', 201);
        } catch (\Exception $e) {
            // Non vogliamo fallire l'operazione principale a causa del log
            // Quindi logghiamo l'errore ma restituiamo successo
            \Log::error('Errore durante la creazione del log API Cerved: ' . $e->getMessage());
            return $this->successResponse(null, 'Operazione completata ma il log non è stato salvato', 201);
        }
    }

    /**
     * Mostra i dettagli di un log specifico
     */
    public function show($id)
    {
        $log = LogApiCerved::with('user:id,name,email')->find($id);
            
        if (!$log) {
            return $this->errorResponse('Log non trovato', 404);
        }

        return $this->successResponse($log);
    }

    /**
     * Aggiorna un log esistente (raro, ma utile per correzioni)
     */
    public function update(Request $request, $id)
    {
        $log = LogApiCerved::find($id);
            
        if (!$log) {
            return $this->errorResponse('Log non trovato', 404);
        }

        $validator = Validator::make($request->all(), [
            'endpoint_chiamato' => 'string|max:255',
            'partita_iva_input' => 'nullable|string|size:11',
            'status_code_risposta' => 'integer|min:100|max:599',
            'request_payload' => 'nullable|string',
            'response_payload' => 'nullable|string',
            'costo_chiamata' => 'nullable|numeric|min:0',
            'user_id' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Errore di validazione', 422, $validator->errors());
        }

        try {
            $log->update($request->only([
                'endpoint_chiamato',
                'partita_iva_input',
                'status_code_risposta',
                'request_payload',
                'response_payload',
                'costo_chiamata',
                'user_id'
            ]));
            
            return $this->successResponse($log, 'Log aggiornato con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'aggiornamento del log: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un log
     */
    public function destroy($id)
    {
        $log = LogApiCerved::find($id);
            
        if (!$log) {
            return $this->errorResponse('Log non trovato', 404);
        }

        try {
            $log->delete();
            return $this->successResponse(null, 'Log eliminato con successo');
        } catch (\Exception $e) {
            return $this->errorResponse('Errore durante l\'eliminazione del log: ' . $e->getMessage());
        }
    }

    /**
     * Statistiche sulle chiamate API
     */
    public function stats(Request $request)
    {
        $query = LogApiCerved::query();

        // Filtri per data
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filtri aggiuntivi
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('endpoint')) {
            $query->where('endpoint_chiamato', 'like', '%' . $request->endpoint . '%');
        }

        // Statistiche di base
        $stats = [
            'total_calls' => (clone $query)->count(),
            'successful_calls' => (clone $query)->whereBetween('status_code_risposta', [200, 299])->count(),
            'error_calls' => (clone $query)->where('status_code_risposta', '>=', 400)->count(),
            'total_cost' => (clone $query)->sum('costo_chiamata'),
            'avg_response_time' => (clone $query)->avg('tempo_risposta_ms') ?? 0,
        ];

        // Chiamate per endpoint
        $endpoints = (clone $query)
            ->selectRaw('endpoint_chiamato, COUNT(*) as count, AVG(tempo_risposta_ms) as avg_time, SUM(costo_chiamata) as total_cost')
            ->groupBy('endpoint_chiamato')
            ->orderBy('count', 'desc')
            ->get();

        // Chiamate per codice di stato
        $statusCodes = (clone $query)
            ->selectRaw('status_code_risposta, COUNT(*) as count')
            ->groupBy('status_code_risposta')
            ->orderBy('count', 'desc')
            ->get();

        // Chiamate per utente
        $users = (clone $query)
            ->join('users', 'log_api_cerved.user_id', '=', 'users.id')
            ->selectRaw('users.id, users.name, users.email, COUNT(*) as count, SUM(costo_chiamata) as total_cost')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('count', 'desc')
            ->get();

        return $this->successResponse([
            'overview' => $stats,
            'endpoints' => $endpoints,
            'status_codes' => $statusCodes,
            'users' => $users,
        ]);
    }
}
