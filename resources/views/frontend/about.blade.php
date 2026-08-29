@extends('frontend.layouts.app')
 
@section('content')

<!-- header section -->
<section class="page-header">
    <div class="overlay"></div>

    <div class="container position-relative"> 
        <h1 data-aos="fade-up">About Us</h1>

        <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
            <li class="breadcrumb-item" data-aos="fade-up">
                <a href="{{ route('home') }}">
                    <i class="bi bi-house-door-fill me-1"></i> Home
                </a>
            </li>

            <li class="breadcrumb-item active" data-aos="fade-up">
                About Us
            </li>
        </ol>
    </div>
</section>

<section>
  <div class="container">
    <div class="row align-items-center">

      <div class="col-lg-6" data-aos="fade-up">
        <div class="about-img">
          <img src="{{ url('public/frontend/images/about.jpg') }}" alt="Team">
        </div>
      </div>

      <div class="col-lg-6">
        <span class="eyebrow" data-aos="fade-up">Our Story</span>
        <h2 class="section-title" data-aos="fade-up">Building Safer, More Comfortable Spaces</h2>
        <p class="text-muted" data-aos="fade-up">Founded with a commitment to reliable property protection, Work Home Safety Solution provides practical and customized safety solutions for homes, apartments, offices, and commercial spaces.</p>
        <p class="text-muted" data-aos="fade-up">From bird control and mosquito protection to invisible grills and safety nets, we combine quality materials with professional installation to help protect your property while maintaining its appearance and comfort.</p>
        <p class="text-muted" data-aos="fade-up">Today, we continue to focus on quality workmanship, durable solutions, transparent service, and customer satisfaction—because protecting your space should be simple and dependable.</p> 
      </div>
    </div>
  </div>
</section>

  <section class="bg-light">
      <div class="container">

          <div class="text-center">
              <span class="eyebrow" data-aos="fade-up">
                  Our Values
              </span>

              <h2 class="section-title" data-aos="fade-up">
                  What Drives Us
              </h2>

              <p class="section-sub" data-aos="fade-up">
                  The values behind every solution we deliver.
              </p>
          </div>


          <div class="row g-3">

              {{-- Quality --}}
              <div class="col-6 col-md-4 about-card" data-aos="fade-up">
                  <div class="why-card">

                      <div class="icon">
                          <i class="bi bi-patch-check"></i>
                      </div>

                      <h5>Quality</h5>

                      <p class="text-muted mb-0">
                          Quality materials and professional workmanship
                          for lasting protection.
                      </p>

                  </div>
              </div>


              {{-- Safety --}}
              <div class="col-6 col-md-4 about-card" data-aos="fade-up">
                  <div class="why-card">

                      <div class="icon">
                          <i class="bi bi-shield-check"></i>
                      </div>

                      <h5>Safety</h5>

                      <p class="text-muted mb-0">
                          Practical solutions designed to make your
                          space safer and more secure.
                      </p>

                  </div>
              </div>


              {{-- Reliability --}}
              <div class="col-6 col-md-4 about-card" data-aos="fade-up">
                  <div class="why-card">

                      <div class="icon">
                          <i class="bi bi-hand-thumbs-up"></i>
                      </div>

                      <h5>Reliability</h5>

                      <p class="text-muted mb-0">
                          Dependable products and installation you
                          can trust for long-term protection.
                      </p>

                  </div>
              </div>


              {{-- Customer Focus --}}
              <div class="col-6 col-md-4 about-card" data-aos="fade-up">
                  <div class="why-card">

                      <div class="icon">
                          <i class="bi bi-heart"></i>
                      </div>

                      <h5>Customer First</h5>

                      <p class="text-muted mb-0">
                          We understand your needs and provide solutions
                          tailored to your property.
                      </p>

                  </div>
              </div>


              {{-- Professionalism --}}
              <div class="col-6 col-md-4 about-card" data-aos="fade-up">
                  <div class="why-card">

                      <div class="icon">
                          <i class="bi bi-person-check"></i>
                      </div>

                      <h5>Professionalism</h5>

                      <p class="text-muted mb-0">
                          Skilled installation with attention to detail
                          from consultation to completion.
                      </p>

                  </div>
              </div>


              {{-- Value --}}
              <div class="col-6 col-md-4 about-card" data-aos="fade-up">
                  <div class="why-card">

                      <div class="icon">
                          <i class="bi bi-award"></i>
                      </div>

                      <h5>Best Value</h5>

                      <p class="text-muted mb-0">
                          Smart, affordable protection without
                          compromising on quality.
                      </p>

                  </div>
              </div>

          </div>

      </div>
  </section>

  <style>
    .about-card {
        display: flex;
    }

    .why-card {
        width: 100%;
        height: 100%;
    }


    /* Mobile */
    @media (max-width: 767px) {

        .about-card {
            margin-bottom: 0;
        }

        .why-card {
            padding: 18px 14px;
            min-height: 190px;
        }

        .why-card .icon {
            width: 48px;
            height: 48px;
            margin-bottom: 14px;
        }

        .why-card h5 {
            font-size: 16px;
            margin-bottom: 7px;
        }

        .why-card p {
            font-size: 13px;
            line-height: 1.5;
        }

    }
  </style>

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