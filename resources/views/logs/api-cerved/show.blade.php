@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">API Request Details</h1>
            <a href="{{ route('logs.api-cerved.index') }}" class="text-indigo-600 hover:text-indigo-900">
                &larr; Back to Logs
            </a>
        </div>
        <div class="mt-2 text-sm text-gray-500">
            {{ $log->created_at->format('F j, Y, g:i a') }} ({{ $log->created_at->diffForHumans() }})
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Request Information</h2>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Endpoint</h3>
                    <p class="mt-1 text-sm text-gray-900 break-all">{{ $log->endpoint_chiamato }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Method</h3>
                    <p class="mt-1">
                        @php
                            $methodColor = [
                                'GET' => 'bg-blue-100 text-blue-800',
                                'POST' => 'bg-green-100 text-green-800',
                                'PUT' => 'bg-yellow-100 text-yellow-800',
                                'DELETE' => 'bg-red-100 text-red-800',
                            ][$log->method] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $methodColor }}">
                            {{ $log->method }}
                        </span>
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status Code</h3>
                    <p class="mt-1">
                        @php
                            $statusColor = $log->status_code_risposta >= 400 ? 'bg-red-100 text-red-800' : 
                                        ($log->status_code_risposta >= 300 ? 'bg-yellow-100 text-yellow-800' : 
                                        'bg-green-100 text-green-800');
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                            {{ $log->status_code_risposta }}
                        </span>
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Partita IVA</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $log->partita_iva_input ?? 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">User</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $log->user ? $log->user->name : 'System' }}
                        @if($log->user)
                            <span class="text-gray-500">({{ $log->user->email }})</span>
                        @endif
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">IP Address</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $log->ip_address }}</p>
                </div>
                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">User Agent</h3>
                    <p class="mt-1 text-sm text-gray-900 break-all">{{ $log->user_agent }}</p>
                </div>
                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Execution Time</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ number_format($log->execution_time_ms, 2) }} ms
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Request Data</h2>
            </div>
            <div class="p-6">
                @if($requestData && count($requestData) > 0)
                    <pre class="bg-gray-50 p-4 rounded-md text-xs overflow-auto max-h-96">@json($requestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)</pre>
                @else
                    <p class="text-sm text-gray-500">No request data available.</p>
                @endif
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Response Data</h2>
            </div>
            <div class="p-6">
                @if($responseData && count($responseData) > 0)
                    <pre class="bg-gray-50 p-4 rounded-md text-xs overflow-auto max-h-96">@json($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)</pre>
                @else
                    <p class="text-sm text-gray-500">No response data available.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    pre {
        white-space: pre-wrap;
        word-wrap: break-word;
    }
</style>
@endsection
