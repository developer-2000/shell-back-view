<!DOCTYPE html>
<html>
<head>
    <title>Distributor Tracker Update</title>
</head>
<body>
<p>Dear {{ $userName }},</p>
<p>The distributor has sent tracker numbers to the following users in relation to the promotion: <b>{{ $promotionName }}</b></p>

<p>The list of users is as follows:</p>
<ul>
    @foreach ($userNames as $name)
        <li>{{ $name }}</li>
    @endforeach
</ul>

<p>Thank you!</p>
</body>
</html>
