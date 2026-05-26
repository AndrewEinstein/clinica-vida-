<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('clinic_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('key')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false)->index();
            $table->string('status')->default('active')->index();
            $table->timestamps();

            $table->unique(['clinic_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};

