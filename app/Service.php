<?php

namespace ScanlonHair\Crm;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /**
     * Get the appointment that owns the service.
     */
    public function appointment()
    {
        return $this->belongsTo('ScanlonHair\Crm\Appointment');
    }
}
