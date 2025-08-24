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
        Schema::create('form_definitions', function (Blueprint $t) {
    $t->id();
    $t->foreignId('department_id')->constrained()->cascadeOnDelete();
    $t->foreignId('doc_type_id')->constrained()->cascadeOnDelete();
    $t->foreignId('doc_item_id')->constrained()->cascadeOnDelete();
    $t->string('slug');
    $t->string('name');
    $t->boolean('is_active')->default(true);
    $t->timestamps();

    // Unique dengan nama index pendek
    $t->unique(
        ['department_id', 'doc_type_id', 'doc_item_id', 'slug'],
        'form_def_slug_unique'
    );
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_definitions');
    }
};
