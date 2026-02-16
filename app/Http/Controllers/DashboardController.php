<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\ContactMessage;

class DashboardController extends Controller
{
    public function index()
    {
        addVendors(['amcharts', 'amcharts-maps', 'amcharts-stock']);

        // Get status objects with names and colors
        $pendingStatus = ReservationStatus::where('name', 'pending')->first();
        $confirmedStatus = ReservationStatus::where('name', 'confirmed')->first();
        $inHouseStatus = ReservationStatus::where('name', 'in-house')->first();
        $declinedStatus = ReservationStatus::where('name', 'canceled')->first();
        $completeStatus = ReservationStatus::where('name', 'complete')->first();

        // Color mapping from Bootstrap badge colors to hex codes
        $colorMap = [
            'warning' => '#ffc107',
            'success' => '#28a745',
            'danger' => '#dc3545',
            'info' => '#17a2b8',
            'primary' => '#007bff',
            'secondary' => '#6c757d',
        ];

        $total_bookings = Reservation::count();
        $new_bookings = $pendingStatus ? Reservation::where('status_id', $pendingStatus->id)->count() : 0;
        $confirmed_bookings = $confirmedStatus ? Reservation::where('status_id', $confirmedStatus->id)->count() : 0;
        $in_house_bookings = $inHouseStatus ? Reservation::where('status_id', $inHouseStatus->id)->count() : 0;
        $declined_bookings = $declinedStatus ? Reservation::where('status_id', $declinedStatus->id)->count() : 0;
        $complete_bookings = $completeStatus ? Reservation::where('status_id', $completeStatus->id)->count() : 0;

        $today_pending_bookings = $pendingStatus ? Reservation::where('status_id', $pendingStatus->id)->whereDate('visit_date', now()->toDateString())->count() : 0;
        $today_declined_bookings = $declinedStatus ? Reservation::where('status_id', $declinedStatus->id)->whereDate('visit_date', now()->toDateString())->count() : 0;
        $today_complete_bookings = $completeStatus ? Reservation::where('status_id', $completeStatus->id)->whereDate('visit_date', now()->toDateString())->count() : 0;

        // Contact Counts
        $total_contact = ContactMessage::count();
        $new_contact = ContactMessage::where('is_read', false)->count();
        $read_contact = ContactMessage::where('is_read', true)->count();

        return view(
            'pages.dashboards.index',
            compact(
                'total_bookings',
                'new_bookings',
                'confirmed_bookings',
                'in_house_bookings',
                'declined_bookings',
                'complete_bookings',
                'today_pending_bookings',
                'today_declined_bookings',
                'today_complete_bookings',
                'total_contact',
                'new_contact',
                'read_contact',
                'pendingStatus',
                'confirmedStatus',
                'inHouseStatus',
                'declinedStatus',
                'completeStatus',
                'colorMap'
            )
        );
    }
}
