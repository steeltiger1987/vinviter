@extends('layouts.master_basic')

@section('title', 'Delete account - Settings')

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
							<li><a href="{{ route('settings.profile') }}">Profile</a></li>
							{{-- <li><a href="{{ route('settings.preferences') }}">Preferences</a></li> --}}
							<li class="active"><a href="{{ route('settings.deleteAccount') }}">Delete account</a></li>
						</ul>
					</div>
					<div class="small-9 columns">
						<div class="settings-container">
                            <h5>Do you want to delete this account?</h5>
							<form method="POST" action="{{ route('settings.postDeleteAccount') }}">
								{{ csrf_field() }}
								{{ method_field('DELETE') }}
								<input type="submit" name="delete_account" class="button delete-button" value="Yes">
								<input type="submit" name="delete_account" class="button orange delete-button" value="No">
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
