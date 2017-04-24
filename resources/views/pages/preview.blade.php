@extends('layouts.master')

@section('title', 'Preview of your page')

@section('head')
@if($page->background_image)
<style type="text/css">
  .header-image{
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    background-image: url({{ url("uploads/pages/".$page->id.'/images/'.$page->background_image) }});
  }
</style>
@else
<style type="text/css">
  .header-image{
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    background-image: url({{ asset("uploads/default/page/all/background.png") }});
  }
</style>
@endif
@endsection

@section('content')
<section class="page">
    <div class="header-image">
        <div class="header-content">
            <div class="row">
                <div class="small-12 columns">
                    <div class="followers">
                        <div class="followers-text">
                            <span>0</span>
                            <span>Followers</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="small-7 columns small-centered">
                    <img class="main-image" src="{{ url('images/small133/'.$page->mainImageFullPath) }}" alt="">
                    <p><strong>{{ $page->name }}</strong></p>
                    <p>{{ '@'.$page->slug }}</p>
                    <p class="status">{{ $page->status }}</p>
                    <div class="row">
                        @if(!$page->address)
                        <div class="small-12 columns">
                            <span class="db text-center">
                                <i class="fa fa-map-marker"></i>
                                {{ $page->city->name.', '.$page->country->name }}
                            </span>
                        </div>
                        @else
                        <div class="small-5 small-offset-2 columns">
                            <span>
                                <i class="fa fa-location-arrow"></i>
                                {{ $page->address }}
                            </span>
                        </div>
                        <div class="small-5 columns">
                            <span>
                                <i class="fa fa-map-marker"></i>
                                {{ $page->city->name.', '.$page->country->name }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="overlay"></div>
    </div>
    <div class="page-tabs">
        <div class="row">
            <div class="small-12 columns">
                <ul class="tabs" data-tabs id="page-tabs">
                    <li class="tabs-title is-active"><a href="#full-info">Full Info</a></li>
                    <li class="tabs-title tab-disabled"><a>Upcoming</a></li>
                    <li class="tabs-title tab-disabled"><a>History</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-11 columns small-centered">
            <div class="page-tabs-content" data-tabs-content="page-tabs">
                <div class="page-tabs-panel is-active" id="full-info">
                    <?php $attributes = $page->attributes->groupBy('type'); ?>
                    <dl>
                      @if(isset($attributes['page.type']))
                      <dt>Type</dt>
                      <dd>{{ $attributes['page.type'][0]->name }}</dd>
                      @endif

                      @if(isset($attributes['page.year']))
                      <dt>Year Founded</dt>
                      <dd>{{ $attributes['page.year'][0]->name }}</dd>
                      @endif

                      @if(isset($attributes['page.activity_period']))
                      <dt>Activity Period</dt>
                      <dd>{{ $attributes['page.activity_period'][0]->name }}</dd>
                      @endif

                      @if(isset($attributes['page.season']))
                      <dt>Season</dt>
                      <dd>{{ $attributes['page.season'][0]->name }}</dd>
                      @endif

                      @if(count($page->keyPeople))
                      <dt>Key People</dt>
                      <dd>
                          @foreach($page->keyPeople as $key => $person)
                          <a href="{{ route('user.profile', $person->username) }}" target="_blank" class="key-people-list">{{ $person->name }}</a><span></span>
                          @endforeach
                      </dd>
                      @endif

                      <dt>Story</dt>
                      <dd>{!! nl2br(e($page->story)) !!}</dd>
                  </dl>
              </div>
          </div>
      </div>
      <form name="preview" action="{{ route('pages.publish', $page->slug) }}" method="POST">
        {!! csrf_field() !!}
        <div class="small-4 columns float-right">
            <div class="row small-up-2">
                <div class="column">
                    <a href="{{ route('pages.edit', $page->slug) }}" title="Go back to the form" class="button back expanded">Back</a>
                </div>
                <div class="column">
                    <input type="hidden" name="id" value="{{ $page->id }}">
                    <input type="submit" name="publish" class="button yellow expanded" title="Publish the page" value="Publish">
                </div>
            </div>
        </div>
    </form>
</div>
</section>
@endsection