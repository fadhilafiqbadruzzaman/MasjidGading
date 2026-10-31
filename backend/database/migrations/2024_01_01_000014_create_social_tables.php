<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->decimal('budget', 15, 2)->nullable();
            $table->enum('status', ['planning', 'active', 'completed', 'cancelled'])->default('active');
            $table->boolean('is_published')->default(false);
            $table->foreignId('admin_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('social_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('social_programs')->cascadeOnDelete();
            $table->string('applicant_name');
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->text('reason');
            $table->decimal('requested_amount', 15, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'distributed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('social_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('social_programs')->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained('social_applications')->nullOnDelete();
            $table->string('recipient_name');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->date('distributed_at');
            $table->string('document_path')->nullable();
            $table->foreignId('admin_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_distributions');
        Schema::dropIfExists('social_applications');
        Schema::dropIfExists('social_programs');
    }
};
