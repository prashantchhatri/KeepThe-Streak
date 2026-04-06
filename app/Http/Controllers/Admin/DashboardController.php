<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $users = User::withTrashed()
            ->orderByRaw("case when role = ? then 0 else 1 end", [User::ROLE_ADMIN])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.dashboard', [
            'users' => $users,
            'totalUsers' => $users->count(),
            'activeUsers' => $users->filter(fn (User $user) => ! $user->trashed() && ! $user->isSuspended())->count(),
            'suspendedUsers' => $users->filter(fn (User $user) => ! $user->trashed() && $user->isSuspended())->count(),
            'deletedUsers' => $users->filter(fn (User $user) => $user->trashed())->count(),
        ]);
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([User::STATUS_ACTIVE, User::STATUS_SUSPENDED])],
        ]);

        if ($user->trashed()) {
            return back()->with('admin-error', 'Deleted accounts cannot be updated.');
        }

        if ($user->isAdmin()) {
            return back()->with('admin-error', 'Admin accounts cannot be suspended or modified here.');
        }

        $user->update([
            'status' => $validated['status'],
        ]);

        return back()->with('status', 'User status updated successfully.');
    }
}
