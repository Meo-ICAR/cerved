<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Comune extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comuni';

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
        'belfiore_code',
        'istat_code_municipality',
        'istat_code_province',
        'municipality_code',
        'municipality_description',
        'province_code',
        'zip_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the provincia that owns the comune.
     */
    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class, 'province_code', 'province_code');
    }

    /**
     * Scope a query to only include active comuni.
     */
    public function scopeAttivi(Builder $query): Builder
    {
        // If there's an 'attivo' column, you can uncomment the following line
        // return $query->where('attivo', true);
        return $query;
    }

    /**
     * Scope a query to find a comune by its ISTAT code.
     */
    public function scopePerCodiceIstat(Builder $query, string $codiceIstat): Builder
    {
        return $query->where('istat_code_municipality', $codiceIstat);
    }

    /**
     * Scope a query to find comuni by province code.
     */
    public function scopeDellaProvincia(Builder $query, string $codiceProvincia): Builder
    {
        return $query->where('province_code', strtoupper($codiceProvincia));
    }

    /**
     * Scope a query to search comuni by name.
     */
    public function scopeCercaPerNome(Builder $query, string $nome): Builder
    {
        return $query->where('municipality_description', 'like', '%' . $nome . '%');
    }

    /**
     * Get the comune name (alias for municipality_description).
     */
    public function getNomeAttribute(): string
    {
        return $this->municipality_description;
    }

    /**
     * Get the CAP (alias for zip_code).
     */
    public function getCapAttribute(): string
    {
        return $this->zip_code;
    }

    /**
     * Find a comune by its ISTAT code.
     */
    public static function findByCodiceIstat(string $codiceIstat): ?self
    {
        return static::where('istat_code_municipality', $codiceIstat)->first();
    }

    /**
     * Find a comune by its Belfiore code.
     */
    public static function findByCodiceBelfiore(string $codiceBelfiore): ?self
    {
        return static::where('belfiore_code', $codiceBelfiore)->first();
    }

    /**
     * Find comuni by province code.
     */
    public static function getByProvincia(string $codiceProvincia)
    {
        return static::where('province_code', strtoupper($codiceProvincia))->get();
    }

    /**
     * Search comuni by name.
     */
    public static function searchByName(string $nome)
    {
        return static::where('municipality_description', 'like', '%' . $nome . '%')->get();
    }

    /**
     * Get the province of the comune.
     */
    public function getProvinciaAttribute()
    {
        return $this->provincia()->first();
    }
}
