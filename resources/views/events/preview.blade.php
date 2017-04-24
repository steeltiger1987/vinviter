@extends('layouts.master')

@section('title', 'Preview of your event')

@section('head')
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
		<div class="row">
			<div class="small-12 columns">
				<div class="event-image-container">
					<img src="{{ url('images/small180/'.$event->mainImageFullPath) }}" alt="">
				</div>
				<div class="float-left">
					<p class="title"><strong>{{ $event->title }}</strong></p>
					<p class="info-row"><i class="fa fa-calendar"></i>{{ $event->starts_at->format('l, d-m-Y') }}</p>
					@if(!$event->is_location_hidden)
					@if($event->venuePage || $event->address || $event->zip_code)
					<div class="info-row">
						<i class="fa fa-location-arrow vat float-left"></i>
						<div class="dib">
						@if($event->venuePage)
							<p><a target="_blank" href="{{ route('pages.show', $event->venuePage->slug) }}">{{ $event->venuePage->name }}</a></p>
						@endif
						@if($event->address)
							<p>{{ $event->address }}</p>
						@endif
						@if($event->zip_code)
							<p>{{ $event->zip_code }}</p>
						@endif
						</div>
					</div>
					@endif
					@endif
					<p class="info-row"><i class="fa fa-map-marker"></i>{{ $event->city->name.', '.$event->country->name }}</p>
				</div>
			</div>
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
	<div class="event-tabs">
		<div class="row">
			<div class="small-12 columns">
				<ul class="tabs" data-tabs id="event-tabs">
					<li class="tabs-title is-active"><a href="#full-info">Full Info</a></li>
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
						<dd>{{ $event->starts_at->format('F d, Y H:i') }}</dd>

						<dt>Ending Time</dt>
						<dd>{{ $event->ends_at->format('F d, Y H:i') }}</dd>

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
			</div>
		</div>
		<form name="preview" action="{{ route('events.publish', $event->id) }}" method="POST">
			{!! csrf_field() !!}
			<div class="small-4 columns float-right">
				<div class="row small-up-2">
					<div class="column">
						<a href="{{ route('events.edit', $event->id) }}" title="Go back to the form" class="button back expanded">Back</a>
					</div>
					<div class="column">
						<input type="hidden" name="id" value="{{ $event->id }}">
						<input type="submit" name="publish" class="button yellow expanded" title="Publish the event" value="Publish">
					</div>
				</div>
			</div>
		</form>
	</div>
</section>
@endsection