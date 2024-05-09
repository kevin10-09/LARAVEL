<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'website',
        'logo'
    ];

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function getTotalScore()
    {
        // Calculer le score total en additionnant les notes de chaque évaluation
        return $this->ratings()->sum('score');
    }
    
}
