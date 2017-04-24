@extends('layouts.master')

@section('title', 'Saved pages - Dashboard')

@section('content')
<nav>
    <div class="middle-gray-panel">
        @include('layouts.feedback_modal')
        <div class="row">
            <div class="small-8 small-centered columns">
                <div class="option-box">
                    <div class="row small-up-3 dib100">
                        <div class="column"><a class="button dashboard-nav-button" href="{{ route('dashboard.upcomingEvents') }}">My Events</a></div>
                        <div class="column"><a class="button dashboard-nav-button dashboard-page-active" href="{{ route('dashboard.publishedPages') }}">My Pages</a></div>
                        <div class="column"><a class="button dashboard-nav-button" href="{{ route('dashboard.inviteLists.show') }}">Invite Lists</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<section class="content dashboard">
    <div class="row">
        <div class="small-12 columns pages my-pages">
            <ul class="menu page-mode-menu">
                <li><a href="{{ route('dashboard.publishedPages') }}">Published</a></li>
                <li class="active {{ ($pages->count() > 0) ? '' : 'list-disabled' }}"><a href="{{ route('dashboard.savedPages') }}">Saved</a></li>
            </ul>
            @forelse($pages as $page)
            @include('dashboard.savedPage_row')
            @empty
            <h5 class="no-pages-found">No pages found.</h5>
            @endforelse
        </div>
    </div>
    @if($pages->hasMorePages())
    <div class="row">
        <div class="small-12 columns">
            <button class="dashboard-pages-load-more load-more" data-request-url="{{ route('dashboard.savedPages') }}" data-next-page="{{ $pages->currentPage()+1 }}" data-last-page="{{ $pages->lastPage() }}" data-left-off="{{ $pages->last()->id }}"><span class="fa fa-refresh"></span>Load more</button>
            <script type="text/javascript">
                var loadMoreParameters = "{!! '?'.http_build_query(Request::query()) !!}";
            </script>
        </div>
    </div>
    @endif
</section>
@endsection