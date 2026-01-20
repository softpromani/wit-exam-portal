@extends('admin.includes.master')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <form action="{{ route('admin.admin-dashboard') }}" method="GET" class="form-inline">
                <label class="mr-2" for="exam_session_id">Filter by Exam Session:</label>
                <select name="exam_session_id" id="exam_session_id" class="form-control mr-2">
                    <option value="">All Sessions</option>
                    @foreach($examsession as $session)
                        <option value="{{ $session->id }}" {{ request('exam_session_id') == $session->id ? 'selected' : '' }}>
                            {{ $session->session_name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Exam Forms (total)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$examFormCount}}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-solid fa-file-invoice text-gray-300" style="font-size:36px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        <!-- Bar Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Exam Forms per Course</h6>
                </div>
                <div class="card-body">
                    <div id="bar_chart_div" style="width: 100%; height: 400px;"></div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Status</h6>
                </div>
                <div class="card-body">
                    <div id="pie_chart_div" style="width: 100%; height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawCharts);

            function drawCharts() {
                drawBarChart();
                drawPieChart();
            }

            function drawBarChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Course', 'Exam Forms', { role: 'style' }],
                    @foreach($courseNames as $index => $name)
                        ['{{ $name }}', {{ $courseCounts[$index] }}, '#4e73df'],
                    @endforeach
                ]);

                var options = {
                    title: 'Exam Forms Distribution by Course',
                    legend: { position: 'none' },
                    hAxis: { 
                        title: 'Course',
                    },
                    vAxis: {
                        title: 'Number of Forms'
                    },
                    animation: {
                        startup: true,
                        duration: 1000,
                        easing: 'out',
                    },
                };

                var chart = new google.visualization.ColumnChart(document.getElementById('bar_chart_div'));
                chart.draw(data, options);
            }

            function drawPieChart() {
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
                    legend: { position:  'bottom' }
                };

                var chart = new google.visualization.PieChart(document.getElementById('pie_chart_div'));
                chart.draw(data, options);
            }

            // Resize charts on window resize
            window.onresize = function() {
                drawCharts();
            };
    </script>
@endsection