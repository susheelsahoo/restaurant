<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\ContactMessage;

class DashboardController extends Controller
{
    public function index()
    {
        // addVendors(['amcharts', 'amcharts-maps', 'amcharts-stock']);

        $total_lead = Lead::count();
        $new_lead = Lead::where('status', 'new')->count();

        // Contact Counts
        $total_contact = ContactMessage::count();
        $new_contact = ContactMessage::where('is_read', false)->count();

        return view('pages.dashboards.index', compact('total_lead', 'new_lead', 'total_contact', 'new_contact'));
    }
}
