<html>

<head>
    <title>Exam Result</title>
    <style>
        /* Table styles */
        #exam-schedule th,
        #exam-schedule td {
            border: 1px solid black;
        }

        #exam-schedule {
            width: 100%;
            border-collapse: collapse;
        }

        /* Watermark styles */
        .watermark {
            position: relative;
        }

        .watermark::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset(' wit/img/output-onlinepngtools.png') }}');
            background-size: 200px;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.1;
            z-index: -1;
        }

        /* Print styles */
        @media print {
            .printbtn {
                display: none;
            }

            .watermark::before {
                background-image: url('{{ asset(' wit/img/output-onlinepngtools.png') }}');
                background-size: 200px;
                background-position: center;
                opacity: 0.1;
            }

            #header img {
                width: 90%;
                height: 100%;
                object-fit: contain;
                margin-left: 20px;
            }
        }
    </style>
</head>

<body class="watermark">
    <div style="width:100%; border:1px solid black">
        <div id="header" style="height: 75px;border-bottom:1px solid grey;">
            <img src="{{ asset('wit/img/Dr.png') }}" alt=""
                style="width: 90%; height: 100%; object-fit: contain; margin-left:20px;">
        </div>

        <div style='width:100%; font-size:25px; color:black; text-align:center; border-bottom:2px; border-top:2px;'>
            Student Result</div>
        <hr style="border-top:5px; background-color:black; height:2px;">

        <div id="profile-detail" style="height: 300px; display:flex">
            <div id="left" style="width:75%; float: left;">
                <div style="height:50px;text-align:center; margin-top:10px"></div>
                <div id="detail">
                    <table style="width:100%;border:0px;text-align:left;padding-left:10px;font-size:14px;">
                        <tbody>
                            <tr>
                                <th>Course Roll No</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->university_roll_no}}</td>
                                <th>Registration No</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->registration_no}}</td>
                            </tr>
                            <tr>
                                <th>Name of candidate</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->student_name}}</td>
                                <th>Gender</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->gender}}</td>
                            </tr>
                            <tr>
                                <th>Father's Name</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->fname}}</td>
                                <th>Mother's Name</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->mname}}</td>
                            </tr>
                            <tr>
                                <th>Institute </th>
                                <td> <span style="margin-right:10px;">:</span> Dr. APJ Abdul Kalam WIT </td>
                                <th>Course</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->course->name}}</td>
                            </tr>
                            <tr>
                                <th>Branch</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->branch->name}}</td>
                                <th>Semester</th>
                                <td> <span style="margin-right:10px;">:</span> {{ $student->semester->semester_name}}
                                </td>
                            </tr>
                            <tr>
                                <th>Examination Center</th>
                                <td> <span style="margin-right:10px;">:</span> B.Ed. Regular, Moti Mahal, LNMU,
                                    Darbhanga</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="right" style="width:25%; float: right;align-content:center;">

                <div id="photo" style="width: 120px; height:160px;border:1px solid black;margin-top:20px;margin:auto;">
                    <img src="{{ asset('storage/'.$student->profile_pic->media) }}" alt="photo"
                        style="width: 120px; height:160px">
                </div>
                <br />
                <div id="signature"
                    style="width: 150px; height:50px;border:1px solid black;margin-top:10px;margin:auto;">
                    <img src="{{ asset('storage/'.$student->sign->media) }}" alt="signature"
                        style="width: 150px; height:50px">
                </div>
            </div>
        </div>

        <table id='exam-schedule' style="width: 100%; margin-top: 20px">
            <thead>
                <tr>
                    <th>Subject Code </th>
                    <th>Subject Name </th>
                    <th>Internal</th>
                    <th>External</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subjects as $subject )
                <tr>
                    <td>{{ $subject->subject_code }}</td>
                    <td>{{ $subject->title }}</td>
                    <td>{{ $subject->internal_marks ?? 0 }}</td>
                    <td>{{ $subject->external_marks ?? 0 }}</td>
                    <td>{{ $subject->total_marks ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
            <div id="left" style="width:75%; float: left;">
                <div style="height:50px;text-align:center; margin-top:10px"></div>
                <div id="detail">
                    <table
                        style="width:100%;border:0px;text-align:left;padding-left:10px;font-size:14px; margin-top:20px; margin-bottom:20px">
                        <tbody>
                            <tr>
                                <th>Session</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->admission_session->session_name }}</td>
                                <th>Semester</th>
                                <td> <span style="margin-right:10px;">:</span> {{ $student->semester->semester_name }}</td>
                            </tr>
                            <tr>
                                <th>Even/Odd</th>
                                <td> <span style="margin-right:10px;">:</span>
                                    @php
                                    $lastDigit = substr($student->semester->semester_name, -1); // Get the last digit of the semester name
                                    echo (is_numeric($lastDigit) && $lastDigit % 2 == 0) ? 'Even' : 'Odd'; // Check if it's even or odd
                                    @endphp
                                </td>
                                <th>Total Subjects</th>
                                <td> <span style="margin-right:10px;">:</span> {{ $subjects->count() }}</td>
                            </tr>
                            <tr></tr>
                            <th>Total Mark Obt.</th>
                            <td> <span style="margin-right:10px;">:</span> 0</td>
                            <th>Total Mark </th>
                            <td> <span style="margin-right:10px;">:</span> 0</td>
                            </tr>
                            <tr>
                                <th>Result Status</th>
                                <td> <span style="margin-right:10px;">:</span> PASS </td>
                                {{--  <th>SGPA</th>
                                <td> <span style="margin-right:10px;">:</span> 0</td>  --}}
                            </tr>
                            <tr>
                                <th>Date of Declaration</th>
                                <td> <span style="margin-right:10px;">:</span> Declared</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </table>


    </div>

    <div class='printbtn' style="width:100%; margin-top:20px;text-center;">
        <button onclick="window.print()"
            style="margin:0px auto;padding:10px;width:150px;background-color:blue;color:white">Print</button>
    </div>
</body>

</html>
