<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('image_tag', function (Blueprint $table) {
            $table->id();

            $table->foreignId('image_id')
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreignId('tag_id')
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_tag');
    }
};
