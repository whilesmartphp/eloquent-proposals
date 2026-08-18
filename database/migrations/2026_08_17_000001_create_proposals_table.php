<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('proposals.proposals_table', 'proposals'), function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();

            // Soft association to an estimate (eloquent-invoices), kept as a
            // plain id so proposals do not depend on the invoices package.
            $table->unsignedBigInteger('estimate_id')->nullable();

            $table->string('number');
            $table->string('title');
            $table->string('status')->default('draft');

            // The proposal body as an editable document (a stored DOCX the
            // editor loads and saves). `sections` stays as optional structured
            // metadata (e.g. an outline), not the primary content.
            $table->string('document_url')->nullable();
            $table->json('sections')->nullable();

            $table->date('sent_at')->nullable();
            $table->date('accepted_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['owner_type', 'owner_id', 'number']);
            $table->index(['owner_type', 'owner_id', 'status']);
            $table->index(['estimate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('proposals.proposals_table', 'proposals'));
    }
};
