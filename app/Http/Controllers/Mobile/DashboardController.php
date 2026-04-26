<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\Concerns\BuildsRequestSummaries;
use App\Models\PurchaseRequest;

class DashboardController extends Controller
{
    use BuildsRequestSummaries;

    public function index()
    {
        $hour = now()->hour;
        if ($hour < 12) {
            $greeting = 'Good morning';
        } elseif ($hour < 17) {
            $greeting = 'Good afternoon';
        } else {
            $greeting = 'Good evening';
        }

        $openStatuses = ['submitted', 'approved'];
        $awaitingApprovalStatuses = ['submitted'];

        return view('mobile.dashboard', [
            'stats' => [
                ['value' => PurchaseRequest::count(), 'label' => 'Total Requests'],
                ['value' => PurchaseRequest::where('status', 'approved')->count(), 'label' => 'Approved'],
                ['value' => PurchaseRequest::where('priority', 'urgent')->count(), 'label' => 'Urgent'],
            ],
            'openRequestsCount' => PurchaseRequest::whereIn('status', $openStatuses)->count(),
            'awaitingApprovalCount' => PurchaseRequest::whereIn('status', $awaitingApprovalStatuses)->count(),
            'recentRequests' => $this->requestListData(5),
            'greeting' => $greeting,
        ]);
    }
}
