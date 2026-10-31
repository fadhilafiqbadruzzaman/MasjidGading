<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qurban_programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('year');
            $table->text('description')->nullable();
            $table->enum('status', ['open', 'closed', 'distributed', 'completed'])->default('open');
            $table->boolean('is_published')->default(false);
            $table->foreignId('admin_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('qurban_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('qurban_programs')->cascadeOnDelete();
            $table->string('participant_name');
            $table->string('phone', 20)->nullable();
            $table->enum('animal_type', ['cow', 'goat', 'sheep', 'camel'])->default('cow');
            $table->integer('shares')->default(1);
            $table->decimal('amount_paid', 15, 2);
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('qurban_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('qurban_programs')->cascadeOnDelete();
            $table->string('recipient_name');
            $table->text('recipient_address')->nullable();
            $table->integer('package_count')->default(1);
            $table->decimal('amount_kg', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->date('distributed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qurban_distributions');
        Schema::dropIfExists('qurban_participants');
        Schema::dropIfExists('qurban_programs');
    }
};
