<div class="search-results">
    @if($events->count() > 0 || $pages->count() > 0 || $profiles->count() > 0)
    @if($events->count() > 0)
    <div id="event-results">
        <div class="group-heading">Events</div>
        @foreach($events as $event)
        <a href="{{ route('events.show', $event->id) }}">{{ $event->title }}</a>
        @endforeach
    </div>
    @endif
    @if($pages->count() > 0)
    <div id="page-results">
        <div class="group-heading">Pages</div>
        @foreach($pages as $page)
        <a href="{{ route('pages.show', $page->slug) }}">{{ $page->name }}</a>
        @endforeach
    </div>
    @endif
    @if($profiles->count() > 0)
    <div id="profile-results">
        <div class="group-heading">Profiles</div>
        @foreach($profiles as $profile)
        <a href="{{ route('user.profile', $profile->username) }}">{{ $profile->name }}</a>
        @endforeach
    </div>
    @endif
    @else
    <div class="group-heading">No results.</div>
    @endif
</div>