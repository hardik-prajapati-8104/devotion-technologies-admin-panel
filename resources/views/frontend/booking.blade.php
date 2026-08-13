<?php
	include_once ('elements/header.php'); 
?>

  <!-- header section -->
  <section class="page-header">
      <div class="overlay"></div>

      <div class="container position-relative"> 
          <h1 data-aos="fade-up">Booking</h1>

          <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
              <li class="breadcrumb-item" data-aos="fade-up">
                  <a href="index.html">
                      <i class="bi bi-house-door-fill me-1"></i> Home
                  </a>
              </li>

              <li class="breadcrumb-item active" data-aos="fade-up">
                  Booking
              </li>
          </ol>
      </div>
  </section>

  <section>
    <div class="container">
      <div class="row g-4">

        <div class="col-lg-8">

          <div class="booking-card">
            <h3 class="fw-bold mb-1" data-aos="fade-up">Get an Instant Quote</h3>
            <p class="text-muted" data-aos="fade-up">Fill in your details — we will confirm within 15 minutes.</p>

            <div id="bookingSuccess" class="alert alert-success d-none">
              <i class="bi bi-check-circle me-2"></i>
              Booking received! Our team will reach out shortly.
            </div>

            <form id="bookingForm" class="row g-3 mt-1" data-aos="fade-up"> 
                <div class="col-md-6">
                  <label class="form-label">Full Name</label>
                  <input class="form-control" placeholder="Enter Your Full Name" id="full_name" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Phone</label>
                  <input class="form-control" type="tel" placeholder="Enter  Your Phone Number" id="phone" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input class="form-control" type="email" placeholder="Enter Your Email Address" id="email" required>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Service Type</label>
                  <select class="form-select">
                    <option>Deep Cleaning</option>
                    <option>Apartment Cleaning</option>
                    <option>Commercial Cleaning</option>
                    <option>Carpet & Sofa</option>
                    <option>Window Cleaning</option>
                    <option>Move In / Out</option>
                  </select>
                </div>
   
                <div class="col-12">
                  <label class="form-label">Address</label>
                  <input class="form-control" placeholder="Enter Your Address" id="address" required>
                </div>

                <div class="col-12">
                  <label class="form-label">Description</label>
                  <textarea class="form-control" rows="3" id="description" placeholder="Pets, parking, special requests..."></textarea>
                </div>

                <div class="col-12">
                  <button class="btn btn-orange w-100 py-3 fw-bold">
                    <i class="bi bi-calendar2-check me-2"></i>Request Booking
                  </button>
                </div>
            </form>

          </div>
        </div>

        <div class="col-lg-4">
          <div class="booking-card">
            <h5 class="fw-bold" data-aos="fade-up">Why Book With Us</h5>
            <ul class="list-unstyled mt-3">

              <li class="mb-3" data-aos="fade-up">
                <i class="bi bi-check-circle-fill text-orange me-2"></i>Vetted & insured cleaners
              </li>

              <li class="mb-3" data-aos="fade-up">
                <i class="bi bi-check-circle-fill text-orange me-2"></i>Flat, transparent pricing
              </li>

              <li class="mb-3" data-aos="fade-up">
                <i class="bi bi-check-circle-fill text-orange me-2"></i>Eco-friendly supplies included
              </li>

              <li class="mb-3" data-aos="fade-up">
                <i class="bi bi-check-circle-fill text-orange me-2"></i>Easy reschedule & cancel
              </li>

              <li class="mb-3" data-aos="fade-up">
                <i class="bi bi-check-circle-fill text-orange me-2"></i>100% happiness guarantee
              </li>
            </ul>
            <hr>
            <p class="mb-1 small text-muted" data-aos="fade-up">Need help?</p>
            <a href="tel:+91 9173307640" class="fw-bold text-orange" data-aos="fade-up"><i class="bi bi-telephone me-1"></i>+91 9173307640</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php
	  include_once ('elements/footer.php');
  ?>
