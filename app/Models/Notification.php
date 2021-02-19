<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Notification extends Model
{
    use SoftDeletes;

    public $table = 'custom_notifications';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $with =[
        'city'
    ];

    protected $fillable = [
        'title',
        'content',
        'city_id',
        'model_type',
        'model_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * belongs to relation
     */
    public function city()
    {
        return $this->belongsTo(City::class,'city_id');
    }

    public function getModelAttribute()
    {
        $className = 'App\\Models\\'.$this->model_type;

        $model =  new $className;

        return $model->findOrFail($this->model_id)->name;

    }
}
