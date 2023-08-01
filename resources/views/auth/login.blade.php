@extends('auth.layouts.app')
@section('content')
    <div class="m-login__signin">
        <div class="m-login__head">
            <h3 class="m-login__title">Sign In To Admin</h3>
        </div>
        <form class="m-login__form m-form" method="POST" action="{{ route('login') }}">
            @if (session('status'))
                <div class="m-alert m-alert--outline alert alert-success alert-dismissible animated fadeIn"
                     role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    </button>
                    <span>{{ session('status') }}</span>
                </div>
            @endif
            {{ csrf_field() }}
            <div class="form-group m-form__group {{ $errors->has('email') ? ' has-danger' : '' }}">
                <input id="email" class="form-control m-input" type="text" placeholder="Email" name="email"
                       value="{{ old('email') }}" autocomplete="off">
                @if ($errors->has('email'))
                    <div id="email-error" class="form-control-feedback">{{ $errors->first('email') }}</div>
                @endif
            </div>
            <div class="form-group m-form__group {{ $errors->has('password') ? ' has-danger' : '' }}">
                <input id="password" class="form-control m-input m-login__form-input--last" type="password"
                       placeholder="Password" name="password">
                @if ($errors->has('password'))
                    <div id="password-error"
                         class="form-control-feedback">{{ $errors->first('password') }}</div>
                @endif
            </div>
            <div class="row m-login__form-sub">
                <div class="col m--align-left m-login__form-left">
                    <label class="m-checkbox  m-checkbox--focus">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Remember me
                        <span></span>
                    </label>
                </div>
                <div class="col m--align-right m-login__form-right">
                    <a href="javascript:;" id="m_login_forget_password" class="m-link">Forget Password ?</a>
                </div>
            </div>
            <div class="m-login__form-action">
                <button id="m_login_signin_submit"
                        class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary">
                    Sign In
                </button>
            </div>
        </form>
    </div>
    <div class="m-login__forget-password">
        <div class="m-login__head">
            <h3 class="m-login__title">Forgotten Password ?</h3>
            <div class="m-login__desc">Enter your email to reset your password:</div>
        </div>
        <form class="m-login__form m-form" method="POST" action="{{ route('password.email') }}">
            {{ csrf_field() }}
            <div class="form-group m-form__group {{ $errors->has('email') ? ' has-danger' : '' }}">
                <input class="form-control m-input" type="text" placeholder="Email" name="email"
                       id="m_email" value="{{ old('email') }}" autocomplete="off">
                @if ($errors->has('email'))
                    <div id="m_email-error" class="form-control-feedback">
                        {{ $errors->first('email') }}
                    </div>
                @endif
            </div>
            <div class="m-login__form-action">
                <button id="m_login_forget_password_submit"
                        class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air  m-login__btn m-login__btn--primaryr">
                    Request
                </button>&nbsp;&nbsp;
                <button id="m_login_forget_password_cancel"
                        class="btn btn-outline-focus m-btn m-btn--pill m-btn--custom m-login__btn">Cancel
                </button>
            </div>
        </form>
    </div>
@endsection