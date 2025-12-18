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
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->timestamp('admin_read_at')->nullable()->after('resolved_at');
        });

        Schema::table('support_ticket_replies', function (Blueprint $table) {
            $table->timestamp('user_read_at')->nullable()->after('is_admin_reply');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn('admin_read_at');
        });

        Schema::table('support_ticket_replies', function (Blueprint $table) {
            $table->dropColumn('user_read_at');
        });
    }
};
