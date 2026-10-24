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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no')->unique();
            $table->unsignedBigInteger('promoted_by')->nullable();
            $table->integer('students_count')->default(0);
            $table->string('from_semester_name')->nullable();
            $table->string('to_semester_name')->nullable();
            $table->json('filter_criteria')->nullable();
            $table->string('status')->default('promoted'); // 'promoted', 'reverted'
            $table->timestamp('reverted_at')->nullable();
            $table->unsignedBigInteger('reverted_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('promoted_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('reverted_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('promotion_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->unsignedBigInteger('from_semester_id');
            $table->unsignedBigInteger('to_semester_id');
            $table->string('status')->default('promoted'); // 'promoted', 'reverted'
            $table->timestamp('reverted_at')->nullable();
            $table->timestamps();

            $table->index(['promotion_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_students');
        Schema::dropIfExists('promotions');
    }
};
