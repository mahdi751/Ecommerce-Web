<header class="header shop">
  <!-- Topbar -->
  <div class="topbar">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-md-12 col-12">
          <!-- Top Left -->

          <div class="col-lg-6 col-md-12 col-12">
            <!-- Top Right -->
            <div class="right-content">
              <ul class="list-main">
                <li><i class="ti-location-pin"></i> <a>Track Order</a></li>
                {{-- <li><i class="ti-alarm-clock"></i> <a href="#">Daily deal</a></li> --}}
                @auth
                    <li><i class="ti-user"></i> <a href="{{ route('user-profile') }}" target="_blank">Dashboard</a></li>
                  
                  <li><i class="ti-power-off"></i> <a href="{{ route('logout') }}">Logout</a></li>
                @else
                  <li><i class="ti-power-off"></i><a>Login /</a> <a>Register</a></li>
                @endauth
              </ul>
              <form id="currencyForm" action="{{ route('updateCurrency') }}" method="POST" class="form-inline">
                @csrf
                <div class="form-group mr-2">
                  <label class="mr-2" for="currency">Choose a currency:</label>
                  <select name="cur" id="currency" class="form-control">
                    <option value="LBP" {{ session('selected_currency') === 'LBP' ? 'selected' : '' }}>LBP</option>
                    <option value="USD" {{ session('selected_currency') === 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ session('selected_currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                    <option value="KWD" {{ session('selected_currency') === 'KWD' ? 'selected' : '' }}>KWD</option>
                  </select>
                </div>
                <button type="submit" class="btn btn-primary">Save Currency</button>
              </form>
            </div>
            <!-- End Top Right -->
          </div>
        </div>
      </div>
    </div>
    <!-- End Topbar -->
    <div class="middle-inner">
      <div class="container">
        <div class="row">
          <div class="col-lg-2 col-md-2 col-12">

            <!-- Search Form -->
            <div class="search-top">
              <div class="top-search"><a href="#0"><i class="ti-search"></i></a></div>
              <!-- Search Form -->
              <div class="search-top">
                <form class="search-form">
                  <input type="text" placeholder="Search here..." name="search">
                  <button value="search" type="submit"><i class="ti-search"></i></button>
                </form>
              </div>
              <!--/ End Search Form -->
            </div>
            <!--/ End Search Form -->
            <div class="mobile-nav"></div>
          </div>
          <div class="col-lg-8 col-md-7 col-12">
            <div class="search-bar-top">
              <div class="search-bar">

                <form method="POST" action="{{ route('product.search') }}">
                  @csrf
                  <input type="hidden" name="store_id" value="{{ session('current_store_id') }}">
                  <input name="search" placeholder="Search Products Here....." type="search">

                  <button class="btnn" type="submit"><i class="ti-search"></i></button>
                </form>
              </div>
            </div>
          </div>
          <div class="col-lg-2 col-md-3 col-12">
            <div class="right-bar">
              <!-- Search Form -->
              <div class="sinlge-bar shopping">
                @php
                  $total_prod = 0;
                  $total_amount = 0;
                @endphp
                @if (session('wishlist'))
                  @foreach (session('wishlist') as $wishlist_items)
                    @php
                      $total_prod += $wishlist_items['quantity'];
                      $total_amount += $wishlist_items['amount'];
                    @endphp
                  @endforeach
                @endif
                <a class="single-icon"><i class="fa fa-heart-o"></i> <span
                    class="total-count">{{ Helper::wishlistCount() }}</span></a>
                <!-- Shopping Item -->
                @auth
                  <div class="shopping-item">
                    <div class="dropdown-cart-header">
                      <span>{{ count(Helper::getAllProductFromWishlist()) }} Items</span>
                      <a href="{{ route('wishlist') }}">View Wishlist</a>
                    </div>
                    <ul class="shopping-list">
                      {{-- {{Helper::getAllProductFromCart()}} --}}
                      @foreach (Helper::getAllProductFromWishlist() as $data)
                        @php
                          $photo = explode(',', $data->product['photo']);
                        @endphp
                        <li>
                          <a href="{{ route('wishlist-delete', $data->id) }}" class="remove" title="Remove this item"><i
                              class="fa fa-remove"></i></a>
                          <a class="cart-img" href="#"><img src="{{ $photo[0] }}"
                              alt="{{ $photo[0] }}"></a>
                          <h4><a href="{{ route('product-detail', $data->product['slug']) }}"
                              target="_blank">{{ $data->product['title'] }}</a></h4>
                          <p class="quantity">{{ $data->quantity }} x - <span
                              class="amount">${{ number_format($data->price, 2) }}</span></p>
                        </li>
                      @endforeach
                    </ul>
                    <div class="bottom">
                      <div class="total">
                        <span>Total</span>
                        <span class="total-amount">${{ number_format(Helper::totalWishlistPrice(), 2) }}</span>
                      </div>
                      <a class="btn animate">Cart</a>
                    </div>
                  </div>
                @endauth
                <!--/ End Shopping Item -->
              </div>
              {{-- <div class="sinlge-bar">
                          <a href="{{route('wishlist')}}" class="single-icon"><i class="fa fa-heart-o" aria-hidden="true"></i></a>
                      </div> --}}
              <div class="sinlge-bar shopping">
                <a class="single-icon"><i class="ti-bag"></i> <span
                    class="total-count">{{ Helper::cartCount() }}</span></a>
                <!-- Shopping Item -->
                @auth
                  <div class="shopping-item">
                    <div class="dropdown-cart-header">
                      <span>{{ count(Helper::getAllProductFromCart()) }} Items</span>
                      <a href="{{ route('cart') }}">View Cart</a>
                    </div>
                    <ul class="shopping-list">
                      {{-- {{Helper::getAllProductFromCart()}} --}}
                      @foreach (Helper::getAllProductFromCart() as $data)
                        @php
                          $photo = explode(',', $data->product['photo']);
                        @endphp
                        <li>
                          <a href="{{ route('cart-delete', $data->id) }}" class="remove" title="Remove this item"><i
                              class="fa fa-remove"></i></a>
                          <a class="cart-img" href="#"><img src="{{ $photo[0] }}"
                              alt="{{ $photo[0] }}"></a>
                          <h4><a href="{{ route('product-detail', $data->product['slug']) }}"
                              target="_blank">{{ $data->product['title'] }}</a></h4>
                          <p class="quantity">{{ $data->quantity }} x - <span
                              class="amount">{{ $selectedCurrencySign }}{{ number_format(Helper::getAmountConverted($selectedCurrency, $data->price), 2) }}</span>
                          </p>
                        </li>
                      @endforeach
                    </ul>
                    <div class="bottom">
                      <div class="total">
                        <span>Total</span>
                        <span class="total-amount">${{ number_format(Helper::totalCartPrice(), 2) }}</span>
                      </div>
                      <a href="{{ route('checkout') }}" class="btn animate">Checkout</a>
                    </div>
                  </div>
                @endauth
                <!--/ End Shopping Item -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Header Inner -->
    @php
      $store_id = DB::table('memory')->value('storeId');
    @endphp
    <div class="header-inner">
      <div class="container">
        <div class="cat-nav-head">
          <div class="row">
            <div class="col-lg-12 col-12">
              <div class="menu-area">
                <!-- Main Menu -->
                <nav class="navbar navbar-expand-lg">
                  <div class="navbar-collapse">
                    <div class="nav-inner">
                      <ul class="nav main-menu menu navbar-nav">
                        <li class="{{ Request::path() == 'home' ? 'active' : '' }}"><a
                            href="{{ route('home') }}">Stores</a></li>
                        <li class="{{ Request::path() == 'home/' . $store_id ? 'active' : '' }}">
                          <a href="{{ route('homestore', ['store_id' => $store_id]) }}">Home</a>
                        </li>

                        <li class="@if (Request::path() == 'product-grids' || Request::path() == 'product-lists') active @endif"><a
                            href="{{ route('product-lists') }}">Products</a><span class="new">New</span></li>





                      </ul>
                    </div>
                  </div>
                </nav>
                <!--/ End Main Menu -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Add an event listener to the currency select element
        document.getElementById('currency').addEventListener('change', function() {
          // Submit the form when the currency selection changes
          document.getElementById('currencyForm').submit();
        });
      });
    </script>
    <!--/ End Header Inner -->
</header>
