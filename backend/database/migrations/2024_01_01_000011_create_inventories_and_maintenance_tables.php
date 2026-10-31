<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->integer('quantity')->default(1);
            $table->string('location')->nullable();
            $table->enum('condition', ['good', 'fair', 'needs_repair', 'damaged'])->default('good');
            $table->date('last_maintained_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('admin_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('maintenance_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('location')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->foreignId('assigned_to')->constrained('users'); // Marbot
            $table->foreignId('assigned_by')->constrained('users'); // Admin
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('inventory_id')->nullable()->constrained('inventories')->nullOnDelete();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('facility_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->nullable()->constrained('maintenance_tasks')->nullOnDelete();
            $table->foreignId('reported_by')->constrained('users'); // Marbot
            $table->string('title');
            $table->text('description');
            $table->string('location')->nullable();
            $table->enum('condition', ['good', 'fair', 'needs_repair', 'damaged'])->default('needs_repair');
            $table->string('image_path')->nullable();
            $table->enum('status', ['open', 'acknowledged', 'resolved'])->default('open');
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_reports');
        Schema::dropIfExists('maintenance_tasks');
        Schema::dropIfExists('inventories');
    }
};
