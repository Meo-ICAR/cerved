<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends BaseApiController
{
    /**
     * Handle the PDF file upload
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPdf(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'piva' => 'required|string|max:20',
            'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB file size
        ]);

        try {
            // Get the file from the request
            $file = $request->file('file');
            $piva = $validated['piva'];
            
            // Create directory if it doesn't exist
            $directory = 'files';
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            // Generate filename with piva and timestamp
            $filename = $piva . '.pdf';
            
            // Store the file
            $path = $file->storeAs($directory, $filename, 'public');

            return $this->sendResponse([
                'path' => $path,
                'url' => Storage::url($path),
                'message' => 'File uploaded successfully.'
            ]);

        } catch (\Exception $e) {
            return $this->sendError('File upload failed: ' . $e->getMessage(), [], 500);
        }
    }
}
