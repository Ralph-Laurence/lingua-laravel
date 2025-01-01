<!DOCTYPE html>
<html>
<head>
    <title>Sign Lingua Registration</title>
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
        <!-- Include the user's name -->
        <p>Welcome to the SignLingua ASL community, {{ $emailData['firstname'] }}!</p>
        <p>Your registration has been <b>approved</b>!</p>
        <p style="font-size: 12px;">You might need to log out and log in again for the changes to take effect.</p>
        <!-- Login Button -->
        <p style="margin-top: 40px;">
            <a href="{{ $emailData['login'] }}" style="display: inline-block; padding: 10px 20px; font-size: 16px; color: #ffffff; background: #007bff; text-decoration: none; border-radius: 5px;">Login to Your Account</a>
        </p>
    </div>
</body>
</html>
