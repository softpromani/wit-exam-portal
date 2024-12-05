@extends('admin.includes.master')

@section('content')
<div class="row">
    <div class="col-xl-3 col-6-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                           Exam Forms (total)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$examFormCount}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa-solid fa-file-invoice text-gray-300" style="font-size:36px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-6-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                          Paid Fee Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$paystatus}}/{{$examFormCount}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-plus-square text-gray-300" style="font-size:26px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-6-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Collected Fee</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$totalFee}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-rupee text-gray-300" style="font-size:36px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


