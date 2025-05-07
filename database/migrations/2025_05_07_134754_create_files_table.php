<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
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
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }
};
