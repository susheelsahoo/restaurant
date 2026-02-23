<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'slug' => 'reservation-pending',
                'title' => '📩 Reservation Request Sent',
                'subject' => 'Your Reservation Request has been Received',
                'short_text' => 'We have received your reservation request and will confirm it shortly.',
                'message' => 'We appreciate your interest in dining with us. We will review your request and get back to you as soon as possible. If you have any special requests or need to make changes, please feel free to contact us.',
                'is_active' => true,
            ],
            [
                'slug' => 'reservation-confirmed',
                'title' => '🍷 Your Table Is Confirmed',
                'subject' => 'Your Reservation at Tifliso Georgian Restaurant is Confirmed!',
                'short_text' => 'Your table at Tifliso Georgian Restaurant has been confirmed!',
                'message' => 'If you need to make any changes to your booking or have special requests, please don\'t hesitate to contact us.<br>We look forward to hosting you soon!',
                'is_active' => true,
            ],
            [
                'slug' => 'reservation-canceled',
                'title' => '❌ Your Booking Canceled',
                'subject' => 'Reservation Cancellation Confirmation',
                'short_text' => 'We would like to confirm that your reservation at Tifliso Georgian Restaurant has been cancelled as requested.',
                'message' => 'While we regret not having the pleasure of welcoming you on this occasion, we sincerely hope to host you for a future dining experience.<br />Should you wish to reserve another table or require any assistance, our team will be delighted to help.<br />📍 Location: <a href="{{ config(\'app.GOOGLE_MAPS\') }}">{{ config(\'app.LOCATION\') }}</a><br />📞 Contact: <a href="tel:{{ config(\'app.CONTECT_NUMBER\') }}">{{ config(\'app.CONTECT_NUMBER\') }}</a><br />Thank you for your understanding. We hope to welcome you soon!',
                'is_active' => true,
            ],
            [
                'slug' => 'reservation-declined',
                'title' => '❌ Your Booking Declined',
                'subject' => 'Reservation Status - Table Not Available',
                'short_text' => 'For the given date and time, all of our tables are fully booked. We\'re very sorry for the inconvenience—maybe another time would work for you?',
                'message' => 'We\'d be happy to help you find an alternative time or date. Please feel free to contact us, and we\'ll do our best to assist.<br />📍 Location: <a href="{{ config(\'app.GOOGLE_MAPS\') }}">{{ config(\'app.LOCATION\') }}</a><br />📞 Contact: <a href="tel:{{ config(\'app.CONTECT_NUMBER\') }}">{{ config(\'app.CONTECT_NUMBER\') }}</a><br />Thank you for your understanding. We hope to welcome you soon!',
                'is_active' => true,
            ],
            [
                'slug' => 'reservation-in-house',
                'title' => '🎉 Customer Arrived',
                'subject' => 'Welcome to Tifliso Georgian Restaurant!',
                'short_text' => 'We have registered that you have arrived at Tifliso Georgian Restaurant.',
                'message' => 'Welcome to Tifliso Georgian Restaurant! We hope to provide you with an exceptional dining experience. Enjoy your meal! 🍷🇬🇪',
                'is_active' => true,
            ],
            [
                'slug' => 'reservation-complete',
                'title' => '✅ Thank You for Visiting',
                'subject' => 'Thank You for Dining With Us!',
                'short_text' => 'Thank you for dining with us. We hope to see you again soon!',
                'message' => 'Thank you for dining with us! We\'re happy to share Georgian hospitality with you and hope to see you again soon 🍷🇬🇪<br />Your feedback means a lot to us—feel free to leave a review here: <br /><a href="https://g.page/r/CcEqwhtiszcgEAE/review">Google Reviews</a>.',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
