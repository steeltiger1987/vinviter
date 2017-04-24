@extends('layouts.master_basic')

@section('title', 'Start creating events or pages')

@section('body')
<div id="wrapper" class="bg-gray">
    @include('layouts.header')

    <section class="content">
        <div class="create-options">
            <div class="row">
                <div class="small-6 columns small-centered">
                    <h3 class="heading">Start creating events or pages</h3>
                </div>
            </div>
            <div class="row">
                <div class="small-5 columns">
                    <h3 class="event-page-heading">Events</h3>
                    <h4>Create any type of event, ranging from common parties to major  festivals.<br>Choose to make the event public or<br>private</h4>
                    <a href="{{ route('events.create') }}" class="button">Create</a>
                </div>
                <div class="small-2 columns">
                    <div class="or-seperator">
                        <div class="line"></div>
                        <div class="wordwrapper">
                            <div class="word">or</div>
                        </div>
                    </div>
                </div>
                <div class="small-5 columns">
                    <h3 class="event-page-heading">Pages</h3>
                    <h4>Create a Page for your venue or<br>event organization. Keep your fans<br>and supporters updated on all of<br>your upcoming and past events</h4>
                    <a href="{{ route('pages.create') }}" class="button">Create</a>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.footer')
    <script src="{{ asset('js/all.js') }}"></script>
</div>
@endsection