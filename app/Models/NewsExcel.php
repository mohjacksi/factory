<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class NewsExcel extends Model implements HasMedia
{
    use  HasMediaTrait;

    protected $fillable = [
        'user_id',
        'is_read',
    ];

    public $table = 'news_excels';


    protected $appends = [
        'file',
    ];



    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function getFileAttribute()
    {
        $file = $this->getMedia('file')->last();

        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
