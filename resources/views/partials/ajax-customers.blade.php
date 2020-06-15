<table id="ajax-customers-datatable" class="border table-auto">
    <thead>
        <tr>
            <th>Timely Cust Id</th>
            <th>First Name</th>
            <th>Family Name</th>
            <th>SMS</th>
            <th>Email</th>
            <th>Appt Count</th>
            <th></th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

@push('scripts')
<script>
    $(document).ready(function() {

        var table = $('#ajax-customers-datatable').DataTable({
            serverSide: true,
            processing: true,
            ajax: "{{ route('api.customers.index') }}",
            columns: [{
                    'data': 'timely_customer_id',
                    'orderable':false

                },
                {
                    'data': 'first_name',
                    'orderable':false

                },
                {
                    'data': 'family_name',
                    'orderable':false

                },
                {
                    'data': 'sms',
                    'orderable':false

                },
                {
                    'data': 'email',
                    'orderable':false
                },
                {
                    'data': 'booking_count',
                    'orderable':false
                },
                {
                    'data': '',
                    'orderable':false
                },
            ],
            columnDefs: [{
              render: function(data, type, row){
                return '<a class="" href=/{{request()->segment(1)}}/' + row['id'] + '>Show</a>';
              },
              targets: 6
            }],
            pageLength: 10,
            lengthMenu: [10, 20, 50, 100],
        });

    });
</script>
@endpush