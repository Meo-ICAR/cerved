<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scoring extends Model
{
    protected $fillable = [
        'azienda_id',
        'data_elaborazione',
        'punteggio',
        'classe_di_rischio',
        'probabile_fallimento',
        'limite_credito_consigliato',
        'fattori_rischio',
        'dettagli_analisi',
    ];

    protected $casts = [
        'data_elaborazione' => 'datetime',
        'punteggio' => 'integer',
        'probabile_fallimento' => 'float',
        'limite_credito_consigliato' => 'decimal:2',
        'fattori_rischio' => 'array',
        'dettagli_analisi' => 'array',
    ];

    public function azienda(): BelongsTo
    {
        return $this->belongsTo(Azienda::class);
    }
}
