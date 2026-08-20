@extends('frontend.layouts.app')
 
@section('content')

<!-- header section -->
<section class="page-header">
    <div class="overlay"></div>

    <div class="container position-relative"> 
        <h1 data-aos="fade-up">Contact Us</h1>

        <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
            <li class="breadcrumb-item" data-aos="fade-up">
                <a href="{{ route('home') }}">
                    <i class="bi bi-house-door-fill me-1"></i> Home
                </a>
            </li>

            <li class="breadcrumb-item active" data-aos="fade-up">
                Contact Us
            </li>
        </ol>
    </div>
</section>

<section>
  <div class="container">

    <div class="row g-4 mb-5">

      <div class="col-md-4" data-aos="fade-up"> 
        <div class="contact-card">
            <div class="icon">
              <i class="bi bi-geo-alt"></i>
            </div>
            <h5>Visit Us</h5>
            @if($configurations['contact_address'] ?? null)
              <p class="text-muted mb-0">
                {!! $configurations['contact_address'] !!}
              </p>
            @endif
        </div> 
      </div>

      <div class="col-md-4" data-aos="fade-up">
        <div class="contact-card">
          <div class="icon">
            <i class="bi bi-telephone"></i>
          </div>
          <h5>Call Us</h5>
            @if($configurations['contact_phone'] ?? null)
              <p class="text-muted mb-0">
                  {!! $configurations['contact_phone'] !!}
              </p>
            @endif
        </div>
      </div>

      <div class="col-md-4" data-aos="fade-up">
        <div class="contact-card">
          <div class="icon">
            <i class="bi bi-envelope"></i>
          </div>
          <h5>Email Us</h5>
          @if($configurations['contact_email'] ?? null)
            <p class="text-muted mb-0">
              {{ $configurations['contact_email'] }}
            </p>
          @endif

        </div>
      </div>
    </div>

    <div class="row g-4">

      <div class="col-lg-7" data-aos="fade-right">

        <div class="booking-card">
          <h3 class="fw-bold mb-3">Send a Message</h3> 
          @csrf {{-- ensure this or a meta tag with csrf-token exists somewhere on the page --}}

        <div id="contactSuccess" class="alert alert-success d-none"><i class="bi bi-check-circle me-2"></i>Thanks! We will get back to you soon.</div>
        <div id="contactError" class="alert alert-danger d-none"></div>

        <form id="contactForm" class="row g-3">

            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input class="form-control" placeholder="Enter Your Full Name" id="name" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" placeholder="Enter Your Email Address" id="email" required>
            </div>

            <div class="col-12">
                <label class="form-label">Service</label>
                <input class="form-control" id="services" placeholder="Enter Your Service Name" required>
            </div>

            <div class="col-12">
                <label class="form-label">Message</label>
                <textarea class="form-control" rows="5" id="message" placeholder="Enter Your Message Here..." required></textarea>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-orange px-4 py-2 fw-bold" id="contactSubmitBtn">
                    Send Message
                </button>
            </div>

        </form>

        <script>
          document.getElementById('contactForm').addEventListener('submit', function (e) {
              e.preventDefault();

              const successBox = document.getElementById('contactSuccess');
              const errorBox    = document.getElementById('contactError');
              const btn         = document.getElementById('contactSubmitBtn');
              const originalText = btn.innerHTML;

              successBox.classList.add('d-none');
              errorBox.classList.add('d-none');
              btn.disabled = true;
              btn.innerHTML = 'Sending...';

              fetch('{{ route('contact.store') }}', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                  },
                  body: JSON.stringify({
                      name: document.getElementById('name').value,
                      email: document.getElementById('email').value,
                      services: document.getElementById('services').value,
                      message: document.getElementById('message').value,
                  })
              })
              .then(async res => {
                  const data = await res.json();
                  if (!res.ok) throw data;
                  return data;
              })
              .then(() => {
                  successBox.classList.remove('d-none');
                  document.getElementById('contactForm').reset();
              })
              .catch(err => {
                  const msg = err?.message || 'Something went wrong. Please try again.';
                  errorBox.textContent = msg;
                  errorBox.classList.remove('d-none');
              })
              .finally(() => {
                  btn.disabled = false;
                  btn.innerHTML = originalText;
              });
          });
        </script>

      </div>  

      <style>
        .map-card{
            height:100%;
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 10px 25px rgba(0,0,0,.08);
        }

        .map-card iframe{
            width:100%;
            height:100%;
            min-height:450px;
            border:0;
        }
      </style>
    
      <!-- Google Map -->
      <div class="col-lg-5" data-aos="fade-left">

          <div class="map-card">
            @if($configurations['map_embed_url'] ?? null)
                <iframe
                    src="{{ $configurations['map_embed_url'] }}"
                    width="600"
                    height="450"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            @else
                <div class="text-muted small">Map unavailable.</div>
            @endif
        </div>

      </div>

    </div>
    
  </div>
</section>

 @endsection