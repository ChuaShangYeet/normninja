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
        Schema::table('reminders', function (Blueprint $table) {
            // Add missing columns expected by the calendar view
            $table->string('title')->nullable()->after('user_id');
            $table->text('description')->nullable()->after('title');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('description');

            // Change date column to datetime to support time component
            // Rename 'date' to 'reminder_date' for clarity
            $table->dropColumn('date');
        });

        Schema::table('reminders', function (Blueprint $table) {
            $table->dateTime('reminder_date')->nullable()->after('priority');

            // Update index to use new column name
            $table->dropIndex(['user_id', 'date']);
            $table->index(['user_id', 'reminder_date']);
        });

        // Keep 'text' column for backward compatibility
        // It can be used as fallback if title is empty
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn(['title', 'description', 'priority', 'reminder_date']);

            // Restore original date column
            $table->date('date')->nullable();

            // Restore original index
            $table->dropIndex(['user_id', 'reminder_date']);
            $table->index(['user_id', 'date']);
        });
    }
};
