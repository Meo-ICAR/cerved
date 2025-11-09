<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class Address extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tipo_indirizzo',
        'indirizzo',
        'cap',
        'codice_comune',
        'comune',
        'codice_comune_istat',
        'sigla_provincia',
        'provincia',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'tipo_indirizzo' => 'SEDE_LEGALE',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_address'];

    /**
     * Get the parent addressable model (Company or Person).
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the full address as a string
     *
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        return sprintf(
            '%s, %s %s (%s)',
            $this->indirizzo,
            $this->cap,
            $this->comune,
            $this->sigla_provincia
        );
    }

    /**
     * Scope a query to only include addresses of a specific type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $tipo
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $tipo)
    {
        return $query->where('tipo_indirizzo', $tipo);
    }

    /**
     * Scope a query to only include legal addresses.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLegal($query)
    {
        return $query->where('tipo_indirizzo', 'SEDE_LEGALE');
    }

    /**
     * Scope a query to only include operational addresses.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOperational($query)
    {
        return $query->where('tipo_indirizzo', 'SEDE_OPERATIVA');
    }

    /**
     * Scope a query to only include addresses in a specific province.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $provinceCode
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInProvince($query, string $provinceCode)
    {
        return $query->where('sigla_provincia', strtoupper($provinceCode));
    }

    /**
     * Scope a query to only include addresses in a specific municipality.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $municipalityIstatCode
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInMunicipality($query, string $municipalityIstatCode)
    {
        return $query->where('codice_comune_istat', $municipalityIstatCode);
    }

    /**
     * Get the province model associated with the address.
     */
    public function provinciaModel()
    {
        return $this->belongsTo(Provincia::class, 'sigla_provincia', 'province_code');
    }

    /**
     * Get the comune model associated with the address.
     */
    public function comuneModel()
    {
        return $this->belongsTo(Comune::class, 'codice_comune_istat', 'istat_code');
    }

    /**
     * Get the address type as a human-readable string.
     *
     * @return string
     */
    public function getTipoIndirizzoEstesoAttribute(): string
    {
        $tipi = [
            'SEDE_LEGALE' => 'Sede Legale',
            'SEDE_OPERATIVA' => 'Sede Operativa',
            'DOMICILIO' => 'Domicilio',
            'RESIDENZA' => 'Residenza',
        ];

        return $tipi[$this->tipo_indirizzo] ?? $this->tipo_indirizzo;
    }
}
