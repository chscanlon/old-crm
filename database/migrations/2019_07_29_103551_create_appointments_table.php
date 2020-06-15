<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('customer_id');
            $table->string('timely_status')->comment('Indicates whether the appointment exists (active) or not (deleted) in Timely. A status of matched is used traniently during schedule import');
            $table->date('appointment_date')->nullable($value = true)->comment('A Timely field. Describes the scheduled date of the appointment');
            $table->string('status')->comment('A Timely field. Describes whether the appointment is confirmed, completed etc');
            $table->date('booking_date')->nullable($value = true)->comment('A Timely field. Describes the date on which the appointment was initially created');
            $table->boolean('retained')->default(0);
            $table->boolean('rebooked')->default(0);
            $table->boolean('invoiced')->default(0);
            $table->integer('invoice_id');
            $table->integer('created_in_import_id')->default(0)->comment('FK to timely_schedule_imports');
            $table->integer('deleted_in_import_id')->default(0)->comment('FK to timely_schedule_imports');
            $table->boolean('has_lightening')->default(0)->comment('Indicates whether the appointment includes a lightening service');
            $table->boolean('has_colour')->default(0)->comment('Indicates whether the appointment includes a global colour service');
            $table->boolean('has_smartbond')->default(0)->comment('Indicates whether the appointment includes a smartbond service');
            $table->boolean('has_treatment')->default(0)->comment('Indicates whether the appointment includes a treatment service');
            $table->boolean('has_cutting')->default(0)->comment('Indicates whether the appointment includes a cutting service');
            $table->boolean('has_styling')->default(0)->comment('Indicates whether the appointment includes a styling service');
            $table->boolean('has_addonfoils')->default(0)->comment('Indicates whether the appointment includes an add on foils service');
            $table->boolean('has_toner')->default(0)->comment('Indicates whether the appointment includes a toner service');
            $table->boolean('has_pensioner')->default(0)->comment('Indicates whether the appointment includes a pensioner service');
            $table->boolean('has_male')->default(0)->comment('Indicates whether the appointment includes a male service');
            $table->boolean('has_children')->default(0)->comment('Indicates whether the appointment includes a childrens service');
            $table->boolean('has_expert')->default(0)->comment('Indicates whether the appointment includes an expert service');
            $table->string('primary_stylist')->comment('Stylist who provided greatest $ value of services in the appointment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
