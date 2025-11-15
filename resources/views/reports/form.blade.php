@if (isset($report))
    @php
        $action = route('reports.update', $report->id);
        $method = 'PUT';
    @endphp
    @section('title', 'Edit Report')
@else
    @php
        $action = route('reports.store');
        $method = 'POST';
        $report = new \App\Models\Report();
    @endphp
    @section('title', 'Create New Report')
@endif

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">@yield('title')</div>

                <div class="card-body">
                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @method($method)

                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $report->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="piva" class="form-label">PIVA</label>
                            <input type="text" class="form-control @error('piva') is-invalid @enderror"
                                   id="piva" name="piva" value="{{ old('piva', $report->piva) }}" required>
                            @error('piva')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="israces"
                                   name="israces" value="1" {{ old('israces', $report->israces) ? 'checked' : '' }}>
                            <label class="form-check-label" for="israces">Is Races</label>
                        </div>

                        <div class="mb-3">
                            <label for="annotation" class="form-label">Annotation</label>
                            <textarea class="form-control @error('annotation') is-invalid @enderror"
                                     id="annotation" name="annotation" rows="3">{{ old('annotation', $report->annotation) }}</textarea>
                            @error('annotation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="apicervedcode" class="form-label">API Cerved Code</label>
                            <input type="number" class="form-control @error('apicervedcode') is-invalid @enderror"
                                   id="apicervedcode" name="apicervedcode" value="{{ old('apicervedcode', $report->apicervedcode) }}">
                            @error('apicervedcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="apiactivation" class="form-label">API Activation</label>
                            <input type="datetime-local" class="form-control @error('apiactivation') is-invalid @enderror"
                                   id="apiactivation" name="apiactivation"
                                   value="{{ old('apiactivation', $report->apiactivation ? $report->apiactivation->format('Y-m-d\TH:i') : '') }}">
                            @error('apiactivation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($report->id) ? 'Update' : 'Create' }} Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
