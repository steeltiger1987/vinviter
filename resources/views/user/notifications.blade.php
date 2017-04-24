@extends('layouts.master_basic')

@section('title', 'Account - Notifications')

@section('body')
<div id="wrapper" class="page-bg-gray">
	@include('layouts.header')
	<section class="content">
		<div class="row">
			<div class="small-11 columns small-centered">
				<div class="user-notifications">
					<div class="notification-row">
						<h4 class="float-left">Notifications</h4>
						<a id="clear-all-notifications" class="clear-all {{ ($user->all_notifications->count()) ? '' : 'hide' }}">Clear all</a>
						<div class="clearfix"></div>
					</div>
					@forelse($user->all_notifications as $n)
					<div class="notification-row notification-item">
						<a href="{{ route('user.profile', $user) }}" target="_blank"><img src="{{ url('images/small82/'.$n->linkUser->avatarFullPath) }}" class="user-avatar"></a>
						<span class="description-text">{{ $n->linkUser->name }} - <strong>{{ config('common.notification_type_title.'.$n->notification_type) }}</strong></span>
						@if($n->linkPage)
						<a href="{{ route('pages.show', $n->linkPage) }}" target="_blank"><i>{{ $n->linkPage->name }}</i></a>
						@elseif($n->linkEvent)
						<a href="{{ route('events.show', $n->linkEvent) }}" target="_blank"><i>{{ $n->linkEvent->title }}</i></a>
						@endif
					</div>
					@empty
					<div class="notification-row">
						<h5>No notifications.</h5>
					</div>
					@endforelse
				</div>
			</div>
		</div>
	</section>
	@include('layouts.footer')
	<script src="{{ asset('js/all.js') }}"></script>
</div>
@endsection
