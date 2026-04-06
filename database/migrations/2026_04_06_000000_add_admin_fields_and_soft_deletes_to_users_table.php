<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default(User::ROLE_USER)->after('password');
            $table->string('status')->default(User::STATUS_ACTIVE)->after('role');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->softDeletes();
            $table->index(['role', 'status']);
        });

        DB::table('users')->update([
            'role' => User::ROLE_USER,
            'status' => User::STATUS_ACTIVE,
        ]);

        DB::table('users')->updateOrInsert(
            ['email' => User::SUPER_ADMIN_EMAIL],
            [
                'name' => 'Prashant Chhatri',
                'password' => Hash::make('Prashant@123'),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'status']);
            $table->dropSoftDeletes();
            $table->dropColumn(['role', 'status', 'last_login_at']);
        });
    }
};
