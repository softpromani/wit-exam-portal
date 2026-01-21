@extends('admin.includes.master')
@section('content')
<div class="container">
    {{-- Filter Section --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Filter Exam Schedule</h5>
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addScheduleModal">
                <i class="fas fa-plus"></i> Add New Schedule
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.exam_schedule_list') }}" method="GET" class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Filter by Exam Session</label>
                    <select class="form-control" name="filter_session">
                        <option value="">All Sessions</option>
                        @foreach ($examsessions as $session)
                            <option value="{{ $session->id }}" {{ request('filter_session') == $session->id ? 'selected' : '' }}>
                                {{ $session->session_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.exam_schedule_list') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Schedule List --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Exam Schedule List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr No</th>
                            <th>Exam Session</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>L-T-P</th>
                            <th>Credits</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($examschedule as $key => $schedule)
                        <tr>
                            <td>{{ $examschedule->firstItem() + $key }}</td>
                            <td>{{ $schedule->exam_session->session_name ?? 'N/A' }}</td>
                            <td>{{ $schedule->subject->title ?? 'N/A' }} ({{ $schedule->subject->subject_code ?? '' }})</td>
                            <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d-M-Y') }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($schedule->from_time)->format('h:i A') }} - 
                                {{ \Carbon\Carbon::parse($schedule->to_time)->format('h:i A') }}
                            </td>
                            <td>{{ $schedule->ltp ?? '-' }}</td>
                            <td>{{ $schedule->credits ?? '-' }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit_exam_schedule" data-id="{{ $schedule->id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No Exam Schedules Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $examschedule->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Add Exam Schedule Modal --}}
<div class="modal fade" id="addScheduleModal" tabindex="-1" role="dialog" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addScheduleModalLabel">Add Exam Schedule</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addScheduleForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Exam Session</label>
                            <select class="form-control" name="exam_session" required>
                                <option value="">Select Session</option>
                                @foreach ($examsessions as $session)
                                <option value="{{ $session->id }}">{{ $session->session_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subject</label>
                            <select class="form-control" name="subject" required>
                                <option value="">Select Subject</option>
                                @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{$subject->subject_code}} / {{ $subject->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">From Time</label>
                            <input type="time" class="form-control" name="from_time" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">To Time</label>
                            <input type="time" class="form-control" name="to_time" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="submitScheduleForm" class="btn btn-primary">Save Schedule</button>
            </div>
        </div>
    </div>
</div>

{{--  exam schedule edit modal  --}}
<!-- Modal -->
<div class="modal fade" id="examScheduleEdit" tabindex="-1" role="dialog" aria-labelledby="examScheduleEditLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form method="post" action="{{ route('admin.exam_schedule_update') }}">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title" id="examScheduleEditLabel">Update Exam Schedule </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" id="examScheduleEditBody">
           498g-=
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
    </form>
    </div>
  </div>
</div>
@endsection
@section('script_section')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function(){

        $('#submitScheduleForm').click(function(e){
            e.preventDefault();
            var formData = $('#addScheduleForm').serialize();
            
            $.ajax({
                url: '{{ route("admin.exam_schedule_store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                  if(response.status==2)
                  {
                    Swal.fire({
                        title: 'Error!',
                        text: 'This schedule already exist!',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                      });
                  }
                  else if(response.status == 1 )
                  {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Schedule saved successfully!',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                      }).then((result) => {
                          if (result.isConfirmed) {
                              location.reload();
                          }
                      });
                  }
                  else {
                      Swal.fire({
                        title: 'Error!',
                        text: 'Could not save schedule.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                      });
                  }
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred: " + error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                      });
                }
            });
        });
    });

    $(document).on('click','.edit_exam_schedule',function(){
        var schedule_id = $(this).data('id');
        $.ajax({
            url:"{{ url('admin/exam-schedule-edit') }}/"+schedule_id,
            method:'GET',
            success:function(response){
                $('#examScheduleEditBody').html(response);
                const myModal = new bootstrap.Modal(document.getElementById('examScheduleEdit'));
                myModal.show(); 
            }
        });
    });
</script>
@endsection
