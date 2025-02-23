<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('image_tag', function (Blueprint $table) {
            $table->index(['image_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        //
    }
};
