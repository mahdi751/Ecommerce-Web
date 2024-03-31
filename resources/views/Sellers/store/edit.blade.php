@extends('Sellers.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Edit Store</h5>
    <div class="card-body">
      <form method="post" action="{{route('store.update',$store->id)}}">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label for="inputStoreName" class="col-form-label">Store Name <span class="text-danger">*</span></label>
            <input id="inputStoreName" type="text" name="name" placeholder="Enter Store Name"  value="{{$store->name}}" class="form-control">
            @error('name')
            <span class="text-danger">{{$message}}</span>
            @enderror
          </div>

          <div class="form-group">
              <label for="description" class="col-form-label">Store Description<span class="text-danger">*</span></label>
              <textarea class="form-control" id="description" name="description">{{$store->description}}"</textarea>
              @error('description')
              <span class="text-danger">{{$message}}</span>
              @enderror
            </div>

            <div class="form-group">
              <label for="inputEmail" class="col-form-label">Email</label>
              <input id="inputEmail" type="email" name="email" placeholder="Enter email"  value="{{$store->email}}" class="form-control">
              @error('email')
              <span class="text-danger">{{$message}}</span>
              @enderror
            </div>

            <div class="form-group">
                <label for="inputNumber" class="col-form-label">Phone Number<span class="text-danger">*</span></label>
                <input id="inputNumber" type="number" name="phone_number" placeholder="Enter number"  value="{{$store->phone_number}}" class="form-control">
                @error('phone_number')
                <span class="text-danger">{{$message}}</span>
                @enderror
              </div>

              <div class="form-group">
                  <label for="inputAddress" class="col-form-label">Address</label>
                  <input id="inputAddress" type="text" name="address" placeholder="Enter store address"  value="{{$store->address}}" class="form-control">
                  @error('address')
                  <span class="text-danger">{{$message}}</span>
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
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script>
    $(document).ready(function() {
    $('#description').summernote({
      placeholder: "Write short description.....",
        tabsize: 2,
        height: 150
    });
    });
</script>
@endpush
