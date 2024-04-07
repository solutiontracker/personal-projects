<!doctype html>

<html>
<head>
    <meta charset="utf-8">
    <title>{{ env('APP_NAME') }} - Agent Forgot Password Email</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
</head>

<body style="margin:0px; padding:0px; background:#fff;">

    <h1>Reset Your Password</h1>

    <p>Click on the following link to reset your password:</p>

    <h4 style="
                                    margin: auto;
                                    text-align: center;
                                    font-size: 28px;
                                    line-height: 1;
                                    letter-spacing: 7px;
                                    display: block;
                                    font-weight: bold;
                                    max-width: 600px;
                                    ">
        {{ $resetCode }}</h4>
    <p>If you didn't request a password reset, you can safely ignore this email.</p>

</body>
</html>


