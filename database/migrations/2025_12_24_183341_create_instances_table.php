<?php

use App\Enums\ConvertingSchemaType;
use App\Enums\InstanceStatus;
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
        Schema::create('instances', function (Blueprint $table) {
            $table->id();
            $table->enum('schema_type', array_column(ConvertingSchemaType::cases(), 'value'));
            $table->enum('status', array_column(InstanceStatus::cases(), 'value'));
            $table->string('original_file_path')
                ->nullable();
            $table->json('reading_settings')
                ->nullable();
            $table->string('converted_file_path')
                ->nullable();
            $table->json('converting_settings')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instances');
    }
};
