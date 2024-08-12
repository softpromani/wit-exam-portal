<!DOCTYPE html>
<html lang="en">

<head>

   @include('admin.auth.includes.head');
   @yield('head-section')

</head>

<body class="bg-gradient-primary">

   @yield('content');

    <!-- Bootstrap core JavaScript-->
   @include('admin.auth.includes.foot');
   @yield('footer-section')
</body>

</html>