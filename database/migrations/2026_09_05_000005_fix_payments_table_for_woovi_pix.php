<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // No SQLite, recria a tabela payments com id string e colunas text
            Schema::dropIfExists('payments');

            Schema::create('payments', function (Blueprint $table) {
                $table->string('id', 100)->primary();
                $table->string('ticket_url', 500)->nullable();
                $table->string('payment_code', 40)->default('pix');
                $table->string('date_of_expiration', 60)->nullable();
                $table->decimal('transaction_amount', 10, 2);
                $table->text('qr_code');
                $table->text('qr_code_img')->nullable();
                $table->string('date_approved')->nullable();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        } else {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('id', 100)->change();
                $table->text('qr_code')->change();
                $table->text('qr_code_img')->nullable();
                $table->string('date_approved')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
