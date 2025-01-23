<!DOCTYPE html>
<html>
<head>
    <title>Email Update Verification</title>
    <style>
        html, body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #22223b;
            width: 100%;
            height: 100%;
            margin: 0;
            text-align: center;
        }

    </style>
</head>
<body>
    <div class="mail-body-container" style="max-width: 400px; border: 1px solid rgb(182, 182, 182); margin: auto; padding: 20px;">
        <img src="{{ $message->embed($emailData['logo']) }}" alt="Company Logo">
        <h3>Verify your email address</h3>
        <p>You entered <b>{{ $emailData['newEmail'] }}</b> as the email address for your account.</p>
        <p style="font-size: 12px;">Please verify this email by clicking the button below.</p>
        <p>Your verification code is <b>{{ $emailData['code'] }}</b></p>
        <!-- Verify Button -->
        <p style="margin-top: 40px;">
            <a href="{{ $emailData['action'] }}" style="display: inline-block; padding: 10px 20px; font-size: 16px; color: #ffffff; background: #007bff; text-decoration: none; border-radius: 5px;">Verify Email</a>
        </p>
    </div>
</body>
</html>
