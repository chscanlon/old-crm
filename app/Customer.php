<?php

namespace ScanlonHair\Crm;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $guarded = [];

    
    /**
     * Get the appointments that belong to the customer.
     */
    public function appointments()
    {
        return $this->hasMany('ScanlonHair\Crm\Appointment');
    }
}
