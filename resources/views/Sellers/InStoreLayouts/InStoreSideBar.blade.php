<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">


  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('seller') }}">
    <div class="sidebar-brand-icon rotate-n-15">
      <i class="fas fa-laugh-wink"></i>
    </div>
    <div class="sidebar-brand-text mx-3">
      Seller
    </div>
  </a>



  <hr class="sidebar-divider my-0">


  <li class="nav-item active">
    <a class="nav-link" href="{{ route('store.show', session('current_store_id')) }}">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span></a>
  </li>


  <hr class="sidebar-divider">



  <div class="sidebar-heading">
    @if (session()->has('current_store_name'))
      {{ session('current_store_name') }} Store
    @else
      Default Shop Name
    @endif
  </div>

  {{-- Categories --}}
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#categoryCollapse"
      aria-expanded="true" aria-controls="categoryCollapse">
      <i class="fas fa-sitemap"></i>
      <span>Categories</span>
    </a>
    <div id="categoryCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Categories Options:</h6>
        <a class="collapse-item" href="{{ route('category.index') }}">Categories</a>
        <a class="collapse-item" href="{{ route('category.create') }}">Add Category</a>
      </div>
    </div>
  </li>

  {{-- Products --}}
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#productCollapse"
      aria-expanded="true" aria-controls="productCollapse">
      <i class="fas fa-cubes"></i>
      <span>Products</span>
    </a>
    <div id="productCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Product Options:</h6>
        <a class="collapse-item" href="{{ route('product.index') }}">Products</a>
        <a class="collapse-item" href="{{ route('product.create') }}">Add Product</a>
      </div>
    </div>
  </li>

  {{-- Events --}}
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#eventCollapse"
      aria-expanded="true" aria-controls="eventCollapse">
      <i class="fas fa-calendar"></i>
      <span>Events</span>
    </a>
    <div id="eventCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Events Options:</h6>
        <a class="collapse-item" href="{{ route('event.index') }}">Events</a>
        <a class="collapse-item" href="{{ route('event.create') }}">Add Event</a>
      </div>
    </div>
  </li>


  {{-- Shipping --}}
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#shippingCollapse"
      aria-expanded="true" aria-controls="shippingCollapse">
      <i class="fas fa-truck"></i>
      <span>Shippings</span>
    </a>
    <div id="shippingCollapse" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Shippings Options:</h6>
        <a class="collapse-item" href="{{ route('shipping.index') }}">Shippings</a>
        <a class="collapse-item" href="{{ route('shipping.create') }}">Add Shipping</a>
      </div>
    </div>
  </li>


  <li class="nav-item">
    <a class="nav-link" href="{{ route('order.index') }}">
      <i class="fas fa-hammer fa-chart-area"></i>
      <span>Orders</span>
    </a>
  </li>


  <li class="nav-item">
    <a class="nav-link" href="{{ route('review.index') }}">
      <i class="fas fa-comments"></i>
      <span>Reviews</span></a>
  </li>


  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block">


  <li class="nav-item">
    <a class="nav-link" href="{{ route('coupon.index') }}">
      <i class="fas fa-table"></i>
      <span>Coupons</span></a>
  </li>


  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>

</ul>
