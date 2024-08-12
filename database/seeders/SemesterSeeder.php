<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('semesters')->insert([
            ['semester_name' => 'sem-1'],
            ['semester_name' => 'sem-2'],
            ['semester_name' => 'sem-3'],
            ['semester_name' => 'sem-4'],
            ['semester_name' => 'sem-5'],
            ['semester_name' => 'sem-6'],
            ['semester_name' => 'sem-7'],
            ['semester_name' => 'sem-8'],
        ]);

    }
}
