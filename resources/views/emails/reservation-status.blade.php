@php
use App\Models\Reservation;

$messages = [
Reservation::STATUS_NEW => [
'title' => '📩 Reservation Request Sent',
'text' => 'We’ve received your reservation request and will confirm it shortly.',
],
Reservation::STATUS_CONFIRMED => [
'title' => '🍷 Table Reservation Confirmed',
'text' => 'Your table has been confirmed. We look forward to welcoming you!',
],
Reservation::STATUS_CANCELLED => [
'title' => '❌ Reservation Cancelled',
'text' => 'Your reservation has been cancelled. If this is a mistake, please contact us.',
],
Reservation::STATUS_COMPLETE => [
'title' => '✅ Thank You for Visiting',
'text' => 'Thank you for dining with us. We hope to see you again soon!',
],
];

$content = $messages[$reservation->status];
@endphp
<!DOCTYPE html>
<html>

<body style="margin:0; padding:0; background:#f8f8f8;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:20px;">
        <tr>
            <td align="center">

                <table width="600" style="background:#fff;padding:24px;border-radius:8px;font-family:Arial">
                    <tr>
                        <td align="center">
                            <h2>{{ $content['title'] }}</h2>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p>Hello <strong>{{ $reservation->customer_name }}</strong>,</p>

                            <p>{{ $content['text'] }}</p>

                            <table width="100%" cellpadding="6">
                                <tr>
                                    <td><strong>Date:</strong></td>
                                    <td align="right">{{ $reservation->visit_date }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Time:</strong></td>
                                    <td align="right">{{ $reservation->visit_time }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Guests:</strong></td>
                                    <td align="right">{{ $reservation->guests }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td align="right">{{ ucfirst($reservation->status) }}</td>
                                </tr>
                            </table>

                            <p style="margin-top:20px;">
                                📍 Budapest, Hungary<br>
                                📞 +36 30 123 4567
                            </p>

                            <p><strong>Tifliso Team</strong></p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>

</html>