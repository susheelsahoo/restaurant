<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ContactMessage;

class DashboardController extends Controller
{
    public function index()
    {
        // addVendors(['amcharts', 'amcharts-maps', 'amcharts-stock']);
        //'pending','confirmed','declined','complete'
        $total_bookings = Reservation::count();
        $new_bookings = Reservation::where('status', 'pending')->count();
        $confirmed_bookings = Reservation::where('status', 'confirmed')->count();
        $cancelled_bookings = Reservation::where('status', 'declined')->count();
        $complete_bookings = Reservation::where('status', 'complete')->count();

        // Contact Counts
        $total_contact = ContactMessage::count();
        $new_contact = ContactMessage::where('is_read', false)->count();
        $read_contact = ContactMessage::where('is_read', true)->count();

        return view('pages.dashboards.index', compact('total_bookings', 'new_bookings', 'confirmed_bookings', 'cancelled_bookings', 'complete_bookings', 'total_contact', 'new_contact', 'read_contact'));
    }
}
