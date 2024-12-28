<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'thumbnail',
        'marvel_id',
        'comics_available',
        'series_available',
        'stories_available',
        'events_available',
    ];

    public function getThumbnailAttribute($value)
    {
        return json_decode($value, true);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}
