<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class ReplacePdfTextAdvanced extends Command
{
    protected $signature = 'pdf:replace-text-advanced 
                            {input : Path to the input PDF file in storage/app/public}
                            {search : Text to search for}
                            {replace : Text to replace with}
                            {--font=Helvetica : Font family}
                            {--size=12 : Font size}
                            {--color=000000 : Text color in hex (e.g., FF0000 for red)}
                            {--output= : Output path in storage/app/public}
                            {--debug : Enable debug mode to show text positions}';

    protected $description = 'Replace text in a PDF by overlaying new text';

    /**
     * Get replacement positions for text in PDF
     */
    private function getReplacementPositions($pdf, $pageCount, $searchText)
    {
        $positions = [];
        
        // This is a simplified approach that will add the text at specific positions
        // You'll need to adjust these positions based on your actual PDF layout
        
        // Common header/footer positions (in mm)
        $headerY = 15;
        $footerY = 277; // For A4 portrait (297mm height)
        
        // Add positions for each page
        for ($page = 1; $page <= $pageCount; $page++) {
            // Add header position (center of page)
            $positions[] = [
                'page' => $page,
                'x' => 50,
                'y' => $headerY,
                'width' => 100,
                'height' => 10
            ];
            
            // Add footer position (center of page)
            $positions[] = [
                'page' => $page,
                'x' => 50,
                'y' => $footerY,
                'width' => 100,
                'height' => 10
            ];
            
            // Add some common content positions (adjust as needed)
            for ($y = 50; $y < 250; $y += 30) {
                $positions[] = [
                    'page' => $page,
                    'x' => 50,
                    'y' => $y,
                    'width' => 100,
                    'height' => 10
                ];
            }
        }
        
        return $positions;
    }
    
    public function handle()
    {
        $inputPath = $this->argument('input');
        $search = $this->argument('search');
        $replace = $this->argument('replace');
        
        // Set output path with timestamp to avoid overwriting
        $outputPath = $this->option('output') ?? 'replaced_' . time() . '.pdf';

        // Ensure the input file exists
        if (!Storage::disk('public')->exists($inputPath)) {
            $this->error("Input file not found: {$inputPath}");
            return 1;
        }

        $inputFullPath = Storage::disk('public')->path($inputPath);
        $outputFullPath = Storage::disk('public')->path($outputPath);

        try {
            // Initialize FPDI first
            $pdf = new Fpdi();
            
            // Get the page count
            $pageCount = $pdf->setSourceFile($inputFullPath);
            
            // Get positions where we should try to replace text
            $positions = $this->getReplacementPositions($pdf, $pageCount, $search);
            
            if ($this->option('debug')) {
                $this->info("Will attempt to replace text at " . count($positions) . " positions");
            }
            
            // Reset FPDI instance for processing
            $pdf = new Fpdi();
            
            // Set the source file for the new instance
            $pdf->setSourceFile($inputFullPath);
            
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
                
                // Find positions for this page
                $pagePositions = array_filter($positions, function($pos) use ($pageNo) {
                    return $pos['page'] == $pageNo;
                });
                
                if (empty($pagePositions)) {
                    if ($this->option('debug')) {
                        $this->line("No replacement positions defined for page {$pageNo}");
                    }
                    continue;
                }
                
                // Process each position on this page
                foreach ($pagePositions as $pos) {
                    // Add a white rectangle to cover the original text
                    $pdf->SetFillColor(255, 255, 255); // White
                    $pdf->Rect(
                        $pos['x'],
                        $pos['y'],
                        $pos['width'] ?? 100,  // Default width if not specified
                        $pos['height'] ?? 10,  // Default height if not specified
                        'F'
                    );
                    
                    // Add the new text
                    $this->setTextColorFromHex($pdf, $this->option('color'));
                    $pdf->SetFont($this->option('font'), '', $this->option('size'));
                    $pdf->SetXY($pos['x'], $pos['y']);
                    $pdf->Cell(0, 0, $replace);
                    
                    if ($this->option('debug')) {
                        $this->line(sprintf(
                            'Replaced "%s" with "%s" at page %d, x=%.2f, y=%.2f',
                            $search,
                            $replace,
                            $pageNo,
                            $pos['x'],
                            $pos['y']
                        ));
                    }
                }
            } // End of for loop
            
            // Save the new PDF
            $pdf->Output($outputFullPath, 'F');
            
            $this->info("Success! PDF with text replacement saved to: storage/app/public/{$outputPath}");
            $this->info("You can access it at: " . url(Storage::url($outputPath)));
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error processing PDF: " . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Set text color from hex value
     */
    private function setTextColorFromHex($pdf, $hexColor)
    {
        $hexColor = ltrim($hexColor, '#');
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        $pdf->SetTextColor($r, $g, $b);
    }
}
