<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comic extends Model
{
    use HasFactory;

    protected $fillable = [
        'marvel_id',
        'title',
        'price',
        'pages',
        'characters',
        'description',
        'publication_date',
        'thumbnail',
        'creators',
        'stories'
    ];

    public function getThumbnailAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getCreatorsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getStoriesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getCharactersAttribute($value)
    {
        return json_decode($value, true);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}