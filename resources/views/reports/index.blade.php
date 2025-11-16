@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col">
            <h1>Reports</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('reports.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nuovo Report
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cerca per nome o P.IVA..."
                               value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i> Cerca
                        </button>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction', 'asc') === 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                Nome
                                @if(request('sort') === 'name')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'piva', 'direction' => request('direction', 'asc') === 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                P.IVA
                                @if(request('sort') === 'piva')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'valore', 'direction' => request('direction', 'desc') === 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                Score
                                @if(request('sort') === 'valore')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'categoria_descrizione', 'direction' => request('direction', 'asc') === 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                Categoria
                                @if(request('sort') === 'categoria_descrizione')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'updated_at', 'direction' => request('direction', 'desc') === 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                Ultimo Aggiornamento
                                @if(request('sort') === 'updated_at' || !request('sort'))
                                    <i class="fas fa-sort-{{ (request('direction', 'desc') === 'desc') ? 'down' : 'up' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </a>
                        </th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td>{{ $report->name ?? 'N/A' }}</td>
                            <td>{{ $report->piva }}</td>
                            <td>{{ $report->valore ?? 'N/A' }}</td>
                            <td>{{ $report->categoria_descrizione ?? 'N/A' }}</td>
                            <td>{{ $report->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="text-nowrap">
                                <div class="btn-group" role="group">

                                    <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-sm btn-warning" title="Modifica">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                       @if(!empty($report->annotation))
                                        <a href="{{ asset('app/reports/' . $report->piva . '_FINAL.pdf') }}"
               class="btn btn-sm btn-outline-primary print-btn"
               target="_blank"
               data-pdf-path="{{ asset('app/reports/' . $report->piva . '_FINAL.pdf') }}"
               title="Stampa PDF">
                <i class="fas fa-print"></i> Stampa
            </a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-gray  print-pdf"
                                                title="Stampa Report" disabled>
                                            <i class="fas fa-print"></i>
                                        </button>
                                    @endif
                                    <form action="{{ route('reports.destroy', $report->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Sei sicuro di voler eliminare questo report?')"
                                                title="Elimina">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Nessun report trovato.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Mostrati {{ $reports->firstItem() }} - {{ $reports->lastItem() }} di {{ $reports->total() }} risultati
                    </div>
                    <div>
                        {{ $reports->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

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

<style>
    .table th {
        white-space: nowrap;
        cursor: pointer;
        position: relative;
        padding-right: 20px;
    }
    .table th:after {
        content: '↕';
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.8em;
        opacity: 0.5;
    }
    .table th.asc:after {
        content: '↑';
        opacity: 1;
    }
    .table th.desc:after {
        content: '↓';
        opacity: 1;
    }
</style>
@endsection
