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
        Schema::table('rifas', function (Blueprint $table) {
            if (!Schema::hasColumn('rifas', 'partner_pix_key')) {
                $table->string('partner_pix_key', 255)->nullable()->after('ranking_buyer');
            }
            if (!Schema::hasColumn('rifas', 'partner_name')) {
                $table->string('partner_name', 255)->nullable()->after('partner_pix_key');
            }
            if (!Schema::hasColumn('rifas', 'partner_split_percent')) {
                $table->unsignedInteger('partner_split_percent')->default(70)->after('partner_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rifas', function (Blueprint $table) {
            if (Schema::hasColumn('rifas', 'partner_split_percent')) {
                $table->dropColumn('partner_split_percent');
            }
            if (Schema::hasColumn('rifas', 'partner_name')) {
                $table->dropColumn('partner_name');
            }
            if (Schema::hasColumn('rifas', 'partner_pix_key')) {
                $table->dropColumn('partner_pix_key');
            }
        });
    }
};
