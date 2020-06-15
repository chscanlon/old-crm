<?php

namespace ScanlonHair\Crm;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    /**
     * Make all attributes mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the customer that owns the appointment.
     */
    public function customer()
    {
        return $this->belongsTo('ScanlonHair\Crm\Customer');
    }

    /**
     * Get the services that belong to the appointment.
     */
    public function services()
    {
        return $this->hasMany('ScanlonHair\Crm\Service');
    }

}
