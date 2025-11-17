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
    $outputDir = public_path('app/reports');
    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

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
       \Log::info('Inizio PDF overlay process');

        try {
            // Initialize FPDI
            $pdf = new Fpdi();

            // Full paths are already set

            // Get the page count
            $pageCount = $pdf->setSourceFile($pdfFullPath);
            $logoWidth = 50; // Width in mm
            $margin = 10; // Margin in mm

            // Process each page
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                // Import the page
                \Log::info('Importing page ' . $pageNo);

                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                // Add a page with the same orientation as the original
                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);

                // Use the imported page
                $pdf->useTemplate($templateId);

                // Add logo to every page (positioned at top-right with some margin)

                $xm = $margin; //$size['width'] - $logoWidth - $margin;
                $ym = $margin;
                \Log::info('Logo file details:', [
                    'path' => $logoFullPath,
                    'exists' => file_exists($logoFullPath) ? 'Yes' : 'No',
                    'is_readable' => is_readable($logoFullPath) ? 'Yes' : 'No',
                    'filesize' => file_exists($logoFullPath) ? filesize($logoFullPath) : 'File not found'
                ]);
                $pdf->Image($logoFullPath, $xm, $ym, $logoWidth);

                // Add blank rectangle in footer of first page only
                if ($pageNo === 1) {
                    \Log::info('Adding blank rectangle in footer of first page only');
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
               \Log::info('last  page only');
           // $this->addFooter($pdf, $report->name);  // Add footer to the ANALISI RACES page
            $pageWidth = $pdf->GetPageWidth();
            $pageHeight = $pdf->GetPageHeight();

            $pdf->Image($logoFullPath, $xm, $ym, $logoWidth);
            $pdf->Image("lightheader.jpg", $pageWidth - $logoWidth - $margin, $ym-5, $logoWidth);
      //      $pdf->Image('public/lightheader.jpg', $pageWidth -30 , $ym, $logoWidth);

            // Add report fields
            $pdf->Ln(2); // Add some space before annotation
            $pdf->SetFont('Arial', '', 12);
            $pdf->Ln(2); // Add some space before annotation
            $testo = $report->descrizione_score . "   " . $report->codice_score;
            $testoWidth = $pdf->GetStringWidth($testo);
            $pdf->Text($margin, 40, $testo);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Text($margin+$testoWidth+4, 40, $report->valore."   ". $report->categoria_descrizione);
            $pdf->Image("classerischio.jpg",$margin+($pageWidth- $logoWidth)/2, 50, $logoWidth);
            $pdf->Text($margin+($pageWidth- $logoWidth)/2+$logoWidth*($report->valore/100)-5, 57, 'X');
            //+(($logoWidth-$report->valore)
            // Add annotation with proper line breaks

            // Set font for the text
            $pdf->SetFont('Arial', 'B', 12);

            // Get page dimensions



            // Calculate text position (centered)
            $text = 'ANALISI RACES';
            $textWidth = $pdf->GetStringWidth($text);
            $x = $margin;//($pageWidth - $textWidth) / 2;
            $y = 70; //$pageHeight / 2;

            // Add the text
            $pdf->SetXY($x, $y);

            $pdf->Cell($textWidth, 10, $text, 0, 1, 'C');

            // Add report details
$pdf->SetFont('Arial', '', 12);


// Convert HTML to plain text with basic formatting
$annotation = $report->annotation;
$annotation = html_entity_decode($annotation, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$annotation = strip_tags($annotation);
$annotation = mb_convert_encoding($annotation, 'UTF-8', 'auto');
// Replace common problematic characters
$search = ['à','è','é','ì','ò','ù','À','È','É','Ì','Ò','Ù','&nbsp','&egrave','&agrave'];
$replace = ['a','e','e','i','o','u','A','E','E','I','O','U',' ','e','a'];
$annotation = str_replace($search, $replace, $annotation);
$annotation = trim($annotation);
// Split into lines and add to PDF
$lines = explode("\n", $annotation);
foreach ($lines as $line) {
    $line = trim($line);
    if (!empty($line)) {
        $pdf->SetFont('Arial', '', 10);  // Make sure to set the font before MultiCell
        $pdf->MultiCell(0, 5, $line, 0, 'L');
        $pdf->Ln(2);  // Small space between lines
    }
}
// Remove HTML tags but keep line breaks
/*
$annotation = strip_tags($annotation, '<br><p>');
$annotation = str_replace(['<br>', '<br/>', '<br />', '</p>', '<p>'], "\n", $annotation);
$annotation = html_entity_decode($annotation, ENT_QUOTES | ENT_HTML5, 'UTF-8');

// Clean up multiple newlines
$annotation = preg_replace("/\n\s*\n/", "\n\n", $annotation);

$annotation = trim($annotation);
// Split into paragraphs (handling different types of line breaks)
$paragraphs = preg_split('/\R+/', $annotation);

foreach ($paragraphs as $paragraph) {
    $paragraph = trim($paragraph);
    if (!empty($paragraph)) {
        // Use MultiCell for automatic word wrapping
        $pdf->MultiCell(0, 10, $paragraph, 0, 'L');
        $pdf->Ln(1); // Add a small space between paragraphs
    }
}

//$pdf->SetXY(10, 20);
*/
// 5. Write the HTML
// The 8 is the line height.
//$pdf->WriteHTML($annotation);


// Position at 1.5 cm from bottom
    $pdf->SetY(-15);
    // Set font
    $pdf->SetFont('Arial', '', 8);
    // Page number
    $pageNumber = $pdf->PageNo();
    // Add report name and page number
    $footerText = 'BUSINESS INFORMATION | ' . $report->name ;

    // Calculate width of the text
    $textWidth = $pdf->GetStringWidth($footerText) + 10;
    // Set X position to center the text
    $x = 10;// ($pdf->GetPageWidth() - $textWidth) / 2;
    // Set text color to gray
    $pdf->SetTextColor(128, 128, 128);
    // Print the text
    $pdf->Text($x, $pdf->GetY(), $footerText);
    $pdf->Text($pdf->GetPageWidth() - 20, $pdf->GetY(),  'Pag. ' . $pageNumber-2);

    // Reset text color to black
    $pdf->SetTextColor(0, 0, 0);
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
