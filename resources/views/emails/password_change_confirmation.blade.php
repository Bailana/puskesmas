<!DOCTYPE html>
<html>
<head>
    <title>Confirm Your Password Change</title>
</head>
<body>
    <p>Hi {{ $name }},</p>
    <p>You requested to change your password. Please confirm this change by clicking the link below:</p>
    <p><a href="{{ $url }}">Confirm Password Change</a></p>
    <p>If you did not request this change, please ignore this email.</p>
    <p>Thank you,<br/>Your Application Team</p>
</body>
</html>
