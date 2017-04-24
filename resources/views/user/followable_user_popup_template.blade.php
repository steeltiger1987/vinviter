<div class="column">
	<a target="_blank" href="{{ route('user.profile', $followable->username) }}"><img class="user-avatar" src="{{ url('images/small86/'.$followable->avatarFullPath) }}"></a>
	@if(Auth::check() && Auth::user()->isFollowerOfTheUser($followable))
	<button class="button follow-followable active" data-follow-url="{{ route('user.profile.follow', $followable->username) }}" data-unfollow-url="{{ route('user.profile.unfollow', $followable->username) }}" data-user-fullname="{{ $followable->name }}">{{ $followable->name }}</button>
	@elseif(Auth::check() && $followable->id == Auth::id())
	<button class="button follow-followable active" data-follow-url="" data-unfollow-url="" data-user-fullname="{{ $followable->name }}" disabled>{{ $followable->name }}</button>
	@else
	<button class="button follow-followable" data-follow-url="{{ route('user.profile.follow', $followable->username) }}" data-unfollow-url="{{ route('user.profile.unfollow', $followable->username) }}" data-user-fullname="{{ $followable->name }}">Follow</button>
	@endif
</div>