# Booking Export Feature Documentation

## Overview
This feature provides functionality to export booking/reservation details to Excel (XLSX) and CSV formats using the latest OpenSpout library.

## Files Created/Modified

### 1. **New Service: `app/Services/BookingExportService.php`**
   - Handles all export logic for bookings
   - Two export methods: `exportToXlsx()` and `exportToCsv()`
   - Supports filtering by status, visit date, and search keywords
   - Exports are saved to `storage/app/exports/`

### 2. **Updated Controller: `app/Http/Controllers/BookingController.php`**
   - Added `export()` method to handle export requests
   - Imported `BookingExportService`
   - Method supports format parameter (xlsx or csv)
   - Automatically downloads file after generation

### 3. **Updated Routes: `routes/web.php`**
   - Added export route: `GET /admin/bookings/export`
   - Route name: `admin.bookings.export`
   - Supports query parameters for filters and format

### 4. **Updated View: `resources/views/pages/bookings/index.blade.php`**
   - Added export dropdown button with two options
   - Excel (XLSX) export
   - CSV export
   - Button group displays alongside "Add Booking" button

## Features

### Export Columns
The export includes the following columns:
1. **ID** - Booking ID
2. **Status** - From reservation_statuses table (label)
3. **Booking Code** - Unique booking identifier
4. **Visit Date** - Formatted as YYYY-MM-DD
5. **Visit Time** - Formatted as HH:MM:SS
6. **Customer Name** - Combined first and last name from customers table
7. **Phone** - From customers table
8. **Email** - From customers table
9. **Guests** - Number of guests
10. **Notes** - Booking notes
11. **Created At** - Creation timestamp
12. **Updated At** - Last update timestamp

### Filter Support
Exports respect the current filters applied on the bookings list:
- **Status Filter** - Export only specific status bookings
- **Date Filter** - Export bookings for specific date
- **Search Filter** - Export bookings matching search criteria

## Usage

### From Admin Panel
1. Navigate to Admin → Bookings
2. Apply any filters if needed (status, date, search)
3. Click the "Export" dropdown button
4. Select either "Export to Excel (XLSX)" or "Export to CSV"
5. File will automatically download

### Programmatically
```php
use App\Services\BookingExportService;

// Inject the service
$exportService = app(BookingExportService::class);

// Export with filters
$filters = [
    'status' => 1,  // Status ID
    'visit_date' => '2024-02-17',
    'search' => 'booking_code'
];

// Export to XLSX
$filePath = $exportService->exportToXlsx($filters);

// Export to CSV
$filePath = $exportService->exportToCsv($filters);
```

## Technical Details

### Dependencies
- **OpenSpout** (v4.32.0) - Already installed
  - Modern replacement for PHPExcel/PHP spreadsheet libraries
  - Handles Excel (.xlsx), ODS, and CSV formats
  - Memory efficient for large datasets

### File Storage
- Exported files are temporarily stored in `storage/app/exports/`
- Files are automatically deleted after download
- Filename format: `bookings_Y-m-d_H-i-s.{xlsx|csv}`

### Relationships Used
- `Reservation` → `Customer` (belongsTo)
- `Reservation` → `ReservationStatus` (belongsTo)
- Status label is pulled from the `reservation_statuses` table

## Error Handling
- Exports are wrapped in try-catch blocks
- Errors are logged and user receives feedback
- Directory creation is handled automatically
- Missing data defaults to empty string or 'N/A'

## Security
- Export respects user authorization (requires 'auth' middleware)
- Filters are validated before query execution
- Files are deleted after download (no permanent storage)
