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
        Schema::create('form_approvals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('form_entry_id')->constrained()->cascadeOnDelete();
            $t->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete(); // Admin Dept
            $t->enum('action', ['submitted', 'approved', 'rejected'])->default('submitted');
            $t->text('notes')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_approvals');
    }
};
