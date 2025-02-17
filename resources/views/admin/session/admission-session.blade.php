@extends('admin.includes.master')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <form action="{{route('admin.admission-session.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row mb-4">
                    <div class="col-4">
                        <x-input-box name="session_name" label="Admission Session"  placeholder="Enter Session Name" />
                    </div>
                    <div class="col-4">
                        <x-input-box type="date" name="from" required/>
                    </div>

                    <div class="col-4">
                        <x-input-box type="date" name="to" required/>
                    </div>
                    <div class="col-4">
                    <button type="submit" id="submitScheduleForm" class="btn btn-primary">Submit</button>

                    </div>
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
                        <th scope="col">Id</th>
                        <th scope="col">Session Name</th>
                        <th scope="col">From </th>
                        <th scope="col">To </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($admissionsessions as $admissionsession)
                    <tr>
                        <th scope="row">{{ $loop->index+1 }}</th>
                        <td>{{ $admissionsession->id ?? 'N/A' }}</td>
                        <td>{{ $admissionsession->session_name ?? 'N/A' }}</td>
                        <td>{{ $admissionsession->from ?? 'N/A' }}</td>
                        <td>{{ $admissionsession->to ?? 'N/A' }}</td>
                        {{-- <td>
                            <!-- Edit Button with Pencil Icon -->
                            <a href="{{ route('admin.admission-session.edit', $admissionsession->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>

                            <!-- Delete Button with Trash Icon -->
                            <form action="{{ route('admin.admission-session.destroy', $admissionsession->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this admission session?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td> --}}
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection