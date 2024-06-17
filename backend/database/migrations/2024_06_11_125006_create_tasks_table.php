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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('priority')->default(\App\Enums\Priority::LOW->value);
            $table->string('status')->default(\App\Enums\TrilioStatus::PENDING->value);
            $table->timestamp('due_date', 0)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('activity_id')->index();
            $table->foreign('activity_id')->references('id')
                ->on('activities')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
