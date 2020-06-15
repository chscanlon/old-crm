@extends('layouts.app')
@section('content')

<div class="container mx-auto">
    <div class="text-2xl font-bold my-4">Appointment Schedule Import Summary</div>

    <div class="my-4">

        <table class="border table-auto">
            <tr>
                <th>Import Id</th>
                <td>{{ $timelyScheduleImport->id }}</td>
            </tr>
            <tr>
                <th>Import Filename</th>
                <td>{{ $timelyScheduleImport->filename }}</td>
            </tr>
            <tr>
                <th>Import Date</th>
                <td>{{ $timelyScheduleImport->imported_at }}</td>
            </tr>
            <tr>
                <th>Rows Imported</th>
                <td>{{ $timelyScheduleImport->item_count }}</td>
            </tr>
        </table>

    </div>

    @empty($newAppointments)

    <div class="text-xl font-bold my-4">No appointments added in this import</div>

    @endempty

    @isset($newAppointments)

    <div class="text-xl font-bold my-4">{{ count($newAppointments) }} appointments added in this import</div>

    <div class="my-4">

        <table class="border table-auto">
            <thead>
                <tr>
                    <th>Timely Customer Id</th>
                    <th>Appointment Date</th>
                    <th>Booked On</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($newAppointments as $newAppointment)
                <tr>
                    <td> {{$newAppointment->customer_id}} </td>
                    <td> {{$newAppointment->appointment_date}} </td>
                    <td> {{$newAppointment->booking_date}} </td>
                    <td> {{$newAppointment->timely_status}} </td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>

    @endisset


    <div class="text-xl font-bold my-4">Appointments deleted in this import</div>

    <div class="my-4">

        <table class="border table-auto">
            <thead>
                <tr>
                    <th>Timely Customer Id</th>
                    <th>Appointment Date</th>
                    <th>Booked On</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deletedAppointments as $deletedAppointment)
                <tr>
                    <td> {{$deletedAppointment->customer_id}} </td>
                    <td> {{$deletedAppointment->appointment_date}} </td>
                    <td> {{$deletedAppointment->booking_date}} </td>
                    <td> {{$deletedAppointment->timely_status}} </td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>
</div>

@endsection