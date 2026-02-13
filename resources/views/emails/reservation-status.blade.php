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
'text' => 'Your table at Tifliso Georgian Restaurant has been confirmed!',
'message' => "If you need to make any changes to your booking or have special requests, please don’t hesitate to contact us.<br>
We look forward to hosting you soon!"
],
Reservation::STATUS_DECLINED => [
'title' => '❌ Your Booking Declined',
'text' => 'For the given date and time, all of our tables are fully booked. We’re very sorry for the inconvenience—maybe another time would work for you?',
'message' => "We’d be happy to help you find an alternative time or date. Please feel free to contact us, and we’ll do our best to assist.<br />
📍 Location: <a href='{{ config('app.GOOGLE_MAPS') }}'>Budapest, Ráday utca 11, Budapest, Hungary</a><br />
📞 Contact: <a href='tel:+36301234567'>+36 30 123 4567</a><br />
Thank you for your understanding. We hope to welcome you soon!"
],
Reservation::STATUS_COMPLETE => [
'title' => '✅ Thank You for Visiting',
'text' => 'Thank you for dining with us. We hope to see you again soon!',
'message' => "Thank you for dining with us! We’re happy to share Georgian hospitality with you and hope to see you again soon 🍷🇬🇪
Your feedback means a lot to us—feel free to leave a review here: <br />
<a href='https://g.page/r/CcEqwhtiszcgEAE/review'>Google Reviews</a>."
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
                                    <td align="right">{{ ucfirst($reservation->status) }}</td>
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