@extends('layouts.hrmaster') @section('title', 'Approved Timesheet')

@section('content')
@include('sweetalert::alert')


<div class="content container-fluid">
    <div class="payslip">
        <div class="header2">
            <img src="{{ asset('path_to_logo.png') }}" alt="Company Logo">
            <h2>JAF DIGITAL MARKETING SERVICES</h2>
            <h3>PAYSLIP</h3>
        </div>
        <table>
            <tr>
                <td>Name</td>
                <td></td>
                <td>SSS</td>
                <td></td>
            </tr>
            <!-- Add the rest of the payslip table rows here -->
        </table>

        <div class="section-title">Earnings</div>
        <table>
            <tr>
                <td>Birthday Leave</td>
                <td class="right"></td>
            </tr>
            <!-- Add the rest of the earnings rows here -->
        </table>

        <div class="section-title">Totals</div>
        <table>
            <tr>
                <td>Total Deduction</td>
                <td class="right"></td>
            </tr>
            <!-- Add the rest of the totals rows here -->
        </table>
    </div>

</div>

@endsection
