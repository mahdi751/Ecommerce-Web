@extends('Sellers.InStorelayouts.InStoreMaster')

@section('main-content')
    <div class="card">
        <h5 class="card-header">Edit Event</h5>
        <div class="card-body">
            <form method="post" action="{{ route('event.update', $event->id) }}">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="title" placeholder="Enter title"
                        value="{{ $event->title }}" class="form-control">
                    @error('title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="description" class="col-form-label">Description</label>
                    <textarea class="form-control" id="description" name="description">{{ $event->description }}</textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="start_time" class="col-form-label">Start Date & Time <span
                            class="text-danger">*</span></label>
                    <input id="start_time" type="datetime-local" name="start_time"
                        value="{{ date('Y-m-d\TH:i', strtotime($event->start_time)) }}" class="form-control">
                    @error('start_time')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="end_time" class="col-form-label">End Date & Time <span class="text-danger">*</span></label>
                    <input id="end_time" type="datetime-local" name="end_time"
                        value="{{ date('Y-m-d\TH:i', strtotime($event->end_time)) }}" class="form-control">
                    @error('end_time')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>




                <div class="form-group">
                    <label for="inputPhoto" class="col-form-label">Photo</label>
                    <div class="input-group">
                        <span class="input-group-btn">
                            <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                                <i class="fa fa-picture-o"></i> Choose
                            </a>
                        </span>
                        <input id="thumbnail" class="form-control" type="text" name="photo"
                            value="{{ $event->photo }}">
                    </div>
                    <div id="holder" style="margin-top:15px;max-height:100px;"></div>
                    @error('photo')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control">
                        <option value="active" {{ $event->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $event->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
@endpush
@push('scripts')
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="{{ asset('backend/summernote/summernote.min.js') }}"></script>
    <script>
        $('#lfm').filemanager('image');

        $(document).ready(function() {
            $('#description').summernote({
                placeholder: "Write short description.....",
                tabsize: 2,
                height: 150
            });
        });
    </script>
@endpush
