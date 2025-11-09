<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Person extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'people';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_soggetto',
        'nome',
        'cognome',
        'denominazione',
        'codice_fiscale',
        'sesso',
        'data_nascita',
        'comune_nascita',
        'provincia_nascita',
        'nazione_nascita',
        'indirizzo_residenza',
        'cap_residenza',
        'comune_residenza',
        'provincia_residenza',
        'telefono',
        'email',
        'ultimo_aggiornamento_cerved',
        'dati_anagrafici_completi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data_nascita' => 'date',
        'ultimo_aggiornamento_cerved' => 'datetime',
        'dati_anagrafici_completi' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name', 'nome_completo', 'denominazione'];

    /**
     * Set the person's date of birth.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setDataNascitaAttribute($value)
    {
        if ($value) {
            $this->attributes['data_nascita'] = is_string($value) 
                ? Carbon::createFromFormat('d-m-Y', $value)
                : $value;
        }
    }

    /**
     * Get all of the addresses for the person.
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Get all positions (cariche) held by the person.
     */
    public function cariche(): HasMany
    {
        return $this->hasMany(Carica::class, 'persona_id');
    }

    /**
     * The companies that the person is related to through positions.
     */
    public function aziende(): BelongsToMany
    {
        return $this->belongsToMany(
            Company::class,
            'cariche',
            'persona_id',
            'azienda_id'
        )->withPivot([
            'tipo_carica',
            'descrizione_carica',
            'data_inizio_carica',
            'data_fine_carica'
        ]);
    }

    /**
     * Get all protests associated with the person.
     */
    public function protesti(): HasMany
    {
        return $this->hasMany(Protesto::class, 'persona_id');
    }

    /**
     * Get the person's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->nome . ' ' . $this->cognome);
    }

    /**
     * Get the person's full name (alias for full_name).
     *
     * @return string
     */
    public function getNomeCompletoAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Get the person's denomination (alias for full_name).
     *
     * @return string
     */
    public function getDenominazioneAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Scope a query to filter by tax code (codice fiscale).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $codiceFiscale
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereCodiceFiscale($query, $codiceFiscale)
    {
        return $query->where('codice_fiscale', $codiceFiscale);
    }

    /**
     * Scope a query to filter by last name (case-insensitive).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $cognome
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereCognome($query, $cognome)
    {
        return $query->where('cognome', 'ilike', "%{$cognome}%");
    }

    /**
     * Scope a query to filter by first name (case-insensitive).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $nome
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereNome($query, $nome)
    {
        return $query->where('nome', 'ilike', "%{$nome}%");
    }

    /**
     * Scope a query to filter by birth date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $dataNascita
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereDataNascita($query, $dataNascita)
    {
        return $query->whereDate('data_nascita', $dataNascita);
    }

    /**
     * Scope a query to filter by place of birth.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $comune
     * @param  string|null  $provincia
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereLuogoNascita($query, $comune, $provincia = null)
    {
        $query->where('comune_nascita', 'ilike', "%{$comune}%");

        if ($provincia) {
            $query->where('provincia_nascita', $provincia);
        }

        return $query;
    }

    /**
     * Check if the person has any active positions.
     *
     * @return bool
     */
    public function haCaricheAttive(): bool
    {
        return $this->cariche()->whereNull('data_fine_carica')->exists();
    }

    /**
     * Get the person's active positions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function caricheAttive()
    {
        return $this->cariche()->whereNull('data_fine_carica')->with('azienda');
    }

    /**
     * Get the person's open protests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function protestiAperti()
    {
        // Assuming a protest is 'open' if it doesn't have a closing date
        // Modify according to your specific business logic
        return $this->protesti()->whereNull('data_chiusura');
    }

    /**
     * Get all properties (fabbricati) owned by the person.
     */
    public function fabbricati()
    {
        return $this->hasMany(Fabbricato::class, 'persona_id');
    }
}
