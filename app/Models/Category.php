<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'image'
    ];

    public function dishes()
    {
        return $this->hasMany(Dish::class);
    }

    public static function getAllCategories()
    {
        return self::all();
    }

}
