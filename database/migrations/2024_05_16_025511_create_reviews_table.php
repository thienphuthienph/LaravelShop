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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->constrained()->cascadeOnDelete();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->string("name");
            $table->string("email");
            $table->string("comment");
            $table->integer("rating")->default(1);
            $table->integer("status");
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
