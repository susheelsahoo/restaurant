<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\Concerns\BuildsRequestSummaries;
use App\Models\PurchaseRequest;
use App\Services\PurchaseRoleService;

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

        $access = app(PurchaseRoleService::class);
        $visibleRequests = PurchaseRequest::query();
        $access->applyRequestVisibility($visibleRequests, auth()->user());

        $approvedRequests = clone $visibleRequests;
        $urgentRequests = clone $visibleRequests;
        $openRequests = clone $visibleRequests;
        $awaitingApprovalRequests = clone $visibleRequests;

        return view('mobile.dashboard', [
            'stats' => [
                ['value' => $visibleRequests->count(), 'label' => 'Total Requests'],
                ['value' => $approvedRequests->where('status', 'approved')->count(), 'label' => 'Approved'],
                ['value' => $urgentRequests->where('priority', 'urgent')->count(), 'label' => 'Urgent'],
            ],
            'openRequestsCount' => $openRequests->whereIn('status', $openStatuses)->count(),
            'awaitingApprovalCount' => $awaitingApprovalRequests->whereIn('status', $awaitingApprovalStatuses)->count(),
            'recentRequests' => $this->requestListData(5),
            'greeting' => $greeting,
        ]);
    }
}
