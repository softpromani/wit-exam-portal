<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('student.dashboard')}}">
        <div class="sidebar-brand-icon">
            <img src="{{asset('wit/img/witlogo.png')}}" alt="" style="height: 50px; width:50px;">
        </div>
        <div class="sidebar-brand-text">Student Panel</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="{{route('student.dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
       Panel
    </div>

    <!-- Nav Item - Pages Collapse Menu -->

 
    <li class="nav-item">
        <a class="nav-link" href="{{route('student.profile')}}">
            <i class="fas fa-user"></i>
            <span>Student Profile</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
       Semester Section
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-building"></i>
            <span>Exams</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Exam form & Admit card</h6>
                <a class="collapse-item" href="{{route('student.semester.exam-form')}}">Exam Form</a>
                <a class="collapse-item" href="#">Admit Card</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    {{--  <hr class="sidebar-divider">  --}}

    <!-- Heading -->
    {{--  <div class="sidebar-heading">
        Addons
    </div>  --}}



    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
