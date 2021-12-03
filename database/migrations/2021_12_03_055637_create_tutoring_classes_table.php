<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTutoringClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tutoring_classes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('teacher_id');
            $table->mediumText('subject');
            $table->mediumText('description');
            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tutoring_classes');
    }
}
