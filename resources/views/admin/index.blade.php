@extends('admin.layouts.master')
@section('title','Seller Dashboard')
@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Welcome back admin!</h1>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <p class="text-muted">Welcome to the Admin Dashboard, your central hub for managing various aspects of the e-commerce platform. Here, you can efficiently oversee users, and stores.</p>
            <p class="text-muted">We've designed the Admin Dashboard to provide you with comprehensive control and insights, ensuring smooth operation and growth of the platform. Whether you're handling routine tasks or addressing complex issues, we're here to facilitate your administrative duties.</p>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Key Features for Admins</h5>
                    <ul class="list-unstyled">
                        <li>User management</li>
                        <li>Change User password</li>
                        <li>Store activation and deactivation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
