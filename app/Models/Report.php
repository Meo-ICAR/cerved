<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'name',
        'piva',
        'israces',
        'annotation',
        'apicervedcode',
        'apicervedresponse',
        'apiactivation',
        'mediaresponse',
        'user_id',
        'id_soggetto',
        'codice_score',
        'descrizione_score',
        'valore',
        'categoria_codice',
        'categoria_descrizione',
        'file_uploaded_at'
    ];
    
    protected $dates = [
        'created_at',
        'updated_at',
        'file_uploaded_at'
    ];

    protected $casts = [
        'israces' => 'boolean',
        'apicervedresponse' => 'array',
        'mediaresponse' => 'array',
        'apiactivation' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
