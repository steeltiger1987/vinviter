<div class="row page-block">
    <div class="small-8 columns">
        <div class="page-thumbnail float-left">
            <a href="{{ route('pages.show', $page->slug) }}"><img src="{{ url('images/small180/'.$page->mainImageFullPath) }}" alt=""></a>
        </div>
        <div class="page-content float-left">
            <h4><a href="{{ route('pages.show', $page->slug) }}">{{ $page->name }}</a></h4>
            <div class="page-info">
                <div>
                    <span class="fa fa-th-large vat float-left"></span>
                    {{ $page->getPageType->first()->name }}
                </div>
                <div><span class="fa fa-map-marker"></span>{{$page->country->name }}</div>
            </div>
        </div>
    </div>
    <div class="small-4 columns">
        @if(count($page->followers) > 0)
        <div class="page-followers float-right">
            <div class="row">
                @if(Auth::check())
                <div class="small-5 columns no-pd-rgt">
                    @if(Auth::user()->isFollowerOfThePage($page))
                    <button class="follow-page-button follow-page-button-active follow-page-trigger user-profile-page" data-post-url="{{ route('pages.show', $page->slug) }}">Following</button>
                    @else
                    <button class="follow-page-button follow-page-trigger user-profile-page" data-post-url="{{ route('pages.show', $page->slug) }}">Follow</button>
                    @endif
                </div>
                @endif
                <div class="small-7 columns">
                    <button class="follow-page-button"><span class="page-total-followers round-to-k" data-total="{{ $page->followers->count() }}">{{ $page->followers->count() }}</span> followers</button>
                </div>
            </div>

            <div class="row small-up-4 avatars">
                @foreach($page->followers as $follower)
                <div class="column" data-id="{{ $follower->id }}"><a href="{{ route('user.profile', $follower) }}" target="_blank"><img data-tooltip aria-haspopup="true" class="has-tip" title="{{ $follower->name }}" src="{{ url('images/small59/'.$follower->avatarFullPath) }}" alt=""></a></div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>