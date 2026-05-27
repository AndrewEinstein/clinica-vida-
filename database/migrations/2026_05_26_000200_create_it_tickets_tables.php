<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_tickets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requester_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->index(); // error, improvement, correction, other
            $table->string('priority')->default('medium')->index(); // low, medium, high, urgent
            $table->string('urgency')->nullable()->index(); // low, medium, high, urgent
            $table->string('impact')->nullable()->index(); // low, medium, high
            $table->string('status')->default('open')->index(); // open, in_progress, resolved, closed
            $table->string('category')->nullable()->index();
            $table->string('subcategory')->nullable()->index();
            $table->string('requester_department')->nullable()->index(); // setor solicitante
            $table->string('subject');
            $table->longText('description')->nullable();
            $table->longText('internal_notes')->nullable(); // only TI/admin
            $table->longText('resolution_notes')->nullable();
            $table->timestamp('sla_due_at')->nullable()->index();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'status']);
            $table->index(['clinic_id', 'type']);
        });

        Schema::create('it_ticket_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_id')->constrained('it_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('visibility')->default('public')->index(); // public|internal
            $table->longText('message');
            $table->timestamps();
        });

        Schema::create('it_ticket_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_id')->constrained('it_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();
        });

        Schema::create('it_ticket_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_id')->constrained('it_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->index(); // created|status_changed|assigned|comment|attachment|internal_note
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('it_ticket_events');
        Schema::dropIfExists('it_ticket_attachments');
        Schema::dropIfExists('it_ticket_comments');
        Schema::dropIfExists('it_tickets');
    }
};
