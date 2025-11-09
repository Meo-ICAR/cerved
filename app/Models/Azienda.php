<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Azienda extends Model
{
    use HasFactory;

    /**
     * Specifica il nome della tabella.
     */
    protected $table = 'aziende';

    // --- Type-hint delle proprietà ---
    public int $id;
    public string $partita_iva;
    public ?string $codice_fiscale;
    public ?string $denominazione;
    public ?string $forma_giuridica;
    public ?string $ateco;
    public ?string $rea;
    public ?Carbon $data_costituzione;
    public ?string $stato_attivita;
    public ?Carbon $ultimo_aggiornamento_cerved;
    public ?array $dati_aziendali_completi; // JSON
    public ?Carbon $created_at;
    public ?Carbon $updated_at;
    // --- Fine Type-hint ---

    /**
     * I campi che possono essere assegnati in massa.
     */
    protected $fillable = [
        'partita_iva',
        'codice_fiscale',
        'denominazione',
        'forma_giuridica',
        'ateco',
        'rea',
        'data_costituzione',
        'stato_attivita',
        'ultimo_aggiornamento_cerved',
        'dati_aziendali_completi',
    ];

    /**
     * Cast per tipi di dato.
     */
    protected $casts = [
        'data_costituzione' => 'date',
        'ultimo_aggiornamento_cerved' => 'datetime',
        'dati_aziendali_completi' => 'array',
    ];

    /**
     * Relazione: Un'azienda ha molte sedi.
     */
    public function sedi(): HasMany
    {
        return $this->hasMany(SedeAzienda::class, 'azienda_id');
    }

    /**
     * Relazione: Un'azienda ha molti bilanci.
     */
    public function bilanci(): HasMany
    {
        return $this->hasMany(Bilancio::class, 'azienda_id');
    }

    /**
     * Relazione: Un'azienda ha molti punteggi di scoring.
     */
    public function scorings(): HasMany
    {
        return $this->hasMany(Scoring::class, 'azienda_id');
    }

    /**
     * Relazione: Un'azienda ha molte cariche (di diverse persone).
     */
    public function cariche(): HasMany
    {
        return $this->hasMany(Carica::class, 'azienda_id');
    }

    /**
     * Relazione: Un'azienda ha molte persone (amministratori, soci, ecc.)
     * TRAMITE la tabella delle cariche.
     */
    public function persone(): BelongsToMany
    {
        return $this->belongsToMany(
            Person::class,
            'cariche',
            'azienda_id',
            'persona_id'
        )
        ->withPivot([
            'tipo_carica',
            'descrizione_carica',
            'data_inizio_carica',
            'data_fine_carica',
            'dati_carica_completi'
        ])
        ->withTimestamps();
    }
    
    /**
     * Relazione: Persone con cariche attive nell'azienda.
     */
    public function personeAttive(): BelongsToMany
    {
        return $this->persone()
            ->wherePivotNull('data_fine_carica')
            ->orWherePivot('data_fine_carica', '>', now());
    }
    
    /**
     * Relazione: Persone con un tipo specifico di carica.
     */
    public function personeConCarica(string $tipoCarica)
    {
        return $this->persone()
            ->wherePivot('tipo_carica', $tipoCarica);
    }

    /**
     * Filtra per partita IVA.
     */
    public function scopeWherePartitaIva($query, $partitaIva)
    {
        return $query->where('partita_iva', $partitaIva);
    }

    /**
     * Filtra per denominazione (ricerca parziale case-insensitive).
     */
    public function scopeWhereDenominazione($query, $denominazione)
    {
        return $query->where('denominazione', 'ilike', "%{$denominazione}%");
    }

    /**
     * Filtra per codice ATECO.
     */
    public function scopeWhereAteco($query, $ateco)
    {
        return $query->where('ateco', $ateco);
    }

    /**
     * Filtra per stato attività.
     */
    public function scopeWhereStatoAttivita($query, $stato)
    {
        return $query->where('stato_attivita', $stato);
    }

    /**
     * Filtra per data di costituzione.
     */
    public function scopeWhereDataCostituzione($query, $dataDa, $dataA = null)
    {
        $query->whereDate('data_costituzione', '>=', $dataDa);
        
        if ($dataA) {
            $query->whereDate('data_costituzione', '<=', $dataA);
        }
        
        return $query;
    }

    /**
     * Verifica se l'azienda è attiva.
     */
    public function isAttiva(): bool
    {
        return $this->stato_attivita === 'ATTIVA';
    }

    /**
     * Ottiene l'ultimo bilancio disponibile.
     */
    public function ultimoBilancio()
    {
        return $this->bilanci()->latest('data_chiusura')->first();
    }

    /**
     * Ottiene l'ultimo punteggio di scoring disponibile.
     */
    public function ultimoScoring()
    {
        return $this->scorings()->latest('data_riferimento')->first();
    }

    /**
     * Ottiene la sede legale.
     */
    public function sedeLegale()
    {
        return $this->sedi()->where('tipo_sede', 'LEGALE')->first();
    }
}
