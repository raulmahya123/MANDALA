<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('department_user', function (Blueprint $t) {
            $t->id();
            $t->foreignId('department_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('role')->default('viewer'); // admin, contributor, viewer
            $t->timestamps();

            $t->unique(['department_id','user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_user');
    }
};
