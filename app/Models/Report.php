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
        'user_id'
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
