<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Possesso extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'possessi';

    protected $fillable = [
        'descrizione_titolo',
        'titolarita_orig',
        'quota_orig',
        'percentuale_quota',
        'possessibile_id',
        'possessibile_type',
    ];

    protected $casts = [
        'percentuale_quota' => 'decimal:2',
    ];

    /**
     * Get the parent possessibile model (Fabbricato or Terreno).
     */
    public function possessibile(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if the possession represents full ownership.
     */
    public function isFullOwnership(): bool
    {
        return $this->percentuale_quota == 100 || 
               $this->quota_orig === '1/1' || 
               $this->quota_orig === '1';
    }
}
