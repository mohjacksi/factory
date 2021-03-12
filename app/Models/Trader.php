<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use \DateTimeInterface;

class Trader extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait;

    public $table = 'traders';

    protected $appends = [
        'images',
        'type_of_trader',
    ];

    const TYPE_RADIO = [
        'service'    => 'خدمي',
        'commercial' => 'تجاري',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'city_id',
        'type',
        'address',
        'activeness',
        'phone_number',
        'details',
        'facebook_url',
        'whatsapp',
        'created_at',
        'updated_at',
        'deleted_at',
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

    public function getImagesAttribute()
    {
        $files = $this->getMedia('images');
        $files->each(function ($item) {
            $item->url       = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview   = $item->getUrl('preview');
        });

        return $files;
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'trader_id', 'id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'trader_id', 'id');
    }


    /**
     * @return
     */
    public function getTypeOfTraderAttribute()
    {
        $trader = new \ReflectionClass(new Trader);
//        $constants = array_flip($trader->getConstants());
        $constants = $trader->getConstants();
        return $this->trader ? $constants['TYPE_RADIO'][$this->trader->type] : '';
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
