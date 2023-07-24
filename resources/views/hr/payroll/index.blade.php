@extends('layouts.hrmaster') @section('title', 'Employee Payroll')

<style>
    .table-container {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .table-heading {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .table-divider {
        border-top: 2px solid #ddd;
        margin: 30px 0;
    }

    .btn-edit {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    .btn-view {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: #fff;
    }

    .btn-icon {
        padding: 5px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
    }
    .cal-icon {
        position: relative;
        width: 100%;
    }
</style>

@section('content')
<br />
<div class="content container-fluid">
    @if(session('success'))
    <div class="alert alert-success">
        {{ session("success") }}
    </div>
    @endif
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Payroll</h3>
            </div>
        </div>

        <!-- CREATE PAYSLIP -->
<div class="column0">
    <form action="{{ route('emp.payroll') }}" method="GET" id="filter-form">
        <div class="date">
            <div class="cold-md-6">
                <div class="cal-icon">
                    <label for="start_date">Start Date:</label>
                    <input class="form-control floating datetimepicker" type="date" id="start_date" name="start_date"  style="width:90%; text-align: center;"/>
                </div>
            </div>
    
                
                <div class="cold-md-6">
                    <label for="end_date">End Date:</label>
                    <input class="form-control floating datetimepicker" type="date" id="end_date" name="end_date" style="width:90%; text-align: center;"/>
                </div>
                
                <div class="col-md-6">
                    <label for="user_id">Employee:</label>
                    <select class="form-control" id="user_id" name="user_id" >
                        <option value="">-- Select --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
    
                <button type="submit" class="button-50">Find</button>
        </div>
    </form>
 </div>
  
    <div id="table-container">
        <h2 class="table-heading">Create Payroll</h2>
            @if (isset($filteredData))
        <table class="table" style="width: 100%" id="dataTable" style="display:none">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Hourly Rate</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Late</th>
                    <th>Total Hours</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @if(count($filteredData) > 0)
                    <tr>
                        <td>{{ $filteredData[0]->name }}</td>
                        <td>{{ $filteredData[0]->hourlyRate }}</td>
                        <td>{{ $startDate }}</td>
                        <td>{{ $endDate }}</td>
                        <td>{{ $totalLate }}</td>
                        <td>{{ $total }}</td>
                        <td>
                            <button type="button" class="btn btn-primary" onclick="confirmSave()">Create Payroll</button>
                            <!-- <button type="button" class="btn btn-primary" onclick="displayPayroll('totalInput', 'hourlyRateInput')">Create Payroll</button> -->

                        </td>
                    </tr>
                @else
                    <tr>
                        <td colspan="7" style="text-align: center;">No Time record</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif
    </div>



    <div id="table-container">
        <h2 class="table-heading">Payslip</h2>
        <table class="table" style="width: 100%" id="example2" style="display:none">
            <thead class="thead-dark">
                <tr>
                    <th>Employee Name</th>
                    <th>Payroll Date</th>
                    <th>Cut-Off Start</th>
                    <th>Cut-Off End</th>
                    <th>Total Salary</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($salaries as $salary)
                <tr>
                    <td>{{ $salary->employee_name }}</td>
                    <td>{{ $salary->payroll_date }}</td>
                    <td>{{ $salary->payroll_start }}</td>
                    <td>{{ $salary->payroll_end }}</td>
                    <td>{{ $salary->salary }}</td>
                    <td>
                        <a href="#" class="btn btn-primary" title="Edit" data-toggle="modal" data-target="#editModal">
                            <i class="material-icons">edit</i>
                        </a>
                        <a href="{{ url('hr/payroll/view/'.$salary->id) }}" class="btn btn-info" title="View">
                            <i class="material-icons">visibility</i>
                        </a>
                        <a href="{{ url('hr/payroll/click_delete/'.$salary->id) }}"
                            class="btn btn-danger"
                            id="delBTN"
                            onclick="confirmation(event)">
                              <i class="material-icons">delete</i>
                        </a>
                        
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</div>

<!-- EDIT POPUP -->

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Popup</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Add your desired popup content here -->
                This is the edit popup!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- END EDIT POPUP -->




<div
            id="popup-form"
            class="modal custom-modal fade"
            style="display: none"
            data-select2-id="select2-data-add_salary"
            aria-hidden="true"
        >
            <div
                class="modal-dialog modal-dialog-centered modal-lg"
                role="document"
            >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Staff Salary</h5>
                        <button
                            type="button"
                            class="close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            @csrf
                            <div class="row">

                                <div class="col-sm-6">
                                    <h4 class="text-primary">Deductions</h4>
                                    <div class="form-group">
                                        <label>Late</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="late"
                                            name="late"
                                            value="{{ $totalLate }}"
                                            oninput="calculateTotal()"
                                            readonly
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Absence</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="absence"
                                            name="absence"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Withholding Tax</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="withholding_tax"
                                            name="withholding_tax"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h4 class="text-primary">..</h4>
                                    <div class="form-group">
                                        <label>SSS</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="sss"
                                            name="sss"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Phil Health</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="phil_health"
                                            name="phil_health"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Pag-Ibig</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="pag_ibig"
                                            name="pag_ibig"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <h4 class="text-primary">Earnings</h4>
                                    <div class="form-group">

                                        <input type="text" id="totalInput" value="{{ $total }}" name="totalInput" oninput="calculateTotal()" >
                                        <input type="text" id="hourlyRateInput" value="{{ $filteredData[0]->hourlyRate }}" name="hourlyRateInput" oninput="calculateTotal()">

                                    
                                        <label>Regular Holiday</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="regular_holiday"
                                            name="regular_holiday"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Special Holiday</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="special_holiday"
                                            name="special_holiday"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Working on Restday</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="working_on_restday"
                                            name="working_on_restday"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Working on Weekend</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="working_on_weekend"
                                            name="working_on_weekend"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Working on Night Shift</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="working_on_nightshift"
                                            name="working_on_nightshift"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Birthday PTO Leave</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="birthday_pto_leave"
                                            name="birthday_pto_leave"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h4 class="text-primary">..</h4>
                                    <div class="form-group">
                                        <label>Overtime</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="overtime"
                                            name="overtime"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>13th Month</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="thirteenth_month"
                                            name="thirteenth_month"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Christmas Bonus</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="christmas_bonus"
                                            name="christmas_bonus"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Food Allowance</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="food_allowance"
                                            name="food_allowance"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Performance Bonus</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="performance_bonus"
                                            name="performance_bonus"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                    <div class="form-group">
                                        <label>Others</label>
                                        <input
                                            class="form-control"
                                            type="text"
                                            id="others"
                                            name="others"
                                            oninput="calculateTotal()"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="submit-section">
                                <div class="button-group">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="saveData()" data-bs-dismiss="modal" aria-label="Close">Save</button>
                                </div>
                                <div class="col-sm-6">
                                <label for="">Calculated Salary : </label>
                                <input class="form-control" type="text" id="total" name="total" readonly />
                                <input class="form-control" type="text" id="latetotal" name="latetotal" readonly />
                                <input class="form-control" type="text" id="earnings" name="earnings" readonly />
                                <input class="form-control" type="text" id="totaldeduc" name="totaldeduc" readonly />
                                <input class="form-control" type="text" id="grossmp" name="grossmp" readonly />
                                <input class="form-control" type="text" id="grossb" name="grossb" readonly />
                            </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function confirmation(ev) {
                ev.preventDefault();
                var urlToRedirect = ev.currentTarget.getAttribute("href");
                console.log(urlToRedirect);
                swal({
                    title: "Are you to delete this data?",
                    text: "You will not be able to revert this!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willCancel) => {
                    if (willCancel) {
                        window.location.href = urlToRedirect;
                    }
                });
            }
        </script>

        <script>
            function calculateTotal() {
                
                // Get the values from the input fields (EARNINGS)
                var rh = parseInt(document.getElementById('regular_holiday').value) || 0;
                var sh = parseInt(document.getElementById('special_holiday').value) || 0;
                var wor = parseInt(document.getElementById('working_on_restday').value) || 0;
                var wow = parseInt(document.getElementById('working_on_weekend').value) || 0;
                var won = parseInt(document.getElementById('working_on_nightshift').value) || 0;
                var bpl = parseInt(document.getElementById('birthday_pto_leave').value) || 0;
                var ot = parseInt(document.getElementById('overtime').value) || 0;
                var tm = parseInt(document.getElementById('thirteenth_month').value) || 0;
                var cb = parseInt(document.getElementById('christmas_bonus').value) || 0;
                var fa = parseInt(document.getElementById('food_allowance').value) || 0;
                var pb = parseInt(document.getElementById('performance_bonus').value) || 0;
                var oth = parseInt(document.getElementById('others').value) || 0;

                // Get the values from the input fields (DEDUCTIONS)
                var lt = document.getElementById('late').value;
                var abs = parseInt(document.getElementById('absence').value) || 0;
                var wht = parseInt(document.getElementById('withholding_tax').value) || 0;
                var ss = parseInt(document.getElementById('sss').value) || 0;
                var ph = parseInt(document.getElementById('phil_health').value) || 0;
                var pi = parseInt(document.getElementById('pag_ibig').value) || 0;

                var total = document.getElementById('totalInput').value;
                var hourlyRate = parseFloat(document.getElementById('hourlyRateInput').value) || 0;

                // Convert time value to decimal format
                var totalDecimal = convertTimeToDecimal(total);
                var totalLateMinutes = convertTimeToMinutes(lt);

                // Perform the calculation
                // Late Rate
                var ltc = hourlyRate / 60; 
                var ld = totalLateMinutes * ltc; // Total Late Deduction

                // Total Earnings
                var earningsTotal = totalDecimal * hourlyRate; 
                var pd = hourlyRate * 8; // Per Day 

                // Regular Holiday
                var rht = (rh > 0 ) ? (hourlyRate * rh - pd) : 0; // regular holiday computation

                // Special Holiday
                var sht = hourlyRate * 1.3; // Special Holiday Rate
                var shtt = (sh > 0) ? (sh * sht - pd) : 0; 

                // Working on Restday Computation
                var worc = hourlyRate * 0.3;
                var wort = (wor > 0 ) ? (wor * worc - pd) : 0;

                // Working on Weekend Computation
                var wowc = hourlyRate * 0.3;
                var wowt = (wow > 0) ? (wow * wowc - pd) : 0;

                // Working on Night Shift
                var wonc = hourlyRate * 0.1;
                var wont = (won > 0) ? (won * wonc - pd) : 0;

                // Birthday PTO Leave 
                var bplc = (bpl > 0 ) ? (hourlyRate * bpl - pd ) : 0;

                // Overtime
                var otc = hourlyRate * ot;

                // Gross Monthly Pay
                var gmp = Math.floor(hourlyRate * 8 * 22);
                var gb = gmp / 2;

                //Overall Computation 
                var total2 = earningsTotal + shtt + rht + wort + wowt + wont + bplc + otc + cb + fa + pb + oth; // Total Computation Earnings
                var total3 = total2 - ld  - wht - ss - ph - pi; // Total Computation with Deductions
                var total4 = ld + wht + ss + ph + pi;

                total3 = total3.toFixed(2);
                ld = ld.toFixed(2);
                earningsTotal = earningsTotal.toFixed(2);
                total4 = total4.toFixed(2);
                

                // Update the total field
                document.getElementById('total').value = total3;
                document.getElementById('latetotal').value = ld;
                document.getElementById('earnings').value = earningsTotal
                document.getElementById('totaldeduc').value = total4;
                document.getElementById('grossmp').value = gmp;
                document.getElementById('grossb').value = gb;

            }

            function convertTimeToDecimal(time) {
                var parts = time.split(':');
                var hours = parseInt(parts[0], 10);
                var minutes = parseInt(parts[1], 10);
                var seconds = parseInt(parts[2], 10);
                var decimal = hours + minutes / 60 + seconds / 3600;
                return decimal;
            }

            function convertTimeToMinutes(time) {
                var parts = time.split(':');
                var hours = parseInt(parts[0], 10);
                var minutes = parseInt(parts[1], 10);
                var seconds = parseInt(parts[2], 10);
                var totalMinutes = hours * 60 + minutes + seconds / 60;
                return totalMinutes;
            }

        </script>
 
    @endsection @section('scripts')
    <script>
        function confirmSave() {
            if (confirm("Are you sure you want to create a payroll for this employee?")) {
                // If the user clicks "OK", proceed with saving the data
                showPopupForm();
            } else {
                // If the user clicks "Cancel", do nothing
                return;
            }
        }

         function showPopupForm() {
            // Show the popup form
            $('#popup-form').modal('show');
        }
        </script>
    <script>
        function saveData() {
            // Get the data from the table
            var name = "{{ $filteredData[0]->name }}";
            var userId = "{{ $filteredData[0]->users_id }}";
            var startDate = "{{ $startDate }}";
            var endDate = "{{ $endDate }}";
            var totalLate = "{{ $totalLate }}";
            var total = "{{ $total }}";
            var regularHoliday = $("#regular_holiday").val();
            var specialHoliday = $("#special_holiday").val();
            var workingOnRestday = $("#working_on_restday").val();
            var workingOnWeekend = $("#working_on_weekend").val();
            var workingOnNightshift = $("#working_on_nightshift").val();
            var birthdayPtoLeave = $("#birthday_pto_leave").val();
            var late = $("#late").val();
            var absence = $("#absence").val();
            var withholdingTax = $("#withholding_tax").val();
            var sss = $("#sss").val();
            var pagIbig = $("#pag_ibig").val();
            var philHealth = $("#phil_health").val();
            var overtime = $("#overtime").val();
            var thirteenthMonth = $("#thirteenth_month").val();
            var christmasBonus = $("#christmas_bonus").val();
            var foodAllowance = $("#food_allowance").val();
            var performanceBonus = $("#performance_bonus").val();
            var others = $("#others").val();
            var totalField = $("#total").val(); // Added line to get the value of the "total" field
            var latetotal = $("#latetotal").val(); // Added line to get the value of the "latetotal" field
            var earnings = $("#earnings").val(); // Added line to get the value of the "earnings" field
            var totaldeduc = $("#totaldeduc").val(); // Added line to get the value of the "totaldeduc" field
            var grossmp = $("#grossmp").val(); // Added line to get the value of the "grossmp" field
            var grossb = $("#grossb").val(); // Added line to get the value of the "grossb" field
    
            // Send an AJAX request to check if the payroll data already exists
            $.ajax({
                type: "POST",
                url: "{{ route('employee.salaries.check') }}",
                data: {
                    name: name,
                    start_date: startDate,
                    end_date: endDate,
                    _token: "{{ csrf_token() }}",
                },
                success: function (response) {
                    // The payroll data does not exist, so save it
                    $.ajax({
                        type: "POST",
                        url: "{{ route('employee.salaries.store') }}",
                        data: {
                            name: name,
                            start_date: startDate,
                            end_date: endDate,
                            total_late: totalLate,
                            total_hours: total,
                            regular_holiday: regularHoliday,
                            special_holiday: specialHoliday,
                            user_id: userId,
                            working_on_restday: workingOnRestday,
                            working_on_weekend: workingOnWeekend,
                            working_on_nightshift: workingOnNightshift,
                            birthday_pto_leave: birthdayPtoLeave,
                            late: late,
                            absence: absence,
                            withholding_tax: withholdingTax,
                            sss: sss,
                            pag_ibig: pagIbig,
                            phil_health: philHealth,
                            overtime: overtime,
                            thirteenth_month: thirteenthMonth,
                            christmas_bonus: christmasBonus,
                            food_allowance: foodAllowance,
                            performance_bonus: performanceBonus,
                            others: others,
                            total: totalField,
                            latetotal: latetotal, // Added line to include the "latetotal" field value
                            earnings: earnings, // Added line to include the "earnings" field value
                            totaldeduc: totaldeduc, // Added line to include the "totaldeduc" field value
                            grossmp: grossmp, // Added line to include the "grossmp" field value
                            grossb: grossb, // Added line to include the "grossb" field value
                            _token: "{{ csrf_token() }}",
                        },
                        success: function (response) {
                            alert("Data saved successfully.");
                        },
                        error: function (xhr) {
                            alert("An error occurred while saving the data.");
                        },
                    });
                },
                error: function (xhr) {
                    // The payroll data already exists
                    alert(xhr.responseJSON.message);
                },
            });
        }
    </script>
        
    <script>
        // Attach event listener to form submit
        document.getElementById('filterForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent form submission
    
            // Perform AJAX request to retrieve filtered data
            // Replace the URL with the actual route or endpoint for retrieving filtered data
            // You may also need to pass the selected values from the form as parameters to the URL
            axios.get('/getFilteredData', {
                params: {
                    start_date: document.getElementById('start_date').value,
                    end_date: document.getElementById('end_date').value,
                    user_id: document.getElementById('user_id').value,
                }
            })
            .then(function (response) {
                // Update table with retrieved data
                document.getElementById('dataTable').innerHTML = response.data;
                document.getElementById('dataTable').style.display = 'table'; // Show table
            })
            .catch(function (error) {
                console.error(error);
            });
        });
    </script>

    <script>
        function displayPayroll(totalInputId, hourlyRateInputId) {
            var totalValue = document.getElementById(totalInputId).value;
            var hourlyRateValue = document.getElementById(hourlyRateInputId).value;

            // Display the values in an input text field
            document.getElementById('payrollTotal').value = totalValue;
            document.getElementById('payrollHourlyRate').value = hourlyRateValue;
        }
    </script>
    @endsection
</div>
