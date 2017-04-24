<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Page;
use App\Like;
use App\HistoryEventPhoto;
use Auth;

class User extends Authenticatable
{
	use SoftDeletes;

	protected $appends  = ['avatarFullPath'];
	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	'name', 'username', 'email', 'gender', 'city_id', 'region_id', 'country_id', 'timezone', 'password'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
	'password', 'remember_token',
	];

	/**
	 * Boot the model.
	 *
	 * @return void
	 */
	public static function boot()
	{
		parent::boot();

		static::creating(function ($user) {
			$user->token = str_random(40);
		});

		static::addGlobalScope('verified', function(Builder $builder) {
			$builder->where('verified', '=', 1);
		});
	}

	/**
	 * Confirm the user.
	 *
	 * @return void
	 */
	public function confirmEmail()
	{
		$this->verified = true;
		$this->token = null;
		$this->save();
	}

	/**
	 * Get the route key for the model.
	 *
	 * @return string
	 */
	public function getRouteKeyName()
	{
		return 'username';
	}

	public  function scopeLike($query, $field, $value)
	{
		return $query->where($field, 'LIKE', "%$value%");
	}

	public function keyPersonPages()
	{
		return $this->belongsToMany('App\Page', 'key_person_page');
	}

	public function attendingEvents()
	{
		return $this->belongsToMany('App\Event', 'attendee_event', 'attendee_id', 'event_id');
	}

	public function attendedEvents()
	{
		return $this->belongsToMany('App\Event', 'attendee_event', 'attendee_id', 'event_id');
	}

	public function adminOfEvents()
	{
		return $this->belongsToMany('App\Event', 'admin_event', 'user_id', 'event_id');
	}


	public function adminOfPages()
	{
		return $this->belongsToMany('App\Page', 'admin_page', 'user_id', 'page_id');
	}


	public function followers()
	{
		return $this->morphToMany('App\User', 'followable', 'followables', 'followable_id', 'follower_id');
	}

	public function following()
	{
		return $this->morphedByMany('App\User', 'followable', 'followables', 'follower_id', 'followable_id');
	}


	public function followingPages()
	{
		return $this->morphedByMany('App\Page', 'followable', 'followables', 'follower_id', 'followable_id');
	}

	public function likedComments()
	{
		return $this->hasMany('App\Like', 'user_id')->where('likeable_type', 'App\Comment');
	}

	public function likedPhotos()
	{
		return $this->hasMany('App\Like', 'user_id')->where('likeable_type', 'App\HistoryEventPhoto');
	}

	public function wasInvitedToPages(){
		return $this->hasMany('App\Invitation')->where('inviteable_type', 'App\Page');
	}

	public function wasInvitedToEvents(){
		return $this->hasMany('App\Invitation')->where('inviteable_type', 'App\Event');
	}

	public function isFollowerOfThePage($page){
		return $this->followingPages()->where('followable_id', $page->id)->exists();
	}

	public function isFollowerOfTheUser($user){
		return $this->following()->where('followable_id', $user->id)->exists();
	}

	public function isAdminOfTheEvent($event){
		return $this->adminOfEvents()->where('event_id', $event->id)->exists();
	}

	public function hasBasicAccessToEvent($event){
		if(!$event->is_private){
			return true;
		}

		$isAllowed = false;
		if($event->user_id == $this->id || $this->isAdminOfTheEvent($event)){
			$isAllowed = true;
		}
		elseif($this->wasInvitedToEvents()->where('inviteable_id', $event->id)->exists()){
			$isAllowed = true;
		}
		return $isAllowed;
	}

	public function isAdminOfThePage($page){
		return $this->adminOfPages()->where('page_id', $page->id)->exists();
	}


	public function doesLikeTheComment($comment){
		return $this->likedComments()->where('likeable_id', $comment->id)->exists();
	}

	public function doesLikeThePhoto($photo){
		return $this->likedPhotos()->where('likeable_id', $photo->id)->exists();
	}

	public function isAttendingTheEvent($event){
		return $this->attendingEvents()->where('event_id', $event->id)->exists();
	}

	public function hasAttendedTheEvent($event){
		return $this->attendedEvents()->where('event_id', $event->id)->exists();
	}

	public function getStatusAttribute($value)
	{
		if(strlen($value) <= 0)
		{
			return 'Default status.';
		}
		return $value;
	}

	public function inviteLists()
	{
		return $this->hasMany('App\InviteList');
	}


	public function events()
	{
		return $this->hasMany('App\Event');
	}


	public function pages()
	{
		return $this->hasMany('App\Page');
	}

	public function savedPages()
	{
		return $this->pages()->withoutGlobalScope('published')->where('published', 0);
	}

	public function getAvatarFullPathAttribute(){
		if($this->avatar){
			return 'users/'.$this->id.'/avatars/'.$this->avatar;
		}

		if($this->gender == 'male'){
			return 'default/user/all/male_avatar.png';
		}
		else{
			return 'default/user/all/female_avatar.png';
		}
	}

	public function pageSettings(){
		return $this->hasMany('App\PageUserSetting');
	}

	public function singlePageSettings($pageID){
		$settings = $this->pageSettings()->where('page_id', $pageID)->first();
		if($settings){
			return $settings;
		}
		else{
			$settings = new \App\PageUserSetting;
			$settings->is_page_blocked = 0;
			$settings->receive_page_notications = 0;
			return $settings;
		}       
	}


	public function likedHistoryEventPhotos($privateEventIds = null){
		$photo  = new HistoryEventPhoto;

		if(is_array($privateEventIds)){
			if(!Auth::check()){
				$query = $photo
				->join('likes', 'history_event_photos.id', '=', 'likes.likeable_id')
				->join('events', 'events.id', '=', 'history_event_photos.event_id')
				->where('likes.likeable_type', 'App\HistoryEventPhoto')
				->where('likes.user_id', $this->id)
				->whereNull('likes.deleted_at')
				->where('events.is_private', 0)
				->select(['likes.*', 'likes.id as like_id', 'history_event_photos.*', 'events.id as event_id', 'events.is_private']);
			}
			else{
				$query = $photo
				->join('likes', 'history_event_photos.id', '=', 'likes.likeable_id')
				->join('events', 'events.id', '=', 'history_event_photos.event_id')
				->where('likes.likeable_type', 'App\HistoryEventPhoto')
				->where('likes.user_id', $this->id)
				->whereNull('likes.deleted_at')
				->where(function($q) use($privateEventIds){
					$q->where('events.is_private', 1)
					->whereIn('events.id', $privateEventIds)
					->orWhere('events.is_private', 0);
				})
				->select(['likes.*', 'likes.id as like_id', 'history_event_photos.*', 'events.id as event_id', 'events.is_private']);
			}
		}
		else{
			$query = $photo
			->join('likes', 'history_event_photos.id', '=', 'likes.likeable_id')
			->where('likes.likeable_type', 'App\HistoryEventPhoto')
			->where('likes.user_id', $this->id)
			->whereNull('likes.deleted_at');
		}
		return $query;
	}


	public function reports()
	{
		return $this->morphMany('App\Report', 'reportable');
	}

	public function notifications(){
		return $this->hasMany('App\Notification', 'user_id');
	}

	public function getNumberOfLikedHistoryEventPhotosAttribute(){
		return $this->likedHistoryEventPhotos()->count();
	}


	public function getNumberOfAttendingEventsAttribute(){
		return $this->attendingEvents()->upcoming()->count();
	}


	public function getNumberOfAttendedEventsAttribute(){
		return $this->attendedEvents()->history()->count();
	}

	public function getNumberOfFollowingAttribute(){
		return $this->following()->count();
	}

	public function getNumberOfFollowersAttribute(){
		return $this->followers()->count();
	}

	public function getNumberOfFollowingPagesAttribute(){
		return $this->followingPages()->count();
	}
}
