<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
	protected $fillable = ['user_id', 'inviteable_id', 'inviteable_type', 'inviter_id'];

	public function inviteable(){
		return $this->morphTo();
	}
}
