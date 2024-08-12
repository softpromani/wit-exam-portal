<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('university_roll_no');
            $table->string('registration_no');
            $table->string('registration_type')->default('normal');
            $table->string('student_name');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('semester_id');
            $table->string('password');
            $table->string('gender')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('fname')->nullable();
            $table->string('mname')->nullable();
            $table->string('parent_number')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_profile')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
