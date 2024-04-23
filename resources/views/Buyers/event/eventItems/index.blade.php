@extends('Buyers.layouts.master')



@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'E-SHOP')
@section('main-content')

  <div class="row">
    <div class="col-12">
      <div class="section-title">
        <h2>{{ $event->title }}</h2>

      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">
      @foreach ($products as $product)
        <div class="col-md-4 mb-4">
          <div class="card">
            <img src="{{ $product->photo }}" class="event-item-img" alt="{{ $product->title }}">
            <div class="card-body">
              <h5 class="card-title">{{ $product->title }}</h5>
              <p class="card-text">{{ $product->description }}</p>
              <p class="card-text">Starting price: ${{ $product->starting_bid_price }}</p>
              <p class="card-text">Closing price: ${{ $product->closing_bid }}</p>
              <p class="card-text">Current highest bid: $<span
                  id="currentBid_{{ $product->id }}">{{ optional($product->highestBid)->bid ?? 0 }}</span></p>
              <p class="card-text">New bid:
                $<span>{{ optional($product->getBidByUser()->first())->bid ?? 0 }}</span>
              </p>
              <form id="bidForm_{{ $product->id }}">
                @csrf
                <div class="form-group">
                  <label for="bidAmount">Your Bid:</label>
                  <input type="number" class="form-control" id="bidAmount_{{ $product->id }}" name="bid"
                    min="{{ (optional($product->highestBid)->bid ?? 0) + 1 }}" required>
                </div>
                <button type="button" class="btn btn-primary" onclick="placeBid('{{ $product->id }}')">Place
                  Bid</button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>



@endsection


@push('scripts')
  <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <script>
    // Initialize Pusher with your app key
    var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }} ', {
      cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
      encrypted: true
    });
    var event_id = '{{ $event->id }}';

    // Subscribe to the channel for bid updates
    var channel = pusher.subscribe('event-'.event_id);
    // Bind to the 'new-bid' event
    channel.bind('NewBid', function(data) {

      var productId = data.product_id;
      var newBid = data.bid;
      document.getElementById('currentBid_' + productId).innerText = newBid;
    });
    // Function to place a bid
    function placeBid(productId) {

      var bidAmount = document.getElementById('bidAmount_' + productId).value;
      var formData = new FormData();
      formData.append('bid', bidAmount);
      formData.append('product_id', productId);
      formData.append('event_id', event_id);

      fetch('{{ route('bids.store') }}', { // Updated route to match the store action
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => {
          if (!response.ok) {
            alert("Invalid request");
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          if (data.message === 'Bid placed successfully') {
            // Optional: Update the UI to show the success message or update bid information
          } else {
            // Optional: Handle error scenario
            alert(data.error);
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }
  </script>
@endpush
