<nav>
	<div class="middle-gray-panel">
		@include('layouts.feedback_modal')
		<div class="row">
			<div class="small-10 small-centered columns">
				<form action="{{ route($eventMode.'Filter') }}" method="POST" name="filterEvents">
					{{ csrf_field() }}
					<div class="row">
						<div class="small-2 columns">
							<ul class="tabs-show-vertical">
								<li class="heading"><span>Show</span></li>
								<li class="{{ (Request::route()->getName() == 'upcoming') ? 'active' : '' }}"><a href="{{ route('upcoming') }}">Upcoming</a></li>
								<li class="{{ (Request::route()->getName() == 'history') ? 'active' : '' }}"><a href="{{ route('history') }}">History</a></li>
							</ul>
						</div>
						<div class="small-10 columns">
							<div class="option-box">
								<div class="row small-up-3">

									<div class="column">
										<select id="country" name="country">
											<option value="">Country</option>
											@foreach($countries as $country)
											<option value="{{ $country->id }}" {{ (Request::get('country') == $country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
											@endforeach
										</select>
									</div>

									<div class="column">
										<select id="region" name="region">
											<option value="">Region</option>
											@foreach($regions as $region)
											<option value="{{ $region->id }}" {{ (Request::get('region') == $region->id) ? 'selected' : '' }}>{{ $region->name }}</option>
											@endforeach
										</select>
									</div>
									<div class="column">
										<select id="city" name="city">
											<option value="">City</option>
											@foreach($cities as $city)
											<option value="{{ $city->id }}" {{ (Request::get('city') == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
											@endforeach
										</select>
									</div>

									<div class="column">
										<select id="type" name="type">
											<option value="">Type</option>
											@foreach($attributes['event.type'] as $row)
											<option value="{{ $row->id }}" {{ ($row->id == Request::get('type')) ? 'selected' : '' }}>{{ $row->name }}</option>
											@endforeach

											@foreach($attributes['event.type_group'] as $type_group)
											<optgroup label="{{ $type_group->name }}">
												@foreach($type_group->children as $type)
												<option value="{{ $type->id }}" {{ ($type->id == Request::get('type')) ? 'selected' : '' }}>{{ $type->name }}</option>
												@endforeach
											</optgroup>
											@endforeach
										</select>
									</div>

									<div class="column">
										<select id="music" name="music">
											<option value="">Music</option>
											@foreach($attributes['event.music'] as $row)
											<option value="{{ $row->id }}" {{ ($row->id == Request::get('music')) ? 'selected' : '' }}>{{ $row->name }}</option>
											@endforeach
											@foreach($attributes['event.music_group'] as $music_group)
											<optgroup label="{{ $music_group->name }}">
												@foreach($music_group->children as $music)
												<option value="{{ $music->id }}" {{ ($music->id == Request::get('music')) ? 'selected' : '' }}>{{ $music->name }}</option>
												@endforeach
											</optgroup>
											@endforeach
										</select>
									</div>

									<div class="column">
										<input type="submit" class="search-button" name="events_filter" value="Search">
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</nav>