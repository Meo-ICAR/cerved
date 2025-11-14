<?php

namespace App\Console\Commands;

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OverlayLogoOnPdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:overlay-logo
                            {pdf : Path to the PDF file in storage/app/public}
                            {logo : Path to the logo image in storage/app/public}
                            {--output= : Output path in storage/app/public (default: output.pdf)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Overlay a logo on a PDF file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pdfPath = $this->argument('pdf');
        $logoPath = $this->argument('logo');
        $outputPath = $this->option('output') ?? 'output.pdf';

        // Check if files exist in public directory
        $publicPath = base_path('public');
        $pdfFullPath = $publicPath . '/' . $pdfPath;
        $logoFullPath = $publicPath . '/' . $logoPath;
        $outputFullPath = $publicPath . '/' . $outputPath;

        if (!file_exists($pdfFullPath)) {
            $this->error("PDF file not found: {$pdfFullPath}");
            return 1;
        }

        if (!file_exists($logoFullPath)) {
            $this->error("Logo file not found: {$logoFullPath}");
            return 1;
        }

        try {
            // Initialize FPDI
            $pdf = new Fpdi();

            // Full paths are already set

            // Get the page count
            $pageCount = $pdf->setSourceFile($pdfFullPath);

            // Process each page
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                // Import the page
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                // Add a page with the same orientation as the original
                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);

                // Use the imported page
                $pdf->useTemplate($templateId);

                // Add logo to every page (positioned at top-right with some margin)
                $logoWidth = 50; // Width in mm
                $margin = 10; // Margin in mm
                $x = $margin; //$size['width'] - $logoWidth - $margin;
                $y = $margin;

                $pdf->Image($logoFullPath, $x, $y, $logoWidth);

                // Add blank rectangle in footer of first page only
                if ($pageNo === 1) {
                    $footerHeight = 20; // Height of the footer rectangle in mm
                    $footerY = $size['height'] - $footerHeight - $margin;

                    // Save current settings
                    $pdf->SetDrawColor(255, 255, 255); // white border
                    $pdf->SetFillColor(255, 255, 255); // White fill

                    // Draw rectangle in footer
                    $pdf->Rect($margin, $footerY, $size['width'] - (2 * $margin), $footerHeight, 'DF');

                    // Reset drawing settings
                    $pdf->SetDrawColor(0, 0, 0); // Reset to black
                }
            }

            // Add a new page with ANALISI RACES text
            $pdf->AddPage();

            // Set font for the text
            $pdf->SetFont('Arial', 'B', 20);

            // Get page dimensions
            $pageWidth = $pdf->GetPageWidth();
            $pageHeight = $pdf->GetPageHeight();

            // Calculate text position (centered)
            $text = 'ANALISI RACES';
            $textWidth = $pdf->GetStringWidth($text);
            $x = ($pageWidth - $textWidth) / 2;
            $y = $pageHeight / 2;

            // Add the text
            $pdf->SetXY($x, $y);
            $pdf->Cell($textWidth, 10, $text, 0, 1, 'C');

            // Save the resulting PDF
            $pdf->Output($outputFullPath, 'F');

            $this->info("Success! PDF with logo overlay and additional page saved to: {$outputPath}");
            return 0;

        } catch (\Exception $e) {
            $this->error("Error processing PDF: " . $e->getMessage());
            return 1;
        }
    }
}
