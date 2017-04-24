@if($notifications->count())
	<li id="notifications-header">
		<div class="notification-row">
			<h6>Notifications</h6>
		</div>
	</li>
	@foreach($notifications as $notification)
		<li>
		@if($notification->linkPage)
		<a href="{{ route('pages.show', $notification->linkPage) }}" target="_blank">
		@elseif($notification->linkEvent)
		<a href="{{ route('events.show', $notification->linkEvent) }}" target="_blank">
		@else
		<a href="{{ route('user.profile', $notification->linkUser) }}" target="_blank">
		@endif
			<div class="notification-row notification-item">
				<div><img src="{{ url('images/small41/'.$notification->linkUser->avatarFullPath) }}" class="user-avatar"></div>
				<span class="description-text">{{ $notification->linkUser->name }} - <strong>{{ config('common.notification_type_title.'.$notification->notification_type) }}</strong></span>
				@if($notification->linkPage)
				<i>{{ $notification->linkPage->name }}</i>
				@elseif($notification->linkEvent)
				<i>{{ $notification->linkEvent->title }}</i>
				@endif
			</div>
		</a>
		</li>
	@endforeach
	<div class="notification-row text-center" id="see-all-notifications">
		<a href="{{ route('user.notifications') }}" class="see-all">See all</a>
	</div>
@else
	<div class="notification-row" id="no-notifications">
		<h6 class="text-center">No notifications</h6>
	</div>
	<div class="notification-row text-center" id="see-all-notifications">
		<a href="{{ route('user.notifications') }}" class="see-all">See all</a>
	</div>
@endif