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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('cif_number');
            $table->string('ticket_number');
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();
            $table->longText('comments')->nullable();
            $table->timestamp('log_date')->nullable();
            $table->string('status')->default('pending');
            $table->string('loger')->default('astha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
