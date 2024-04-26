@extends('Buyers.layouts.master')

@section('title', 'E-SHOP')

@section('main-content')
  <!-- Breadcrumbs -->
  <div class="breadcrumbs">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="bread-inner">
            <ul class="bread-list">
              <li><a href="index1.html">Home<i class="ti-arrow-right"></i></a></li>
              <li class="active"><a href="blog-single.html">Shop Grid</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Breadcrumbs -->

  <!-- Product Style -->
  <form action="{{ route('shop.filter') }}" method="POST">
    @csrf


    <section class="product-area shop-sidebar shop section">
      <div class="container">
        <div class="row">




          <div class="col-lg-3 col-md-4 col-12">
            <div class="shop-sidebar">
              <!-- Single Widget -->
              <div class="single-widget category">
                <h3 class="title">Categories</h3>
                <ul class="categor-list">

                  @if ($categories)
                    <li>
                      @foreach ($categories as $cat_info)
                        @if ($cat_info->child_cat->count() > 0)
                    <li><a href="{{ route('product-cat', $cat_info->slug) }}">{{ $cat_info->title }}</a>
                      <ul>
                        @foreach ($cat_info->child_cat as $sub_menu)
                          <li><a
                              href="{{ route('product-sub-cat', [$cat_info->slug, $sub_menu->slug]) }}">{{ $sub_menu->title }}</a>
                          </li>
                        @endforeach
                      </ul>
                    </li>
                  @else
                    <li><a href="{{ route('product-cat', $cat_info->slug) }}">{{ $cat_info->title }}</a></li>
                  @endif
                  @endforeach
                  </li>
                  @endif

                </ul>
              </div>
              <!--/ End Single Widget -->
              <!-- Shop By Price -->
              <div class="single-widget range">
                <h3 class="title">Shop by Price</h3>
                <div class="price-filter">
                  <div class="price-filter-inner">
                    @php
                      $max = DB::table('products')->max('price');
                      // dd($max);
                    @endphp
                    <div id="slider-range" data-min="0" data-max="{{ $max }}"></div>
                    <div class="product_filter">
                      <button type="submit" class="filter_button">Filter</button>
                      <div class="label-input">
                        <span>Range:</span>
                        <input style="" type="text" id="amount" readonly />
                        <input type="hidden" name="price_range" id="price_range"
                          value="@if (!empty($_GET['price'])) {{ $_GET['price'] }} @endif" />

                      </div>
                    </div>
                  </div>
                </div>

              </div>
              <!--/ End Shop By Price -->

            </div>

          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <div class="row">
              <div class="col-12">
                <!-- Shop Top -->
                <div class="shop-top">
                  <div class="shop-shorter">
                    <div class="single-shorter">
                      <label>Show :</label>
                      <select class="show" name="show" onchange="this.form.submit();">
                        <option value="">Default</option>
                        <option value="9" @if (!empty($_GET['show']) && $_GET['show'] == '9') selected @endif>09</option>
                        <option value="15" @if (!empty($_GET['show']) && $_GET['show'] == '15') selected @endif>15</option>
                        <option value="21" @if (!empty($_GET['show']) && $_GET['show'] == '21') selected @endif>21</option>
                        <option value="30" @if (!empty($_GET['show']) && $_GET['show'] == '30') selected @endif>30</option>
                      </select>
                    </div>
                    <div class="single-shorter">
                      <label>Sort By :</label>
                      <select class='sortBy' name='sortBy' onchange="this.form.submit();">
                        <option value="">Default</option>
                        <option value="title" @if (!empty($_GET['sortBy']) && $_GET['sortBy'] == 'title') selected @endif>Name</option>
                        <option value="price" @if (!empty($_GET['sortBy']) && $_GET['sortBy'] == 'price') selected @endif>Price</option>
                        <option value="category" @if (!empty($_GET['sortBy']) && $_GET['sortBy'] == 'category') selected @endif>Category</option>

                      </select>
                    </div>
                  </div>
                  <ul class="view-mode">
                    <li class="active"><a href="javascript:void(0)"><i class="fa fa-th-large"></i></a></li>
                    <li><a href="{{ route('product-lists') }}"><i class="fa fa-th-list"></i></a></li>
                  </ul>
                </div>
                <!--/ End Shop Top -->
              </div>
            </div>
            <div class="row">
              {{-- {{$products}} --}}
              @if (count($products) > 0)
                @foreach ($products as $product)
                  <div class="col-lg-4 col-md-6 col-12">
                    <div class="single-product">
                      <div class="product-img">
                        <a href="{{ route('product-detail', $product->slug) }}">
                          @php
                            $photo = explode(',', $product->photo);
                          @endphp
                          <img class="default-img product-image" src="{{ $photo[0] }}" alt="{{ $photo[0] }}">
                          <img class="hover-img product-image" src="{{ $photo[0] }}" alt="{{ $photo[0] }}">
                          @if ($product->discount)
                            <span class="price-dec">{{ $product->discount }} % Off</span>
                          @endif
                        </a>
                        <div class="button-head">
                          <div class="product-action">

                            <a title="Wishlist" class="wishlist" data-id="{{ $product->id }}"><i
                                class=" ti-heart "></i><span>Add to Wishlist</span></a>
                          </div>
                          <div class="product-action-2">
                            <a title="Add to cart">Add to cart</a>
                          </div>
                        </div>
                      </div>
                      <div class="product-content">
                        <h3><a href="{{ route('product-detail', $product->slug) }}">{{ $product->title }}</a></h3>
                        @php
                          $after_discount =
                              $product->getAmountConverted($selectedCurrency, $product->price) -
                              ($product->getAmountConverted($selectedCurrency, $product->price) * $product->discount) /
                                  100;
                        @endphp
                        <span>${{ number_format($after_discount, 2) }}</span>
                        <del
                          style="padding-left:4%;">${{ number_format($product->getAmountConverted($selectedCurrency, $product->price), 2) }}</del>
                      </div>
                    </div>
                  </div>
                @endforeach
              @else
                <h4 class="text-warning" style="margin:100px auto;">There are no products.</h4>
              @endif




            </div>
            <div class="row">
              <div class="col-md-12 justify-content-center d-flex">
                {{ $products->appends($_GET)->links() }}
              </div>

            </div>


          </div>

        </div>
    </section>
  </form>

  <!--/ End Product Style 1  -->




@endsection
@push('styles')
  <style>
    .pagination {
      display: inline-flex;
    }

    .filter_button {
      /* height:20px; */
      text-align: center;
      background: #F7941D;
      padding: 8px 16px;
      margin-top: 10px;
      color: white;
    }
  </style>
@endpush
@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
  {{-- <script>
        $('.cart').click(function(){
            var quantity=1;
            var pro_id=$(this).data('id');
            $.ajax({
                url:"{{route('add-to-cart')}}",
                type:"POST",
                data:{
                    _token:"{{csrf_token()}}",
                    quantity:quantity,
                    pro_id:pro_id
                },
                success:function(response){
                    console.log(response);
					if(typeof(response)!='object'){
						response=$.parseJSON(response);
					}
					if(response.status){
						swal('success',response.msg,'success').then(function(){
							document.location.href=document.location.href;
						});
					}
                    else{
                        swal('error',response.msg,'error').then(function(){
							// document.location.href=document.location.href;
						});
                    }
                }
            })
        });
    </script> --}}
  <script>
    $(document).ready(function() {
      /*----------------------------------------------------*/
      /*  Jquery Ui slider js
      /*----------------------------------------------------*/
      if ($("#slider-range").length > 0) {
        const max_value = parseInt($("#slider-range").data('max')) || 500;
        const min_value = parseInt($("#slider-range").data('min')) || 0;
        const currency = $("#slider-range").data('currency') || '';
        let price_range = min_value + '-' + max_value;
        if ($("#price_range").length > 0 && $("#price_range").val()) {
          price_range = $("#price_range").val().trim();
        }

        let price = price_range.split('-');
        $("#slider-range").slider({
          range: true,
          min: min_value,
          max: max_value,
          values: price,
          slide: function(event, ui) {
            $("#amount").val(currency + ui.values[0] + " -  " + currency + ui.values[1]);
            $("#price_range").val(ui.values[0] + "-" + ui.values[1]);
          }
        });
      }
      if ($("#amount").length > 0) {
        const m_currency = $("#slider-range").data('currency') || '';
        $("#amount").val(m_currency + $("#slider-range").slider("values", 0) +
          "  -  " + m_currency + $("#slider-range").slider("values", 1));
      }
    })
  </script>
@endpush
