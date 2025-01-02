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
        <p style="text-align: start; margin-top: 30px; margin-bottom: 10px;">Dear {{ $emailData['firstname'] }},<br></p>
        <p>
            We're sorry to inform you that your registration as a tutor on SignLingua ASL Community has been <b>declined</b> by our administrators.
        </p>
        <p>However, you are welcome to reapply by providing valid documentary proof of your identity. Please ensure that your documentation is complete and accurate, as this will help us confirm your identity during the review process.</p>
        <p style="text-align: start; margin-top: 30px;">Best Regards,<br>SignLingua Support</p>
    </div>
</body>
</html>
