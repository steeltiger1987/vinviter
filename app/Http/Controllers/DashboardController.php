<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use Auth;
use App\InviteList;
use View;

class DashboardController extends Controller
{

	protected $owner;

	public function __construct()
	{
		$this->middleware('auth');
		$this->owner = Auth::user();
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
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}


	public function getSavedEvents(Request $request)
	{

		if($request->ajax()){
			$eventsTemplate = '';
			$this->validate($request, ['after' => 'required|numeric']);
			$events = $this->owner->events()->saved()->orderBy('id', 'DESC')->where('id', '<', $request->after)->take(config('common.events_per_load'))->get();
			if($events){
				foreach($events as $event)
				{
					$eventsTemplate .= View::make('dashboard.savedEvent_row', compact('event'));
				}
			}
			if($eventsTemplate){
				return response(['html' => $eventsTemplate, 'leftOff' => $events->last()->id], 200);
			}
			return response("error", 400);
		}
		$events = $this->owner->events()->saved()->orderBy('id', 'DESC')->paginate(config('common.events_per_load'));
		return view('dashboard.savedEvents', compact('events'));
	}


	public function getSavedPages(Request $request)
	{

		if($request->ajax()){
			$pagesTemplate = '';
			$this->validate($request, ['after' => 'required|numeric']);
			$pages = $this->owner->pages()->saved()->with('getPageType')->where('id', '<', $request->after)->orderBy('id', 'DESC')->take(config('common.pages_per_load'))->get();

			if($pages){
				foreach($pages as $page)
				{
					$pagesTemplate .= View::make('dashboard.savedPage_row', compact('page'));
				}
			}
			if($pagesTemplate){
				return response(['html' => $pagesTemplate, 'leftOff' => $pages->last()->id], 200);
			}
			return response("error", 400);
		}

		$pages = $this->owner->pages()->saved()->with('getPageType')->orderBy('id', 'DESC')->paginate(config('common.pages_per_load'));
		return view('dashboard.savedPages', compact('pages'));
	}


	public function getPublishedPages(Request $request)
	{
		$inviteLists = InviteList::where('user_id', Auth::id())->orderBy('id', 'DESC')->paginate(config('common.general_items_per_load'), ['*'], 'page', 1);


		if($request->ajax()){
			$pagesTemplate = '';
			$this->validate($request, ['after' => 'required|numeric']);
			$pages = $this->owner->pages()->with('getPageType', 'inviteLists')->where('id', '<', $request->after)->orderBy('id', 'DESC')->take(config('common.pages_per_load'))->get();

			if($pages){
				foreach($pages as $page)
				{
					$pagesTemplate .= View::make('dashboard.creatorPage_row', compact('page', 'inviteLists'));
				}
			}
			if($pagesTemplate){
				return response(['html' => $pagesTemplate, 'leftOff' => $pages->last()->id], 200);
			}
			return response("error", 400);
		}

		$pages = $this->owner->pages()->with('getPageType', 'inviteLists')->orderBy('id', 'DESC')->paginate(config('common.pages_per_load'));
		return view('dashboard.pages', compact('pages', 'inviteLists'));
	}


	public function getPublishedPagesTypeAdmin(Request $request)
	{
		$inviteLists = InviteList::where('user_id', Auth::id())->orderBy('id', 'DESC')->paginate(config('common.general_items_per_load'), ['*'], 'page', 1);

		if($request->ajax()){
			$pagesTemplate = '';
			$this->validate($request, ['after' => 'required|exists:pages,id']);
			$pages = $this->owner->adminOfPages()->with('getPageType', 'inviteLists')->where('id', '<', $request->after)->orderBy('id', 'DESC')->take(config('common.pages_per_load'))->get();

			if($pages){
				foreach($pages as $page)
				{
					$pagesTemplate .= View::make('dashboard.adminPage_row', compact('page', 'inviteLists'));
				}
			}
			if($pagesTemplate){
				return response(['html' => $pagesTemplate, 'leftOff' => $pages->last()->id], 200);
			}
			return response("error", 400);
		}

		$pages = $this->owner->adminOfPages()->with('getPageType', 'inviteLists')->orderBy('id', 'DESC')->paginate(config('common.pages_per_load'));
		return view('dashboard.adminPages', compact('pages', 'inviteLists'));
	}


	public function getUpcomingEvents(Request $request)
	{
		$inviteLists = InviteList::where('user_id', Auth::id())->orderBy('id', 'DESC')->paginate(config('common.general_items_per_load'), ['*'], 'page', 1);

		if($request->ajax()){
			$eventsTemplate = '';
			$this->validate($request, ['after' => 'required|numeric']);
			$events = $this->owner->events()->upcoming()->with('inviteLists')->orderBy('id', 'DESC')->where('id', '<', $request->after)->take(config('common.events_per_load'))->get();

			if($events){
				foreach($events as $event)
				{
					$eventsTemplate .= View::make('dashboard.creatorUpcomingEvent_row', compact('event', 'inviteLists'));
				}
			}
			if($eventsTemplate){
				return response(['html' => $eventsTemplate, 'leftOff' => $events->last()->id], 200);
			}
			return response("error", 400);
		}

		$events = $this->owner->events()->upcoming()->with('inviteLists')->orderBy('id', 'DESC')->paginate(config('common.events_per_load'));
		return view('dashboard.upcoming', compact('events', 'inviteLists'));
	}


	public function getUpcomingEventsTypeAdmin(Request $request)
	{
		$inviteLists = InviteList::where('user_id', Auth::id())->orderBy('id', 'DESC')->paginate(config('common.general_items_per_load'), ['*'], 'page', 1);

		if($request->ajax()){
			$eventsTemplate = '';
			$this->validate($request, ['after' => 'required|exists:events,id']);
			$events = $this->owner->adminOfEvents()->upcoming()->with('inviteLists')->orderBy('id', 'DESC')->where('id', '<', $request->after)->take(config('common.events_per_load'))->get();

			if($events){
				foreach($events as $event)
				{
					$eventsTemplate .= View::make('dashboard.adminUpcomingEvent_row', compact('event', 'inviteLists'));
				}
			}
			if($eventsTemplate){
				return response(['html' => $eventsTemplate, 'leftOff' => $events->last()->id], 200);
			}
			return response("error", 400);
		}

		$events = $this->owner->adminOfEvents()->upcoming()->with('inviteLists')->orderBy('id', 'DESC')->paginate(config('common.events_per_load'));
		return view('dashboard.upcomingTypeAdmin', compact('events', 'ownershipType', 'inviteLists'));
	}



	public function getHistoryEvents(Request $request)
	{

		if($request->ajax()){
			$eventsTemplate = '';
			$this->validate($request, ['after' => 'required|numeric']);
			$events = $this->owner->events()->history()->orderBy('id', 'DESC')->where('id', '<', $request->after)->take(config('common.events_per_load'))->get();
			
			if($events){
				foreach($events as $event)
				{
					$eventsTemplate .= View::make('dashboard.creatorHistoryEvent_row', compact('event'));
				}
			}
			if($eventsTemplate){
				return response(['html' => $eventsTemplate, 'leftOff' => $events->last()->id], 200);
			}
			return response("error", 400);
		}

		$events = $this->owner->events()->history()->orderBy('id', 'DESC')->paginate(config('common.events_per_load'));
		return view('dashboard.history', compact('events', 'ownershipType'));
	}


	public function getHistoryEventsTypeAdmin(Request $request)
	{

		if($request->ajax()){
			$eventsTemplate = '';
			$this->validate($request, ['after' => 'required|exists:events,id']);
			$events = $this->owner->adminOfEvents()->history()->orderBy('id', 'DESC')->where('id', '<', $request->after)->take(config('common.events_per_load'))->get();

			if($events){
				foreach($events as $event)
				{
					$eventsTemplate .= View::make('dashboard.adminHistoryEvent_row', compact('event'));
				}
			}
			if($eventsTemplate){
				return response(['html' => $eventsTemplate, 'leftOff' => $events->last()->id], 200);
			}
			return response("error", 400);
		}

		$events = $this->owner->adminOfEvents()->history()->orderBy('id', 'DESC')->paginate(config('common.events_per_load'));
		return view('dashboard.historyTypeAdmin', compact('events', 'ownershipType'));
	}

}
