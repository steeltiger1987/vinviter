@extends('layouts.master_basic')

@section('title', 'Profile - Settings')

@section('body')
<div id="wrapper" class="page-bg-gray">
	@include('layouts.header')
	<section class="content">
		<div class="row">
			<div class="small-9 columns small-centered">
				<div class="row user-settings">
					<div class="small-3 columns">
						<ul class="tabs-show-vertical tabs-vertical-li-border">
							<li><a href="{{ route('settings.account') }}">Account</a></li>
							<li class="active"><a href="{{ route('settings.profile') }}">Profile</a></li>
							{{-- <li><a href="{{ route('settings.preferences') }}">Preferences</a></li> --}}
							<li><a href="{{ route('settings.deleteAccount') }}">Delete account</a></li>
						</ul>
					</div>
					<div class="small-9 columns">
						<div class="settings-container">
							@if(Session::has('success'))
							<div class="callout success">
								<p>{{ Session::get('success') }}</p>
							</div>
							@endif
							<form method="POST" name="profileSettings">
								{{ csrf_field() }}
								<div class="row">
									<div class="small-4 columns">
										<label for="username">Username:</label>
									</div>
									<div class="small-8 columns">
										<input type="text" name="username" id="username" value="{{ old('username', $user->username) }}">
										{!! $errors->first('username', '<span class="form-error is-visible">:message</span>') !!}
									</div>
								</div>

								<div class="row gender-radio">
									<div class="small-4 columns">
										<label for="male">Gender:</label>
									</div>
									<div class="small-8 columns">
										<input type="radio" name="gender" value="male"  id="male" {{  old('gender', $user->gender) == 'male' ? 'checked' : '' }}><label for="male">Male</label>
										<input type="radio" name="gender" value="female" id="female" {{ old('gender', $user->gender) == 'female' ? 'checked' : '' }}><label for="female">Female</label>
										{!! $errors->first('gender', '<span class="form-error is-visible">:message</span>') !!}
									</div>
								</div>

								<div class="row">
									<div class="small-4 columns">
										<label for="country">Country:</label>
									</div>
									<div class="small-8 columns">
										<select id="country" name="country">
											<option value="">Select...</option>
											@foreach($countries as $country)
											<option value="{{ $country->id }}" {{ (old('country', $user->country_id) == $country->id) ? 'selected' : '' }}>
												{{ $country->name }}
											</option>
											@endforeach
										</select>
										{!! $errors->first('country', '<span class="form-error is-visible">:message</span>') !!}
									</div>
								</div>

								<div class="row" id="region-row">
									<div class="small-4 columns">
										<label for="region">Region:</label>
									</div>
									<div class="small-8 columns">
										<select id="region" name="region">
											<option value="">Select...</option>
											@if(count($regions) > 0))
											@foreach($regions as $region)
											<option value="{{ $region->id }}" {{ (old('region', $user->region_id) == $region->id) ? 'selected' : '' }}>{{ $region->name }}</option>
											@endforeach
											@endif
										</select>
										{!! $errors->first('region', '<span class="form-error is-visible">:message</span>') !!}
									</div>
								</div>

								<div class="row">
									<div class="small-4 columns">
										<label for="city">City:</label>
									</div>
									<div class="small-8 columns">
										<select id="city" name="city">
											<option value="">Select...</option>
											@if(count($cities) > 0))
											@foreach($cities as $city)
											<option value="{{ $city->id }}" {{ (old('city', $user->city_id) == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
											@endforeach
											@endif
										</select>
										{!! $errors->first('city', '<span class="form-error is-visible">:message</span>') !!}
									</div>
								</div>

								<div class="row">
									<div class="small-4 columns">
										<label for="timezone">Timezone:</label>
									</div>
									<div class="small-8 columns">
										<select name="timezone" id="timezone">
											@foreach($timezones as $timezone)
											<option value="{{ $timezone->php_timezone_identifier_name }}" {{ (old('timezone', $user->timezone) == $timezone->php_timezone_identifier_name) ? 'selected' : '' }}>{{ $timezone->name }}</option>
											@endforeach
										</select>
										{!! $errors->first('timezone', '<span class="form-error is-visible">:message</span>') !!}
									</div>
								</div>
								<div class="row">
									<div class="small-12 columns">
										<input type="submit" class="button float-right" name="submit" value="Save Changes">
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	@include('layouts.footer')
	<script src="{{ asset('js/all.js') }}"></script>
</div>
@endsection
