<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PodcastModel extends Model
{
    use HasFactory;
    protected $table = 'podcasts';
    protected $appends = ['media'];

    public function serie()
    {
        return $this->belongsTo(PodcastSerieModel::class, 'serie_id');
    }
    public function getMediaAttribute()
    {
        return env('PODCAST_IMAGE_URL_PREFIX') . '/' . $this->id . '.' . $this->extention . '?v=' . env('CACHE_VERSION');
    }
}
