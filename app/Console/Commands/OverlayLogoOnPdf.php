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
                        {piva : P.IVA of the report to fetch additional data}
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
         \Log::info('Starting PDF overlay process', [
        'pdf' => $this->argument('pdf'),
        'logo' => $this->argument('logo'),
        'piva' => $this->argument('piva'),
        'output' => $this->option('output')
    ]);
        $pdfPath = $this->argument('pdf');
       $logoPath = $this->argument('logo');
    $piva = $this->argument('piva');
    $outputPath = $this->option('output') ?? 'output.pdf';

    // Fetch the report
    $report = \App\Models\Report::where('piva', $piva)->first();

    if (!$report) {
        $this->error("Report not found for P.IVA: {$piva}");
          \Log::error("Report not found for P.IVA: {$piva}");
        $this->error($error);
        return 1;
    }

        // Check if files exist in public directory
        $publicPath = base_path('public');
        $pdfFullPath = $publicPath . '/' . $pdfPath;
        $logoFullPath = $publicPath . '/' . $logoPath;
        $outputFullPath = $publicPath . '/' . $outputPath;

    if (!file_exists($pdfFullPath)) {
        $error = "PDF file not found: {$pdfFullPath}";
        \Log::error($error);
        $this->error($error);
        return 1;
    }

    if (!file_exists($logoFullPath)) {
        $error = "Logo file not found: {$logoFullPath}";
        \Log::error($error);
        $this->error($error);
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

            // Add report details
$pdf->SetFont('Arial', '', 12);
$pdf->Ln(20); // Add some space after the title

// Add report fields
$pdf->Cell(0, 10, "Descrizione Score: " . $report->descrizione_score, 0, 1);
$pdf->Cell(0, 10, "Codice Score: " . $report->codice_score, 0, 1);
$pdf->Cell(0, 10, "Valore: " . $report->valore, 0, 1);

// Add annotation with proper line breaks
$pdf->Ln(10); // Add some space before annotation
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, "Note:", 0, 1);
$pdf->SetFont('Arial', '', 12);

// Handle HTML content from TinyMCE
$annotation = strip_tags($report->annotation, '<p><br><strong><em><u><ol><ul><li>');
$annotation = str_replace(['<br>', '<br/>', '<br />'], "\n", $annotation);
$annotation = html_entity_decode($annotation);

// Split the text into lines
$lines = explode("\n", wordwrap($annotation, 100)); // 100 characters per line

foreach ($lines as $line) {
    $pdf->Cell(0, 10, trim($line), 0, 1);
}

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
