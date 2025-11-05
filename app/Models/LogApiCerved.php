<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class LogApiCerved extends Model
{
    use HasFactory;

    /**
     * Specifica il nome della tabella.
     */
    protected $table = 'log_api_cerved';

    // --- Type-hint delle proprietà ---
    public int $id;
    public ?int $user_id; // Nullable se la chiamata è di sistema
    public string $endpoint_chiamato;
    public ?string $partita_iva_input;
    public int $status_code_risposta;
    public ?string $request_payload;
    public ?string $response_payload;
    public ?string $costo_chiamata; // Cast 'decimal:4' diventa string
    public ?Carbon $created_at;
    public ?Carbon $updated_at;
    // --- Fine Type-hint ---

    /**
     * I campi che possono essere assegnati in massa.
     */
    protected $fillable = [
        'user_id',
        'endpoint_chiamato',
        'partita_iva_input',
        'status_code_risposta',
        'request_payload',
        'response_payload',
        'costo_chiamata',
    ];

    /**
     * Cast per tipi di dato.
     */
    protected $casts = [
        'status_code_risposta' => 'integer',
        'costo_chiamata' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // I payload sono spesso JSON, ma è più sicuro
        // salvarli come 'string' (text/longtext nel DB)
        // e fare il json_decode solo se serve,
        // per evitare errori di cast se la risposta non è JSON
        // (es. errore 500 HTML).
    ];

    /**
     * Relazione: Il log può appartenere a un utente.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Converte il payload di richiesta in array se è JSON.
     */
    public function getRequestPayloadAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    /**
     * Converte il payload di risposta in array se è JSON.
     */
    public function getResponsePayloadAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    /**
     * Filtra i log per partita IVA.
     */
    public function scopeWherePartitaIva($query, $partitaIva)
    {
        return $query->where('partita_iva_input', $partitaIva);
    }

    /**
     * Filtra i log per endpoint.
     */
    public function scopeWhereEndpoint($query, $endpoint)
    {
        return $query->where('endpoint_chiamato', 'like', "%{$endpoint}%");
    }

    /**
     * Filtra i log per codice di stato.
     */
    public function scopeWhereStatusCode($query, $statusCode)
    {
        return $query->where('status_code_risposta', $statusCode);
    }

    /**
     * Filtra i log per utente.
     */
    public function scopeWhereUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Filtra i log per intervallo di date.
     */
    public function scopeDateRange($query, $from, $to = null)
    {
        $query->whereDate('created_at', '>=', $from);
        
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        
        return $query;
    }
}
