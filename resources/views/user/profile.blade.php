@extends('layouts.master')

@section('title', $user->name)

@section('head')
<link rel="stylesheet" type="text/css" href="{{ asset('css/magnific-popup.css') }}">
@if($user->background_image)
<style type="text/css">
	.header-image{
		background-size: cover;
		background-repeat: no-repeat;
		background-position: center center;
		background-image: url({{ url("uploads/users/".$user->id."/backgrounds/".$user->background_image) }});
	}
</style>
@else
<style type="text/css">
	.header-image{
		background-size: cover;
		background-repeat: no-repeat;
		background-position: center center;
		background-image: url({{ asset("uploads/default/user/all/background.png") }});
	}
</style>
@endif
<script type="text/javascript">
	var username = "{!! $user->username !!}";
</script>
@endsection

@section('content')
<section class="profile">
	@if(Auth::id() == $user->id)
	<button class="button edit-bg-image" type="button" id="add-background-image">Edit Background Image</button>
	@endif
	<div class="header-image">
		<div class="header-content">
			<div class="row">
				<div class="small-4 columns float-right cursor-ptr" data-open="view-all-follows">
					<div class="user-follows">
						<div class="user-follows-text">
							<span id="total-followers" class="round-to-k" data-total="{{ $user->numberOfFollowers }}">{{ $user->numberOfFollowers }}</span>
							<span>Followers</span>
						</div>
					</div>
					<div class="user-follows">
						<div class="user-follows-text">
							<span id="total-following" class="round-to-k" data-total="{{ $user->numberOfFollowing }}">{{ $user->numberOfFollowing }}</span>
							<span>Following</span>
						</div>
					</div>

					<div class="reveal-following-followers reveal" id="view-all-follows" data-reveal>
						<div class="reveal-gray-ribbon no-padding">
							<div class="title">
								<ul class="tabs profile-following-followers-tabs" data-tabs id="followers-following-tabs">
									<li class="tabs-title is-active">
										<a href="#following">Following <span class="highlight round-to-k" data-total="{{ $user->numberOfFollowing }}">{{ $user->numberOfFollowing }}</span></a>
									</li>
									<li class="tabs-title">
										<a href="#followers" aria-selected="true">Followers <span class="highlight round-to-k" data-total="{{ $user->numberOfFollowers }}">{{ $user->numberOfFollowers }}</span></a>
									</li>
								</ul>
							</div>
							<button class="close-button profile-following-followers-close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
						</div>
						<div class="tabs-content no-border" data-tabs-content="followers-following-tabs">
							<div class="tabs-panel is-active" id="following">
								<div class="row small-up-6" id="profile-following-container">
									@foreach($user->following as $followable)
									@include('user.followable_user_popup_template')
									@endforeach
								</div>
								@if($user->following->hasMorePages())
								<a class="float-right" id="profile-following-view-more" data-request-url="{{ route('user.following', $user) }}" data-type="following" data-next-page="{{ $user->following->currentPage()+1 }}" data-last-page="{{ $user->following->lastPage() }}" data-left-off="{{ $user->following->last()->id }}">View more</a>
								@endif
							</div>

							<div class="tabs-panel" id="followers">
								<div class="row small-up-6" id="profile-followers-container">
									@foreach($user->followers as $followable)
									@include('user.followable_user_popup_template')
									@endforeach
								</div>
								@if($user->followers->hasMorePages())
								<a class="float-right" id="profile-followers-view-more" data-request-url="{{ route('user.followers', $user) }}" data-type="followers" data-next-page="{{ $user->followers->currentPage()+1 }}" data-last-page="{{ $user->followers->lastPage() }}" data-left-off="{{ $user->followers->last()->id }}">View more</a>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="small-7 columns small-centered text-center">
					<img class="avatar" src="{{ url('images/small133/'.$user->avatarFullPath) }}" alt="">
					@if(Auth::id() == $user->id)
					<button class="button edit-profile-image" id="add-profile-image">+
					</button>
					@endif
					<p class="full-name">{{ $user->name }}</p>
					<p>{{ '@'.$user->username }}</p>
					<p class="status" id="editable-user-status">{{ $user->status }}</p>
					@if(Auth::id() == $user->id)
					<a class="fa-stack edit-status">
						<i class="fa fa-circle fa-stack-2x"></i>
						<i class="fa fa-edit fa-stack-1x fa-inverse" id="edit-save-status-button"></i>
					</a>
					@endif
					@if(Auth::check() && $user->id !== Auth::id())
					<div class="text-center">
						<ul class="dropdown menu dib" data-dropdown-menu data-alignment="right" data-disable-hover="true" data-click-open="true">
							<li>
								<a href="#" class="fa fa-cog settings"></a>
								<ul class="menu">
									<li><a data-open="report-user-reveal">Report</a></li>
								</ul>
							</li>
						</ul>
						<div class="reveal report-page-user-reveal" id="report-user-reveal" data-reveal>
							<h4>Report this user</h4>
							<form action="{{ route('report.user', $user) }}" method="POST" name="reportUser">
								{{ csrf_field() }}
								<div class="row">
									<div class="small-12 columns">
										<label>
											Comments:
											<textarea id="report-comments" name="comments" rows="4"></textarea>
										</label>
									</div>
								</div>
								<div class="row">
									<div class="small-12 columns">
										<input id="report-user-submit" type="submit" class="button" value="Report">
									</div>
								</div>
							</form>
							<button class="close-button" data-close aria-label="Close modal" type="button">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						@if(Auth::user()->isFollowerOfTheUser($user))
						<button class="follow-user-button" type="button" data-post-url="{{ Request::url() }}">Following</button>
						@else
						<button class="follow-user-button" type="button" data-post-url="{{ Request::url() }}">Follow</button>
						@endif
					</div>
					@endif
				</div>
			</div>
		</div>
		<div class="overlay"></div>
	</div>
	<div class="profile-tabs">
		<div class="row">
			<div class="small-12 columns">
				<ul class="tabs row small-up-4" data-tabs id="profile-tabs">
					<li class="tabs-title column {{ ($user->numberOfAttendingEvents) ? '' : 'tab-disabled' }}">
						<a href="#attending">Attending | <span class="round-to-k" data-total="{{ $user->numberOfAttendingEvents }}">{{ $user->numberOfAttendingEvents }}</span></a>
					</li>
					<li class="tabs-title column {{ ($user->numberOfAttendedEvents) ? '' : 'tab-disabled' }}">
						<a href="#history">History | <span class="round-to-k" data-total="{{ $user->numberOfAttendedEvents }}">{{ $user->numberOfAttendedEvents }}</span></a>
					</li>
					<li class="tabs-title column {{ ($user->numberOfLikedHistoryEventPhotos) ? '' : 'tab-disabled' }}">
						<a href="#favorites">Favorites | <span class="round-to-k" data-total="{{ $user->numberOfLikedHistoryEventPhotos }}">{{ $user->numberOfLikedHistoryEventPhotos }}</span></a>
					</li>
					<li class="tabs-title column {{ ($user->numberOfFollowingPages) ? '' : 'tab-disabled' }}">
						<a href="#pages">Pages | <span class="round-to-k" data-total="{{ $user->numberOfFollowingPages }}">{{ $user->numberOfFollowingPages }}</span></a>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="small-12 columns small-centered">
			<div class="profile-tabs-content" data-tabs-content="profile-tabs">

				<div class="profile-tabs-panel events" id="attending">
					@if(count($user->attendingEvents) > 0)
					<div id="profile-attending-events">
						<?php
						$events = $user->attendingEvents;
						$eventMode = 'upcoming';
						?>
						@foreach($events as $event)
						@include('events.event_row_template')
						@endforeach
					</div>
					@if($events->hasMorePages())
					<button class="profile-attending-load-more load-more" data-event-type="attending" data-request-url="{{ route('user.events.attending', $user) }}" data-next-page="{{ $events->currentPage()+1 }}" data-last-page="{{ $events->lastPage() }}" data-left-off="{{ $events->last()->id }}"><span class="fa fa-refresh"></span>Load more</button>
					@endif
					@endif
				</div>

				<div class="profile-tabs-panel events" id="history">
					@if(count($user->attendedEvents) > 0)
					<div id="profile-attended-events">
						<?php
						$events = $user->attendedEvents;
						$eventMode = 'history';
						?>
						@foreach($events as $event)
						@include('events.event_row_template')
						@endforeach
					</div>
					@if($events->hasMorePages())
					<button class="profile-attended-load-more load-more" data-event-type="attended" data-request-url="{{ route('user.events.attended', $user) }}" data-next-page="{{ $events->currentPage()+1 }}" data-last-page="{{ $events->lastPage() }}" data-left-off="{{ $events->last()->id }}"><span class="fa fa-refresh"></span>Load more</button>
					@endif
					@endif
				</div>

				<div class="profile-tabs-panel favorites" id="favorites">
					<div class="row small-up-4" id="favorites-container">
						@foreach($user->favorites as $favorite)
						@include('user.favorite_template')
						@endforeach
					</div>
					@if($user->favorites->hasMorePages())
					<p class="text-center {{ ($user->favorites->hasMorePages()) ? '' : 'hide' }}">
						<button id="profile-favorites-load-more" type="button" class="button load-more-button"  data-request-url="{{ route('user.favorites', $user) }}" data-next-page="{{ $user->favorites->currentPage()+1 }}" data-last-page="{{ $user->favorites->lastPage() }}" data-left-off="{{ $user->favorites->last()->id }}">Load more</button>
					</p>
					@endif
				</div>

				<div class="profile-tabs-panel pages" id="pages">
					<div id="profile-pages-container">
						@if(count($user->followingPages) > 0)
						@foreach($user->followingPages as $page)
						@include('pages.pages_list_template')
						@endforeach
						@endif
					</div>
					@if($user->followingPages->hasMorePages())
					<button id="profile-pages-load-more" class="load-more" data-request-url="{{ route('user.followingPages', $user) }}" data-next-page="{{ $user->followingPages->currentPage()+1 }}" data-last-page="{{ $user->followingPages->lastPage() }}" data-left-off="{{ $user->followingPages->last()->id }}"><span class="fa fa-refresh"></span>Load more</button>
					@endif
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

@section('scripts')
@if($user->id == Auth::id())
<script type="text/javascript" src="{{ asset('js/dropzone.min.js') }}"></script>
<script type="text/javascript">
	Dropzone.autoDiscover = false;
	var bgImage = new Dropzone('#add-background-image', {
		url: "<?php echo route('user.profile.backgroundImage', $user->username); ?>",
		paramName: "profileBackgroundImage",
		maxFiles: 1,
		acceptedFiles: "image/*",
		previewTemplate : '<div style="display:none"></div>',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		createImageThumbnails: false,
		init: function() {
			var myDropzone = this;
			this.on('success', function(file, response){
				myDropzone.removeAllFiles(true);
				$('.header-image').css("background-image", "url(/" + response + ")");
			});
			this.on('error', function(file, errorMessage){
				myDropzone.removeAllFiles(true);
			});
		}
	});


	var profileImage = new Dropzone('#add-profile-image', {
		url: "<?php echo route('user.profile.profileImage', $user->username); ?>",
		paramName: "profileImage",
		maxFiles: 1,
		acceptedFiles: "image/*",
		previewTemplate : '<div style="display:none"></div>',
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		createImageThumbnails: false,
		init: function() {
			var myDropzone = this;
			this.on('success', function(file, response){
				myDropzone.removeAllFiles(true);
				$('.avatar').attr('src', '/images/small133/' + response);
				$('.profile-picture > img').attr('src', '/images/small41/' + response);
			});
			this.on('error', function(file, errorMessage){
				myDropzone.removeAllFiles(true);
			});
		}
	});
</script>
@endif
<script type="text/javascript" src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.favorite-media-element').magnificPopup({
			type:'image',
			gallery: {
				enabled:true
			}
		});
	});
</script>
@endsection