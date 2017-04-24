<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{

	use SoftDeletes;

	protected $fillable = ['user_id', 'notification_type', 'link_user_id', 'link_event_id', 'link_page_id'];

	public function linkUser(){
		return $this->belongsTo('App\User', 'link_user_id');
	}

	public function linkPage(){
		return $this->belongsTo('App\Page', 'link_page_id');
	}

	public function linkEvent(){
		return $this->belongsTo('App\Event', 'link_event_id');
	}

}
