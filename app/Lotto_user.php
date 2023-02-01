<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lotto_user extends Model
{
    protected $fillable = ['user_id','username','tel','lotto_number','nickname','comment','comment_datetime','datetime_prediction','created_at','updated_at'];
}
