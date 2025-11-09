<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Protesto extends Model
{
    use HasFactory;

    /**
     * Specifica il nome della tabella.
     */
    protected $table = 'protesti';

    // --- Type-hint delle proprietà ---
    public int $id;
    public int $persona_id;
    public string $tipo_protesto; // Es. "Assegno", "Cambiale"
    public ?Carbon $data_evento;
    public ?string $importo; // Cast 'decimal:2'
    public ?string $camera_commercio;
    public ?array $dati_protesto_completi; // JSON
    public ?Carbon $created_at;
    public ?Carbon $updated_at;
    // --- Fine Type-hint ---
    
    /**
     * I campi che possono essere assegnati in massa.
     */
    protected $fillable = [
        'persona_id',
        'tipo_protesto',
        'data_evento',
        'importo',
        'camera_commercio',
        'dati_protesto_completi',
    ];

    /**
     * Cast per tipi di dato.
     */
    protected $casts = [
        'data_evento' => 'date',
        'importo' => 'decimal:2',
        'dati_protesto_completi' => 'array',
    ];

    /**
     * Relazione: Un protesto appartiene a una persona.
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Filtra per tipo di protesto.
     */
    public function scopeWhereTipo($query, $tipo)
    {
        return $query->where('tipo_protesto', $tipo);
    }

    /**
     * Filtra per intervallo di date dell'evento.
     */
    public function scopeWhereDataEvento($query, $dataDa, $dataA = null)
    {
        $query->whereDate('data_evento', '>=', $dataDa);
        
        if ($dataA) {
            $query->whereDate('data_evento', '<=', $dataA);
        }
        
        return $query;
    }

    /**
     * Filtra per intervallo di importo.
     */
    public function scopeWhereImporto($query, $min, $max = null)
    {
        $query->where('importo', '>=', $min);
        
        if ($max) {
            $query->where('importo', '<=', $max);
        }
        
        return $query;
    }

    /**
     * Filtra per camera di commercio.
     */
    public function scopeWhereCameraCommercio($query, $cameraCommercio)
    {
        return $query->where('camera_commercio', 'ilike', "%{$cameraCommercio}%");
    }

    /**
     * Filtra per protesti attivi (senza data di fine).
     */
    public function scopeAttivi($query)
    {
        return $query->whereNull('data_fine');
    }

    /**
     * Verifica se il protesto è attivo.
     */
    public function isAttivo(): bool
    {
        return is_null($this->data_fine);
    }

    /**
     * Formatta l'importo come valuta.
     */
    public function formattaImporto(): string
    {
        return number_format($this->importo, 2, ',', '.') . ' €';
    }

    /**
     * Ottiene l'età della persona alla data del protesto.
     */
    public function getEtaAllaDataEvento(): ?int
    {
        if (!$this->data_evento || !$this->persona->data_nascita) {
            return null;
        }

        return $this->data_evento->diffInYears($this->persona->data_nascita);
    }
}
