@extends('layouts.hrmaster') @section('title', 'Employee Payroll')

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
    }

    .content {
        padding-top: 20px;
    }

    .page-header {
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .page-title {
        margin: 0;
        font-weight: 600;
    }

    .breadcrumb {
        margin-bottom: 0;
        font-size: 14px;
    }

    .card {
        border-radius: 0;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.9);
        border: 2px solid red;
    }

    .payslip-title {
        margin-top: 0;
        font-size: 24px;
        font-weight: 600;
        text-align: center;
    }

    .inv-logo {
        max-height: 100px;
    }

    .invoice-details {
        text-align: right;
    }

    .invoice-details h3 {
        margin-top: 0;
        font-size: 18px;
    }

    .list-unstyled {
        margin-bottom: 0;
    }

    .list-unstyled li {
        list-style-type: none;
    }

    .list-unstyled li span {
        color: #888;
    }

    .table-bordered {
        border: 1px solid #ddd;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .float-end {
        float: right;
    }

    .m-b-20 {
        margin-bottom: 20px;
    }

    .m-b-10 {
        margin-bottom: 10px;
    }

    .mb-0 {
        margin-bottom: 0;
    }

    .text-uppercase {
        text-transform: uppercase;
    }

    .text-center {
        text-align: center;
    }

    .card-body {
        padding: 20px;
    }
</style>

@section('content')

<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Payslip</h3>
            </div>
            <div class="col-auto float-end ms-auto">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-primary" onclick="downloadAsPDF()">
                        <i class="material-icons">download</i>
                    </button>
                    <button class="btn btn-primary" onclick="printCard()">
                        <i class="material-icons">print</i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="payslip-title">
                        Payslip for the month of Feb 2019
                    </h4>
                    <div class="row">
                        <div class="col-sm-6 m-b-20">
                            <img
                                src="../assets/img/jaflogo.png"
                                class="inv-logo"
                                alt=""
                            />
                            <h3>JAF Digital</h3>
                            <ul class="list-unstyled mb-0">
                                <li>Dreamguy's Technologies</li>
                                <li>3864 Quiet Valley Lane,</li>
                                <li>Sherman Oaks, CA, 91403</li>
                            </ul>
                        </div>
                        <div class="col-sm-6 m-b-20">
                            <div class="invoice-details">
                                <h3 class="text-uppercase">Payslip #49029</h3>
                                <ul class="list-unstyled">
                                    <li>
                                        Salary Month: <span>March, 2019</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 m-b-20">
                            <ul class="list-unstyled">
                                <li>
                                    <h5 class="mb-0">
                                        <strong>John Doe</strong>
                                    </h5>
                                </li>
                                <li><span>Web Designer</span></li>
                                <li>Employee ID: FT-0009</li>
                                <li>Joining Date: 1 Jan 2013</li>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div>
                                <h4 class="m-b-10">
                                    <strong>Earnings</strong>
                                </h4>
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong>Basic Income</strong>
                                                <span class="float-end"
                                                    >$6500</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Vacation Leave</strong>
                                                <span class="float-end"
                                                    >$55</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Sick Leave</strong>
                                                <span class="float-end"
                                                    >$55</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Regular Holiday</strong>
                                                <span class="float-end"
                                                    >$55</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong
                                                    >Birthday PTO Leave</strong
                                                >
                                                <span class="float-end"
                                                    >$55</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Overtime</strong>
                                                <span class="float-end"
                                                    >$55</span
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div>
                                <h4 class="m-b-10">
                                    <strong>Bonus</strong>
                                </h4>
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong>Allowance</strong>
                                                <span class="float-end"
                                                    >$100</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>13th Month</strong>
                                                <span class="float-end"
                                                    >$200</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Others</strong>
                                                <span class="float-end"
                                                    >$50</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong
                                                    >Performance Bonus</strong
                                                >
                                                <span class="float-end"
                                                    >$150</span
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div>
                                <h4 class="m-b-10">
                                    <strong>Deductions</strong>
                                </h4>
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong>Late</strong>
                                                <span class="float-end"
                                                    >$6500</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Withholding Tax</strong>
                                                <span class="float-end"
                                                    >$55</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>SSS</strong>
                                                <span class="float-end"
                                                    >$55</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Phil Health</strong>
                                                <span class="float-end"
                                                    >$55</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Pag-IBIG</strong>
                                                <span class="float-end"
                                                    >$55</span
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div>
                                <h4 class="m-b-10">
                                    <strong>Total</strong>
                                </h4>
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong style="color: red"
                                                    >NET PAY</strong
                                                >
                                                <span
                                                    class="float-end"
                                                    style="color: red"
                                                    >$5000</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Total Earnings</strong>
                                                <span class="float-end"
                                                    >$5000</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong
                                                    >Total Deductions</strong
                                                >
                                                <span class="float-end"
                                                    >$200</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Total Hours</strong>
                                                <span class="float-end"
                                                    >160</span
                                                >
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <strong
                                                    >Gross Monthly Pay</strong
                                                >
                                                <span class="float-end"
                                                    >$4800</span
                                                >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Gross Basic Pay</strong>
                                                <span class="float-end"
                                                    >$4000</span
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <p>
                                <strong>Note: </strong>Lorem ipsum dolor sit
                                amet, consectetur adipisicing elit. Aut, quod
                                suscipit provident totam perspiciatis similique
                                consectetur velit itaque quisquam ullam.
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="clearfix m-t-40">
                                <h5 class="small text-uppercase">
                                    <strong>Authorized Signature:</strong>
                                </h5>
                                <p>________________________</p>
                                <p class="text-muted">Director</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="clearfix m-t-40">
                                <h5 class="small text-uppercase">
                                    <strong>Employee Signature:</strong>
                                </h5>
                                <p>________________________</p>
                                <p class="text-muted">Employee</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
<script>
    function printCard() {
        var printContents = document.querySelector(".card").innerHTML;
        var originalContents = document.body.innerHTML;

        // Add CSS styles for printing
        var printStyles = `
        <style>
            @media print {
                .inv-logo {
                    display: block !important;
                }
                .no-print {
                    display: none !important;
                }
            }
        </style>
    `;

        document.body.innerHTML = printStyles + printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

    function downloadAsPDF() {
        // Create a new jsPDF instance
        var doc = new jsPDF();

        // Get the HTML content of the card
        var card = document.querySelector(".card");
        var cardContent = card.innerHTML;

        // Define options for the HTML to PDF conversion
        var options = {
            format: "jpeg",
            quality: 0.9,
            orientation: "portrait",
        };

        // Convert HTML to PDF
        doc.html(cardContent, options);

        // Save the PDF file
        doc.save("payslip.pdf");
    }
</script>

@endsection
