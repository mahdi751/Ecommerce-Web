
<!-- Start Shop Newsletter  -->
<section class="shop-newsletter section">
  <div class="container">
      <div class="inner-top">
          <div class="row">
              <div class="col-lg-8 offset-lg-2 col-12">
                  <div class="inner">
                      <h4>Newsletter</h4>
                      <p> Subscribe to our newsletter and get latest product updates</p>
                      <form method="post" action="{{ route('Savesubscribe') }}" class="newsletter-inner">
                        @csrf
                        {{-- <input name="email" placeholder="Your email address" required="" type="email"> --}}
                        <button class="btn" type="submit" style="border-radius: 30px;!important">Subscribe</button>
                    </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>
<!-- End Shop Newsletter -->
