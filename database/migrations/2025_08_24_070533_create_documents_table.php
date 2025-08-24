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
        Schema::create('documents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('department_id')->constrained()->cascadeOnDelete();
            $t->foreignId('doc_type_id')->constrained()->cascadeOnDelete();
            $t->foreignId('doc_item_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->string('slug')->unique();
            $t->text('summary')->nullable();
            $t->string('file_path');
            $t->string('file_ext', 10);
            $t->enum('status', ['draft', 'open', 'archived'])->default('draft');
            $t->timestamp('published_at')->nullable();
            $t->foreignId('uploaded_by')->constrained('users');
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
