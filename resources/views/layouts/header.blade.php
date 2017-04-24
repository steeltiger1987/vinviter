<header>
	<div class="top-bar top-header {{ (in_array(Request::url(), [route('auth.login'), route('auth.register')])) ? 'no-bd-btm' : '' }}">
		<div class="row expanded">
			<div class="small-3 columns">
				<ul class="dropdown menu" data-dropdown-menu>
					@if(Auth::check())
					<li><a href="{{ route('upcoming') }}"><img src="{{ asset('images/logo.png') }}" alt=""></a></li>
					@else
					<li><a href="{{ route('landingPage') }}"><img src="{{ asset('images/logo.png') }}" alt=""></a></li>
					@endif
				</ul>
			</div>
			<div class="small-6 columns">
				<div class="search-bar">
					<a href="{{ route('upcoming') }}"><img class="explore" src="{{ asset('images/explore.png') }}" alt=""></a>
					<div class="search-term">
						<input id="app-main-search" type="text" placeholder="Search Events, Pages and Profiles...">
					</div>
					<a href="{{ route('app.create') }}" title="Create" class="create-button">Create</a>
				</div>
			</div>
			<div class="small-3 columns float-right">
				@if(Auth::check())
				<div class="user-bar float-right">
					<ul class="dropdown menu" data-dropdown-menu data-disable-hover="true" data-click-open="true" data-alignment="right">
						{{-- <li><a href="" class="fa fa-refresh action-icons"></a></li> --}}
						<li id="notifications-icon">
							<?php 
								$userc = new \App\Http\Controllers\UserController;
								$notifications = $userc->getLatestNotificationsForPopup();
							?>
							@if($notifications->count())
								<a href="{{ route('user.notifications') }}" class="fa fa-bell action-icons" id="notifications-bell-icon" data-has-new="1">
								<span id="new-notifications-count">{{ $notifications->count() }}</span>
								</a>
							@else
							<a href="{{ route('user.notifications') }}" class="fa fa-bell action-icons" id="notifications-bell-icon" data-has-new="0">
							<span id="new-notifications-count" class="hide"></span>
							</a>
							@endif
							<ul class="menu">
								<div class="user-notifications-popup" id="notifications-popup">
									@if($notifications)
									@include('user.notification_popup')
									@endif
								</div>
							</ul>
						</li>
						<li>
							<a href="{{ route('user.profile', Auth::user()->username) }}" class="profile-picture">
								<img src="{{ url('images/small41/'.Auth::user()->avatarFullPath) }}">
							</a>
						</li>
						<li>
							<a href="#">{{ Auth::user()->name }}</a>
							<ul class="menu">
								<li><a href="{{ route('user.profile', Auth::user()->username) }}">My Profile</a></li>
								<li><a href="{{ route('dashboard.upcomingEvents') }}">Dashboard</a></li>
								<li><a href="{{ route('settings.account') }}">Settings</a></li>
								<li><a href="{{ route('auth.logout') }}">Log out</a></li>
							</ul>
						</li>
					</ul>
				</div>
				@else
				<ul class="menu">
					@if(Request::url() == route('auth.login'))
					<li class="float-right"><a href="{{ route('auth.register') }}" title="Sign up" class="log-reg-button">Sign up</a></li>
					@else
					<li class="float-right"><a href="{{ route('auth.login') }}" title="Log in" class="log-reg-button">Log in</a></li>
					@endif
				</ul>
				@endif
			</div>
		</div>
	</div>
</header>