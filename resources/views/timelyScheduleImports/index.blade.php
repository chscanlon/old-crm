@extends('./layouts.app')
@section('content')

<div class="container mx-auto">
    <div class="text-2xl font-bold my-4">Importing Schedule Data</div>

    <div class="mb-2">Appointments are maintained in Timely. Customer records are imported in this CRM system from
        Timely to allow more in depth analysis
    </div>



    <div class="">
        <a role="button" href="/timelyScheduleImports/create"
            class="bg-blue-500 hover:bg-blue-700 text-gray-100 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Create New Import
        </a>
    </div>


    <div class="my-4">

        <table class="border table-auto">
            <thead>
                <tr>
                    <th>Import Date</th>
                    <th>Filename</th>
                    <th>Record Count</th>
                </tr>
            </thead>
            @foreach ($imports as $import)
            <tbody>
                <tr>
                    <td>{{$import->imported_at}}</td>
                    <td>{{$import->filename}}</td>
                    <td>{{$import->item_count}}</td>
                </tr>
            </tbody>
            @endforeach
        </table>

    </div>

</div>
@endsection