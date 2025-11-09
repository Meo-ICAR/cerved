<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provincia extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'provincie';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activity' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the region that owns the province.
     */
    public function regione()
    {
        return $this->belongsTo(Regione::class, 'region_code', 'code');
    }

    /**
     * Get the comuni for the province.
     */
    public function comuni(): HasMany
    {
        return $this->hasMany(Comune::class, 'province_code', 'province_code');
    }

    /**
     * Scope a query to only include active provinces.
     */
    public function scopeAttive(Builder $query): Builder
    {
        return $query->where('activity', true)
                    ->orWhere('flag_active', 'Y');
    }

    /**
     * Scope a query to filter by region code.
     */
    public function scopePerRegione(Builder $query, string $codiceRegione): Builder
    {
        return $query->where('region_code', $codiceRegione);
    }

    /**
     * Scope a query to find a province by ISTAT code.
     */
    public function scopePerCodiceIstat(Builder $query, string $codiceIstat): Builder
    {
        return $query->where('istat_code_province', $codiceIstat);
    }

    /**
     * Scope a query to find a province by its code (e.g., "RM" for Rome).
     */
    public function scopePerSigla(Builder $query, string $sigla): Builder
    {
        return $query->where('province_code', strtoupper($sigla));
    }

    /**
     * Check if the province is active.
     */
    public function isAttiva(): bool
    {
        return $this->activity === true || $this->flag_active === 'Y';
    }

    /**
     * Get the province code (alias for province_code).
     */
    public function getSiglaAttribute(): string
    {
        return $this->province_code;
    }

    /**
     * Get the province name (alias for province_description).
     */
    public function getNomeAttribute(): string
    {
        return $this->province_description;
    }

    /**
     * Get the ISTAT code (alias for istat_code_province).
     */
    public function getCodiceIstatAttribute(): string
    {
        return $this->istat_code_province;
    }

    /**
     * Find a province by its code.
     */
    public static function findByCode(string $code): ?self
    {
        return static::where('province_code', strtoupper($code))->first();
    }

    /**
     * Find a province by its ISTAT code.
     */
    public static function findByIstatCode(string $istatCode): ?self
    {
        return static::where('istat_code_province', $istatCode)->first();
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
