<!DOCTYPE html>
<html>

<body style="font-family: Arial; background:#f8f8f8; padding:20px">
    <div style="max-width:600px; background:#fff; padding:20px; border-radius:8px">

        <h2>🍷 Table Reservation Confirmed</h2>

        <p>Hello {{ $reservation->customer_name }},</p>

        <p>Thank you for booking a table at <strong>Tifliso Georgian Restaurant</strong>.</p>

        <table style="width:100%; margin-top:15px">
            <tr>
                <td><strong>Date:</strong></td>
                <td>{{ $reservation->visit_date }}</td>
            </tr>
            <tr>
                <td><strong>Time:</strong></td>
                <td>{{ $reservation->visit_time }}</td>
            </tr>
            <tr>
                <td><strong>Guests:</strong></td>
                <td>{{ $reservation->guests }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>{{ ucfirst($reservation->status) }}</td>
            </tr>
        </table>

        <p style="margin-top:20px">
            📍 Budapest, Hungary<br>
            📞 +36 30 123 4567
        </p>

        <p>We look forward to welcoming you!</p>

        <p><strong>Tifliso Team</strong></p>
    </div>
</body>

</html>