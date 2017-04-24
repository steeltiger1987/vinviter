@extends('layouts.master_basic')

@section('title', 'Account - Settings')

@section('body')
<div id="wrapper" class="page-bg-gray">
	@include('layouts.header')
	<section class="content">
		<div class="row">
			<div class="small-9 columns small-centered">
				<div class="row user-settings">
					<div class="small-3 columns">
						<ul class="tabs-show-vertical tabs-vertical-li-border">
							<li class="active"><a href="{{ route('settings.account') }}">Account</a></li>
							<li><a href="{{ route('settings.profile') }}">Profile</a></li>
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
							<form method="POST">
								{{ csrf_field() }}
								<div class="row">
									<div class="small-4 columns">
										<label for="full-name">Full name:</label>
									</div>
									<div class="small-8 columns">
										<input type="text" name="full_name" id="full-name" value="{{ old('full_name', $user->name) }}">
										{!! $errors->first('full_name', '<span class="form-error is-visible">:message</span>') !!}
									</div>
								</div>

								<div class="row">
									<div class="small-4 columns">
										<label for="email">Email address:</label>
									</div>
									<div class="small-8 columns">
										<input type="email" name="email" id="email" value="{{ old('email', $user->email) }}">
										{!! $errors->first('email', '<span class="form-error is-visible">:message</span>') !!}
									</div>
								</div>

								<div class="row">
									<div class="small-4 columns">
										<label for="current-password">Current password:</label>
									</div>
									<div class="small-8 columns">
										<input type="password" name="current_password" id="current-password">
										{!! $errors->first('current_password', '<span class="form-error is-visible">:message</span>') !!}
									</div>
								</div>
								<div class="row">
									<div class="small-4 columns">
										<label for="new-password">New password:</label>
									</div>
									<div class="small-8 columns">
										<input type="password" name="new_password" id="new-password">
										{!! $errors->first('new_password', '<span class="form-error is-visible">:message</span>') !!}
									</div>
								</div>
								<div class="row">
									<div class="small-4 columns">
										<label for="confirm-password">Confirm password:</label>
									</div>
									<div class="small-8 columns">
										<input type="password" name="new_password_confirmation" id="confirm-password">
										{!! $errors->first('new_password_confirmation', '<span class="form-error is-visible">:message</span>') !!}
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
