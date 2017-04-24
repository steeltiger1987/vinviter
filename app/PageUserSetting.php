<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageUserSetting extends Model
{
	public $timestamps = false;
	protected $fillable   = ['user_id', 'page_id'];
}
