<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class InviteList extends Model
{
	use SoftDeletes;
	
    protected $fillable = ['name', 'user_id'];

    public function members(){
    	return $this->belongsToMany('App\User', 'invite_list_member', 'list_id', 'user_id');
    }

    public function getTotalMembersAttribute(){
        return $this->members()->count();
    }
}
