<!DOCTYPE html>
<html>

<body style="margin:0; padding:0; background:#f8f8f8;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f8f8; padding:20px;">
        <tr>
            <td align="center">

                <!-- MAIN CARD -->
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; padding:24px; border-radius:8px; font-family:Arial, sans-serif;">

                    <tr>
                        <td align="center">
                            <h2 style="margin-top:0;">🍷 Table Reservation Confirmed</h2>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p>Hello <strong>{{ $reservation->customer_name }}</strong>,</p>

                            <p>
                                Thank you for booking a table at
                                <strong>Tifliso Georgian Restaurant</strong>.
                            </p>

                            <table width="100%" cellpadding="6" cellspacing="0" style="margin-top:15px;">
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

                            <p>We look forward to welcoming you!</p>

                            <p style="margin-bottom:0;">
                                <strong>Tifliso Team</strong>
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- END CARD -->

            </td>
        </tr>
    </table>

</body>

</html>