<?php

namespace App\Models;

use App\Helpers\DurationOfReading;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'image'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commenttable');
    }

    public function getReadingDurationAttribute()
    {
        return \app(DurationOfReading::class)
            ->setText($this->description)
            ->getDurationPerMin();
    }
}
