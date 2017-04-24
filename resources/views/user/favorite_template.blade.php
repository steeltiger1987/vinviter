<div class="column" data-photo-id="{{ $favorite->id }}">
	<a href="{{ url('uploads/'.$favorite->fullPath) }}" class="favorite-media-element">
		<img src="{{ url('images/200x135/'.$favorite->fullPath) }}" alt="">
	</a>
	<p><button class="fa fa-heart button-favorite active" data-is-liked="1" data-request-url="{{ route('user.favoriteLike', [Auth::user(), $favorite->likeable_id]) }}"></button><span class="round-to-k" data-total="{{ count($favoritesLikes[$favorite->likeable_id]) }}">{{ count($favoritesLikes[$favorite->likeable_id]) }}</span> Favorites</p>
</div>