@extends('layouts.app')
@section('content')

<div class="flex items-center">
    <div class="md:w-3/4 md:mx-auto">

        <div class="flex flex-col break-words bg-white border border-2 rounded shadow-md">

            <div class=" py-3 px-6 mb-0 text-3xl font-semibold bg-gray-200 text-gray-700">
                Customer List
            </div>

            <div class="w-full p-6">
                @livewire('customer-index')
            </div>

        </div>
    </div>
</div>

@endsection