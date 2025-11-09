<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CervedEntityController extends Controller
{
    /**
     * Show the search form
     *
     * @return \Illuminate\View\View
     */
    public function showSearchForm()
    {
        return view('cerved.entity_search');
    }

    /**
     * Handle the search request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2|max:255',
            'type' => 'sometimes|in:all,person,company',
            'debug' => 'nullable|boolean',
        ]);

        $searchTerm = $request->input('search');
        $searchType = $request->input('type', 'all');
        $debug = $request->has('debug'); // This will return true if the checkbox is checked

        try {
            // Execute the command and capture output
            $command = [
                'cerved:fetch-entity',
                $searchTerm,
                '--type=' . $searchType,
            ];

            if ($debug) {
                $command[] = '--debug';
            }

            // Execute the command and capture the output
            $output = [];
            $exitCode = Artisan::call('cerved:fetch-entity', [
                'search' => $searchTerm,
                '--type' => $searchType,
                '--debug' => $debug,
            ]);

            // Get the output from the command
            $commandOutput = Artisan::output();
            
            // Parse the output to extract results
            $results = [
                'people_processed' => 0,
                'companies_processed' => 0,
                'errors' => [],
            ];

            // Simple parsing of the command output
            if (preg_match('/People processed: (\d+)/', $commandOutput, $matches)) {
                $results['people_processed'] = (int)$matches[1];
            }
            
            if (preg_match('/Companies processed: (\d+)/', $commandOutput, $matches)) {
                $results['companies_processed'] = (int)$matches[1];
            }

            // Extract errors if any
            if (preg_match('/Errors encountered:(.*?)(?=Search Results|$)/s', $commandOutput, $errorMatches)) {
                $errorLines = explode("\n", trim($errorMatches[1]));
                foreach ($errorLines as $line) {
                    if (preg_match('/- (\w+) \(ID: ([^)]+)\): (.+)/', trim($line), $errorParts)) {
                        $results['errors'][] = [
                            'type' => $errorParts[1],
                            'id' => $errorParts[2],
                            'error' => $errorParts[3],
                        ];
                    }
                }
            }

            $debugInfo = null;
            if ($debug) {
                $debugInfo = [
                    'command' => 'cerved:fetch-entity ' . $searchTerm . ' --type=' . $searchType . ($debug ? ' --debug' : ''),
                    'exit_code' => $exitCode,
                    'raw_output' => $commandOutput,
                ];
            }

            return view('cerved.entity_search', [
                'results' => $results,
                'debugInfo' => $debugInfo,
                'debug' => $debug,
            ]);

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'An error occurred while processing your request: ' . $e->getMessage(),
            ]);
        }
    }
}
