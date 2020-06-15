<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use ScanlonHair\Crm\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $imports = DB::table('customer_imports')->limit(10)->get();

        $customers = $imports->map(function($import) {

            $cust = Customer::firstOrNew(['timely_customer_id' => $import->CustomerId]);
            $cust->timely_status = 'matched';
            //$cust->created_in_import_id = $importId;
            $cust->first_name = $import->FirstName;
            $cust->family_name = $import->LastName;
            $cust->card_index = $import->CompanyName;
            $cust->email = $import->Email;
            $cust->phone = $import->Telephone;
            $cust->sms = $import->SmsNumber;
            $cust->address_line1 = $import->PhysicalAddress1;
            $cust->address_line2 = $import->PhysicalAddress2;
            $cust->suburb = $import->PhysicalSuburb;
            $cust->city = $import->PhysicalCity;
            $cust->state = $import->PhysicalState;
            $cust->postcode = $import->PhysicalPostCode;
            $cust->date_of_birth = new Carbon($import->DateOfBirth);
            $cust->is_vip = ($import->IsVip = 'Y') ? true : false;
            $cust->booking_count = $import->BookingCount;
            $cust->last_booking_date = new Carbon($import->LastBookingDate);
            $cust->gender = $import->Gender;
            $cust->occupation = $import->Occupation;
            $cust->referred_by = $import->ReferredBy;
            $cust->save();

            return $cust->id;

        });

        dd($customers);
    }

}
