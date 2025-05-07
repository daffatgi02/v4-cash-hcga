<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('rkb_id')->nullable()->constrained();
            $table->foreignId('staff_id')->nullable()->constrained('staff');
            $table->enum('type', ['pemasukan', 'pengeluaran']);
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->text('description');
            $table->string('recipient_name')->nullable(); // vendor, staff, finance
            $table->enum('recipient_type', ['vendor', 'staff', 'finance'])->nullable();
            $table->boolean('is_settlement')->default(false); // pengembalian dana talangan
            $table->foreignId('temporary_fund_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
