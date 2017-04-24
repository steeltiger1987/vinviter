<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $appends = array('numberOfReplies', 'numberOfLikes');
    protected $fillable = ['user_id', 'event_id', 'parent_id', 'body'];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('order', function(Builder $builder) {
            $builder->oldest();
        });
    }

    public function replies(){
        return $this->hasMany('App\Comment', 'parent_id', 'id');
    }
    public function author(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    public function getNumberOfRepliesAttribute(){
        if($this->replies->count() > 0){
            return '('.$this->replies->count().')';
        }
        return '';
    }
    public function likes(){
        return $this->morphMany('App\Like', 'likeable');
    }
    public function getNumberOfLikesAttribute(){
        if($this->likes->count() > 0){
            $likes = ($this->likes->count() > 1) ? ' likes' : ' like';
            return $this->likes->count().$likes;
        }
        return '';
    }
}
