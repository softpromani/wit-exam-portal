@extends('admin.includes.master')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.exam-session.store') }}" method="POST">
                @csrf
                <div class="row mb-4">
                    <div class="col-4">
                        <x-input-box name="session_name" label="Exam Session"  placeholder="Enter Session Name" />
                    </div>
                    <div class="col-4">
                        <x-input-box type="date" name="from" required/>
                    </div>

                    <div class="col-4">
                        <x-input-box type="date" name="to" required/>
                    </div>
                    <div class="col-4">
                        <x-input-box name="exam_center" label="Exam Center"  placeholder="Enter Exam Center" />
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
                        <th scope="col">Session Name</th>
                        <th scope="col">From </th>
                        <th scope="col">To </th>
                        <th scope="col">Status</th>
                        <th scope="col">Exam Center </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($examsessions as $examsession)
                    <tr>
                        <th scope="row">{{ $loop->index+1 }}</th>
                        <td>{{ $examsession->session_name ?? 'N/A' }}</td>
                        <td>{{ $examsession->from ?? 'N/A' }}</td>
                        <td>{{ $examsession->to ?? 'N/A' }}</td>
                        <td>
                            <x-select-box
                                name="status"
                                :options="['processing' => 'Processing', 'admit-card' => 'Admit Card', 'completed' => 'Completed']"
                                :value="$examsession->status"
                                :attributes="[
                                    'data-id' => $examsession->id,
                                    'class' => 'form-select status-select'
                                ]"
                            />

                        </td>


                        <td>{{ $examsession->exam_center ?? 'N/A' }}</td>

                      {{-- <td>
                            <!-- Edit Button with Pencil Icon -->
                            <a href="{{ route('admin.exam-session.edit', $examsession->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                        </a>

                        <!-- Delete Button with Trash Icon -->
                        <form action="{{ route('admin.exam-session.destroy', $examsession->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this exam session?')">
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
<script>
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function () {
            const status = this.value;
            const id = this.dataset.id;

            fetch(`/update-exam-status/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success){
                    alert('Status updated!');
                } else {
                    alert('Update failed!');
                }
            });
        });
    });
</script>
