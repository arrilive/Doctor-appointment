<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();                                          // bigint, autoincrement
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');                            // bigint unsigned
            $table->foreignId('speciality_id')
                ->nullable()
                ->constrained('specialities')
                ->onDelete('set null');                           // bigint unsigned, nullable
            $table->string('medical_license_number')
                ->nullable();                                    // varchar(255), nullable
            $table->text('biography')
                ->nullable();                                    // text, nullable
            $table->timestamps();                                  // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
