<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('12345678');

        $users = [
            [
                'name'        => 'Nguyễn Hoàng Quang',
                'email'       => 'quang.nguyen@brewlearn.com',
                'role'        => 'quan_ly',
                'ChucVu'      => 'Quản Lý Cửa Hàng',
                'ChiNhanh'    => 'Flagship Quận 1',
                'CapDo'       => 3,
                'TienDoTong'  => 100,
                'password'    => $defaultPassword,
            ],
            [
                'name'        => 'Trần Minh Thư',
                'email'       => 'minhthu.tran@brewlearn.com',
                'role'        => 'truong_ca',
                'ChucVu'      => 'Trưởng Ca',
                'ChiNhanh'    => 'Flagship Quận 1',
                'CapDo'       => 2,
                'TienDoTong'  => 85,
                'password'    => $defaultPassword,
            ],
            [
                'name'        => 'Hoàng Văn Đức',
                'email'       => 'duc.hoang@brewlearn.com',
                'role'        => 'nhan_vien',
                'ChucVu'      => 'Barista Tập Sự (L1)',
                'ChiNhanh'    => 'Flagship Quận 1',
                'CapDo'       => 1,
                'TienDoTong'  => 35,
                'password'    => $defaultPassword,
            ],
            [
                'name'        => 'Lê Quốc Tuấn',
                'email'       => 'tuan.le@brewlearn.com',
                'role'        => 'nhan_vien',
                'ChucVu'      => 'Thu Ngân & Dịch Vụ',
                'ChiNhanh'    => 'Flagship Quận 1',
                'CapDo'       => 1,
                'TienDoTong'  => 65,
                'password'    => $defaultPassword,
            ],
            [
                'name'        => 'Phạm Minh Hải',
                'email'       => 'hai.pham@brewlearn.com',
                'role'        => 'nhan_vien',
                'ChucVu'      => 'Barista L2 (Vận Hành Quầy)',
                'ChiNhanh'    => 'Flagship Quận 1',
                'CapDo'       => 2,
                'TienDoTong'  => 90,
                'password'    => $defaultPassword,
            ],
        ];

        foreach ($users as $userData) {
            // Tự bỏ qua các cột chưa có trong DB để không bị lỗi QueryException
            $filteredData = array_filter($userData, function ($key) {
                return Schema::hasColumn('users', $key);
            }, ARRAY_FILTER_USE_KEY);

            User::updateOrCreate(
                ['email' => $userData['email']],
                $filteredData
            );
        }
    }
}