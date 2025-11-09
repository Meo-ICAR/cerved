<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Persona extends Model
{
    use HasFactory;

    /**
     * Specifica il nome della tabella.
     */
    protected $table = 'persone';

    // --- Type-hint delle proprietà ---
    public int $id;
    public string $codice_fiscale; // Chiave univoca!
    public ?string $nome;
    public ?string $cognome;
    public ?Carbon $data_nascita;
    public ?string $comune_nascita;
    public ?string $provincia_nascita;
    public ?Carbon $ultimo_aggiornamento_cerved;
    public ?array $dati_anagrafici_completi; // JSON
    public ?Carbon $created_at;
    public ?Carbon $updated_at;
    // --- Fine Type-hint ---

    /**
     * I campi che possono essere assegnati in massa.
     */
    protected $fillable = [
        'codice_fiscale',
        'nome',
        'cognome',
        'data_nascita',
        'comune_nascita',
        'provincia_nascita',
        'ultimo_aggiornamento_cerved',
        'dati_anagrafici_completi',
    ];

    /**
     * Cast per tipi di dato.
     */
    protected $casts = [
        'data_nascita' => 'date',
        'ultimo_aggiornamento_cerved' => 'datetime',
        'dati_anagrafici_completi' => 'array',
    ];

    /**
     * Relazione: Una persona ha molte cariche (in diverse aziende).
     */
    public function cariche(): HasMany
    {
        return $this->hasMany(Carica::class);
    }

    /**
     * Relazione: Una persona può essere collegata a molte aziende
     * TRAMITE la tabella delle cariche.
     */
    public function aziende(): BelongsToMany
    {
        return $this->belongsToMany(
            Azienda::class,
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
     * Relazione: Una persona può avere molti protesti.
     */
    public function protesti(): HasMany
    {
        return $this->hasMany(Protesto::class);
    }

    /**
     * Filtra per codice fiscale.
     */
    public function scopeWhereCodiceFiscale($query, $codiceFiscale)
    {
        return $query->where('codice_fiscale', $codiceFiscale);
    }

    /**
     * Filtra per cognome (ricerca case-insensitive).
     */
    public function scopeWhereCognome($query, $cognome)
    {
        return $query->where('cognome', 'ilike', "%{$cognome}%");
    }

    /**
     * Filtra per nome (ricerca case-insensitive).
     */
    public function scopeWhereNome($query, $nome)
    {
        return $query->where('nome', 'ilike', "%{$nome}%");
    }

    /**
     * Filtra per data di nascita.
     */
    public function scopeWhereDataNascita($query, $dataNascita)
    {
        return $query->whereDate('data_nascita', $dataNascita);
    }

    /**
     * Filtra per luogo di nascita.
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
     * Restituisce il nome completo della persona.
     */
    public function getNomeCompletoAttribute(): string
    {
        return trim($this->nome . ' ' . $this->cognome);
    }

    /**
     * Verifica se la persona ha cariche attive.
     */
    public function haCaricheAttive(): bool
    {
        return $this->cariche()->whereNull('data_fine_carica')->exists();
    }

    /**
     * Ottiene le cariche attive della persona.
     */
    public function caricheAttive()
    {
        return $this->cariche()->whereNull('data_fine_carica')->with('azienda');
    }

    /**
     * Ottiene i protesti non risolti della persona.
     */
    public function protestiAperti()
    {
        // Assumendo che un protesto sia "aperto" se non ha una data di chiusura
        // Modificare in base alla logica specifica
        return $this->protesti();
    }
}
