<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terreno extends Model
{
    use HasFactory;

    protected $table = 'terreni';

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
        'sezione_censuaria',
        'codice_porzione',
        'descrizione_qualita',
        'superficie_ettari',
        'superficie_are',
        'superficie_centiare',
        'rendita_dominicale',
        'rendita_agraria',
        'stima',
    ];

    protected $casts = [
        'superficie_ettari' => 'decimal:2',
        'superficie_are' => 'decimal:2',
        'superficie_centiare' => 'decimal:2',
        'rendita_dominicale' => 'decimal:2',
        'rendita_agraria' => 'decimal:2',
        'stima' => 'array',
    ];

    /**
     * Get the person that owns the terreno.
     */
    public function person()
    {
        return $this->belongsTo(Person::class, 'persona_id');
    }

    /**
     * Get the possessi for the terreno.
     */
    public function possessi()
    {
        return $this->morphMany(Possesso::class, 'possessibile');
    }

    /**
     * Get the total surface area in square meters.
     */
    public function getSuperficieTotaleAttribute()
    {
        $ettari = $this->superficie_ettari ?? 0;
        $are = $this->superficie_are ?? 0;
        $centiare = $this->superficie_centiare ?? 0;
        
        return ($ettari * 10000) + ($are * 100) + $centiare;
    }
}
