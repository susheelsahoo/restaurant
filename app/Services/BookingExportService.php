<?php

namespace App\Services;

use App\Models\Reservation;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Cell\StringCell;
use OpenSpout\Common\Entity\Cell\NumericCell;

class BookingExportService
{
    /**
     * Export bookings to XLSX file
     * 
     * @param array $filters - Optional filters for bookings
     * @return string - Path to exported file
     */
    public function exportToXlsx($filters = []): string
    {
        // Build query with relationships
        $query = Reservation::with('customer', 'reservationStatus');

        // Apply filters if provided
        if (!empty($filters['status'])) {
            $query->where('status_id', $filters['status']);
        }

        if (!empty($filters['visit_date'])) {
            $query->whereDate('visit_date', $filters['visit_date']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Get all bookings
        $bookings = $query
            ->orderByDesc('visit_date')
            ->orderBy('visit_time')
            ->get();

        // Create temp file path
        $fileName = 'bookings_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $filePath = storage_path('app/exports/' . $fileName);

        // Ensure export directory exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Create and configure writer
        $writer = new Writer();
        $writer->openToFile($filePath);

        // Write header row
        $headerRow = new Row([
            new StringCell('ID', null),
            new StringCell('Status', null),
            new StringCell('Booking Code', null),
            new StringCell('Visit Date', null),
            new StringCell('Visit Time', null),
            new StringCell('Customer Name', null),
            new StringCell('Phone', null),
            new StringCell('Email', null),
            new StringCell('Guests', null),
            new StringCell('Notes', null),
            new StringCell('Created At', null),
            new StringCell('Updated At', null),
        ]);
        $writer->addRow($headerRow);

        // Write data rows
        foreach ($bookings as $booking) {
            $customerName = trim(
                ($booking->customer->first_name ?? '') . ' ' .
                    ($booking->customer->last_name ?? '')
            );

            $dataRow = new Row([
                new NumericCell($booking->id, null),
                new StringCell($booking->reservationStatus?->label ?? $booking->status ?? 'N/A', null),
                new StringCell($booking->booking_code, null),
                new StringCell($booking->visit_date?->format('Y-m-d') ?? '', null),
                new StringCell($booking->visit_time?->format('H:i:s') ?? '', null),
                new StringCell($customerName, null),
                new StringCell($booking->customer->phone ?? '', null),
                new StringCell($booking->customer->email ?? '', null),
                new NumericCell($booking->guests, null),
                new StringCell($booking->notes ?? '', null),
                new StringCell($booking->created_at?->format('Y-m-d H:i:s') ?? '', null),
                new StringCell($booking->updated_at?->format('Y-m-d H:i:s') ?? '', null),
            ]);
            $writer->addRow($dataRow);
        }

        $writer->close();

        return $filePath;
    }

    /**
     * Export bookings to CSV file
     * 
     * @param array $filters - Optional filters for bookings
     * @return string - Path to exported file
     */
    public function exportToCsv($filters = []): string
    {
        // Build query with relationships
        $query = Reservation::with('customer', 'reservationStatus');

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status_id', $filters['status']);
        }

        if (!empty($filters['visit_date'])) {
            $query->whereDate('visit_date', $filters['visit_date']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Get all bookings
        $bookings = $query
            ->orderByDesc('visit_date')
            ->orderBy('visit_time')
            ->get();

        // Create temp file path
        $fileName = 'bookings_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $filePath = storage_path('app/exports/' . $fileName);

        // Ensure export directory exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Create and configure writer
        $writer = new \OpenSpout\Writer\CSV\Writer();
        $writer->openToFile($filePath);

        // Write header row
        $headerRow = new Row([
            new StringCell('ID', null),
            new StringCell('Status', null),
            new StringCell('Booking Code', null),
            new StringCell('Visit Date', null),
            new StringCell('Visit Time', null),
            new StringCell('Customer Name', null),
            new StringCell('Phone', null),
            new StringCell('Email', null),
            new StringCell('Guests', null),
            new StringCell('Notes', null),
            new StringCell('Created At', null),
            new StringCell('Updated At', null),
        ]);
        $writer->addRow($headerRow);

        // Write data rows
        foreach ($bookings as $booking) {
            $customerName = trim(
                ($booking->customer->first_name ?? '') . ' ' .
                    ($booking->customer->last_name ?? '')
            );

            $dataRow = new Row([
                new NumericCell($booking->id, null),
                new StringCell($booking->reservationStatus?->label ?? $booking->status ?? 'N/A', null),
                new StringCell($booking->booking_code, null),
                new StringCell($booking->visit_date?->format('Y-m-d') ?? '', null),
                new StringCell($booking->visit_time?->format('H:i:s') ?? '', null),
                new StringCell($customerName, null),
                new StringCell($booking->customer->phone ?? '', null),
                new StringCell($booking->customer->email ?? '', null),
                new NumericCell($booking->guests, null),
                new StringCell($booking->notes ?? '', null),
                new StringCell($booking->created_at?->format('Y-m-d H:i:s') ?? '', null),
                new StringCell($booking->updated_at?->format('Y-m-d H:i:s') ?? '', null),
            ]);
            $writer->addRow($dataRow);
        }

        $writer->close();

        return $filePath;
    }
}
