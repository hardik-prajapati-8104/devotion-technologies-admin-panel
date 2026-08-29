@extends('frontend.layouts.app')
 
@section('content')
 
    <style> 
            /* Hide on Desktop */
            @media(min-width:768px){
                .mobile-settings{
                    display:none;
                }
            }

            .settings-header{
                padding:20px;
                background:#fff;
                border-bottom:1px solid #eee;
            }

            .profile-card{
                background:#fff;
                border-radius:18px;
                padding:20px;
                margin:15px;
                box-shadow:0 5px 20px rgba(0,0,0,.05);
            }

            .profile-avatar{
                width:65px;
                height:65px;
                background:rgba(255,122,0,.1);
                color:var(--orange);
                border-radius:50%;
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:28px;
            }

            .menu-card{
                background:#fff;
                border-radius:18px;
                margin:15px;
                overflow:hidden;
                box-shadow:0 5px 20px rgba(0,0,0,.05);
            }

            .menu-item{
                display:flex;
                align-items:center;
                justify-content:space-between;
                padding:16px 20px;
                text-decoration:none;
                color:#222;
                border-bottom:1px solid #f3f3f3;
            }

            .menu-item:last-child{
                border-bottom:none;
            }

            .menu-item:hover{
                background:#fff7f0;
            }

            .icon-box{
                width:38px;
                height:38px;
                border-radius:10px;
                background:rgba(255,122,0,.12);
                color:var(--orange);
                display:flex;
                align-items:center;
                justify-content:center;
                margin-right:12px;
            }

            .call-card{
                background:linear-gradient(135deg,#ff7a00,#ff9d32);
                color:#fff;
                border-radius:18px;
                padding:25px;
                margin:15px;
            }

            .call-btn{
                background:#fff;
                color:var(--orange);
                font-weight:600;
                border:none;
                width:100%;
                font-size: 14px;
                border-radius:12px;
                padding:12px;
            }

            .section-title{
                font-weight:600;
                font-size:20px;
            }

            .btn:hover 
            {
                color: var(--orange);
                background-color: #fff;
                border-color: var(--orange);
            }
    </style>

    <div class="mobile-settings"> 
      
        <!-- Header -->
        <div class="settings-header" data-aos="fade-up">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 section-title">Settings</h4>
                <a href="{{ route('home') }}" class="text-dark">
                    <i class="bi bi-x-lg fs-4"></i>
                </a>
            </div>
        </div>

          <style>
            .company-brand{
                text-align:center;
                padding:25px 15px;
                background:linear-gradient(135deg,#ff7a00,#ff9a2f); 
                margin-bottom:20px;
            }

            .company-brand img{
                width:85px;
                height:85px;
                border-radius:50%;
                background:#fff;
                padding:8px;
            }

            .company-brand h4{
                color:#fff;
                font-size:18px;
                font-weight:700;
                margin-top:12px;
            }

            .company-brand p{
                color:rgba(255,255,255,.85);
                font-size:12px;
            }
        </style>
      
        <!-- Company Brand -->
        
        {{-- <div class="company-brand" data-aos="fade-up">
            <img src="{{ url('public/frontend/images/clients/dubai.png') }}" alt="Work Home Safety Solution">

            <h4>WORK HOME SAFETY SOLUTION</h4>

            <p>
                Invisible Safety Grills • Premium Mosquito Protection Systems • Bird Control Netting Solutions • Anti-Bird Spike Protection Systems • Professional Sports & Cricket Netting Solutions   
            </p>
        </div> --}}

        <div class="company-brand" data-aos="fade-up">
            @if($configurations['site_favicon'] ?? null)
                <img src="{{ url('storage/app/public/' . $configurations['site_favicon']) }}" alt="{{ $configurations['site_name'] ?? 'Work Home Safety Solution' }}" width="auto;" height="auto;">
            @else
                <img src="{{ url('public/frontend/images/clients/dubai.png') }}" alt="Work Home Safety Solution">
            @endif

            <h4>{{ $configurations['site_name'] ?? 'WORK HOME SAFETY SOLUTION' }}</h4>

            <p>
                {{ $configurations['site_tagline'] ?? 'Invisible Safety Grills • Premium Mosquito Protection Systems • Bird Control Netting Solutions • Anti-Bird Spike Protection Systems • Professional Sports & Cricket Netting Solutions' }}
            </p>
        </div>

        <!-- User Info -->
        <div class="profile-card d-none" data-aos="fade-up">
            <div class="d-flex align-items-center">
                <div class="profile-avatar">
                    <i class="bi bi-person"></i>
                </div>

                <div class="ms-3">
                    <div><strong>User Login Id:</strong> USER001</div>
                    <div><strong>Username:</strong> Hardik</div>
                </div>
            </div>
        </div>

        <!-- Settings Menu -->
        <div class="menu-card" data-aos="fade-up">

            <a href="javascript:void();" class="menu-item d-none">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-box-arrow-in-right me-1"></i>
                    </div>
                    Login / Signup
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>


            <a href="{{ route('about') }}" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-info-circle"></i>
                    </div>
                    About Us
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('brochure') }}" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-file-earmark-arrow-down-fill"></i>
                    </div>
                    Our Brochure
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('reviews') }}" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    Clients Reviews
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('gallery') }}" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-image-fill"></i>
                    </div>
                    Gallery
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('services') }}" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    Services
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('booking') }}" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    Booking
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('contact') }}" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-envelope"></i>
                    </div>
                    Contact Us
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('privacy-policy') }}" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    Privacy Policy
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('clients') }}" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                    <i class="bi bi-people-fill"></i>
                    </div>
                    Our Clients
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('faqs') }}" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    Faqs
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('terms-condition') }}" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    Terms & Conditions
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="{{ route('cookie-policy') }}" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-cookie"></i>
                    </div>
                    Cookie Policy
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

        </div>

        <!-- Call Now Card -->
        <div class="call-card" data-aos="fade-up">
            <h5>Need Immediate Assistance?</h5>
            <p class="mb-3">
                Our cleaning experts are available to help you book or answer your questions.
            </p>

            @if($configurations['contact_phone'] ?? null)
            <a href="tel: {{ $configurations['contact_phone'] }}" class="btn call-btn">
                <i class="bi bi-telephone-fill me-2"></i>
                Call Now
            </a>
            @endif

             @if($configurations['contact_email'] ?? null)
                <a href="mailto:{{ $configurations['contact_email'] }}" class="btn call-btn mt-2">
                    <i class="bi bi-envelope-fill me-2"></i>
                    {!! $configurations['contact_email'] !!}
                </a>
            @endif
        </div>

    </div>

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
            margin: 0px 10px 70px 10px;
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