<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('temporary_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_fund_id')->constrained('funds'); // Dana asal
            $table->foreignId('target_fund_id')->constrained('funds'); // Dana yang ditopang
            $table->foreignId('rkb_id')->nullable()->constrained(); // Jika untuk RKB
            $table->decimal('amount', 15, 2);
            $table->date('loan_date');
            $table->date('settlement_date')->nullable(); // Tanggal pengembalian dana
            $table->enum('status', ['outstanding', 'settled'])->default('outstanding');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('temporary_funds');
    }
};
