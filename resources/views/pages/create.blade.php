@extends('layouts.master')

@section('title', 'Create a new page')

@section('head')
<link rel="stylesheet" type="text/css" href="{{ asset('css/dropzone.min.css') }}">
@endsection

@section('content')
<section class="create-edit-page">
	<div class="heading-block">
		<div class="row">
			<div class="small-10 small-centered columns">
				<h3 class="heading">Pages</h3>
				<div class="description">
					<h5>Create a page for your venue or event organization.</h5>
					<h5>Keep your fans and supporters updated on all of your upcoming and past events.</h5>
				</div>
			</div>
		</div>
	</div>

	<form action="{{ route('pages.store') }}" name="createPage" method="POST" enctype="multipart/form-data">
		{!! csrf_field() !!}
		<input type="hidden" name="formID" value="{{ old('formID', str_random(10)) }}">
		
		<div class="row content-block">
			<div class="small-11 small-centered columns">
				<div class="row">
					<div class="small-6 columns">
						<div class="row">
							<div class="small-4 columns">
								<label for="name">Name<span class="required">*</span>:</label>
							</div>
							<div class="small-7 float-left columns">
								<input id="name" type="text" name="name" value="{{ old('name') }}">
								{!! $errors->first('name', '<span class="form-error is-visible">:message</span>') !!}
							</div>
						</div>

						<div class="row">
							<div class="small-4 columns">
								<label for="slug">Page slug<span class="required">*</span>:</label>
							</div>
							<div class="small-7 float-left columns">
								<input id="slug" type="text" name="slug" value="{{ old('slug') }}">
								{!! $errors->first('slug', '<span class="form-error is-visible">:message</span>') !!}
							</div>
							<div class="small-1 columns">
								<span class="info-icon fa fa-question-circle has-tip" data-tooltip data-disable-hover='false' title="Add a page slug (nickname) of your Venue or Organization. You can keep same as Name if you don't have one."></span>
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
					</div>
					<div class="small-5 float-right columns">
						<div class="row">
							<div class="small-4 columns">
								<label for="type">Type<span class="required">*</span>:</label>
							</div>
							<div class="small-8 columns">
								<select id="type" name="type">
									<option value="">Select...</option>
									@foreach($attributes['page.type_group'] as $type_group)
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
								<label for="status">Status:</label>
							</div>
							<div class="small-8 float-left columns">
								<textarea id="status" name="status" rows="4">{{ old('status') }}</textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="gray-block add-images">
			<div class="row">
				<div class="small-5 small-centered columns">
					<div class="row small-up-2">
						<div class="column">
							<h5 class="heading-tiny">Main Image</h5>
							<div class="image-wrapper">
								<button class="button" type="button" id="upload-main-image">Upload</button>
								<div id="main-image-preview" class="dropzone dropzone-previews"></div>
								<input type="hidden" name="main_image" value="1">
								{!! $errors->first('main_image', '<span class="form-error is-visible">:message</span>') !!}
							</div>
						</div>
						<div class="column">
							<h5 class="heading-tiny">Background Image</h5>
							<div class="image-wrapper">
								<button class="button text-centered" type="button" id="upload-bg-image">Upload</button>
								<div id="bg-image-preview" class="dropzone dropzone-previews"></div>
								<input type="hidden" name="background_image" value="1">
								{!! $errors->first('background_image', '<span class="form-error is-visible">:message</span>') !!}
							</div>
						</div>
					</div>
					<p><h6 class="text-center">Don’t have an image? Skip by using our Default one.</h6></p>
				</div>
			</div>
		</div>
		<div class="row content-block">
			<div class="small-11 small-centered columns">
				<div class="row">
					<div class="small-2 columns">
						<label for="story">Story<span class="required">*</span>:</label>
					</div>
					<div class="small-6 float-left columns">
						<textarea id="story" name="story" rows="8">{{ old('story') }}</textarea>
						{!! $errors->first('story', '<span class="form-error is-visible">:message</span>') !!}
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
						<div class="small-5 columns">
							<label for="year-founded">Year Founded:</label>
						</div>
						<div class="small-7 columns">
							<select id="year-founded" name="year_founded">
								<option value="">Select...</option>
								@foreach($attributes['page.year'] as $year)
								<option value="{{ $year->id }}" {{ ($year->id == old('year_founded')) ? 'selected' : '' }}>{{ $year->name }}</option>
								@endforeach
							</select>
							{!! $errors->first('year_founded', '<span class="form-error is-visible">:message</span>') !!}
						</div>
					</div>
					<div class="row">
						<div class="small-5 columns">
							<label for="key-people-search">Key People:</label>
						</div>
						<div class="small-7 columns">
							<div class="key-people-box">
								<div class="key-people-results">
									<div>
										<div class="row">
											<div class="small-12 columns">
												<ul>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<input id="key-people-search" placeholder="Enter name" type="text">
							</div>
						</div>
					</div>
				</div>
				<div class="small-4 small-offset-1 float-left columns">
					<div class="row">
						<div class="small-5 columns">
							<label for="activity-period">Activity Period:</label>
						</div>
						<div class="small-7 columns">
							<select id="activity-period" name="activity_period">
								<option value="">Select...</option>
								@foreach($attributes['page.activity_period'] as $activity_period)
								<option value="{{ $activity_period->id }}" {{ ($activity_period->id == old('activity_period')) ? 'selected' : '' }}>{{ $activity_period->name }}</option>
								@endforeach
							</select>
							{!! $errors->first('activity_period', '<span class="form-error is-visible">:message</span>') !!}
						</div>
					</div>
					<div class="row">
						<div class="small-5 columns">
							<label for="season">Season:</label>
						</div>
						<div class="small-7 columns">
							<select id="season" name="season">
								<option value="">Select...</option>
								@foreach($attributes['page.season'] as $season)
								<option value="{{ $season->id }}" {{ ($season->id == old('season')) ? 'selected' : '' }}>{{ $season->name }}</option>
								@endforeach
							</select>
							{!! $errors->first('season', '<span class="form-error is-visible">:message</span>') !!}
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="small-12 columns" id="selected-key-people">
					@if($oldKeyPeople)
					@foreach($oldKeyPeople as $person)
					<span class="key-people-label" data-closable>{{ $person->name }}<i class="fa fa-times key-people-remove" data-close data-id="{{ $person->id }}"></i></span>
					<input type="hidden" name="key_people[]" value="{{ $person->id }}">
					@endforeach
					@endif
				</div>
			</div>
		</div>

		<div class="row content-block">
			<div class="small-4 columns">
				<button id="create-page-select-admins" class="button" type="button">Select Your Admins</button>
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
								<a class="view-all-users" data-open="page-select-admin-following-followers"><span class="fa fa-plus"></span> View all users</a>

								<div class="reveal-following-followers reveal" id="page-select-admin-following-followers" data-reveal>
									<div class="reveal-gray-ribbon no-padding">
										<div class="title">
											<ul class="tabs create-page-following-followers-tab" data-tabs id="followers-following-tabs">
												<li class="tabs-title is-active">
													<a href="#following">Following <span class="highlight round-to-k" data-total="{{ $user->numberOfFollowing }}">{{ $user->numberOfFollowing }}</span></a>
												</li>
												<li class="tabs-title">
													<a href="#followers" aria-selected="true">Followers <span class="highlight round-to-k" data-total="{{ $user->numberOfFollowers }}">{{ $user->numberOfFollowers }}</span></a>
												</li>
											</ul>
										</div>
										<button class="close-button create-page-following-followers-close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
									</div>

									<div class="tabs-content no-border select-admins-user-list" data-tabs-content="followers-following-tabs">
										<div class="tabs-panel is-active" id="following">
											<div class="row small-up-4" id="create-page-following-container">
												@foreach($user->following as $followable)
												@include('layouts.select_admin_add_user_popup')
												@endforeach
											</div>

											@if($user->following->hasMorePages())
											<a class="float-right" id="create-page-following-view-more" data-request-url="{{ route('user.following', $user) }}" data-type="following" data-next-page="{{ $user->following->currentPage()+1 }}" data-last-page="{{ $user->following->lastPage() }}">View more</a>
											@endif
										</div>

										<div class="tabs-panel" id="followers">
											<div class="row small-up-4" id="create-page-followers-container">
												@foreach($user->followers as $followable)
												@include('layouts.select_admin_add_user_popup')
												@endforeach
											</div>

											@if($user->followers->hasMorePages())
											<a class="float-right" id="create-page-followers-view-more" data-request-url="{{ route('user.followers', $user) }}" data-type="followers" data-next-page="{{ $user->followers->currentPage()+1 }}" data-last-page="{{ $user->followers->lastPage() }}">View more</a>
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
<script type="text/javascript" src="{{ asset('js/dropzone.min.js') }}"></script>

@if(Session::has('create_page_main_image'))
<?php $oldMainImage = Session::get('create_page_main_image'); ?>
<script type="text/javascript">
	var oldMainImage = {!! json_encode($oldMainImage) !!};
</script>
@endif
@if(Session::has('create_page_bg_image'))
<?php $oldBgImage = Session::get('create_page_bg_image'); ?>
<script type="text/javascript">
	var oldBgImage = {!! json_encode($oldBgImage) !!};
</script>
@endif

<script>
	Dropzone.autoDiscover = false;

	var pageMainImage = new Dropzone('#upload-main-image', {
		url: "<?php echo route('pages.create.ajaxUploadImage'); ?>",
		previewsContainer: '#main-image-preview',
		paramName: "pageImage",
		maxFiles: 1,
		acceptedFiles: "image/*",
		addRemoveLinks: true,
		thumbnailWidth: null,
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		init: function() {
			this.on('sending', function(file, xhr, formData){
				formData.append('pageImageType', 'main');
			});
			this.on('maxfilesexceeded', function(file){
				this.removeFile(file);
			});

			this.on('removedfile', function(file){
				if(file.status !== "error"){
					$.ajax({
						type: "DELETE",
						url: "<?php echo route('pages.create.ajaxDeleteImage'); ?>" + '/?image=' + file.newFileName,
						success: function(data){

						}
					});
				}
				$('#upload-main-image').show();
			});

			this.on('success', function(file, response){
				$('.dz-filename > span:contains("' + file.name + '")').html(response);
				file.newFileName = response;

				$('#upload-main-image').hide();
			});

			this.on('error', function(file, errorMessage){
				this.removeFile(file);
				if(typeof errorMessage === 'string'){
					alert(errorMessage);
				}
				else{
					alert(errorMessage.pageMainImage);
				}
			});

			thisDropzone = this;
			if(typeof oldMainImage !== 'undefined'){

				var file = { name: oldMainImage[0], size: oldMainImage[1], newFileName: oldMainImage[0]};

				thisDropzone.options.addedfile.call(thisDropzone, file);

				thisDropzone.options.thumbnail.call(thisDropzone, file, "/uploads/temp/" + file.name);

				thisDropzone.emit("complete", file);

				$('#upload-main-image').hide();

			}
		}
	});

var pageBgImage = new Dropzone('#upload-bg-image', {
	url: "<?php echo route('pages.create.ajaxUploadImage'); ?>",
	previewsContainer: '#bg-image-preview',
	paramName: "pageImage",
	maxFiles: 1,
	acceptedFiles: "image/*",
	addRemoveLinks: true,
	thumbnailWidth: null,
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	},
	init: function() {
		this.on('sending', function(file, xhr, formData){
			formData.append('pageImageType', 'bg');
		});
		this.on('maxfilesexceeded', function(file){
			this.removeFile(file);
		});

		this.on('removedfile', function(file){
			if(file.status !== "error"){
				$.ajax({
					type: "DELETE",
					url: "<?php echo route('pages.create.ajaxDeleteImage'); ?>" + '/?image=' + file.newFileName,
					success: function(data){
						$('#upload-bg-image').show();
					}
				});
			}
		});

		this.on('success', function(file, response){
			$('.dz-filename > span:contains("' + file.name + '")').html(response);
			file.newFileName = response;

			$('#upload-bg-image').hide();
		});

		this.on('error', function(file, errorMessage){
			this.removeFile(file);
			if(typeof errorMessage === 'string'){
				alert(errorMessage);
			}
			else{
				alert(errorMessage.pageBgImage);
			}
		});

		thisDropzone = this;
		if(typeof oldBgImage !== 'undefined'){

			var file = { name: oldBgImage[0], size: oldBgImage[1], newFileName: oldBgImage[0]};

			thisDropzone.options.addedfile.call(thisDropzone, file);

			thisDropzone.options.thumbnail.call(thisDropzone, file, "/uploads/temp/" + file.name);

			thisDropzone.emit("complete", file);

			$('#upload-bg-image').hide();

		}
	}
});

</script>
@endsection