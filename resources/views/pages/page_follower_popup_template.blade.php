<div class="column">
	<a target="_blank" href="{{ route('user.profile', $follower->username) }}"><img class="user-avatar" src="{{ url('images/small86/'.$follower->avatarFullPath) }}"></a>
	@if(Auth::check() && Auth::user()->isFollowerOfTheUser($follower))
	<button class="button follow-followable active" data-follow-url="{{ route('user.profile.follow', $follower->username) }}" data-unfollow-url="{{ route('user.profile.unfollow', $follower->username) }}" data-user-fullname="{{ $follower->name }}">{{ $follower->name }}</button>
	@elseif(Auth::check() && $follower->id == Auth::id())
	<button class="button follow-followable active" data-follow-url="" data-unfollow-url="" data-user-fullname="{{ $follower->name }}" disabled>{{ $follower->name }}</button>
	@else
	<button class="button follow-followable" data-follow-url="{{ route('user.profile.follow', $follower->username) }}" data-unfollow-url="{{ route('user.profile.unfollow', $follower->username) }}" data-user-fullname="{{ $follower->name }}">Follow</button>
	@endif
</div>