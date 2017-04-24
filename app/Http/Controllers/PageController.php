<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Attribute;
use App\Country;
use App\Region;
use App\City;
use App\User;
use App\Page;
use Auth;
use DB;
use File;
use View;
use Carbon\Carbon;
use App\InviteList;
use App\Notification;
use App\Invitation;

class PageController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth', ['except' => 'show']);
		$this->tempUploadsDirectory = base_path().'/public/uploads/temp/';
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request)
	{
		// Clear images in session if request is not coming from pre-submitted form
		if($request->session()->get('errors') == NULL){
			if($request->session()->has('create_page_main_image')){
				$request->session()->forget('create_page_main_image');
			}
			if($request->session()->has('create_page_bg_image')){
				$request->session()->forget('create_page_bg_image');
			}
		};

		$oldKeyPeople = $oldAdmins = NULL;


		$attributes = Attribute::where('type', 'LIKE', 'page.%')->where('parent_id', NULL)
		->with('children')->get()->groupBy('type');

		$countries = Country::all();
		$regions   = $cities = [];

		$user            = Auth::user();
		$user->following = $user->following()->paginate(config('common.general_follow_per_load'));
		$user->followers = $user->followers()->paginate(config('common.general_follow_per_load'));

		if($request->old('country') && is_numeric($request->old('country'))){
			$regions = Region::select('id', 'name')->where('country_id', $request->old('country'))->orderBy('name', 'ASC')->get();
		}
		if($request->old('region') && is_numeric($request->old('region'))){
			$cities = City::select('id', 'name')->where('region_id', $request->old('region'))->orderBy('name', 'ASC')->get();
		}

		if($request->old('key_people') && is_array($request->old('key_people'))){
			$oldKeyPeople = User::whereIn('id', $request->old('key_people'))->select('id','name')->get();
		}
		if($request->old('admins') && is_array($request->old('admins'))){
			$oldAdmins = User::whereIn('id', $request->old('admins'))->where('id', '<>', Auth::id())->select('id','name', 'username', 'avatar')->get();
		}

		return view('pages.create', compact('attributes', 'countries', 'regions', 'cities', 'oldKeyPeople', 'oldAdmins', 'user'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Requests\StorePageRequest $request)
	{
		$now = Carbon::now('utc')->toDateTimeString(); // later used for timestamps in bulk insert

		session()->regenerateToken();
		$keyPeople = NULL;
		$pageAdmins = NULL;
		$mainImage = NULL;
		$bgImage = NULL;

		$page = new Page;
		$page->user_id          = Auth::id();
		$page->name             = $request->name;
		$page->slug             = strtolower($request->slug);
		$page->address          = $request->address;
		$page->zip_code         = $request->zip_code;
		$page->status           = $request->status;
		$page->story            = $request->story;
		$page->country_id       = $request->country;
		$page->region_id        = $request->region;
		$page->city_id          = $request->city;

		$attributes = array_filter($request->only(['type', 'year_founded', 'activity_period', 'season']));

		if($request->has('key_people') && is_array($request->input('key_people'))){
			$keyPeople = array_filter($request->input('key_people'));
		}
		if($request->has('admins') && is_array($request->input('admins'))){
			$pageAdmins = array_filter($request->input('admins'));
		}

		DB::transaction(function() use ($request, $mainImage, $bgImage, $page, $attributes, $keyPeople, $pageAdmins, $now){
			$page->save();
			$page->attributes()->sync(array_values($attributes));
			if($keyPeople){
				$page->keyPeople()->sync(array_values($keyPeople));
			}
			if($pageAdmins){
				$page->admins()->sync(array_values($pageAdmins));
				$notifs = [];

				foreach($pageAdmins as $a){
					$notifs[] = [
					'user_id'           => $a,
					'notification_type' => 'page_admin',
					'link_user_id'      => Auth::id(),
					'link_page_id'      => $page->id,
					'created_at'        => $now,
					'updated_at'        => $now
					];
				}

				if($notifs){
					\App\Notification::insert($notifs);
				}
			}

			/* Check that pages directory exists, if not create */
			if(!File::exists(base_path().'/public/uploads/pages')){
				File::makeDirectory(base_path().'/public/uploads/pages');
			}
			
			$pagePath = base_path().'/public/uploads/pages/'.$page->id;
			$imagesPath = $pagePath.'/images';

			File::makeDirectory($pagePath);
			File::makeDirectory($imagesPath);

			if($request->session()->has('create_page_main_image'))
			{
				$mainImage = $request->session()->get('create_page_main_image');
				/* first array key is the image name */
				$mainImage = $mainImage[0];
				File::move($this->tempUploadsDirectory.$mainImage, $imagesPath.'/'.$mainImage);
				$request->session()->forget('create_page_main_image');
			}

			if($request->session()->has('create_page_bg_image'))
			{
				$bgImage = $request->session()->get('create_page_bg_image');
				/* first array key is the image name */
				$bgImage = $bgImage[0];
				File::move($this->tempUploadsDirectory.$bgImage, $imagesPath.'/'.$bgImage);
				$request->session()->forget('create_page_bg_image');
			}

			$page->main_image = $mainImage;
			$page->background_image = $bgImage;
			$page->save();
		});

return redirect()->route('pages.preview', $page->slug);
}


	/**
	 * Display the specified resource.
	 *
	 * @param  Page $page
	 * @return \Illuminate\Http\Response
	 */
	public function show(Page $page)
	{
		$userPrivateEventAccess = [];
		$accessibleEvents = 'public'; // for guests
		if(Auth::check()){
			$user   = Auth::user();
			$userc = new \App\Http\Controllers\UserController;
			$userPrivateEventAccess = $userc->privateEventsWithAccess($user);
			$accessibleEvents = 'accessibleToUser';
		}

		$typeInfo = $page->getPageType()->first()->type_info;
		$upcomingEvents = ($typeInfo == 'type_venue') ? 'venueUpcomingEvents' : 'creatorUpcomingEvents';
		$historyEvents = ($typeInfo == 'type_venue') ? 'venueHistoryEvents' : 'creatorHistoryEvents';

		$page->$upcomingEvents = $page->$upcomingEvents()->with('country', 'region', 'city')->$accessibleEvents($userPrivateEventAccess)->paginate(config('common.events_per_load'));
		$page->$historyEvents = $page->$historyEvents()->with('country', 'region', 'city')->$accessibleEvents($userPrivateEventAccess)->paginate(config('common.events_per_load'));
		$page->followers = $page->followers()->orderBy('id', 'DESC')->paginate(config('common.general_follow_per_load'));

		$userPageSettings = $isUserFollowerOfThisPage = null;
		if(Auth::check()){
			$userPageSettings = Auth::user()->singlePageSettings($page->id);
			$isUserFollowerOfThisPage = Auth::user()->isFollowerOfThePage($page);
		}
		return view('pages.show', compact('page', 'upcomingEvents', 'historyEvents', 'userPageSettings', 'isUserFollowerOfThisPage', 'typeInfo'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($slug, Request $request)
	{
		$page = Page::with('attributes', 'keyPeople', 'admins')->withoutGlobalScope('published')->where('slug', $slug)->firstOrFail();
		$this->authorize('edit', $page);
		$page->attributes = $page->attributes->groupBy('type');

		$attributes = Attribute::where('type', 'LIKE', 'page.%')->where('parent_id', NULL)
		->with('children')->get()->groupBy('type');

		$countries = Country::all();
		$regions   = Region::where('country_id', $page->country_id)->get();
		$cities    = City::where('region_id', $page->region_id)->get();

		$user            = Auth::user();
		$user->following = $user->following()->paginate(config('common.general_follow_per_load'));
		$user->followers = $user->followers()->paginate(config('common.general_follow_per_load'));
		
		$oldKeyPeople    = $page->key_people; 
		$oldAdmins       = $page->admins;


		if($request->old('key_people') && is_array($request->old('key_people')))
		{
			$oldKeyPeople = User::whereIn('id', $request->old('key_people'))->select('id','name')->get();
		}
		if($request->old('admins') && is_array($request->old('admins')))
		{
			$oldAdmins = User::whereIn('id', $request->old('admins'))->select('id','name')->get();
		}

		// Clear images in session if request is not coming from pre-submitted form
		if($request->session()->get('errors') == NULL){
			if($request->session()->has('edit_page_main_image')){
				$request->session()->forget('edit_page_main_image');
			}
			if($request->session()->has('edit_page_bg_image')){
				$request->session()->forget('edit_page_bg_image');
			}
		};

		if($page->main_image){
			$mainImageSize = File::size(base_path().'/public/uploads/pages/'.$page->id.'/images/'.$page->main_image);
			$request->session()->put('edit_page_main_image', [$page->main_image, $mainImageSize, "is_temp" => 0]);
		}
		if($page->background_image){
			$bgImageSize = File::size(base_path().'/public/uploads/pages/'.$page->id.'/images/'.$page->background_image);
			$request->session()->put('edit_page_bg_image', [$page->background_image, $bgImageSize, "is_temp" => 0]);
		}

		return view('pages.edit', compact('page', 'attributes', 'countries', 'regions', 'cities', 'oldKeyPeople', 'oldAdmins', 'user'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Requests\UpdatePageRequest $request)
	{
		$now = Carbon::now('utc')->toDateTimeString(); // later used for timestamps in bulk insert
		$page = Page::withoutGlobalScope('published')->findOrFail($request->input('id'));

		$this->authorize('edit', $page);
		
		session()->regenerateToken();
		$keyPeople = [];
		$pageAdmins = [];
		$mainImage = NULL;
		$bgImage = NULL;

		$page->name             = $request->name;
		$page->slug             = strtolower($request->slug);
		$page->address          = $request->address;
		$page->zip_code         = $request->zip_code;
		$page->status           = $request->status;
		$page->story            = $request->story;
		$page->country_id       = $request->country;
		$page->region_id        = $request->region;
		$page->city_id          = $request->city;

		$attributes = array_filter($request->only(['type', 'year_founded', 'activity_period', 'season']));

		if($request->has('key_people') && is_array($request->input('key_people'))){
			$keyPeople = array_filter($request->input('key_people'));
		}
		if($request->has('admins') && is_array($request->input('admins'))){
			$pageAdmins = array_filter($request->input('admins'));
		}

		DB::transaction(function() use ($page, $attributes, $keyPeople, $pageAdmins, $mainImage, $bgImage, $request, $now)
		{
			$page->save();
			$page->attributes()->sync(array_values($attributes));
			$page->keyPeople()->sync(array_values($keyPeople));
			$page->admins()->sync(array_values($pageAdmins));
			$notifs = [];
			
			foreach($pageAdmins as $a){
				$notifs[] = [
				'user_id'           => $a,
				'notification_type' => 'page_admin',
				'link_user_id'      => Auth::id(),
				'link_page_id'      => $page->id,
				'created_at'        => $now,
				'updated_at'        => $now
				];
			}

			if($notifs){
				\App\Notification::insert($notifs);
			}

			$pagePath = base_path().'/public/uploads/pages/'.$page->id;
			$imagesPath = $pagePath.'/images';


			if($request->session()->has('edit_page_main_image'))
			{
				$mainImage = $request->session()->get('edit_page_main_image');
				$mainImage = $mainImage[0]; // first array key is the image name

				if($mainImage != $page->main_image && File::exists($this->tempUploadsDirectory.$mainImage)){
					File::move($this->tempUploadsDirectory.$mainImage, $imagesPath.'/'.$mainImage);
				}
				$request->session()->forget('edit_page_main_image');
			}

			if($request->session()->has('edit_page_bg_image'))
			{
				$bgImage = $request->session()->get('edit_page_bg_image');
				$bgImage = $bgImage[0]; // first array key is the image name

				if($bgImage != $page->background_image && File::exists($this->tempUploadsDirectory.$bgImage)){
					File::move($this->tempUploadsDirectory.$bgImage, $imagesPath.'/'.$bgImage);
				}
				$request->session()->forget('edit_page_bg_image');
			}

			$page->main_image = $mainImage;
			$page->background_image = $bgImage;
			$page->save();
		});

if($page->published){
	return redirect()->route('pages.show', $page);
}
return redirect()->route('pages.preview', $page->slug);
}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($slug)
	{
		$page = Page::where('slug', '=', $slug)->withoutGlobalScope('published')->first();
		
		if(!$page){
			return response('Error', 400);
		}
		if(Auth::user()->cannot('destroy', $page)){
			return response('Unathorized', 403);
		}
		if($page->delete()){
			return response('Deleted', 200);
		}
		return response('Error', 400);
	}



	public function deleteAdmin(Page $page, $id)
	{
		$user = User::find($id);

		if($user && $user->isAdminOfThePage($page)){
			if($user->adminOfPages()->detach($page->id)){
				return response('Removed', 200);
			}
		}
		return response('Error', 400);
	}



	/**
	* Show preview of page before publishing it
	*
	* @param int $id
	* @return \Illuminate\Http\Response
	*/
	public function preview($slug){
		$page = Page::with('attributes', 'keyPeople')->withoutGlobalScope('published')->where('published', 0)->where('slug', $slug)->firstOrFail();
		$this->authorize('preview', $page);

		return view('pages.preview', compact('page'));
	}



	public function publish(Request $request){
		$page = Page::withoutGlobalScope('published')->where('published', 0)->findOrFail($request->input('id'));
		$this->authorize('preview', $page);
		$page->published = TRUE;
		$page->save();
		return redirect()->route('pages.show', $page->slug);
	}



	public function follow(Page $page){
		$page->followers()->sync([Auth::id()], false);

		$user = Auth::user();
		$avatar = ($user->avatarFullPath) ? 'images/small59/'.$user->avatarFullPath : config('common.userDefaultAvatarPath');

		$avatarHTML = '<div class="column" data-id="'. $user->id .'"><img data-tooltip aria-haspopup="true" class="has-tip" title="'. $user->name. '" src="'. url($avatar) .'" alt=""></div>';

		return response(['buttonText' => 'Following', 'id' => Auth::id(), 'avatar' => $avatarHTML], 200);
	}



	public function unfollow(Page $page){
		$page->followers()->detach(Auth::id());
		return response(['buttonText' => 'Follow', 'id' => Auth::id()], 200);
	}


	public function ajaxUploadImage(Request $request){
		$mainImageSessionName = 'create_page_main_image';
		$bgImageSessionName   = 'create_page_bg_image';

		// check for upload_max_file_size error
		if(isset($_FILES['pageImage']) && $_FILES['pageImage']['error'] == 1){
			return response('File size exceeds the limit', 400);
		}
		// catch post_max_size error
		if(empty($_POST)){
			$post_size = trim(ini_get('post_max_size'));
			$post_size = substr($post_size, 0, -1);
			$post_size = ($post_size * 1024) * 1024;

			if ($post_size < $request->header('Content-Length')) {
				return response('File size exceeds the limit', 400);
			}

		}

		if($request->has('page')){
			$page = Page::where('slug', '=', $request->page)->withoutGlobalScope('published')->first();
			if($page){
				$this->authorize('edit', $page);
				$mainImageSessionName = 'edit_page_main_image';
				$bgImageSessionName   = 'edit_page_bg_image';
			}
		}

		$this->validate($request, ['pageImage' => 'image|max:10240', 'pageImageType' => 'in:main,bg']);

		if($request->hasFile('pageImage') && $request->file('pageImage')->isValid()){
			$ext = $request->file('pageImage')->getClientOriginalExtension();
			$size = $request->file('pageImage')->getSize();

			while(true){
				$pageImage = uniqid(rand(), true) . '_' . date('d_m_Y_H_i_s') . '.' . $ext;
				if(!file_exists($this->tempUploadsDirectory.'/'.$pageImage)) break;
			}
			$request->file('pageImage')->move($this->tempUploadsDirectory, $pageImage);

			if($request->pageImageType == 'main'){
				if($request->session()->has($mainImageSessionName)) {
					return response('You can upload only 1 image.', 400);
				}
				else{
					$tempUploadedImage = [$pageImage, $size, 'is_temp' => 1];
					$request->session()->put($mainImageSessionName, $tempUploadedImage);
				}
				return response($pageImage, 200);
			}
			else{
				if($request->session()->has($bgImageSessionName)) {
					return response('You can upload only 1 image.', 400);
				}
				else{
					$tempUploadedImage = [$pageImage, $size, "is_temp" => 1];
					$request->session()->put($bgImageSessionName, $tempUploadedImage);
				}
				return response($pageImage, 200);
			}
		}
		return response('Something went wrong.', 400);
	}



	public function ajaxDeleteImage(Request $request){
		if($request->has('image'))
		{
			$mainImage        = $bgImage = array();
			$image            = $request->image;
			$imgFolder        = $this->tempUploadsDirectory;
			$isImageSavedInDB = false;
			$mainImageSessionName = 'create_page_main_image';
			$bgImageSessionName   = 'create_page_bg_image';

			if($request->has('page')){
				$page = Page::where('slug', '=', $request->page)->withoutGlobalScope('published')->first();
				if($page){
					$this->authorize('edit', $page);
					$mainImageSessionName = 'edit_page_main_image';
					$bgImageSessionName   = 'edit_page_bg_image';
					if($page->main_image == $image || $page->background_image == $image){
						$imgFolder = base_path().'/public/uploads/pages/'.$page->id.'/images/';
						$isImageSavedInDB = true;
					}
				}
			}


			if($request->session()->has($mainImageSessionName)){
				$mainImage = $request->session()->get($mainImageSessionName);
				if($mainImage[0] == $image){
					File::delete($imgFolder.$image);
					$request->session()->forget($mainImageSessionName);

					if($isImageSavedInDB){
						$page->main_image = NULL;
						$page->save();
					}
					return response('success', 200);
				}
			}

			if($request->session()->has($bgImageSessionName)){
				$bgImage = $request->session()->get($bgImageSessionName);
				if($bgImage[0] == $image){
					File::delete($imgFolder.$image);
					$request->session()->forget($bgImageSessionName);

					if($isImageSavedInDB){
						$page->background_image = NULL;
						$page->save();
					}
					return response('success', 200);
				}
			}
			return response('Invalid image', 400);
		}
		else{
			return response('Something went wrong.', 400);
		}
	}


	public function getFollowers(Page $page, Request $request){
		$this->validate($request, ['after' => 'required|exists:users,id']);

		$followersTemplate = '';
		$followers = $page->followers()->where('id', '<', $request->after)->orderBy('id', 'DESC')->take(config('common.general_follow_per_load'))->get();

		if($followers){
			foreach($followers as $follower)
			{
				$followersTemplate .= View::make('pages.page_follower_popup_template', compact('follower'));
			};
		}

		if($followersTemplate){
			return response(['html' => $followersTemplate, 'leftOff' => $followers->last()->id], 200);
		}
		return response(['status' => 'error'], 404);
	}


	public function getUpcomingEvents(Page $page){
		$eventMode = 'upcoming';
		$eventsTemplate = '';

		$typeInfo = $page->getPageType()->first()->type_info;
		$upcomingEvents = ($typeInfo == 'type_venue') ? 'venueUpcomingEvents' : 'creatorUpcomingEvents';

		$userPrivateEventAccess = [];
		$accessibleEvents = 'public'; // for guests
		if(Auth::check()){
			$user   = Auth::user();
			$userc = new \App\Http\Controllers\UserController;
			$userPrivateEventAccess = $userc->privateEventsWithAccess($user);
			$accessibleEvents = 'accessibleToUser';
		}


		$events = $page->$upcomingEvents()->$accessibleEvents($userPrivateEventAccess)->paginate(config('common.events_per_load'));

		foreach($events as $event)
		{
			$eventsTemplate .= View::make('events.event_row_template', compact('event', 'eventMode'));
		}

		if($eventsTemplate){
			return response($eventsTemplate, 200);
		}
		return response("error", 400);
	}


	public function getHistoryEvents(Page $page){
		$eventMode = 'history';
		$eventsTemplate = '';

		$typeInfo = $page->getPageType()->first()->type_info;
		$historyEvents = ($typeInfo == 'type_venue') ? 'venueHistoryEvents' : 'creatorHistoryEvents';

		$userPrivateEventAccess = [];
		$accessibleEvents = 'public'; // for guests
		if(Auth::check()){
			$user   = Auth::user();
			$userc = new \App\Http\Controllers\UserController;
			$userPrivateEventAccess = $userc->privateEventsWithAccess($user);
			$accessibleEvents = 'accessibleToUser';
		}

		$events = $page->$historyEvents()->$accessibleEvents($userPrivateEventAccess)->paginate(config('common.events_per_load'));

		foreach($events as $event)
		{
			$eventsTemplate .= View::make('events.event_row_template', compact('event', 'eventMode'));
		}

		if($eventsTemplate){
			return response($eventsTemplate, 200);
		}
		return response("error", 400);
	}


	public function inviteAList(Page $page, Request $request){
		$this->authorize('invite', $page);
		$this->validate($request, ['list_id' => 'required|exists:invite_lists,id,user_id,'.Auth::id()]);

		if($page->isListInvited($request->list_id)){
			return response('Error', 400);
		}

		$list = InviteList::find($request->list_id);
		$list->members->load(['wasInvitedToPages' => function($query) use($page){
			$query->where('inviteable_id', $page->id);
		}]);
		$notifs = $invitations = NULL;
		$now = Carbon::now('utc')->toDateTimeString(); // later used for timestamps in bulk insert

		/* Get users to exclude from search, the creator of the page and admins */
		$excludedUsers = $page->admins()->select('id')->get();
		$excludedUsers = $excludedUsers->keyBy('id')->keys()->toArray();
		$excludedUsers[] = $page->user_id;

		foreach($list->members as $m){
			if($m->wasInvitedToPages->count() == 0){
				if(!in_array($m->id, $excludedUsers)){
					$notifs[] = [
					'user_id'           => $m->id,
					'notification_type' => 'page_invite',
					'link_user_id'      => Auth::id(),
					'link_page_id'      => $page->id,
					'created_at'        => $now,
					'updated_at'        => $now
					];

					$invitations[] = [
					'user_id' => $m->id,
					'inviteable_id' => $page->id,
					'inviteable_type' => 'App\Page',
					'inviter_id' => Auth::id(),
					'created_at'        => $now,
					'updated_at'        => $now
					];
				}
			}
		}
		if($notifs && $invitations){
			Invitation::insert($invitations);
			Notification::insert($notifs);
		}
		if($list){
			$page->inviteLists()->save($list);
			return response('Invited',200);
		}

		return response('Error', 400);
	}


	public function inviteAUser(Page $page, Request $request){
		$this->authorize('invite', $page);

		/* Get users to exclude from search, the creator of the page and admins */
		$excludedUsers = $page->admins()->select('id')->get();
		$excludedUsers = $excludedUsers->keyBy('id')->keys()->toArray();
		$excludedUsers[] = $page->user_id;

		$this->validate($request, ['user_id' => 'required|exists:users,id|not_in:'.implode(',', $excludedUsers)]);

		if($page->isUserInvited($request->user_id)){
			return response('Error', 400);
		}

		$user = User::find($request->user_id);

		$notifs = $invitations = NULL;
		$now = Carbon::now('utc')->toDateTimeString(); // later used for timestamps in bulk insert



		$notif = [
		'user_id'           => $user->id,
		'notification_type' => 'page_invite',
		'link_user_id'      => Auth::id(),
		'link_page_id'      => $page->id,
		'created_at'        => $now,
		'updated_at'        => $now
		];
		
		$invitation = [
		'user_id'         => $user->id,
		'inviteable_id'   => $page->id,
		'inviteable_type' => 'App\Page',
		'inviter_id'      => Auth::id(),
		'created_at'      => $now,
		'updated_at'      => $now
		];

		if($notif && $invitation){
			Invitation::firstOrCreate($invitation);
			Notification::firstOrCreate($notif);
			return response('Invited', 200);
		}

		return response('Error', 400);
	}


	public function getInviteListsAjax(Page $page, Request $request)
	{
		$this->authorize('invite', $page);
		$page->load('inviteLists');
		$lists = Auth::user()->inviteLists()->orderBy('id', 'DESC')->paginate(config('common.general_items_per_load'));
		$listsTemplate = '';

		foreach($lists as $list)
		{
			$listsTemplate .= View::make('dashboard.invite_list_page_popup_row', compact('list', 'page'));
		}
		if($listsTemplate){
			return response($listsTemplate, 200);
		}
		return response("error", 400);

	}


	public function searchInviteUsersAjax(Page $page, $search, Request $request)
	{
		$this->authorize('invite', $page);

		$per_load = 5;
		if($request->has('per_request') && is_numeric($request->per_request) && ($request->per_request <= 30)){
			$per_load = $request->per_request;
		}

		/* Get users to exclude from search, the creator of the page and admins */
		$excludedUsers = $page->admins()->select('id')->get();
		$excludedUsers = $excludedUsers->keyBy('id')->keys()->toArray();
		$excludedUsers[] = $page->user_id;


		$users = User::like('name', $search)->select('id', 'name', 'username', 'avatar')->whereNotIn('id', $excludedUsers)->orderBy('name')->take($per_load)->get();
		$users->load(['wasInvitedToPages' => function($query) use($page){
			$query->where('inviteable_id', $page->id);
		}]);

		$usersHTML = '';

		foreach($users as $user){
			$inviteText = 'Invite';
			$inviteClass = '';
			if($user->wasInvitedToPages->count() > 0){
				$inviteText = 'Invited';
				$inviteClass = 'added';
			}
			$usersHTML .= "<div class=\"column user\" data-id=\"$user->id\"><a href=\"/users/$user->username\" target=\"_blank\"><img src=\"/images/small59/$user->avatarFullPath\"></a><div><span class=\"name\">$user->name</span><button class=\"add-button $inviteClass\" type=\"button\" data-id=\"$user->id\" data-name=\"$user->name\" data-username=\"$user->username\">$inviteText</button></div></div>";
		}
		return response($usersHTML, 200);
	}

}
