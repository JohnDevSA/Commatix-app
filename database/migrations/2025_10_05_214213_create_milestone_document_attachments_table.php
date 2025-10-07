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
        Schema::create('milestone_document_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_type_id')->constrained()->onDelete('cascade');
            $table->string('attachment_name');
            $table->boolean('is_required')->default(false);
            $table->unsignedBigInteger('template_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestone_document_attachments');
    }
};