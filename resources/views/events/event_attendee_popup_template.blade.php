<div class="column">
	<a target="_blank" href="{{ route('user.profile', $attendee->username) }}"><img class="attendee-avatar" src="{{ url('images/small86/'.$attendee->avatarFullPath) }}"></a>
	@if(Auth::check() && Auth::user()->isFollowerOfTheUser($attendee))
	<button class="button follow-attendee active" data-follow-url="{{ route('user.profile.follow', $attendee->username) }}" data-unfollow-url="{{ route('user.profile.unfollow', $attendee->username) }}" data-user-fullname="{{ $attendee->name }}">{{ $attendee->name }}</button>
	@elseif($attendee->id == Auth::id())
	<button class="button follow-attendee active" data-follow-url="" data-unfollow-url="" data-user-fullname="{{ $attendee->name }}" disabled>{{ $attendee->name }}</button>
	@else
	<button class="button follow-attendee" data-follow-url="{{ route('user.profile.follow', $attendee->username) }}" data-unfollow-url="{{ route('user.profile.unfollow', $attendee->username) }}" data-user-fullname="{{ $attendee->name }}">Follow</button>
	@endif
</div>