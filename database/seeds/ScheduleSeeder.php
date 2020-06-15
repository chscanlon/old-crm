<?php

use Illuminate\Database\Seeder;
use ScanlonHair\Crm\Appointment;
use ScanlonHair\Crm\Service;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          //query the min and max value in the date column of the appointment_imports table
          $minDate = DB::table('appointment_imports')->min('date');
          echo $minDate . ' start date in the appointment_imports table' . PHP_EOL;

          $maxDate = DB::table('appointment_imports')->max('date');
          echo $maxDate . ' end date in the appointment_imports table' . PHP_EOL;


          //for records in the date range, update the appointments.is_synched_to_timely field to false
          $rowsUpdated = DB::table('appointments')->where([
            ['date', '>=', $minDate],
            ['date', '<=', $maxDate],
          ])->update(['is_synched_to_timely' => false]);
          echo $rowsUpdated . ' existing rows in date range set to not synched' . PHP_EOL;

          $imports = DB::table('appointment_imports')->get();
          $importCount = DB::table('appointment_imports')->count();
          echo $importCount . ' rows to be inserted / updated' . PHP_EOL;

          //loop through the records in the appointment_imports and create new / update existing appointments
          foreach ($imports as $import) {
                  $appt = Appointment::updateOrCreate(
                  [
                    'customer_id' => $import->timely_customer_id,
                    'date' => $import->date
                  ],
                  [
                    'is_synched_to_timely' => true,
                    'status' => $import->status,
                    'booking_date' => $import->booking_date,
                    'retained' => $import->retained,
                    'rebooked' => $import->rebooked,
                    'invoiced' => $import->invoiced,
                    'invoice_id' => $import->timely_invoice_id
                  ]
                );
                //Delete all services currently associated with the appointment
                $deleted = DB::table('services')->where('appointment_id', $appt->id)->delete();
                //Get the records in service_imports that belong to appointment_import
                $bookings =DB::table('service_imports')->where('appointment_imports_id', $import->id)->get();
                //Create new service records from service_imports and then associate with the appointment
                foreach ($bookings as $booking) {
                  $service = new Service;
                  $service->appointment()->associate($appt);
                  $service->stylist = $booking->stylist;
                  $service->name = $booking->name;
                  $service->timely_booking_id = $booking->timely_booking_id;
                  $service->start_time = $booking->start_time;
                  $service->end_time = $booking->end_time;
                  $service->value = $booking->value;
                  $service->save();
                }

          }


    }
}
