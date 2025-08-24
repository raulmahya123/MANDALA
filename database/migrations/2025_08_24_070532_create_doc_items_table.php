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
        Schema::create('doc_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('department_id')->constrained()->cascadeOnDelete();
            $t->foreignId('doc_type_id')->constrained()->cascadeOnDelete();
            $t->string('name');       // "Harus mandi", "Baca", dst.
            $t->string('slug');       // unique per (dept, doc_type)
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->unique(['department_id', 'doc_type_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_items');
    }
};
