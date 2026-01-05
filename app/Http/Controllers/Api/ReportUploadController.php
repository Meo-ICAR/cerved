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
    public function upload(Request $request, $piva = null)
    {
        // Use piva from request if not in URL
        $piva = $piva ?? $request->input('piva');

        // Validate the request
        $request->validate([
            'file' => 'required|file|mimes:xml,pdf|max:10240',  // Max 10MB, accepts both XML and PDF
            'piva' => 'required_if:' . $piva . ',null|digits:11',  // Validate PIVA if not in URL
        ]);

        // Find or create report with the given piva
        $report = Report::firstOrNew(['piva' => $piva]);

        if (!$report->exists) {
            // If it's a new report, set default values
            $report->name = 'Report for PIVA ' . $piva;
            $report->user_id = Auth::id() ?? 1;  // Default to user 1 if not authenticated
            $report->israces = false;
        }

        // Store the uploaded file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = 'reports/' . $piva . '/' . time() . '_' . Str::random(10) . '.' . $extension;

            try {
                // Store the file in the storage/app/public directory
                $path = $file->storeAs('public/' . dirname($fileName), basename($fileName));

                // Update the report with the file path and current timestamp
                $report->mediaresponse = array_merge(
                    (array) $report->mediaresponse,
                    [
                        'file' => $fileName,
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_at' => now()->toDateTimeString(),
                    ]
                );
                $report->file_uploaded_at = now();

                // Save the report
                $report->save();

                return response()->json([
                    'success' => true,
                    'message' => 'File uploaded successfully',
                    'data' => [
                        'piva' => $piva,
                        'file_path' => Storage::url($fileName),
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'report_id' => $report->id,
                    ]
                ], 201);
            } catch (\Exception $e) {
                Log::error('File upload failed: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload file',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'No file was uploaded',
        ], 400);
    }
}
