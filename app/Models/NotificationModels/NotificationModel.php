<?php

namespace App\Models\NotificationModels;

use App\Models\Order\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'type',
        'notifiable',
        'data',
        'read_at',
    ];


    protected $dates = [
        'updated_at',
        'read_at',
    ];


    protected $appends = array('model');

    public function getDataAttribute($value)
    {
        return json_decode($value);
    }

    public function getIdAttribute($value)
    {
        return (string)$value;
    }

    public function getModelAttribute()
    {
        $className = 'App\\Models\\' . $this->data->model_type;

        $model = new $className;

        return $model::where('id', $this->data->model_id)->first();
    }


}

