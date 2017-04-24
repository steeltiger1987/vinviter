@extends('layouts.master_basic')

@section('title', 'Log in')

@section('body')
<div id="wrapper" class="login">
    @include('layouts.header')

    <section class="content">
        <div class="row">
            <div class="small-4 small-centered columns">
            @if(Session::has('auth_verified'))
                <div class="callout secondary no-border">
                    <p>Your account is verified, you may login now.</p>
                </div>
            @endif
            @if(Session::has('password_changed'))
                <div class="callout secondary no-border">
                    <p>{{ Session::get('password_changed') }}</p>
                </div>
            @endif
                <div class="account-panel">
                    <div class="panel-top">
                        <h3><span class="fa fa-sign-in"></span>Log in</h3>
                    </div>
                    <div class="panel-body">
                        <form action="{{ route('auth.login') }}" method="POST" name="login" data-abide novalidate>
                            {!! csrf_field() !!}
                            <label>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
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
                            <label class="remember-me"><input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>Remember me</label>
                            <p><input type="submit" name="submit" class="button expanded" value="Log in"></p>
                        </form>
                        <a class="forgot-password" href="{{ route('auth.reset') }}">Forgot password?</a>
                    </div>
                    <span class="dotted-hr"></span>
                    <div class="panel-bottom-text">
                        <p class="log-reg-here">New to Vinviter? <a href="{{ route('auth.register') }}" title="Sign up">Sign up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.footer')
    <script src="{{ asset('js/all.js') }}"></script>
</div>
@endsection