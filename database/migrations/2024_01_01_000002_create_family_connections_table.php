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
        Schema::create('family_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_node_id')->constrained('family_members')->onDelete('cascade');
            $table->foreignId('target_node_id')->constrained('family_members')->onDelete('cascade');
            $table->string('relation_type')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->unique(['source_node_id', 'target_node_id']);
            $table->index(['relation_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_connections');
    }
};