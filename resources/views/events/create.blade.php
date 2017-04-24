@extends('layouts.master')

@section('title', 'Create a new event')
@section('head')
<link rel="stylesheet" type="text/css" href="{{ asset('css/chosen.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/dropzone.min.css') }}">
@endsection
@section('content')
<section class="create-event">
	<div class="heading-block">
		<div class="row">
			<div class="small-10 small-centered columns">
				<h3 class="heading">Events</h3>
				<div class="description">
					<h5>Create any type of event, ranging from common parties to major festivals.</h5>
					<h5>Choose to make event public or private.</h5>
				</div>
			</div>
		</div>
	</div>
	<form action="{{ route('events.store') }}" name="createNewEvent" method="POST" enctype="multipart/form-data">
		{!! csrf_field() !!}
		<input type="hidden" name="formID" value="{{ old('formID', str_random(10)) }}">
		<input type="hidden" name="mainImage">
		<div class="row content-block">
			<div class="small-11 small-centered columns">
				<div class="row">
					<div class="small-6 columns">
						<div class="row">
							<div class="small-4 columns">
								<label for="title">Title<span class="required">*</span>:</label>
							</div>
							<div class="small-7 float-left columns">
								<input id="title" type="text" name="title" value="{{ old('title') }}">
								{!! $errors->first('title', '<span class="form-error is-visible">:message</span>') !!}
							</div>
						</div>
						<div class="row">
							<div class="small-4 columns">
								<label for="visibility">Visibility<span class="required">*</span>:</label>
							</div>
							<div class="small-7 columns">
								<select id="visibility" name="visibility">
									<option value="">Select...</option>
									<option value="0" {{ (old('visibility') == '0') ? 'selected' : '' }}>Public</option>
									<option value="1" {{ (old('visibility') == '1') ? 'selected' : '' }}>Private</option>
								</select>
								{!! $errors->first('visibility', '<span class="form-error is-visible">:message</span>') !!}
							</div>
							<div class="small-1 columns">
								<span class="info-icon fa fa-question-circle has-tip" data-tooltip data-disable-hover='false' title="Select the visibility of your event. Public: (all users and visitors) Private: (Only invited users and Admins)"></span>
							</div>
						</div>
						
						<div class="row">
							<div class="small-4 columns">
								<label for="type">Type<span class="required">*</span>:</label>
							</div>
							<div class="small-7 float-left columns">
								<select id="type" name="type">
									<option value="">Select...</option>
									@foreach($attributes['event.type'] as $row)
									<option value="{{ $row->id }}" {{ ($row->id == old('type')) ? 'selected' : '' }}>{{ $row->name }}</option>
									@endforeach
									@foreach($attributes['event.type_group'] as $type_group)
									<optgroup label="{{ $type_group->name }}">
										@foreach($type_group->children as $type)
										<option value="{{ $type->id }}" {{ ($type->id == old('type')) ? 'selected' : '' }}>{{ $type->name }}</option>
										@endforeach
									</optgroup>
									@endforeach
								</select>
								{!! $errors->first('type', '<span class="form-error is-visible">:message</span>') !!}
							</div>
						</div>

						<div class="row">
							<div class="small-4 columns">
								<label for="country">Country<span class="required">*</span>:</label>
							</div>
							<div class="small-7 float-left columns">
								<select id="country" name="country">
									<option value="">Select...</option>
									@foreach($countries as $country)
									<option value="{{ $country->id }}" {{ (old('country') == $country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
									@endforeach
								</select>
								{!! $errors->first('country', '<span class="form-error is-visible">:message</span>') !!}
							</div>
						</div>

						<div class="row" id="region-row">
							<div class="small-4 columns">
								<label for="region">Region<span class="required">*</span>:</label>
							</div>
							<div class="small-7 float-left columns">
								<select id="region" name="region">
									<option value="">Select...</option>
									@if(old('country') && count($regions) > 0))
									@foreach($regions as $region)
									<option value="{{ $region->id }}" {{ (old('region') == $region->id) ? 'selected' : '' }}>{{ $region->name }}</option>
									@endforeach
									@endif
								</select>
								{!! $errors->first('region', '<span class="form-error is-visible">:message</span>') !!}
							</div>
						</div>

						<div class="row">
							<div class="small-4 columns">
								<label for="city">City<span class="required">*</span>:</label>
							</div>
							<div class="small-7 float-left columns">
								<select id="city" name="city">
									<option value="">Select...</option>
									@if(old('region') && count($cities) > 0))
									@foreach($cities as $city)
									<option value="{{ $city->id }}" {{ (old('city') == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
									@endforeach
									@endif
								</select>
								{!! $errors->first('city', '<span class="form-error is-visible">:message</span>') !!}
							</div>
						</div>

						<div class="row">
							<div class="small-4 columns">
								<label for="address">Address:</label>
							</div>
							<div class="small-7 float-left columns">
								<input id="address" type="text" name="address" value="{{ old('address') }}">
								{!! $errors->first('address', '<span class="form-error is-visible">:message</span>') !!}
							</div>
						</div>
						<div class="row">
							<div class="small-4 columns">
								<label for="zip-code">Zip Code:</label>
							</div>
							<div class="small-7 float-left columns">
								<input id="zip-code" type="text" name="zip_code" value="{{ old('zip_code') }}">
								{!! $errors->first('zip_code', '<span class="form-error is-visible">:message</span>') !!}
							</div>
						</div>
						<div class="row">
							<div class="small-4 columns">
								<label for="hide-location">Hide Location:</label>
							</div>
							<div class="small-7 columns">
								<div class="switch large">
									<input class="switch-input" id="hide-location" type="checkbox" name="hide_location" {{ (old('hide_location')) ? 'checked' : '' }}>
									<label class="switch-paddle" for="hide-location">
										<span class="switch-active">Yes</span>
										<span class="switch-inactive">No</span>
									</label>
									{!! $errors->first('hide_location', '<p></p><span class="form-error is-visible">:message</span>') !!}
								</div>
							</div>
							<div class="small-1 columns">
								<span class="info-icon fa fa-question-circle has-tip" data-tooltip data-disable-hover='false' title="Decide to keep event location hidden, but remember to release the button on the preferred time."></span>
							</div>
						</div>
						<div class="row">
							<div class="small-4 columns">
								<label for="venue-page">Venue Page:</label>
							</div>
							<div class="small-7 columns">
								<select data-placeholder="Select..." name="venue_page" id="venue-page">
									@if($oldVenuePage)
									<option value="{{ $oldVenuePage->id }}" selected>{{ $oldVenuePage->name }}</option>
									@endif
								</select>
								{!! $errors->first('venue_page', '<span class="form-error is-visible">:message</span>') !!}
							</div>
							<div class="small-1 columns">
								<span class="info-icon fa fa-question-circle has-tip" data-tooltip data-disable-hover='false' title="Select The venue page you would like to link to this event. Example: Arena, Night Club, Lounge, Event Hall."></span>
							</div>
						</div>
						<div class="row">
							<div class="small-4 columns">
								<label for="creator-page">Organization Page:</label>
							</div>
							<div class="small-7 columns">
								<select data-placeholder="Select..." name="creator_page" id="creator-page">
									@if($oldCreatorPage)
									<option value="{{ $oldCreatorPage->id }}" selected>{{ $oldCreatorPage->name }}</option>
									@endif
								</select>
								{!! $errors->first('creator_page', '<span class="form-error is-visible">:message</span>') !!}
							</div>
							<div class="small-1 columns">
								<span class="info-icon fa fa-question-circle has-tip" data-tooltip data-disable-hover='false' title="Select The organization page you would like to link to this event. Example: Event Planner, Event Promoter."></span>
							</div>
						</div>
					</div>
					<div class="small-5 float-right columns">
						<div class="row">
							<div class="small-4 columns">
								<label for="ending-time">Timezone<span class="required">*</span>:</label>
							</div>
							<div class="small-8 columns">
								<select name="timezone">
									<option value="" selected default>Select timezone</option>
									@foreach($timezones as $timezone)
									<option value="{{ $timezone->php_timezone_identifier_name }}" {{ (old('timezone') == $timezone->php_timezone_identifier_name) ? 'selected' : '' }}>{{ $timezone->name }}</option>
									@endforeach
								</select>
								{!! $errors->first('timezone', '<span class="form-error is-visible">:message</span>') !!}
							</div>
						</div>
					</div>
					<div class="small-5 float-right columns">
						<div class="row">
							<div class="small-4 columns">
								<label for="starting-time">Starting Time<span class="required">*</span>:</label>
							</div>
							<div class="small-8 columns">
								<input id="starting-time" type="text" name="starting_time" value="{{ old('starting_time') }}">
								{!! $errors->first('starting_time', '<span class="form-error is-visible">:message</span>') !!}
							</div>
						</div>
					</div>
					<div class="small-5 float-right columns">
						<div class="row">
							<div class="small-4 columns">
								<label for="ending-time">Ending Time<span class="required">*</span>:</label>
							</div>
							<div class="small-8 columns">
								<input id="ending-time" type="text" name="ending_time" value="{{ old('ending_time') }}">
								{!! $errors->first('ending_time', '<span class="form-error is-visible">:message</span>') !!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="gray-block add-images">
			<div class="row">
				<div class="small-10 small-centered columns">
					<h4 class="heading-small text-center">Add Images
						<span class="info-icon fa fa-question-circle has-tip" data-tooltip data-disable-hover='false' title="Drag the image to the first position to make it the main image of the event"></span>
					</h4>

					<div class="description text-center">
						<h5 class="small">You can upload up to 4 images</h5>
					</div>
				</div>
			</div>
			<p class="text-center"><button type="button" class="button" id="add-images">Upload</button></p>
			<p><h6 class="text-center">Don’t have an image? Skip by using our Default one.</h6></p>
			<div class="row">
				<div class="small-9 small-centered columns text-center">
					<div class="dropzone dropzone-previews" id="previews-holder">
						{!! $errors->first('main_image', '<span class="form-error is-visible">:message</span>') !!}
					</div>
				</div>
			</div>
		</div>

		<div class="row content-block">
			<div class="small-11 small-centered columns">
				<div class="row">
					<div class="small-2 columns">
						<label for="details">Details<span class="required">*</span>:</label>
					</div>
					<div class="small-6 float-left columns">
						<textarea id="details" name="details" rows="8">{{ old('details') }}</textarea>
						{!! $errors->first('details', '<span class="form-error is-visible">:message</span>') !!}
					</div>
				</div>
			</div>
		</div>
		<div class="gray-block">
			<div class="row">
				<div class="small-10 small-centered columns">
					<h4 class="heading-small other-requirements">Other Requirements (Optional)</h4>
				</div>
			</div>
			<div class="row">
				<div class="small-4 columns">
					<div class="row">
						<div class="small-4 columns">
							<label for="entrance">Entrance:</label>
						</div>
						<div class="small-7 columns float-left">
							<select id="entrance" name="entrance">
								<option value="">Select...</option>
								@foreach($attributes['event.entrance'] as $row)
								<option value="{{ $row->id }}" {{ ($row->id == old('entrance')) ? 'selected' : '' }}>{{ $row->name }}</option>
								@endforeach
							</select>
							{!! $errors->first('entrance', '<span class="form-error is-visible">:message</span>') !!}
						</div>
					</div>
					<div class="row">
						<div class="small-4 columns">
							<label for="dress-code">Dress Code:</label>
						</div>
						<div class="small-7 columns float-left">
							<select id="dress-code" name="dress_code">
								<option value="">Select...</option>
								@foreach($attributes['event.dress_code'] as $row)
								<option value="{{ $row->id }}" {{ ($row->id == old('dress_code')) ? 'selected' : '' }}>{{ $row->name }}</option>
								@endforeach
							</select>
							{!! $errors->first('dress_code', '<span class="form-error is-visible">:message</span>') !!}
						</div>
					</div>
				</div>
				<div class="small-4 columns">
					<div class="row">
						<div class="small-4 columns">
							<label for="age-limit">Age Limit:</label>
						</div>
						<div class="small-7 columns float-left">
							<select id="age-limit" name="age_limit">
								<option value="">Select...</option>
								@foreach($attributes['event.age_limit'] as $row)
								<option value="{{ $row->id }}" {{ ($row->id == old('age_limit')) ? 'selected' : '' }}>{{ $row->name }}</option>
								@endforeach
							</select>
							{!! $errors->first('age_limit', '<span class="form-error is-visible">:message</span>') !!}
						</div>
					</div>
					<div class="row">
						<div class="small-4 columns">
							<label for="music">Music:</label>
						</div>
						<div class="small-7 columns float-left">
							<select id="music" name="music">
								<option value="">Select...</option>
								@foreach($attributes['event.music'] as $row)
								<option value="{{ $row->id }}" {{ ($row->id == old('music')) ? 'selected' : '' }}>{{ $row->name }}</option>
								@endforeach
								@foreach($attributes['event.music_group'] as $music_group)
								<optgroup label="{{ $music_group->name }}">
									@foreach($music_group->children as $music)
									<option value="{{ $music->id }}" {{ ($music->id == old('music')) ? 'selected' : '' }}>{{ $music->name }}</option>
									@endforeach
								</optgroup>
								@endforeach
							</select>
							{!! $errors->first('music', '<span class="form-error is-visible">:message</span>') !!}
						</div>
					</div>
				</div>
				<div class="small-4 columns">
					<div class="row">
						<div class="small-5 columns">
							<label for="document">Document / ID:</label>
						</div>
						<div class="small-7 columns">
							<select id="document" name="document">
								<option value="">Select...</option>
								@foreach($attributes['event.document'] as $row)
								<option value="{{ $row->id }}" {{ ($row->id == old('document')) ? 'selected' : '' }}>{{ $row->name }}</option>
								@endforeach
							</select>
							{!! $errors->first('document', '<span class="form-error is-visible">:message</span>') !!}
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row content-block">
			<div class="small-4 columns">
				<button id="create-event-select-admins" class="button" type="button">Select Your Admins</button>
			</div>
			<div class="small-12 columns {{ (count($oldAdmins) > 0) ? '' : 'hide' }}" id="select-admins-wrap">
				<div class="row">
					<div class="small-10 small-centered columns select-admins-block">
						<button class="close" aria-label="Close" type="button">
							<span aria-hidden="true">&times;</span>
						</button>
						<div class="selected-label"><span>{{ (count($oldAdmins) > 0) ? count($oldAdmins) : '0' }}</span> selected</div>
						<div class="row small-up-3 select-admins-user-list" id="selected-admins">
							@if($oldAdmins)
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
								<input type="text" placeholder="Search users" id="select-admins-search">
							</div>
							<div class="small-2 float-left columns">
								<a class="view-all-users" data-open="event-select-admin-following-followers"><span class="fa fa-plus"></span> View all users</a>

								<div class="reveal-following-followers reveal" id="event-select-admin-following-followers" data-reveal>
									<div class="reveal-gray-ribbon no-padding">
										<div class="title">
											<ul class="tabs create-event-following-followers-tab" data-tabs id="followers-following-tabs">
												<li class="tabs-title is-active">
													<a href="#following">Following <span class="highlight round-to-k" data-total="{{ $user->numberOfFollowing }}">{{ $user->numberOfFollowing }}</span></a>
												</li>
												<li class="tabs-title">
													<a href="#followers" aria-selected="true">Followers <span class="highlight round-to-k" data-total="{{ $user->numberOfFollowers }}">{{ $user->numberOfFollowers }}</span></a>
												</li>
											</ul>
										</div>
										<button class="close-button create-event-following-followers-close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
									</div>

									<div class="tabs-content no-border select-admins-user-list" data-tabs-content="followers-following-tabs">
										<div class="tabs-panel is-active" id="following">
											<div class="row small-up-4" id="create-event-following-container">
												@foreach($user->following as $followable)
												@include('layouts.select_admin_add_user_popup')
												@endforeach
											</div>

											@if($user->following->hasMorePages())
											<a class="float-right" id="create-event-following-view-more" data-request-url="{{ route('user.following', $user) }}" data-type="following" data-next-page="{{ $user->following->currentPage()+1 }}" data-last-page="{{ $user->following->lastPage() }}">View more</a>
											@endif
										</div>

										<div class="tabs-panel" id="followers">
											<div class="row small-up-4" id="create-event-followers-container">
												@foreach($user->followers as $followable)
												@include('layouts.select_admin_add_user_popup')
												@endforeach
											</div>

											@if($user->followers->hasMorePages())
											<a class="float-right" id="create-event-followers-view-more" data-request-url="{{ route('user.followers', $user) }}" data-type="followers" data-next-page="{{ $user->followers->currentPage()+1 }}" data-last-page="{{ $user->followers->lastPage() }}">View more</a>
											@endif
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row small-up-3 select-admins-user-list search-results">
						</div>
					</div>
				</div>
			</div>
			<div class="small-8 columns float-right text-right">
				<input type="submit" name="preview_publish" class="button yellow" value="Preview / Publish">
			</div>
		</div>
	</form>
</section>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/chosen.jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/dropzone.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/sortable.min.js') }}"></script>
@if(Session::has('create_event_images'))
<?php $oldImages = Session::get('create_event_images'); ?>
<script type="text/javascript">
	var oldImages = {!! json_encode($oldImages) !!};
</script>
@endif
@if(old('mainImage'))
<script type="text/javascript">
	var oldMainImage = {!! json_encode(old('mainImage')) !!}
</script>
@endif
<script>
	$('#venue-page').chosen({
		'disable_search_threshold': -1,
		'width': '100%'
	});
	$('#creator-page').chosen({
		'disable_search_threshold': -1,
		'width': '100%'
	});
	Dropzone.autoDiscover = false;
	var eventImages = new Dropzone('#add-images', {
		url: "<?php echo route('events.create.ajaxUploadImage'); ?>",
		previewsContainer: '#previews-holder',
		paramName: "eventImage",
		maxFiles: 4,
		acceptedFiles: "image/*",
		addRemoveLinks: true,
		thumbnailWidth: null,
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		init: function() {
			this.on('maxfilesexceeded', function(file){
				this.removeFile(file);
			});
			this.on('removedfile', function(file){
				if(file.status !== "error"){
					$.ajax({
						type: "DELETE",
						url: "<?php echo route('events.create.ajaxDeleteImage'); ?>" + '/?image=' + file.newFileName,
						success: function(data){

						}
					});
				}
			});
			this.on('success', function(file, response){
				$('.dz-filename > span:contains("' + file.name + '")').html(response);
				file.newFileName = response;
			});
			this.on('error', function(file, errorMessage){
				this.removeFile(file);
				if(typeof errorMessage === 'string'){
					alert(errorMessage);
				}
				else{
					alert(errorMessage.eventImage);
				}
			});
			thisDropzone = this;
			if(typeof oldImages !== 'undefined'){
				if(typeof oldMainImage !== 'undefined'){
					$.each(oldImages, function(key, value){
						if(value[0] == oldMainImage){
							var file = { name: value[0], size: value[1], newFileName: value[0]};
							thisDropzone.options.addedfile.call(thisDropzone, file);
							thisDropzone.options.thumbnail.call(thisDropzone, file, "/uploads/temp/" + file.name);
							thisDropzone.emit("complete", file);
							oldImages.splice(key, 1);
							return false;
						}
					});
				}

				$.each(oldImages, function(key, value){

					var file = { name: value[0], size: value[1], newFileName: value[0]};

					thisDropzone.options.addedfile.call(thisDropzone, file);

					thisDropzone.options.thumbnail.call(thisDropzone, file, "/uploads/temp/" + file.name);

					thisDropzone.emit("complete", file);

				});
			}
		}
	});

var el = document.getElementById('previews-holder');
Sortable.create(el);

$('input[name="preview_publish"]').on('click', function(){
	var mainImage = $('div.dz-preview.dz-complete:first-child').find('div.dz-filename > span').html();
	if(typeof mainImage !== 'undefined'){
		$('input[name="mainImage"]').val(mainImage);
	}
});

</script>
@endsection