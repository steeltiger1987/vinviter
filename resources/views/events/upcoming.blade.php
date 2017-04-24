@extends('layouts.master')

@section('title', $event->title)

@section('head')
<link rel="stylesheet" type="text/css" href="{{ asset('css/magnific-popup.css') }}">
@endsection

@section('content')
<section class="event">
	<div class="event-header">
		@if($event->is_private)
		<div class="row">
			<div class="small-3 columns float-right">
				<span class="private-icon"><i class="fa fa-lock"></i> Private</span>
			</div>
		</div>
		@endif
		<div class="row first-row">
			<div class="small-9 columns">
				<div class="event-image-container">
					<a href="{{ url('/uploads/'.$event->mainImageFullPath) }}" class="event-gallery-image">
						<img src="{{ url('images/small180/'.$event->mainImageFullPath) }}" alt="">
					</a>
				</div>
				<div class="float-left">
					<p class="title"><strong>{{ $event->title }}</strong></p>
					<p class="info-row"><i class="fa fa-calendar"></i>{{ strftime('%A, %d-%m-%Y', strtotime($event->starts_at)) }}</p>
					@if(!$event->is_location_hidden)
					@if($event->venuePage || $event->address || $event->zip_code || $event->city)
					<div class="info-row">
						<i class="fa fa-location-arrow vat float-left"></i>
						<div class="dib">
							@if($event->venuePage)
							<p><a target="_blank" href="{{ route('pages.show', $event->venuePage->slug) }}">{{ $event->venuePage->name }}</a></p>
							@endif
							@if($event->address)
							<p>{{ $event->address }}</p>
							@endif
							
							<p>
								@if($event->zip_code)
								{{ $event->zip_code.', ' }}
								@endif
								{{ $event->city->name }}
							</p>
						</div>
					</div>
					@endif
					@endif
					<p class="info-row"><i class="fa fa-map-marker"></i>{{ $event->region->name.', '.$event->country->name }}</p>
				</div>
			</div>
			<div class="small-3 columns">
				<div class="event-attendees float-right">
					<div class="row">
						@if(Auth::check())
						<div class="small-5 columns no-pd-rgt">
							@if(Auth::user()->isAttendingTheEvent($event))
							<button class="attend-event attend-button-active" data-id="{{ $event->id }}">I'm going <span class="fa fa-check-circle"></span></button>
							@else
							<button class="attend-event attend-button" data-id="{{ $event->id }}">Attend</button>
							@endif
						</div>
						@endif
						<div class="small-7 columns">
							<button class="number-of-attendees attend-button" data-open="reveal-attendance"><span class="round-to-k" data-total="{{ $event->numberOfAttendees }}">{{ $event->numberOfAttendees }}</span> attending</button>

							<div class="reveal-event-attendance reveal" id="reveal-attendance" data-reveal>
								<div class="row small-up-6" id="event-attendees-container">
									@foreach($event->attendees as $attendee)
									@include('events.event_attendee_popup_template')
									@endforeach
								</div>
								@if($event->attendees->hasMorePages())
								<a class="float-right" id="event-attendees-view-more" data-event-id="{{ $event->id }}" data-next-page="{{ $event->attendees->currentPage()+1 }}" data-last-page="{{ $event->attendees->lastPage() }}">View more</a>
								@endif
								<div class="reveal-gray-ribbon">
									<div class="title"><span class="highlight round-to-k" data-total="{{ $event->numberOfAttendees }}">{{ $event->numberOfAttendees }}</span> Attending</div>
									<button class="close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
								</div>
							</div>
						</div>
					</div>
					<div class="row small-up-3 avatars">
						@foreach($event->attendees->take(6) as $attendee)
						<div class="column" data-id="{{ $attendee->id }}"><a target="_blank" href="{{ route('user.profile', $attendee->username) }}"><img data-tooltip aria-haspopup="true" class="has-tip" title="{{ $attendee->name }}" src="{{ url('images/small59/'.$attendee->avatarFullPath) }}" alt=""></a></div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="small-8 columns">
				@if($secondaryImages)
				@foreach($secondaryImages as $image)
				<a class="event-gallery-image" href="{{ asset('uploads/events/'.$event->id.'/images/'.$image->name) }}"><img class="event-secondary-images" src="{{ url('images/small50/events/'.$event->id.'/images/'.$image->name) }}"></a>
				@endforeach
				@endif
			</div>
			<div class="small-4 columns float-right">
				@if($event->creatorPage)
				<?php $creatorPage = $event->creatorPage; ?>
				<p class="event-created-by">
					<span>Created by: </span>
					<a target="_blank" href="{{ route('pages.show', $creatorPage->slug) }}">{{ $creatorPage->name }}</a>
					<img src="{{ url('images/small50/'.$creatorPage->mainImageFullPath) }}">
				</p>
				@endif
			</div>
		</div>
	</div>
	<div class="event-tabs">
		<div class="row">
			<div class="small-12 columns">
				<ul class="tabs" data-tabs id="event-tabs">
					<li class="tabs-title is-active"><a href="#full-info">Full Info</a></li>
					<li class="tabs-title"><a href="#comments">Comments | {{ $event->numberOfComments }}</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="small-11 columns small-centered">
			<div class="event-tabs-content" data-tabs-content="event-tabs">
				<div class="event-tabs-panel is-active" id="full-info">
					<?php $attributes = $event->attributes->groupBy('type'); ?>
					<dl>
						@if(isset($attributes['event.type']))
						<dt>Type</dt>
						<dd>{{ $attributes['event.type'][0]->name }}</dd>
						@endif

						<dt>Starting Time</dt>
						<dd>{{ strftime('%B %d, %Y %H:%M', strtotime($event->starts_at)) }}</dd>

						<dt>Ending Time</dt>
						<dd>{{ strftime('%B %d, %Y %H:%M', strtotime($event->ends_at)) }}</dd>

						@if(isset($attributes['event.age_limit']))
						<dt>Age Limit</dt>
						<dd>{{ $attributes['event.age_limit'][0]->name }}</dd>
						@endif

						@if(isset($attributes['event.dress_code']))
						<dt>Dress Code</dt>
						<dd>{{ $attributes['event.dress_code'][0]->name }}</dd>
						@endif

						@if(isset($attributes['event.activity_period']))
						<dt>Activity Period</dt>
						<dd>{{ $attributes['event.activity_period'][0]->name }}</dd>
						@endif

						@if(isset($attributes['event.music']))
						<dt>Music</dt>
						<dd>{{ $attributes['event.music'][0]->name }}</dd>
						@endif

						@if(isset($attributes['event.entrance']))
						<dt>Entrance</dt>
						<dd>{{ $attributes['event.entrance'][0]->name }}</dd>
						@endif

						<dt>Details</dt>
						<dd>{!! nl2br(e($event->details)) !!}</dd>
					</dl>
				</div>
				<div class="event-tabs-panel" id="comments">
					@include('events.comments')
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script type="text/javascript" src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.event-gallery-image').magnificPopup({
			type:'image',
			gallery: {
				enabled:true
			}
		});
	});
</script>
@endsection