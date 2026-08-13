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
        <h2 class="section-title" data-aos="fade-up">A Decade of Cleaner, Happier Homes</h2>
        <p class="text-muted" data-aos="fade-up">Founded in 2014, SparkleCare started as a small team of passionate cleaners with a simple mission — make professional housekeeping reliable, affordable and stress-free for everyone.</p>
        <p class="text-muted" data-aos="fade-up">Today we serve thousands of homes and businesses with a network of background-checked, trained, and insured professionals.</p>
        <div class="row g-3 mt-2" data-aos="fade-up">
          <div class="col-4"><div class="stat"><h3>10K+</h3><span>Happy Clients</span></div></div>
          <div class="col-4"><div class="stat"><h3>250+</h3><span>Pros On Team</span></div></div>
          <div class="col-4"><div class="stat"><h3>4.9★</h3><span>Avg Rating</span></div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="bg-light">
  <div class="container">

    <div class="text-center">
      <span class="eyebrow" data-aos="fade-up">Our Values</span>
      <h2 class="section-title" data-aos="fade-up">What Drives Us</h2>
      <p class="section-sub" data-aos="fade-up">The principles behind every visit.</p>
    </div>

    <div class="row">

      <div class="col-md-4 about-card" data-aos="fade-up">
        <div class="why-card">
          <div class="icon">
            <i class="bi bi-heart"></i>
          </div>
          <h5>Care</h5>
          <p class="text-muted mb-0">We treat every home like our own.</p>
        </div>
      </div>

      <style>
        @media(max-width:767px){
          .about-card{
            margin-bottom: 10px;
          }
          .eyebrow{
            margin-top: 10px;
          }
        }
      </style>

      <div class="col-md-4 about-card" data-aos="fade-up">
        <div class="why-card">
          <div class="icon">
            <i class="bi bi-patch-check"></i>
          </div>
          <h5>Quality</h5>
          <p class="text-muted mb-0">Consistent results, visit after visit.</p>
        </div>
      </div>

      <div class="col-md-4" data-aos="fade-up">
        <div class="why-card">
          <div class="icon">
            <i class="bi bi-people"></i>
          </div>
          <h5>Respect</h5>
          <p class="text-muted mb-0">Fair pay and a safe workplace for our team.</p>
        </div>
      </div>

    </div>
  </div>
</section>

 @endsection