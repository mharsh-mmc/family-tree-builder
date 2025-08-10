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
        Schema::create('family_tree_edges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_node_id')->constrained('family_tree_nodes')->onDelete('cascade');
            $table->foreignId('target_node_id')->constrained('family_tree_nodes')->onDelete('cascade');
            $table->string('relation_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_tree_edges');
    }
};
