@extends('layouts.app')

@section('content')
<div class="">
    <div class="w-7/12 items-center mx-auto">

        {{-- Reporting Period Card --}}
        <div class="mb-6 bg-gray-300 shadow-lg rounded-lg">
            {{-- Title --}}
            <div>
                <p class="px-6 py-2 bg-blue-900 text-gray-100 rounded-t-lg font-semibold">Dashboard Report Filter</p>
                <p class="px-6 py-2">The dashboards report on appointments in the date range selected below.</p>
            </div>

            <div>
                <form class="px-6 py-2" action="/dashboard" method="POST">
                    @csrf
                    <label class="pr-2 font-medium" for="reportPeriod">Select Reporting Period</label>
                    <select class=" border border-gray-600 focus:border-2 focus:border-gray-600" name="reportPeriod"
                        id="reportPeriod">
                        <option value="0">-- Select Filter --</option>
                        <option value="1">Today</option>
                        <option value="2">Yesterday</option>
                        <option value="3">Last 7 Days</option>
                        <option value="4">Last 14 Days</option>
                        <option value="5">Last 28 Days</option>
                        <option value="6">Last 210 Days</option>
                        {{-- <option value="6">This Week</option>
                        <option value="7">This Month</option> --}}
                        <option value="8">Last Week</option>
                        <option value="9">Last Month</option>
                        <option value="10">Two Weeks Ago</option>
                        <option value="11">Two Months Ago</option>
                        <option value="12">Last 5 Days</option>
                    </select>

                    <button class="ml-4 px-6 py-1 text-gray-100 border-gray-600 bg-gray-600 rounded-lg"
                        type="submit">Apply Filter</button>
                </form>

                <p class="px-6 py-2">Note that these dashboards only report on appointments that have been marked as
                    completed in Timely. Data are not automatically synched from Timely. <span class=" font-medium">The
                        most
                        recent import was on
                        {{ $dbSummary['lastScheduleImport'] }}</span></p>

            </div>

            <div class="rounded-b-lg">
                <div class="py-1 bg-gray-500 text-gray-100 text-center">
                    <span class=" font-medium">Database Summary</span>
                </div>
                <div class="flex justify-between bg-gray-300 text-gray-600">
                    <div>
                        <span class="pt-1 ml-8 text-sm block">First Appt : {{ $dbSummary['minApptDate'] }}</span>
                        <span class="pb-1 ml-8 text-sm block">Last Appt : {{ $dbSummary['maxApptDate'] }}</span>
                    </div>
                    <div>
                        <span class="pt-1 text-sm block">Last Import :
                            {{ $dbSummary['lastScheduleImport'] }}</span>
                    </div>
                    <div>
                        <span class="pt-1 mr-8 text-sm block">Customer Count : {{ $dbSummary['countCust'] }}</span>
                        <span class="pb-1 mr-8 text-sm block">Appointment Count : {{ $dbSummary['countAppt'] }}</span>
                    </div>

                </div>
            </div>


        </div>


        {{-- Service Category Card --}}
        <div class="mb-6 bg-gray-300 shadow-lg rounded-lg">
            {{-- Title --}}
            <div>
                <div class="flex justify-between px-6 py-2 align-middle bg-blue-900 text-gray-100 rounded-t-lg">
                    <span class="font-semibold">Not So Fancy Service Category Dashboard
                    </span>
                    <span class=" text-sm">Currently reporting data from {{ $apptDateFromInclusive->format('D d/m/Y') }} to {{ $apptDateToInclusive->format('D d/m/Y') }}</span>
                </div>

                <p class="px-6 py-2">This dashboard shows how many appointments have services in one or more of main
                    service
                    categories. An appointment often has more than one service so the sum of the service
                    categories may be more than the number of appointments. Because a customer (think of Mrs Sinclair)
                    may have more than one appointment in the reporting period, the number of appointments may be more
                    than the number of customers.</p>
            </div>
            {{-- Table --}}
            <div class="pt-2 pb-6">
                <table class="px-2 mx-auto table-auto border shadow-md">

                    <tr>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Stylist</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Customers</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Appointments</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Global Colour</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Foils / Balayage
                        </td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Expert</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Cutting</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Styling</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Pensioner</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Male</td>
                    </tr>


                    <tbody>
                        @foreach ($summary as $item)
                        @foreach ($item as $value)
                        @if ($value['stylist'] ==='Salon Total')
                        <tr>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['stylist'] }}
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['custCount'] }}
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['apptCount'] }}
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['globalColour'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['globalColour'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['lightening'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['lightening'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['expert'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['expert'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['cutting'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['cutting'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['styling'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['styling'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['pensioner'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['pensioner'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['male'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['male'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">{{ $value['stylist'] }}
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-gray-200">
                                {{ $value['custCount'] }}
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-gray-200">
                                {{ $value['apptCount'] }}
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">
                                {{ $value['globalColour'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['globalColour'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">
                                {{ $value['lightening'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['lightening'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">
                                {{ $value['expert'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['expert'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">
                                {{ $value['cutting'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['cutting'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">
                                {{ $value['styling'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['styling'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">
                                {{ $value['pensioner'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['pensioner'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">
                                {{ $value['male'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCount'] > 0 ? (number_format(($value['male'] / $value['apptCount']) * 100, 1).'%'): '' }})</span>
                            </td>
                        </tr>

                        @endif
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Treatment Card --}}
        <div class="mb-6 bg-gray-300 shadow-lg rounded-lg">
            {{-- Title --}}
            <div>
                <div class="flex justify-between px-6 py-2 align-middle bg-blue-900 text-gray-100 rounded-t-lg">
                    <span class="font-semibold">Treatment Dashboard
                    </span>
                    <span class=" text-sm">Currently reporting data from
                        @if ($reportPeriod == 1)
                        today
                        @elseif ($reportPeriod == 2)
                        yesterday
                        @elseif ($reportPeriod == 3)
                        the last 7 days
                        @elseif ($reportPeriod == 4)
                        the last 14 days
                        @elseif ($reportPeriod == 5)
                        the last 28 days
                        @elseif ($reportPeriod == 6)
                        the last 210
                        @elseif ($reportPeriod == 8)
                        last week
                        @elseif ($reportPeriod == 9)
                        last month
                        @elseif ($reportPeriod == 12)
                        the last 5 days
                        @endif
                    </span>
                </div>

                <p class="px-6 py-2">This dashboard shows how many appointments include a teatment service. Not all of
                    our customers are targets for a treatment service and so the data below exclude appointments that
                    include pensioner services, childrens services and men services. Our goal is that by July 2020, 25%
                    of these
                    targeted appointments will include a treatment service.</p>
            </div>
            {{-- Table --}}
            <div class="pt-2 pb-6">
                <table class="px-2 mx-auto table-auto border shadow-md">

                    <tr>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Stylist</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Target Cust
                        </td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Target Appt</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Treatments</td>
                    </tr>


                    <tbody>
                        @foreach ($summary as $item)
                        @foreach ($item as $value)
                        @if ($value['stylist'] ==='Salon Total')
                        <tr>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['stylist'] }}
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['custCountTreatmentTarget'] }}
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['apptCountTreatmentTarget'] }}
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['treatment'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCountTreatmentTarget'] > 0 ? (number_format(($value['treatment'] / $value['apptCountTreatmentTarget']) * 100, 1).'%'): '' }})</span>
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">{{ $value['stylist'] }}
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-gray-200">
                                {{ $value['custCountTreatmentTarget'] }}
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-gray-200">
                                {{ $value['apptCountTreatmentTarget'] }}
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">
                                {{ $value['treatment'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCountTreatmentTarget'] > 0 ? (number_format(($value['treatment'] / $value['apptCountTreatmentTarget']) * 100, 1).'%'): '' }})</span>
                            </td>
                        </tr>

                        @endif
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Smartbond Card --}}
        <div class="mb-6 bg-gray-300 shadow-lg rounded-lg">
            {{-- Title --}}
            <div>
                <div class="flex justify-between px-6 py-2 align-middle bg-blue-900 text-gray-100 rounded-t-lg">
                    <span class="font-semibold">Smartbond Dashboard
                    </span>
                    <span class=" text-sm">Currently reporting data from
                        @if ($reportPeriod == 1)
                        today
                        @elseif ($reportPeriod == 2)
                        yesterday
                        @elseif ($reportPeriod == 3)
                        the last 7 days
                        @elseif ($reportPeriod == 4)
                        the last 14 days
                        @elseif ($reportPeriod == 5)
                        the last 28 days
                        @elseif ($reportPeriod == 6)
                        the last 210
                        @elseif ($reportPeriod == 8)
                        last week
                        @elseif ($reportPeriod == 9)
                        last month
                        @elseif ($reportPeriod == 12)
                        the last 5 days
                        @endif
                    </span>
                </div>

                <p class="px-6 py-2">This dashboard shows how many appointments that include a Global Colour, Foils /
                    Balayage, or Expert Service also include a Smartbond service. Our goal is that by July 2020, 60% of
                    these
                    targeted appointments will include a Smartbond service.</p>
            </div>
            {{-- Table --}}
            <div class="pt-2 pb-6">
                <table class="px-2 mx-auto table-auto border shadow-md">

                    <tr>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Stylist</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Target Cust
                        </td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Target Appt</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">Smartbond</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">SB with GC</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">SB with F/B</td>
                        <td class="px-2 bg-gray-800 text-gray-100 text-center border border-gray-800">SB with ES</td>
                    </tr>


                    <tbody>
                        @foreach ($summary as $item)
                        @foreach ($item as $value)
                        @if ($value['stylist'] ==='Salon Total')
                        <tr>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['stylist'] }}
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['custCountSmartbondTarget'] }}
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['apptCountSmartbondTarget'] }}
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['smartbond'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCountSmartbondTarget'] > 0 ? (number_format(($value['smartbond'] / $value['apptCountSmartbondTarget']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['smartbondPlusGlobalColour'] }} out of {{ $value['globalColour'] }}
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['smartbondPlusLightening'] }} out of {{ $value['lightening'] }}
                            </td>
                            <td
                                class="px-2 mx-2 border border-gray-400 text-center text-gray-100 font-semibold bg-gray-600">
                                {{ $value['smartbondPlusExpert'] }} out of {{ $value['expert'] }}
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">{{ $value['stylist'] }}
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-gray-200">
                                {{ $value['custCountSmartbondTarget'] }}
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-gray-200">
                                {{ $value['apptCountSmartbondTarget'] }}
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">
                                {{ $value['smartbond'] }}<span
                                    class="ml-2 text-sm">({{ $value['apptCountSmartbondTarget'] > 0 ? (number_format(($value['smartbond'] / $value['apptCountSmartbondTarget']) * 100, 1).'%'): '' }})</span>
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">
                                {{ $value['smartbondPlusGlobalColour'] }} out of {{ $value['globalColour'] }}
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">
                                {{ $value['smartbondPlusLightening'] }} out of {{ $value['lightening'] }}
                            </td>
                            <td class="px-2 mx-2 border border-gray-400 text-center bg-white">
                                {{ $value['smartbondPlusExpert'] }} out of {{ $value['expert'] }}
                            </td>
                        </tr>

                        @endif
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


    </div>
</div>
@endsection