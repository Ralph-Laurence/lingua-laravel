<!DOCTYPE html>
<html>
<head>
    <title>Sign Lingua Hire Tutor</title>
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
        <p>{{ $emailData['name'] }} would like to hire you!</p>
        <!-- Login Button -->
        <p style="margin-top: 40px;">
            <a href="{{ $emailData['action'] }}" style="display: inline-block; padding: 10px 20px; font-size: 16px; color: #ffffff; background: #007bff; text-decoration: none; border-radius: 5px;">View Details</a>
        </p>
    </div>
</body>
</html>
