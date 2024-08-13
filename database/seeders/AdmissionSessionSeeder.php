<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class AdmissionSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('admission_sessions')->insert([
            [
                'session_name' => '2020-24',
                'from' => '2020-01-08',
                'to' => '2024-07-31',
            ],
            [
                'session_name' => '2021-25',
                'from' => '2021-01-08',
                'to' => '2025-07-31',
            ],
            [
                'session_name' => '2022-26',
                'from' => '2022-01-08',
                'to' => '2026-07-31',
            ],
            [
                'session_name' => '2023-27',
                'from' => '2023-01-08',
                'to' => '2027-07-31',
            ],
            [
                'session_name' => '2023-26',
                'from' => '2023-01-08',
                'to' => '2027-07-31',
            ],
            [
                'session_name' => '2022-25',
                'from' => '2023-01-08',
                'to' => '2027-07-31',
            ],
            [
                'session_name' => '2021-24',
                'from' => '2023-01-08',
                'to' => '2027-07-31',
            ],
        ]);
        
    }
}
