<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AdminBookingCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_create_update_and_delete_a_booking(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $deletePermission = Permission::firstOrCreate(['name' => 'delete']);
        $user->givePermissionTo($deletePermission);

        $pending = ReservationStatus::firstOrCreate(
            ['name' => 'pending'],
            ['label' => 'Pending', 'color' => 'warning', 'sort_order' => 1, 'is_active' => true]
        );

        $confirmed = ReservationStatus::firstOrCreate(
            ['name' => 'confirmed'],
            ['label' => 'Confirmed', 'color' => 'success', 'sort_order' => 2, 'is_active' => true]
        );

        $canceled = ReservationStatus::firstOrCreate(
            ['name' => 'canceled'],
            ['label' => 'Canceled', 'color' => 'danger', 'sort_order' => 3, 'is_active' => true]
        );

        $createResponse = $this
            ->actingAs($user)
            ->post(route('admin.bookings.store'), [
                'customer_name' => 'Codex Smoke',
                'email' => '',
                'phone' => '',
                'visit_date' => now()->addDay()->toDateString(),
                'visit_time' => '18:30',
                'guests' => 2,
                'notes' => 'Created in test',
            ]);

        $createResponse->assertRedirect(route('admin.bookings.index'));

        $booking = Reservation::query()->latest('id')->first();

        $this->assertNotNull($booking);
        $this->assertSame('Codex Smoke', $booking->customer_name);
        $this->assertNull($booking->email);
        $this->assertNull($booking->phone);
        $this->assertSame($pending->id, $booking->status_id);

        $updateResponse = $this
            ->actingAs($user)
            ->put(route('admin.bookings.update', $booking), [
                'customer_name' => 'Codex Smoke Updated',
                'email' => 'codex@example.com',
                'phone' => '1234567890',
                'visit_date' => now()->addDays(2)->toDateString(),
                'visit_time' => '19:15',
                'guests' => 4,
                'status' => $canceled->name,
                'notes' => 'Updated in test',
            ]);

        $updateResponse->assertRedirect(route('admin.bookings.index', ['status' => $pending->name]));

        $booking->refresh();

        $this->assertSame('Codex Smoke Updated', $booking->customer_name);
        $this->assertSame('codex@example.com', $booking->email);
        $this->assertSame('1234567890', $booking->phone);
        $this->assertSame($canceled->id, $booking->status_id);
        $this->assertSame('declined', $booking->status);
        $this->assertSame(4, $booking->guests);

        $deleteResponse = $this
            ->actingAs($user)
            ->delete(route('admin.bookings.destroy', $booking));

        $deleteResponse->assertRedirect(route('admin.bookings.index'));
        $this->assertDatabaseMissing('reservations', ['id' => $booking->id]);
    }
}
