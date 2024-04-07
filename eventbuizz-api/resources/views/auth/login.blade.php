<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zapier</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;1,400;1,700&display=swap" rel="stylesheet">
    {{ HTML::style('_zapier_assets/css/style.css') }}
</head>

<body>
    <div id="wrapper">
        <div class="login-container">
            <div class="logo-container">
                {{ HTML::image('_zapier_assets/images/logo.jpeg') }}
            </div>
            <div class="heading-container">
                Sign in to Dropbox to link with Zapier
            </div>
            <!-- <div class="login-form-google">
                <button>
                    <span>Sign in with Google</span>
                </button>
            </div>
            <div class="hr-label">
                <span class="text">or</span>
            </div> -->
{{--            <form class="form-horizontal" method="POST" action="{{ route('zapier-auth-post-login') }}">--}}
            <form class="form-horizontal" method="POST">
                {{ csrf_field() }}
                <div class="login-container-form">
                    <div class="input-field">
                        <span class="error-box"></span>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                        @if ($errors && $errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="input-field">
                        <span class="error-box"></span>
                        <input id="password" type="password" class="form-control" name="password" required>
                        @if ($errors && $errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="recaptcha-terms-text">This page is protected by reCAPTCHA, and subject to the Google<a href="https://www.google.com/policies/privacy/" target="_blank" rel="noreferrer"> Privacy Policy </a>and<a href="https://www.google.com/policies/terms/" target="_blank" rel="noreferrer"> Terms of Service</a>.
                    </div>
                    <div class="login-form-bottom">
                        <!-- <div class="login-need-help">
                            <a href="/forgot">Forgot your password?</a>
                        </div> -->
                        <div class="button-area">
                            <button type="submit">Sign in</button>
                        </div>
                    </div>
                </div>
            </form>
            <!-- <div class="login-register-switch">
                <a href="#" class="login-register-switch-link">New to Dropbox? Create an account</a>
            </div> -->
        </div>
    </div>
</body>

</html>