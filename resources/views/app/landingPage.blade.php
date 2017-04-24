@extends('layouts.master_basic')

@section('title', 'Home')

@section('body')	
<div id="wrapper">
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

				<div class="small-3 columns float-right">
					@if(Auth::check())
					<div class="user-bar float-right">
						<ul class="dropdown menu" data-dropdown-menu data-disable-hover="true" data-click-open="true" data-alignment="right">
							{{-- <li><a href="" class="fa fa-refresh action-icons"></a></li> --}}
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
	<section class="content landing-page">
		<div class="introduction">
			<div class="row">
				<p class="main-heading">CREATE SHARE AND ENHANCE EVENTS WITH VINVITER</p>
			</div>
			<div class="row small-up-4 introduction-content">
				<div class="column">
					<div class="box">
						<span class="box-heading">Discover</span>
						<p>
							Discover and locate the most amazing events in your local area – all the information festivals, concerts and history events will be easily accessible to you with only a click of your mouse.
						</p> 
					</div>
				</div>
				<div class="column">
					<div class="box">
						<span class="box-heading">Create</span>
						<p>
							Build and publish your own events and attract, entice and impress thousands of people who are looking for exactly the outstanding event you have to present them with!
						</p> 
					</div>
				</div>
				<div class="column">
					<div class="box">
						<span class="box-heading">Invite</span>
						<p>
							Create amazing invitations for the interested parties all around the world and make your events the coolest, happily visited and most talked about in the area!
						</p> 
					</div>
				</div>
				<div class="column">
					<div class="box">
						<span class="box-heading">Never forget</span>
						<p>
							Cherish the best moments and memories you’ve had at various events & showcase your opinions and perceptions to the world in an attention – grabbing manner!
						</p> 
					</div>
				</div>
			</div>
			<div class="row text-center">
				{{-- <a href="{{ route('upcoming') }}" class="button explore-button">Explore</a> --}}
			</div>
		</div>
		<div class="row">
			<div class="small-12 columns-small-centered">
				<h5 class="links-to-social-heading">Follow us</h5>
				<p class="links-to-social">
					<a href="https://www.facebook.com/Vinviter-1458318067744581/" target="_blank">
						<span class="fa-stack fa-lg">
							<i class="fa fa-circle fa-stack-2x"></i>
							<i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
						</span>
					</a>
					<a href="https://twitter.com/Vinviter" target="_blank">
						<span class="fa-stack fa-lg">
							<i class="fa fa-circle fa-stack-2x"></i>
							<i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
						</span>
					</a>
					<a href="https://plus.google.com/100425952813067892726/about" target="_blank">
						<span class="fa-stack fa-lg">
							<i class="fa fa-circle fa-stack-2x"></i>
							<i class="fa fa-google-plus fa-stack-1x fa-inverse"></i>
						</span>
					</a>
				</p>
			</div>
		</div>
	</section>
	@include('layouts.footer')
	<script src="{{ asset('js/all.js') }}"></script>
</div>

@endsection