@extends('admin.auth.includes.master')
@section('content')
<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 text-center mt-5  d-lg-block bg-login-image">
                            <div> <h4 class="ml-5"> Welcome to WIT LNMU</h4></div>

                            <div class="mt-4">
                                <img src="{{asset('wit/img/witlogo.png')}}" style="max-height:200px" class="img-responsive" alt="WIT Logo"/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Welcome To Admin Portal!</h1>
                                </div>

                                @if (session('error'))
                                    <div class="alert alert-danger py-2 small" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                @if (session('success'))
                                    <div class="alert alert-success py-2 small" role="alert">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <form action="{{route ('admin.admin-login-store') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-user @error('email') is-invalid @enderror"
                                            id="exampleInputEmail" name="email" aria-describedby="emailHelp"
                                            placeholder="Enter Email Address" value="{{ old('email') }}">
                                        @error('email')
                                            <span class="text-danger small ml-2">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-user @error('password') is-invalid @enderror"
                                            id="exampleInputPassword" name="password" placeholder="Enter Password">
                                        @error('password')
                                            <span class="text-danger small ml-2">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Captcha Section -->
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between mb-2 p-1 border rounded" style="background: #f8f9fc; border-color: #d1d3e2 !important; border-radius: 10rem !important;">
                                            <div class="d-flex align-items-center pl-2">
                                                <img src="{{ route('captcha.image') }}" id="captcha-img" alt="Captcha" style="border-radius: 6px; height: 38px; display: block;">
                                            </div>
                                            <button type="button" class="btn btn-sm btn-link text-primary mr-2" id="refresh-captcha" title="Refresh Captcha" style="text-decoration: none; box-shadow: none;">
                                                <i class="fas fa-sync-alt fa-lg"></i>
                                            </button>
                                        </div>
                                        <input type="text" class="form-control form-control-user @error('captcha') is-invalid @enderror"
                                            id="captcha" name="captcha" placeholder="Enter Captcha Code" autocomplete="off" maxlength="6">
                                        @error('captcha')
                                            <span class="text-danger small ml-2">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" name="remember_me" id="customCheck" {{ old('remember_me') ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="customCheck">Remember Me</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Login
                                    </button>
                                </form>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@section('footer-section')
<script>
    $(document).ready(function() {
        $('#refresh-captcha').on('click', function(e) {
            e.preventDefault();
            var $icon = $(this).find('i');
            $icon.addClass('fa-spin');

            var newSrc = "{{ route('captcha.image') }}?t=" + new Date().getTime();
            var img = new Image();
            img.onload = function() {
                $('#captcha-img').attr('src', newSrc);
                $icon.removeClass('fa-spin');
            };
            img.onerror = function() {
                $icon.removeClass('fa-spin');
            };
            img.src = newSrc;

            $('#captcha').val('').focus();
        });
    });
</script>
@endsection
