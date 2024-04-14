<!DOCTYPE html>
<html lang="en">

@include('admin.layouts.head')

<body id="page-top">


  <div id="wrapper">


    @include('admin.layouts.sidebar')



    <div id="content-wrapper" class="d-flex flex-column">


      <div id="content">


        @include('admin.layouts.header')



        @yield('main-content')


      </div>

      @include('admin.layouts.footer')

</body>

</html>
