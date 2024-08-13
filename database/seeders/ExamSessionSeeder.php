<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class ExamSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('exam_sessions')->insert([
            [
                'session_name' => '2023-24',
                'from' => '2024-03-03',
                'to' => '2024-01-01',
                'semester_id' => '2',
                'status'=>'process',
            ],

            [
                'session_name' => '2023-24',
                'from' => '2024-03-03',
                'to' => '2024-01-01',
                'semester_id' => '4',
                'status'=>'process',
            ],
            [
                'session_name' => '2023-24',
                'from' => '2024-03-03',
                'to' => '2024-01-01',
                'semester_id' => '6',
                'status'=>'process',
            ],
            [
                'session_name' => '2023-24',
                'from' => '2024-03-03',
                'to' => '2024-01-01',
                'semester_id' => '8',
                'status'=>'process',
            ],
        ]);

    }
}
