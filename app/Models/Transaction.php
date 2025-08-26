<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $table = 'transactions';
    protected $fillable = [
        'cashier_name',
        'invoice',
        'customer_id',
        'service_id',
        'queue_number',
        'plate_number',
        'vehicle_name',
        'status',
        'is_free',
        'transaction_at',
        'waiting_at',
        'processing_at',
        'done_at',
        'is_paid',
        'paid_at',
        'total_price',
        'service_price',
        'promo_id',
        'promo_discount',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    /**
     * Calculate total price after discount
     */
    public function calculateTotalPrice(): float
    {
        $servicePrice = $this->service_price ?? 0;
        $discountAmount = $this->promo_discount ?? 0;

        return max(0, $servicePrice - $discountAmount);
    }

    public function getDiscountPercentageAttribute(): float
    {
        if (!$this->service_price || !$this->promo_discount) {
            return 0;
        }

        return round(($this->promo_discount / $this->service_price) * 100, 2);
    }

    /**
     * Auto calculate total price before saving
     */

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($transaction) {
            if ($transaction->promo && $transaction->promo->isAvailable()) {
                $transaction->promo_discount = $transaction->promo->calculateDiscount($transaction->service_price);
            } else {
                $transaction->promo_discount = 0;
            }

            $transaction->total_price = $transaction->calculateTotalPrice();
        });
    }

    public function getTrackingStepsAttribute()
    {
        return [
            'pending' => 'Pesanan Diterima',
            'waiting' => 'Kendaraan Masuk Antrian',
            'processing' => 'Proses Pencucian Dimulai',
            'finishing' => 'Finishing & Quality Check',
            'done' => 'Selesai - Siap Diambil'
        ];
    }

    public function getCurrentStepAttribute()
    {
        return $this->getTrackingStepsAttribute()[$this->status] ?? 'Status Tidak Diketahui';
    }
}
