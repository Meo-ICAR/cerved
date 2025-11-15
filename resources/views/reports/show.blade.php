@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Report Details</span>
                    <div class="btn-group">
                        <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('reports.destroy', $report->id) }}" method="POST" class="ms-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this report?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <h5>Basic Information</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> {{ $report->id }}</p>
                                <p><strong>Name:</strong> {{ $report->name ?? 'N/A' }}</p>
                                <p><strong>PIVA:</strong> {{ $report->piva }}</p>
                                <p><strong>Is Races:</strong> {{ $report->israces ? 'Yes' : 'No' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Created At:</strong> {{ $report->created_at->format('Y-m-d H:i') }}</p>
                                <p><strong>Updated At:</strong> {{ $report->updated_at->format('Y-m-d H:i') }}</p>
                                @if($report->user)
                                    <p><strong>Created By:</strong> {{ $report->user->name }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($report->annotation)
                        <div class="mb-3">
                            <h5>Annotation</h5>
                            <hr>
                            <p>{{ $report->annotation }}</p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <h5>API Information</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>API Cerved Code:</strong> {{ $report->apicervedcode ?? 'N/A' }}</p>
                                <p><strong>API Activation:</strong> {{ $report->apiactivation ? $report->apiactivation->format('Y-m-d H:i') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($report->apicervedresponse)
                        <div class="mb-3">
                            <h5>API Response</h5>
                            <hr>
                            <pre class="bg-light p-3 rounded">{{ json_encode($report->apicervedresponse, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif

                    @if($report->mediaresponse)
                        <div class="mb-3">
                            <h5>Media Response</h5>
                            <hr>
                            <pre class="bg-light p-3 rounded">{{ json_encode($report->mediaresponse, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
