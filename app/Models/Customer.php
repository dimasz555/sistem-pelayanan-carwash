<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $table = 'customers';
    protected $fillable = [
        'name',
        'sapaan',
        'phone',
        'address',
        'total_wash',
        'free_wash_count',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFullNameAttribute()
    {
        return trim($this->sapaan . ' ' . $this->name);
    }
}
