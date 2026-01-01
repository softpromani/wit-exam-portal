@extends('admin.includes.master')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.exam-session.store') }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-4">
                            <x-input-box name="session_name" label="Exam Session" placeholder="Enter Session Name" />
                        </div>
                        <div class="col-4">
                            <x-input-box type="date" name="from" required />
                        </div>

                        <div class="col-4">
                            <x-input-box type="date" name="to" required />
                        </div>
                        <div class="col-4">
                            <x-input-box name="exam_center" label="Exam Center" placeholder="Enter Exam Center" />
                        </div>
                        <div class="col-4">
                            <button type="submit" id="submitScheduleForm" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">Sr No</th>
                            <th scope="col">Session Name</th>
                            <th scope="col">From</th>
                            <th scope="col">To</th>
                            <th scope="col">Status</th>
                            <th scope="col">Fee Configs</th>
                            <th scope="col">Exam Center</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examsessions as $examsession)
                                        <tr>
                                            <th scope="row">{{ $loop->index + 1 }}</th>
                                            <td>{{ $examsession->session_name ?? 'N/A' }}</td>
                                            <td>{{ $examsession->from ?? 'N/A' }}</td>
                                            <td>{{ $examsession->to ?? 'N/A' }}</td>
                                            <td>
                                                <x-select-box name="status" :options="['processing' => 'Processing', 'admit-card' => 'Admit Card', 'completed' => 'Completed']" :value="$examsession->status" :attributes="[
                                'data-id' => $examsession->id,
                                'class' => 'form-select status-select'
                            ]" />
                                            </td>
                                            <td>
                                                @if($examsession->exam_session_has_cbs->count() > 0)
                                                    <span class="badge bg-success text-white">
                                                        {{ $examsession->exam_session_has_cbs->count() }} Set
                                                    </span>
                                                @else
                                                    <span class="text-muted small">Not set</span>
                                                @endif
                                            </td>
                                            <td>{{ $examsession->exam_center ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('admin.exam-session.set-amount', $examsession->id) }}"
                                                    class="btn btn-sm btn-info btn-round px-3">
                                                    <i class="fas fa-money-bill-wave me-1"></i> Set Amount
                                                </a>
                                            </td>
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
                    if (data.success) {
                        alert('Status updated!');
                    } else {
                        alert('Update failed!');
                    }
                });
        });
    });
</script>