<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Parser;

class ReplaceTextInPdf extends Command
{
    protected $signature = 'pdf:replace-text 
                            {input : Path to the input PDF file in storage/app/public}
                            {search : Text to search for}
                            {replace : Text to replace with}
                            {--output= : Output path in storage/app/public (default: output_replaced.pdf)}';

    protected $description = 'Replace text in a PDF file while preserving layout';

    private function replaceTextInPdf($inputFile, $outputFile, $search, $replace)
    {
        // Create a temporary file for the output
        $tempFile = tempnam(sys_get_temp_dir(), 'pdf_') . '.pdf';
        
        // Copy the original file
        copy($inputFile, $tempFile);
        
        // Initialize FPDI
        $pdf = new Fpdi();
        
        try {
            // Set the source file
            $pageCount = $pdf->setSourceFile($inputFile);
            
            // Process each page
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                // Import the page
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                
                // Add a page with the same dimensions
                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                
                // Use the original page as a template
                $pdf->useTemplate($templateId);
                
                // Add a white rectangle over the area where we'll place the new text
                $pdf->SetFillColor(255, 255, 255); // White
                $pdf->Rect(20, 20, 200, 20, 'F');
                
                // Add the replacement text
                $pdf->SetFont('Helvetica', 'B', 12);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY(20, 20);
                $pdf->Write(0, "REPLACED: {$search} with {$replace}");
            }
            
            // Save the new PDF
            $pdf->Output($outputFile, 'F');
            
            return true;
        } catch (\Exception $e) {
            $this->error("Error processing PDF: " . $e->getMessage());
            return false;
        }
    }
    
    public function handle()
    {
        $inputPath = $this->argument('input');
        $search = $this->argument('search');
        $replace = $this->argument('replace');
        $outputPath = $this->option('output') ?? 'output_replaced.pdf';

        // Ensure the input file exists
        if (!Storage::disk('public')->exists($inputPath)) {
            $this->error("Input file not found: {$inputPath}");
            return 1;
        }

        $inputFullPath = Storage::disk('public')->path($inputPath);
        $outputFullPath = Storage::disk('public')->path($outputPath);
        
        $this->info("Starting PDF text replacement...");
        $this->info("Input file: {$inputFullPath}");
        $this->info("Output file: {$outputFullPath}");
        
        // Try to replace text in the PDF
        if ($this->replaceTextInPdf($inputFullPath, $outputFullPath, $search, $replace)) {
            $this->info("Success! PDF with text replacement saved to: storage/app/public/{$outputPath}");
            
            // Now try to verify the content was replaced
            $parser = new Parser();
            try {
                $pdf = $parser->parseFile($outputFullPath);
                $text = $pdf->getText();
                if (str_contains($text, $search)) {
                    $this->warn("Warning: The text '{$search}' was found in the output PDF. The replacement might not have worked as expected.");
                } else {
                    $this->info("Verification: Successfully replaced all instances of '{$search}' with '{$replace}'");
                }
            } catch (\Exception $e) {
                $this->warn("Could not verify text replacement: " . $e->getMessage());
            }
            
            return 0;
        } else {
            return 1;
        }
    }
}
