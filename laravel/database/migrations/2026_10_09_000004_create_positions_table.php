<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('golf_carts')->cascadeOnDelete();
            $table->foreignId('round_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('speed', 8, 3)->default(0);
            $table->unsignedTinyInteger('hole')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['cart_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
