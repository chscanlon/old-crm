<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('appointment_id'); //foriegn key to the appointments table
            $table->string('stylist');
            $table->string('name');
            $table->integer('timely_booking_id');
            $table->datetime('start_time')->nullable($value = true);
            $table->datetime('end_time')->nullable($value = true);
            $table->decimal('value', 8, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
