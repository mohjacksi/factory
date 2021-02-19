<?php

namespace App\Models\Firebase;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FirebaseTokenModel extends Model
{

    protected $table = 'firebase_tokens';

    protected $fillable = [
        'token',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
