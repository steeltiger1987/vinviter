<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Like extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'likeable_id'];
    protected $dates = ['deleted_at'];

    public function historyEventPhoto(){
    	return $this->morphTo('App\HistoryEventPhoto', 'likes');
    }
}
