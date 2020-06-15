<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_imports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->nullable($value = true);
            $table->integer('timely_customer_id');
            $table->string('status');
            $table->date('booking_date')->nullable($value = true);
            $table->boolean('retained')->default(0);
            $table->boolean('rebooked')->default(0);
            $table->boolean('invoiced')->default(0);
            $table->integer('timely_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointment_imports');
    }
}
