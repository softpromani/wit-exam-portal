@extends('student.includes.master')
@section('style_area')
@endsection
@section('content')
    <div class="card">
        <div class="card-body">

            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Sr No</th>
                        <th>Exam Session</th>
                        <th>Payment Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($examSessions as $examSession)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $examSession->session_name }}</td>
                            <td>
                                @if($examSession->payment_status == 'done')
                                    <span class="badge badge-success">Done</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($examSession->form_status == false)
                                    <a href="{{ route('student.semester.exam-for-apply', $examSession->id) }}"
                                        class="btn btn-sm btn-success"> Apply </a>
                                @else
                                    <button type="button" class="btn btn-sm btn-primary viewSubjectBtn" data-toggle="modal"
                                        data-target="#exampleModal" data-id="{{ $examSession->id }}">
                                        View Subjects
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info viewPaymentBtn" data-toggle="modal"
                                        data-target="#paymentModal" data-id="{{ $examSession->id }}">
                                        Payment History
                                    </button>
                                    <a href="{{ route('student.semester.exam-for-apply', ['session_id' => $examSession->id, 'edit' => true]) }}"
                                        class="btn btn-sm btn-secondary">Change Subject</a>
                                    @if($examSession->payment_status != 'done')
                                        <a href="{{ route('student.payment.process', ['examFormId' => $examSession->exam_form_id]) }}"
                                            class="btn btn-sm btn-warning">Pay Now</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Subject Select For this Exam</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modal-data">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Payment History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="payment-modal-data">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script_section')
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
crossorigin="anonymous"></script>
<script>

    $(document).on('click', '.viewSubjectBtn', function () {
        var examSessionId = $(this).data('id');
        $('#modal-data').html('<div class="text-center"><div class="spinner-border text-success" role="status"><span class="sr-only">Loading...</span></div></div>');
        $.ajax({
            url: '{{ url('student/semester/locked-subject-by-examsession') }}/'+examSessionId,
            method: 'GET',
            success: function (response) {
                $('#modal-data').html(response);
            },
            error: function () {
                $('#modal-data').html('An error occurred while loading the data.');
            }
        });
    });

    $(document).on('click', '.viewPaymentBtn', function () {
        var examSessionId = $(this).data('id');
        $('#payment-modal-data').html('<div class="text-center"><div class="spinner-border text-success" role="status"><span class="sr-only">Loading...</span></div></div>');
        $.ajax({
            url: '{{ url('student/semester/locked-payment-history') }}/'+examSessionId,
            method: 'GET',
            success: function (response) {
                $('#payment-modal-data').html(response);
            },
            error: function () {
                $('#payment-modal-data').html('An error occurred while loading the data.');
            }
        });
    });

    $(document).on('click', '.recheckStatusBtn', function () {
        var btn = $(this);
        var txnId = btn.data('id');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        
        $.ajax({
            url: '{{ url('student/payment/recheck-status') }}/'+txnId,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if(response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Recheck failed: ' + response.message);
                    btn.prop('disabled', false).html('Recheck');
                }
            },
            error: function (xhr) {
                alert('An error occurred: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Unknown error'));
                btn.prop('disabled', false).html('Recheck');
            }
        });
    });
</script>
@endsection