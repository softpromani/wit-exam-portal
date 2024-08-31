<html>

<head>
    <title>Admit Card</title>
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
            background-image: url('{{ asset('wit/img/output-onlinepngtools.png') }}');
            background-size: 200px;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.1;
            z-index: -1;
        }

        /* Print styles */
        @media print {
            .printbtn{
                display: none;
            }
            .watermark::before {
                background-image: url('{{ asset('wit/img/output-onlinepngtools.png') }}');
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

<body class="watermark" >
    <div style='width:100%; border:1px solid black;text-align:center;border-bottom:0px;'>Admit Card</div>
    <div style="width:100%; border:1px solid black">
        <div id="header" style="height: 75px;border-bottom:1px solid grey;" >
            <img src="{{ asset('wit/img/Dr.png') }}" alt="" style="width: 90%; height: 100%; object-fit: contain; margin-left:20px;">
        </div>
        <div id="profile-detail" style="height: 300px; display:flex">
            <div id="left" style="width:75%; float: left;">
                <div style="height:50px;text-align:center; margin-top:10px"></div>
                <div id="detail">
                    <table style="width:100%;border:0px;text-align:left;padding-left:10px;font-size:14px;">
                        <tbody>
                            <tr>
                                <th>Course Roll No</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->university_roll_no}}</td>
                            </tr>
                             <tr>
                                <th>Registration No</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->registration_no}}</td>
                            </tr>
                            <tr>
                                <th>Name of candidate</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->student_name}}</td>
                            </tr>
                            <tr>
                                <th>Gender</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->gender}}</td>
                            </tr>
                            <tr>
                                <th>Father's Name</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->fname}}</td>
                            </tr>
                             <tr>
                                <th>Mother's Name</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->mname}}</td>
                            </tr>
                            <tr>
                                <th>Institute </th>
                                <td> <span style="margin-right:10px;">:</span> Dr. APJ Abdul Kalam WIT </td>
                            </tr>
                            <tr>
                                <th>Course</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->course->name}}</td>
                            </tr>
                            <tr>
                                <th>Branch</th>
                                <td> <span style="margin-right:10px;">:</span> {{$student->branch->name}}</td>
                            </tr>
                            <tr>
                                <th>Semester</th>
                                <td> <span style="margin-right:10px;">:</span> {{ $student->semester->semester_name}}</td>
                            </tr>
                            <tr>
                                <th>Examination Center</th>
                                <td> <span style="margin-right:10px;">:</span> B.Ed. Regular, Moti Mahal, LNMU, Darbhanga</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="right" style="width:25%; float: right;align-content:center;">

                <div id="photo" style="width: 120px; height:160px;border:1px solid black;margin-top:20px;margin:auto;">
                    <img src="{{ asset('storage/'.$student->profile_pic->media) }}" alt="photo" style="width: 120px; height:160px">
                </div>
                <br/>
                <div id="signature" style="width: 150px; height:50px;border:1px solid black;margin-top:10px;margin:auto;">
                    <img src="{{ asset('storage/'.$student->sign->media) }}" alt="signature" style="width: 150px; height:50px">
                </div>
            </div>
        </div>
        <table id='exam-schedule' style="width: 100%;">
            <thead>
                <tr>
                    <th>Subject Code </th>
                    <th>Subject Name </th>
                    <th>Exam Date</th>
                    <th>Timings</th>
                    <th>Answer Book No.</th>
                    <th>Invigilator Sign</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subjects as $subject )
                <tr>
                    <td>{{ $subject->subject_code }}</td>
                    <td>{{ $subject->title }}</td>
                    <td>{{ $subject->date }}</td>
                    <td>{{ $subject->time }}</td>
                    <td>{{ $subject->answer_book_no }}</td>
                    <td>{{ $subject->invigilator }}</td>
                    <td></td>

                </tr>
                @endforeach
            </tbody>
        </table>
        <hr>
        <h5 style="margin-left:20px;">Important Notes (Read Carefully)</h5>
        <hr>
        <div style="font-size: 10px; padding:5px">

            1: Reporting time at exam centre atleast an hour before the commencement of exam. No entry is allowed after
            the start of the exam.<br>
            2: The student should bring the Admit Card along with a Institute Photo Id/Other valid Id for appearing in
            the examination failing which he/she shall not be allowed to appear in the examination.<br>
            3: You are advised to follow the COVID-19 safety and social distancing guidlines as issued by the government
            from time to time.<br>
            4:If photograph of student is not available in the admit card,paste a recent photograph attested by the
            Director of respective institute.<br>
            5:Please verify the Exam date with latest date sheet available on witlnmu.ac.in website. In case
            of
            any discrepancy the date sheet available on the website shall be FINAL.<br>
            6: Kindly keep checking circulars at witlnmu.ac.in daily for updates in exam schedule.<br>
            7: उक्त प्रवेश पत्र में वर्णित सूचनाओं में कोई त्रुटि / संशोधन होने की स्थिति में कृपया
            itcell@lnmu.ac.in पर ही स्कैन कॉपी भेजना सुनिश्चित करें <br>
            8:
            आप से अपेक्षा है कि आप किसी भी सूरत में उत्तर पुस्तिका के अंदर के पृष्ठों पर अपना रोल नंबर या फोन नंबर अथवा
            कोई अन्य
            पहचान अंकित नहीं करें। साथ ही किसी भी प्रकार से नकल (व्यक्तिगत या सामूहिक) में संलिप्त न हों। Digital
            Evaluation से | ऐसी कॉपियों को पकड़ा जा सकता है एवम विश्वविद्यालय के ऑर्डिनेंस के नियमानुसार दंड दिया जाएगा
            जिसकी समस्त जिम्मेवारी आप की होगी।
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; padding-left: 30px; padding-right: 30px;">
            <div style="text-align: left;">
                <p style="margin: 0;">D.A</p>
            </div>
            <div style="text-align: center;">
                <p style="margin: 0;">Director</p>
            </div>
            <div style="text-align: right;">
                <img src="{{ asset('wit/img/exam-controller-sign.png') }}" alt="signature image" style="width: 150px; height:50px; margin-top:5px">
                <p style="margin: 0;">Controller of Examination</p>
            </div>
        </div>

        </div>

        </div>


    </div>

    <div class='printbtn' style="width:100%; margin-top:20px;text-center;">
        <button onclick="window.print()" style="margin:0px auto;padding:10px;width:150px;background-color:blue;color:white">Print</button>
    </div>
</body>

</html>
