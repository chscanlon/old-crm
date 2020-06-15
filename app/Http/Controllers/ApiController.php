<?php

namespace ScanlonHair\Crm\Http\Controllers;

use ScanlonHair\Crm\Customer;

class ApiController extends Controller
{
    public function getCustomers()
    {
        $cust = (Customer::select('id', 'email', 'timely_customer_id', 'first_name', 'family_name')->where([
            ['timely_status', 'active'],
        ])->paginate(5)->toJson());

        //dd($cust);
        return ($cust);

    }
}
