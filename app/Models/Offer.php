<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use \DateTimeInterface;

class Offer extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait, Filterable;

    public $table = 'offers';

    protected $appends = [
        'images',
        'type_of_category',
    ];

    protected $with = [
        'city'
    ];
    protected $dates = [
        'add_date',
        'date_end',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'city_id',
        'description',
        'show_in_trader_page',
        'show_in_main_page',
        'show_in_main_offers_page',
        'category_id',
        'sub_category_id',
        'add_date',
        'date_end',
        'phone_number',
        'location',
        'price',
        'trader_id',
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

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function getAddDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setAddDateAttribute($value)
    {
        $this->attributes['add_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getDateEndAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDateEndAttribute($value)
    {
        $this->attributes['date_end'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function trader()
    {
        return $this->belongsTo(Trader::class, 'trader_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function getImagesAttribute()
    {
        $files = $this->getMedia('images');
        if ($files) {

            $files->each(function ($item) {
                $item->url = $item->getUrl();
                $item->thumbnail = $item->getUrl('thumb');
                $item->preview = $item->getUrl('preview');
            });
        }

        return $files;
    }


    public function showInTraderPage()
    {
        return $this->show_in_trader_page ? 'نعم' : 'لا';
    }


    public function showInMainPage()
    {
        return $this->show_in_main_page ? 'نعم' : 'لا';
    }


    /**
     * @return
     */
    public function getTypeOfCategoryAttribute()
    {
        $category = new \ReflectionClass(new Category);
//        $constants = array_flip($category->getConstants());
        $constants = $category->getConstants();
        return $this->category ? $constants['TYPE_RADIO'][$this->category->type] : '';
    }

}
