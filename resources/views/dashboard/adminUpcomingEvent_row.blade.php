<div class="row event-block" data-id="{{ $event->id }}">
    <div class="small-10 columns">
        <div class="event-thumbnail float-left">
            <a href="{{ route('events.show', $event->id) }}"><img src="{{ url('images/small180/'.$event->mainImageFullPath) }}" alt=""></a>
        </div>
        <div class="event-content float-left">
            <h4><a href="{{ route('events.show', $event->id) }}">{{ $event->title }} <span class="user-event-relation">Admin{{ ($event->is_private) ? ', Private' : '' }}</span></a></h4>
            <div class="event-info">
                <div><span class="fa fa-calendar"></span>{{ $event->starts_at->format('l, d-m-Y') }}</div>
                @if($event->venuePage || $event->address || $event->zip_code)
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
                        {{ $event->zip_code }}<br>
                        @endif
                    </div>
                </div>
                @endif
                <div><span class="fa fa-map-marker"></span>{{ $event->city->name.', '.$event->country->name }}</div>
            </div>
            <div>
                <div class="invites-sent">Invites sent <span data-total="{{ $event->numberOfInvitedUsers }}" class="round-to-k">{{ $event->numberOfInvitedUsers }}</span></div>
                <div class="attendance">Attending <span data-total="{{ $event->numberOfAttendees }}" class="round-to-k">{{ $event->numberOfAttendees }}</span></div>
            </div>
        </div>
    </div>
    <div class="small-2 columns actions">
        <a data-open="event-invite-{{ $event->id }}" class="button">Invite</a>
        <a class="button hollow" href="{{ route('events.edit', $event->id) }}" target="_blank">Edit</a>
        <a data-open="remove-event-admin-{{ $event->id }}" class="button hollow">Remove</a>
    </div>
    <div class="reveal-do-invitation reveal" id="event-invite-{{ $event->id }}" data-reveal>
        <div class="reveal-gray-ribbon no-padding">
            <div class="title">
                <div class="event-invite-tabs"><span class="top-title">Invite</span></div>
                <ul class="tabs event-invite-tabs" data-tabs id="event-invite-tabs">
                    <li class="tabs-title is-active">
                        <a href="#search-users-{{ $event->id }}">Search Users</a>
                    </li>
                    <li class="tabs-title">
                        <a href="#invite-lists-{{ $event->id }}" aria-selected="true">My Invite Lists</a>
                    </li>
                </ul>
            </div>
            <button class="close-button event-invite-close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="tabs-content no-border" data-tabs-content="event-invite-tabs">
            <div class="tabs-panel is-active" id="search-users-{{ $event->id }}">
                <div class="row">
                    <div class="small-8 small-centered columns">
                        <input type="text" placeholder="Search users" class="event-invite-search-users" data-event-id="{{ $event->id }}">
                        {!! $errors->first('members', '<span class="form-error is-visible">:message</span>') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="small-11 columns small-offset-1">
                        <div class="row">
                            <div class="row small-up-3 small-centered event-invite-search-user-list search-results" data-event-id="{{ $event->id }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tabs-panel" id="invite-lists-{{ $event->id }}" data-request-url="{{ route('events.inviteAList', $event) }}">
                <div class="row small-up-6 event-invite-invite-lists-container">
                    @foreach($inviteLists as $list)
                    @include('dashboard.invite_list_event_popup_row')
                    @endforeach
                </div>
                @if($inviteLists->hasMorePages())
                <a class="text-center event-invite-lists-view-more" data-request-url="{{ route('events.inviteLists', $event) }}" data-next-page="{{ $inviteLists->currentPage()+1 }}" data-last-page="{{ $inviteLists->lastPage() }}">View more</a>
                @endif
            </div>
        </div>
    </div>
    <div class="reveal" id="remove-event-admin-{{ $event->id }}" data-reveal>
        <h4>Remove</h4>
        <p>Are you sure you want to remove yourself as admin from this event?</p>
        <p class="float-right remove-admin-buttons">
            <button type="button" class="button secondary" data-close>Cancel</button>
            <button type="button" class="button alert remove-event-admin-confirm" data-admin-id="{{ Auth::id() }}" data-id="{{ $event->id }}">Remove</button>
        </p>
        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>