<!DOCTYPE html>
<html>
<head>
    <title>New Promotion</title>
</head>
<body>
<p>Hello {{ $userName }},</p>

<p>A new promotion has been launched, and you will soon receive the following designs on various surfaces:</p>

@foreach($properties as $propertyName => $items)
    <h3>{{ $propertyName }}</h3>
    <ul>
        @foreach($items as $design => $quantity)
            <li>{{ $design }}: {{ $quantity }} design(s)</li>
        @endforeach
    </ul>
@endforeach

<p>You can view all details of the promotion and its status in your personal cabinet at the following link:</p>
<p><a href="{{ $link }}">Go to your promotions</a></p>

<p>Best regards,</p>
<p>Your Team</p>
</body>
</html>
