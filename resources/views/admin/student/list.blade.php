@extends('admin.includes.master')

@section('style_area')
@endsection

@section('content')
<form action="{{route('admin.student.list')}}" method="post">
    @csrf
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-4">
                <label for="branch_id">Branch</label>
                <select class="form-control" name="branch_id" id="branch_id">
                    @foreach($courses as $course)
                        <optgroup label="{{ $course->name }}">
                            @foreach($course->branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="col-4">
                <label for="semester_id">Semester</label>
                <select class="form-control" name="semester_id" id="semester_id">
                    @foreach($semesters as $sem)
                    <option value="{{$sem->id}}">{{$sem->semester_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4">
                <label for="admission_session_id">Admission Session</label>
                <select class="form-control" name="admission_session_id" id="admission_session_id">
                    @foreach($admission_sessions as $ads)
                    <option value="{{$ads->id}}">{{$ads->session_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 mt-3">
                <button class="btn btn-primary" type="submit">Fetch Student</button>
            </div>
        </div>
    </div>
</div>
</form>
@isset($students)
    <div class="card mt-3">
        <div class="card-body">
            <form id="select-students-form">
                @csrf
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th><input type="checkbox" id="select-all"> All</th>
                        <th>Sr No</th>
                        <th>Registration No</th>
                        <th>University Roll No</th>
                        <th>Name</th>
                        <th>Branch</th>
                        <th>Curr. Semester</th>
                        <th>Adm. Semester</th>
                        <th>Admission Session</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $st)
                    <tr>
                        <td><input type="checkbox" class="select-item" name="selected_students[]" value="{{$st->id}}"></td>
                        <td>{{$loop->index+1}}</td>
                        <td>{{$st->registration_no}}</td>
                        <td>
                            <input type="text" value="{{ $st->university_roll_no }}" data-id="{{ $st->id }}" class="form-control roll-no-class"/>
                            <small class="text-danger error-message"></small>
                        </td>
                        <td>{{$st->student_name}}</td>
                        <td>{{$st->branch?->name}}</td>
                        <td>{{$st->semester?->semester_name}}</td>
                        <td>{{$st->admission_semester?->semester_name}}</td>
                        <td>{{$st->admission_session?->session_name}}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3"><button class="btn btn-danger" type="button" id="promote-btn">Promote Next Sem</button></th>
                    </tr>
                </tfoot>
            </table>
            </form>
        </div>
    </div>
@endisset

<style>
    .loading {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" style="margin:auto; background:none; display:block;" width="20px" height="20px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="50" cy="50" fill="none" stroke="%2300f" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform></circle></svg>');
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px 20px;
}
</style>
@endsection

@section('script_section')
<script>
    document.querySelectorAll('.roll-no-class').forEach(function (input) {
    input.addEventListener('blur', function () {
        // `this` refers to the input that triggered blur
        const value = this.value;
        const studentId = this.dataset.id;
        const errorEl = this.nextElementSibling;

            // Add loading state
            this.classList.add('loading');

            // Clear previous status
            this.classList.remove('border-success', 'border-danger');
            if (errorEl) errorEl.innerText = '';
            // Send AJAX request
            fetch('/admin/student/update-roll-no', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept':'application/json'
                },
                body: JSON.stringify({
                    id: studentId,
                    university_roll_no: value
                })
            })
            .then(async response => {
            this.classList.remove('loading');
            if (response.ok) {
                this.classList.add('border-success');
            } else if (response.status === 422) {
                const data = await response.json();
                this.classList.add('border-danger');
                if (data.errors && data.errors.university_roll_no && errorEl) {
                    errorEl.innerText = data.errors.university_roll_no[0];
                }
            } else {
                this.classList.add('border-danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.classList.remove('loading');
            this.classList.add('border-danger');
            if (errorEl) errorEl.innerText = 'Something went wrong.';
        });
    });
});
</script>

<script>


    document.getElementById('select-all').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.select-item');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    document.getElementById('promote-btn').addEventListener('click', function() {
        let selectedStudents = document.querySelectorAll('.select-item:checked');

        if (selectedStudents.length === 0) {
            alert("Please select at least one student.");
            return;
        }

        let form = document.getElementById('select-students-form');
        form.action = "{{ url('admin/student-promote') }}"; // Set form action URL
        form.method = "POST"; // Ensure method is POST
        form.submit(); // Submit form
    });

</script>
@endsection

