
@extends('frontend.layouts.app')
  
@section('content')


<style>
    .swiper-pagination-bullet-active { 
        background: var(--orange);
}
</style>

@include('frontend.elements.home-slider')

<section class="about-home py-5">
    <div class="container">

        <div class="row align-items-center g-4">

            <!-- Image -->
            <div class="col-lg-6" data-aos="fade-right">

                <div class="about-img-wrapper">

                    <img src="{{ url('public/frontend/images/about-img1.jpg') }}" alt="About Us" class="img-fluid about-img" loading="lazy">

                    <div class="experience-box">
                        <h3>10+</h3>
                        <p>Years Experience</p>
                    </div>

                </div>

            </div>

            <!-- Content -->
            <div class="col-lg-6" data-aos="fade-left">

                <span class="about-badge">
                    About Us
                </span>

                <h2 class="about-title">
                    Trusted Safety & Protection Solutions For Every Space
                </h2>

                <p class="about-text">
                    Work Home Safety Solution provides premium safety systems,
                    bird control netting, anti-bird spikes, invisible grills,
                    mosquito protection systems, and professional cleaning
                    services for residential and commercial properties.
                </p>

                <div class="row g-3 mt-3">

                    <div class="col-sm-6">
                        <div class="feature-box">
                            <i class="bi bi-check-circle-fill"></i>
                            Expert Installation
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="feature-box">
                            <i class="bi bi-check-circle-fill"></i>
                            Premium Quality
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="feature-box">
                            <i class="bi bi-check-circle-fill"></i>
                            Affordable Pricing
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="feature-box">
                            <i class="bi bi-check-circle-fill"></i>
                            Customer Support
                        </div>
                    </div>

                </div>

                <div class="about-stats">

                    <div class="stat-item">
                        <h4>500+</h4>
                        <span>Projects</span>
                    </div>

                    <div class="stat-item">
                        <h4>250+</h4>
                        <span>Clients</span>
                    </div>

                    <div class="stat-item">
                        <h4>100%</h4>
                        <span>Satisfaction</span>
                    </div>

                </div>

                <a href="about.html" class="btn btn-orange mt-4">
                    Learn More
                    <i class="bi bi-arrow-right ms-2"></i>
                </a>

            </div>

        </div>

    </div>
</section>

<style>
    
    /* Prevent Horizontal Scroll */
    /* body{
        overflow-x:hidden;
    } */

    .about-home{
        background:#fff;
        overflow:hidden;
    }

    .about-badge{
        display:inline-block;
        padding:8px 18px;
        background:var(--orange-soft);
        color:var(--orange);
        border-radius:50px;
        font-weight:600;
        margin-bottom:15px;
    }

    .about-title{
        font-size:clamp(2rem,4vw,3rem);
        font-weight:800;
        color:#222;
        margin-bottom:15px;
    }

    .about-text{
        color:#666;
        line-height:1.8;
    }

    .about-img-wrapper{
        position:relative;
    }

    .about-img{
        width:100%;
        border-radius:20px;
        display:block;
        box-shadow:0 15px 40px rgba(0,0,0,.08);
    }

    .experience-box{
        position:absolute;
        left:20px;
        bottom:20px;
        background:#fff;
        padding:15px 20px;
        border-radius:15px;
        box-shadow:0 10px 25px rgba(0,0,0,.12);
    }

    .experience-box h3{
        margin:0;
        color:var(--orange);
        font-weight:800;
    }

    .experience-box p{
        margin:0;
        font-size:14px;
    }

    .feature-box{
        background:#fff8f2;
        border-radius:12px;
        padding:14px;
        font-weight:600;
        transition:.3s;
    }

    .feature-box i{
        color:var(--orange);
        margin-right:8px;
    }

    .feature-box:hover{
        background:var(--orange);
        color:#fff;
        transform:translateY(-4px);
    }

    .feature-box:hover i{
        color:#fff;
    }

    .about-stats{
        display:flex;
        gap:25px;
        flex-wrap:wrap;
        margin-top:25px;
    }

    .stat-item h4{
        color:var(--orange);
        font-weight:800;
        margin-bottom:4px;
    }

    .stat-item span{
        color:#666;
        font-size:14px;
    }

    .btn-orange{
        background:var(--orange);
        color:#fff;
        border-radius:50px;
        padding:12px 28px;
        font-weight:600;
        border:none;
    }

    .btn-orange:hover{
        background:#e66d00;
        color:#fff;
    }

    /* Mobile */
    @media (max-width:767px){

        .about-home .container{
            padding-left:15px;
            padding-right:15px;
        }

        .about-title{
            font-size:2rem;
        }

        .experience-box{
            left:10px;
            bottom:10px;
            padding:10px 15px;
        }

        .about-stats{
            justify-content:space-between;
            gap:15px;
        }

        .stat-item{
            text-align:center;
            flex:1;
        }
    }
</style>
 
<!-- CATEGORIES -->
<section class="category-sec d-none">
  <div class="container">
    <div class="text-center">
      <span class="eyebrow">Categories</span>
      <h2 class="section-title">Browse by Service Type</h2>
      <p class="section-sub">Pick the service that fits your need — every booking includes vetted pros and supplies.</p>
    </div>
    <div class="row g-3">
      <div class="col-6 col-md-3 col-lg-2"><div class="cat-tile"><i class="bi bi-house-heart"></i><h6>Home</h6></div></div>
      <div class="col-6 col-md-3 col-lg-2"><div class="cat-tile"><i class="bi bi-building"></i><h6>Office</h6></div></div>
      <div class="col-6 col-md-3 col-lg-2"><div class="cat-tile"><i class="bi bi-droplet-half"></i><h6>Deep Clean</h6></div></div>
      <div class="col-6 col-md-3 col-lg-2"><div class="cat-tile"><i class="bi bi-window"></i><h6>Windows</h6></div></div>
      <div class="col-6 col-md-3 col-lg-2"><div class="cat-tile"><i class="bi bi-flower1"></i><h6>Carpet</h6></div></div>
      <div class="col-6 col-md-3 col-lg-2"><div class="cat-tile"><i class="bi bi-truck"></i><h6>Move In/Out</h6></div></div>
    </div>
  </div>
</section>

<!-- service section start-->
<section class="category-section py-5">
    <div class="container">

        <div class="text-center mb-5">
            <span class="category-badge" data-aos="fade-up">Our Services</span>
            <h2 class="section-title-services" data-aos="fade-up">
                Explore Our Professional Solutions
            </h2>
            <p class="section-subtitle-sevices" data-aos="fade-up">
                Premium safety, protection and cleaning services for homes,
                offices and commercial spaces.
            </p>
        </div>

        <div class="row g-4" data-aos="fade-up">

            <div class="col-6 col-md-4 col-lg-3">
                <div class="service-category-card active">
                    <div class="service-icon">
                        <i class="bi bi-trophy"></i>
                    </div> 
                    <h6>Invisible Grills</h6>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="service-category-card">
                    <div class="service-icon">
                        <i class="bi bi-shield-check"></i>
                    </div> 
                    <h6>Bird Netting</h6>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="service-category-card">
                    <div class="service-icon">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </div>
                    <h6>Mosquito Protection</h6> 
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="service-category-card">
                    <div class="service-icon">
                        <i class="bi bi-bug"></i>
                    </div>
                    <h6>Bird Spikes</h6>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="service-category-card">
                    <div class="service-icon">
                        <i class="bi bi-border-all"></i>
                    </div> 
                    <h6>Sports & Cricket Netting</h6>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="service-category-card">
                    <div class="service-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <h6>Balcony Safety Nets</h6>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="service-category-card">
                    <div class="service-icon">
                        <i class="bi bi-stars"></i>
                    </div>
                    <h6>Sliding Mosquito Nets</h6>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="service-category-card">
                    <div class="service-icon">
                        <i class="bi bi-house-heart"></i>
                    </div>
                    <h6>Stainless Steel Invisible Grills</h6>
                </div>
            </div>

        </div>

    </div>
</section>

<style>
  .category-section{
    background:#fff;
  }

  .category-badge{
      display:inline-block;
      padding:8px 18px;
      border-radius:50px;
      background:var(--orange-soft);
      color:var(--orange);
      font-weight:600;
      margin-bottom:15px;
  }

  .section-title-services{
      font-size:clamp(2rem,4vw,3rem);
      font-weight:800;
      color:#222;
  }

  .section-subtitle-services{
      color:#777;
      max-width:650px;
      margin:auto;
  }

  .service-category-card{
      position:relative;
      background:#fff;
      border-radius:20px;
      padding:30px 20px;
      text-align:center;
      cursor:pointer;
      overflow:hidden;
      transition:.4s ease;
      border:1px solid #f2f2f2;
      box-shadow:0 10px 30px rgba(0,0,0,.06);
      height:100%;
  }

  .service-category-card::before{
      content:'';
      position:absolute;
      left:0;
      top:0;
      width:100%;
      height:4px;
      background:var(--orange);
      transform:scaleX(0);
      transition:.4s;
  }

  .service-category-card:hover::before{
      transform:scaleX(1);
  }

  .service-category-card:hover{
      transform:translateY(-10px);
      box-shadow:0 20px 40px rgba(255,122,0,.18);
  }

  .service-icon{
      width:80px;
      height:80px;
      margin:auto;
      margin-bottom:18px;
      border-radius:50%;
      background:var(--orange-soft);
      display:flex;
      align-items:center;
      justify-content:center;
      transition:.4s;
  }

  .service-icon i{
      font-size:32px;
      color:var(--orange);
  }

  .service-category-card:hover .service-icon{
      background:var(--orange);
      transform:rotateY(180deg);
  }

  .service-category-card:hover .service-icon i{
      color:#fff;
      transform:rotateY(-180deg);
  }

  .service-category-card h6{
      font-size:16px;
      font-weight:700;
      margin:0;
      line-height:1.5;
  }
  
  .active .service-icon i{
      color:var(--orange);
  }

  @media(max-width:767px){

      .service-category-card{
          padding:20px 15px;
          border-radius:16px;
      }

      .service-icon{
          width:60px;
          height:60px;
      }

      .service-icon i{
          font-size:24px;
      }

      .service-category-card h6{
          font-size:13px;
      }
  }
</style>
<!-- service section end -->
 
<!-- POPULAR SERVICES SWIPER -->
<section class="bg-light">
  <div class="container">
    <div class="text-center">
      <span class="eyebrow" data-aos="fade-up">Popular Services</span>
      <h2 class="section-title" data-aos="fade-up">Most Booked This Month</h2>
      <p class="section-sub" data-aos="fade-up">Trusted favorites — chosen by thousands of customers.</p>
    </div>
    <div class="swiper services-swiper" data-aos="fade-up">
      <div class="swiper-wrapper pb-5">


        <div class="swiper-slide">
            <div class="service-card">
                <div class="img" style="background-image:url(public/frontend/images/s1.jpg)"></div>
                <div class="body">
                    <h5>Deep Cleaning</h5>
                    <p class="text-muted small">Top-to-bottom intensive clean for every room.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price">From $89</span>
                        <a href="booking.html" class="btn btn-sm btn-outline-orange">Book</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper-slide">
            <div class="service-card">
                <div class="img" style="background-image:url(public/frontend/images/s2.jpg)"></div>
                <div class="body">
                    <h5>Apartment Cleaning</h5>
                    <p class="text-muted small">Regular weekly or bi-weekly tidy and clean.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price">From $59</span>
                        <a href="booking.html" class="btn btn-sm btn-outline-orange">Book</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper-slide">
            <div class="service-card">
                <div class="img" style="background-image:url(public/frontend/images/s3.jpg)"></div>
                <div class="body">
                    <h5>Commercial Cleaning</h5>
                    <p class="text-muted small">Offices, shops and facilities, on your schedule.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price">From $149</span>
                        <a href="booking.html" class="btn btn-sm btn-outline-orange">Book</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper-slide">
            <div class="service-card">
                <div class="img" style="background-image:url(public/frontend/images/s4.jpg)"></div>
                <div class="body">
                    <h5>Carpet & Sofa</h5>
                    <p class="text-muted small">Steam-cleaning that lifts deep stains & odors.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price">From $79</span>
                        <a href="booking.html" class="btn btn-sm btn-outline-orange">Book</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper-slide">
            <div class="service-card">
                <div class="img" style="background-image:url(public/frontend/images/s5.jpg)">
                </div>
                <div class="body">
                    <h5>Window Cleaning</h5>
                    <p class="text-muted small">Crystal-clear interior and exterior windows.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price">From $69</span>
                        <a href="booking.html" class="btn btn-sm btn-outline-orange">Book</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper-slide">
            <div class="service-card">
                <div class="img" style="background-image:url(public/frontend/images/s6.jpg)"></div>
                <div class="body">
                    <h5>Move In / Move Out</h5>
                    <p class="text-muted small">Get your security deposit back, guaranteed.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price">From $129</span>
                        <a href="booking.html" class="btn btn-sm btn-outline-orange">Book</a>
                    </div>
                </div>
            </div>
        </div>

      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>

<!-- WHY US -->
<section class="d-none">
  <div class="container">
    <div class="text-center">
      <span class="eyebrow" data-aos="fade-up">Why Work Home Company</span>
      <h2 class="section-title" data-aos="fade-up">Built on Trust & Quality</h2>
      <p class="section-sub" data-aos="fade-up">Background-checked pros, transparent pricing, and a happiness guarantee.</p>
    </div>

    <div class="row g-4" data-aos="fade-up">

        <div class="col-md-6 col-lg-3">
            <div class="why-card">
                <div class="icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h5>Vetted Pros</h5>
                <p class="text-muted mb-0">Every cleaner is interviewed, trained and background-checked.</p>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="why-card">
                <div class="icon">
                    <i class="bi bi-tree-fill"></i>
                </div>
                <h5>Eco Products</h5>
                <p class="text-muted mb-0">Non-toxic, family-safe supplies brought to every visit.</p>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
        <div class="why-card">
            <div class="icon">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <h5>Flat Pricing</h5>
            <p class="text-muted mb-0">No surprises. See the price before you book.</p>
        </div>
        </div>

        <div class="col-md-6 col-lg-3">
        <div class="why-card">
            <div class="icon">
                <i class="bi bi-emoji-smile"></i>
            </div>
            <h5>100% Guarantee</h5>
            <p class="text-muted mb-0">Not happy? We will re-clean for free.</p>
        </div>
        </div>

    </div>

  </div>
</section>

<!-- Why Choose Us Section Starts -->
<section class="why-choose-us py-5">
    <div class="container">

        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge">Why Choose Us</span>
            <h2 class="section-title">
                Trusted Safety Solutions With Professional Excellence
            </h2>
            <p class="section-subtitle">
                We provide reliable protection systems and quality services
                designed to keep your home and workplace safe.
            </p>
        </div>

        <div class="row g-4">

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="choose-card">
                    <div class="choose-icon">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h5>Certified Experts</h5>
                    <p>
                        Experienced professionals delivering high-quality
                        installations and services.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="choose-card">
                    <div class="choose-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5>Premium Protection</h5>
                    <p>
                        Durable materials and advanced safety solutions
                        for long-term reliability.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="choose-card">
                    <div class="choose-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <h5>Affordable Pricing</h5>
                    <p>
                        Competitive pricing with no compromise on quality
                        and customer satisfaction.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
                <div class="choose-card">
                    <div class="choose-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h5>Fast Service</h5>
                    <p>
                        Quick response and timely project completion
                        with professional support.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
                <div class="choose-card">
                    <div class="choose-icon">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h5>24/7 Support</h5>
                    <p>
                        Dedicated customer assistance whenever you need
                        help or guidance.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="600">
                <div class="choose-card">
                    <div class="choose-icon">
                        <i class="bi bi-emoji-smile-fill"></i>
                    </div>
                    <h5>Customer Satisfaction</h5>
                    <p>
                        Hundreds of happy clients trust us for safety,
                        security and cleaning solutions.
                    </p>
                </div>
            </div>

        </div>

        <!-- Statistics -->
        <div class="row text-center mt-5 g-4">

            <div class="col-6 col-lg-3">
                <div class="stat-box">
                    <h3>500+</h3>
                    <span>Projects Completed</span>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stat-box">
                    <h3>250+</h3>
                    <span>Happy Clients</span>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stat-box">
                    <h3>10+</h3>
                    <span>Years Experience</span>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stat-box">
                    <h3>100%</h3>
                    <span>Satisfaction</span>
                </div>
            </div>

        </div>

    </div>
</section>

<style>
    
    .why-choose-us{
        background:#fffaf5;
        overflow:hidden;
    }

    .section-badge{
        display:inline-block;
        background:var(--orange-soft);
        color:var(--orange);
        padding:8px 18px;
        border-radius:50px;
        font-weight:600;
        margin-bottom:15px;
    }

    .section-title{
        font-size:clamp(2rem,4vw,3rem);
        font-weight:800;
        color:#222;
    }

    .section-subtitle{
        max-width:700px;
        margin:auto;
        color:#666;
    }

    .choose-card{
        background:#fff;
        border-radius:20px;
        padding:30px 25px;
        text-align:center;
        height:100%;
        transition:.4s;
        box-shadow:0 10px 30px rgba(0,0,0,.06);
        border:1px solid #f5f5f5;
    }

    .choose-card:hover{
        transform:translateY(-10px);
        box-shadow:0 20px 40px rgba(255,122,0,.15);
    }

    .choose-icon{
        width:80px;
        height:80px;
        margin:0 auto 20px;
        border-radius:50%;
        background:var(--orange-soft);
        display:flex;
        align-items:center;
        justify-content:center;
        transition:.4s;
    }

    .choose-icon i{
        font-size:32px;
        color:var(--orange);
    }

    .choose-card:hover .choose-icon{
        background:var(--orange);
    }

    .choose-card:hover .choose-icon i{
        color:#fff;
    }

    .choose-card h5{
        font-weight:700;
        margin-bottom:15px;
    }

    .choose-card p{
        color:#666;
        margin:0;
    }

    .stat-box{
        background:#fff;
        border-radius:18px;
        padding:25px;
        box-shadow:0 10px 25px rgba(0,0,0,.05);
    }

    .stat-box h3{
        color:var(--orange);
        font-size:2rem;
        font-weight:800;
        margin-bottom:5px;
    }

    .stat-box span{
        color:#666;
    }

    /* Mobile */
    @media(max-width:767px){

        .choose-card{
            padding:25px 20px;
        }

        .choose-icon{
            width:65px;
            height:65px;
        }

        .choose-icon i{
            font-size:26px;
        }

        .stat-box{
            padding:20px;
        }

        .stat-box h3{
            font-size:1.6rem;
        }
    }
</style>
<!-- why Choose Us Section End -->

<!-- TESTIMONIALS -->
<section class="bg-light">
  <div class="container">
    <div class="text-center">
      <span class="eyebrow" data-aos="fade-up">Testimonials</span>
      <h2 class="section-title" data-aos="fade-up">What Customers Say</h2>
      <p class="section-sub" data-aos="fade-up">Real reviews from real homes and businesses.</p>
    </div>

    <div class="swiper testi-swiper" data-aos="fade-up">
      <div class="swiper-wrapper pb-5">

        <div class="swiper-slide">
            <div class="testimonial">
                <div class="stars">★★★★★</div>
                <p>"Booked a deep clean — they were on time, friendly and my apartment smells amazing. Booking again!"</p>
                <div class="who">
                    <div class="avatar">EM</div>
                    <div>
                        <strong>Emma M.</strong>
                        <div class="small text-muted">Brooklyn, NY</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper-slide">
            <div class="testimonial">
                <div class="stars">★★★★★</div>
                <p>"We use SparkleCare for our office every week. Professional, reliable, and great communication."</p>
                <div class="who">
                    <div class="avatar">JR</div>
                    <div>
                        <strong>James R.</strong>
                        <div class="small text-muted">Office Manager</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper-slide">
            <div class="testimonial">
                <div class="stars">★★★★★</div>
                <p>"Got my full security deposit back thanks to the move-out clean. Worth every penny."</p>
                <div class="who">
                    <div class="avatar">LP</div>
                    <div>
                        <strong>Lara P.</strong>
                        <div class="small text-muted">Queens, NY</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper-slide">
            <div class="testimonial">
                <div class="stars">★★★★★</div>
                <p>"Easy app-like booking and the cleaner was fantastic. Highly recommend SparkleCare."</p>
                <div class="who">
                    <div class="avatar">SO</div>
                    <div>
                        <strong>Sam O.</strong>
                        <div class="small text-muted">Manhattan</div>
                    </div>
                </div>
            </div>
        </div>

      </div>
      <div class="swiper-pagination"></div>
    </div>
    
  </div>
</section>

<!-- OUR CLIENTS -->
<section class="clients-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge" data-aos="fade-up">Trusted Partners</span>
            <h2 class="section-title" data-aos="fade-up">Our Clients</h2>
            <p class="section-desc" data-aos="fade-up">
                Proudly serving businesses and organizations with reliable cleaning solutions.
            </p>
        </div>

        <div class="swiper clientsSwiper" >
            <div class="swiper-wrapper py-5">

                <div class="swiper-slide" data-aos="fade-up">
                    <div class="client-card">
                        <img src="public/frontend/images/clients/binghati.png" alt="Client">
                    </div>
                </div>

                <div class="swiper-slide" data-aos="fade-up">
                    <div class="client-card">
                        <img src="public/frontend/images/clients/bnw.png" alt="Client">
                    </div>
                </div>

                <div class="swiper-slide" data-aos="fade-up">
                    <div class="client-card">
                        <img src="public/frontend/images/clients/damac.png" alt="Client">
                    </div>
                </div>

                <div class="swiper-slide" data-aos="fade-up">
                    <div class="client-card">
                        <img src="public/frontend/images/clients/danube.png" alt="Client">
                    </div>
                </div>

                <div class="swiper-slide" data-aos="fade-up">
                    <div class="client-card">
                        <img src="public/frontend/images/clients/dubai.png" alt="Client">
                    </div>
                </div>

                <div class="swiper-slide" data-aos="fade-up">
                    <div class="client-card">
                        <img src="public/frontend/images/clients/Meraas.png" alt="Client">
                    </div>
                </div>

            </div>

            <div class="swiper-pagination mt-4"></div>
        </div>
    </div>
</section>

<!-- CTA -->
<section>
  <div class="container">
    <div class="cta" data-aos="fade-up">
      <h2>Ready for a Sparkling Clean Space?</h2>
      <p class="mb-4" data-aos="fade-up">Get a free instant quote in under a minute.</p>
      <a href="booking.html" class="btn btn-light btn-lg fw-bold text-orange" data-aos="fade-up"><i class="bi bi-calendar2-check me-2"></i>Enquire Now</a>
    </div>
  </div>
</section>

 @endsection