@extends('layouts.app')

@section('content')

<head>
    <link href="{{ asset('backend/css/home.css') }}" rel="stylesheet">

    <style>
        #imageBanner {
            width: 100%;
            object-fit: cover;
        }

        .store-image-container {
            display: inline-block;
            position: relative;
        }

        .store-image {
            max-height: 200px;
            transition: transform 0.3s;
            object-fit: cover;
            border: 10px solid #ffffff; /* Add a white border to separate the shadow */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
        }

        .store-name {
            color: orange;
        }

       
    </style>

</head>

<div>
    <section id='imageBanner'>
        <img id='imageBanner' src="{{ asset('backend/img/banner1.jpg') }}" alt="Banner">
    </section>



    <div class="row justify-content-center mb-4" style="margin-top:50px">
        <div class="col-md-6 text-center">
            <p class="h3 mb-0 text-white" style="letter-spacing: 4px; font-weight: 100; font-size: 20px">Our Stores</p>
        </div>
    </div>



    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="row">
                    @foreach ($stores as $store)
                    <div class="col-md-4 mb-4">
                        <a href="{{ route('homestore', $store->id) }}" class="text-decoration-none">
                            <div class="card rounded-3 shadow-lg hover-expand" style="height: 300px; background-color: #ffffff; overflow: hidden; border-radius: 20px; transition: box-shadow 0.3s;">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div class="text-center mb-3 store-image-container">
                                        @if ($store->photo)
                                            @php
                                                $photo = explode(',', $store->photo);
                                            @endphp
                                            <img src="{{ $photo[0] }}" class="img-fluid rounded-circle store-image" alt="{{ $store->name }}">
                                        @else
                                            <img src="{{ asset('backend/img/thumbnail-default.jpg') }}" class="img-fluid rounded-circle store-image" alt="avatar.png">
                                        @endif
                                    </div>
                                    <div class="text-center">
                                        <h5 class="card-title fs-5 fw-bold mb-3 store-name">{{ $store->name }}</h5>
                                        <p class="card-text text-secondary mb-3" style="font-size: 0.9rem; color: #6c757d;">{{ strip_tags($store->description) }}</p>
                                        <!-- <ul class="list-unstyled mb-3">
                                            @if ($store->email)
                                                <li class="mb-2"><strong>Email:</strong> <span>{{ $store->email }}</span></li>
                                            @endif
                                            <li class="mb-2"><strong>Phone Number:</strong> <span>{{ $store->phone_number }}</span></li>
                                            @if ($store->address)
                                                <li><strong>Address:</strong> <span>{{ $store->address }}</span></li>
                                            @endif
                                        </ul> -->
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


</div>

@endsection
