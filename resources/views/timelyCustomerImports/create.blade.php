@extends('layouts.app')
@section('content')

<div class="container mx-auto">
    <div class="text-2xl font-bold my-4">New Customer Import</div>

    <div class="mb-2">Customer data are imported from the Timely Customer List report. The report should be exported
        from Timely as a csv file and saved. You can then select the saved file using the form below.
    </div>


    <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" action="/timelyCustomerImports" method="POST"
        enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="customerListReport">Select a file to
                upload</label>
            <input type="file"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                id="customerListReport" name="customerListReport" placeholder="Select File">
        </div>

        <button type="submit"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Submit</button>


    </form>

</div>

@endsection