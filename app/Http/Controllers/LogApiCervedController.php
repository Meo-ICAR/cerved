<?php

namespace App\Http\Controllers;

use App\Models\LogApiCerved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogApiCervedController extends Controller
{
    /**
     * Display a listing of the logs.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = LogApiCerved::with('user')
            ->orderBy('created_at', 'desc');

        // Add search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('endpoint_chiamato', 'like', "%{$search}%")
                  ->orWhere('method', 'like', "%{$search}%")
                  ->orWhere('status_code_risposta', 'like', "%{$search}%")
                  ->orWhere('partita_iva_input', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(25);

        return view('logs.api-cerved.index', compact('logs'));
    }

    /**
     * Display the specified log.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $log = LogApiCerved::with('user')->findOrFail($id);
        
        // Format request and response data for better display
        $requestData = is_string($log->request_body) ? json_decode($log->request_body, true) : $log->request_body;
        $responseData = is_string($log->response_body) ? json_decode($log->response_body, true) : $log->response_body;
        
        return view('logs.api-cerved.show', [
            'log' => $log,
            'requestData' => $requestData,
            'responseData' => $responseData
        ]);
    }
}
