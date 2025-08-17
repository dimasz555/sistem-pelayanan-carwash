<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Size extends Model
{
    use SoftDeletes;

    protected $table = 'sizes';
    protected $fillable = [
        'name',
    ];

    /**
     * Get the services associated with the size.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
