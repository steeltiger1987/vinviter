@extends('layouts.master')

@section('title', $event->title)

@section('head')
<link rel="stylesheet" type="text/css" href="{{ asset('css/dropzone.min.css') }}">
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
					<a href="{{ url('uploads/'.$event->mainImageFullPath) }}" class="event-gallery-image">
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
							@if(Auth::user()->hasAttendedTheEvent($event))
							<button class="attend-event attend-button-active" data-id="{{ $event->id }}" data-type="history">I was there <span class="fa fa-check-circle"></span></button>
							@else
							<button class="attend-event attend-button" data-id="{{ $event->id }}" data-type="history">I was there</button>
							@endif
						</div>
						@endif
						<div class="small-7 columns">
							<button class="number-of-attendees attend-button" data-open="reveal-attendance"><span class="round-to-k" data-total="{{ $event->numberOfAttendees }}">{{ $event->numberOfAttendees }}</span> attended</button>

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
									<div class="title"><span class="highlight round-to-k" data-total="{{ $event->numberOfAttendees }}">{{ $event->numberOfAttendees }}</span> Attended</div>
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
					<li class="tabs-title is-active"><a href="#comments">Comments | <span class="round-to-k" data-total="{{ $event->numberOfComments }}">{{ $event->numberOfComments }}</span></a></li>
					<li class="tabs-title {{ (!$event->numberOfHistoryPhotos && !($isCreatorOfTheEvent || $isAdminOfTheEvent)) ? 'tab-disabled' : '' }}"><a href="#photos">Photos | <span class="round-to-k" data-total="{{ $event->numberOfHistoryPhotos }}">{{ $event->numberOfHistoryPhotos }}</span></a></li>
					<li class="tabs-title"><a href="#full-info">Full Info</a></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="small-11 columns small-centered">
			<div class="event-tabs-content" data-tabs-content="event-tabs">
				<div class="event-tabs-panel is-active" id="comments">
					@include('events.comments')
				</div>
				<div class="event-tabs-panel event-photos" id="photos">
					@if(Auth::check() && ($isCreatorOfTheEvent || $isAdminOfTheEvent))
					<span>Click on the button on the right, to add as many photos you can on your history event.</span>
					<button class="button float-right add-photo-button" data-open="add-event-photo">Add Photo</button>

					<div class="reveal event-add-photo-modal" id="add-event-photo" data-reveal>
						<h4 class="text-center">Add Photo</h4>
						<p class="text-center"><button class="button upload-photo-button">Upload</button></p>
						<div id="history-event-photos-preview" class="dropzone text-center"></div>
						<p id="publish-photos-buttons" class="hide">
							<button id="publish-history-photos" class="button float-right publish-button">Publish</button>
							<button class="button float-right" data-close>Cancel</button>
						</p>
						<div class="reveal-gray-ribbon">
							<div class="title">Add Photo</div>
							<button class="close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
						</div>
					</div>
					@endif

					<div class="row small-up-4" id="history-event-photos-container" data-event-id="{{ $event->id }}">
						@foreach($event->historyPhotos as $photo)
						@include('events.history_photo_template')
						@endforeach
					</div>
					<p class="text-center {{ ($event->historyPhotos->hasMorePages()) ? '' : 'hide' }}"><button type="button" class="button load-more-button"  data-event-id="{{ $event->id }}" data-next-page="{{ $event->historyPhotos->currentPage()+1 }}" data-last-page="{{ $event->historyPhotos->lastPage() }}">Load more</button></p>
				</div>
				<div class="event-tabs-panel" id="full-info">
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
			</div>
		</div>
	</div>

</section>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/dropzone.min.js') }}"></script>
<script type="text/javascript">
	var publishHistoryPhotosURL = "{{ route('events.publishHistoryPhotos', $event->id) }}";
	var publishPhotosButtons = $('#publish-photos-buttons');

	Dropzone.autoDiscover = false;
	var eventPhotos = new Dropzone('.upload-photo-button', {
		url: "<?php echo route('events.uploadHistoryPhoto', $event->id); ?>",
		paramName: "historyEventPhoto",
		maxFiles: 12,
		acceptedFiles: "image/*",
		previewsContainer : '#history-event-photos-preview',
		addRemoveLinks: true,
		thumbnailHeight: null,
		maxFilesize: 10,
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		init: function() {
			var myDropzone = this;

			this.on('success', function(file, response){
				$('.dz-filename > span:contains("' + file.name + '")').html(response);
				file.newFileName = response;

				if(publishPhotosButtons.hasClass('hide')){
					publishPhotosButtons.removeClass('hide');
				}
			});

			this.on('error', function(file, errorMessage){
				this.removeFile(file);
				if(typeof errorMessage === 'string'){
					alert(errorMessage);
				}
			});

			this.on('removedfile', function(file){
				if(file.status !== "error"){
					$.ajax({
						type: "DELETE",
						url: "<?php echo route('events.deleteHistoryPhoto', $event->id); ?>" + '/?image=' + file.newFileName,
						success: function(data){

						}
					});
				}
				if($('#history-event-photos-preview').children().length == 0){
					publishPhotosButtons.addClass('hide');
				}
			});
		}
	});
</script>

<script type="text/javascript" src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.event-gallery-image').magnificPopup({
			type:'image',
			gallery: {
				enabled:true
			}
		});
		$('.history-event-photo').magnificPopup({
			type:'image',
			gallery: {
				enabled:true
			}
		});
	});
</script>
@endsection