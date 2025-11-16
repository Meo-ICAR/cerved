@php
    $action = route('reports.store');
    $method = 'POST';
    $report = $report ?? new \App\Models\Report();
@endphp

@extends('layouts.app')

@section('title', 'Crea Nuovo Report')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Inserisci P.IVA per il nuovo report</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ $action }}" id="reportForm">
                        @csrf
                        @method($method)

                        <div class="mb-4">
                            <label for="piva" class="form-label">Partita IVA <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control form-control-lg text-center @error('piva') is-invalid @enderror"
                                   id="piva"
                                   name="piva"
                                   value="{{ old('piva', $report->piva) }}"
                                   placeholder="Inserisci la partita IVA (11 cifre)"
                                   required
                                   maxlength="11"
                                   pattern="\d{11}"
                                   title="Inserisci esattamente 11 cifre"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                            @error('piva')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Inserisci la partita IVA dell'azienda (11 cifre numeriche)</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-arrow-left me-1"></i> Torna all'elenco
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-search me-1"></i> Cerca Dati
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

// In resources/views/reports/index.blade.php

// Find the print button section
@foreach($reports as $report)
    <tr>
        <!-- Other columns... -->
         <td>
            <a href="{{ asset('app/reports/' . $report->piva . '_FINAL.pdf') }}"
               class="btn btn-sm btn-outline-primary print-btn"
               target="_blank"
               data-pdf-path="{{ asset('app/reports/' . $report->piva . '_FINAL.pdf') }}"
               title="Stampa PDF">
                <i class="fas fa-print"></i> Stampa
            </a>
        </td>
    </tr>
@endforeach

@push('scripts')
<script>
document.querySelectorAll('.print-btn').forEach(button => {
    button.addEventListener('click', async function(e) {
        e.preventDefault();
        const pdfUrl = this.getAttribute('data-pdf-path');

        try {
            // Check if PDF exists
            const response = await fetch(pdfUrl, { method: 'HEAD' });
            if (response.ok) {
                window.open(pdfUrl, '_blank');
            } else {
                alert('Il PDF non è ancora stato generato.');
            }
        } catch (error) {
            console.error('Error checking PDF:', error);
            alert('Errore durante il caricamento del PDF.');
        }
    });
});
</script>
@endpush
@endsection
