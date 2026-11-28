<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
            $table->foreignId('specialization_id')->constrained('specializations')->restrictOnDelete();
            $table->string('license_number', 60)->unique();
            $table->string('qualification', 150)->nullable();
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->decimal('consultation_fee', 12, 2)->default(0.00);
            $table->unsignedSmallInteger('slot_duration_minutes')->default(15);
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['department_id', 'is_active']);
            $table->index(['specialization_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
