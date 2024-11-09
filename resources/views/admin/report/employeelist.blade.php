@extends('layouts.master')
@section('title', 'Employee Record')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    /* Set a minimum width for select boxes and prevent them from shrinking */
    .form-group.select-focus {
        min-width: 200px;
        /* Adjust as needed */
        flex: 0 1 auto;
        /* Prevent shrinking */
    }

    /* Set a consistent width for buttons */
    .dropdown .btn,
    .form-group .form-control {
        min-width: 150px;
    }

    /* Date Range and Submit button */
    #dateRange,
    .btn-primary {
        min-width: 200px;
    }

    /* Adjust the height of the Select2 container to match Bootstrap input fields */
    .select2-container .select2-selection--multiple {
        height: calc(2.25rem + 2px);
        /* Match Bootstrap input height */
        padding-top: 5px;
        /* Adjust padding to align the text */
        padding-bottom: 35px;
        border: 1px solid #ced4da;
        /* Match input border color */
        border-radius: 0.25rem;
        /* Match input border radius */
        box-sizing: border-box;
    }

    .select2-container--default .select2-selection--multiple {
        padding-bottom: 45px !important;
    }

    /* Style each selected item (tag) within the Select2 field */
    .select2-container .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        border-radius: 0.25rem;
        margin: 2px 5px 2px 0;
        padding: 2px 500px;
        /* Adjust padding to fit within the height */
    }

    /* Remove extra space at the bottom to match input field height */
    .select2-container .select2-selection--multiple .select2-selection__rendered {
        padding: 0 5px;
    }

    /* Align Select2 with the same width and spacing as other form controls */
    .select2-container {
        width: 100% !important;
    }

    .filter-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        /* Space between tags */
    }

    .filter-tag {
        background-color: #f0f0f0;
        color: #333;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 8px 12px;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;
        font-size: 14px;
        white-space: nowrap;
        /* Prevent tags from breaking to multiple lines */
    }

    /* Hover effect */
    .filter-tag:hover {
        background-color: #DF1C2A;
        color: #fff;
    }

    /* Selected state */
    .filter-tag.selected {
        background-color: #DF1C2A;
        color: #fff;
        border-color: #DF1C2A;
    }

</style>

@section('content')
@include('sweetalert::alert')
<div class="content container-fluid">
    @php
    $attributeLabels = [
    // User Model Attributes
    'name' => 'Name',
    'fName' => 'First Name',
    'mName' => 'Middle Name',
    'lName' => 'Last Name',
    'email' => 'Email',
    'empNumber' => 'Employee ID',
    'typeOfContract' => 'Contract Type',
    'phoneNumber' => 'Phone Number',
    'dateHired' => 'Hire Date',
    'birthday' => 'Birthday',
    'completeAddress' => 'Address',
    'mSalary' => 'Monthly Salary',
    'position' => 'Position',
    'role_as' => 'Role',
    'sss' => 'SSS Number',
    'pagIbig' => 'Pag-IBIG Number',
    'philHealth' => 'PhilHealth Number',
    'tin' => 'TIN Number',
    'department' => 'Department',
    'reporting_to' => 'Supervisor',

    // PersonalInformation Model Attributes
    'religion' => 'Religion',
    'age' => 'Age',
    'education' => 'Education',
    'nationality' => 'Nationality',
    'mStatus' => 'Marital Status',
    'numChildren' => 'Number of Children',

    // BankInformation Model Attributes
    'bankName' => 'Bank Name',
    'bankAccName' => 'Bank Account Name',
    'bankAccNumber' => 'Bank Account Number',
    ];

    // Define attributes to exclude from filter tags
    $excludedAttributes = ['bdayLeave', 'vacLeave', 'sickLeave', 'name', 'image', 'password', 'role', 'age'];
    @endphp

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Employee Record</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Employee Record</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-primary no-print" onclick="printTable()">
                        <i class="fa fa-print"></i> Print
                    </button>
                    <button class="btn btn-success no-print" onclick="downloadCSV()">
                        <i class="fa fa-download"></i> Download as CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Form for Selecting Department and Employees -->
    <form id="optionsForm" action="{{ route('admin.employeeList') }}" method="GET"
        class="d-flex align-items-center flex-wrap p-2">
        <!-- Department Filter -->
        <div class="form-group form-focus select-focus mr-3">
            <select class="select floating form-control" name="department" id="department-select">
                <option value="" disabled selected>Select Department</option>
                <option value="all">All Departments</option>
                @foreach ($departments as $dept)
                <option value="{{ $dept }}">{{ $dept }}</option>
                @endforeach
            </select>
            <label class="focus-label">Department</label>
        </div>

        <!-- Employee Filter -->
        <div class="form-group form-focus mr-3">
            <select class="select2" name="users_id[]" id="employee-select" multiple>
                <option value="" disabled>Select Employee(s)</option>
            </select>
        </div>

        <!-- Submit Button -->
        <div class="form-group form-focus mr-3">
            <button type="button" class="btn btn-primary" id="submitSelection">Submit</button>
        </div>
    </form>

    <!-- Select/Deselect All Tags -->
    <div class="form-group mb-3">
        <button type="button" class="btn btn-success btn-sm" id="selectAll">Select All</button>
        <button type="button" class="btn btn-danger btn-sm" id="deselectAll">Deselect All</button>
    </div>

    <!-- Filter Tags -->
    <!-- Filter Tags with Attributes from User and PersonalInformation -->
    <div class="filter-tags">
        @foreach ($attributeLabels as $attribute => $label)
        @if (!in_array($attribute, $excludedAttributes))
        <div class="filter-tag" data-attribute="{{ $attribute }}">
            {{ $label }}
        </div>
        @endif
        @endforeach
    </div>


    <div class="row mt-3">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="attributeTable" class="table table-hover table-nowrap mb-0 datatable">
                    <thead>
                        <tr id="tableHeader"></tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Data rows will be appended here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
@endsection

@section('scripts')

<script>
    // Pass the PHP attribute labels to JavaScript as a JSON object
    const attributeLabels = @json($attributeLabels);

</script>

<script>
    $(document).ready(function () {
        // Initialize Select2 for the employee select box
        $('#employee-select').select2({
            placeholder: "Select Employee(s)",
            width: '100%' // Ensures the Select2 dropdown has the same width as other inputs
        });

        // When a department is selected
        $('#department-select').change(function () {
            var department = $(this).val();

            // Clear previous employee options
            $('#employee-select').empty();

            if (department) {
                $.ajax({
                    url: '{{ route("admin.getEmployeesByDepartment") }}',
                    method: 'GET',
                    data: {
                        department: department
                    },
                    success: function (data) {
                        $.each(data, function (key, employee) {
                            // Add employee options to Select2 dropdown
                            $('#employee-select').append(new Option(employee.name,
                                employee.id, false, false));
                        });
                        $('#employee-select').trigger(
                            'change'); // Refresh Select2 with new options
                    },
                    error: function () {
                        alert('Unable to fetch employees.');
                    }
                });
            }
        });
    });

</script>

<script>
    let selectedAttributes = [];

    // Fetch employees when a department is selected
    document.getElementById('department-select').addEventListener('change', function () {
        const department = this.value;

        // Clear the employee select box
        document.getElementById('employee-select').innerHTML =
            '<option value="" disabled>Select Employee(s)</option>';

        if (department) {
            fetch(`/admin/report/get-employees?department=${department}`)
                .then(response => response.json())
                .then(data => {
                    console.log("Employees fetched:", data);
                    data.employees.forEach(employee => {
                        const option = document.createElement("option");
                        option.value = employee.id;
                        option.text = `${employee.fName} ${employee.lName}`;
                        document.getElementById('employee-select').appendChild(option);
                    });
                })
                .catch(error => console.error("Error fetching employees:", error));
        }
    });

    // Toggle selection of filter tags
    document.querySelectorAll('.filter-tag').forEach(tag => {
        tag.addEventListener('click', function () {
            const attribute = this.getAttribute('data-attribute');
            this.classList.toggle('selected');

            if (selectedAttributes.includes(attribute)) {
                selectedAttributes = selectedAttributes.filter(attr => attr !== attribute);
            } else {
                selectedAttributes.push(attribute);
            }

            updateTableHeader();
        });
    });

    // Select All and Deselect All Buttons
    document.getElementById('selectAll').addEventListener('click', function () {
        selectedAttributes = [];
        document.querySelectorAll('.filter-tag').forEach(tag => {
            const attribute = tag.getAttribute('data-attribute');
            if (!selectedAttributes.includes(attribute)) {
                selectedAttributes.push(attribute);
                tag.classList.add('selected');
            }
        });
        updateTableHeader();
    });

    document.getElementById('deselectAll').addEventListener('click', function () {
        selectedAttributes = [];
        document.querySelectorAll('.filter-tag').forEach(tag => {
            tag.classList.remove('selected');
        });
        updateTableHeader();
    });

    // Update table header based on selected attributes
    function updateTableHeader() {
        const headerRow = document.getElementById('tableHeader');
        headerRow.innerHTML = ''; // Clear existing headers

        selectedAttributes.forEach(attr => {
            const th = document.createElement('th');
            th.textContent = attributeLabels[attr] || attr.replace(/_/g, ' ').toUpperCase();
            headerRow.appendChild(th);
        });
    }

    document.getElementById('submitSelection').addEventListener('click', function () {
        const employees = Array.from(document.getElementById('employee-select').selectedOptions).map(option =>
            option.value);
        if (employees.length === 0 || selectedAttributes.length === 0) return;

        fetch(
                `/admin/report/get-employee-data?employees=${employees.join(',')}&attributes=${selectedAttributes.join(',')}`
            )
            .then(response => response.json())
            .then(data => {
                console.log("Data received from server:", data); // DEBUG

                const tableBody = document.getElementById('tableBody');
                tableBody.innerHTML = ''; // Clear existing rows

                data.forEach(row => {
                    const tr = document.createElement('tr');
                    selectedAttributes.forEach(attr => {
                        const td = document.createElement('td');
                        td.textContent = row[attr] || ''; // Display attribute data
                        tr.appendChild(td);
                    });
                    tableBody.appendChild(tr);
                });
            })
            .catch(error => console.error("Error fetching data:", error));
    });

</script>

<script>
    function downloadCSV() {
        let csvContent = "";
        const table = document.getElementById("attributeTable");

        // Extract headers
        const headers = Array.from(table.querySelectorAll("thead th")).map(th => th.innerText.trim());
        csvContent += headers.join(",") + "\n";

        // Extract rows
        Array.from(table.querySelectorAll("tbody tr")).forEach(row => {
            const rowData = Array.from(row.querySelectorAll("td")).map(td => td.innerText.trim());
            csvContent += rowData.join(",") + "\n";
        });

        // Trigger download
        const blob = new Blob([csvContent], {
            type: "text/csv;charset=utf-8;"
        });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "table_data.csv";
        link.style.display = "none";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function printTable() {
        const tableContent = document.getElementById("attributeTable").outerHTML;
        const style = `
        <style>
            table { width: 100%; border-collapse: collapse; }
            table, th, td { border: 1px solid black; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style>
    `;
        const printWindow = window.open("", "", "height=600,width=800");
        printWindow.document.write("<html><head><title>Print Table</title>");
        printWindow.document.write(style);
        printWindow.document.write("</head><body>");
        printWindow.document.write(tableContent);
        printWindow.document.write("</body></html>");
        printWindow.document.close();
        printWindow.print();
    }

</script>


@endsection
