@php
use App\Models\Reservation;

$messages = [
Reservation::STATUS_NEW => [
'title' => '📩 Reservation Request Sent',
'text' => 'We’ve received your reservation request and will confirm it shortly.',
'message' => "We appreciate your interest in dining with us. We will review your request and get back to you as soon as possible. If you have any special requests or need to make changes, please feel free to contact us."
],
Reservation::STATUS_CONFIRMED => [
'title' => '🍷 Your Table Is Confirmed ',
'text' => 'Your table at Tifliso Georgian Restaurant has been confirmed! We look forward to welcoming you.',
'message' => "If you need to make any changes to your booking or have special requests, please don’t hesitate to contact us.
We look forward to hosting you soon!"
],
Reservation::STATUS_DECLINED => [
'title' => '❌ Your Booking Declined',
'text' => 'Your reservation has been declined. If this is a mistake, please contact us.',
'message' => "We apologize for any inconvenience. Please feel free to reach out if you have any questions or would like to make a new reservation."
],
Reservation::STATUS_COMPLETE => [
'title' => '✅ Thank You for Visiting',
'text' => 'Thank you for dining with us. We hope to see you again soon!',
'message' => "We hope you had a wonderful experience at Tifliso Georgian Restaurant. We look forward to welcoming you back in the future!"
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
                            <p>Reservation details:</p>
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
                                📍 {{ config('app.LOCATION') }}<br>
                                📞 {{ config('app.CONTACT_NUMBER') }}
                            </p>
                            <p>{{ $content['message'] }}</p>

                            <p><strong>Tifliso Team</strong></p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>

</html>