<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Person extends Model
{
    protected $fillable = [
        'id_soggetto',
        'nome',
        'cognome',
        'denominazione',
        'codice_fiscale',
        'data_nascita',
    ];

    protected $casts = [
        'data_nascita' => 'date',
    ];
    
    protected $dates = [
        'data_nascita',
    ];
    
    protected $appends = ['full_name'];
    
    public function setDataNascitaAttribute($value)
    {
        if ($value) {
            $this->attributes['data_nascita'] = \Carbon\Carbon::createFromFormat('d-m-Y', $value);
        }
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Get the cariche (corporate positions) for this person.
     */
    public function cariche()
    {
        return $this->hasMany(Carica::class, 'persona_id');
    }

    public function getDenominazioneAttribute(): string
    {
        return trim($this->nome . ' ' . $this->cognome);
    }
}
