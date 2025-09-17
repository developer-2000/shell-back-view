<!DOCTYPE html>
<html>
<head>
    <title>Order Shipped</title>
</head>
<body>
<p>Dear {{ $companyName }},</p>
<p>We have shipped your order for the campaign <b>{{ $promotionName }}</b>.</p>
<p>The following surfaces were included in your shipment: <b>{{ $surfaceNames }}</b>.</p>
<p>Here is your tracking number: <a href="{{ $trackingLink }}">{{ $trackingLink }}</a></p>
<p>Thank you for your cooperation!</p>
</body>
</html>
