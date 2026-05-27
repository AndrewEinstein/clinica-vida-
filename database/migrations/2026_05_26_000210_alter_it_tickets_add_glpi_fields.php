<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_tickets', function (Blueprint $table): void {
            if (! Schema::hasColumn('it_tickets', 'urgency')) {
                $table->string('urgency')->nullable()->index();
            }
            if (! Schema::hasColumn('it_tickets', 'impact')) {
                $table->string('impact')->nullable()->index();
            }
            if (! Schema::hasColumn('it_tickets', 'category')) {
                $table->string('category')->nullable()->index();
            }
            if (! Schema::hasColumn('it_tickets', 'subcategory')) {
                $table->string('subcategory')->nullable()->index();
            }
            if (! Schema::hasColumn('it_tickets', 'requester_department')) {
                $table->string('requester_department')->nullable()->index();
            }
            if (! Schema::hasColumn('it_tickets', 'internal_notes')) {
                $table->longText('internal_notes')->nullable();
            }
            if (! Schema::hasColumn('it_tickets', 'sla_due_at')) {
                $table->timestamp('sla_due_at')->nullable()->index();
            }
        });

        Schema::table('it_ticket_comments', function (Blueprint $table): void {
            if (! Schema::hasColumn('it_ticket_comments', 'visibility')) {
                $table->string('visibility')->default('public')->index();
            }
        });

        if (! Schema::hasTable('it_ticket_attachments')) {
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
        }

        if (! Schema::hasTable('it_ticket_events')) {
            Schema::create('it_ticket_events', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('ticket_id')->constrained('it_tickets')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('type')->index();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('it_ticket_events');
        Schema::dropIfExists('it_ticket_attachments');

        Schema::table('it_ticket_comments', function (Blueprint $table): void {
            if (Schema::hasColumn('it_ticket_comments', 'visibility')) {
                $table->dropColumn('visibility');
            }
        });

        Schema::table('it_tickets', function (Blueprint $table): void {
            foreach (['urgency', 'impact', 'category', 'subcategory', 'requester_department', 'internal_notes', 'sla_due_at'] as $col) {
                if (Schema::hasColumn('it_tickets', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

