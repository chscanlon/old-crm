<?php

namespace ScanlonHair\Crm\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ScanlonHair\Crm\Customer;
use ScanlonHair\Crm\CustomerImport;
use ScanlonHair\Crm\TimelyCustomerImport;

class TimelyCustomerImportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $imports = TimelyCustomerImport::all();

        return view('timelyCustomerImports.index', compact('imports'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('timelyCustomerImports.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $path = $request->file('customerListReport')->store('customerListReports');
        $importedRows = $this->importCustomerList($path);

        $timelyCustomerImport = new TimelyCustomerImport;

        $timelyCustomerImport->imported_at = Carbon::now();
        $timelyCustomerImport->item_count = $importedRows;
        $timelyCustomerImport->filename = $path;
        $timelyCustomerImport->save();

        //$this->importId = $timelyCustomerImport->id;

        $this->updateCustomers($timelyCustomerImport->id);

        $newCustomers = Customer::where('created_in_import_id', $timelyCustomerImport->id)->get();
        $deletedCustomers = Customer::where('deleted_in_import_id', $timelyCustomerImport->id)->get();

        return view('timelyCustomerImports.show', compact('timelyCustomerImport', 'newCustomers', 'deletedCustomers'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \ScanlonHair\Crm\TimelyCustomerImport  $timelyCustomerImport
     * @return \Illuminate\Http\Response
     */
    public function show(TimelyCustomerImport $timelyCustomerImport)
    {
        $newCustomers = Customer::where('created_in_import_id', $timelyCustomerImport->id)->get();
        $deletedCustomers = Customer::where('deleted_in_import_id', $timelyCustomerImport->id)->get();

        return view('timelyCustomerImports.show', compact('timelyCustomerImport', 'newCustomers', 'deletedCustomers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \ScanlonHair\Crm\TimelyCustomerImport  $timelyCustomerImport
     * @return \Illuminate\Http\Response
     */
    public function edit(TimelyCustomerImport $timelyCustomerImport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \ScanlonHair\Crm\TimelyCustomerImport  $timelyCustomerImport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TimelyCustomerImport $timelyCustomerImport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \ScanlonHair\Crm\TimelyCustomerImport  $timelyCustomerImport
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimelyCustomerImport $timelyCustomerImport)
    {
        //
    }

    protected function importCustomerList($filepath)
    {
        DB::table('customer_imports')->truncate();

        $query = "LOAD DATA LOCAL INFILE '../storage/app/".$filepath."'
                INTO TABLE customer_imports
                FIELDS TERMINATED BY ','
                OPTIONALLY ENCLOSED BY '\"'
                LINES TERMINATED BY '\r\n'
                IGNORE 2 LINES";

        DB::connection()->getPdo()->exec($query);

        $deleted = DB::table('customer_imports')->where('CustomerId', '=', '')->delete();

        return DB::table('customer_imports')->count();
    }

    protected function updateCustomers($importId)
    {
        //$imports = CustomerImport::cursor();

        $customers = CustomerImport::cursor()->map(function ($import) use ($importId) {
            $cust = Customer::firstOrNew(
                ['timely_customer_id' => $import->CustomerId],
                ['created_in_import_id' => $importId]
            );
            $cust->timely_status = 'matched';
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
        })->all();

        DB::table('customers')
            ->where('timely_status', 'active')
            ->update(['timely_status' => 'deleted', 'deleted_in_import_id' => $importId]);

        DB::table('customers')
            ->where('timely_status', 'matched')
            ->update(['timely_status' => 'active']);
    }
}
