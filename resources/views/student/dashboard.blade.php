@extends('student.includes.master')

@section('content')
<div class="row">
    @if(auth()->guard('student')->check() && auth()->guard('student')->user()->is_profile == 0)
    <div class="col-12 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Action Required - Profile Unlocked
                        </div>
                        <div class="h6 mb-1 font-weight-bold text-gray-800">
                            Your profile has been unlocked by administrator for editing.
                        </div>
                        <p class="mb-0 text-muted small">Please verify and update your information, then save to complete your profile.</p>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('student.profile') }}" class="btn btn-warning font-weight-bold shadow-sm">
                            <i class="fas fa-edit mr-1"></i> Edit Profile Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


