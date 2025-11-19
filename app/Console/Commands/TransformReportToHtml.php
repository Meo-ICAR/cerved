<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TransformReportToHtml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:transform-html {piva : P.IVA associated with the report XML}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply the cerved XSL stylesheet to a report XML and produce the HTML output';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $piva = $this->argument('piva');
        $reportsDir = public_path('app/reports');
        $xmlPath = $reportsDir . DIRECTORY_SEPARATOR . $piva . '.xml';
        $outputPath = $reportsDir . DIRECTORY_SEPARATOR . $piva . '_FINAL.html';
        $xslPath = public_path('cerved.xsl');

        if (!file_exists($xmlPath)) {
            $this->error("XML file not found: {$xmlPath}");
            return self::FAILURE;
        }

        if (!file_exists($xslPath)) {
            $this->error("XSL stylesheet not found: {$xslPath}");
            return self::FAILURE;
        }

        if (!is_dir($reportsDir)) {
            if (!mkdir($reportsDir, 0755, true) && !is_dir($reportsDir)) {
                $this->error("Unable to create reports directory: {$reportsDir}");
                return self::FAILURE;
            }
        }

        $xml = new \DOMDocument();
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = false;

        try {
            if (!$xml->load($xmlPath)) {
                $this->error("Failed to load XML file: {$xmlPath}");
                return self::FAILURE;
            }
        } catch (\Throwable $e) {
            $this->error("Error while loading XML: " . $e->getMessage());
            return self::FAILURE;
        }

        $xsl = new \DOMDocument();

        try {
            if (!$xsl->load($xslPath)) {
                $this->error("Failed to load XSL stylesheet: {$xslPath}");
                return self::FAILURE;
            }
        } catch (\Throwable $e) {
            $this->error("Error while loading XSL stylesheet: " . $e->getMessage());
            return self::FAILURE;
        }

        $processor = new \XSLTProcessor();

        try {
            $processor->importStylesheet($xsl);
            $result = $processor->transformToXml($xml);
        } catch (\Throwable $e) {
            $this->error("Error during XSL transformation: " . $e->getMessage());
            return self::FAILURE;
        }

        if ($result === false) {
            $this->error('The transformation returned no result.');
            return self::FAILURE;
        }

        try {
            file_put_contents($outputPath, $result);
        } catch (\Throwable $e) {
            $this->error("Unable to write output HTML: " . $e->getMessage());
            return self::FAILURE;
        }

        $this->info("HTML report generated: {$outputPath}");

        return self::SUCCESS;
    }
}

