<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('department_user_access', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->constrained()->cascadeOnDelete();
      $t->foreignId('department_id')->constrained()->cascadeOnDelete();

      // scope: batasan akses
      $t->enum('scope_type', ['department','doc_type','item'])->default('department');
      $t->unsignedBigInteger('scope_id')->nullable(); // id doc_types.id atau doc_items.id (sesuai scope_type)
      $t->boolean('can_edit')->default(false);        // false = view only

      $t->timestamps();

      $t->index(['user_id','department_id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('department_user_access');
  }
};
