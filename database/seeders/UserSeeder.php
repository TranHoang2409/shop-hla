<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@hla.test',
                'name' => 'Quản trị HLA',
                'phone' => '0900000001',
                'address' => 'Văn phòng HLA Wifi Shop, Quận 5, TP. Hồ Chí Minh',
                'role' => User::ROLE_ADMIN,
            ],
            [
                'email' => 'customer@hla.test',
                'name' => 'Khách hàng mẫu',
                'phone' => '0900000002',
                'address' => 'Chung cư Sunrise, Quận 7, TP. Hồ Chí Minh',
                'role' => User::ROLE_CUSTOMER,
            ],
            [
                'email' => 'lan.nguyen@hla.test',
                'name' => 'Lan Nguyễn',
                'phone' => '0900000003',
                'address' => 'Nhà phố Thủ Đức, TP. Hồ Chí Minh',
                'role' => User::ROLE_CUSTOMER,
            ],
            [
                'email' => 'minh.tran@hla.test',
                'name' => 'Minh Trần',
                'phone' => '0900000004',
                'address' => 'Văn phòng công ty tại Bình Thạnh, TP. Hồ Chí Minh',
                'role' => User::ROLE_CUSTOMER,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    ...$user,
                    'password' => Hash::make('password'),
                ],
            );
        }
    }
}
