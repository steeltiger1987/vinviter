@extends('layouts.master')

@section('title', 'Published pages - Dashboard')

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
            <div class="row">
                <div class="small-6 columns">
                    <ul class="menu page-mode-menu">
                        <li class="active"><a href="{{ route('dashboard.publishedPages') }}">Published</a></li>
                        <li {{ (Auth::user()->savedPages()->count() > 0) ? '' : 'class=list-disabled' }}><a href="{{ route('dashboard.savedPages') }}">Saved</a></li>
                    </ul>

                </div>
                <div class="small-6 columns">
                    <ul class="menu page-type-mode-menu float-right">
                        <li class="active"><a href="{{ route('dashboard.publishedPages') }}">Creator</a></li>
                        <li {{ (Auth::user()->adminOfPages()->count() > 0) ? '' : 'class=list-disabled' }}><a href="{{ route('dashboard.publishedPages.typeAdmin') }}">Admin</a></li>
                    </ul>
                </div>
            </div>

            @forelse($pages as $page)
            @include('dashboard.creatorPage_row')
            @empty
            <h5 class="no-pages-found">No pages found. Click on (Create) to make a new one.</h5>
            @endforelse
        </div>
    </div>
    @if($pages->hasMorePages())
    <div class="row">
        <div class="small-12 columns">
            <button class="dashboard-pages-load-more load-more" data-request-url="{{ route('dashboard.publishedPages') }}" data-next-page="{{ $pages->currentPage()+1 }}" data-last-page="{{ $pages->lastPage() }}" data-left-off="{{ $pages->last()->id }}"><span class="fa fa-refresh"></span>Load more</button>
            <script type="text/javascript">
                var loadMoreParameters = "{!! '?'.http_build_query(Request::query()) !!}";
            </script>
        </div>
    </div>
    @endif
</section>
@endsection