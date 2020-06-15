<?php

namespace ScanlonHair\Crm\Http\AppImportRules;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;


class TimelyScheduleImport
{

    public function ImportSchedule($path)
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