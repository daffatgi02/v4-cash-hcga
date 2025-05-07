<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Funds
        Schema::create('funds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['rekening_utama', 'kas_kecil', 'dana_operasional', 'dana_rkb']);
            $table->decimal('initial_balance', 15, 2)->default(0);
            $table->timestamps();
        });

        // 2. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['pemasukan', 'pengeluaran']);
            $table->timestamps();
        });

        // 3. Staff
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('bank_account')->nullable();
            $table->timestamps();
        });

        // 4. RKBs
        Schema::create('rkbs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('requested_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->default(0);
            $table->decimal('received_amount', 15, 2)->default(0);
            $table->decimal('used_amount', 15, 2)->default(0);
            $table->date('request_date');
            $table->date('approval_date')->nullable();
            $table->enum('status', ['pending', 'partial', 'full', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // 5. Transactions
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

        // 6. Staff Debts
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

        // 7. Temporary Funds
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

        // 8. Files
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->morphs('fileable'); // Polymorphic untuk Transaction, RKB, StaffDebt, dll
            $table->string('filename');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('file_type'); // invoice, nota, bukti transfer, dll
            $table->string('mime_type');
            $table->integer('file_size');
            $table->timestamps();
        });

        // Fix for temporary_fund_id foreign key in transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('temporary_fund_id')->references('id')->on('temporary_funds')->onDelete('set null');
        });
    }

    public function down()
    {
        // Drop tables in reverse order
        Schema::dropIfExists('files');
        Schema::dropIfExists('temporary_funds');
        Schema::dropIfExists('staff_debts');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('rkbs');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('funds');
    }
};
