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
                                <img src="{{asset('wit/img/witlogo.png')}}" style="max-height:200px" class="img-responsive"/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Welcome To Result Portal!</h1>
                                </div>
                                <form action="{{route ('fetch-result') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-user"
                                            id="exampleInputResult" name="resultrollno" aria-describedby="resultRollno"
                                            placeholder="Enter User Roll No">
                                            @error('result')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                    </div>
                                    {{--  <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" name="remember_me"  id="customCheck">
                                            <label class="custom-control-label" for="customCheck">Remember
                                                Me</label>
                                        </div>
                                    </div>  --}}
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </form>
                                <hr>
                                {{-- <div class="text-center">
                                    <a class="small" href="forgot-password.html">Forgot Password?</a>
                                </div>
                                <div class="text-center">
                                    <a class="small" href="register.html">Create an Account!</a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

