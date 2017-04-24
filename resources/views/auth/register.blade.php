@extends('layouts.master_basic')

@section('title', 'Sign up')

@section('body')
	<div id="wrapper" class="register">
		@include('layouts.header')

		<section class="content">
			<div class="row">
			@if(Session::has('auth_verify_email'))
				<div class="small-8 small-centered columns">
					<div class="callout success no-border">
						<h5>Thank you for signing up!</h5>
						<p>Check your email for the account verification link.</p>
						<p></p>
					</div>
				</div>
			@else
				<div class="small-4 small-centered columns">
					<div class="account-panel">
						<div class="panel-top">
							<h3><span class="fa fa-user-plus"></span>Sign up</h3>
						</div>
						<div class="panel-body">
							<form action="{{ route('auth.register') }}" method="POST" name="signup" data-abide novalidate>
								{!! csrf_field() !!}
								<label>
									<input type="text" name="name" value="{{ old('name') }}" placeholder="Full name" required>
									@if($errors->has('name'))
									<span class="form-error is-visible server-side-error">{{ $errors->first('name') }}</span>
									@endif
									<span class="form-error">Full name is required.</span>
								</label>
								<label>
									<input type="text" name="username" value="{{ old('username') }}" placeholder="Username" required>
									@if($errors->has('username'))
									<span class="form-error is-visible server-side-error">{{ $errors->first('username') }}</span>
									@endif
									<span class="form-error">Username is required.</span>
								</label>
								<label>
									<input type="email" name="email" value="{{ old('email') }}" placeholder="Email address" required>
									@if($errors->has('email'))
									<span class="form-error is-visible server-side-error">{{ $errors->first('email') }}</span>
									@endif
									<span class="form-error">Please enter an email address.</span>
								</label>
								<label>
									<input type="password" name="password" placeholder="Password" required>
									@if($errors->has('password'))
									<span class="form-error is-visible server-side-error">{{ $errors->first('password') }}</span>
									@endif
									<span class="form-error">Password is required.</span>
								</label>

								<label>
									<select name="country" required>
										<option value="" selected default>Select country</option>
										@foreach($countries as $country)
										<option value="{{ $country->id }}" {{ (old('country') == $country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
										@endforeach
									</select>
									@if($errors->has('country'))
									<span class="form-error is-visible server-side-error">{{ $errors->first('country') }}</span>
									@endif
									<span class="form-error">Country is required.</span>
								</label>

								<label id="region-label">
									<select name="region" required>
										<option value="" default>Select region</option>
										@if(old('country') && count($regions) > 0)
										@foreach($regions as $region)
										<option value="{{ $region->id }}" {{ (old('region') == $region->id) ? 'selected' : '' }}>{{ $region->name }}</option>
										@endforeach
										@endif
									</select>
									@if($errors->has('region'))
									<span class="form-error is-visible server-side-error">{{ $errors->first('region') }}</span>
									@endif
									<span class="form-error">Region is required.</span>
								</label>

								<label id="city-label">
									<select name="city" required>
										<option value="" selected default>Select city</option>
										@if(old('region') && count($regions) > 0))
										@foreach($cities as $city)
										<option value="{{ $city->id }}" {{ (old('city') == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
										@endforeach
										@endif
									</select>
									@if($errors->has('city'))
									<span class="form-error is-visible server-side-error">{{ $errors->first('city') }}</span>
									@endif
									<span class="form-error">City is required.</span>
								</label>

								<label id="timezone-label">
									<select name="timezone" required>
										<option value="" selected default>Select timezone</option>
										@foreach($timezones as $timezone)
										<option value="{{ $timezone->php_timezone_identifier_name }}" {{ (old('timezone') == $timezone->php_timezone_identifier_name) ? 'selected' : '' }}>{{ $timezone->name }}</option>
										@endforeach
									</select>
									@if($errors->has('timezone'))
									<span class="form-error is-visible server-side-error">{{ $errors->first('timezone') }}</span>
									@endif
									<span class="form-error">Timezone is required.</span>
								</label>

								<div class="gender-switch">
									<label><input type="radio" id="male" name="gender" value="male" {{ (old('gender') == 'female') ? '' : 'checked' }}><span>Male</span></label>
									<label><input type="radio" id="female" name="gender" value="female" {{ (old('gender') == 'female') ? 'checked' : '' }}><span>Female</span></label>
								</div>
								<p><input type="submit" name="submit" class="button expanded" value="Sign up"></p>
							</form>
						</div>
						<span class="dotted-hr"></span>
						<div class="panel-bottom-text">
							<p class="log-reg-here">Already a member? <a href="{{ route('auth.login') }}" title="Log in here">Log in here</a></p>
							<p class="agree-to-terms">By clicking sign up, you agree to our <a href="{{ route('app.pages.terms') }}">Terms of service</a> and <a href="{{ route('app.pages.privacy') }}">Privacy policy.</a></p>
						</div>
					</div>
				</div>
			@endif
			</div>
		</section>

		@include('layouts.footer')
		<script src="{{ asset('js/all.js') }}"></script>
	</div>
@endsection