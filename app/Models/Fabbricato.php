<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fabbricato extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fabbricati';

    protected $fillable = [
        'codice_immobile',
        'persona_id',
        'classe',
        'codice_comune',
        'codice_belfiore',
        'descrizione_comune',
        'codice_provincia',
        'foglio',
        'particella',
        'subalterno',
        'indirizzo',
        'piano',
        'codice_categoria',
        'descrizione_categoria',
        'unita_misura_consistenza',
        'valore_consistenza',
        'rendita',
        'stima',
    ];

    protected $casts = [
        'stima' => 'array',
        'valore_consistenza' => 'decimal:2',
        'rendita' => 'decimal:2',
        'subalterno' => 'integer',
    ];

    /**
     * Get the person that owns the fabbricato.
     */
    public function person()
    {
        return $this->belongsTo(Person::class, 'persona_id');
    }

    /**
     * Get all of the possessi for the fabbricato.
     */
    public function possessi()
    {
        return $this->morphMany(Possesso::class, 'possessibile');
    }

    /**
     * Get the estimated value.
     */
    public function getValoreStimatoAttribute()
    {
        return $this->stima['valorePuntuale'] ?? null;
    }

    /**
     * Get the confidence level of the estimate.
     */
    public function getLivelloConfidenzaAttribute()
    {
        return $this->stima['livelloConfidenza'] ?? null;
    }
}
