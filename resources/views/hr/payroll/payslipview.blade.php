@extends('layouts.hrmaster') @section('title', 'Payslip')
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
                                <img src="{{ asset('images/' . $themeSettings->logo) }}" class="inv-logo" alt="">
                                <ul class="list-unstyled mb-0">
                                    <li>{{ $companySettings->company }}</li>
                                    <li>{{ $companySettings->comAddress }}</li>
                                    <li>{{ $companySettings->city }}, {{ $companySettings->country }},
                                        {{ $companySettings->postalCode }}</li>
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
                                        <h5 class="mb-0"><strong>{{ $view->ename }}</strong></h5>
                                    </li>
                                    <li><span>{{ $view->user->position }}</span></li>
                                    <li>Employee ID: {{ $view->user->empNumber }}</li>
                                    <li>Joining Date: {{ $view->user->dateHired }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div>
                                    <h4 class="m-b-10"><strong>Earnings</strong></h4>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td><strong>Birthday Leave</strong> <span
                                                        class="float-right">₱{{ $view->bdayLeave  ?? '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Vacation Leave</strong> <span
                                                        class="float-right">₱{{ $view->vacLeave ?? '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Sick Leave</strong> <span
                                                        class="float-right">₱{{ $view->sickLeave ?? '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Overtime</strong> <span
                                                        class="float-right">₱{{ $view->otTotal ?? '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Regular Holiday</strong> <span
                                                        class="float-right">₱{{ $view->regHoliday ?? '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Reimbursement</strong> <span
                                                        class="float-right">₱{{ $view->reimbursement ?? '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Bonus</strong> <span
                                                        class="float-right">₱{{ $view->bonus ?? '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Hours</strong> <span
                                                        class="float-right"><strong>{{ $view->totalHours }}</strong></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Earnings</strong> <span
                                                        class="float-right"><strong>₱{{ $view->totalEarning }}</strong></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div>
                                    <h4 class="m-b-10"><strong>Deductions</strong></h4>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td><strong>SSS</strong> <span
                                                        class="float-right">₱{{ $view->sss ?? '0.00' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>PhilHealth</strong> <span
                                                        class="float-right">₱{{ $view->philHealth ?? '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Pag-Ibig</strong> <span
                                                        class="float-right">₱{{ $view->pagIbig ?? '0.00' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Withholding Tax</strong> <span
                                                        class="float-right">₱{{ $view->withHolding ?? '0.00' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Late</strong> <span
                                                        class="float-right">₱{{ $view->late ?? '0.00'  }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>SSS Loan</strong> <span
                                                        class="float-right">₱{{ $view->sssLoan ?? '0.00' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Other Loan</strong> <span
                                                        class="float-right">₱{{ $view->loan ?? '0.00' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Advance</strong> <span
                                                        class="float-right">₱{{ $view->advance ?? '0.00' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Savings</strong> <span
                                                        class="float-right">₱{{ $view->savings ?? '0.00' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>HMO</strong> <span
                                                        class="float-right">₱{{ $view->hmo ?? '0.00' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Others</strong> <span
                                                        class="float-right">₱{{ $view->others ?? '0.00' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Deductions</strong> <span
                                                        class="float-right"><strong>₱{{ $view->totalDeduction }}</strong></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color:red;"><strong>Net Salary</strong> <span
                                                        class="float-right"><strong>₱{{ $view->netPayTotal }}</strong></span>
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
