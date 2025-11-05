<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SedeAzienda extends Model
{
    protected $table = 'sedi_aziende';

    protected $fillable = [
        'azienda_id',
        'tipo_sede',
        'indirizzo',
        'cap',
        'comune',
        'provincia',
        'regione',
        'nazione',
        'telefono',
        'email',
        'pec',
        'is_legale',
        'is_operativa',
    ];

    protected $casts = [
        'is_legale' => 'boolean',
        'is_operativa' => 'boolean',
    ];

    public function azienda(): BelongsTo
    {
        return $this->belongsTo(Azienda::class);
    }
}
