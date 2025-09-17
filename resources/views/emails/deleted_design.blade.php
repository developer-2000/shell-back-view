<!DOCTYPE html>
<html>
<head>
    <title>Design Removed from Promotion</title>
    <style>
        .design-name {
            font-weight: bold;
            color: #ff0000; /* Красный цвет */
        }
    </style>
</head>
<body>
<p>Dear {{ $userName }}</p>

<p>We would like to inform you that the design <span class="design-name">{{ $designName }}</span> has been removed from the promotion.</p>

<ul>
    <li><strong>Promotion:</strong> {{ $promotionName }}</li>
    <li><strong>Surface:</strong> {{ $surfaceName }}</li>
</ul>

<p>You can view the updated promotion details by clicking the link below:</p>
<p><a href="{{ $promotionLink }}">View Promotion</a></p>

<p>Best regards,<br> Your Team</p>
</body>
</html>
