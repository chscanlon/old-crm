<?php

namespace ScanlonHair\Crm\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use ScanlonHair\Crm\Appointment;
use ScanlonHair\Crm\Customer;
use ScanlonHair\Crm\TimelyScheduleImport;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDashboard(Request $request)
    {
        //default reporting period to last seven days
        $apptDateFrom = Carbon::today()->subDays(7);
        $apptDateTo = Carbon::today();

        $reportPeriod = $request->input('reportPeriod');
        switch ($reportPeriod) {
            case 1:
                //today
                $apptDateFrom = Carbon::today();
                $apptDateTo = Carbon::today();
                break;
            case 2:
                //yesterday
                $apptDateFrom = Carbon::today()->subDays(1);
                $apptDateTo = Carbon::today();
                break;
            case 3:
                //Last 7 Days
                $apptDateFrom = Carbon::today()->subDays(7);
                $apptDateTo = Carbon::today();
                break;
            case 4:
                //Last 14 Days
                $apptDateFrom = Carbon::today()->subDays(14);
                $apptDateTo = Carbon::today();
                break;
            case 5:
                //Last 28 Days
                $apptDateFrom = Carbon::today()->subDays(28);
                $apptDateTo = Carbon::today();
                break;
            case 6:
                //Last 210 Days
                $apptDateFrom = Carbon::today()->subDays(210);
                $apptDateTo = Carbon::today();
                break;
            case 8:
                //Last Week
                $startOfCurrentWeek =
                $apptDateFrom = Carbon::today()->subDays(28);
                $apptDateTo = Carbon::today();
                break;
            default:
                //Last 7 Days
                $apptDateFrom = Carbon::today()->subDays(7);
                $apptDateTo = Carbon::today();
        }

        $lastScheduleImport = date('D, jS M Y', strtotime(TimelyScheduleImport::max('imported_at')));

        // Database summary
        $minApptDate = date('D, jS M Y', strtotime(Appointment::min('appointment_date')));
        $maxApptDate = date('D, jS M Y', strtotime(Appointment::max('appointment_date')));
        $countAppt = Appointment::distinct()->count();
        $countCust = Customer::count();

        $dbSummary = array(
            'lastScheduleImport' => $lastScheduleImport,
            'minApptDate' => $minApptDate,
            'maxApptDate' => $maxApptDate,
            'countAppt' => $countAppt,
            'countCust' => $countCust,
        );

        //dd($dbSummary);

        $summary = array();

        //first add the numbers for each stylist
        $stylistNames = Appointment::where([
            ['appointment_date', '>=', $apptDateFrom],
            ['appointment_date', '<=', $apptDateTo],
            ['status', 'Completed'],
        ])->distinct()->orderBy('primary_stylist')->pluck('primary_stylist');

        $stylistNames->push('Salon Total');

        foreach ($stylistNames as $stylistName) {

            $apptCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array(), 0);
            $apptCountSmartbondTarget = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('OR', 'has_colour', 'has_lightening', 'has_expert'), 0);
            $apptCountTreatmentTarget = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array(), 0, array('has_pensioner', 'has_male', 'has_children'));
            $custCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array(), 1);
            $custCountSmartbondTarget = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('OR', 'has_colour', 'has_lightening', 'has_expert'), 1);
            $custCountTreatmentTarget = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array(), 1, array('has_pensioner', 'has_male', 'has_children'));
            $globalColourCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_colour'), 0);
            $lighteningCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_lightening'), 0);
            $smartbondCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_smartbond'), 0);
            $treatmentCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_treatment'), 0);
            $cuttingCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_cutting'), 0);
            $stylingCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_styling'), 0);
            $addFoilsCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_addonfoils'), 0);
            $pensionerCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_pensioner'), 0);
            $maleCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_male'), 0);
            $childrenCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_children'), 0);
            $tonerCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_toner'), 0);
            $expertCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_expert'), 0);
            $smartbondPlusGlobalColourCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_colour', 'has_smartbond'), 0);
            $smartbondPlusLighteningCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_lightening', 'has_smartbond'), 0);
            $smartbondPlusExpertCount = $this->getCount($apptDateFrom, $apptDateTo, $stylistName, array('has_expert', 'has_smartbond'), 0);

            array_push($summary, array([
                'stylist' => $stylistName,
                'apptCount' => $apptCount,
                'apptCountTreatmentTarget' => $apptCountTreatmentTarget,
                'custCountTreatmentTarget' => $custCountTreatmentTarget,
                'apptCountSmartbondTarget' => $apptCountSmartbondTarget,
                'custCountSmartbondTarget' => $custCountSmartbondTarget,
                'custCount' => $custCount,
                'globalColour' => $globalColourCount,
                'lightening' => $lighteningCount,
                'smartbond' => $smartbondCount,
                'treatment' => $treatmentCount,
                'cutting' => $cuttingCount,
                'styling' => $stylingCount,
                'pensioner' => $pensionerCount,
                'male' => $maleCount,
                'childern' => $childrenCount,
                'toner' => $tonerCount,
                'expert' => $expertCount,
                'smartbondPlusGlobalColour' => $smartbondPlusGlobalColourCount,
                'smartbondPlusLightening' => $smartbondPlusLighteningCount,
                'smartbondPlusExpert' => $smartbondPlusExpertCount,
            ]));

        }

        return view('dashboard', compact('reportPeriod', 'dbSummary', 'summary'));
    }

    private function getCount($apptDateFrom, $apptDateTo, $stylistName, $flags, $custCount, $negatedFlags = array())
    {

        $strWhereClauses = "appointment_date >= DATE('" . $apptDateFrom . "')";
        $strWhereClauses .= " AND appointment_date <= DATE('" . $apptDateTo . "')";
        $strWhereClauses .= " AND status = 'Completed'";

        if ($stylistName != 'Salon Total') {
            $strWhereClauses .= " AND primary_stylist = '" . $stylistName . "'";
        }

        $strWhereClauses .= $this->arrayToWhereClause($flags);
        $strWhereClauses .= $this->arrayToWhereClause($negatedFlags, 0);

        //Log::info($strWhereClauses);

        if ($custCount) {
            $count = Appointment::whereRaw($strWhereClauses)->distinct()->pluck('customer_id')->count();
        } else {
            $count = Appointment::whereRaw($strWhereClauses)->count();
        }

        return $count;

    }

    private function arrayToWhereClause($arr, $bln = 1)
    {

        $strWhere = '';

        if (count($arr) > 0) {
            if ($arr[0] == 'OR') {
                for ($i = 1; $i <= count($arr) - 1; $i++) {
                    if ($i == 1) {
                        $strWhere .= " AND (" . $arr[$i] . " = " . $bln;
                    } else {
                        $strWhere .= " OR " . $arr[$i] . " = " . $bln;
                    }
                }
                $strWhere .= ")";
                //Log::info($strWhere);
            } else {
                foreach ($arr as $item) {
                    $strWhere .= " AND " . $item . " = " . $bln;
                }
            }
        }
        return ($strWhere);

    }
}
