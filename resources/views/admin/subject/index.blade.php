@extends('admin.includes.master')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Subjects Management</h1>
            <p class="text-muted small mb-0">Manage course subjects, subject codes, branches, and semesters</p>
        </div>
        <div>
            <a href="{{ route('admin.subject.import-view') }}" class="btn btn-success btn-sm shadow-sm font-weight-bold mr-2">
                <i class="fas fa-file-csv mr-1"></i> Import CSV / Excel
            </a>
            <a href="{{ route('admin.subject.create') }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-plus fa-sm text-white mr-1"></i> Add New Subject
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-1"></i> Filter & Search Subjects
            </h6>
            <span class="badge badge-primary px-3 py-2">
                Showing {{ $filteredCount }} of {{ $totalSubjects }} Subjects
            </span>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.subject.index') }}" method="GET" id="subjectFilterForm">
                <div class="row align-items-end">
                    <!-- Search by Code / Title -->
                    <div class="col-md-3 mb-3">
                        <label for="search" class="small font-weight-bold text-gray-700">Keyword Search</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" name="search" id="search"
                                   placeholder="Subject code or title..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="course_id" class="small font-weight-bold text-gray-700">Course</label>
                        <select name="course_id" id="course_id" class="form-control form-control-sm">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Branch Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="branch_id" class="small font-weight-bold text-gray-700">Branch</label>
                        <select name="branch_id" id="branch_id" class="form-control form-control-sm">
                            <option value="">All Branches</option>
                            @foreach($courses as $course)
                                <optgroup label="{{ $course->name }}">
                                    @foreach($course->branches as $branch)
                                        <option value="{{ $branch->id }}" data-course="{{ $branch->course_id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Semester Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="semester_id" class="small font-weight-bold text-gray-700">Semester</label>
                        <select name="semester_id" id="semester_id" class="form-control form-control-sm">
                            <option value="">All Semesters</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                    {{ $semester->semester_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div class="col-md-1 mb-3">
                        <label for="type" class="small font-weight-bold text-gray-700">Type</label>
                        <select name="type" id="type" class="form-control form-control-sm">
                            <option value="">All</option>
                            <option value="t" {{ request('type') == 't' ? 'selected' : '' }}>Theory</option>
                            <option value="p" {{ request('type') == 'p' ? 'selected' : '' }}>Practical</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="col-md-2 mb-3 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.subject.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Subjects Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-book mr-1 text-primary"></i> Subject List
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped mb-0 text-gray-900">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">#</th>
                            <th style="width: 130px;">Subject Code</th>
                            <th>Subject Title</th>
                            <th style="width: 110px;">Course</th>
                            <th style="width: 140px;">Branch</th>
                            <th style="width: 100px;">Semester</th>
                            <th style="width: 100px;" class="text-center">Type</th>
                            <th style="width: 110px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                            <tr>
                                <td class="text-center font-weight-bold align-middle">
                                    {{ ($subjects->currentPage() - 1) * $subjects->perPage() + $loop->iteration }}
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-light border text-dark font-weight-bold px-2 py-1" style="font-size: 0.85rem;">
                                        {{ $subject->subject_code }}
                                    </span>
                                </td>
                                <td class="align-middle font-weight-bold">
                                    {{ $subject->title }}
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-info px-2 py-1">
                                        {{ optional($subject->course)->name ?? 'Course ' . $subject->course_id }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    {{ optional($subject->branch)->name ?? 'Branch ' . $subject->branch_id }}
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-secondary px-2 py-1">
                                        {{ optional($subject->semester)->semester_name ?? 'Sem ' . $subject->semester_id }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    @php
                                        $t = strtolower(trim((string)$subject->type));
                                    @endphp
                                    @if($t === 't' || $t === 'theory')
                                        <span class="badge badge-primary px-2 py-1">Theory</span>
                                    @elseif($t === 'p' || $t === 'practical')
                                        <span class="badge badge-warning text-dark px-2 py-1">Practical</span>
                                    @elseif(!empty($subject->type))
                                        <span class="badge badge-info px-2 py-1">{{ ucfirst($subject->type) }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <a href="{{ route('admin.subject.edit', $subject->id) }}"
                                       class="btn btn-sm btn-primary rounded-circle"
                                       title="Edit Subject" style="width: 32px; height: 32px; padding: 4px 0;">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.subject.destroy', $subject->id) }}"
                                          method="POST"
                                          style="display: inline-block;"
                                          onsubmit="return confirm('Are you sure you want to delete this subject: {{ $subject->subject_code }} - {{ addslashes($subject->title) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger rounded-circle ml-1"
                                                title="Delete Subject" style="width: 32px; height: 32px; padding: 4px 0;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block text-gray-400"></i>
                                    <h5>No Subjects Found</h5>
                                    <p class="small mb-3">Try adjusting your filters or add a new subject.</p>
                                    <a href="{{ route('admin.subject.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus mr-1"></i> Add Subject
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($subjects->hasPages())
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
                <div class="small text-muted">
                    Showing {{ $subjects->firstItem() ?? 0 }} to {{ $subjects->lastItem() ?? 0 }} of {{ $subjects->total() }} entries
                </div>
                <div>
                    {{ $subjects->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
