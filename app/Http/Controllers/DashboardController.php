<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ContactMessage;

class DashboardController extends Controller
{
    public function index()
    {
        // addVendors(['amcharts', 'amcharts-maps', 'amcharts-stock']);

        $total_bookings = Reservation::count();
        $new_bookings = Reservation::where('status', 'new')->count();

        // Contact Counts
        $total_contact = ContactMessage::count();
        $new_contact = ContactMessage::where('is_read', false)->count();

        return view('pages.dashboards.index', compact('total_bookings', 'new_bookings', 'total_contact', 'new_contact'));
    }
}
