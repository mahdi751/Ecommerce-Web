@extends('Buyers.layouts.master')



@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'E-SHOP')
@section('main-content')
  <div style="width: 100%">

    <div class="row">
      <div class="col-12">
        <div class="section-title">
          <h2>{{ $event->title }}</h2>

        </div>
      </div>
    </div>
    <div style="width: 100%">
      <div class="row ">
        <div class="container " style="width: 100%; display:flex; flex-wrap:wrap ">
          @foreach ($products as $product)
            <div style="margin: 20px 10px; width:330px">
              <!-- Start Single List  -->
              <div class="single-list">

                <div class="card card-bid">
                  <img style="width: 200px; height:100px;" src="{{ $product->photo }}" class="event-item-img"
                    alt="{{ $product->title }}">
                  <div class="card-body">
                    <h5 class="card-title">{{ $product->title }}</h5>
                    <p class="card-text">{!! html_entity_decode($product->summary) !!}</p>
                    <p class="card-text">Starting price: ${{ $product->starting_bid_price }}</p>
                    <p class="card-text">Current highest bid: $<span
                        id="currentBid_{{ $product->id }}">{{ optional($product->highestBid)->bid ?? 0 }}</span></p>
                    <p class="card-text">your bid:
                      $<span
                        id="userBid_{{ $product->id }}">{{ optional($product->getBidByUser()->first())->bid ?? 0 }}</span>
                    </p>
                    @if ($product->bid_status == 'open')
                      <form id="bidForm_{{ $product->id }}">
                        @csrf
                        <div class="form-group">
                          <label for="bidAmount">Place new Bid:</label>
                          <input type="number" class="form-control" id="bidAmount_{{ $product->id }}" name="bid"
                            min="{{ (optional($product->highestBid)->bid ?? 0) + 1 }}" required>
                        </div>
                        <button style="margin: 10px 0" type="button" class="btn btn-primary"
                          onclick="placeBid('{{ $product->id }}')">Place
                          Bid</button>
                        <button type="button" class="btn btn-primary"
                          onclick="grabItem('{{ $product->id }}','{{ $product->closing_bid }}')">Grab it for
                          ${{ $product->closing_bid }}
                        </button>
                      </form>
                    @else
                      <div class="form-group">

                        <button type="button" class=" btn-sold" disabled>
                          Item Sold</button>

                        <div class="bid-message-container">
                          <div id="bidMessage_{{ $product->id }}" class="bid-message">New bid placed!</div>
                        </div>

                      </div>
                    @endif

                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

  </div>

@endsection


@push('scripts')
  <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <script>
    // Initialize Pusher with your app key
    var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
      cluster: '{{ env('PUSHER_APP_CLUSTER') }}'
    });
    Pusher.logToConsole = true;

    var event_id = '{{ $event->id }}';

    // Subscribe to the channel for bid updates

    var channel = pusher.subscribe('event-' + event_id);
    channel.bind('pusher:subscription_succeeded', function(members) {
      console.log('successfully subscribed!');
    });

    // Bind to the 'new-bid' event
    channel.bind('NewBid', function(data) {
      var productId = data.bid.product_id;
      var newBid = data.bid.bid;
      var currentBidElement = document.getElementById('currentBid_' + productId);
      var currentBid = parseFloat(currentBidElement.innerText);
      var bidDifference = newBid - currentBid;
      var user_bid = '';

      // Animate the update of the bid amount
      $({
        bidValue: currentBid
      }).animate({
        bidValue: newBid
      }, {
        duration: 1000, // Duration of the animation in milliseconds
        step: function() {
          currentBidElement.innerText = this.bidValue.toFixed(2);
        }
      });

      var bidMessage = document.getElementById('bidMessage_' + productId);
      if (data.bid.user_id === {{ auth()->user()->id }}) {
        bidMessage.style.backgroundColor = 'rgba(9, 135, 15, 0.9)';
        bidMessage.innerText = 'your bid was placed!';

      } else {
        bidMessage.style.backgroundColor = 'rgba(156, 8, 8, 0.9)';
        bidMessage.innerText = 'Higher bid placed!';
      }
      bidMessage.classList.remove('red', 'green');
      bidMessage.classList.add('show');
      setTimeout(function() {
        bidMessage.classList.remove('show');
      }, 2000);
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
          },
        })
        .then(response => {
          if (!response.ok) {
            alert("Invalid request");
            console.log(response.json());
            throw new Error('Network response was not ok');
          }

          if (parseInt(bidAmount) >= parseInt('{{ $product->closing_bid }}')) {
            document.getElementById('userBid_' + productId).innerText = parseFloat('{{ $product->closing_bid }}');
          } else {
            document.getElementById('userBid_' + productId).innerText = bidAmount;
          }
          document.getElementById('bidAmount_' + productId).value = '';

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

    function grabItem(productId, bidAmount) {
      var formData = new FormData();
      formData.append('bid', bidAmount);
      formData.append('product_id', productId);
      formData.append('event_id', event_id);

      fetch('{{ route('bids.store') }}', { // Updated route to match the store action
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
        })
        .then(response => {
          if (!response.ok) {
            alert("Invalid request");
            console.log(response.json());
            throw new Error('Network response was not ok');
          }
          document.getElementById('userBid_' + productId).innerText = bidAmount;
          document.getElementById('bidAmount_' + productId).value = '';

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
