<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiUsage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product',
        'statistics',
        'report_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'statistics' => 'array',
        'report_date' => 'date',
    ];

    /**
     * Get the total number of requests for this product
     *
     * @return int
     */
    public function getTotalRequestsAttribute(): int
    {
        return $this->statistics['total_requests'] ?? 0;
    }

    /**
     * Get the statistics by status code
     *
     * @return array
     */
    public function getStatisticsByStatusAttribute(): array
    {
        return $this->statistics['by_status'] ?? [];
    }

    /**
     * Get the number of requests by status code
     *
     * @return array
     */
    public function getRequestsByStatusAttribute(): array
    {
        $result = [];
        foreach ($this->statistics_by_status as $status) {
            $result[$status['status']] = $status['count'];
        }
        return $result;
    }
}
