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
        <div class="login-container popup-container">
            <div class="logo-container">
                {{ HTML::image('_zapier_assets/images/logo.jpeg') }}
            </div>
            <div class="inner-popup-wrapp">
                <div class="content-popup-wrapp">
                    <p>
                        <strong>{{ $client->name }}</strong> is requesting permission to access your account.
                    </p>
                    <!-- Scope List -->
                    @if (count($scopes) > 0)
                    <div class="scopes">
                        <p><strong>This application will be able to:</strong></p>

                        <ul>
                            @foreach ($scopes as $scope)
                            <li>{{ $scope->description }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="button-panel">
                        <!-- Cancel Button -->
                        <form method="post" action="/oauth/authorize">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}

                            <input type="hidden" name="state" value="{{ $request->state }}">
                            <input type="hidden" name="client_id" value="{{ $client->id }}">
                            <button type="submit" class="btn">Cancel</button>
                        </form>
                        <!-- Authorize Button -->
                        <form method="post" action="/oauth/authorize">
                            {{ csrf_field() }}
                            <input type="hidden" name="state" value="{{ $request->state }}">
                            <input type="hidden" name="client_id" value="{{ $client->id }}">
                            <button type="submit" class="btn btn-submit">Authorize</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>