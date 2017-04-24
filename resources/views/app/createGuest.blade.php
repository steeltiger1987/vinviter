@extends('layouts.master_basic')

@section('title', 'Create')

@section('body')
<div id="wrapper" class="bg-gray">
    @include('layouts.header')

    <section class="content">
        <div class="create-options">
            <div class="row">
                <div class="small-6 columns small-centered">
                    <h3 class="heading">You need to log in or<br>sign up to create an event or page</h3>
                </div>
            </div>
            <div class="row">
                <div class="small-3 small-offset-1 columns">
                    <span class="fa fa-sign-in fa-2x fa-fw"></span>
                    <h4>Already have account?</h4>
                    <a href="{{ route('auth.login') }}" class="button">Log in</a>
                </div>
                <div class="small-4 columns">
                    <div class="or-seperator">
                        <div class="line"></div>
                        <div class="wordwrapper">
                            <div class="word">or</div>
                        </div>
                    </div>
                </div>
                <div class="small-3 float-left columns">
                    <span class="fa fa-user-plus fa-2x fa-fw"></span>
                    <h4>New to Vinviter?</h4>
                    <a href="{{ route('auth.register') }}" class="button">Sign up</a>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.footer')
    <script src="{{ asset('js/all.js') }}"></script>
</div>
@endsection