<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AziendaController;
use App\Http\Controllers\Api\SedeAziendaController;
use App\Http\Controllers\Api\BilancioController;
use App\Http\Controllers\Api\ScoringController;
use App\Http\Controllers\Api\LogApiCervedController;
use App\Http\Controllers\Api\PersonaController;
use App\Http\Controllers\Api\CaricaController;
use App\Http\Controllers\Api\ProtestoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route per l'autenticazione (se necessario)
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Rotte per i protesti
Route::prefix('protesti')->group(function () {
    // Elenco protesti con filtri
    Route::get('/', [ProtestoController::class, 'index']);
    
    // Crea un nuovo protesto
    Route::post('/', [ProtestoController::class, 'store']);
    
    // Statistiche
    Route::get('/statistiche', [ProtestoController::class, 'statistiche']);
    
    // Rotte per un singolo protesto
    Route::prefix('{protesto}')->group(function () {
        // Visualizza un protesto specifico
        Route::get('/', [ProtestoController::class, 'show']);
        
        // Aggiorna un protesto
        Route::put('/', [ProtestoController::class, 'update']);
        
        // Elimina un protesto
        Route::delete('/', [ProtestoController::class, 'destroy']);
    });
    
    // Protesti di una persona
    Route::get('/persona/{personaId}', [ProtestoController::class, 'protestiPersona']);
});

// Rotte per le cariche
Route::prefix('cariche')->group(function () {
    // Elenco cariche con filtri
    Route::get('/', [CaricaController::class, 'index']);
    
    // Crea una nuova carica
    Route::post('/', [CaricaController::class, 'store']);
    
    // Rotte per una singola carica
    Route::prefix('{carica}')->group(function () {
        // Visualizza una carica specifica
        Route::get('/', [CaricaController::class, 'show']);
        
        // Aggiorna una carica
        Route::put('/', [CaricaController::class, 'update']);
        
        // Termina una carica (imposta data_fine_carica a oggi)
        Route::post('/termina', [CaricaController::class, 'termina']);
        
        // Elimina una carica
        Route::delete('/', [CaricaController::class, 'destroy']);
    });
    
    // Cariche attive di una persona
    Route::get('/persona/{personaId}/attive', [CaricaController::class, 'caricheAttivePersona']);
    
    // Cariche di un'azienda
    Route::get('/azienda/{aziendaId}', [CaricaController::class, 'caricheAzienda']);
});

// Rotte per le persone
Route::prefix('persone')->group(function () {
    // Elenco persone con filtri
    Route::get('/', [PersonaController::class, 'index']);
    
    // Crea una nuova persona
    Route::post('/', [PersonaController::class, 'store']);
    
    // Cerca per codice fiscale
    Route::get('/cf/{codiceFiscale}', [PersonaController::class, 'cercaPerCodiceFiscale']);
    
    // Rotte per una singola persona
    Route::prefix('{persona}')->group(function () {
        // Visualizza una persona specifica
        Route::get('/', [PersonaController::class, 'show']);
        
        // Aggiorna una persona
        Route::put('/', [PersonaController::class, 'update']);
        
        // Elimina una persona
        Route::delete('/', [PersonaController::class, 'destroy']);
        
        // Aggiorna i dati da Cerved
        Route::post('/aggiorna-cerved', [PersonaController::class, 'aggiornaDaCerved']);
    });
});

// Rotte per i log delle API Cerved
Route::prefix('logs/cerved')->group(function () {
    // Elenco log con filtri
    Route::get('/', [LogApiCervedController::class, 'index']);
    
    // Crea un nuovo log
    Route::post('/', [LogApiCervedController::class, 'store']);
    
    // Statistiche
    Route::get('/stats', [LogApiCervedController::class, 'stats']);
    
    // Rotte per un singolo log
    Route::prefix('{log}')->group(function () {
        // Visualizza un log specifico
        Route::get('/', [LogApiCervedController::class, 'show']);
        
        // Aggiorna un log
        Route::put('/', [LogApiCervedController::class, 'update']);
        
        // Elimina un log
        Route::delete('/', [LogApiCervedController::class, 'destroy']);
    });
});

// Raggruppamento delle rotte per le aziende
Route::prefix('aziende')->group(function () {
    // Rotta per la ricerca e il recupero di più aziende
    Route::get('/', [AziendaController::class, 'index']);
    
    // Rotta per la creazione di una nuova azienda
    Route::post('/', [AziendaController::class, 'store']);
    
    // Raggruppamento delle rotte per una singola azienda
    Route::prefix('{azienda}')->group(function () {
        // Visualizza un'azienda specifica
        Route::get('/', [AziendaController::class, 'show']);
        
        // Aggiorna un'azienda specifica
        Route::put('/', [AziendaController::class, 'update']);
        
        // Elimina un'azienda specifica
        Route::delete('/', [AziendaController::class, 'destroy']);
        
        // Rotte per le sedi dell'azienda
        Route::prefix('sedi')->group(function () {
            // Elenco delle sedi
            Route::get('/', [SedeAziendaController::class, 'index']);
            
            // Crea una nuova sede
            Route::post('/', [SedeAziendaController::class, 'store']);
            
            // Rotte per una singola sede
            Route::prefix('{sede}')->group(function () {
                // Visualizza una sede specifica
                Route::get('/', [SedeAziendaController::class, 'show']);
                
                // Aggiorna una sede specifica
                Route::put('/', [SedeAziendaController::class, 'update']);
                
                // Elimina una sede specifica
                Route::delete('/', [SedeAziendaController::class, 'destroy']);
            });
        });
        
        // Rotte per i bilanci dell'azienda
        Route::prefix('bilanci')->group(function () {
            // Elenco dei bilanci
            Route::get('/', [BilancioController::class, 'index']);
            
            // Crea un nuovo bilancio
            Route::post('/', [BilancioController::class, 'store']);
            
            // Rotte per un singolo bilancio
            Route::prefix('{bilancio}')->group(function () {
                // Visualizza un bilancio specifico
                Route::get('/', [BilancioController::class, 'show']);
                
                // Aggiorna un bilancio specifico
                Route::put('/', [BilancioController::class, 'update']);
                
                // Elimina un bilancio specifico
                Route::delete('/', [BilancioController::class, 'destroy']);
            });
        });
        
        // Rotte per gli scoring dell'azienda
        Route::prefix('scoring')->group(function () {
            // Elenco degli scoring
            Route::get('/', [ScoringController::class, 'index']);
            
            // Crea un nuovo scoring
            Route::post('/', [ScoringController::class, 'store']);
            
            // Ottieni l'ultimo scoring
            Route::get('/ultimo', [ScoringController::class, 'ultimo']);
            
            // Rotte per un singolo scoring
            Route::prefix('{scoring}')->group(function () {
                // Visualizza uno scoring specifico
                Route::get('/', [ScoringController::class, 'show']);
                
                // Aggiorna uno scoring specifico
                Route::put('/', [ScoringController::class, 'update']);
                
                // Elimina uno scoring specifico
                Route::delete('/', [ScoringController::class, 'destroy']);
            });
        });
    });
});

// Altre rotte API possono essere aggiunte qui
