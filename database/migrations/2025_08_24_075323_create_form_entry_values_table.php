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
        Schema::create('form_entry_values', function (Blueprint $t) {
            $t->id();
            $t->foreignId('form_entry_id')->constrained()->cascadeOnDelete();
            $t->foreignId('form_field_id')->constrained()->cascadeOnDelete();
            $t->text('value')->nullable(); // simpan string (bisa JSON untuk checkbox)
            $t->timestamps();
            $t->unique(['form_entry_id', 'form_field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_entry_values');
    }
};
