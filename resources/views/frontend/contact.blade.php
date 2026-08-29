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

 <!-- ==============================
     SOCIAL MEDIA SECTION
================================ -->
<div class="about-social">

    <div class="social-heading">
        <span class="social-heading-icon">
            <i class="bi bi-share-fill"></i>
        </span>

        <div>
            <h6>Connect With Us</h6>
            <p>
                Follow Work Home Safety Solution for helpful tips, updates,
                and the latest protection solutions.
            </p>
        </div>
    </div>

    <div class="social-links">

      @if($configurations['social_twitter'] ?? null)
          <a href="{{ $configurations['social_twitter'] }}" class="social-btn social-x"
            aria-label="Follow us on X" target="_blank" rel="noopener">
              <i class="bi bi-twitter-x"></i>
              <span>Twiter</span>
          </a>
        @endif

        @if($configurations['social_linkedin'] ?? null)
          <a href="{{ $configurations['social_linkedin'] }}" class="social-btn social-linkedin"
            aria-label="Follow us on LinkedIn" target="_blank" rel="noopener">
              <i class="bi bi-linkedin"></i>
              <span>LinkedIn</span>
          </a>
        @endif

        @if($configurations['social_facebook'] ?? null)
          <a href="{{ $configurations['social_facebook'] }}" class="social-btn social-github"
            aria-label="Follow us on GitHub" target="_blank" rel="noopener">
              <i class="bi bi-facebook"></i>
              <span>Facebook</span>
          </a>
        @endif

        @if($configurations['social_instagram'] ?? null)
          <a href="{{ $configurations['social_instagram'] }}" class="social-btn social-website"
            aria-label="Visit our website" target="_blank" rel="noopener">
              <i class="bi bi-globe2"></i>
              <span>Instagram</span>
          </a>
        @endif

        @if($configurations['social_youtube'] ?? null)
          <a href="{{ $configurations['social_youtube'] }}" class="social-btn social-youtube"
            aria-label="Subscribe on YouTube" target="_blank" rel="noopener">
              <i class="bi bi-youtube"></i>
              <span>YouTube</span>
          </a>
        @endif

    </div>

</div>

<style>
          /* ==========================================
          SOCIAL MEDIA SECTION
        ========================================== */

        .about-social { 
            padding: 22px;
            background: #ffffff;
            border: 1px solid #edf0f4;
            border-radius: 18px;
            margin-bottom: 70px;
            box-shadow: 0 8px 30px rgba(23, 43, 77, 0.06);
        }


        /* Heading */

        .social-heading {
            display: flex;
            align-items: flex-start;
            gap: 13px;
            margin-bottom: 20px;
        }


        .social-heading-icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(23, 43, 77, 0.08);
            color: #172b4d;
            font-size: 17px;
        }


        .about-social h6 {
            margin: 0 0 5px;
            font-size: 16px;
            font-weight: 700;
            color: #172b4d;
        }


        .about-social p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }


        /* ==========================================
          SOCIAL LINKS GRID
        ========================================== */

        .social-links {
            display: grid;
            grid-template-columns:
                repeat(5, minmax(0, 1fr));
            gap: 10px;
        }


        /* Social Button */

        .social-btn {
            min-height: 78px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 6px;
            background: #ffffff;
            border: 1px solid #e7ebf0;
            border-radius: 14px;
            color: #172b4d;
            text-decoration: none;
            transition:
                transform 0.25s ease,
                background 0.25s ease,
                border-color 0.25s ease,
                color 0.25s ease,
                box-shadow 0.25s ease;
        }


        .social-btn i {
            font-size: 19px;
            line-height: 1;
        }


        .social-btn span {
            font-size: 11px;
            font-weight: 600;

            line-height: 1.2;
        }


        /* Hover */

        .social-btn:hover {
            transform: translateY(-4px);

            background: #172b4d;
            border-color: #172b4d;

            color: #ffffff;

            box-shadow:
                0 10px 25px rgba(23, 43, 77, 0.18);
        }


        /* Individual Brand Hover Colors */

        .social-x:hover {
            background: #000000;
            border-color: #000000;
        }


        .social-linkedin:hover {
            background: #0a66c2;
            border-color: #0a66c2;
        }


        .social-github:hover {
            background: #24292f;
            border-color: #24292f;
        }


        .social-website:hover {
            background: var(--orange);
            border-color: var(--orange);
        }


        .social-youtube:hover {
            background: #ff0000;
            border-color: #ff0000;
        }


        /* ==========================================
          TABLET
        ========================================== */

        @media (max-width: 767px) {

            .about-social {
                padding: 18px;
                margin-top: 28px;
            }

            .social-links {
                grid-template-columns: repeat(5, 1fr);
                gap: 7px;
            }

            .social-btn {
                min-height: 68px;
                padding: 8px 3px;

                border-radius: 12px;
            }

            .social-btn i {
                font-size: 17px;
            }

            .social-btn span {
                font-size: 9px;
            }

        }


        /* ==========================================
          SMALL MOBILE
        ========================================== */

        @media (max-width: 400px) {

            .about-social {
                padding: 16px;
            }

            .social-heading {
                gap: 10px;
            }

            .social-heading-icon {
                width: 38px;
                height: 38px;

                flex: 0 0 38px;

                font-size: 15px;
            }

            .about-social h6 {
                font-size: 15px;
            }

            .about-social p {
                font-size: 13px;
            }

            .social-links {
                grid-template-columns: repeat(5, 1fr);
                gap: 5px;
            }

            .social-btn {
                min-height: 62px;
                border-radius: 10px;
            }

            .social-btn i {
                font-size: 16px;
            }

            .social-btn span {
                font-size: 8px;
            }

        }
</style>
 @endsection