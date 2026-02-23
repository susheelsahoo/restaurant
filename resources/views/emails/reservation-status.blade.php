<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Status</title>
</head>

<body style="margin:0; padding:0; background:#f8f8f8; font-family: Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:20px;">
        <tr>
            <td align="center">
                <table width="600" style="background:#fff; padding:24px; border-radius:8px; font-family:Arial, sans-serif;">
                    <tr>
                        <td align="center">
                            <h2 style="margin: 0 0 20px 0; color: #333;">{{ $template->title }}</h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="color: #555; line-height: 1.6;">
                            <p>Dear <strong>{{ ucfirst($reservation->customer->first_name ?? 'Guest') }}</strong>,</p>

                            <p>{{ $template->short_text }}</p>

                            <p style="font-weight: bold; color: #333;">Reservation Details:</p>
                            <table width="100%" cellpadding="8" style="border: 1px solid #ddd; border-radius: 4px;">
                                <tr style="background: #f9f9f9;">
                                    <td style="border-right: 1px solid #ddd;"><strong>Date:</strong></td>
                                    <td align="right">{{ \Carbon\Carbon::parse($reservation->visit_date)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="border-right: 1px solid #ddd;"><strong>Time:</strong></td>
                                    <td align="right">{{ \Carbon\Carbon::parse($reservation->visit_time)->format('H:i') }}</td>
                                </tr>
                                <tr style="background: #f9f9f9;">
                                    <td style="border-right: 1px solid #ddd;"><strong>Guests:</strong></td>
                                    <td align="right">{{ $reservation->guests }}</td>
                                </tr>
                                <tr>
                                    <td style="border-right: 1px solid #ddd;"><strong>Status:</strong></td>
                                    <td align="right">{{ $reservation->reservationStatus?->label ?? 'Unknown' }}</td>
                                </tr>
                            </table>

                            <p style="margin-top: 20px; line-height: 1.6;">{!! $template->message !!}</p>

                            <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

                            <p>Warm regards,</p>
                            <p style="margin: 10px 0;"><strong>Tifliso Team</strong></p>
                            <p style="margin: 0; color: #888; font-size: 12px;">Authentic Georgian Restaurant in Budapest.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>