<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->integer('order');
            $table->string('name');
            $table->string('icon')->default('🔬');
            $table->text('description')->nullable();
            $table->text('procedure')->nullable();
            $table->text('safety_reminders')->nullable();
            $table->boolean('is_locked')->default(true);
            $table->boolean('deadline_enabled')->default(false);
            $table->timestamp('deadline_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
};
