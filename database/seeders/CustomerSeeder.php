<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '+62 812-3456-7890',
                'status' => 'active'
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '+62 823-4567-8901',
                'status' => 'pending'
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'phone' => '+62 834-5678-9012',
                'status' => 'active'
            ],
            [
                'name' => 'Alice Brown',
                'email' => 'alice@example.com',
                'phone' => '+62 845-6789-0123',
                'status' => 'inactive'
            ],
            [
                'name' => 'Charlie Wilson',
                'email' => 'charlie@example.com',
                'phone' => '+62 856-7890-1234',
                'status' => 'active'
            ]
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
