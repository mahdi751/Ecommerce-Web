@extends('Sellers.InStoreLayouts.InStoreMaster')
@section('title','Seller Dashboard')
@section('main-content')
<div class="container-fluid">
    @include('Sellers.layouts.notification')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Welcome to Seller Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <p class="text-muted">Welcome to the Seller Dashboard, your central hub for managing your products and orders on our e-commerce platform. Here, you can easily add new products, update existing ones, track your sales, and much more.</p>
            <p class="text-muted">We've designed the Seller Dashboard to be intuitive and efficient, allowing you to focus on growing your business. Whether you're a seasoned seller or just getting started, we're here to support you every step of the way.</p>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Key Features for Sellers</h5>
                    <ul class="list-unstyled">
                        <li>Manage products with ease</li>
                        <li>Track sales and orders</li>
                        <li>Track stores and products reviews</li>
                        <li>Multi store support</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
