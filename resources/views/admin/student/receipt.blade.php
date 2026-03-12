<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Receipt</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .page-break {
                page-break-after: always;
            }

            .cut-line {
                border-top: 2px dashed #000;
                margin: 20px 0;
            }

            .container {
                max-width: 100% !important;
                width: 100% !important;
            }
        }

        .receipt-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 10px;
            font-size: 11px;
            /* Reduced font size for compact fitting */
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
            pointer-events: none;
        }

        .header-title {
            font-size: 20px;
            font-weight: bold;
        }

        .header-sub {
            font-size: 14px;
        }

        h1,
        h4 {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        @for ($i = 0; $i < 2; $i++)
            <div class="receipt-box position-relative">
                <div class="watermark">{{ $i == 0 ? 'LNMU Darbhanga' : 'LNMU Darbhanga' }}</div>
                <div class="text-end fw-bold mb-1">{{ $i == 0 ? 'STUDENT COPY' : 'OFFICE COPY' }}</div>

                <div class="row border-bottom pb-2">
                    <div class="col-2">
                        <img src="/wit/img/university_logo.jpg" style="height:80px;width:80px" />
                    </div>
                    <div class="col-10 text-center">
                        <div class="header-title">LALIT NARAYAN MITHILA UNIVERSITY</div>
                        <div class="header-sub">Kameshwarnagar, Darbhanga</div>
                    </div>
                </div>

                <div class="row border-bottom py-1">
                    <div class="col-3">
                        <b>Registration no : {{ $student->registration_no ?? 'N/A' }}</b>
                    </div>
                    <div class="col-3">
                        <b>Registration Year : {{ \Carbon\Carbon::parse($student->admission_session->from)->year ?? 'N/A' }}</b>
                    </div>
                     <div class="col-3">
                        <b>Session : {{ $student->admission_session->session_name ?? 'N/A' }}</b>
                    </div>
                    <div class="col-3">
                        <b>Course : {{ $student->course->name ?? 'N/A' }}</b>
                    </div>
                </div>

                <div class="row border-bottom py-1">
                    <div class="col-3">
                        <b>College Code & Name</b><br /><br/>
                        <b>Name of Student</b><br />
                        <b>Father's Name</b><br />
                        <b>University Roll No.</b><br />
                        <b>Gender</b>
                    </div>
                    <div class="col-6">
                        <b>: Dr. APJ Abdul Kalam Women’s Institute of Technology, Darbhanga</b><br />
                        <b>: {{ $student->student_name }}</b><br />
                        <b>: {{ $student->fname }}</b><br />
                        <b>: {{ $student->university_roll_no ?? 'N/A' }}</b><br />
                        <b>: {{ $student->gender }}</b><br />
                    </div>
                    <div class="col-3 photo text-center border-start">
                        @if($student->profile_pic)
                            <img src="{{ asset('storage/' . $student->profile_pic->media) }}"
                                style="height: 100px; width: 80px; object-fit: cover;" alt="Student Photo">
                        @else
                            <div style="height:100px; line-height:100px;">No Photo</div>
                        @endif
                    </div>
                </div>

                <div class="row border-bottom py-2" style="min-height: 50px;">
                    <div class="col-9 align-self-center">
                        <b>Subject : </b> {{ $student->course->name ?? 'N/A' }} in {{ $student->branch->name ?? 'N/A' }}
                    </div>
                    <div class="col-3 sign text-center border-start align-self-center">
                        @if($student->sign)
                            <img src="{{ asset('storage/' . $student->sign->media) }}"
                                style="height: 40px; width: auto; max-width: 100%; object-fit: contain;" alt="Signature">
                        @endif
                        <br>
                        <span style="font-size: 10px;">(Student Signature)</span>
                    </div>
                </div>

                <div class="row py-3">
                    <div class="col-6 text-center">D.A.</div>
                    <div class="col-6 text-center">Controller of Examinations</div>
                </div>

                <div class="row border-top pt-1" style="font-size: 10px;">
                    <div class="col-12">
                        <b>Important Directions: </b> Please Keep this slip safely for future reference.<br/> Please ensure all
                        the
                        information printed are correct.<br/> Please Contact your college within fifteen days for any correction.
                    </div>
                </div>
            </div>

            @if($i == 0)
                <div class="cut-line"></div>
            @endif

        @endfor

    </div>

    <script>
        // Automatically print on load if desired, distinct from preview
        // window.print();
    </script>
</body>

</html>
