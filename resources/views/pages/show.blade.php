@extends('layouts.master')

@section('title', $page->name)

@section('head')
@if($page->background_image)
<style type="text/css">
	.header-image{
		background-size: cover;
		background-repeat: no-repeat;
		background-position: center center;
		background-image: url({{ url("uploads/pages/".$page->id.'/images/'.$page->background_image) }});
	}
</style>
@else
<style type="text/css">
	.header-image{
		background-size: cover;
		background-repeat: no-repeat;
		background-position: center center;
		background-image: url({{ asset("uploads/default/page/all/background.png") }});
	}
</style>
@endif
@endsection

@section('content')
<section class="page">
	<div class="header-image">
		<div class="header-content">
			<div class="row">
				@if(Auth::check())
				<div class="small-6 columns page-notifications" {!! ($isUserFollowerOfThisPage) ? '' : 'style="display:none"' !!}>
					<span class="fa fa-bell-o bell-icon"></span>
					<div class="switch small">
						@if($userPageSettings->receive_page_notifications)
						<input class="switch-input" id="page-notifications-switch" type="checkbox" name="page_notifications" data-notifications-url="{{ route('user.postPageNotifications', [Auth::user()->username, $page->slug]) }}" checked>
						@else
						<input class="switch-input" id="page-notifications-switch" type="checkbox" name="page_notifications" data-notifications-url="{{ route('user.postPageNotifications', [Auth::user()->username, $page->slug]) }}">
						@endif
						<label class="switch-paddle" for="page-notifications-switch">
							<span class="show-for-sr">Notifications</span>
							<span class="switch-active" aria-hidden="true">On</span>
							<span class="switch-inactive" aria-hidden="true">Off</span>
						</label>
					</div>
					<span class="fa fa-question-circle-o help-icon has-tip" data-tooltip data-disable-hover='false' title="Switch to receive notifications from this page"></span>
				</div>
				@else
				<div class="small-6 columns"></div>
				@endif
				<div class="small-6 columns">

					@if($page->numberOfFollowers > 0)
					<div class="followers">
						<div class="followers-text" data-open="reveal-page-followers">
							<span class="page-total-followers round-to-k" data-total="{{ $page->numberOfFollowers }}">{{ $page->numberOfFollowers }}</span>
							<span>Followers</span>
						</div>
					</div>

					<div class="reveal-following-followers reveal" id="reveal-page-followers" data-reveal>
						<div class="row small-up-6" id="page-followers-container">
							@foreach($page->followers as $follower)
							@include('pages.page_follower_popup_template')
							@endforeach
						</div>
						@if($page->followers->hasMorePages())
						<a class="float-right" id="page-followers-view-more" data-page-slug="{{ $page->slug }}" data-next-page="{{ $page->followers->currentPage()+1 }}" data-last-page="{{ $page->followers->lastPage() }}" data-left-off="{{ $page->followers->last()->id }}">View more</a>
						@endif
						<div class="reveal-gray-ribbon">
							<div class="title"><span class="highlight round-to-k" data-total="{{ $page->numberOfFollowers }}">{{ $page->numberOfFollowers }}</span> Followers</div>
							<button class="close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
						</div>
					</div>

					@else
					<div class="followers">
						<div class="followers-text">
							<span class="page-total-followers round-to-k" data-total="{{ $page->numberOfFollowers }}">{{ $page->numberOfFollowers }}</span>
							<span>Followers</span>
						</div>
					</div>
					@endif
				</div>
			</div>
			<div class="row">
				<div class="small-7 columns small-centered">
					<img class="main-image" src="{{ url('images/small133/'.$page->mainImageFullPath) }}" alt="">

					<p><strong>{{ $page->name }}</strong></p>
					<p>{{ '@'.$page->slug }}</p>
					<p class="status">{{ $page->status }}</p>
					<div class="row mb-2em">
						@if(!$page->address)
						<div class="small-12 columns">
							<span class="db text-center">
								<i class="fa fa-map-marker"></i>
								{{ $page->city->name.', '.$page->country->name }}
							</span>
						</div>
						@else
						<div class="small-5 small-offset-2 columns">
							<span>
								<i class="fa fa-location-arrow"></i>
								{{ $page->address }}
							</span>
						</div>
						<div class="small-5 columns">
							<span>
								<i class="fa fa-map-marker"></i>
								{{ $page->city->name.', '.$page->country->name }}
							</span>
						</div>
						@endif
					</div>
					@if(Auth::check())
					<div class="text-center">
						<ul class="dropdown menu dib" data-dropdown-menu data-alignment="right" data-disable-hover="true" data-click-open="true">
							<li>
								<a href="#" class="fa fa-cog settings-page"></a>
								<ul class="menu" id="page-action-menu">
									<li><a data-open="report-page-reveal">Report</a></li>
{{-- 									@if($userPageSettings->is_page_blocked)
									<li><a id="page-block" class="text-red" data-page-block-url="{{ route('user.postBlockPage', [Auth::user()->username, $page->slug]) }}" data-is-blocked="true">Unblock</a></li>
									@else
									<li><a id="page-block" class="text-red" data-page-block-url="{{ route('user.postBlockPage', [Auth::user()->username, $page->slug]) }}" data-is-blocked="false">Block</a></li>
									@endif --}}
								</ul>
							</li>
						</ul>
						<div class="reveal report-page-user-reveal" id="report-page-reveal" data-reveal>
							<h4>Report this page</h4>
							<form action="{{ route('report.page', $page) }}" method="POST" name="reportPage">
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
										<input id="report-page-submit" type="submit" class="button" value="Report">
									</div>
								</div>
							</form>
							<button class="close-button" data-close aria-label="Close modal" type="button">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>

						@if($isUserFollowerOfThisPage)
						<button class="follow-page-button follow-page-trigger" type="button" data-post-url="{{ Request::url() }}">Following</button>
						@else
						<button class="follow-page-button follow-page-trigger" type="button" data-post-url="{{ Request::url() }}">Follow</button>
						@endif
					</div>
					@endif
				</div>
			</div>
		</div>
		<div class="overlay"></div>
	</div>
	<div class="page-tabs">
		<div class="row">
			<div class="small-12 columns">
				<ul class="tabs" data-tabs id="page-tabs">
					<li class="tabs-title is-active"><a href="#full-info">Full Info</a></li>
					@if($typeInfo == 'type_venue')
					<li class="tabs-title {{ ($page->$upcomingEvents->count()) ? '' : 'tab-disabled'}}"><a href="#upcoming">Upcoming | <span class="round-to-k" data-total="{{ $page->numberOfVenueUpcomingEvents }}">{{ $page->numberOfVenueUpcomingEvents }}</span></a></li>
					<li class="tabs-title {{ ($page->$historyEvents->count()) ? '' : 'tab-disabled'}}"><a href="#history">History | <span class="round-to-k" data-total="{{ $page->numberOfVenueHistoryEvents }}">{{ $page->numberOfVenueHistoryEvents }}</span></a></li>
					@else
					<li class="tabs-title {{ ($page->$upcomingEvents->count()) ? '' : 'tab-disabled'}}"><a href="#upcoming">Upcoming | <span class="round-to-k" data-total="{{ $page->numberOfCreatorUpcomingEvents }}">{{ $page->numberOfCreatorUpcomingEvents }}</span></a></li>
					<li class="tabs-title {{ ($page->$historyEvents->count()) ? '' : 'tab-disabled'}}"><a href="#history">History | <span class="round-to-k" data-total="{{ $page->numberOfCreatorHistoryEvents }}">{{ $page->numberOfCreatorHistoryEvents }}</span></a></li>
					@endif
				</ul>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="small-11 columns small-centered">
			<div class="page-tabs-content" data-tabs-content="page-tabs">
				<div class="page-tabs-panel is-active" id="full-info">
					<?php $attributes = $page->attributes->groupBy('type'); ?>
					<dl>
						@if(isset($attributes['page.type']))
						<dt>Type</dt>
						<dd>{{ $attributes['page.type'][0]->name }}</dd>
						@endif

						@if(isset($attributes['page.year']))
						<dt>Year Founded</dt>
						<dd>{{ $attributes['page.year'][0]->name }}</dd>
						@endif

						@if(isset($attributes['page.activity_period']))
						<dt>Activity Period</dt>
						<dd>{{ $attributes['page.activity_period'][0]->name }}</dd>
						@endif

						@if(isset($attributes['page.season']))
						<dt>Season</dt>
						<dd>{{ $attributes['page.season'][0]->name }}</dd>
						@endif

						@if(count($page->keyPeople))
						<dt>Key People</dt>
						<dd>
							@foreach($page->keyPeople as $key => $person)
							<a href="{{ route('user.profile', $person->username) }}" target="_blank" class="key-people-list">{{ $person->name }}</a><span></span>
							@endforeach
						</dd>
						@endif

						<dt>Story</dt>
						<dd>{!! nl2br(e($page->story)) !!}</dd>
					</dl>
				</div>
				<div class="page-tabs-panel" id="upcoming">
					<div class="row">
						<div class="small-12 columns events">
							<?php $eventMode = 'upcoming'; ?>
							@forelse($page->$upcomingEvents as $event)
							@include('events.event_row_template')
							@empty
							<p>No events found.</p>
							@endforelse
						</div>
					</div>
					@if($page->$upcomingEvents->hasMorePages())
					<div class="row">
						<div class="small-12 columns">
							<button class="page-events-load-more load-more" data-event-type="{{ $eventMode }}" data-next-page="{{ $page->$upcomingEvents->currentPage()+1 }}" data-last-page="{{ $page->$upcomingEvents->lastPage() }}" data-request-url="{{ route('pages.events.upcoming', $page) }}"><span class="fa fa-refresh"></span>Load more</button>
						</div>
					</div>
					@endif
				</div>

				<div class="page-tabs-panel" id="history">
					<div class="row">
						<div class="small-12 columns events">
							<?php $eventMode = 'history'; ?>
							@forelse($page->$historyEvents as $event)
							@include('events.event_row_template')
							@empty
							<p>No events found.</p>
							@endforelse
						</div>
					</div>
					@if($page->$historyEvents->hasMorePages())
					<div class="row">
						<div class="small-12 columns">
							<button class="page-events-load-more load-more" data-event-type="{{ $eventMode }}" data-next-page="{{ $page->$historyEvents->currentPage()+1 }}" data-last-page="{{ $page->$historyEvents->lastPage() }}" data-request-url="{{ route('pages.events.history', $page) }}"><span class="fa fa-refresh"></span>Load more</button>
						</div>
					</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</section>
@endsection