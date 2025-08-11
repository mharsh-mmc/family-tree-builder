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
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('relation')->nullable();
            $table->date('dob')->nullable();
            $table->date('dod')->nullable();
            $table->boolean('is_alive')->default(true);
            $table->text('biodata')->nullable();
            $table->string('profile_pic')->nullable();
            $table->decimal('position_x', 10, 2)->default(0);
            $table->decimal('position_y', 10, 2)->default(0);
            $table->timestamps();
            
            $table->index(['is_alive']);
            $table->index(['relation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};