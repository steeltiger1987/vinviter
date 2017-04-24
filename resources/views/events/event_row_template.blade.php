<div class="row event-block">
    <div class="small-8 columns">
        <div class="event-thumbnail float-left">
            <a href="{{ route('events.show', $event->id) }}"><img src="{{ url('images/small180/'.$event->mainImageFullPath) }}" alt=""></a>
        </div>
        <div class="event-content float-left">
            <h4><a href="{{ route('events.show', $event->id) }}">{{ $event->title }}</a></h4>
            <div class="event-info">
                <div><span class="fa fa-calendar"></span>{{ $event->starts_at->format('l, d-m-Y') }}</div>
                @if(!$event->is_location_hidden)
                @if($event->venuePage || $event->address || $event->zip_code || $event->city)
                <div>
                    <span class="fa fa-location-arrow vat float-left"></span>
                    <div class="dib">
                        @if($event->venuePage)
                        <a target="_blank" href="{{ route('pages.show', $event->venuePage->slug) }}">{{ $event->venuePage->name }}</a><br>
                        @endif
                        @if($event->address)
                        {{ $event->address }}<br>
                        @endif
                        @if($event->zip_code)
                        {{ $event->zip_code.', ' }}
                        @endif
                        {{ $event->city->name }}
                    </div>
                </div>
                @endif
                @endif
                <div><span class="fa fa-map-marker"></span>{{ $event->region->name.', '.$event->country->name }}</div>
            </div>
        </div>
    </div>

    <div class="small-4 columns">
        @if($eventMode == 'upcoming')
        <div class="event-attendees float-right">
            <div class="row">
                @if(Auth::check())
                <div class="small-5 columns no-pd-rgt">
                    @if(Auth::user()->isAttendingTheEvent($event))
                    <button class="attend-event attend-button-active" data-id="{{ $event->id }}" data-type="upcoming">I'm going <span class="fa fa-check-circle"></span></button>
                    @else
                    <button class="attend-event attend-button" data-id="{{ $event->id }}" data-type="upcoming">Attend</button>
                    @endif
                </div>
                @endif
                <div class="small-7 columns">
                    <button class="number-of-attendees attend-button"><span class="round-to-k" data-total="{{ $event->numberOfAttendees }}">{{ $event->numberOfAttendees }}</span> attending</button>
                </div>
            </div>
            <div class="row small-up-3 avatars">
                @foreach($event->attendees as $attendee)
                <div class="column" data-id="{{ $attendee->id }}"><a href="{{ route('user.profile', $attendee) }}" target="_blank"><img data-tooltip aria-haspopup="true" class="has-tip" title="{{ $attendee->name }}" src="{{ url('images/small59/'.$attendee->avatarFullPath) }}" alt=""></a></div>
                @endforeach
            </div>
        </div>

        @else
        <div class="event-attendees float-right">
            <div class="row">
                @if(Auth::check())
                <div class="small-5 columns no-pd-rgt">
                    @if(Auth::user()->hasAttendedTheEvent($event))
                    <button class="attend-event attend-button-active" data-id="{{ $event->id }}" data-type="history">I was there <span class="fa fa-check-circle"></span></button>
                    @else
                    <button class="attend-event attend-button" data-id="{{ $event->id }}" data-type="history">I was there</button>
                    @endif
                </div>
                @endif
                <div class="small-7 columns">
                    <button class="number-of-attendees attend-button"><span class="round-to-k" data-total="{{ $event->numberOfAttendees }}">{{ $event->numberOfAttendees }}</span> attended</button>
                </div>
            </div>
            <div class="row small-up-3 avatars">
                @foreach($event->attendees as $attendee)
                <div class="column" data-id="{{ $attendee->id }}"><a href="{{ route('user.profile', $attendee) }}" target="_blank"><img data-tooltip aria-haspopup="true" class="has-tip" title="{{ $attendee->name }}" src="{{ url('images/small59/'.$attendee->avatarFullPath) }}" alt=""></a></div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>