<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sequences', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('prefix', 20);
            $table->unsignedBigInteger('current_value')->default(0);
            $table->unsignedTinyInteger('padding')->default(6);
            $table->boolean('include_year')->default(true);
            $table->unsignedSmallInteger('last_year')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sequences');
    }
};
