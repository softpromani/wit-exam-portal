@extends('admin.includes.master')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <form
                    action="{{ isset($editexamsession) ? route('admin.exam-session.update', $editexamsession->id) : route('admin.exam-session.store') }}"
                    method="POST">
                    @csrf
                    @if(isset($editexamsession))
                        @method('PUT')
                    @endif
                    <div class="row mb-4">
                        <div class="col-4">
                            <x-input-box name="session_name" :value="isset($editexamsession) ? $editexamsession->session_name : ''" label="Exam Session"
                                placeholder="Enter Session Name" />
                        </div>
                        <div class="col-4">
                            <x-input-box type="date" name="from" :value="isset($editexamsession) ? $editexamsession->from : ''" required />
                        </div>

                        <div class="col-4">
                            <x-input-box type="date" name="to" :value="isset($editexamsession) ? $editexamsession->to : ''"
                                required />
                        </div>
                        <div class="col-4">
                            <x-input-box name="exam_center" :value="isset($editexamsession) ? $editexamsession->exam_center : ''" label="Exam Center" placeholder="Enter Exam Center" />
                        </div>
                        <div class="col-4">
                            <button type="submit" id="submitScheduleForm"
                                class="btn btn-primary">{{ isset($editexamsession) ? 'Update' : 'Submit' }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <table class="table table-bordered table-sm table-hover small">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">Sr No</th>
                            <th scope="col">Session Name</th>
                            <th scope="col">From</th>
                            <th scope="col">To</th>
                            <th scope="col">Forms Filled</th>
                            <th scope="col">Status</th>
                            <th scope="col">Fee Configs</th>
                            <th scope="col">Exam Center</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examsessions as $examsession)
                            <tr>
                                <th scope="row">
                                    {{ ($examsessions->currentPage() - 1) * $examsessions->perPage() + $loop->iteration }}</th>
                                <td>{{ $examsession->session_name ?? 'N/A' }}</td>
                                <td>{{ $examsession->from ?? 'N/A' }}</td>
                                <td>{{ $examsession->to ?? 'N/A' }}</td>
                                <td>
                                    <span class="">{{ $examsession->exam_forms_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <x-select-box name="status" :options="['processing' => 'Processing', 'admit-card' => 'Admit Card', 'completed' => 'Completed']" :value="$examsession->status"
                                        data-id="{{ $examsession->id }}" class="form-select status-select" />
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
                                        <i class="fas fa-money-bill-wave me-1"></i>
                                    </a>
                                    <a href="{{ route('admin.exam-session.edit', $examsession->id) }}"
                                        class="btn btn-sm btn-primary btn-round px-3">
                                        <i class="fas fa-edit me-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $examsessions->links() }}
            </div>
        </div>
    </div>

@endsection
@section('script_section')
    <script>     document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function () {
                const status = this.value; const id = this.dataset.id; const url = "{{ route('admin.exam-session.update-status', ':id') }}".replace(':id', id);
                fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ status: status }) }).then(response => response.json()).then(data => { if (data.success) { alert('Status updated!'); } else { alert('Update failed!'); } });
            });
        });
    </script>
@endsection