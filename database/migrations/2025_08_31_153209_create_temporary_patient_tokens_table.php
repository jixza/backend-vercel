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
        Schema::create('temporary_patient_tokens', function (Blueprint $table) {
            $table->id();
            
            // Untuk PostgreSQL, gunakan string patient_id karena mungkin bukan auto-increment
            $table->string('patient_id', 50)->index();
            
            $table->string('token', 255)->unique();
            $table->timestamp('expires_at');
            $table->unsignedBigInteger('created_by_user_id');
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            
            // PostgreSQL specific: gunakan inet untuk IP address
            $table->inet('created_from_ip')->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();
            
            // Index untuk optimasi query
            $table->index(['token', 'expires_at']);
            $table->index(['patient_id', 'is_used']);
            $table->index(['expires_at', 'is_used']);
            
            // Foreign key constraints
            $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Partial index untuk PostgreSQL (hanya active tokens)
            // $table->index(['patient_id'], 'idx_active_tokens')->where('is_used', false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_patient_tokens');
    }
};
