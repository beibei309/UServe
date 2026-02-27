<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

test('guest is redirected from notifications routes', function () {
    $this->get(route('notifications.index'))->assertRedirect('/login');
    $this->post(route('notifications.markAllRead'))->assertRedirect('/login');
});

test('authenticated user can open notifications index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk();
});

test('marking a notification as read redirects to action url', function () {
    $user = User::factory()->create();
    $notificationId = (string) Str::uuid();

    DB::table('h2u_notifications')->insert([
        'hn_id' => $notificationId,
        'hn_type' => 'App\\Notifications\\ServiceRequestStatusUpdated',
        'hn_notifiable_type' => $user->getMorphClass(),
        'hn_notifiable_id' => $user->hu_id,
        'hn_data' => json_encode([
            'title' => 'Test Notification',
            'message' => 'Test Message',
            'action_url' => '/dashboard',
        ]),
        'hn_read_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($user)
        ->post(route('notifications.read', $notificationId))
        ->assertRedirect('/dashboard');

    expect(DB::table('h2u_notifications')->where('hn_id', $notificationId)->value('hn_read_at'))->not->toBeNull();
});

test('mark all notifications as read updates unread notifications', function () {
    $user = User::factory()->create();

    DB::table('h2u_notifications')->insert([
        [
            'hn_id' => (string) Str::uuid(),
            'hn_type' => 'App\\Notifications\\ServiceRequestStatusUpdated',
            'hn_notifiable_type' => $user->getMorphClass(),
            'hn_notifiable_id' => $user->hu_id,
            'hn_data' => json_encode(['title' => 'N1', 'message' => 'M1', 'action_url' => '#']),
            'hn_read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'hn_id' => (string) Str::uuid(),
            'hn_type' => 'App\\Notifications\\ServiceRequestStatusUpdated',
            'hn_notifiable_type' => $user->getMorphClass(),
            'hn_notifiable_id' => $user->hu_id,
            'hn_data' => json_encode(['title' => 'N2', 'message' => 'M2', 'action_url' => '#']),
            'hn_read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    $this->actingAs($user)
        ->post(route('notifications.markAllRead'))
        ->assertSessionHas('success');

    $remainingUnread = DB::table('h2u_notifications')
        ->where('hn_notifiable_type', $user->getMorphClass())
        ->where('hn_notifiable_id', $user->hu_id)
        ->whereNull('hn_read_at')
        ->count();

    expect($remainingUnread)->toBe(0);
});
