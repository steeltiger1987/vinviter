<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
use App\Http\Validators\HashValidator;
use View;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Validator::resolver(function($translator, $data, $rules, $messages) {
			return new HashValidator($translator, $data, $rules, $messages);
		});

		Validator::extend('without_spaces', function($attr, $value){
			return preg_match('/^\S*$/u', $value);
		});
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
