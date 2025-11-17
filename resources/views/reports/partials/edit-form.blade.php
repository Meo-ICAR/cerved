@php
    $action = route('reports.update', $report->id);
    $method = 'PUT';
@endphp

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Modifica Report</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ $action }}" id="editReportForm">
                        @csrf
                        @method($method)

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Partita IVA</label>
                                    <div>
                                        <a href="#" class="piva-link" data-piva="{{ $report->piva }}" title="Visualizza PDF">
                                            {{ $report->piva }}
                                            <i class="fas fa-external-link-alt ms-2"></i>
                                        </a>
                                        <small class="d-block text-muted">click per visualizzare report</small>
                                        <span class="spinner-border spinner-border-sm d-none" role="status" style="width: 1rem; height: 1rem;"></span>
                                        <div class="invalid-feedback d-none">PDF non trovato</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Denominazione</label>
                                    <input type="text" class="form-control-plaintext" value="{{ $report->name }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Score</label>
                                    <input type="text" class="form-control-plaintext" value="{{ $report->valore }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Categoria</label>
                                    <input type="text" class="form-control-plaintext" value="{{ $report->categoria_descrizione }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="israces" name="israces" value="1" {{ $report->israces ? 'checked' : '' }}>
                                <label class="form-check-label" for="israces">Is Races</label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="annotation" class="form-label">Annotazioni</label>
                            <textarea class="form-control rich-editor @error('annotation') is-invalid @enderror"
                                     id="annotation"
                                     name="annotation"
                                     rows="10">{{ old('annotation', $report->annotation) }}</textarea>
                            @error('annotation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('reports.show', $report->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Annulla
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Salva Modifiche
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tinymce@5.10.5/tinymce.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<style>
    .piva-link {
        color: #0d6efd;
        text-decoration: none;
        cursor: pointer;
    }
    .piva-link:hover {
        text-decoration: underline;
    }
    .piva-link .spinner-border,
    .piva-link .invalid-feedback {
        margin-left: 0.5rem;
    }
</style>

<script>
    document.addEventListener('click', function(e) {
        if (e.target.closest('.piva-link')) {
            e.preventDefault();
            const link = e.target.closest('.piva-link');
            const piva = link.dataset.piva;
            const spinner = link.nextElementSibling;
            const errorMsg = spinner.nextElementSibling;

            // Show loading spinner
            link.querySelector('i').classList.add('d-none');
            spinner.classList.remove('d-none');
            errorMsg.classList.add('d-none');

            // Try to open the PDF
            const tryOpenPdf = (url) => {
                return new Promise((resolve) => {
                    const newWindow = window.open(url, '_blank');
                    // Check if window was blocked by popup blocker
                    if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
                        resolve(false);
                    } else {
                        // Check if the PDF loaded successfully
                        newWindow.onload = () => resolve(true);
                        // If onload doesn't fire, check after a short delay
                        setTimeout(() => resolve(!!newWindow.document.body.innerHTML), 1000);
                    }
                });
            };

            // Try both possible PDF locations
            Promise.race([
                tryOpenPdf(`/public/app/reports/${piva}.pdf`),
                tryOpenPdf(`/storage/${piva}.pdf`)
            ])
            .then(success => {
                if (!success) {
                    throw new Error('PDF could not be opened');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMsg.classList.remove('d-none');
            })
            .finally(() => {
                // Hide loading spinner
                spinner.classList.add('d-none');
                link.querySelector('i').classList.remove('d-none');
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '#annotation',
            plugins: 'link lists image code table media',
            toolbar: 'undo redo | formatselect | bold italic backcolor | \
                     alignleft aligncenter alignright alignjustify | \
                     bullist numlist outdent indent | removeformat | help',
            menubar: false,
            height: 300,
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
    });
</script>
@endpush
