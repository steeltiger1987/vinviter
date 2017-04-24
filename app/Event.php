<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Http\Controllers\UserController;
use DB;

class Event extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at', 'starts_at', 'ends_at'];
    

    /**
     * Get all of the events's attributes.
     */
    public function attributes()
    {
        return $this->morphToMany('App\Attribute', 'attributable');
    }


    public function images(){
        return $this->morphMany('App\Image', 'imageable');
    }


    public function mainImage(){
        return $this->images()->where('is_main_image', 1)->first();
    }


    public function admins(){
        return $this->belongsToMany('App\User', 'admin_event');
    }


    public function country(){
        return $this->belongsTo('App\Country');
    }

    public function region(){
        return $this->belongsTo('App\Region');
    }


    public function city(){
        return $this->belongsTo('App\City');
    }


    public function venuePage(){
        return $this->belongsTo('App\Page', 'venue_page_id');
    }


    public function creatorPage(){
        return $this->belongsTo('App\Page', 'creator_page_id');
    }


    public function scopeUpcoming($query){
        return $query->where('starts_at', '>', UserController::getUsersTimeInUTC()->toDateTimeString());
    }


    public function scopeHistory($query){
        return $query->where('starts_at', '<', UserController::getUsersTimeInUTC()->toDateTimeString());
    }

    public function getIsHistoryAttribute(){
        return $this->starts_at->lt(UserController::getUsersTime());
    }

    public function scopeSaved($query){
        return $query->withoutGlobalScope('published')->where('published', 0);
    }


    public function attendees(){
        return $this->belongsToMany('App\User', 'attendee_event', 'event_id', 'attendee_id');
    }


    /* Convert UTC starting time and ending time timestamp to time in user's timezone */
    public function getStartsAtAttribute($value){
        $carbon = new Carbon($value, 'UTC');
        if(\Auth::check()){
            $carbon = $carbon->timezone(\Auth::user()->timezone);
        }
        return $carbon;
    }


    public function getEndsAtAttribute($value){
        $carbon = new Carbon($value, 'UTC');
        if(\Auth::check()){
            $carbon = $carbon->timezone(\Auth::user()->timezone);
        }
        return $carbon;
    }


    public function comments(){
        return $this->hasMany('App\Comment')->where('parent_id', NULL);
    }


    public function getNumberOfCommentsAttribute(){
        return $this->comments->count();
    }


    public function getNumberOfHistoryPhotosAttribute(){
        return $this->historyPhotos()->count();
    }

    public function getNumberOfAttendeesAttribute(){
        return $this->attendees()->count();
    }

    public function getNumberOfInvitedUsersAttribute(){
        return $this->invitedUsers()->count();
    }


    public function getMainImageFullPathAttribute(){
        $mainImage = $this->mainImage();

        if($mainImage){
            return 'events/'.$this->id.'/images/'.$mainImage->name;
        }
        return 'default/event/all/avatar.png';
    }


    public  function scopeLike($query, $field, $value){
        return $query->where($field, 'LIKE', "%$value%");
    }

    public function scopePublic($query){
        return $query->where('is_private', 0);
    }

    public function historyPhotos(){
        return $this->hasMany('App\HistoryEventPhoto');
    }

    public function scopeAccessibleToUser($query, $privateEventIds){
        return $query->where('is_private', 0)->orWhere(function($q) use($privateEventIds){
            $q->where('is_private', 1)
            ->whereIn('id', $privateEventIds);
        });
    }


    public function scopeFilterByRequest($query, $request, $includeTime = true){
        if($request->has('city')){
            $query->where('city_id', $request->city);
        }
        elseif($request->has('region')){
            $query->where('region_id', $request->region);
        }
        elseif($request->has('country')){
            $query->where('country_id', $request->country);
        }

        if($request->has('type')){
            $query->whereHas('attributes', function($q) use($request)
            {
                $q->where('attribute_id', $request->type)->where('type', 'event.type');
            });
        }

        if($request->has('music')){
            $query->whereHas('attributes', function($q) use($request)
            {
                $q->where('attribute_id', $request->music)->where('type', 'event.music');
            });
        }

        if($includeTime){
            /* Convert month, year to UTC before doing where query */
            if($request->has('year') && $request->has('month')){
                if(\Auth::check()){
                    $carbon = Carbon::createFromDate($request->year, $request->month, null, \Auth::user()->timezone);
                    $carbon->timezone('UTC');
                    $year = $carbon->year;
                    $month = $carbon->month;
                }
                else{
                    $year = $request->year;
                    $month = $request->month;
                }

                $query->whereYear('starts_at', '=', $year);
                $query->whereMonth('starts_at', '=', $month);

                return $query;
            }

            if($request->has('year')){
                if(\Auth::check()){
                    $carbon = Carbon::createFromDate($request->year, null, null, \Auth::user()->timezone);
                    $carbon->timezone('UTC');
                    $query->whereYear('starts_at', '=', $carbon->year);
                }
                else{
                    $query->whereYear('starts_at', '=', $request->year);
                }
            }
            if($request->has('month')){
                $query->whereMonth('starts_at', '=', $request->month);
            }
        }

        return $query;
    }


    public function inviteLists(){
        return $this->belongsToMany('App\InviteList', 'event_invite_list');
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


}
