<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;  // Add this line
use Illuminate\Support\Facades\Log;     // Also good to add for the Log facade

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
            \Illuminate\Support\Facades\Artisan::call('cerved:fetch-score', [
                'piva' => $request->piva,
                '--report' => $report->id
            ]);

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
    $logoName = $report->israces ? 'logoraces.png' : 'logoabsg.png';
    $pdfPath = "app/reports/{$report->piva}.pdf";
    $outputPath = "app/reports/{$report->piva}_FINAL.pdf";

    // Ensure the output directory exists
     // Ensure the output directory exists
    if (!file_exists(public_path('app/reports'))) {
        mkdir(public_path('app/reports'), 0755, true);
    }

    try {
        // Execute the PDF overlay command with P.IVA parameter
        \Artisan::call('pdf:overlay-logo', [
            'pdf' => $pdfPath,
            'logo' => "public/{$logoName}",
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
        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Report deleted successfully');
    }
}
