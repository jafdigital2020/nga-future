@extends('layouts.empmaster') @section('title', 'Attendance')

<style>
    .table-container2 {
        padding: 20px;
    }
</style>
@section('content')

<div class="table-container2">
    <h2 class="table-heading">Payslip</h2>
    <table class="table" style="width: 100%" id="example" style="display: none">
        <thead class="thead-dark">
            <tr>
                <th>Employee Name</th>
                <th>Payroll Date</th>
                <th>Cut-Off Start</th>
                <th>Cut-Off End</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($userslip as $payslip)
            <tr>
                <td>{{ $payslip->employee_name }}</td>
                <td>{{ $payslip->payroll_date }}</td>
                <td>{{ $payslip->payroll_start }}</td>
                <td>{{ $payslip->payroll_end }}</td>
                <td>
                    <a
                        href="{{ url('emp/payslip/view/'.$payslip->id) }}"
                        class="btn btn-info"
                        title="View"
                    >
                        View Payslip
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
