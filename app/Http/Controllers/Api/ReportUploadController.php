<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportUploadController extends Controller
{
    // app/Http/Controllers/Api/ReportUploadController.php

    public function upload(Request $request, $piva = null)
    {
        \Log::info('Upload request received', [
            'headers' => $request->headers->all(),
            'all' => $request->all(),
            'files' => $request->allFiles(),
            'piva' => $piva
        ]);
        // Validate the request
        $validated = $request->validate([
            'file' => 'required|file|mimes:xml,pdf|max:10240',
            'piva' => 'required|digits:11'
        ]);
        $piva = $piva ?? $validated['piva'];
        try {
            if (!$request->hasFile('file')) {
                throw new \Exception('No file was uploaded');
            }
            // Find or create report
            $report = Report::firstOrNew(['piva' => $piva]);
            $report->name = 'Report for PIVA ' . $piva;
            $report->user_id = 1;  // Or get from auth if
            $report->israces = true;
            // $report->israces = false;

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('reports/' . $piva, 'public');

                $report->mediaresponse = [
                    'file' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
                $report->file_uploaded_at = now();
            }
            $report->save();
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => $report
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'request_data' => $request->all(),
                'files' => $request->allFiles()
            ], 400);
        }
    }
}
