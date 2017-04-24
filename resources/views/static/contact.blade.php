@extends('layouts.master_basic')

@section('title', 'Contact Us')

@section('body')
<div id="wrapper" class="page-bg-gray">
    @include('layouts.header')
    <section class="single-page-content">
        <div class="row">
            <div class="small-10 columns small-centered">
                <div class="single-page-container">
                    <h1>Contact Us</h1>
                    @if(Session::has('success'))
                    <div class="callout success">
                        <p>{{ Session::get('success') }}</p>
                    </div>
                    @endif
                    <form name="contact" method="POST" action="{{ route('app.pages.postContact') }}">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="small-3 columns">
                                <label for="name">Name:</label>
                            </div>
                            <div class="small-7 columns float-left">
                                <input type="text" name="name" id="name" value="{{ old('name', $name) }}" {{ ($name) ? 'readonly' : '' }}>
                                {!! $errors->first('name', '<span class="form-error is-visible">:message</span>') !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="small-3 columns">
                                <label for="email">Email:</label>
                            </div>
                            <div class="small-7 columns float-left">
                                <input type="email" name="email" id="email" value="{{ old('email', $email) }}" {{ ($email) ? 'readonly' : '' }}>
                                {!! $errors->first('email', '<span class="form-error is-visible">:message</span>') !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="small-3 columns">
                                <label for="message">Message:</label>
                            </div>
                            <div class="small-7 columns float-left">
                                <textarea id="message" name="message" rows="5">{{ old('message') }}</textarea>
                                {!! $errors->first('message', '<span class="form-error is-visible">:message</span>') !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="small-7 small-offset-3 columns">
                                <input type="submit" name="submit" value="Send" class="button">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @include('layouts.footer')
    <script src="{{ asset('js/all.js') }}"></script>
</div>
@endsection