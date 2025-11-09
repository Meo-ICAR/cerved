@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Cerved Entity Search</h2>
                </div>

                <div class="card-body">
                    <form id="searchForm" method="GET" action="{{ route('cerved.entity.search') }}" class="mb-4">
                        <div class="form-row">
                            <div class="col-md-8 mb-3">
                                <label for="search">Search Term</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" required>
                                <small class="form-text text-muted">
                                    Enter a tax code, company name, or person name
                                </small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="type">Search Type</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>All</option>
                                    <option value="company" {{ request('type') === 'company' ? 'selected' : '' }}>Company</option>
                                    <option value="person" {{ request('type') === 'person' ? 'selected' : '' }}>Person</option>
                                </select>
                            </div>
                            <div class="col-md-1 d-flex align-items-end mb-3">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </div>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="debug" name="debug" value="1"
                                   {{ request()->has('debug') ? 'checked' : '' }}>
                            <input type="hidden" name="debug" value="0">
                            <label class="form-check-label" for="debug">Show debug information</label>
                        </div>
                    </form>

                    @if(isset($results) || $errors->any())
                        <div class="mt-4">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <h5>Error</h5>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(isset($results) && (isset($results['people_processed']) || isset($results['companies_processed'])))
                                <div class="alert alert-info">
                                    <h5>Search Results</h5>
                                    <p>People processed: {{ $results['people_processed'] ?? 0 }}</p>
                                    <p>Companies processed: {{ $results['companies_processed'] ?? 0 }}</p>
                                    
                                    @if(!empty($results['errors']))
                                        <div class="mt-3">
                                            <h6>Errors:</h6>
                                            <ul class="mb-0">
                                                @foreach($results['errors'] as $error)
                                                    <li>
                                                        <strong>{{ ucfirst($error['type'] ?? 'unknown') }}</strong> 
                                                        (ID: {{ $error['id'] ?? 'N/A' }}): 
                                                        {{ $error['error'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if(isset($debugInfo) && $debug)
                                <div class="mt-4">
                                    <h5>Debug Information</h5>
                                    <div class="card">
                                        <div class="card-body p-0">
                                            <pre class="mb-0 p-3" style="max-height: 300px; overflow: auto;">{{ json_encode($debugInfo, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any client-side interactivity here if needed
    });
</script>
@endpush
