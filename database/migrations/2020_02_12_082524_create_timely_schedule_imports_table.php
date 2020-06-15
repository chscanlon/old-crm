<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimelyScheduleImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timely_schedule_imports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->date('imported_at');
            $table->integer('item_count');
            $table->string('filename');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timely_schedule_imports');
    }
}
