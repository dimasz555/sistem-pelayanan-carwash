<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'promos';
    protected $fillable = [
        'name',
        'description',
        'value',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Calculate discount amount (percentage based)
     */
    public function calculateDiscount(float $servicePrice): float
    {
        if (!$this->isAvailable()) {
            return 0;
        }

        // Calculate percentage discount
        $discountAmount = ($this->value / 100) * $servicePrice;

        return round($discountAmount, 2);
    }

    /**
     * Check if promo is available for use
     */
    public function isAvailable(): bool
    {
        // Check if promo is active
        if (!$this->is_active) {
            return false;
        }

        // Check date range
        $today = now()->toDateString();
        if ($today < $this->start_date || $today > $this->end_date) {
            return false;
        }

        return true;
    }

    /**
     * Scope for active promos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Get formatted value
     */
    public function getFormattedValueAttribute()
    {
        return $this->value . '%';
    }
}
