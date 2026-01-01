<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exam_session_has_cbs', function (Blueprint $table) {
            $table->longText('amounts')->nullable()->after('semesters');
            $table->json('semester_amounts')->nullable()->after('amounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_session_has_cbs', function (Blueprint $table) {
            $table->dropColumn(['amounts', 'semester_amounts']);
        });
    }
};
