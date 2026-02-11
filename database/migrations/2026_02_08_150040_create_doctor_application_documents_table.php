<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctor_application_documents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('doctor_application_id');
            $table->unsignedBigInteger('doctor_requirement_id');

            $table->enum('document_type', ['file', 'text'])->default('file');
            $table->string('file_path')->nullable();
            $table->longText('text_value')->nullable();

            $table->enum('status', ['submitted', 'accepted', 'rejected'])->default('submitted');

            $table->timestamps();

            // ✅ Short unique index name (fix for MySQL 1059)
            $table->unique(
            ['doctor_application_id', 'doctor_requirement_id'],
                'doc_app_req_unique'
            );

            // ✅ Foreign keys with short names (safer on MySQL/Windows)
            $table->foreign('doctor_application_id', 'doc_docs_app_fk')
                ->references('id')->on('doctor_applications')
                ->onDelete('cascade');

            $table->foreign('doctor_requirement_id', 'doc_docs_req_fk')
                ->references('id')->on('doctor_requirements')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_application_documents');
    }
};
