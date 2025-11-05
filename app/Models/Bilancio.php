<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bilancio extends Model
{
    protected $fillable = [
        'azienda_id',
        'anno',
        'data_chiusura',
        'esercizio_chiuso',
        'valuta',
        'fatturato',
        'utile_perdita',
        'patrimonio_netto',
        'attivo_circolante',
        'totale_attivo',
        'totale_passivo',
        'dati_completi',
    ];

    protected $casts = [
        'data_chiusura' => 'date',
        'esercizio_chiuso' => 'boolean',
        'fatturato' => 'decimal:2',
        'utile_perdita' => 'decimal:2',
        'patrimonio_netto' => 'decimal:2',
        'attivo_circolante' => 'decimal:2',
        'totale_attivo' => 'decimal:2',
        'totale_passivo' => 'decimal:2',
        'dati_completi' => 'array',
    ];

    public function azienda(): BelongsTo
    {
        return $this->belongsTo(Azienda::class);
    }
}
