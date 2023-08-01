@extends('auth.layouts.app')
@section('content')
    <div class="m-login__reset-password">
        <div class="m-login__head">
            <h3 class="m-login__title">Reset Password</h3>
            <div class="m-login__desc">Enter your new password:</div>
        </div>
        <form class="m-login__form m-form" method="POST" action="{{ route('password.request') }}">
            {{ csrf_field() }}
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group m-form__group {{ $errors->has('email') ? ' has-danger' : '' }}">
                <input class="form-control m-input" type="text" placeholder="Email" name="email"
                       value="{{ $email or old('email') }}" autocomplete="off">
                @if ($errors->has('email'))
                    <div id="email-error" class="form-control-feedback">{{ $errors->first('email') }}</div>
                @endif
            </div>
            <div class="form-group m-form__group {{ $errors->has('password') ? ' has-danger' : '' }}">
                <input class="form-control m-input" type="password" placeholder="Password" name="password">
                @if ($errors->has('password'))
                    <div id="password-error"
                         class="form-control-feedback">{{ $errors->first('password') }}</div>
                @endif
            </div>
            <div class="form-group m-form__group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <input class="form-control m-input m-login__form-input--last" type="password"
                       placeholder="Confirm Password" name="password_confirmation">
                @if ($errors->has('password_confirmation'))
                    <div id="rpassword-error"
                         class="form-control-feedback">{{ $errors->first('password_confirmation') }}</div>
                @endif
            </div>

            <div class="m-login__form-action">
                <button id="m_login_reset-password_submit"
                        class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air  m-login__btn">Reset Password
                </button>&nbsp;&nbsp;
            </div>
        </form>
    </div>
@endsection