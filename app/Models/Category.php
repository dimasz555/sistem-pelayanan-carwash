<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    
    protected $table = 'categories';
    protected $fillable = [
        'name',
    ];

    /**
     * Get the services associated with the category.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
