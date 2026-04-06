<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_lists_registered_users_including_deleted_ones(): void
    {
        $admin = User::where('email', User::SUPER_ADMIN_EMAIL)->firstOrFail();
        $activeUser = User::factory()->create([
            'name' => 'Active User',
            'email' => 'active@example.com',
        ]);
        $deletedUser = User::factory()->create([
            'name' => 'Deleted User',
            'email' => 'deleted@example.com',
        ]);

        $deletedUser->delete();

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertSeeText('Admin Dashboard')
            ->assertSeeText($activeUser->email)
            ->assertSeeText($deletedUser->email)
            ->assertSeeText('Deleted');
    }

    public function test_admin_can_suspend_regular_users(): void
    {
        $admin = User::where('email', User::SUPER_ADMIN_EMAIL)->firstOrFail();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.users.status', $user), [
                'status' => User::STATUS_SUSPENDED,
            ]);

        $response
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertSame(User::STATUS_SUSPENDED, $user->fresh()->status);
    }

    public function test_admin_route_is_forbidden_for_regular_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_super_admin_status_can_not_be_changed_from_admin_dashboard(): void
    {
        $admin = User::where('email', User::SUPER_ADMIN_EMAIL)->firstOrFail();

        $response = $this
            ->actingAs($admin)
            ->patch(route('admin.users.status', $admin), [
                'status' => User::STATUS_SUSPENDED,
            ]);

        $response
            ->assertRedirect()
            ->assertSessionHas('admin-error');

        $this->assertSame(User::STATUS_ACTIVE, $admin->fresh()->status);
    }
}
