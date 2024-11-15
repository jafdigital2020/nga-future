@extends('layouts.hrmaster') @section('title', 'Payroll')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    @media print {
        .no-print {
            display: none;
        }

        /* Add other print-specific styles here */
    }

</style>

@section('content')
@include('sweetalert::alert')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Payslip</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('hr/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payslip</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-white no-print" onclick="printDiv('printable-area')">
                        <i class="fa fa-print"></i> Print Payslip
                    </button>
                    <button class="btn btn-white no-print" onclick="downloadPDF()">
                        <i class="fa fa-download"></i> Download as PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    <div id="printable-area">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="payslip-title">Payslip for {{ $view->cut_off }}</h4>
                        <div class="row">
                            <div class="col-sm-6 m-b-20">
                                @if($themeSettings && $themeSettings->logo)
                                <img src="{{ asset('images/' . $themeSettings->logo) }}" class="inv-logo" alt="">
                                @else
                                <img src="{{ asset('images/default-logo.png') }}" class="inv-logo" alt="Default Logo">
                                @endif

                                <ul class="list-unstyled mb-0">
                                    @if($companySettings)
                                    <li>{{ $companySettings->company ?? 'Company Name Not Available' }}</li>
                                    <li>{{ $companySettings->comAddress ?? 'Address Not Available' }}</li>
                                    <li>
                                        {{ $companySettings->city ?? 'City Not Available' }},
                                        {{ $companySettings->country ?? 'Country Not Available' }},
                                        {{ $companySettings->postalCode ?? 'Postal Code Not Available' }}
                                    </li>
                                    @else
                                    <li>Company information is not available.</li>
                                    @endif
                                </ul>

                            </div>
                            <div class="col-sm-6 m-b-20">
                                <div class="invoice-details">
                                    <h3 class="text-uppercase">Payslip #{{ $view->id }}</h3>
                                    <ul class="list-unstyled">
                                        <li>Salary Month: <span>{{ $view->month }}, {{ $view->year }}</span></li>
                                        <li>Cut-Off: <span>{{ $view->start_date }} to {{ $view->end_date }}</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 m-b-20">
                                <ul class="list-unstyled">
                                    <li>
                                        <h5 class="mb-0"><strong>{{ $view->user->lName }}, {{ $view->user->fName }}
                                                {{ $view->user->mName }} ({{ $view->user->name }})
                                            </strong></h5>
                                    </li>
                                    <li><span>{{ $view->user->position }}</span></li>
                                    <li>Employee ID: {{ $view->user->empNumber }}</li>
                                    <li>Joining Date: {{ $view->user->dateHired }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Earnings -->
                            <div class="col-sm-6">
                                <div>
                                    <h4 class="m-b-10"><strong>Earnings</strong></h4>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <!-- Display Earnings -->
                                            @if(!empty($earnings))
                                            @foreach ($earnings as $earning)
                                            <tr>
                                                <td><strong>{{ $earning['name'] }}</strong> <span
                                                        class="float-right">₱{{ $earning['amount'] }}</span></td>
                                            </tr>
                                            @endforeach
                                            @endif
                                            <tr>
                                                <td><strong>Paid Leave</strong> <span
                                                        class="float-right">₱{{ $view->paidLeave ?? 0 }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Overtime</strong> <span
                                                        class="float-right">₱{{ $view->overtimeHours ?? 0 }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Earnings</strong> <span
                                                        class="float-right">₱{{ $view->total_earnings }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Deductions -->
                            @if(!empty($deductions))
                            <div class="col-sm-6">
                                <div>
                                    <h4 class="m-b-10"><strong>Deductions</strong></h4>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <!-- Display Earnings -->

                                            @foreach ($deductions as $deduction)
                                            <tr>
                                                <td><strong>{{ $deduction['name'] }}</strong> <span
                                                        class="float-right">₱{{ $deduction['amount'] }}</span></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                            <!-- Loans -->
                            @if(!empty($loans))
                            <div class="col-sm-6">
                                <div>
                                    <br>
                                    <h4 class="m-b-10"><strong>Loans</strong></h4>
                                    <table class="table table-bordered">
                                        <tbody>
                                            @foreach ($loans as $loan)
                                            <tr>
                                                <td><strong>{{ $loan['loan_name'] }}</strong> <span
                                                        class="float-right">₱{{ $loan['amount'] }}</span></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                            <div class="col-sm-6">
                                <div>
                                    <br>
                                    <h4 class="m-b-10"><strong>Total</strong></h4>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td><strong>Total Hours</strong> <span
                                                        class="float-right">{{ $view->total_hours }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Daily Rate</strong> <span
                                                        class="float-right">₱{{ $view->daily_rate }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Hourly Rate</strong> <span
                                                        class="float-right">₱{{ $view->hourly_rate }}</span></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>Monthly Salary</strong>
                                                    <span
                                                        class="float-right">₱{{ number_format($view->monthly_salary, 2) }}</span>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td><strong>Gross Pay</strong> <span
                                                        class="float-right">₱{{ number_format($view->gross_pay, 2) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Net Pay</strong> <span class="float-right"
                                                        style="color:red;"><strong>₱{{ number_format($view->net_pay, 2) }}</strong></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

@endsection

@section('scripts')

<script>
    function printDiv(divId) {
        var content = document.getElementById(divId).innerHTML;
        var originalContent = document.body.innerHTML;

        // Replace the body content with only the printable content
        document.body.innerHTML = content;

        // Trigger the print dialog
        window.print();

        // Restore the original page content after printing
        document.body.innerHTML = originalContent;
    }

</script>

<script>
    function downloadPDF() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF('p', 'pt', 'a4'); // Initialize jsPDF, a4 size (portrait)

        var content = document.getElementById('printable-area');

        html2canvas(content, {
            scale: 2, // Increase resolution
            useCORS: true, // Allow cross-origin images
        }).then(function (canvas) {
            var imgData = canvas.toDataURL('image/png');

            // Calculate the image height and width
            var imgWidth = 595.28; // A4 width in points
            var pageHeight = 841.89; // A4 height in points
            var imgHeight = canvas.height * imgWidth / canvas.width;
            var heightLeft = imgHeight;

            var position = 0;

            // Add the image and manage pagination
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                doc.addPage();
                doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            // Save the PDF
            doc.save('payslip.pdf');
        });
    }

</script>


@endsection
