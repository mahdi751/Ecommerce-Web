@extends('Sellers.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Add Store</h5>
    <div class="card-body">
      <form method="post" action="{{route('store.store')}}">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputStoreName" class="col-form-label">Store Name <span class="text-danger">*</span></label>
          <input id="inputStoreName" type="text" name="name" placeholder="Enter Store Name"  value="{{old('name')}}" class="form-control">
          @error('name')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
            <label for="description" class="col-form-label">Store Description<span class="text-danger">*</span></label>
            <textarea class="form-control" id="description" name="description">{{old('description')}}</textarea>
            @error('description')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>

          <div class="form-group">
            <label for="inputEmail" class="col-form-label">Email</label>
            <input id="inputEmail" type="email" name="email" placeholder="Enter email"  value="{{old('email')}}" class="form-control">
            @error('email')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>

          <div class="form-group">
              <label for="inputNumber" class="col-form-label">Phone Number<span class="text-danger">*</span></label>
              <input id="inputNumber" type="number" name="phone_number" placeholder="Enter number"  value="{{old('phone_number')}}" class="form-control">
              @error('phone_number')
              <span class="text-danger">{{$message}}</span>
              @enderror
            </div>


            <div class="form-group">
                <label for="inputPhoto" class="col-form-label">Photo <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-btn">
                        <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                            <i class="fa fa-picture-o"></i> Choose
                        </a>
                    </span>
                    <input id="thumbnail" class="form-control" type="text" name="photo"
                        value="{{ old('photo') }}">
                </div>
                <div id="holder" style="margin-top:15px;max-height:100px;"></div>
                @error('photo')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="inputAddress" class="col-form-label">Address</label>
                <input id="inputAddress" type="text" name="address" placeholder="Enter store address"  value="{{old('address')}}" class="form-control">
                @error('address')
                <span class="text-danger">{{$message}}</span>
                @enderror
              </div>

        <div class="form-group mb-3">
          <button type="reset" class="btn btn-warning">Reset</button>
           <button class="btn btn-success" type="submit">Submit</button>
        </div>
      </form>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script>

$('#lfm').filemanager('image');

    $(document).ready(function() {
      $('#description').summernote({
        placeholder: "Write short description.....",
          tabsize: 2,
          height: 120
      });
    });
</script>

@endpush
