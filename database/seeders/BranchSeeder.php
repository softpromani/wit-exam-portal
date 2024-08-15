<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('branches')->insert([
            [
                'name' => 'CSE',
                'course_id' => '1',
            ],
            [
                'name' => 'IT',
                'course_id' => '1',
            ],
            [
                'name' => 'BIOINFORMATICS',
                'course_id' => '1',
            ],

        ]);
    }
}
