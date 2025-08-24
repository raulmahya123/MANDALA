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
       Schema::create('form_definitions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('department_id')->constrained()->cascadeOnDelete();
    $table->foreignId('doc_type_id')->constrained()->cascadeOnDelete();
    $table->foreignId('doc_item_id')->nullable()->constrained()->cascadeOnDelete();
    $table->string('title'); // <- tambahkan ini
    $table->string('slug')->unique(); // <- tambahkan ini
    $table->boolean('is_active')->default(true); // <- tambahkan ini
    $table->timestamps();

    // Unique dengan nama index pendek
    $table->unique(
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
