<?php

namespace App\Http\Controllers;

class MobileController extends Controller
{
    private function commonData(): array
    {
        return [
            'favoriteItems' => ['Tomato', 'Onion', 'Yogurt', 'Flour', 'Oil'],
            'recentItems' => ['Chicken Breast', 'Butter', 'Lemon', 'Cucumber'],
            'basketItems' => [
                ['name' => 'Tomato', 'supplier' => 'FreshFarm', 'category' => 'Vegetables', 'quantity' => '2.5 kg'],
                ['name' => 'Onion', 'supplier' => 'FreshFarm', 'category' => 'Vegetables', 'quantity' => '3 kg'],
                ['name' => 'Yogurt', 'supplier' => 'DairyPlus', 'category' => 'Dairy', 'quantity' => '8 pcs'],
            ],
            'scannedProduct' => [
                'id' => 101,
                'name' => 'Tomato',
                'category' => 'Vegetables',
                'unit' => 'kg',
                'preferred_supplier' => 'FreshFarm',
                'pack_size' => '1 crate',
                'barcode' => '1234567890123',
            ],
        ];
    }

    public function dashboard()
    {
        $hour = now()->hour;
        if ($hour < 12) {
            $greeting = 'Good morning';
        } elseif ($hour < 17) {
            $greeting = 'Good afternoon';
        } else {
            $greeting = 'Good evening';
        }

        return view('mobile.dashboard', [
            'stats' => [
                ['value' => '5', 'label' => 'Open Requests'],
                ['value' => '2', 'label' => 'Awaiting Approval'],
                ['value' => '12', 'label' => 'Items in Basket'],
                ['value' => '3', 'label' => 'Urgent Items'],
            ],
            'greeting' => $greeting,
        ]);
    }

    public function quickAdd() { return view('mobile.quick-add', $this->commonData()); }
    public function requestDetail() { return view('mobile.request-detail', $this->commonData()); }

    public function approvals()
    {
        return view('mobile.approvals', [
            'approvalRequests' => [
                ['code' => 'REQ-2026-0215', 'requester' => 'Nino G.', 'department' => 'Kitchen', 'summary' => 'Tomato, Onion, Yogurt', 'priority' => 'Urgent', 'neededBy' => 'Today 18:00'],
                ['code' => 'REQ-2026-0214', 'requester' => 'Lasha T.', 'department' => 'Housekeeping', 'summary' => 'Cleaning spray, gloves, paper towels', 'priority' => 'Normal', 'neededBy' => 'Tomorrow morning'],
            ],
        ]);
    }

    public function purchasing() { return view('mobile.purchasing'); }
    public function purchaseOrder() { return view('mobile.purchase-order'); }
    public function receiving() { return view('mobile.receiving'); }
}
