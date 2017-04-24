<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryEventPhoto extends Model
{
	protected $table = "history_event_photos";

	protected $fillable = ['name', 'user_id'];

	public function getFullPathAttribute(){
		return 'events/'.$this->event_id.'/history_photos/'.$this->name;
	}

	public function likes(){
		return $this->morphMany('App\Like', 'likeable');
	}

    public function getNumberOfLikesAttribute(){
        return $this->likes()->count();
    }
}
