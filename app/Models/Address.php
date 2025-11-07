<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
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
     * Get the parent addressable model (Company or Person).
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the full address as a string
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
}
