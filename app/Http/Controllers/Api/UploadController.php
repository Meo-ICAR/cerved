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
            
            // Ensure the files directory exists with proper permissions
            $directory = 'public/files';
            if (!file_exists(storage_path('app/' . $directory))) {
                mkdir(storage_path('app/' . $directory), 0775, true);
            }

            // Generate filename with piva
            $filename = $piva . '.pdf';
            $fullPath = $directory . '/' . $filename;
            
            // Store the file with public visibility
            $file->storeAs('public/files', $filename);
            
            // Ensure the file has the correct permissions
            chmod(storage_path('app/' . $fullPath), 0664);
            
            // Path for the response
            $path = 'files/' . $filename;

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
