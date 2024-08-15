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
        Schema::create('exam_session_has_cbs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_session_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('branch_id');
            $table->longText('semesters');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
