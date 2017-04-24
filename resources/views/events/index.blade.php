@extends('layouts.master')

@section('title', 'Upcoming')

@section('content')
@include('layouts.search_panel')
<section class="content">
	<div class="row">
		<div class="small-10 small-centered columns date-filter">
			<div class="row">
				<form action="{{ route($eventMode.'Filter') }}" method="POST">
					{{ csrf_field() }}

					@if(Request::has('country'))
					<input type="hidden" name="country" value="{{ Request::get('country') }}">
					@endif

					@if(Request::has('region'))
					<input type="hidden" name="region" value="{{ Request::get('region') }}">
					@endif

					@if(Request::has('city'))
					<input type="hidden" name="city" value="{{ Request::get('city') }}">
					@endif

					@if(Request::has('type'))
					<input type="hidden" name="type" value="{{ Request::get('type') }}">
					@endif

					@if(Request::has('music'))
					<input type="hidden" name="music" value="{{ Request::get('music') }}">
					@endif

					<div class="small-3 columns">
						<h3>{{ ($eventMode == 'history') ? 'History' : 'Upcoming' }}:</h3>
					</div>
					<div class="small-3 columns">
						<select name="month" id="">
							<option value="">Month</option>
							@foreach($months as $key => $month)
							<option value="{{ $key }}" {{ ($key == Request::get('month')) ? 'selected' : '' }}>{{ $month }}</option>
							@endforeach
						</select>
					</div>
					<div class="small-3 columns">
						<select name="year" id="year">
							<option value="">Year</option>
							@foreach($yearsRange as $year)
							<option value="{{ $year }}" {{ ($year == Request::get('year')) ? 'selected' : '' }}>{{ $year }}</option>
							@endforeach
						</select>
					</div>
					<div class="small-3 columns">
						<input type="submit" name="events_filter" class="view-button" value="View">
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="small-12 columns events">
			@foreach($events as $event)
			@include('events.event_row_template')
			@endforeach
		</div>
	</div>
	<div class="row">
		<div class="small-12 columns">
			@if($events->hasMorePages())
			<button class="home-events-load-more load-more" data-event-type="{{ $eventMode }}" data-next-page="{{ $events->currentPage()+1 }}" data-last-page="{{ $events->lastPage() }}"><span class="fa fa-refresh"></span>Load more</button>
			<script type="text/javascript">
				var loadMoreParameters = "{!! '?'.http_build_query(Request::query()) !!}";
			</script>
			@endif
		</div>
	</div>
	
</section>
@endsection