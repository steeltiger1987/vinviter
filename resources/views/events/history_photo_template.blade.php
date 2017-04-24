<div class="column" data-photo-id="{{ $photo->id }}">
	<a href="{{ url('uploads/'.$photo->fullPath) }}" class="history-event-photo">
		<img src="{{ url('images/200x135/'.$photo->fullPath) }}" alt="">
	</a>
	@if(Auth::check() && Auth::user()->doesLikeThePhoto($photo))
	<p><button class="fa fa-heart button-favorite active" data-is-liked="1"></button><span class="round-to-k" data-total="{{ $photo->numberOfLikes }}">{{ $photo->numberOfLikes }}</span> Favorites</p>
	@elseif(Auth::check())
	<p><button class="fa fa-heart button-favorite" data-is-liked="0"></button><span class="round-to-k" data-total="{{ $photo->numberOfLikes }}">{{ $photo->numberOfLikes }}</span> Favorites</p>
	@else
	<p><span class="round-to-k" data-total="{{ $photo->numberOfLikes }}">{{ $photo->numberOfLikes }}</span> Favorites</p>
	@endif
</div>