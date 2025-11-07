<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
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

    protected $casts = [
        'flag_operativa' => 'boolean',
        'is_ente' => 'boolean',
        'is_fornitore' => 'boolean',
        'is_partecipata' => 'boolean',
        'data_iscrizione_rea' => 'date',
    ];

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function legalAddress()
    {
        return $this->morphOne(Address::class, 'addressable')
            ->where('tipo_indirizzo', 'SEDE_LEGALE');
    }
}
