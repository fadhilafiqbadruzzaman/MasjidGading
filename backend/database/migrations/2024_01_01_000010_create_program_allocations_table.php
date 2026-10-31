<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->decimal('total_allocated', 15, 2);
            $table->foreignId('campaign_id')->nullable()->constrained('donation_campaigns')->nullOnDelete();
            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
            $table->boolean('is_published')->default(false);
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('published_at')->nullable();
            $table->foreignId('admin_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('program_realizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('allocation_id')->constrained('program_allocations')->cascadeOnDelete();
            $table->text('description');
            $table->decimal('amount_used', 15, 2);
            $table->date('realization_date');
            $table->string('document_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('admin_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_realizations');
        Schema::dropIfExists('program_allocations');
    }
};
