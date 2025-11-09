<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Company extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_soggetto',
        'denominazione',
        'codice_fiscale',
        'partita_iva',
        'codice_ateco',
        'ateco',
        'codice_ateco_infocamere',
        'ateco_infocamere',
        'codice_ateco_2025',
        'ateco_2025',
        'codice_ateco_infocamere_2025',
        'ateco_infocamere_2025',
        'codice_stato_attivita',
        'flag_operativa',
        'codice_rea',
        'data_iscrizione_rea',
        'is_ente',
        'tipo_ente',
        'is_fornitore',
        'is_partecipata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'flag_operativa' => 'boolean',
        'is_ente' => 'boolean',
        'is_fornitore' => 'boolean',
        'is_partecipata' => 'boolean',
        'data_iscrizione_rea' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'data_iscrizione_rea',
        'created_at',
        'updated_at',
    ];

    /**
     * Get all of the company's addresses.
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Get the company's legal address.
     */
    public function legalAddress()
    {
        return $this->morphOne(Address::class, 'addressable')
            ->where('tipo_indirizzo', 'SEDE_LEGALE');
    }

    /**
     * Get the people associated with the company through positions.
     */
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(
            Person::class,
            'cariche',
            'azienda_id',
            'persona_id'
        )->withPivot([
            'tipo_carica',
            'descrizione_carica',
            'data_inizio_carica',
            'data_fine_carica'
        ]);
    }

    /**
     * Get the company's active positions.
     */
    public function activePositions()
    {
        return $this->hasMany(Carica::class, 'azienda_id')
            ->whereNull('data_fine_carica');
    }

    /**
     * Get the company's balance sheets.
     */
    public function bilanci(): HasMany
    {
        return $this->hasMany(Bilancio::class, 'azienda_id');
    }

    /**
     * Get the company's latest balance sheet.
     */
    public function ultimoBilancio()
    {
        return $this->hasOne(Bilancio::class, 'azienda_id')
            ->latest('anno_riferimento');
    }

    /**
     * Scope a query to only include active companies.
     */
    public function scopeAttive(Builder $query): Builder
    {
        return $query->where('flag_operativa', true);
    }

    /**
     * Scope a query to only include companies with a specific ATECO code.
     */
    public function scopeWhereAteco(Builder $query, string $codiceAteco): Builder
    {
        return $query->where('codice_ateco', $codiceAteco)
            ->orWhere('codice_ateco_infocamere', $codiceAteco)
            ->orWhere('codice_ateco_2025', $codiceAteco)
            ->orWhere('codice_ateco_infocamere_2025', $codiceAteco);
    }

    /**
     * Scope a query to only include companies that are suppliers.
     */
    public function scopeFornitori(Builder $query): Builder
    {
        return $query->where('is_fornitore', true);
    }

    /**
     * Scope a query to only include public entities.
     */
    public function scopeEntiPubblici(Builder $query): Builder
    {
        return $query->where('is_ente', true);
    }

    /**
     * Check if the company is active.
     */
    public function isAttiva(): bool
    {
        return $this->flag_operativa === true;
    }

    /**
     * Check if the company is a public entity.
     */
    public function isEntePubblico(): bool
    {
        return $this->is_ente === true;
    }

    /**
     * Check if the company is a supplier.
     */
    public function isFornitore(): bool
    {
        return $this->is_fornitore === true;
    }

    /**
     * Check if the company is a subsidiary.
     */
    public function isPartecipata(): bool
    {
        return $this->is_partecipata === true;
    }

    /**
     * Get the company's ATECO code (prioritizing the 2025 version if available).
     */
    public function getAtecoCorrenteAttribute(): ?string
    {
        return $this->codice_ateco_2025 ?? $this->codice_ateco;
    }

    /**
     * Get the company's description of the ATECO code (prioritizing the 2025 version if available).
     */
    public function getAtecoDescrizioneAttribute(): ?string
    {
        return $this->ateco_2025 ?? $this->ateco;
    }

    /**
     * Set the data_iscrizione_rea attribute.
     */
    public function setDataIscrizioneReaAttribute($value)
    {
        $this->attributes['data_iscrizione_rea'] = $value ? Carbon::parse($value) : null;
    }
}
