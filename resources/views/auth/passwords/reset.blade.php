@extends('layouts.master_basic')

@section('title', 'Reset Password')

@section('body')
<div id="wrapper" class="login">
    @include('layouts.header')

    <section class="content">
        <div class="row">
            @if(Session::has('status'))
            <div class="small-6 small-centered columns">
                <div class="callout success no-border">
                    <p>{{ Session::get('status') }}.</p>
                </div>
            </div>
            @else
            <div class="small-4 small-centered columns">
                <div class="account-panel">
                    <div class="panel-top">
                        <h4><span class="fa fa-key"></span> <strong>Reset Password</strong></h4>
                    </div>
                    <div class="panel-body">
                        <form action="{{ route('auth.reset') }}" method="POST" name="resetPassword" data-abide novalidate>
                            {!! csrf_field() !!}
                            <input type="hidden" name="token" value="{{ $token }}">
                            <label>
                                <input type="email" name="email" value="{{ $email or old('email') }}" placeholder="Email" required>
                                @if($errors->has('email'))
                                <span class="form-error is-visible server-side-error">{{ $errors->first('email') }}</span>
                                @endif
                                <span class="form-error">Please enter an email address.</span>
                            </label>
                            <label>
                                <input type="password" name="password" placeholder="Password" required>
                                @if($errors->has('password'))
                                <span class="form-error is-visible server-side-error">{{ $errors->first('password') }}</span>
                                @endif
                                <span class="form-error">Password is required.</span>
                            </label>
                            <label>
                                <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                                @if($errors->has('password_confirmation'))
                                <span class="form-error is-visible server-side-error">{{ $errors->first('password_confirmation') }}</span>
                                @endif
                                <span class="form-error">Confirm Password is required.</span>
                            </label>
                            <p><input type="submit" name="submit" class="button expanded" value="Reset Password"></p>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>

    @include('layouts.footer')
    <script src="{{ asset('js/all.js') }}"></script>
</div>
@endsection
