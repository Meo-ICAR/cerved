<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Provincia extends Model
{
    use HasFactory;

    /**
     * Nome della tabella.
     *
     * @var string
     */
    protected $table = 'provincie';

    /**
     * I campi che possono essere assegnati in massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'activity',
        'flag_active',
        'region_code',
        'istat_code_province',
        'province_code',
        'province_description',
    ];

    /**
     * I tipi di dato per gli attributi.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activity' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope per filtrare le province attive.
     */
    public function scopeAttive(Builder $query): Builder
    {
        return $query->where('activity', true)
                    ->orWhere('flag_active', 'Y');
    }

    /**
     * Scope per filtrare per codice regione.
     */
    public function scopePerRegione(Builder $query, string $codiceRegione): Builder
    {
        return $query->where('region_code', $codiceRegione);
    }

    /**
     * Scope per trovare una provincia per codice ISTAT.
     */
    public function scopePerCodiceIstat(Builder $query, string $codiceIstat): Builder
    {
        return $query->where('istat_code_province', $codiceIstat);
    }

    /**
     * Scope per trovare una provincia per sigla (es. "RM" per Roma).
     */
    public function scopePerSigla(Builder $query, string $sigla): Builder
    {
        return $query->where('province_code', strtoupper($sigla));
    }

    /**
     * Verifica se la provincia è attiva.
     */
    public function isAttiva(): bool
    {
        return $this->activity === true || $this->flag_active === 'Y';
    }

    /**
     * Accessor per la sigla della provincia.
     */
    public function getSiglaAttribute(): string
    {
        return $this->province_code;
    }

    /**
     * Accessor per il nome della provincia.
     */
    public function getNomeAttribute(): string
    {
        return $this->province_description;
    }

    /**
     * Relazione: Una provincia può avere molte sedi aziendali.
     */
    public function sediAziendali()
    {
        return $this->hasMany(SedeAzienda::class, 'provincia', 'province_code');
    }

    /**
     * Relazione: Una provincia può avere molte persone nate in essa.
     */
    public function personeNate()
    {
        return $this->hasMany(Person::class, 'provincia_nascita', 'province_code');
    }
}
