<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('auth.admin.password');

        if (blank($password)) {
            if (app()->isProduction()) {
                throw new RuntimeException('Cần đặt ADMIN_PASSWORD trong .env trước khi seed trên production.');
            }

            $password = 'password';
        }

        User::firstOrCreate(
            ['email' => config('auth.admin.email')],
            [
                'name' => 'Quản trị viên',
                'password' => $password,
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ],
        );
    }
}
