@extends('layouts.master_basic')

@section('title', 'Preferences - Settings')

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
							{{-- <li class="active"><a href="{{ route('settings.preferences') }}">Preferences</a></li> --}}
							<li><a href="{{ route('settings.deleteAccount') }}">Delete account</a></li>
						</ul>
					</div>
					<div class="small-9 columns">
						<div class="settings-container">
							<form method="POST">
								{{ csrf_field() }}
								<div class="row">
									<div class="small-12 columns">
										<h5>I want to receive invitations from:</h5>
									</div>
									<div class="small-6 columns float-left">
										<div class="expanded button-group">
											@if($user->receive_invitations)
											<a id="receive-invitations-on" class="button text-uppercase">Everyone</a>
											<a id="toggle-manage-invitations" class="secondary button text-uppercase">Custom</a>
											@else
											<a id="receive-invitations-on" class="secondary button text-uppercase">Everyone</a>
											<a id="toggle-manage-invitations" class="button text-uppercase">Custom</a>
											@endif
										</div>
									</div>	
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row content-block">
			<div class="small-12 columns hide" id="manage-invitations-wrap">
				<div class="row">
					<div class="small-10 small-centered columns manage-invitations-block">
						<button class="close" aria-label="Close" type="button">
							<span aria-hidden="true">&times;</span>
						</button>
						<div class="row small-up-3 manage-invitations-user-list">
							@if(6 != 6)
							<?php $addedMode = true; ?>
							@foreach($oldAdmins as $followable)
							@include('layouts.select_admin_add_user_popup')
							<input type="hidden" name="admins[]" value="{{ $followable->id }}">
							@endforeach
							<?php $addedMode = false; ?>
							@endif
						</div>

						<div class="row">
							<div class="small-7 columns">
								<input type="text" placeholder="Search users" id="manage-invitations-search">
							</div>
						</div>

						<div class="row small-up-3 manage-invitations-user-list search-results">
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
