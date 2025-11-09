<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Carica extends Model
{
    use HasFactory;

    /**
     * Specifica il nome della tabella.
     */
    protected $table = 'cariche';

    // --- Type-hint delle proprietà ---
    public int $id;
    public int $persona_id;
    public int $azienda_id;
    public string $tipo_carica; // Es. "Amministratore Delegato"
    public string $descrizione_carica; // Descrizione estesa da Cerved
    public ?Carbon $data_inizio_carica;
    public ?Carbon $data_fine_carica; // Null se attiva
    public ?int $numero_quote;
    public ?float $valore_totale_quote;
    public ?float $quota_massima_societa;
    public ?float $percentuale_quota_partecipazione;
    public ?string $tipo_diritto;
    public ?array $dati_carica_completi; // JSON
    public ?Carbon $created_at;
    public ?Carbon $updated_at;
    // --- Fine Type-hint ---

    /**
     * I campi che possono essere assegnati in massa.
     */
    protected $fillable = [
        'persona_id',
        'azienda_id',
        'tipo_carica',
        'descrizione_carica',
        'data_inizio_carica',
        'data_fine_carica',
        'numero_quote',
        'valore_totale_quote',
        'quota_massima_societa',
        'percentuale_quota_partecipazione',
        'tipo_diritto',
        'dati_carica_completi',
    ];

    /**
     * Cast per tipi di dato.
     */
    protected $casts = [
        'data_inizio_carica' => 'date',
        'data_fine_carica' => 'date',
        'valore_totale_quote' => 'decimal:2',
        'quota_massima_societa' => 'decimal:2',
        'percentuale_quota_partecipazione' => 'decimal:2',
        'dati_carica_completi' => 'array',
    ];

    /**
     * Relazione: Questa carica appartiene a una persona.
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Relazione: Questa carica è relativa a un'azienda.
     */
    public function azienda(): BelongsTo
    {
        return $this->belongsTo(Azienda::class);
    }

    /**
     * Controlla se la carica è attiva.
     */
    public function isAttiva(): bool
    {
        return is_null($this->data_fine_carica);
    }

    /**
     * Filtra per cariche attive.
     */
    public function scopeAttive($query)
    {
        return $query->whereNull('data_fine_carica');
    }

    /**
     * Filtra per tipo di carica.
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_carica', $tipo);
    }

    /**
     * Filtra per azienda.
     */
    public function scopeAzienda($query, $aziendaId)
    {
        return $query->where('azienda_id', $aziendaId);
    }

    /**
     * Filtra per persona.
     */
    public function scopePersona($query, $personaId)
    {
        return $query->where('persona_id', $personaId);
    }
}
