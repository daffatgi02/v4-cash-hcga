<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff');
            $table->foreignId('transaction_id')->constrained(); // Transaksi awal transfer ke staff
            $table->decimal('given_amount', 15, 2); // Jumlah yang diberikan ke staff
            $table->decimal('used_amount', 15, 2)->default(0); // Jumlah yang sudah direalisasikan
            $table->decimal('returned_amount', 15, 2)->default(0); // Jumlah yang sudah dikembalikan
            $table->string('purchase_details')->nullable(); // Detail pembelian
            $table->enum('status', ['outstanding', 'partial', 'settled'])->default('outstanding');
            $table->enum('type', ['transfer', 'cash']); // Jenis transaksi
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_debts');
    }
};
