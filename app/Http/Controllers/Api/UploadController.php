<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Models\LogApiCerved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
        $startTime = microtime(true);
        
        // Validate the request
        $validated = $request->validate([
            'piva' => 'required|string|max:20',
            'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB file size
        ]);

        try {
            // Get the file from the request
            $file = $request->file('file');
            $piva = $validated['piva'];
            
            // Sanitize the PIVA to create a safe filename
            $filename = preg_replace('/[^a-zA-Z0-9]/', '_', $piva) . '.pdf';
            $storagePath = 'files';  // Changed to store directly in public/files
            
            // Log the upload attempt
            Log::info('Attempting to upload file', [
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'storage_path' => $storagePath
            ]);
            
            // Ensure the directory exists with proper permissions
            $fullStoragePath = public_path($storagePath);
            if (!file_exists($fullStoragePath)) {
                if (!mkdir($fullStoragePath, 0775, true)) {
                    Log::error('Failed to create directory', ['path' => $fullStoragePath]);
                    return $this->sendError('Failed to create storage directory', [], 500);
                }
                chmod($fullStoragePath, 0775);
            }
            
            // Store the file directly in public/files
            $storedPath = $file->move($fullStoragePath, $filename);
            
            if (!$storedPath) {
                Log::error('File storage failed', [
                    'filename' => $filename,
                    'storage_path' => $storagePath,
                    'error' => 'Storage::storeAs returned false'
                ]);
                return $this->sendError('Failed to store the file', [], 500);
            }
            
            // Verify the file was stored
            $fullPath = $storedPath->getPathname();
            if (!file_exists($fullPath)) {
                Log::error('File not found after storage', [
                    'expected_path' => $fullPath,
                    'stored_path' => $storedPath
                ]);
                return $this->sendError('File was not stored correctly', [
                    'stored_path' => $storedPath,
                    'full_path' => $fullPath,
                    'storage_disk' => config('filesystems.default')
                ], 500);
            }
            
            // Set file permissions
            chmod($fullPath, 0664);
            
            // Generate the public URL
            $publicPath = 'files/' . $filename;
            $publicUrl = url($publicPath);
            
            $responseData = [
                'url' => $publicUrl,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];
            
            // Log the successful API request
            $this->logApiRequest($request, 200, $responseData, $startTime);
            
            return $this->sendResponse($responseData, 'File uploaded successfully');

        } catch (\Exception $e) {
            Log::error('File upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Log the failed API request
            $this->logApiRequest(
                $request, 
                500, 
                ['error' => $e->getMessage()], 
                $startTime
            );
            
            return $this->sendError('File upload failed', ['error' => $e->getMessage()], 500);
        }
    }
    
    private function logApiRequest(Request $request, int $statusCode, array $responseData, float $startTime)
    {
        $log = new LogApiCerved();
        $log->method = $request->getMethod();
        $log->url = $request->fullUrl();
        $log->status_code = $statusCode;
        $log->response_data = json_encode($responseData);
        $log->request_data = json_encode($request->all());
        $log->ip_address = $request->ip();
        $log->user_agent = $request->userAgent();
        $log->response_time = microtime(true) - $startTime;
        $log->save();
    }
}
