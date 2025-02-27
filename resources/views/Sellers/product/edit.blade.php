@extends('Sellers.InStorelayouts.InStoreMaster')

@section('main-content')
  <div class="card">
    <h5 class="card-header">Edit Product</h5>
    <div class="card-body">
      <form method="post" action="{{ route('product.update', $product->id) }}">
        @csrf
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
          <input id="inputTitle" type="text" name="title" placeholder="Enter title" value="{{ $product->title }}"
            class="form-control">
          @error('title')
            <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label for="is_event_item">Is Event Item</label><br>
          <input type="checkbox" name='is_event_item' id='is_event_item' value='1'
            {{ $product->is_event_item ? 'checked' : '' }} disabled> Yes
        </div>

        {{-- Event-related fields --}}
        @if ($product->is_event_item)
          <div class="form-group">
            <label for="event_id">Event <span class="text-danger">*</span></label>
            <select name="event_id" id="event_id" class="form-control">
              <option value="">--Select any event--</option>
              @foreach ($events as $key => $event_data)
                <option value='{{ $event_data->id }}' {{ $product->event_id == $event_data->id ? 'selected' : '' }}>
                  {{ $event_data->title }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="starting_bid_price">Starting Bid Price</label>
            <input type="number" value="{{ $product->starting_bid_price }}" name="starting_bid_price"
              id="starting_bid_price" class="form-control">
          </div>
          <div class="form-group">
            <label for="minimum_bid_increment">Minimum Bid Increment</label>
            <input type="number" value="{{ $product->minimum_bid_increment }}" name="minimum_bid_increment"
              id="minimum_bid_increment" class="form-control">
          </div>
          <div class="form-group">
            <label>Current Highest Bid</label>
            <input value="{{ optional($product->highestBid)->bid ?? 0 }}" class="form-control" readonly>
          </div>
          <div class="form-group">
            <label for="closing_bid">Closing Bid</label>
            <input type="number"value="{{ $product->closing_bid }}" name="closing_bid" id="closing_bid"
              class="form-control">
          </div>
          <div class="form-group">
            <label for="bid_status">Bid Status</label>
            <select name="bid_status" id="bid_status" class="form-control">
              <option value="open" {{ $product->bid_status == 'open' ? 'selected' : '' }}>Open</option>
              <option value="closed" {{ $product->bid_status == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
          </div>
        @endif


        <div class="form-group">
          <label for="summary" class="col-form-label">Summary <span class="text-danger">*</span></label>
          <textarea class="form-control" id="summary" name="summary">{{ $product->summary }}</textarea>
          @error('summary')
            <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="description" class="col-form-label">Description</label>
          <textarea class="form-control" id="description" name="description">{{ $product->description }}</textarea>
          @error('description')
            <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>


        <div class="form-group">
          <label for="is_featured">Is Featured</label><br>
          <input type="checkbox" name='is_featured' id='is_featured' value='{{ $product->is_featured }}'
            {{ $product->is_featured ? 'checked' : '' }}> Yes
        </div>
        {{-- {{$categories}} --}}

        <div class="form-group">
          <label for="cat_id">Category <span class="text-danger">*</span></label>
          <select name="cat_id" id="cat_id" class="form-control">
            <option value="">--Select any category--</option>
            @foreach ($categories as $key => $cat_data)
              <option value='{{ $cat_data->id }}' {{ $product->cat_id == $cat_data->id ? 'selected' : '' }}>
                {{ $cat_data->title }}</option>
            @endforeach
          </select>
        </div>
        @php
          $sub_cat_info = DB::table('categories')
              ->select('title')
              ->where('id', $product->child_cat_id)
              ->get();
          // dd($sub_cat_info);
        @endphp
        {{-- {{$product->child_cat_id}} --}}
        <div class="form-group {{ $product->child_cat_id ? '' : 'd-none' }}" id="child_cat_div">
          <label for="child_cat_id">Sub Category</label>
          <select name="child_cat_id" id="child_cat_id" class="form-control">
            <option value="">--Select any sub category--</option>

          </select>
        </div>

        {{-- <div class="form-group">
                    <label for="price" class="col-form-label">Price <span class="text-danger">*</span></label>
                    <input id="price" type="number" name="price" placeholder="Enter price"
                        value="{{ $product->price }}" class="form-control">

                    @error('price')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div> --}}
        <div class="form-group">
          <label for="price" class="col-form-label">Price <span class="text-danger">*</span></label>
          @if ($product->is_event_item)
            <span>: {{ $product->price }}</span>
            <input type="hidden" name="price" value="{{ $product->price }}">
          @else
            <input id="price" type="number" name="price" placeholder="Enter price"
              value="{{ $product->price }}" class="form-control">
          @endif
          @error('price')
            <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>
        @if (!$product->is_event_item)
          <div class="form-group">
            <label for="discount" class="col-form-label">Discount(%)</label>
            <input id="discount" type="number" name="discount" min="0" max="100"
              placeholder="Enter discount" value="{{ $product->discount }}" class="form-control">
            @error('discount')
              <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        @endif
        <div class="form-group">
          <label for="size">Size</label>
          <select name="size[]" class="form-control selectpicker" multiple data-live-search="true">
            <option value="">--Select any size--</option>
            @foreach ($items as $item)
              @php
                $data = explode(',', $item->size);
                // dd($data);
              @endphp
              <option value="S" @if (in_array('S', $data)) selected @endif>Small</option>
              <option value="M" @if (in_array('M', $data)) selected @endif>Medium</option>
              <option value="L" @if (in_array('L', $data)) selected @endif>Large</option>
              <option value="XL" @if (in_array('XL', $data)) selected @endif>Extra Large</option>
            @endforeach
          </select>
        </div>


        <div class="form-group">
          <label for="condition">Condition</label>
          <select name="condition" class="form-control">
            <option value="">--Select Condition--</option>
            <option value="default" {{ $product->condition == 'default' ? 'selected' : '' }}>Default</option>
            <option value="new" {{ $product->condition == 'new' ? 'selected' : '' }}>New</option>
            <option value="hot" {{ $product->condition == 'hot' ? 'selected' : '' }}>Hot</option>
          </select>
        </div>

        <div class="form-group">
          <label for="stock">Quantity <span class="text-danger">*</span></label>
          <input id="quantity" type="number" name="stock" min="0" placeholder="Enter quantity"
            value="{{ $product->stock }}" class="form-control">
          @error('stock')
            <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label for="inputPhoto" class="col-form-label">Photo <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-btn">
              <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
                <i class="fas fa-image"></i> Choose
              </a>
            </span>
            <input id="thumbnail" class="form-control" type="text" name="photo" value="{{ $product->photo }}">
          </div>
          <div id="holder" style="margin-top:15px;max-height:100px;"></div>
          @error('photo')
            <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
            <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
          @error('status')
            <span class="text-danger">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
          <button class="btn btn-success" type="submit">Update</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('backend/summernote/summernote.min.css') }}">
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
@endpush
@push('scripts')
  <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
  <script src="{{ asset('backend/summernote/summernote.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

  <script>
    $('#lfm').filemanager('image');

    $(document).ready(function() {
      $('#summary').summernote({
        placeholder: "Write short description.....",
        tabsize: 2,
        height: 150
      });
    });
    $(document).ready(function() {
      $('#description').summernote({
        placeholder: "Write detail Description.....",
        tabsize: 2,
        height: 150
      });
    });
  </script>

  <script>
    var child_cat_id = '{{ $product->child_cat_id }}';
    // alert(child_cat_id);
    $('#cat_id').change(function() {
      var cat_id = $(this).val();

      if (cat_id != null) {
        // ajax call
        $.ajax({
          url: "/seller/category/" + cat_id + "/child",
          type: "POST",
          data: {
            _token: "{{ csrf_token() }}"
          },
          success: function(response) {
            if (typeof(response) != 'object') {
              response = $.parseJSON(response);
            }
            var html_option = "<option value=''>--Select any one--</option>";
            if (response.status) {
              var data = response.data;
              if (response.data) {
                $('#child_cat_div').removeClass('d-none');
                $.each(data, function(id, title) {
                  html_option += "<option value='" + id + "' " + (
                      child_cat_id == id ? 'selected ' : '') + ">" +
                    title + "</option>";
                });
              } else {
                console.log('no response data');
              }
            } else {
              $('#child_cat_div').addClass('d-none');
            }
            $('#child_cat_id').html(html_option);

          }
        });
      } else {

      }

    });
    if (child_cat_id != null) {
      $('#cat_id').change();
    }
  </script>
@endpush
