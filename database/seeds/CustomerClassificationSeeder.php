<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CustomerClassificationSeeder extends Seeder
{
    const RETENTION_LIMIT = 84; // max number of days between appointments for retained clients

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($classificationDate = '')
    {
        $this->analysisDate =  (strlen($classificationDate) > 0) ? $classificationDate : now();
        $this->analysisDateLess18M = $this->analysisDate->subMonths(18);

        $customers = DB::select('SELECT * FROM customers;');

        $classification = $customers->map(function($customer)
        {
            $this->getLifetimeApptCount($customer->id);
            $this->analyseLast18MonthsAppt($customer->id);
            $this->analyseFutureAppt($customer->id);
            $this->analyseStylistServiceValueLast18Months($customer->id);
            $this->classifyCustomer($customer->id);
            $customer->classification = $this->customerClassification;
            $customer->sub_classification = $this->customerSubClassification;
            $customer->classification_date = $this->analysisDate;
            $customer->lifetime_appt_count = $this->lifetimeApptCount;
            $customer->first_appt_last18month = $this->firstApptLast18Months;
            $customer->last_appt_last18month = $this->lastApptLast18Months;
            $customer->appt_count_last18month = $this->apptCountLast18Months;
            $customer->appt_interval_last18month = $this->ApptIntervalLast18Months;
            $customer->days_since_last_appt = $this->daysSinceLastAppt;
            $customer->future_appt_count = $this->futureApptCount;
            $customer->service_spend_last18month = $this->clientServiceSpendLast18Months;
            $customer->primary_stylist_last18month = $this->primaryStylistLast18Months;
            $customer->primary_stylist_percent_last18month = $this->primaryStylistPercentLast18Months;
            $customer->stylist_count_last18month = $this->stylistCountLast18Months;
            $customer->primary_stylist_service_spend_last18month = $this->primaryStylistValueLast18Months;

            return $customer->classification;

        });

    }

    /**
     * The reference date from which the customer classification should be calculated.
     * Customer classification is a point in time analysis so the date on which the classification
     * is calculated is important
     *
     * @var date
     */
    public $analysisDate;

    /**
     * @var date
     */
    public $analysisDateLess18M;

    /**
     * @var integer
     */
    protected $lifetimeApptCount;

    /**
     * @var integer
     */
    protected $apptCountLast18Months;

    /**
     * @var date
     */
    protected $firstApptLast18Months;

    /**
     * @var date
     */
    protected $lastApptLast18Months;

    /**
     * @var integer
     */
    protected $futureApptCount;

    /**
     * @var string
     */
    protected $primaryStylistLast18Months;

    /**
     * @var double
     */
    protected $primaryStylistValueLast18Months;

    /**
     * @var integer
     */
    protected $stylistCountLast18Months;

    /**
     * @var double
     */
    protected $clientServiceSpendLast18Months;

    /**
     * @var double
     */
    protected $primaryStylistPercentLast18Months;

    /**
     * @var string
     */
    protected $customerClassification;

    /**
     * @var string
     */
    protected $customerSubClassification;

    /**
     * @var integer
     */
    protected $daysSinceLastAppt;

    /**
     * @var integer
     */
    protected $ApptIntervalLast18Months;



    protected function getLifetimeApptCount($custId)
    {
        $this->lifetimeApptCount = DB::table('appointments')->where(
        [
          ['customer_id', '=', $custId],
          ['date', '<=', $this->analysisDate],
        ]
        )->count();
    }

    protected function analyseLast18MonthsAppt($custId)
    {
        $Last18MonthsAppts = DB::table('appointments')->where([
          ['customer_id', '=', $custId],
          ['date', '<=', $this->analysisDate],
          ['date', '>', $this->analysisDateLess18M]
        ])
          ->orderBy('date', 'asc')
          ->get();

        $this->apptCountLast18Months = $Last18MonthsAppts->count();

        if ($this->apptCountLast18Months > 0) {
            $this->firstApptLast18Months = new DateTime($Last18MonthsAppts->first()->date);
            $this->lastApptLast18Months = new DateTime($Last18MonthsAppts->last()->date);
            $interval = $this->lastApptLast18Months->diff($this->analysisDate);
            $this->daysSinceLastAppt = $interval->format('%a');
        } else {
            $this->firstApptLast18Months = null;
            $this->lastApptLast18Months = null;
            $this->daysSinceLastAppt = 0;
        }

        if ($this->apptCountLast18Months > 3) {
            $interval = $this->firstApptLast18Months->diff($this->lastApptLast18Months);
            $this->ApptIntervalLast18Months = $interval->format('%a')/($this->apptCountLast18Months - 1);
        } else {
            $this->ApptIntervalLast18Months = 0;
        }
    }

    protected function analyseFutureAppt($custId)
    {
        $this->futureApptCount = DB::table('appointments')->where([
        ['customer_id', '=', $custId],
        ['date', '>', $this->analysisDate]
      ])
        ->count();
    }

    protected function analyseStylistServiceValueLast18Months($custId)
    {
        $sylistValues = DB::table('appointments')
        ->join('services', 'appointments.id', '=', 'services.appointment_id')
        ->selectRaw('appointments.customer_id, services.stylist, sum(services.value) as sum_services_purchased')
        ->where([
          ['customer_id', '=', $custId],
          ['date', '<=', $this->analysisDate],
          ['date', '>', $this->analysisDateLess18M]
        ])
        ->groupBy('appointments.customer_id', 'services.stylist')
        ->orderBy('appointments.customer_id', 'asc')
        ->orderBy('sum_services_purchased', 'desc')
        ->get();


        if ($sylistValues->count() > 0) {
            $this->primaryStylistLast18Months = $sylistValues->first()->stylist;
            $this->primaryStylistValueLast18Months = $sylistValues->first()->sum_services_purchased;
            $this->stylistCountLast18Months = $sylistValues->count();
            $this->clientServiceSpendLast18Months = $sylistValues->sum('sum_services_purchased');
        } else {
            $this->primaryStylistLast18Months = '';
            $this->primaryStylistValueLast18Months = 0;
            $this->stylistCountLast18Months = 0;
            $this->clientServiceSpendLast18Months = 0;
        }

        if ($this->primaryStylistValueLast18Months > 0 & $this->clientServiceSpendLast18Months > 0) {
            $this->primaryStylistPercentLast18Months = $this->primaryStylistValueLast18Months/$this->clientServiceSpendLast18Months;
        } else {
            $this->primaryStylistPercentLast18Months = 0;
        }
    }

    protected function classifyCustomer($custId)
    {
        $this->customerClassification = 'NOT CLASSIFIED';
        $this->customerSubClassification = 'Not Classified';

        // LOST Customers
        if ($this->lifetimeApptCount == 0 & $this->futureApptCount == 0) {
            $this->customerClassification = 'LOST';
            $this->customerSubClassification = '2. To be deleted';
        }

        if ($this->apptCountLast18Months == 0 & $this->futureApptCount == 0 & $this->lifetimeApptCount > 0) {
            $this->customerClassification = 'LOST';
            $this->customerSubClassification = '1. Dormant';
        }

        // NEW Customers
        if (
          ($this->apptCountLast18Months > 0 & $this->lifetimeApptCount < 4) or
          ($this->lifetimeApptCount == 0 & $this->futureApptCount > 0)
        ) {
            $this->customerClassification = 'NEW';

            // Sub classification of NEW customers
            if ($this->lifetimeApptCount == 0 & $this->futureApptCount > 0) {
                $this->customerSubClassification = '1. Future Appointment Only';
            } elseif ($this->apptCountLast18Months == 1 & $this->daysSinceLastAppt <= self::RETENTION_LIMIT) {
                $this->customerSubClassification = '2a. Completed 1 appointment';
            } elseif ($this->apptCountLast18Months == 2 & $this->daysSinceLastAppt <= self::RETENTION_LIMIT) {
                $this->customerSubClassification = '2b. Completed 2 appointments';
            } elseif ($this->apptCountLast18Months == 3 & $this->daysSinceLastAppt <= self::RETENTION_LIMIT) {
                $this->customerSubClassification = '2c. Completed 3 appointments';
            } elseif ($this->apptCountLast18Months == 1 & $this->daysSinceLastAppt > self::RETENTION_LIMIT & $this->futureApptCount > 0) {
                $this->customerSubClassification = '3a. Overdue after 1 appointment but returning';
            } elseif ($this->apptCountLast18Months == 2 & $this->daysSinceLastAppt > self::RETENTION_LIMIT & $this->futureApptCount > 0) {
                $this->customerSubClassification = '3b. Overdue after 2 appointments but returning';
            } elseif ($this->apptCountLast18Months == 3 & $this->daysSinceLastAppt > self::RETENTION_LIMIT & $this->futureApptCount > 0) {
                $this->customerSubClassification = '3c. Overdue after 2 appointments but returning';
            } elseif ($this->apptCountLast18Months == 1 & $this->daysSinceLastAppt < (2 * self::RETENTION_LIMIT)) {
                $this->customerSubClassification = '4a. Overdue after 1 appointment';
            } elseif ($this->apptCountLast18Months == 2 & $this->daysSinceLastAppt < (2 * self::RETENTION_LIMIT)) {
                $this->customerSubClassification = '4b. Overdue after 2 appointments';
            } elseif ($this->apptCountLast18Months == 3 & $this->daysSinceLastAppt < (2 * self::RETENTION_LIMIT)) {
                $this->customerSubClassification = '4c. Overdue after 3 appointments';
            } elseif ($this->apptCountLast18Months == 1) {
                $this->customerSubClassification = '5a. Dormant after 1 appointment';
            } elseif ($this->apptCountLast18Months == 2) {
                $this->customerSubClassification = '5b. Dormant after 2 appointments';
            } elseif ($this->apptCountLast18Months == 3) {
                $this->customerSubClassification = '5c. Dormant after 3 appointments';
            } else {
                $this->customerSubClassification = '6. Not Classified';
            }
        }

        // ESTABLISHED Customers
        if ($this->lifetimeApptCount > 3 & $this->apptCountLast18Months > 0) {
            $this->customerClassification = 'ESTABLISHED';

            // Sub classification of ESTABLISHED customers
            if($this->apptCountLast18Months > 4 & $this->ApptIntervalLast18Months < self::RETENTION_LIMIT & $this->daysSinceLastAppt < $this->ApptIntervalLast18Months) {
              $this->customerSubClassification = '1. Active';
            } elseif($this->apptCountLast18Months > 4 & $this->ApptIntervalLast18Months < self::RETENTION_LIMIT & $this->daysSinceLastAppt < 2 * $this->ApptIntervalLast18Months & $this->futureApptCount == 0) {
              $this->customerSubClassification = '2. Active Overdue';
            } elseif($this->apptCountLast18Months > 4 & $this->ApptIntervalLast18Months < self::RETENTION_LIMIT & $this->daysSinceLastAppt > 2 * $this->ApptIntervalLast18Months & $this->futureApptCount == 0) {
              $this->customerSubClassification = '3. Active Now Dormant';
            } elseif($this->apptCountLast18Months == 4 & $this->ApptIntervalLast18Months < self::RETENTION_LIMIT & $this->daysSinceLastAppt < $this->ApptIntervalLast18Months) {
              $this->customerSubClassification = '4. Newly Active';
            } elseif($this->apptCountLast18Months == 4 & $this->ApptIntervalLast18Months < self::RETENTION_LIMIT & $this->daysSinceLastAppt < 2 * $this->ApptIntervalLast18Months & $this->futureApptCount == 0) {
              $this->customerSubClassification = '5. Newly Active Overdue';
            } elseif($this->apptCountLast18Months == 4 & $this->ApptIntervalLast18Months < self::RETENTION_LIMIT & $this->daysSinceLastAppt > 2 * $this->ApptIntervalLast18Months & $this->futureApptCount == 0) {
              $this->customerSubClassification = '6. Newly Active Now Dormant';
            } else {
                $this->customerSubClassification = '7. Ad Hoc';
            }
        }
    }
}
