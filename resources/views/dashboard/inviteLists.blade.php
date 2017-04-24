@extends('layouts.master')

@section('title', 'Invite Lists - Dashboard')

@section('content')
<nav>
	<div class="middle-gray-panel">
		@include('layouts.feedback_modal')
		<div class="row">
			<div class="small-8 small-centered columns">
				<div class="option-box">
					<div class="row small-up-3 dib100">
						<div class="column"><a class="button dashboard-nav-button" href="{{ route('dashboard.upcomingEvents') }}">My Events</a></div>
						<div class="column"><a class="button dashboard-nav-button" href="{{ route('dashboard.publishedPages') }}">My Pages</a></div>
						<div class="column"><a class="button dashboard-nav-button dashboard-page-active" href="{{ route('dashboard.inviteLists.show') }}">Invite Lists</a></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</nav>
<section class="content dashboard">
	<div class="row invite-lists">
		<div class="small-12 columns">
			<button type="button" class="button create-new">Create New</button>
			<span class="info-icon fa fa-question-circle-o has-tip" data-tooltip data-disable-hover='false' title="This is the easiest way to invite multiple users at once. Examples: Birthday list, Family list, Event list and etc"></span>
		</div>
		<div class="small-12 columns list-container">
			<div class="row collapse list-block hide" id="create-new-invite-list">
				<div class="small-5 columns">
					<div class="input-group">
						<input class="input-group-field" type="text" name="list_name" placeholder="Enter name">
						<span class="input-group-label">0</span>
					</div>
				</div>
				<div class="small-3 columns actions float-right">
					<div class="row small-up-2">
						<div class="column"><button id="submit-create-new" class="button hollow" type="button">Done</button></div>
						<div class="column"><button id="cancel-create-new" class="button hollow" type="button">Remove</button></div>
					</div>
				</div>
			</div>
			@forelse($lists as $list)
			@include('dashboard.invite_list_block')
			@empty
			<br>
			@endforelse
		</div>
	</div>
    @if($lists->hasMorePages())
    <div class="row">
        <div class="small-12 columns">
            <button class="dashboard-invite-lists-load-more load-more" data-request-url="{{ route('dashboard.inviteLists.show') }}" data-next-page="{{ $lists->currentPage()+1 }}" data-last-page="{{ $lists->lastPage() }}" data-left-off="{{ $lists->last()->id }}"><span class="fa fa-refresh"></span>Load more</button>
            <script type="text/javascript">
                var loadMoreParameters = "{!! '?'.http_build_query(Request::query()) !!}";
            </script>
        </div>
    </div>
    @endif
</section>
@endsection