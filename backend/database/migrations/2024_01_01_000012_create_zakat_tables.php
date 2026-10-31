<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zakat_records', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['fitrah', 'maal', 'profesi', 'fidyah', 'other'])->default('fitrah');
            $table->integer('period_year');
            $table->string('period_label');
            $table->decimal('amount', 15, 2);
            $table->string('payer_name')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('admin_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('zakat_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zakat_record_id')->nullable()->constrained('zakat_records')->nullOnDelete();
            $table->enum('type', ['fitrah', 'maal', 'profesi', 'fidyah', 'other'])->default('fitrah');
            $table->string('recipient_name');
            $table->enum('recipient_category', ['fakir', 'miskin', 'amil', 'mualaf', 'riqab', 'gharim', 'fisabilillah', 'ibnu_sabil'])->default('fakir');
            $table->decimal('amount', 15, 2);
            $table->date('distributed_at');
            $table->text('notes')->nullable();
            $table->foreignId('admin_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zakat_distributions');
        Schema::dropIfExists('zakat_records');
    }
};
