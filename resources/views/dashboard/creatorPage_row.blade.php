<div class="row page-block" data-id="{{ $page->id }}">
    <div class="small-10 columns">
        <div class="page-thumbnail float-left">
            <a href="{{ route('pages.show', $page->slug) }}"><img src="{{ url('images/small180/'.$page->mainImageFullPath) }}" alt=""></a>
        </div>
        <div class="page-content float-left">
            <h4><a href="{{ route('pages.show', $page->slug) }}">{{ $page->name }} <span class="user-page-relation">Creator</span></a></h4>
            <div class="page-info">
                <div><span class="fa fa-star"></span>{{ $page->getPageType[0]->name }}</div>
                <div><span class="fa fa-map-marker"></span>{{ $page->city->name.', '.$page->country->name }}</div>
            </div>
            <div>
                <div class="total-followers">Followers <span data-total="{{ $page->numberOfFollowers }}" class="{{ $page->numberOfFollowers }}">{{ $page->numberOfFollowers }}</span></div>
            </div>
        </div>
    </div>
    <div class="small-2 columns actions">
        <a data-open="page-invite-{{ $page->id }}" class="button">Invite</a>
        <a class="button hollow" target="_blank`" href="{{ route('pages.edit', $page->slug) }}">Edit</a>
        <a data-open="delete-page-{{ $page->id }}" class="button hollow">Delete</a>
    </div>
    <div class="reveal-do-invitation reveal" id="page-invite-{{ $page->id }}" data-reveal>
        <div class="reveal-gray-ribbon no-padding">
            <div class="title">
                <div class="page-invite-tabs"><span class="top-title">Invite</span></div>
                <ul class="tabs page-invite-tabs" data-tabs id="page-invite-tabs">
                    <li class="tabs-title is-active">
                        <a href="#search-users-{{ $page->id }}">Search Users</a>
                    </li>
                    <li class="tabs-title">
                        <a href="#invite-lists-{{ $page->id }}" aria-selected="true">My Invite Lists</a>
                    </li>
                </ul>
            </div>
            <button class="close-button page-invite-close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="tabs-content no-border" data-tabs-content="page-invite-tabs">
            <div class="tabs-panel is-active" id="search-users-{{ $page->id }}">
                <div class="row">
                    <div class="small-8 small-centered columns">
                        <input type="text" placeholder="Search users" class="page-invite-search-users" data-page-slug="{{ $page->slug }}">
                        {!! $errors->first('members', '<span class="form-error is-visible">:message</span>') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="small-11 columns small-offset-1">
                        <div class="row">
                            <div class="row small-up-3 small-centered page-invite-search-user-list search-results" data-page-slug="{{ $page->slug }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tabs-panel" id="invite-lists-{{ $page->id }}" data-request-url="{{ route('pages.inviteAList', $page) }}">
                <div class="row small-up-6 page-invite-invite-lists-container">
                    @foreach($inviteLists as $list)
                    @include('dashboard.invite_list_page_popup_row')
                    @endforeach
                </div>
                @if($inviteLists->hasMorePages())
                <a class="text-center page-invite-lists-view-more" data-request-url="{{ route('pages.inviteLists', $page) }}" data-next-page="{{ $inviteLists->currentPage()+1 }}" data-last-page="{{ $inviteLists->lastPage() }}">View more</a>
                @endif
            </div>
        </div>
    </div>
    <div class="reveal" id="delete-page-{{ $page->id }}" data-reveal>
        <h4>Delete</h4>
        <p>Are you sure you want to delete this page?</p>
        <p class="float-right delete-page-buttons">
            <button type="button" class="button secondary" data-close>Cancel</button>
            <button type="button" class="button alert delete-page-confirm" data-slug="{{ $page->slug }}" data-id="{{ $page->id }}">Delete</button>
        </p>
        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>