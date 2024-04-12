<!DOCTYPE html>
<html lang="en">

@include('Sellers.InStoreLayouts.InStoreHead')

<body id="page-top">


  <div id="wrapper">


    @include('Sellers.InStoreLayouts.InStoreSideBar')



    <div id="content-wrapper" class="d-flex flex-column">


      <div id="content">


        @include('Sellers.InStoreLayouts.InStoreHeader')



        @yield('main-content')


      </div>

      @include('Sellers.InStoreLayouts.footer')

</body>

</html>
