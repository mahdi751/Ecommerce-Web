<!DOCTYPE html>
<html lang="en">

@include('Sellers.layouts.head')

<body id="page-top">


  <div id="wrapper">


    @include('Sellers.layouts.sidebar')



    <div id="content-wrapper" class="d-flex flex-column">


      <div id="content">


        @include('Sellers.layouts.header')



        @yield('main-content')


      </div>
      
      @include('Sellers.layouts.footer')

</body>

</html>
