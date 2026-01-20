@extends('admin.includes.master')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.admin-dashboard') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label for="exam_session_id">Exam Session</label>
                            <select name="exam_session_id" id="exam_session_id" class="form-control">
                                <option value="">All Exam Sessions</option>
                                @foreach($examsession as $session)
                                    <option value="{{ $session->id }}" {{ request('exam_session_id') == $session->id ? 'selected' : '' }}>
                                        {{ $session->session_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="branch_id">Branch</label>
                            <select name="branch_id" id="branch_id" class="form-control">
                                <option value="">All Branches</option>
                                @foreach($courses as $course)
                                    <optgroup label="{{ $course->name }}">
                                        @foreach($course->branches as $branch)
                                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="semester_id">Semester</label>
                            <select name="semester_id" id="semester_id" class="form-control">
                                <option value="">All Semesters</option>
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                        {{ $sem->semester_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="admission_session_id">Admission Session</label>
                            <select name="admission_session_id" id="admission_session_id" class="form-control">
                                <option value="">All Admission Sessions</option>
                                @foreach($admission_sessions as $ads)
                                    <option value="{{ $ads->id }}" {{ request('admission_session_id') == $ads->id ? 'selected' : '' }}>
                                        {{ $ads->session_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Apply Filters</button>
                            <a href="{{ route('admin.admin-dashboard') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Total Students Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Students</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$studentCount}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa-users text-gray-300" style="font-size:36px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Forms Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Exam Forms</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$examFormCount}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa-file-invoice text-gray-300" style="font-size:36px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Paid Fee Status Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                          Paid Fee Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$paystatus}}/{{$examFormCount}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-check-circle text-gray-300" style="font-size:26px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collected Fee Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Collected Fee</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($totalFee, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-rupee-sign text-gray-300" style="font-size:36px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Students per Branch -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Students per Branch</h6>
            </div>
            <div class="card-body">
                <div id="branch_chart_div" style="width: 100%; height: 400px;"></div>
            </div>
        </div>
    </div>

    <!-- Students per Semester -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Students per Semester</h6>
            </div>
            <div class="card-body">
                <div id="semester_chart_div" style="width: 100%; height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Students per Session -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Students per Admission Session</h6>
            </div>
            <div class="card-body">
                <div id="session_chart_div" style="width: 100%; height: 400px;"></div>
            </div>
        </div>
    </div>

     <!-- Exam Forms per Course (Legacy) -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Exam Forms per Course</h6>
            </div>
            <div class="card-body">
                <div id="bar_chart_div" style="width: 100%; height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
     <!-- Payment Status -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Payment Status Details</h6>
            </div>
            <div class="card-body">
                <div id="pie_chart_div" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        drawBranchChart();
        drawSemesterChart();
        drawSessionChart();
        drawExamFormChart();
        drawPaymentChart();
    }

    function drawBranchChart() {
        var data = google.visualization.arrayToDataTable([
            ['Branch', 'Students', { role: 'style' }],
            @foreach($branchNames as $index => $name)
                ['{{ $name }}', {{ $branchCounts[$index] }}, '#36b9cc'],
            @endforeach
        ]);

        var options = {
            title: 'Student Distribution by Branch',
            legend: { position: 'none' },
            hAxis: { title: 'Branch' },
            vAxis: { title: 'Students' },
            animation: { startup: true, duration: 1000, easing: 'out' }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('branch_chart_div'));
        chart.draw(data, options);
    }

    function drawSemesterChart() {
        var data = google.visualization.arrayToDataTable([
            ['Semester', 'Students'],
            @foreach($semesterNames as $index => $name)
                ['{{ $name }}', {{ $semesterCounts[$index] }}],
            @endforeach
        ]);

        var options = {
            title: 'Student Distribution by Semester',
            is3D: true,
             pieHole: 0.2,
        };

        var chart = new google.visualization.PieChart(document.getElementById('semester_chart_div'));
        chart.draw(data, options);
    }

    function drawSessionChart() {
        var data = google.visualization.arrayToDataTable([
            ['Session', 'Students', { role: 'style' }],
            @foreach($sessionNames as $index => $name)
                ['{{ $name }}', {{ $sessionCounts[$index] }}, '#f6c23e'],
            @endforeach
        ]);

        var options = {
            title: 'Student Distribution by Admission Session',
             legend: { position: 'none' },
             hAxis: { title: 'Session' },
            vAxis: { title: 'Students' },
            animation: { startup: true, duration: 1000, easing: 'out' }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('session_chart_div'));
        chart.draw(data, options);
    }

    function drawExamFormChart() {
         var data = google.visualization.arrayToDataTable([
            ['Course', 'Exam Forms', { role: 'style' }],
            @foreach($courseNames as $index => $name)
                ['{{ $name }}', {{ $courseCounts[$index] }}, '#4e73df'],
            @endforeach
        ]);

        var options = {
            title: 'Exam Forms by Course',
            legend: { position: 'none' },
             hAxis: { title: 'Course' },
            vAxis: { title: 'Forms' },
            animation: { startup: true, duration: 1000, easing: 'out' }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('bar_chart_div'));
        chart.draw(data, options);
    }

    function drawPaymentChart() {
        var data = google.visualization.arrayToDataTable([
            ['Status', 'Count'],
            ['Paid',     {{ $paystatus }}],
            ['Unpaid/Pending',      {{ $unpaidCount }}]
        ]);

        var options = {
            title: 'Payment Status',
             pieHole: 0.4,
            slices: {
                0: { color: '#1cc88a' }, // Paid
                1: { color: '#e74a3b' }  // Unpaid
            },
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.PieChart(document.getElementById('pie_chart_div'));
        chart.draw(data, options);
    }
    
    window.onresize = function() {
        drawCharts();
    };
</script>
@endsection