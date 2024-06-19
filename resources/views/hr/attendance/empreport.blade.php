@extends('layouts.hrmaster') @section('title', 'Attendance') @section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Employees Attendance Records</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/hr/dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Employees Attendance Records
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    <!-- Search Filter -->
    <form action="{{ route('report.empindex') }}" method="GET">
        @csrf
        <div class="row filter-row">
            <div class="col-sm-2">
                <div class="form-group form-focus">
                    <div class="cal-icon">
                        <input type="text" class="form-control floating datetimepicker" id="start_date"
                            name="start_date" />
                    </div>
                    <label class="focus-label">From</label>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group form-focus">
                    <div class="cal-icon">
                        <input type="text" class="form-control floating datetimepicker" id="end_date" name="end_date" />
                    </div>
                    <label class="focus-label">To</label>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="filter" id="filter">
                        <option value="">--</option>
                        <option value="last_15_days">Last 15 Days</option>
                        <option value="last_30_days">Last 30 Days</option>
                        <option value="last_year">Last Year</option>
                    </select>
                    <label class="focus-label">Select Days</label>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group form-focus select-focus">
                    <select class="select floating" name="month" id="month">
                        <option>-</option>
                        <option>Jan</option>
                        <option>Feb</option>
                        <option>Mar</option>
                        <option>Apr</option>
                        <option>May</option>
                        <option>Jun</option>
                        <option>Jul</option>
                        <option>Aug</option>
                        <option>Sep</option>
                        <option>Oct</option>
                        <option>Nov</option>
                        <option>Dec</option>
                    </select>
                    <label class="focus-label">Select Month</label>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group form-focus select-focus">
                    <select class="form-control" id="user_id" name="user_id">
                        <option value="">-</option>
                        <option value="">All Employees</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}</option>
                        @endforeach
                    </select>
                    <label class="focus-label">Select Employee</label>
                </div>
            </div>

            <div class="col-sm-2">
                <button type="submit" class="btn btn-success btn-block">
                    Search
                </button>
            </div>
        </div>
    </form>
    <!-- /Search Filter -->
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                @csrf
                <table class="table datatable" id="edittable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Break In</th>
                            <th>Break Out</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th>Total Late</th>
                            <th>Total Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filteredData as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->date }}</td>
                            <td>{{ $item->timeIn }}</td>
                            <td>{{ $item->breakIn }}</td>
                            <td>{{ $item->breakOut }}</td>
                            <td>{{ $item->timeOut }}</td>
                            <td>
                                <span class="{{ $item->status == 'Late' ? 'bg-inverse-danger' : 'bg-inverse-success' }}"
                                    style="
                                        padding: 5px 10px 5px 10px;
                                        border-radius: 5px;
                                        font-size: 12px;
                                        font-weight: bold;
                                        color: white;
                                    ">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>{{ $item->totalLate }}</td>
                            <td>
                                {{ $item->timeTotal }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7">Total</th>
                            <th></th>
                            <th>{{ $totalLate }}</th>
                            <th id="total_hours">
                                {{ $total }}
                            </th>

                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script type="text/javascript">
    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $("input[name=_token]").val()
            }
        });

        $('#edittable').Tabledit({
            url: '{{ route("update.table") }}',
            dataType: "json",
            columns: {
                identifier: [0, 'id'],
                editable: [
                    [3, 'timeIn'],
                    [4, 'breakIn'],
                    [5, 'breakOut'],
                    [6, 'timeOut']
                ]
            },
            restoreButton: false,
            onSuccess: function (data, textStatus, jqXHR) {
                if (data.action == 'delete') {
                    $('#' + data.id).remove();
                }
                location.reload(); // Refresh the browser
            }
        });

    });

</script>
@endsection
