@extends('layouts.app')
@section('content')

<div class="container mx-auto">
    <div class="text-2xl font-bold my-4">Customer Import Summary</div>

    <div class="my-4">

        <table class="border table-auto">
            <tr>
                <th>Import Id</th>
                <td>{{ $timelyCustomerImport->id }}</td>
            </tr>
            <tr>
                <th>Import Filename</th>
                <td>{{ $timelyCustomerImport->filename }}</td>
            </tr>
            <tr>
                <th>Import Date</th>
                <td>{{ $timelyCustomerImport->imported_at }}</td>
            </tr>
            <tr>
                <th>Rows Imported</th>
                <td>{{ $timelyCustomerImport->item_count }}</td>
            </tr>
        </table>

    </div>


    <div class="text-xl font-bold my-4">Customers added in this import</div>

    <div class="my-4">

        <table class="border table-auto">
            <thead>
                <tr>
                    <th>Timely Customer Id</th>
                    <th>First Name</th>
                    <th>Family Name</th>
                    <th>SMS Number</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($newCustomers as $newCustomer)
                <tr>
                    <td> {{$newCustomer->timely_customer_id}} </td>
                    <td> {{$newCustomer->first_name}} </td>
                    <td> {{$newCustomer->family_name}} </td>
                    <td> {{$newCustomer->sms}} </td>
                    <td> {{$newCustomer->email}} </td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>


    <div class="text-xl font-bold my-4">Customers deleted in this import</div>

    <div class="my-4">

        <table class="border table-auto">
            <thead>
                <tr>
                    <th>Timely Customer Id</th>
                    <th>First Name</th>
                    <th>Family Name</th>
                    <th>SMS Number</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deletedCustomers as $deletedCustomer)
                <tr>
                    <td> {{$deletedCustomer->timely_customer_id}} </td>
                    <td> {{$deletedCustomer->first_name}} </td>
                    <td> {{$deletedCustomer->family_name}} </td>
                    <td> {{$deletedCustomer->sms}} </td>
                    <td> {{$deletedCustomer->email}} </td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>
</div>

@endsection