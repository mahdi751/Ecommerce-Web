<!DOCTYPE html>
<html lang="zxx">
<head>

</head>
<body class="js">
    <div class="container">
        <h1>Events</h1>
        <div class="row">
            @foreach ($events as $event)
                <div class="col-md-4 mb-4">
                 
                    <div class="card">
                        <img src="{{ $event->photo }}" class="card-img-top" alt="{{ $event->title }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $event->title }}</h5>
                            <p class="card-text">{{ $event->description }}</p>
                            <p class="card-text">Starts: {{ $event->start_time }}</p>
                            <p class="card-text">Ends: {{ $event->end_time }}</p>
                            <a href="{{ route('products.show', ['event_id' => $event->id]) }}" class="btn btn-primary">View Products</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>