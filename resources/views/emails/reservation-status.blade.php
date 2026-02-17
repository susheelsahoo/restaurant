@php
use App\Models\Reservation;

$messages = [
'pending' => [
'title' => '📩 Reservation Request Sent',
'text' => 'We’ve received your reservation request and will confirm it shortly.',
'message' => "We appreciate your interest in dining with us. We will review your request and get back to you as soon as possible. If you have any special requests or need to make changes, please feel free to contact us."
],
'confirmed' => [
'title' => '🍷 Your Table Is Confirmed ',
'text' => 'Your table at Tifliso Georgian Restaurant has been confirmed!',
'message' => "If you need to make any changes to your booking or have special requests, please don’t hesitate to contact us.<br>
We look forward to hosting you soon!"
],
'canceled' => [
'title' => '❌ Your Booking Canceled',
'text' => 'We would like to confirm that your reservation at Tifliso Georgian Restaurant has been cancelled as requested.
Cancelled reservation details:',
'message' => "While we regret not having the pleasure of welcoming you on this occasion, we sincerely hope to host you for a future dining experience.
<br />
Should you wish to reserve another table or require any assistance, our team will be delighted to help.
<br />
📍 Location: <a href='{{ config('app.GOOGLE_MAPS') }}'>Budapest, Ráday utca 11, Budapest, Hungary</a><br />
📞 Contact: <a href='tel:+36301234567'>+36 30 123 4567</a><br />
Thank you for your understanding. We hope to welcome you soon!"
],
'in-house' => [
'title' => '🎉 Customer Arrived',
'text' => 'We have registered that you have arrived at Tifliso Georgian Restaurant.',
'message' => "Welcome to Tifliso Georgian Restaurant! We hope to provide you with an exceptional dining experience. Enjoy your meal! 🍷🇬🇪"
],
'complete' => [
'title' => '✅ Thank You for Visiting',
'text' => 'Thank you for dining with us. We hope to see you again soon!',
'message' => "Thank you for dining with us! We’re happy to share Georgian hospitality with you and hope to see you again soon 🍷🇬🇪
Your feedback means a lot to us—feel free to leave a review here: <br />
<a href='https://g.page/r/CcEqwhtiszcgEAE/review'>Google Reviews</a>."
],
];

$statusName = $reservation->reservationStatus?->name ?? 'pending';
$content = $messages[$statusName] ?? $messages['pending'];
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
                            <p>Hello <strong>{{ ucfirst($reservation->customer->first_name ?? 'Guest') }}</strong>,</p>

                            <p>{{ $content['text'] }}</p>
                            <p>Reservation details:</p>
                            <table width="100%" cellpadding="6">
                                <tr>
                                    <td><strong>Date:</strong></td>
                                    <td align="right">{{ \Carbon\Carbon::parse($reservation->visit_date)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Time:</strong></td>
                                    <td align="right">{{ \Carbon\Carbon::parse($reservation->visit_time)->format('H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Guests:</strong></td>
                                    <td align="right">{{ $reservation->guests }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td align="right">{{ $reservation->reservationStatus?->label ?? 'Unknown' }}</td>
                                </tr>
                            </table>
                            <p>{!! $content['message'] !!}</p><br />
                            <p>Warm regards,</p>
                            <p><strong>Tifliso Team</strong></p>
                            <p>Authentic Georgian Restaurant in Budapest.</p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>

</html>