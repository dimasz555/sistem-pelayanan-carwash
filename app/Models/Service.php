<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    use SoftDeletes;

    protected $table = 'services';
    protected $fillable = [
        'category_id',
        'size_id',
        'name',
        'description',
        'price',
        'thumbnail',
    ];
    protected $casts = [
        'price' => 'integer',
    ];  
    /**
     * Get the category that owns the service.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }           
    /**
     * Get the size that owns the service.
     */
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
    
    protected static function booted()
    {
        static::deleting(function ($service) {
            if ($service->thumbnail && Storage::disk('public')->exists($service->thumbnail)) {
                Storage::disk('public')->delete($service->thumbnail);
            }
        });
    }
}
