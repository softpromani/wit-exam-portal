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
        Schema::create('exam_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('semester_id');
            $table->unsignedBigInteger('session_id');
            $table->string('result_status')->default('pending')->comment('use for student this sem exam result');
            $table->string('exam_status')->default('pending')->comment('use for semester exam status');
            $table->timestamps();
            // Define foreign key constraints
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
        Schema::create('exam_form_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_form_id');
            $table->unsignedBigInteger('subject_id');
            $table->bigInteger('total_marks')->default(0.00);
            $table->bigInteger('obtain_marks')->default(0.00);
            $table->string('grade')->nullable();
            $table->timestamps();    
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('exam_form_id')->references('id')->on('exam_forms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_forms');
        Schema::dropIfExists('exam_form_subjects');
    }
};
