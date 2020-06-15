<?php

namespace ScanlonHair\Crm\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use ScanlonHair\Crm\Customer;
use ScanlonHair\Crm\Appointment;
use ScanlonHair\Crm\Service;
use ScanlonHair\Crm\TimelyScheduleImport;


class TimelyScheduleImportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $imports = TimelyScheduleImport::all();
        return view('timelyScheduleImports.index', compact('imports'));}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('timelyScheduleImports.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $path = $request->file('apptScheduleReport')->store('apptScheduleReports');
        $this->importSchedule($path);

        $apptCount = DB::table('appointment_imports')->count();

        $timelyScheduleImport = new TimelyScheduleImport;

        $timelyScheduleImport->imported_at = Carbon::now();
        $timelyScheduleImport->item_count = $apptCount;
        $timelyScheduleImport->filename = $path;
        $timelyScheduleImport->save();

        //$this->importId = $timelyScheduleImport->id;

        $this->updateSchedule($timelyScheduleImport->id);

        $newAppointments = Appointment::where('created_in_import_id', $timelyScheduleImport->id)->get();
        $deletedAppointments = Appointment::where('deleted_in_import_id', $timelyScheduleImport->id)->get();

        return view('timelyScheduleImports.show', compact('timelyScheduleImport', 'newAppointments', 'deletedAppointments'));}

    /**
     * Display the specified resource.
     *
     * @param  \ScanlonHair\Crm\TimelyScheduleImport  $timelyScheduleImport
     * @return \Illuminate\Http\Response
     */
    public function show(TimelyScheduleImport $timelyScheduleImport)
    {
        $newAppointments = Appointment::where('created_in_import_id', $timelyScheduleImport->id)->get();
        $deletedAppointments = Appointment::where('deleted_in_import_id', $timelyScheduleImport->id)->get();

        return view('timelyScheduleImports.show', compact('timelyScheduleImport', 'newAppointments', 'deletedAppointments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \ScanlonHair\Crm\TimelyScheduleImport  $timelyScheduleImport
     * @return \Illuminate\Http\Response
     */
    public function edit(TimelyScheduleImport $timelyScheduleImport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \ScanlonHair\Crm\TimelyScheduleImport  $timelyScheduleImport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TimelyScheduleImport $timelyScheduleImport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \ScanlonHair\Crm\TimelyScheduleImport  $timelyScheduleImport
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimelyScheduleImport $timelyScheduleImport)
    {
        //
    }

    protected function ImportSchedule($path)
    {

        DB::table('appointment_imports')->truncate();
        DB::table('service_imports')->truncate();

        $reader = new Xlsx();
        $spreadsheet = $reader->load(storage_path('app/' . $path));
        $worksheet = $spreadsheet->getActiveSheet();
        $clientId = 0;
        $invoiceId = 0;

        foreach ($worksheet->getRowIterator(8) as $row) {
            //If column C contains a hyperlink this is the first line of an appointment
            if ($worksheet->getCell('C' . $row->getRowIndex())->hasHyperlink())
            {
                $clientId = $this->getTimelyClientIndex($worksheet->getCell('C' . $row->getRowIndex())->getHyperlink()->GetURL());
            } else {
                $clientId = 0;
            }

            $apptDate = $worksheet->getCell('B' . $row->getRowIndex())->getValue();
            $clientName = $worksheet->getCell('C' . $row->getRowIndex())->getValue();
            $smsNumber = $worksheet->getCell('F' . $row->getRowIndex())->getValue();
            $apptStatus = $worksheet->getCell('H' . $row->getRowIndex())->getValue();
            $apptCreatedDate = $worksheet->getCell('I' . $row->getRowIndex())->getValue();
            $retained = $worksheet->getCell('P' . $row->getRowIndex())->getValue();
            $rebooked = $worksheet->getCell('Q' . $row->getRowIndex())->getValue();
            $invoiced = $worksheet->getCell('R' . $row->getRowIndex())->getValue();
            //If column R contains a hyperlink this is the line includes service details
            if ($worksheet->getCell('R' . $row->getRowIndex())->hasHyperlink())
            {
                $invoiceId = $this->getTimelyInvoiceIndex($worksheet->getCell('R' . $row->getRowIndex())->getHyperlink()->GetURL());
            } else {
                $invoiceId = 0;
            }
            $startTime = $worksheet->getCell('K' . $row->getRowIndex())->getValue();
            if ($worksheet->getCell('K' . $row->getRowIndex())->hasHyperlink())
            {
                $bookingId = $this->getTimelyBookingIndex($worksheet->getCell('K' . $row->getRowIndex())->getHyperlink()->GetURL());
            } else {
                $bookingId = 0;
            }
            $endTime = $worksheet->getCell('L' . $row->getRowIndex())->getValue();
            $stylist = $worksheet->getCell('M' . $row->getRowIndex())->getValue();
            $serviceName = $worksheet->getCell('N' . $row->getRowIndex())->getValue();
            $serviceValue = $worksheet->getCell('S' . $row->getRowIndex())->getValue();

            if ($clientId !== 0) {
                $apptId = DB::table('appointment_imports')->insertGetId(
                    [
                        'date' => $this->excelDate2SqlDate($apptDate),
                        'timely_customer_id' => $clientId,
                        'status' => $apptStatus,
                        'booking_date' => $this->excelDate2SqlDate($apptCreatedDate),
                        'retained' => ($retained == 'Y') ? true : false,
                        'rebooked' => ($rebooked == 'Y') ? true : false,
                        'invoiced' => ($invoiced == 'Y') ? true : false,
                        'timely_invoice_id' => $invoiceId,
                    ]
                );
            }
            if ($bookingId !== 0) {
                DB::table('service_imports')->insert(
                    [
                        'appointment_imports_id' => $apptId,
                        'stylist' => $stylist,
                        'name' => $serviceName,
                        'timely_booking_id' => $bookingId,
                        'start_time' => $this->excelDate2SqlDateTime($startTime),
                        'end_time' => $this->excelDate2SqlDateTime($endTime),
                        'value' => ($serviceValue == null) ? 0 : $serviceValue,
                    ]
                );
            }
        }

    }

    protected function UpdateSchedule($importId)
    {
        //query the min and max value in the date column of the appointment_imports table
        $minDate = DB::table('appointment_imports')->min('date');
        //Log::info($minDate . ' start date in the appointment_imports table');

        $maxDate = DB::table('appointment_imports')->max('date');
        //Log::info($maxDate . ' end date in the appointment_imports table');

        //for records in the date range, update the appointments.timely_status field to 'processing'
        $rowsUpdated = DB::table('appointments')->where([
            ['appointment_date', '>=', $minDate],
            ['appointment_date', '<=', $maxDate],
        ])->update(['timely_status' => 'processing']);
        //Log::info($rowsUpdated . ' existing rows in date range set to processing');

        $imports = DB::table('appointment_imports')->get();
        $importCount = DB::table('appointment_imports')->count();
        //Log::info($importCount . ' rows to be inserted / updated');

        //loop through the records in the appointment_imports and create new / update existing appointments
        foreach ($imports as $import) {
            
            // Just in case there is a customer in the import that dosen't exist in the Customer model
            try {
                $cust = Customer::where('timely_customer_id', $import->timely_customer_id)->firstorFail();
            } catch (ModelNotFoundException $e) {
                info('timely_customer_id :' . $import->timely_customer_id);
                break;
            }
            $appt = Appointment::where([['customer_id', $cust->id], ['appointment_date', $import->date]])->first();

            if (is_null($appt)) {
                $appt = new Appointment();
                $appt->customer()->associate($cust);
                $appt->appointment_date = $import->date;
                $appt->timely_status = 'matched';
                $appt->status = $import->status;
                $appt->booking_date = $import->booking_date;
                $appt->retained = $import->retained;
                $appt->rebooked = $import->rebooked;
                $appt->invoiced = $import->invoiced;
                $appt->invoice_id = $import->timely_invoice_id;
                $appt->created_in_import_id = $importId;
                $appt->primary_stylist = "";
                $appt->save();
            } else {
                $appt->timely_status = 'matched';
                $appt->status = $import->status;
                $appt->booking_date = $import->booking_date;
                $appt->retained = $import->retained;
                $appt->rebooked = $import->rebooked;
                $appt->invoiced = $import->invoiced;
                $appt->invoice_id = $import->timely_invoice_id;
                $appt->save();

                //Delete all services currently associated with the appointment
                $deleted = DB::table('services')->where('appointment_id', $appt->id)->delete();
            }

            $lighteningServices = array('Parting Classic Foils', '1/2 Head Classic Foils', '3/4 Head Classic Foils', 'Full Head Classic Foils', 'Full Head Balayage', '3/4 Head Balayage');
            $colouringServices = array('All Over Permanent Colour', 'All Over Semi-Permanent Colour', 'Hairline Permanent Colour', 'Hairline Semi-Permanent Colour', 'Retouch Permanent Colour', 'Retouch Semi-Permanent Colour');
            $addOnFoilServices = array('10 Foils with Global Colour', '15 Foils with Global Colour', '20 Foils with Global Colour');
            $smartbondServices = array('Smartbond');
            $treatmentServices = array('Express Treatment', 'Deluxe Treatment Fibercutic', 'ProFiber In Salon Treatment');
            $cuttingServices = array('Style Cut', 'Restyle Cut', 'Clipper Cut', 'Maintenance Cut');
            $stylingServices = array('Style 20', 'Style 30', 'Style 40', 'Style 50', 'Style 60', 'Style 20 - with Cut or Colour', 'Style 30 - with Cut or Colour', 'Style 40 - with Cut or Colour', 'Style 50 - with Cut or Colour', 'Style 60 - with Cut or Colour');
            $tonerServices = array('Tone 15', 'Tone 30');
            $expertServices = array('Partial Bleach', 'Retouch Bleach', 'Full Head Bleach', 'Creative Colouring', 'Colour Correction', 'Blondys', 'Sunkissed Oil');
            $mensServices = array('Male Style Cut', 'Male Permanent Colour', 'Male Semi-Permanent Colour', 'Beard Trim');
            $pensionerServices = array('Pensioner Cut', 'Pensioner Male Cut', 'Pensioner Style 20', 'Pensioner Permanent Colour', 'Pensioner Semi-Permanent Colour');
            $childrensServices = array('Baby Cut', 'Preschool Cut', 'Primary School Cut', 'High School Boys Cut', 'High School Girls Cut');

            $bookings = DB::table('service_imports')->where('appointment_imports_id', $import->id)->get();
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
                if (in_array($booking->name, $colouringServices)) {
                    $appt->has_colour = true;
                    $appt->save();
                } elseif (in_array($booking->name, $cuttingServices)) {
                    $appt->has_cutting = true;
                    $appt->save();
                } elseif (in_array($booking->name, $stylingServices)) {
                    $appt->has_styling = true;
                    $appt->save();
                } elseif (in_array($booking->name, $pensionerServices)) {
                    $appt->has_pensioner = true;
                    $appt->save();
                } elseif (in_array($booking->name, $lighteningServices)) {
                    $appt->has_lightening = true;
                    $appt->save();
                } elseif (in_array($booking->name, $addOnFoilServices)) {
                    $appt->has_addonfoils = true;
                    $appt->save();
                } elseif (in_array($booking->name, $smartbondServices)) {
                    $appt->has_smartbond = true;
                    $appt->save();
                } elseif (in_array($booking->name, $treatmentServices)) {
                    $appt->has_treatment = true;
                    $appt->save();
                } elseif (in_array($booking->name, $tonerServices)) {
                    $appt->has_toner = true;
                    $appt->save();
                } elseif (in_array($booking->name, $mensServices)) {
                    $appt->has_male = true;
                    $appt->save();
                } elseif (in_array($booking->name, $childrensServices)) {
                    $appt->has_children = true;
                    $appt->save();
                } elseif (in_array($booking->name, $expertServices)) {
                    $appt->has_expert = true;
                    $appt->save();
                }
            }

        }

        $apptsUpdated = DB::table('appointments')->where('timely_status', 'matched')->pluck('id');
        foreach ($apptsUpdated as $apptid) {
            DB::table('appointments')->where('id', $apptid)->update(['primary_stylist' => $this->getPrimaryStylistForAppt($apptid)]);
        }

        //Update any records that still have appointments.timely_status = 'matched' to 'active'
        $rowsUpdated = DB::table('appointments')->where('timely_status', 'matched')->update(['timely_status' => 'active']);
        //Log::info($rowsUpdated . ' existing rows in date range updated from matched to active');

        //Finaly, update any records that still have appointments.timely_status = 'processing' to 'deleted' and record the import_id
        $rowsUpdated = DB::table('appointments')->where('timely_status', 'processing')->update(['timely_status' => 'deleted'], ['deleted_in_import_id' => $importId]);
        //Log::info($rowsUpdated . ' existing rows in date range updated from processing to deleted');
    }

    private function getTimelyClientIndex($url)
    {
        $array = explode('/', $url);
        $array2 = explode('?', $array[count($array) - 1]);

        return ($array2[0]);
    }

    private function getTimelyInvoiceIndex($url)
    {
        $array = explode('/', $url);

        return ($array[count($array) - 1]);
    }

    private function getTimelyBookingIndex($url)
    {
        $array = explode('=', $url);

        return ($array[count($array) - 1]);
    }

    private function excelDate2SqlDate($excelDate)
    {
        if ($excelDate <= 25569) {
            return null;
        }

        return date('Y-m-d', ($excelDate - 25569) * 86400);
    }

    private function excelDate2SqlDateTime($excelDate)
    {
        if ($excelDate <= 25569) {
            return null;
        }

        return date('Y-m-d H:i:s', ($excelDate - 25569) * 86400);
    }

    private function formatSqlDate($formatString, $sqlDate)
    {

        //Sql date is in format YYYY-MM-DD

    }

    private function getPrimaryStylistForAppt($apptId)
    {
        $apptStylist = DB::table('appointments')
            ->join('services', 'appointments.id', '=', 'services.appointment_id')
            ->selectRaw('services.stylist as primary_stylist, sum(services.value) as sum_services_purchased')
            ->where('appointments.id', $apptId)
            ->groupBy('services.stylist')
            ->orderBy('sum_services_purchased', 'desc')
            ->first();

        return $apptStylist->primary_stylist;
    }

}
