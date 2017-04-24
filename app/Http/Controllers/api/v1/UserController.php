<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Auth;

class UserController extends Controller
{
	public function search($name, Request $request){
		$per_load = 5;

		if($request->has('per_request') && is_numeric($request->per_request) && ($request->per_request <= 30)){
			$per_load = $request->per_request;
		}
		if($request->has('includeUser') && $request->includeUser == 1){
			$users = User::like('name', $name)->select('id', 'name', 'username', 'avatar')->orderBy('name')->take($per_load)->get();
			return $users;
		}

		$users = User::like('name', $name)->select('id', 'name', 'username', 'avatar')->where('id', '<>', Auth::id())->orderBy('name')->take($per_load)->get();

		return $users;
	}
}
