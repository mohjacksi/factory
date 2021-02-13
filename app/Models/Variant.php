<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Variant extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait;

    public $table = 'variants';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

     protected $with =[
         'color',
         'size',
     ];

    protected $appends = [
        'image',
        'color_name',
        'size_name',
    ];

    protected $fillable = [
        'count',
        'price',
        'color_id',
        'is_available',
        'size_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }


    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function getImageAttribute()
    {
        $file = $this->getMedia('image')->last();

        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }

    public function products()
    {
        return $this->belongsToMany(\App\Models\Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    protected function getColorNameAttribute()
    {
        if ($this->color_id) {
            return Color::findOrFail($this->color_id)->name;
        }

        return null;
    }
    protected function getSizeNameAttribute()
    {
        if ($this->size_id) {
            return Size::findOrFail($this->size_id)->name;
        }

        return null;
    }


}
