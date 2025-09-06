<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    protected $fillable = [
        'id',
        'component_type',
        'brand',
        'model',
        'price',
        'stock',
        'image',
    ];

    // Virtual attribute for product name
    public function getNameAttribute()
    {
        return "{$this->brand} {$this->model}";
    }
}
