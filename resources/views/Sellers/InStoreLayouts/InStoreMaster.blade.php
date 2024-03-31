<!DOCTYPE html>
<html lang="en">

@include('Sellers.InStoreLayouts.InStoreHead')

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    @include('Sellers.InStoreLayouts.InStoreSideBar')
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        @include('Sellers.InStoreLayouts.InStoreHeader')
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        @yield('main-content')
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->
      @include('Sellers.InStoreLayouts.footer')

</body>

</html>
