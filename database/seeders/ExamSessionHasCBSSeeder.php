<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamSessionHasCBSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('exam_session_has_cbs')->insert(
            [
            [
                'exam_session_id'=>1,
                'course_id'=>1,
                'branch_id'=>1,
                'semesters'=>json_encode([2,4,6,8])
            ],
            [
                'exam_session_id'=>1,
                'course_id'=>1,
                'branch_id'=>2,
                'semesters'=>json_encode([2,4,6,8])
            ],
            [
                'exam_session_id'=>1,
                'course_id'=>1,
                'branch_id'=>3,
                'semesters'=>json_encode([2,4,6,8])
            ],
            // examses-2
            [
                'exam_session_id'=>2,
                'course_id'=>1,
                'branch_id'=>1,
                'semesters'=>json_encode([2,4,6,8])
            ],
            [
                'exam_session_id'=>2,
                'course_id'=>1,
                'branch_id'=>2,
                'semesters'=>json_encode([2,4,6,8])
            ],
            [
                'exam_session_id'=>2,
                'course_id'=>1,
                'branch_id'=>3,
                'semesters'=>json_encode([2,4,6,8])
            ]
            ]
        );
    }
}
