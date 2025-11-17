<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource with sorting and filtering.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'sort' => 'nullable|in:name,piva,valore,categoria_descrizione,updated_at',
            'direction' => 'nullable|in:asc,desc',
            'search' => 'nullable|string|max:255',
        ]);

        $sort = $validated['sort'] ?? 'updated_at';
        $direction = $validated['direction'] ?? 'desc';
        $search = $validated['search'] ?? null;

        $query = Report::query();

        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('piva', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $query->orderBy($sort, $direction);

        $reports = $query->paginate(10)->withQueryString();

        return view('reports.index', [
            'reports' => $reports,
            'sort' => $sort,
            'direction' => $direction,
            'search' => $search
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reports.create', ['report' => new Report()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportRequest $request)
    {
        try {
            // Create the report with just the P.IVA and user_id
            $report = Report::create([
                'piva' => $request->piva,
                'user_id' => Auth::id(),
                'name' => 'Report ' . now()->format('Y-m-d H:i:s')
            ]);

            // Dispatch the command to fetch Cerved data
            $exitCode = \Illuminate\Support\Facades\Artisan::call('cerved:fetch-score', [
                'piva' => $request->piva,
                '--report' => $report->id
            ]);

            // Check if the command was successful
            if ($exitCode !== 0) {
                $errorMessage = 'Failed to fetch Cerved data. ';
                
                // Add specific error messages based on the exit code
                if ($exitCode === 1) {
                    $errorMessage .= 'Invalid P.IVA format or API key not configured.';
                } elseif ($exitCode === 2) {
                    $errorMessage .= 'Report not found.';
                } else {
                    $errorMessage .= 'Error code: ' . $exitCode;
                }
                
                // Delete the report if it was created but the command failed
                $report->delete();
                
                return redirect()->back()
                    ->with('error', $errorMessage)
                    ->withInput();
            }

            // Refresh the report to get updated data
            $report->refresh();

            return redirect()->route('reports.show', $report->id)
                ->with('success', 'Report creato con successo. Dettagli aggiornati automaticamente.');

        } catch (\Exception $e) {
            // In case of error, redirect back with error message
            return redirect()->back()
                ->withInput()
                ->with('error', 'Si è verificato un errore durante la creazione del report: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        return view('reports.show', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        return view('reports.edit', compact('report'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(UpdateReportRequest $request, Report $report)
{
    $validated = $request->validated();
    $report->update($validated);

    // Determine logo path based on israces value
    $logoName = $report->israces ? 'logoraces.jpg' : 'logoabsg.jpg';
    $pdfPath = "app/reports/{$report->piva}.pdf";
    $outputPath = "app/reports/{$report->piva}_FINAL.pdf";

    // Ensure the output directory exists
     // Ensure the output directory exists
    if (!file_exists(public_path('app/reports'))) {
        mkdir(public_path('app/reports'), 0755, true);
    }

    // Remove existing output file if it exists
    $outputFullPath = public_path($outputPath);
    if (file_exists($outputFullPath)) {
        unlink($outputFullPath);
    }

    try {
        // Execute the PDF overlay command with P.IVA parameter
        \Artisan::call('pdf:overlay-logo', [
            'pdf' => $pdfPath,
            'logo' => $logoName,
            'piva' => $report->piva,  // Add P.IVA parameter
            '--output' => $outputPath
        ]);

        return redirect()->route('reports.index')
            ->with('success', 'Report aggiornato con successo e PDF generato.');

    } catch (\Exception $e) {
        \Log::error('Error generating final PDF: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Report aggiornato, ma si è verificato un errore durante la generazione del PDF finale: ' . $e->getMessage());
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        // Add authorization check if needed
        // $this->authorize('delete', $report);

        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Report eliminato con successo');
    }

    /**
     * Handle the upload of a PDF file for a report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPdf(Request $request, Report $report)
    {
        // Start debug log
        Log::info('Starting PDF upload process', [
            'report_id' => $report->id,
            'piva' => $report->piva,
            'has_file' => $request->hasFile('pdf_file'),
            'all_files' => $request->allFiles(),
            'request_data' => $request->all()
        ]);

        try {
            $request->validate([
                'pdf_file' => 'required|file|mimes:pdf|max:5120', // 5MB max
            ]);

            // Create the public directory if it doesn't exist
            $publicPath = public_path('app/reports');
            Log::info('Checking directory', ['path' => $publicPath, 'exists' => file_exists($publicPath)]);
            
            if (!file_exists($publicPath)) {
                $created = mkdir($publicPath, 0755, true);
                Log::info('Directory creation', ['path' => $publicPath, 'success' => $created]);
                
                if (!$created) {
                    throw new \Exception("Failed to create directory: {$publicPath}");
                }
            }

            // Get file info before moving
            $file = $request->file('pdf_file');
            Log::info('File info', [
                'original_name' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'temp_path' => $file->getPathname()
            ]);

            // Delete old PDF if it exists
            $filename = $report->piva . '.pdf';
            $destinationPath = $publicPath . '/' . $filename;
            
            if (file_exists($destinationPath)) {
                $deleted = unlink($destinationPath);
                Log::info('Old PDF removal', [
                    'path' => $destinationPath,
                    'success' => $deleted,
                    'error' => $deleted ? null : error_get_last()
                ]);
            }

            // Store the new PDF
            $moved = $file->move($publicPath, $filename);
            
            if (!$moved) {
                throw new \Exception("Failed to move uploaded file to {$destinationPath}");
            }

            Log::info('File moved successfully', [
                'original_path' => $file->getPathname(),
                'destination' => $destinationPath,
                'file_exists' => file_exists($destinationPath),
                'is_readable' => is_readable($destinationPath),
                'file_size' => filesize($destinationPath)
            ]);

            // Update the report
            $updateData = [
                'annotation' => 'PDF caricato manualmente',
                'file_uploaded_at' => now(),
                'updated_at' => now()
            ];
            
            $updated = $report->update($updateData);
            
            Log::info('Report updated', [
                'report_id' => $report->id,
                'update_success' => $updated,
                'update_data' => $updateData
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF caricato con successo',
                'path' => 'app/reports/' . $filename,
                'report_updated' => $updated
            ]);

        } catch (\Exception $e) {
            $errorContext = [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'report_id' => $report->id ?? null,
                'piva' => $report->piva ?? null,
                'has_file' => $request->hasFile('pdf_file'),
                'file_info' => $request->file('pdf_file') ? [
                    'name' => $request->file('pdf_file')->getClientOriginalName(),
                    'size' => $request->file('pdf_file')->getSize(),
                    'mime' => $request->file('pdf_file')->getMimeType(),
                    'error' => $request->file('pdf_file')->getError(),
                    'error_message' => $request->file('pdf_file')->getErrorMessage()
                ] : null,
                'request_headers' => $request->header(),
                'php_errors' => error_get_last(),
                'disk_space' => [
                    'free' => disk_free_space('/'),
                    'total' => disk_total_space('/')
                ],
                'permissions' => [
                    'public_path' => [
                        'exists' => file_exists(public_path()),
                        'writable' => is_writable(public_path()),
                        'permissions' => substr(sprintf('%o', fileperms(public_path())), -4)
                    ],
                    'storage_path' => [
                        'exists' => file_exists(storage_path()),
                        'writable' => is_writable(storage_path()),
                        'permissions' => substr(sprintf('%o', fileperms(storage_path())), -4)
                    ],
                    'destination_dir' => [
                        'exists' => isset($publicPath) && file_exists($publicPath),
                        'writable' => isset($publicPath) && is_writable($publicPath),
                        'permissions' => isset($publicPath) && file_exists($publicPath) ? 
                            substr(sprintf('%o', fileperms($publicPath)), -4) : 'N/A'
                    ]
                ]
            ];

            Log::error('PDF upload failed', $errorContext);
            
            return response()->json([
                'success' => false,
                'message' => 'Errore durante il caricamento del PDF: ' . $e->getMessage(),
                'debug' => config('app.debug') ? $errorContext : null
            ], 500);
        }
    }
}
