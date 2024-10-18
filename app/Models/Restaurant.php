<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Restaurant extends Model
{
    use HasFactory;

    public function categories() {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function regular_holiday() {
        return $this->belongsToMany(Regular_holiday::class)->withTimestamps();
    }

    protected $fillable = [
        'name',
        'description',
        'lowest_price',
        'highest_price',
        'postal_code',
        'address',
        'opening_time',
        'closing_time',
        'seating_capacity',
    ];
}
