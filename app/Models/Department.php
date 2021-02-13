<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use \DateTimeInterface;

class Department extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait, Filterable;

    public $table = 'departments';

    protected $appends = [
        'logo',
        'type_of_category',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'about',
        'city_id',
        'item_number',
        'phone_number',
        'category_id',
        'sub_category_id',
        'show_in_main_page',
        'show_in_main_departments_page',
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

    public function getLogoAttribute()
    {
        $file = $this->getMedia('logo')->last();

        if ($file) {
            $file->url = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview = $file->getUrl('preview');
        }

        return $file;
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * @return BelongsTo
     */
    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
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


    public function trader()
    {
        return $this->belongsTo(Trader::class, 'trader_id');
    }


}
