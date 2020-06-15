<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('timely_status')->comment('Indicates whether the appointment exists (active) or not (deleted) in Timely. A status of matched is used traniently during schedule import');
            $table->integer('timely_customer_id')->unique();
            $table->date('date_added')->nullable($value = true);
            $table->string('first_name')->default('');
            $table->string('family_name')->default('');
            $table->string('card_index')->default('');
            $table->string('email')->default('');
            $table->string('phone')->default('');
            $table->string('sms')->default('');
            $table->string('address_line1')->default('');
            $table->string('address_line2')->default('');
            $table->string('suburb')->default('');
            $table->string('city')->default('');
            $table->string('state')->default('');
            $table->string('postcode')->default('');
            $table->date('date_of_birth')->nullable($value = true);
            $table->boolean('is_vip')->default(0);
            $table->integer('booking_count')->default(0);
            $table->date('last_booking_date')->nullable($value = true);
            $table->string('gender')->default('');
            $table->string('occupation')->default('');
            $table->string('referred_by')->default('');
            $table->integer('created_in_import_id')->default(0)->comment('FK to timely_customer_imports');
            $table->integer('deleted_in_import_id')->default(0)->comment('FK to timely_customer_imports');
            // data for the following columns are calculated during customer classification
            $table->string('classification')->default('');
            $table->string('sub_classification')->default('');
            $table->date('classification_date')->nullable($value = true);
            $table->integer('lifetime_appt_count')->default(0);
            $table->integer('appt_count_last18month')->default(0);
            $table->date('first_appt_last18month')->nullable($value = true);
            $table->date('last_appt_last18month')->nullable($value = true);
            $table->integer('appt_interval_last18month')->default(0);
            $table->integer('days_since_last_appt')->default(0);
            $table->integer('future_appt_count')->default(0);
            $table->unsignedDecimal('service_spend_last18month', 8, 2)->default(0.0);
            $table->string('primary_stylist_last18month')->default('');
            $table->unsignedDecimal('primary_stylist_service_spend_last18month', 8, 2)->default(0.0);
            $table->unsignedDecimal('primary_stylist_percent_last18month', 8, 2)->default(0.0);
            $table->integer('stylist_count_last18month')->default(0);});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
