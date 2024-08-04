<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->integer('area')->default(0);
            $table->string('room_number')->nullable();
            $table->enum('status', ['draft', 'published']);
            $table->integer('max_adults')->default(0);
            $table->integer('max_children')->default(0);
            $table->integer('price')->default(0);
            $table->integer('max_occupancy_points')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
