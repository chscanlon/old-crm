<div>

    <table class="mx-auto table-auto border">

        <thead class="">
            <tr>
                <th class="p-2 border-r uppercase tracking-wide text-sm font-bold text-gray-500">First Name</th>
                <th class="p-2 border-r uppercase tracking-wide text-sm font-bold text-gray-500">Family Name</th>
                <th class="p-2 border-r uppercase tracking-wide text-sm font-bold text-gray-500">SMS</th>
                <th class="p-2 border-r uppercase tracking-wide text-sm font-bold text-gray-500">Email</th>
                <th class="p-2 border-r uppercase tracking-wide text-sm font-bold text-gray-500">Appt Count</th>
                <th class="p-2 border-r uppercase tracking-wide text-sm font-bold text-gray-500">Last Appt Date</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($customers as $customer)
            <tr>
                <td class="p-1 pl-2 border">{{ $customer->first_name }}</td>
                <td class="p-1 pl-2 border">{{ $customer->family_name }}</td>
                <td class="p-1 pl-2 border">{{ $customer->sms }}</td>
                <td class="p-1 pl-2 border">{{ $customer->email }}</td>
                <td class="p-1 pl-2 border">{{ $customer->booking_count }}</td>
                <td class="p-1 pl-2 border">{{ $customer->last_booking_date }}</td>
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td class="p-1 pl-2 border-none italic text-sm text-right" colspan="6">
                    Showing records {{ $customers->firstItem() }} to {{ $customers->lastItem() }} from a total of {{ $customers->total() }}
                </td>
            </tr>
        </tfoot>


</div>
