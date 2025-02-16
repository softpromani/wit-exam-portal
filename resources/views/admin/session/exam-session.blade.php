@extends('admin.includes.master')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.exam-session.store') }}" method="POST">
                @csrf
                <div class="row d-flex mb-4">
                    <div>
                        <label for="fordate" class="form-label">Exam Session</label>
                        <input type="text" class="form-control" name="session_name" placeholder="Enter Session Name">

                    </div>
                    <div class="col-4">
                        <label for="fordate" class="form-label">From </label>
                        <input type="date" class="form-control" id="date1" name="from">
                    </div>

                    <div class="col-4">
                        <label for="todate" class="form-label">To </label>
                        <input type="date" class="form-control" id="todate" name="to">
                    </div>
                 

                        <button type="submit" id="submitScheduleForm" class="btn btn-primary mt-3">Submit</button>

                </div>
            </form>
        </div>
    </div>

    <div class="card mt-3" >
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">Sr No</th>
                        <th scope="col">Session Name</th>
                        <th scope="col">From </th>
                        <th scope="col">To </th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($examsessions as $examsession)
                    <tr>
                        <th scope="row">{{ $loop->index+1 }}</th>
                        <td>{{ $examsession->session_name ?? 'N/A' }}</td>
                        <td>{{ $examsession->from ?? 'N/A' }}</td>
                        <td>{{ $examsession->to ?? 'N/A' }}</td>
                        <td>{{ $examsession->status ?? 'N/A' }}</td>

                      {{-- <td>
                            <!-- Edit Button with Pencil Icon -->
                            <a href="{{ route('admin.admission-session.edit', $examsession->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                        </a>

                        <!-- Delete Button with Trash Icon -->
                        <form action="{{ route('admin.admission-session.destroy', $examsession->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this admission session?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                            </td>  --}}
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection