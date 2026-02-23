<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>We received your message</title>
</head>

<body style="font-family: Arial, sans-serif; line-height:1.6;">

    <h2>Dear {{ $contact->name }},</h2>

    <p>
        Thank you for contacting <strong>Tifliso Georgian Restaurant</strong>.
        We have received your message and our team will get back to you shortly.
    </p>

    <p><strong>Your message:</strong></p>

    <blockquote style="background:#f7f7f7; padding:10px;">
        {{ $contact->message }}
    </blockquote>

    <p>
        If your enquiry is urgent, please contact us directly:
    </p>

    <p>
        📞 +36 20 581 1111 <br>
        📧 tifliszorestaurant@gmail.com
    </p>

    <p>
        Kind regards,<br>
        <strong>Tifliso Georgian Restaurant</strong>
    </p>

</body>

</html>