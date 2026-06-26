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
        Schema::create('user_identity_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('template_id');
            $table->string('token')->unique(); // secure random token for QR
            $table->enum('status', ['Active', 'Expired', 'Revoked', 'Lost', 'Duplicate'])->default('Active');
            $table->date('issued_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->unsignedBigInteger('printed_by')->nullable();
            $table->string('rfid_uid')->nullable();
            $table->string('nfc_identifier')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('id_card_templates')->onDelete('cascade');
            $table->foreign('printed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_identity_cards');
    }
};
