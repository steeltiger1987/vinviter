<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Page extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function likes(){
        return $this->morphMany('App\Like', 'likeable');
    }

    /**
     * Get all of the page's attributes.
     */

    public function attributes()
    {
        return $this->morphToMany('App\Attribute', 'attributable');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function country(){
        return $this->belongsTo('App\Country');
    }
    public function city(){
        return $this->belongsTo('App\City');
    }

    public function keyPeople(){
        return $this->belongsToMany('App\User', 'key_person_page');
    }
    public function admins(){
        return $this->belongsToMany('App\User', 'admin_page');
    }
    public function inviteLists(){
        return $this->belongsToMany('App\InviteList', 'invite_list_page');
    }

    public function invitedUsers(){
        return $this->morphMany('App\Invitation', 'inviteable');
    }

    public function isListInvited($list_id){
        return $this->inviteLists()->where('invite_list_id', $list_id)->exists();
    }

    public function isUserInvited($user_id){
        return $this->invitedUsers()->where('user_id', $user_id)->exists();
    }


    public function scopeWherePageType($query, $parameter){
        return $query->whereHas('attributes', function($q) use ($parameter){
            $q->where('type', 'page.type')->where('type_info', '=', 'type_'.$parameter);
        });
    }
    public function getPageType(){
        return $this->attributes()->where('type', 'page.type');
    }
    public function venueUpcomingEvents(){
        return $this->hasMany('App\Event', 'venue_page_id')->upcoming();
    }
    public function venueHistoryEvents(){
        return $this->hasMany('App\Event', 'venue_page_id')->history();
    }
    public function creatorUpcomingEvents(){
        return $this->hasMany('App\Event', 'creator_page_id')->upcoming();
    }
    public function creatorHistoryEvents(){
        return $this->hasMany('App\Event', 'creator_page_id')->history();
    }

    public function scopeSaved($query){
        return $query->withoutGlobalScope('published')->where('published', 0);
    }

    public function followers(){
        return $this->morphToMany('App\User', 'followable', 'followables', 'followable_id', 'follower_id');
    }

    public function getNumberOfFollowersAttribute(){
        return $this->followers()->count();
    }

    public function getNumberOfVenueUpcomingEventsAttribute(){
        return $this->venueUpcomingEvents()->count();
    }

    public function getNumberOfVenueHistoryEventsAttribute(){
        return $this->venueHistoryEvents()->count();
    }

    public function getNumberOfCreatorUpcomingEventsAttribute(){
        return $this->creatorUpcomingEvents()->count();
    }

    public function getNumberOfCreatorHistoryEventsAttribute(){
        return $this->creatorHistoryEvents()->count();
    }

    public function getMainImageFullPathAttribute(){
        $mainImage = $this->main_image;

        if($mainImage){
            return 'pages/'.$this->id.'/images/'.$mainImage;
        }
        return 'default/page/all/avatar.png';
    }

    public function getPageUserRelationAttribute(){
        $relationName = '';
        $user = Auth::user();
        if($this->user_id == $user->id){
            $relationName = 'Creator';
        }
        elseif($this->admins()->where('user_id', $user->id)->exists()){
            $relationName = 'Admin';
        }
        return $relationName;
    }


    /**
     * Boot the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('published', function(Builder $builder) {
            $builder->where('published', '=', 1);
        });
    }

    public  function scopeLike($query, $field, $value){
        return $query->where($field, 'LIKE', "%$value%");
    }


    public function reports()
    {
        return $this->morphMany('App\Report', 'reportable');
    }

}
