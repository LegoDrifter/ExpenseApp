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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string("description");
            $table->smallInteger("recurring");
            $table->smallInteger("cycle");
            $table->smallInteger('cycles_left');
            $table->smallInteger("type");
            $table->smallInteger("status")->default(1);
            $table->double("wage");
            $table->dateTime("date");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
