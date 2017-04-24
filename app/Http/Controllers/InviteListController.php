<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\InviteList;
use Auth;
use View;

class InviteListController extends Controller
{

	protected $owner;
	
	public function __construct()
	{
		$this->middleware('auth');
		$this->owner = Auth::user();
	}


	public function index(Request $request)
	{

		if($request->ajax()){
			$this->validate($request, ['after' => 'required|numeric']);
			$lists = $this->owner->inviteLists()->orderBy('id', 'DESC')->where('id', '<', $request->after)->take(config('common.general_items_per_load'))->get();
			$listsTemplate = '';

			if($lists){
				foreach($lists as $list)
				{
					$listsTemplate .= View::make('dashboard.invite_list_block', compact('list'));
				}
			}
			if($listsTemplate){
				return response(['html' => $listsTemplate, 'leftOff' => $lists->last()->id], 200);
			}
			return response("error", 400);
		}

		$lists = $this->owner->inviteLists()->orderBy('id', 'DESC')->paginate(config('common.general_items_per_load'));
		return view('dashboard.inviteLists', compact('lists'));
	}

	public function store(Request $request)
	{
		$this->validate($request, ['list_name' => 'required|max:255']);

		$list = InviteList::create(['name' => $request->list_name, 'user_id' => Auth::id()]);

		if($list){
			$listBlock = View::make('dashboard.invite_list_block', compact('list'));
			return response($listBlock, 200);
		}
		else{
			return response("error", 400);
		}
	}


	public function delete($id, Request $request)
	{
		$list = InviteList::find($id);
		if($list && $request->delete_list == 'Yes'){
			if(Auth::id() !== $list->user_id){
				return response('Unauthorized!', 400);
			}

			if($list->delete()){
				return response('Deleted', 200);
			}
		}
		return response('Error', 400);
	}



	public function update($id, Request $request)
	{
		if($request->has('list_name')){
			$this->validate($request, ['list_name' => 'required|max:255']);
			$list       = InviteList::find($id);

			if($list){
				if(Auth::id() !== $list->user_id){
					return response('Unauthorized!', 400);
				}

				$list->name = $request->list_name;

				if($list->save()){
					return response('Updated', 200);
				}
			}
			return response('Error', 400);
		}

		elseif($request->has('save_invite_list_members')){
			$this->validate($request, [
				'members'        => 'array',
				'members.*'      => 'required_with:members|exists:users,id',
				]);

			$list = InviteList::find($id);

			if($list && Auth::id() == $list->user_id && $request->has('members') && is_array($request->input('members')))
			{
				$listMembers = array_filter($request->input('members'));
				$list->members()->sync(array_values($listMembers));
			}
			return redirect()->back();
		}
		else{
			return redirect()->back();
		}
	}


	public function edit(InviteList $list){
		$list->load('members');
		$user = Auth::user();
		$user->following = $user->following()->paginate(config('common.general_follow_per_load'));
		$user->followers = $user->followers()->paginate(config('common.general_follow_per_load'));
		return view('dashboard.invite_list_edit', compact('list', 'user'));
	}


}
