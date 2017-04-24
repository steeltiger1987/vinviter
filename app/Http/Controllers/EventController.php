<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Attribute;
use App\Country, App\Region, App\City;
use App\Event;
use App\Page;
use App\User;
use Auth;
use DB;
use Carbon\Carbon;
use File;
use App\Comment;
use App\Like;
use Validator;
use View;
use App\InviteList;
use App\Notification;
use App\Invitation;

class EventController extends Controller
{
	protected $uploadsTempDirectory;

	public function __construct(){
		$this->middleware('auth', ['except' => ['show','index','buildEventsURL']]);
		$this->uploadsTempDirectory = base_path().'/public/uploads/temp/';
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{

		// Get event mode
		$currentRoute = $request->route()->getName();
		$eventMode    = 'upcoming';
		if($currentRoute == 'history'){
			$eventMode = 'history';
		}


		// Get countries, regions, cities
		$countries = Country::orderBy('name', 'ASC')->get();
		$regions   = $cities = [];
		if($request->has('country') && is_numeric($request->get('country'))){
			$regions = Region::select('id', 'name')->where('country_id', $request->get('country'))->orderBy('name', 'ASC')->get();
		}
		if($request->has('region') && is_numeric($request->get('region'))){
			$cities = City::select('id', 'name')->where('region_id', $request->get('region'))->orderBy('name', 'ASC')->get();
		}

		/* Validate event query parameters */
		$validator = Validator::make($request->query(), [
			'country' => 'exists:countries,id',
			'region'  => 'exists:regions,id',
			'city'    => 'exists:cities,id',
			'type'    => 'exists:attributes,id,type,event.type',
			'music'   => 'exists:attributes,id,type,event.music',
			'month'   => 'date_format:m',
			'year'	  => 'date_format:Y'
			]);

		if($validator->fails()){
			if($request->ajax()){
				return response('', 400);
			}
			return redirect()->route($eventMode);
		}

		$userPrivateEventAccess = [];
		$accessibleEvents = 'public'; // for guests
		if(Auth::check()){
			$user   = Auth::user();
			$userc = new \App\Http\Controllers\UserController;
			$userPrivateEventAccess = $userc->privateEventsWithAccess($user);
			$accessibleEvents = 'accessibleToUser';
		}

		// Get attributes and events
		$attributes = Attribute::where('type', 'LIKE', 'event.%')->where('parent_id', NULL)->with('children')->get()->groupBy('type');
		$events = Event::$eventMode()->filterByRequest($request)->with('attendees', 'country', 'region', 'city')->$accessibleEvents($userPrivateEventAccess)->orderBy('id', 'DESC')->paginate(config('common.events_per_load'));

		// Return view response if the request is ajax
		if ($request->ajax())
		{
			$eventsTemplate = '';
			foreach($events as $event)
			{
				$eventsTemplate .= View::make('events.event_row_template', compact('event', 'eventMode'));
			}
			if($eventsTemplate){
				return response($eventsTemplate, 200);
			}
			return response("error", 400);
		}

		// Get month and year range for search dropdown
		$earliestEvent = Event::$eventMode()->filterByRequest($request, false)->$accessibleEvents($userPrivateEventAccess)->select('starts_at')->orderBy('starts_at', 'ASC')->first();
		$latestEvent = Event::$eventMode()->filterByRequest($request, false)->$accessibleEvents($userPrivateEventAccess)->select('starts_at')->orderBy('starts_at', 'DESC')->first();

		$yearsRange = $months = [];
		if($earliestEvent && $latestEvent){
			$yearsRange = range($earliestEvent->starts_at->year, $latestEvent->starts_at->year);
			$months = $this->get12Months();
		}

		// Return view
		return view('events.index', compact('events', 'countries', 'regions', 'cities', 'attributes', 'eventMode', 'yearsRange', 'months', 'userPrivateEventAccess'));
	}

	
	public function buildEventsURL(Request $request){
		$currentRoute = $request->route()->getName();
		$eventMode = 'upcoming';
		if($currentRoute == 'historyFilter')
		{
			$eventMode = 'history';
		}


		if($request->method() == "POST" && $request->has('events_filter'))
		{
			$url = '';
			if($request->has('country')){
				$url .= 'country='.$request->country;
			}
			if($request->has('region')){
				$url .= '&region='.$request->region;
			}
			if($request->has('city')){
				$url .= '&city='.$request->city;
			}
			if($request->has('type')){
				$url .= '&type='.$request->type;
			}
			if($request->has('music')){
				$url .= '&music='.$request->music;
			}

			if($request->has('month')){
				$url .= '&month='.$request->month;
			}
			if($request->has('year')){
				$url .= '&year='.$request->year;
			}

			if($url){
				return redirect()->route($eventMode, $url);
			}
			return redirect()->route($eventMode);


		}
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
			if($request->session()->has('create_event_images')){
				$request->session()->forget('create_event_images');
			}
		};

		$timezones = DB::table('timezones')->get();
		$oldVenuePage = $oldCreatorPage = $oldAdmins = NULL;
		$regions = $cities = [];
		$countries = Country::orderBy('name', 'ASC')->get();

		if($request->old('country') && is_numeric($request->old('country'))){
			$regions = Region::select('id', 'name')->where('country_id', $request->old('country'))->orderBy('name', 'ASC')->get();
		}

		if($request->old('region') && is_numeric($request->old('region'))){
			$cities = City::select('id', 'name')->where('region_id', $request->old('region'))->orderBy('name', 'ASC')->get();
		}


		$attributes = Attribute::where('type', 'LIKE', 'event.%')->where('parent_id', NULL)->with('children')->get()->groupBy('type');

		if($request->old('venue_page') && is_numeric($request->old('venue_page'))){
			$oldVenuePage = Page::where('id', $request->old('venue_page'))->wherePageType('venue')->select('id','name')->first();
		}

		if($request->old('creator_page') && is_numeric($request->old('creator_page'))){
			$oldCreatorPage = Page::where('id', $request->old('creator_page'))->wherePageType('organization')->select('id','name')->first();
		}

		if($request->old('admins') && is_array($request->old('admins'))){
			$oldAdmins = User::whereIn('id', $request->old('admins'))->where('id', '<>', Auth::id())->select('id','name',  'username', 'avatar')->get();
		}

		$user            = Auth::user();
		$user->following = $user->following()->paginate(config('common.general_follow_per_load'));
		$user->followers = $user->followers()->paginate(config('common.general_follow_per_load'));

		return view('events.create', compact('countries','regions', 'cities', 'attributes', 'timezones', 'oldVenuePage', 'oldCreatorPage', 'oldAdmins', 'user'));
	}

	public function ajaxUploadImage(Request $request){
		$tempDirectory = $this->uploadsTempDirectory;
		$imagesSessionName = 'create_event_images';

		// check for upload_max_file_size error
		if(isset($_FILES['eventImage']) && $_FILES['eventImage']['error'] == 1){
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

		if($request->has('event')){
			$event = Event::where('id', '=', $request->event)->withoutGlobalScope('published')->first();
			if($event){
				$this->authorize('historyPhoto', $event);
				$imagesSessionName = 'edit_event_images';
			}
		}

		$this->validate($request, ['eventImage' => 'image|max:10240']);

		if($request->hasFile('eventImage') && $request->file('eventImage')->isValid())
		{
			$ext = $request->file('eventImage')->getClientOriginalExtension();
			$size = $request->file('eventImage')->getSize();

			while(true){
				$eventImage = uniqid(rand(), true) . '_' . date('d_m_Y_H_i_s') . '.' . $ext;
				if(!file_exists($tempDirectory.'/'.$eventImage)) break;
			}
			$request->file('eventImage')->move($tempDirectory, $eventImage);

			if($request->session()->has($imagesSessionName)) {
				$sessionImages = $request->session()->get($imagesSessionName);
				if(count($sessionImages) >= 4){
					return response('You can upload up to 4 images.', 400);
				}
				$request->session()->push($imagesSessionName, [$eventImage, $size, "is_temp" => 1]);
			}
			else{
				$tempUploadedImages = array();
				$tempUploadedImages[] = [$eventImage, $size, "is_temp" => 1];
				$request->session()->put($imagesSessionName, $tempUploadedImages);
			}
			return response($eventImage, 200);
		}
		return response('Something went wrong.', 400);
	}

	public function ajaxDeleteImage(Request $request){
		$imagesSessionName  = 'create_event_images';
		if($request->has('event')){
			$event = Event::where('id', '=', $request->event)->withoutGlobalScope('published')->first();
			if($event){
				$this->authorize('preview', $event);
				$imagesSessionName   = 'edit_event_images';
			}
		}

		if($request->session()->has($imagesSessionName) && $request->has('image'))
		{
			$sessionImages      = $request->session()->get($imagesSessionName);
			$image              = $request->get('image');
			$imgFolder          = $this->uploadsTempDirectory;
			$imageFoundKey      = NULL;
			$imageModelToDelete = false;
			$imageModelKey      = false;

			if($request->has('event')){
				$event = Event::where('id', '=', $request->event)->withoutGlobalScope('published')->first();
				if($event){
					$this->authorize('preview', $event);
					foreach($event->images as $key => $value){
						if($value->name == $image){
							$imgFolder          = base_path().'/public/uploads/events/'.$event->id.'/images/';
							$imageModelToDelete = $value;
							$imageModelKey      = $key;
							break;
						}
					}					
				}
			}


			foreach($sessionImages as $key => $row){
				if($row[0] == $image)
				{
					$imageFoundKey = $key;
					break;
				}
			}

			if(isset($imageFoundKey))
			{
				File::delete($imgFolder.$image);
				unset($sessionImages[$imageFoundKey]);
				$request->session()->put($imagesSessionName, $sessionImages);

				if($imageModelToDelete){
					if($imageModelToDelete->is_main_image == 1){
						$event->images->forget($imageModelKey);
						$nextImage = $event->images->first();
						if($nextImage){
							$nextImage->is_main_image = 1;
							$nextImage->save();
						}
					}
					$imageModelToDelete->delete();
				}

				return response('success', 200);
			}
			return response('Invalid image', 400);
		}
		else{
			return response('Something went wrong.', 400);
		}
	}


	public function ajaxUploadHistoryPhoto(Request $request, $id)
	{
		$event = Event::where('id', $id)->select('id', 'starts_at', 'user_id')->first();
		if(!$event || !$event->isHistory){
			return response('Something went wrong', 400);
		}

		/* Return false if user is not the owner or admin of the event */
		if(!((Auth::id() == $event->user_id) || Auth::user()->isAdminOfTheEvent($event))){
			return response('Something went wrong', 400);
		}

		$tempDirectory = $this->uploadsTempDirectory;
		$sessionName   = 'history_event_photos_'.$event->id;

		// check for upload_max_file_size error
		if(isset($_FILES['historyEventPhoto']) && $_FILES['historyEventPhoto']['error'] == 1){
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

		$this->validate($request, ['historyEventPhoto' => 'image|max:10240']);

		if($request->hasFile('historyEventPhoto') && $request->file('historyEventPhoto')->isValid())
		{
			$ext = $request->file('historyEventPhoto')->getClientOriginalExtension();
			$size = $request->file('historyEventPhoto')->getSize();

			while(true)
			{
				$historyEventPhoto = uniqid(rand(), true) . '_' . date('d_m_Y_H_i_s') . '.' . $ext;
				if(!file_exists($tempDirectory.'/'.$historyEventPhoto)) break;
			}

			$request->file('historyEventPhoto')->move($tempDirectory, $historyEventPhoto);

			if($request->session()->has($sessionName))
			{
				$sessionImages = $request->session()->get($sessionName);
				if(count($sessionImages) >= 12){
					return response('You can upload up to 12 images.', 400);
				}
				$request->session()->push($sessionName, [$historyEventPhoto, $size]);
			}
			else
			{
				$tempUploadedImages = array();
				$tempUploadedImages[] = [$historyEventPhoto, $size];
				$request->session()->put($sessionName, $tempUploadedImages);
			}

			return response($historyEventPhoto, 200);
		}
		return response('Something went wrong.', 400);
	}


	public function ajaxDeleteHistoryPhoto($id, Request $request){
		$event = Event::where('id', $id)->select('id', 'user_id', 'starts_at')->first();
		if(!$event || !$event->isHistory){
			return response('Something went wrong', 400);
		}
		$this->authorize('historyPhoto', $event);

		$sessionName   = 'history_event_photos_'.$event->id;

		if($request->session()->has($sessionName) && $request->has('image')) {

			$sessionImages = $request->session()->get($sessionName);
			$image = $request->get('image');
			$imageFoundKey = NULL;

			foreach($sessionImages as $key => $row){
				if($row[0] == $image){
					$imageFoundKey = $key;
					break;
				}
			}

			if(isset($imageFoundKey)){
				File::delete($this->uploadsTempDirectory.$image);
				unset($sessionImages[$imageFoundKey]);
				$request->session()->put($sessionName, $sessionImages);
				return response('success', 200);
			}
			return response('Invalid image', 400);

		}
		else{
			return response('Something went wrong.', 400);
		}
	}



	public function getHistoryPhotos(Event $event, Request $request){
		if(!$request->ajax()){
			return redirect()->route('upcoming');
		}

		if($event->is_private){
			if(Auth::check()){
				$this->authorize('publicPrivateAccess', $event);
			}
		}

		$photos = $event->historyPhotos()->orderBy('id', 'DESC')->paginate(config('common.history_event_photos_per_load'));
		
		if($photos){
			$html = '';
			
			foreach($photos as $photo)
			{
				$html .= View::make('events.history_photo_template', compact('photo'));
			}
			return $html;
		}
	}



	public function publishHistoryPhotos($id, Request $request){
		$event = Event::where('id', $id)->select('id', 'user_id', 'starts_at')->first();
		if(!$event || !$event->isHistory){
			return response('Something went wrong', 400);
		}
		$this->authorize('historyPhoto', $event);
		
		$sessionName   = 'history_event_photos_'.$event->id;

		if($request->session()->has($sessionName)){
			// Build photo models to insert
			$photos = $request->session()->get($sessionName);
			$photoModels = [];

			foreach($photos as $key => $photo){
				$photoModels[] = new \App\HistoryEventPhoto([
					'name'         => $photo[0],
					'user_id'      => Auth::id()
					]);
			}

			if($photoModels){
				// Save photos
				$savedPhotos = $event->historyPhotos()->saveMany($photoModels);

				// Move photos from temporary location to proper location
				$eventPath = base_path().'/public/uploads/events/'.$event->id;
				$photosPath = $eventPath.'/history_photos';

				if(!File::exists($photosPath)){
					File::makeDirectory($photosPath, $mode = 0755, $recursive = true);
				}

				if(isset($savedPhotos) && count($savedPhotos) > 0){
					foreach($savedPhotos as $photo){
						File::move($this->uploadsTempDirectory.$photo->name, $photosPath.'/'.$photo->name);
					}
					$request->session()->forget($sessionName);
					
					$photos = $event->historyPhotos()->orderBy('id', 'DESC')->paginate(config('common.history_event_photos_per_load'));
					$photosHTML = [];
					if($photos){
						foreach($photos as $photo){
							$photosHTML['photos'][] = "".View::make('events.history_photo_template', compact('photo'));
						}
						$photosHTML['next_page'] = $photos->currentPage() + 1;
						$photosHTML['last_page'] = $photos->lastPage();
					}
					return response($photosHTML, 200);
				}
			}
		}
		return response('Something went wrong', 400);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Requests\StoreEventRequest $request)
	{
		$now = Carbon::now('utc')->toDateTimeString(); // later used for timestamps in bulk insert

		session()->regenerateToken();
		$eventAdmins = NULL;
		$is_location_hidden = false;

		if($request->has('hide_location') && $request->hide_location == 'on'){
			$is_location_hidden = true;
		}

		$starting_time = new Carbon($request->starting_time.':00', $request->timezone);
		$ending_time = new Carbon($request->ending_time.':00', $request->timezone);

		$starting_time_UTC = $starting_time->timezone('UTC');
		$ending_time_UTC = $ending_time->timezone('UTC');


		$event                     = new Event;
		$event->user_id            = Auth::id();
		$event->title              = $request->title;
		$event->address            = $request->address;
		$event->zip_code           = $request->zip_code;
		$event->details            = $request->details;
		$event->venue_page_id      = $request->venue_page;
		$event->creator_page_id    = $request->creator_page;
		$event->country_id         = $request->country;
		$event->region_id          = $request->region;
		$event->city_id            = $request->city;
		$event->timezone           = $request->timezone;
		$event->is_location_hidden = $is_location_hidden;
		$event->is_private         = $request->visibility;
		$event->starts_at          = $starting_time_UTC;
		$event->ends_at            = $ending_time_UTC;

		$attributes = array_filter($request->only(['type', 'entrance', 'dress_code', 'age_limit', 'music', 'document']));
		$images = $request->session()->get('create_event_images');

		if($request->has('admins') && is_array($request->input('admins'))){
			$eventAdmins = array_filter($request->input('admins'));
		}

		DB::transaction(function() use ($event, $attributes, $eventAdmins, $images, $request, $now)
		{
			$event->save();
			$event->attributes()->sync(array_values($attributes));

			if($eventAdmins){
				$event->admins()->sync(array_values($eventAdmins));
				$notifs = [];

				foreach($eventAdmins as $a){
					$notifs[] = [
					'user_id'           => $a,
					'notification_type' => 'event_admin',
					'link_user_id'      => Auth::id(),
					'link_event_id'     => $event->id,
					'created_at'        => $now,
					'updated_at'        => $now
					];
				}

				if($notifs){
					\App\Notification::insert($notifs);
				}
			}


			if(!empty($images))
			{
				$imageModels = [];
				$firstImageKey = key($images);
				foreach($images as $key => $image){
					if($key == $firstImageKey){
						$imageModels[] = new \App\Image(['name' => $image[0], 'is_main_image' => 1]);
					}
					else{
						$imageModels[] = new \App\Image(['name' => $image[0]]);
					}
				}
				$savedImages = $event->images()->saveMany($imageModels);
				$request->session()->forget('create_event_images');
			}

			/* Check that events directory exists, if not create */
			if(!File::exists(base_path().'/public/uploads/events')){
				File::makeDirectory(base_path().'/public/uploads/events');
			}

			$eventPath = base_path().'/public/uploads/events/'.$event->id;
			$imagesPath = $eventPath.'/images';

			/* Delete event directory if it already exists */
			if(File::exists($eventPath)){
				File::deleteDirectory($eventPath);
			}

			File::makeDirectory($eventPath);
			File::makeDirectory($imagesPath);

			if(isset($savedImages) && count($savedImages) > 0){
				foreach($savedImages as $row){
					File::move($this->uploadsTempDirectory.$row->name, $imagesPath.'/'.$row->name);
				}
			}


		}); // Database transaction ends here

return redirect()->route('events.preview', $event->id);
}

	/**
	* Show preview of event before publishing it
	*
	* @param int $id
	* @return \Illuminate\Http\Response
	*/
	public function preview($id){
		$event = Event::with(['attributes',
			'venuePage' => function($query){
				$query->select('id','slug','name','main_image');
			},
			'creatorPage' => function($query){
				$query->select('id','slug','name','main_image');
			}
			])->withoutGlobalScope('published')->where('published', 0)->where('id', $id)->firstOrFail();
		$this->authorize('preview', $event);

		return view('events.preview', compact('event'));
	}

	public function publish(Request $request){
		$event = Event::withoutGlobalScope('published')->where('published', 0)->findOrFail($request->input('id'));
		$this->authorize('preview', $event);
		$event->published = TRUE;
		$event->save();
		return redirect()->route('events.show', $event->id);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id, Request $request)
	{

		$event = Event::with(['country', 'region', 'city', 'attributes', 'images', 'comments.author', 'comments.likes', 'comments.replies.author',
			'venuePage' => function($query){
				$query->select('id','slug','name','main_image');
			},
			'creatorPage' => function($query){
				$query->select('id','slug','name','main_image');
			}
			])->where('id', $id)->firstOrFail();
		if($event->is_private){
			if(Auth::check()){
				$this->authorize('publicPrivateAccess', $event);
			}
			else{
				return redirect()->route('events.index');
			}
		}

		$event->attendees = $event->attendees()->paginate(config('common.event_attendes_popup_per_load'));

		// seperate main image from rest, group by boolean flag
		$event->images = $event->images->groupBy('is_main_image');
		$mainImage = $secondaryImages = $isCreatorOfTheEvent = $isAdminOfTheEvent = NULL;

		if(isset($event->images[1]) && isset($event->images[1][0])){
			$mainImage = $event->images[1][0];
		}
		if(isset($event->images) && isset($event->images[0])){
			$secondaryImages = $event->images[0];
		}

		if(Auth::check()){
			$isCreatorOfTheEvent = (Auth::id() == $event->user_id);
			$isAdminOfTheEvent = Auth::user()->isAdminOfTheEvent($event);
		}


		if($event->starts_at->gt(UserController::getUsersTime())){
		// show event as upcoming if it didn't start yet
			return view('events.upcoming', compact('event', 'mainImage', 'secondaryImages', 'isCreatorOfTheEvent', 'isAdminOfTheEvent'));
		}

		// Clear history photos in session if page is reloaded
		if($request->session()->has('history_event_photos')){
			$request->session()->forget('history_event_photos');
		}

		// Get first page of history photos
		$event->historyPhotos = $event->historyPhotos()->orderBy('id', 'DESC')->paginate(config('common.history_event_photos_per_load'));

		return view('events.history', compact('event', 'mainImage', 'secondaryImages', 'isCreatorOfTheEvent', 'isAdminOfTheEvent')); // if not, it belongs to history
	}

	public function likeHistoryPhoto(Request $request){
		$event = Event::find($request->id);
		if($event && $event->is_private){
			$this->authorize('publicPrivateAccess', $event);
		}

		$photo = \App\HistoryEventPhoto::where('id', $request->historyPhoto)->where('event_id', $request->id)->first();
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

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id, Request $request)
	{
		$event             = Event::with('attributes', 'venuePage', 'creatorPage', 'admins', 'images')->withoutGlobalScope('published')->where('id', $id)->firstOrFail();
		
		$this->authorize('edit', $event);

		$timezones         = DB::table('timezones')->get();
		$oldVenuePage      = $event->venuePage;
		$oldCreatorPage    = $event->creatorPage;
		
		$user              = Auth::user();
		$user->following   = $user->following()->paginate(config('common.general_follow_per_load'));
		$user->followers   = $user->followers()->paginate(config('common.general_follow_per_load'));
		
		$countries         = Country::all();
		$regions           = Region::where('country_id', $event->country_id)->get();
		$cities            = City::where('region_id', $event->region_id)->get();
		
		$attributes        = Attribute::where('type', 'LIKE', 'event.%')->where('parent_id', NULL)->with('children')->get()->groupBy('type');
		$event->attributes = $event->attributes->groupBy('type');

		$startsAt          = new Carbon($event->getOriginal('starts_at'), "UTC");
		$endsAt            = new Carbon($event->getOriginal('ends_at'), "UTC");

		$startsAt  = $startsAt->timezone($event->timezone);
		$endsAt    = $endsAt->timezone($event->timezone);


		// Clear images in session if request is not coming from pre-submitted form
		if($request->session()->get('errors') == NULL){
			if($request->session()->has('edit_event_images')){
				$request->session()->forget('edit_event_images');
			}
		};


		if($event->images && !$request->session()->has('edit_event_images')){
			$images = array('reserved');
			foreach($event->images as $image){
				$imageSize = File::size(base_path().'/public/uploads/events/'.$event->id.'/images/'.$image->name);
				if($image->name == $event->mainImage()->name){
					$images[0] = [$image->name, $imageSize, "is_temp" => 0];
				}
				else{
					$images[] = [$image->name, $imageSize, "is_temp" => 0];
				}
			}
			if($images[0] == 'reserved'){
				unset($images[0]);
			}
			$request->session()->put('edit_event_images', $images);
		}


		if($request->old('country') && is_numeric($request->old('country')) && $request->old('country') != $event->country_id){
			$regions = Region::select('id', 'name')->where('country_id', $request->old('country'))->orderBy('name', 'ASC')->get();
		}

		if($request->old('region') && is_numeric($request->old('region')) && $request->old('region') != $event->region_id){
			$cities = City::select('id', 'name')->where('region_id', $request->old('region'))->orderBy('name', 'ASC')->get();
		}



		if($request->old('venue_page') && is_numeric($request->old('venue_page')) && $request->old('venue_page') != $oldVenuePage->id){
			$oldVenuePage = Page::where('id', $request->old('venue_page'))->wherePageType('venue')->select('id','name')->first();
		}

		if($request->old('creator_page') && is_numeric($request->old('creator_page')) && $request->old('creator_page') != $oldCreatorPage->id){
			$oldCreatorPage = Page::where('id', $request->old('creator_page'))->wherePageType('organization')->select('id','name')->first();
		}

		if($request->old('admins') && is_array($request->old('admins'))){
			$oldAdmins = User::whereIn('id', $request->old('admins'))->where('id', '<>', Auth::id())->select('id','name',  'username', 'avatar')->get();
		}
		else{
			$oldAdmins = $event->admins;
		}

		return view('events.edit', compact('event', 'countries','regions', 'cities', 'attributes', 'timezones', 'oldVenuePage', 'oldCreatorPage', 'oldAdmins', 'user', 'startsAt', 'endsAt'));

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Requests\UpdateEventRequest $request)
	{		
		$now = Carbon::now('utc')->toDateTimeString(); // later used for timestamps in bulk insert
		$event              = Event::withoutGlobalScope('published')->where('id', $request->event)->firstOrFail();

		$this->authorize('edit', $event);
		
		session()->regenerateToken();
		$eventAdmins        = [];
		$is_location_hidden = false;

		if($request->has('hide_location') && $request->hide_location == 'on'){
			$is_location_hidden = true;
		}

		$event->title              = $request->title;
		$event->address            = $request->address;
		$event->zip_code           = $request->zip_code;
		$event->details            = $request->details;
		$event->venue_page_id      = $request->venue_page;
		$event->creator_page_id    = $request->creator_page;
		$event->country_id         = $request->country;
		$event->region_id          = $request->region;
		$event->city_id            = $request->city;
		$event->is_location_hidden = $is_location_hidden;
		$event->is_private         = $request->visibility;

		if(!$event->isHistory){
			$starting_time = new Carbon($request->starting_time.':00', $request->timezone);
			$ending_time = new Carbon($request->ending_time.':00', $request->timezone);

			$starting_time_UTC = $starting_time->timezone('UTC');
			$ending_time_UTC = $ending_time->timezone('UTC');

			$event->timezone           = $request->timezone;
			$event->starts_at          = $starting_time_UTC;
			$event->ends_at            = $ending_time_UTC;
		}

		$attributes = array_filter($request->only(['type', 'entrance', 'dress_code', 'age_limit', 'music', 'document']));
		$images = $request->session()->get('edit_event_images');

		if($request->has('admins') && is_array($request->input('admins'))){
			$eventAdmins = array_filter($request->input('admins'));
		}

		DB::transaction(function() use ($event, $attributes, $eventAdmins, $images, $request, $now)
		{
			$event->save();
			$event->attributes()->sync(array_values($attributes));

			$event->admins()->sync(array_values($eventAdmins));
			$notifs = [];
			
			foreach($eventAdmins as $a){
				$notifs[] = [
				'user_id'           => $a,
				'notification_type' => 'event_admin',
				'link_user_id'      => Auth::id(),
				'link_event_id'     => $event->id,
				'created_at'        => $now,
				'updated_at'        => $now
				];
			}

			if($notifs){
				\App\Notification::insert($notifs);
			}

			if(!empty($images))
			{
				$imageModels = [];
				$firstImageKey = key($images);
				foreach($images as $key => $image){
					if($image["is_temp"] == 1)
					{
						if($key == $firstImageKey){
							$imageModels[] = new \App\Image(['name' => $image[0], 'is_main_image' => 1]);
							$mainImage = $event->mainImage();
							if($mainImage){
								$mainImage->is_main_image = 0;
								$mainImage->save();
							}
						}
						else{
							$imageModels[] = new \App\Image(['name' => $image[0]]);
						}
					}
					elseif($image["is_temp"] == 0 && $key == $firstImageKey){
						$mainImage = $event->mainImage();
						if($mainImage && $mainImage->name !== $image[0])
						{
							$newMainImage = $event->images()->where('name', $image[0])->first();
							if($newMainImage)
							{
								$newMainImage->is_main_image = 1;
								$newMainImage->save();
								$mainImage->is_main_image = 0;
								$mainImage->save();
							}
						}
					}
				}
				$savedImages = $event->images()->saveMany($imageModels);
				$request->session()->forget('edit_event_images');
			}

			$eventPath = base_path().'/public/uploads/events/'.$event->id;
			$imagesPath = $eventPath.'/images';

			if(isset($savedImages) && count($savedImages) > 0){
				foreach($savedImages as $row){
					File::move($this->uploadsTempDirectory.$row->name, $imagesPath.'/'.$row->name);
				}
			}


		});  // Database transaction ends here

if($event->published){
	return redirect()->route('events.show', $event);
}
return redirect()->route('events.preview', $event->id);
}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$event = Event::where('id', '=', $id)->withoutGlobalScope('published')->first();
		
		if(!$event){
			return response('Error', 400);
		}
		if(Auth::user()->cannot('destroy', $event)){
			return response('Unathorized', 403);
		}
		if($event->delete()){
			return response('Deleted', 200);
		}
		return response('Error', 400);
	}


	public function deleteAdmin(Event $event, $id)
	{
		$user = User::find($id);

		if($user && $user->isAdminOfTheEvent($event)){
			if($user->adminOfEvents()->detach($event->id)){
				return response('Removed', 200);
			}
		}
		return response('Error', 400);
	}


	public function getAttendees($id){
		$event = Event::find($id);
		if($event){
			$attendeesTemplate = '';
			$attendees = $event->attendees()->paginate(config('common.event_attendes_popup_per_load'));

			foreach($attendees as $attendee)
			{
				$attendeesTemplate .= View::make('events.event_attendee_popup_template', compact('attendee'));
			};

			return response($attendeesTemplate, 200);
		}
		return response(['status' => 'error'], 404);
	}

	public function storeAttendee($id){
		$event = Event::find($id);

		if($event){

			if($event->is_private){
				$this->authorize('publicPrivateAccess', $event);
			}

			$event->attendees()->sync([Auth::id()], false);
			$user = Auth::user();
			$avatar = ($user->avatarFullPath) ? 'images/small59/'.$user->avatarFullPath : config('common.userDefaultAvatarPath');
			$response['status'] = 'success';
			$response['avatar'] = '<div class="column" data-id="'. $user->id .'"><img data-tooltip aria-haspopup="true" class="has-tip" title="'. $user->name. '" src="'. url($avatar) .'" alt=""></div>';

			return response($response, 200);
		}
		return response(['status' => 'error'], 404);
	}

	public function deleteAttendee($id)
	{
		$event = Event::find($id);
		if($event)
		{
			if($event->is_private){
				$this->authorize('publicPrivateAccess', $event);
			}

			$event->attendees()->detach(Auth::id());
			return response(['status' => 'success', 'id' => Auth::id()], 200);
		}
		return response(['status' => 'error'], 404);
	}

	public function storeComment($id, Request $request)
	{
		$event = Event::find($id);
		if($event){
			if($event->is_private){
				$this->authorize('publicPrivateAccess', $event);
			}

			$this->validate($request, [
				'parent_id' => 'numeric|exists:comments,id,parent_id,NULL',
				'body' => 'required'
				]);
			$comment = FALSE;
			DB::transaction( function() use ($request, $event, &$comment){
				$parent_id = NULL;
				if($request->has('parent_id')){
					$parent_id = $request->parent_id;
				}
				$commentModel = [
				'user_id' => Auth::id(),
				'event_id' => $event->id,
				'parent_id' => $parent_id,
				'body' => $request->body,
				];
				$comment = $event->comments()->create($commentModel);
			});
			if($comment){
				$comment->load('author');
				$event_id = $event->id;
				if($comment->parent_id == NULL){
					$totalComments = $event->numberOfComments;
					return response()->view('events.single_comment_template', compact('comment', 'event_id', 'totalComments'));
				}
				else{
					$reply = $comment;
					$displayNone = 'style="display:none;"';
					return response()->view('events.single_comment_reply_template', compact('reply', 'displayNone'));
				}
			}
		}
		return response("error", 404);
	}

	public function likeComment(Request $request){

		$comment = Comment::where('id', $request->comment)->where('parent_id', NULL)->first();
		if($comment){
			$event = Event::find($comment->event_id);
			if($event && $event->is_private){
				$this->authorize('publicPrivateAccess', $event);
			}

			$like = $comment->likes()->where('user_id', Auth::id())->withTrashed()->first();

			if($like && $request->unlike == 1 && !$like->trashed()){
				$like->delete();
				return response(['status' => 'unliked', 'totalLikes'=> $comment->numberOfLikes], 200);
			}
			if($like && $like->trashed()){
				$like->restore();
				return response(['status' => 'liked', 'totalLikes'=> $comment->numberOfLikes], 200);
			}
			if(!$like){
				$newLike = new Like(['user_id' => Auth::id(), 'likeable_id' => $comment->id]);
				$comment->likes()->save($newLike);
				return response(['status' => 'liked', 'totalLikes'=> $comment->numberOfLikes], 200);
			}
		}
		return response('error', 404);
	}


	public function get12Months(){
		$months = [];
		$start = Carbon::now();
		$end = $start->copy()->addYear();

		while($start->lte($end)){
			$months[$start->copy()->format('m')] = $start->copy()->format('F');
			$start->addMonth();
		}

		ksort($months);
		return $months;
	}



	public function inviteAList(Event $event, Request $request){
		$this->authorize('invite', $event);
		$this->validate($request, ['list_id' => 'required|exists:invite_lists,id,user_id,'.Auth::id()]);

		if($event->isListInvited($request->list_id) || $event->isHistory){
			return response('Error', 400);
		}

		$list = InviteList::find($request->list_id);
		$list->members->load(['wasInvitedToEvents' => function($query) use($event){
			$query->where('inviteable_id', $event->id);
		}]);
		$notifs = $invitations = NULL;
		$now = Carbon::now('utc')->toDateTimeString(); // later used for timestamps in bulk insert

		/* Get users to exclude from invitation, the creator of the event and admins */
		$excludedUsers = $event->admins()->select('id')->get();
		$excludedUsers = $excludedUsers->keyBy('id')->keys()->toArray();
		$excludedUsers[] = $event->user_id;

		foreach($list->members as $m){
			if($m->wasInvitedToEvents->count() == 0){
				if(!in_array($m->id, $excludedUsers)){
					$notifs[] = [
					'user_id'           => $m->id,
					'notification_type' => 'event_invite',
					'link_user_id'      => Auth::id(),
					'link_event_id'     => $event->id,
					'created_at'        => $now,
					'updated_at'        => $now
					];

					$invitations[] = [
					'user_id' => $m->id,
					'inviteable_id' => $event->id,
					'inviteable_type' => 'App\Event',
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
			$event->inviteLists()->save($list);
			return response('Invited',200);
		}

		return response('Error', 400);
	}


	public function inviteAUser(Event $event, Request $request){
		$this->authorize('invite', $event);

		/* Get users to exclude from invitation, the creator of the event and admins */
		$excludedUsers = $event->admins()->select('id')->get();
		$excludedUsers = $excludedUsers->keyBy('id')->keys()->toArray();
		$excludedUsers[] = $event->user_id;

		$this->validate($request, ['user_id' => 'required|exists:users,id|not_in:'.implode(',', $excludedUsers)]);

		if($event->isUserInvited($request->user_id) || $event->isHistory){
			return response('Error', 400);
		}

		$user = User::find($request->user_id);

		$notifs = $invitations = NULL;
		$now = Carbon::now('utc')->toDateTimeString(); // later used for timestamps in bulk insert



		$notif = [
		'user_id'           => $user->id,
		'notification_type' => 'event_invite',
		'link_user_id'      => Auth::id(),
		'link_event_id'     => $event->id,
		'created_at'        => $now,
		'updated_at'        => $now
		];
		
		$invitation = [
		'user_id'         => $user->id,
		'inviteable_id'   => $event->id,
		'inviteable_type' => 'App\Event',
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


	public function getInviteListsAjax(Event $event, Request $request)
	{
		$this->authorize('invite', $event);
		$event->load('inviteLists');
		$lists = Auth::user()->inviteLists()->orderBy('id', 'DESC')->paginate(config('common.general_items_per_load'));
		$listsTemplate = '';

		foreach($lists as $list)
		{
			$listsTemplate .= View::make('dashboard.invite_list_event_popup_row', compact('list', 'event'));
		}
		if($listsTemplate){
			return response($listsTemplate, 200);
		}
		return response("error", 400);

	}


	public function searchInviteUsersAjax(Event $event, $search, Request $request)
	{
		$this->authorize('invite', $event);

		$per_load = 5;
		if($request->has('per_request') && is_numeric($request->per_request) && ($request->per_request <= 30)){
			$per_load = $request->per_request;
		}

		/* Get users to exclude from search, the creator of the event and admins */
		$excludedUsers = $event->admins()->select('id')->get();
		$excludedUsers = $excludedUsers->keyBy('id')->keys()->toArray();
		$excludedUsers[] = $event->user_id;

		$users = User::like('name', $search)->select('id', 'name', 'username', 'avatar')->whereNotIn('id', $excludedUsers)->orderBy('name')->take($per_load)->get();
		$users->load(['wasInvitedToEvents' => function($query) use($event){
			$query->where('inviteable_id', $event->id);
		}]);

		$usersHTML = '';

		foreach($users as $user){
			$inviteText = 'Invite';
			$inviteClass = '';
			if($user->wasInvitedToEvents->count() > 0){
				$inviteText = 'Invited';
				$inviteClass = 'added';
			}
			$usersHTML .= "<div class=\"column user\" data-id=\"$user->id\"><a href=\"/users/$user->username\" target=\"_blank\"><img src=\"/images/small59/$user->avatarFullPath\"></a><div><span class=\"name\">$user->name</span><button class=\"add-button $inviteClass\" type=\"button\" data-id=\"$user->id\" data-name=\"$user->name\" data-username=\"$user->username\">$inviteText</button></div></div>";
		}
		return response($usersHTML, 200);
	}

}
