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
        Schema::create('form_fields', function (Blueprint $t) {
            $t->id();
            $t->foreignId('form_definition_id')->constrained()->cascadeOnDelete();
            $t->string('label');
            $t->string('name');         // machine name (unique per form)
            $t->string('type');         // text, textarea, number, date, select, checkbox
            $t->json('options')->nullable(); // untuk select/checkbox
            $t->boolean('required')->default(false);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
            $t->unique(['form_definition_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
