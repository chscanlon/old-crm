<?php

namespace ScanlonHair\Crm\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use ScanlonHair\Crm\Customer;

class CustomerIndex extends Component
{

    use WithPagination;

    public function render()
    {
        return view('livewire.customer-index', ['customers' => Customer::where('timely_status', 'active')->paginate(15)]);
    }
}
