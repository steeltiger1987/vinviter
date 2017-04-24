<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Auth;
use App\Country;
use App\Region;
use App\City;
use App\Page;
use App\Like;
use View;
use DB;
use App\HistoryEventPhoto;

class UserController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth', ['only' => [
			'follow', 
			'unfollow', 
			'clearNotifications', 
			'updateAccount', 
			'updateProfile', 
			'updateStatus',
			'postFavoriteLike',
			'postPageNotifications',
			'delete',
			'ajaxBackgroundImage',
			'ajaxProfileImage'
			]]);
	}

	/* Show user profile */
	public function showProfile(User $user)
	{
		$userPrivateEventAccess = [];
		$accessibleEvents = 'public'; // for guests
		if(Auth::check()){
			$userPrivateEventAccess = $this->privateEventsWithAccess(Auth::user());
			$accessibleEvents       = 'accessibleToUser';
		}
		$user->favorites = $user->likedHistoryEventPhotos($userPrivateEventAccess)->orderBy('likes.likeable_id', 'DESC')->paginate(config('common.user_favorites_per_load'));
		$photoIDS        = $user->favorites->lists('likeable_id')->toArray();
		$favoritesLikes  = Like::whereIn('likeable_id', $photoIDS)->select("id", "likeable_id")->get()->groupBy('likeable_id')->toArray();

		$user->followingPages  = $user->followingPages()->with('getPageType', 'followers')->orderBy('id', 'DESC')->paginate(config('common.events_per_load'));
		
		$user->attendingEvents = $user->attendingEvents()->upcoming()->with('country', 'region', 'city')->$accessibleEvents($userPrivateEventAccess)->orderBy('id', 'DESC')->paginate(config('common.events_per_load'));
		$user->attendedEvents  = $user->attendedEvents()->history()->with('country', 'region', 'city')->$accessibleEvents($userPrivateEventAccess)->orderBy('id', 'DESC')->paginate(config('common.events_per_load'));
		$user->following       = $user->following()->orderBy('id', 'DESC')->paginate(config('common.general_follow_per_load'));
		$user->followers       = $user->followers()->orderBy('id', 'DESC')->paginate(config('common.general_follow_per_load'));

		return view('user.profile', compact('user', 'favoritesLikes'));
	}


	public function getAttendingEvents(User $user, Request $request){
		$this->validate($request, ['after' => 'required|exists:events,id']);

		$userPrivateEventAccess = [];
		$accessibleEvents = 'public'; // for guests
		if(Auth::check()){
			$userPrivateEventAccess = $this->privateEventsWithAccess(Auth::user());
			$accessibleEvents       = 'accessibleToUser';
		}

		$events = $user->attendingEvents()->upcoming()->$accessibleEvents($userPrivateEventAccess)->where('id', '<', $request->after)->orderBy('id', 'DESC')->take(config('common.events_per_load'))->get();
		$eventMode  = 'upcoming';
		$eventsHTML = '';

		if($events){
			foreach($events as $event){
				$eventsHTML .= View::make('events.event_row_template', compact('event', 'eventMode'));
			}
		}

		if($eventsHTML){
			return response(['html' => $eventsHTML, 'leftOff' => $events->last()->id], 200);
		}
		return response('error', 400);
	}


	public function getAttendedEvents(User $user, Request $request){
		$this->validate($request, ['after' => 'required|exists:events,id']);

		$userPrivateEventAccess = [];
		$accessibleEvents = 'public'; // for guests
		if(Auth::check()){
			$userPrivateEventAccess = $this->privateEventsWithAccess(Auth::user());
			$accessibleEvents       = 'accessibleToUser';
		}


		$events = $user->attendedEvents()->history()->$accessibleEvents($userPrivateEventAccess)->where('id', '<', $request->after)->orderBy('id', 'DESC')->take(config('common.events_per_load'))->get();
		$eventMode  = 'history';
		$eventsHTML = '';

		if($events){
			foreach($events as $event){
				$eventsHTML .= View::make('events.event_row_template', compact('event', 'eventMode'));
			}
		}

		if($eventsHTML){
			return response(['html' => $eventsHTML, 'leftOff' => $events->last()->id], 200);
		}
		return response('error', 400);
	}

	public static function getUsersTime()
	{
		$now = Carbon::now('UTC');
		if(Auth::check()){
			$now = $now->timezone(Auth::user()->timezone);
		}
		return $now;
	}

	public static function getUsersTimeInUTC()
	{
		$now = Carbon::now('UTC');
		if(Auth::check()){
			$now = $now->timezone(Auth::user()->timezone);
		}
		return $now->timezone('UTC');
	}

	public function follow(User $user)
	{
		if($user->id == Auth::id()){
			return response('error', 404);
		}
		$user->followers()->sync([Auth::id()], false);

		$notification                    = new \App\Notification;
		$notification->user_id           = $user->id;
		$notification->notification_type = 'user_follow';
		$notification->link_user_id      = Auth::id();

		$notification->save();
		return response(['buttonText' => 'Following'], 200);
	}

	public function unfollow(User $user)
	{
		if($user->id == Auth::id()){
			return response('error', 404);
		}
		$user->followers()->detach(Auth::id());
		return response(['buttonText' => 'Follow'], 200);
	}

	public function settingsView(Request $request)
	{
		$routeName = $request->route()->getName();
		$user      = Auth::user();
		$countries = $regions = $cities = $timezones = NULL;

		if($routeName == 'settings.profile')
		{
			$countryID = $user->country_id;
			$regionID  = $user->region_id;

			if($request->old('country') && is_numeric($request->old('country'))){
				$countryID = $request->old('country');
			}
			if($request->old('region') && is_numeric($request->old('region'))){
				$regionID  = $request->old('region');
			}
			$countries = Country::orderBy('name', 'ASC')->get();
			$regions   = Region::select('id', 'name')->where('country_id', $countryID)->orderBy('name', 'ASC')->get();
			$cities    = City::select('id', 'name')->where('region_id', $regionID)->orderBy('name', 'ASC')->get();
			$timezones = DB::table('timezones')->get();
		}

		return view('user.'.$routeName, compact('user', 'countries', 'regions', 'cities', 'timezones'));
	}

	/**
	 * Update user account information
	 *
	 * @param
	 * @return
	 */
	public function updateAccount(Request $request)
	{
		$user = Auth::user();

		$this->validate($request, [
			'full_name'        => 'required|max:255',
			'email'            => 'required|email|max:255|unique:users,email,'.Auth::id(),
			'current_password' => 'required_with:new_password|hash:'.$user->password,
			'new_password'     => 'required_with:current_password,new_password_confirmation|different:current_password|min:6|confirmed',
			]);

		$user->name   = $request->full_name;
		if($request->has('new_password'))
		{
			$user->password = bcrypt($request->new_password);
		}

		if($user->save()){
			if($request->has('new_password'))
			{
				Auth::logout();
				$request->session()->flash('password_changed', 'Please login with your new password.');
				return redirect()->route('auth.login');
			}
			$request->session()->flash('success', 'Account was updated successfully.');
		}
		return back();
	}

/**
	 * Update user profile information
	 *
	 * @param
	 * @return
	 */
public function updateProfile(Request $request)
{
	$this->validate($request, [
		'username' => 'required|regex:/^[a-zA-Z0-9]+$/|max:30|unique:users,username,'.Auth::id(),
		'gender'   => 'required|in:male,female',
		'country'  => 'required|exists:countries,id',
		'region'   => 'required|exists:regions,id,country_id,'.$request->country,
		'city'     => 'required|exists:cities,id,region_id,'.$request->region,
		'timezone' => 'required|timezone'
		]);

	$user           = Auth::user();

	$user->username   = strtolower($request->username);
	$user->gender     = $request->gender;
	$user->country_id = $request->country;
	$user->region_id  = $request->region;
	$user->city_id    = $request->city;
	$user->timezone   = $request->timezone;

	if($user->save()){
		$request->session()->flash('success', 'Profile was updated successfully.');
	}
	return back();
}


	/**
	 * Delete the user account
	 *
	 * @param
	 * @return
	 */
	public function delete(Request $request)
	{
		if($request->delete_account == 'Yes'){

			$user = Auth::user();
			Auth::logout();

			if($user->delete()){
				return redirect()->route('upcoming');
			}
		}
		return back();
	}

	/* Update user status */
	public function updateStatus(User $user, Request $request)
	{
		if($request->has('status') && strlen($request->status) <= 255 && $user->id == Auth::id()){
			$user->status = $request->status;
			if($user->save()){
				return $user->status;
			}
		}
		return response('Error', 404);
	}


	public function ajaxBackgroundImage(User $user, Request $request)
	{
		$this->authorize('isUserHimself', $user);

		$bgDirectory = base_path().'/public/uploads/users/'.Auth::id().'/backgrounds';

		// check for upload_max_file_size error
		if(isset($_FILES['profileBackgroundImage']) && $_FILES['profileBackgroundImage']['error'] == 1)
		{
			return response('File size exceeds the limit', 400);
		}

		// catch post_max_size error
		if(empty($_POST))
		{
			$post_size = trim(ini_get('post_max_size'));
			$post_size = substr($post_size, 0, -1);
			$post_size = ($post_size * 1024) * 1024;

			if ($post_size < $request->header('Content-Length'))
			{
				return response('File size exceeds the limit', 400);
			}

		}

		$this->validate($request, ['profileBackgroundImage' => 'image|max:10240']);

		if($request->hasFile('profileBackgroundImage') && $request->file('profileBackgroundImage')->isValid())
		{
			$ext = $request->file('profileBackgroundImage')->getClientOriginalExtension();
			$size = $request->file('profileBackgroundImage')->getSize();

			while(true)
			{
				$profileBackgroundImage = uniqid(rand(), true) . '_' . date('d_m_Y_H_i_s') . '.' . $ext;
				if(!file_exists($bgDirectory.'/'.$profileBackgroundImage)) break;
			}
			$request->file('profileBackgroundImage')->move($bgDirectory, $profileBackgroundImage);

			// Update background image path in database
			$user->background_image = $profileBackgroundImage;
			$user->save();

			$fullImagePath = "uploads/users/".Auth::id()."/backgrounds/".$profileBackgroundImage;
			return response($fullImagePath, 200);
		}

		return response('Something went wrong.', 400);
	}


	public function ajaxProfileImage(User $user, Request $request)
	{
		$this->authorize('isUserHimself', $user);

		$avatarsDirectory = base_path().'/public/uploads/users/'.Auth::id().'/avatars';

		// check for upload_max_file_size error
		if(isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 1)
		{
			return response('File size exceeds the limit', 400);
		}

		// catch post_max_size error
		if(empty($_POST))
		{
			$post_size = trim(ini_get('post_max_size'));
			$post_size = substr($post_size, 0, -1);
			$post_size = ($post_size * 1024) * 1024;

			if ($post_size < $request->header('Content-Length'))
			{
				return response('File size exceeds the limit', 400);
			}

		}

		$this->validate($request, ['profileImage' => 'image|max:10240']);

		if($request->hasFile('profileImage') && $request->file('profileImage')->isValid())
		{
			$ext = $request->file('profileImage')->getClientOriginalExtension();
			$size = $request->file('profileImage')->getSize();

			while(true)
			{
				$profileImage = uniqid(rand(), true) . '_' . date('d_m_Y_H_i_s') . '.' . $ext;
				if(!file_exists($avatarsDirectory.'/'.$profileImage)) break;
			}
			$request->file('profileImage')->move($avatarsDirectory, $profileImage);

			// Update profile image path in database
			$user->avatar = $profileImage;
			$user->save();

			$fullImagePath = "users/".Auth::id()."/avatars/".$profileImage;
			return response($fullImagePath, 200);
		}

		return response('Something went wrong.', 400);
	}


	public function getNotifications(Request $request)
	{
		$user = Auth::user();

		if($request->ajax()){
			$user->load(['notifications' => function($query){
				$query->where('seen', 0)->latest();
			},'notifications.linkUser', 'notifications.linkEvent', 'notifications.linkPage'])->take(5)->get();

			$count = $user->notifications->count();
			$notifications = $user->notifications;
			$notificationsHTML = '';
			$notificationsHTML .= View::make('user.notification_popup', compact('notifications'));
			return response(['total' => $count, 'notifications' => $notificationsHTML], 200);
		}

		$user->load(['notifications' => function($query){
			$query->latest();
		},'notifications.linkUser', 'notifications.linkEvent', 'notifications.linkPage'])->take(10)->get();

		$user->all_notifications = $user->notifications; // to prevent overwrite from header notification popup query

		return view('user.notifications', compact('user'));
	}

	public function clearNotifications()
	{
		$user = Auth::user();
		if($user->notifications()->delete()){
			return response('Cleared', 200);
		}
		return response('Error', 400);
	}

	public function notificationsSeen()
	{
		$user = Auth::user();
		$query = DB::table('notifications')->where('user_id', $user->id)->where('seen', 0)->update(['seen' => 1]);
		return response('Success', 200);
	}

	public function getLatestNotificationsForPopup(){
		$user = Auth::user();
		$user->load(['notifications' => function($query){
			$query->where('seen', 0)->latest();
		},'notifications.linkUser', 'notifications.linkEvent', 'notifications.linkPage'])->take(5)->get();
		return $user->notifications;
	}
	

	public function postPageNotifications(User $user, Page $page, Request $request)
	{
		if($user->followingPages->contains($page) && $user->id == Auth::id())
		{
			if($request->has('switch') && in_array($request->switch, [0, 1]))
			{
				$switch = ($request->switch === 'true') ? 1 : 0;
				$notification = $user->pageSettings()->firstOrNew([
					'user_id' => $user->id,
					'page_id' => $page->id
					]);

				$notification->receive_page_notifications = $switch;
				$notification->save();

				return response('success', 200);
			}
		}
		return response('error', 400);
	}



	// public function postBlockPage(User $user, Page $page, Request $request)
	// {
	// 	if($user->isFollowerOfThePage($page) && $user->id == Auth::id())
	// 	{
	// 		if($request->has('switch') && in_array($request->switch, [0, 1]))
	// 		{
	// 			$switch = ($request->switch === 'true') ? 1 : 0;
	// 			$block = $user->pageSettings()->firstOrNew([
	// 				'user_id' => $user->id,
	// 				'page_id' => $page->id
	// 				]);

	// 			$block->is_page_blocked = $switch;
	// 			if($block->save()){
	// 				return response(['is_blocked' => $block->is_page_blocked], 200);
	// 			}
	// 		}
	// 	}
	// 	return response('error', 400);
	// }


	public function getFavorites(User $user, Request $request){
		if(!$request->ajax()){
			return redirect()->route('user.profile', $user);
		}

		$this->validate($request, ['after' => 'required|exists:history_event_photos,id']);

		$userPrivateEventAccess = [];
		if(Auth::check()){
			$userPrivateEventAccess = $this->privateEventsWithAccess(Auth::user());
		}

		$favorites      = $user->likedHistoryEventPhotos($userPrivateEventAccess)->where('likes.likeable_id', '<', $request->after)->orderBy('likes.likeable_id', 'DESC')->take(config('common.user_favorites_per_load'))->get();

		$favoriteIDS    = $favorites->lists('likeable_id')->toArray();
		$favoritesLikes = Like::whereIn('likeable_id', $favoriteIDS)->select("id", "likeable_id")->get()->groupBy('likeable_id')->toArray();
		
		if($favorites){
			$html = '';

			foreach($favorites as $favorite)
			{
				$html .= View::make('user.favorite_template', compact('favorite', 'favoritesLikes'));
			}
			if($html){
				return response(['html' => $html, 'leftOff' => $favorites->last()->id], 200);
			}
		}
	}


	public function postFavoriteLike($userID, $favoriteID, Request $request){
		$photo = HistoryEventPhoto::where('id', $favoriteID)->first();
		if($photo){
			$like = $photo->likes()->where('user_id', Auth::id())->withTrashed()->first();

			if($like && $request->unlike == 1 && !$like->trashed()){
				$like->delete();
				return response(['status' => 'unliked', 'totalLikes'=> $photo->numberOfLikes], 200);
			}
			if($like && $like->trashed()){
				$like->restore();
				return response(['status' => 'liked', 'totalLikes'=> $photo->numberOfLikes], 200);
			}
			if(!$like){
				$newLike = new Like(['user_id' => Auth::id(), 'likeable_id' => $photo->id]);
				$photo->likes()->save($newLike);
				return response(['status' => 'liked', 'totalLikes'=> $photo->numberOfLikes], 200);
			}
		}
		return response('error', 404);
	}


	public function getFollowing(User $user, Request $request){

		$followingTemplate = '';

		if($request->has('type') && $request->type == 'addable'){
			$templateName = 'layouts.select_admin_add_user_popup';
			$following    = $user->following()->paginate(config('common.general_follow_per_load'));
		}
		else{
			$this->validate($request, ['after' => 'required|numeric']);
			$templateName = 'user.followable_user_popup_template';
			$following    = $user->following()->where('id', '<', $request->after)->orderBy('id', 'DESC')->take(config('common.general_follow_per_load'))->get();
		}

		if($following){
			foreach($following as $followable)
			{
				$followingTemplate .= View::make($templateName, compact('followable'));
			};
		}

		if($followingTemplate){
			if($templateName == 'layouts.select_admin_add_user_popup'){
				return response($followingTemplate, 200);
			}
			else{
				return response(['html' => $followingTemplate, 'leftOff' => $following->last()->id], 200);
			}
		}
		return response(['status' => 'error'], 404);
	}


	public function getFollowers(User $user, Request $request){
		$followersTemplate = '';


		if($request->has('type') && $request->type == 'addable'){
			$templateName = 'layouts.select_admin_add_user_popup';
			$followers = $user->followers()->paginate(config('common.general_follow_per_load'));
		}
		else{
			$this->validate($request, ['after' => 'required|exists:followables,follower_id,followable_id,'.$user->id]);
			$templateName = 'user.followable_user_popup_template';
			$followers = $user->followers()->where('id', '<', $request->after)->orderBy('id', 'DESC')->take(config('common.general_follow_per_load'))->get();
		}

		if($followers){
			foreach($followers as $followable)
			{
				$followersTemplate .= View::make($templateName, compact('followable'));
			};

		}

		if($followersTemplate){
			if($templateName == 'layouts.select_admin_add_user_popup'){
				return response($followersTemplate, 200);
			}
			else{
				return response(['html' => $followersTemplate, 'leftOff' => $followers->last()->id], 200);				
			}
		}
		return response(['status' => 'error'], 404);
	}


	public function getFollowingPages(User $user, Request $request){
		$pagesTemplate = '';
		$this->validate($request, ['after' => 'required|exists:pages,id']);

		$pages = $user->followingPages()->where('id', '<', $request->after)->orderBy('id', 'DESC')->take(config('common.pages_per_load'))->get();

		if($pages){
			foreach($pages as $page)
			{
				$pagesTemplate .= View::make('pages.pages_list_template', compact('page'));
			}
		}
		if($pagesTemplate){
			return response(['html' => $pagesTemplate, 'leftOff' => $pages->last()->id], 200);
		}
		return response(['status' => 'error'], 404);
	}

	public function privateEventsWithAccess($user){
		$creator = $user->events()->select('id')->where('is_private', 1)->get()->keyBy('id')->keys()->toArray();
		$admin   = $user->adminOfEvents()->select('event_id')->where('is_private', 1)->get()->keyBy('event_id')->keys()->toArray();
		$invited = $user->wasInvitedToEvents()->select(['inviteable_id', 'user_id'])->get()->keyBy('inviteable_id')->keys()->toArray();
		$keys = array_merge($creator, $admin, $invited);
		return $keys;
	}

}
