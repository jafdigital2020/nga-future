@extends('layouts.hrmaster') @section('title', 'Payroll')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    @media print {
        .no-print {
            display: none;
        }

        /* Add other print-specific styles here */
    }

    .payroll-summary {
        width: 90%;
        max-width: 1200px;
        margin: 20px auto;
        background: #fff;
        border-radius: 8px;
        padding: 20px 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .header {
        text-align: center;
        margin-bottom: 30px;
    }

    .header h1 {
        margin: 0;
        font-size: 24px;
        color: #4CAF50;
    }

    .section {
        margin-bottom: 30px;
    }

    .section h2 {
        font-size: 20px;
        margin-bottom: 10px;
        border-bottom: 2px solid #4CAF50;
        padding-bottom: 5px;
        color: black;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        background-color: #fff;
    }

    table th,
    table td {
        text-align: left;
        padding: 10px;
        border: 1px solid #ddd;
        font-size: 14px;
    }

    table th {
        background-color: #DF1C2A;
        color: white;
        text-transform: uppercase;
    }

    table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    table tbody tr.total {
        font-weight: bold;
        background-color: #f4f4f4;
    }

    .total td {
        color: #000;
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
                                    @if ($themeSettings && $themeSettings->logo)
                                        <img src="{{ asset('images/' . $themeSettings->logo) }}" class="inv-logo"
                                            alt="">
                                    @else
                                        <img src="{{ asset('images/default-logo.png') }}" class="inv-logo"
                                            alt="Default Logo">
                                    @endif

                                    <ul class="list-unstyled mb-0">
                                        @if ($companySettings)
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
                                <div class="col-sm-6 m-b-20" style="margin-top: 70px;">
                                    <div class="invoice-details">
                                        <h3 class="text-uppercase">Payslip #{{ $view->id }}</h3>
                                        <ul class="list-unstyled">
                                            <li>
                                                <h5 class="mb-0"><strong>{{ $view->user->lName }},
                                                        {{ $view->user->fName }}
                                                        {{ $view->user->mName }} ({{ $view->user->name }})
                                                    </strong></h5>
                                            </li>
                                            <li><span>{{ $view->user->position }}</span></li>
                                            <li>Employee ID: {{ $view->user->empNumber }}</li>
                                            <li>Joining Date: {{ $view->user->dateHired }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Pay Period and Salary -->
                            <div class="section">
                                <h2>Pay Period and Salary</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Pay Period</th>
                                            <th>Payment Date</th>
                                            <th>Pay Begin</th>
                                            <th>Pay End Date</th>
                                            <th>Monthly Rate</th>
                                            <th>Hourly Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Semi-Month</td>
                                            <td>{{ $view->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $view->start_date }}</td>
                                            <td>{{ $view->end_date }}</td>
                                            <td>{{ $view->monthly_salary }}</td>
                                            <td>{{ $view->user->hourly_rate }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


                            <!-- Taxable Earnings -->
                            <div class="section">
                                <h2>Taxable Earnings</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Basic Pay</td>
                                            <td>₱{{ number_format($view->basic_pay, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Gross Pay</td>
                                            <td>₱{{ number_format($view->gross_pay, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Special Holiday</td>
                                            <td>₱{{ number_format($view->special_holiday_pay, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Regular Holiday</td>
                                            <td>₱{{ number_format($view->regular_holiday_pay, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Overtime Pay</td>
                                            <td>₱{{ number_format($view->overtimeHours, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Paid Leave</td>
                                            <td>₱{{ number_format($view->paidLeave, 2) }}</td>
                                        </tr>
                            
                                        @php
                                            $totalTaxable = $view->basic_pay + $view->gross_pay + $view->special_holiday_pay + $view->regular_holiday_pay + $view->overtimeHours + $view->paidLeave; // Initialize total for taxable earnings
                                        @endphp
                            
                                        @foreach ($taxableEarnings as $earning)
                                            <tr>
                                                <td>{{ $earning['name'] }}</td>
                                                <td>₱{{ number_format($earning['amount'], 2) }}</td>
                                            </tr>
                                            @php
                                                $totalTaxable += $earning['amount']; // Sum up taxable earnings
                                            @endphp
                                        @endforeach
                            
                                        <tr class="total">
                                            <td colspan="1"><strong>Total</strong></td>
                                            <td><strong>₱{{ number_format($totalTaxable, 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            

                            <!-- Non-Taxable Earnings -->
                            <div class="section">
                                <h2>Non-Taxable Earnings</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @php $nonTaxableTotal = 0; @endphp
                                        @if (!empty($nonTaxableEarnings))
                                            @foreach ($nonTaxableEarnings as $earning)
                                                <tr>
                                                    <td>{{ $earning['name'] ?? 'N/A' }}</td>
                                                    <td>{{ number_format($earning['amount'] ?? 0, 2) }}</td>
                                                </tr>
                                                @php $nonTaxableTotal += $earning['amount'] ?? 0; @endphp
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="2">No non-taxable earnings available.</td>
                                            </tr>
                                        @endif

                                        <tr class="total">
                                            <td><strong>Total</strong></td>
                                            <td><strong>{{ number_format($nonTaxableTotal, 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Deductions -->
                            <div class="section">
                                <h2>Deductions</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $deductionTotal = 0; @endphp
                                        @if (!empty($deductions))
                                            @foreach ($deductions as $deduction)
                                                <tr>
                                                    <td>{{ $deduction['name'] ?? 'N/A' }}</td>
                                                    <td>{{ number_format($deduction['amount'], 2) }}</td>
                                                </tr>
                                                @php $deductionTotal += $deduction['amount'] ?? 0; @endphp
                                            @endforeach
                                        @endif
                                        <tr class="total">
                                            <td><strong>Total Deductions</strong></td>
                                            <td><strong>{{ number_format($deductionTotal, 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Loans -->
                            @if (!empty($loans))
                                <div class="section">
                                    <h2>Loans</h2>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($loans as $loan)
                                                <tr>
                                                    <td>{{ $loan['loan_name'] ?? 'N/A' }}</td>
                                                    <td>{{ number_format($loan['amount'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <div class="section">
                                <h2>Payment Methods</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Bank Name</th>
                                            <th>Account Name</th>
                                            <th>Account Number</th>
                                            <th>Net Pay</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $view->user->bankInfo->first()?->bankName ?? 'N/A' }}</td>
                                            <td>{{ $view->user->bankInfo->first()?->bankAccName ?? 'N/A' }}</td>
                                            <td>{{ $view->user->bankInfo->first()?->bankAccNumber ?? 'N/A' }}</td>
                                            <td>₱{{ number_format($view->net_pay, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="validation-note"
                                        style="margin-top: 30px; text-align: center; font-size: 14px; color: #555;">
                                        <p
                                            style="border-top: 1px solid #ddd; padding-top: 10px; line-height: 1.6; padding-left:100px; padding-right:100px;">
                                            This payslip is an official document issued by
                                            <strong>{{ $companySettings->company ?? 'the company' }}</strong>
                                            and is considered valid without the need for a manual signature.
                                            Should you require further proof of income, kindly reach out to the HR or
                                            Accounting department.
                                        </p>
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
                }).then(function(canvas) {
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
