<?php
	include_once ('elements/header.php'); 
?>
 

<!-- header section -->
<section class="page-header">
    <div class="overlay"></div>

    <div class="container position-relative"> 
        <h1 data-aos="fade-up">Our Services</h1>

        <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
            <li class="breadcrumb-item">
                <a href="index.html">
                    <i class="bi bi-house-door-fill me-1"></i> Home
                </a>
            </li>

            <li class="breadcrumb-item active">
                Our Services
            </li>
        </ol>
    </div>
</section>

<style>
      
    .services-filter-section{
        background:#fff;
    }

    .services-filter-wrapper{
        display:flex;
        gap:15px;
        overflow-x:auto;
        scrollbar-width:none;
        -ms-overflow-style:none;
        padding-bottom:5px;
    }

    .services-filter-wrapper::-webkit-scrollbar{
        display:none;
    }

    .service-filter{
        min-width:max-content;
        display:flex;
        align-items:center;
        gap:10px;
        padding:14px 22px;
        background:#fff;
        border:1px solid #eee;
        border-radius:50px;
        cursor:pointer;
        transition:.3s;
        white-space:nowrap;
        font-weight:500;
    }

    .service-filter i{
        color:var(--orange);
        font-size:18px;
    }

    .service-filter:hover,
    .service-filter.active{
        background:var(--orange);
        border-color:var(--orange);
        color:#fff;
        /* box-shadow:0 8px 20px rgba(255,122,0,.25); */
    }

    .service-filter:hover i,
    .service-filter.active i{
        color:#fff;
    }

    @media(max-width:767px){

        section{
          padding: 20px 0 !important;
        }

        .services-filter-wrapper{
            gap:10px;
        }

        .service-filter{
            padding:12px 18px;
            font-size:14px;
        }

        .service-filter i{
            font-size:16px;
        }
    }

</style>

<!-- Services Search Bar -->
<section class="services-filter-section py-4">
    <div class="container">

        <div class="services-filter-wrapper">

            <div class="service-filter active" data-aos="fade-up">
                <i class="bi bi-trophy"></i>
                <span>Deep Cleaning</span>
            </div>

            <div class="service-filter" data-aos="fade-up">
                <i class="bi bi-trophy"></i>
                <span>Apartment Cleaning</span>
            </div>

            <div class="service-filter" data-aos="fade-up">
                <i class="bi bi-trophy"></i>
                <span>Commercial Cleaning</span>
            </div>

            <div class="service-filter" data-aos="fade-up">
                <i class="bi bi-trophy"></i>
                <span>Carpet & Sofa</span>
            </div>

            <div class="service-filter" data-aos="fade-up">
                <i class="bi bi-trophy"></i>
                <span>Sports & Cricket Netting</span>
            </div>

            <div class="service-filter" data-aos="fade-up">
                <i class="bi bi-shield-check"></i>
                <span>Bird Spikes</span>
            </div>

            <div class="service-filter" data-aos="fade-up">
                <i class="bi bi-grid-3x3-gap"></i>
                <span>Bird Netting</span>
            </div>

            <div class="service-filter" data-aos="fade-up">
                <i class="bi bi-bug"></i>
                <span>Mosquito Protection</span>
            </div>

            <div class="service-filter" data-aos="fade-up">
                <i class="bi bi-border-all"></i>
                <span>Invisible Grills</span>
            </div>

        </div>

    </div>
</section>

<section>
  <div class="container">

    <div class="text-center">
        <span class="eyebrow" data-aos="fade-up">What We Offer</span>
        <h2 class="section-title" data-aos="fade-up">Cleaning Services for Every Space</h2>
        <p class="section-sub" data-aos="fade-up">Tailored packages for homes, apartments, offices and more.</p>
    </div>

    <div class="row g-4">

      <div class="col-md-6 col-lg-4" data-aos="fade-up">
        <div class="service-card">
          <div class="img" style="background-image:url(images/s1.jpg)"></div>
          <div class="body">
            <h5>Deep Cleaning</h5>
            <p class="text-muted">A complete top-to-bottom clean for every room — perfect for first-time visits or seasonal resets.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="price">From $89</span>
              <a href="booking.html" class="btn btn-sm btn-orange">Book</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4" data-aos="fade-up">
        <div class="service-card">
          <div class="img" style="background-image:url(images/s2.jpg)"></div>
          <div class="body">
            <h5>Apartment Cleaning</h5>
            <p class="text-muted">Recurring weekly or bi-weekly cleans that keep your apartment fresh and welcoming.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="price">From $59</span>
              <a href="booking.html" class="btn btn-sm btn-orange">Book</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4" data-aos="fade-up">
        <div class="service-card">
          <div class="img" style="background-image:url(images/s3.jpg)"></div>
          <div class="body">
            <h5>Commercial Cleaning</h5>
            <p class="text-muted">Offices, retail, gyms and clinics — flexible scheduling and trained crews.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="price">From $149</span>
              <a href="booking.html" class="btn btn-sm btn-orange">Book</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4" data-aos="fade-up">
        <div class="service-card">
          <div class="img" style="background-image:url(images/s4.jpg)"></div>
          <div class="body">
            <h5>Carpet & Sofa Cleaning</h5>
            <p class="text-muted">Professional steam-cleaning to remove stains, allergens and odors.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="price">From $79</span>
              <a href="booking.html" class="btn btn-sm btn-orange">Book</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4" data-aos="fade-up">
        <div class="service-card">
          <div class="img" style="background-image:url(images/s5.jpg)"></div>
          <div class="body">
            <h5>Window Cleaning</h5>
            <p class="text-muted">Streak-free windows inside and out, from townhouses to high-rises.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="price">From $69</span>
              <a href="booking.html" class="btn btn-sm btn-orange">Book</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4" data-aos="fade-up">
        <div class="service-card">
          <div class="img" style="background-image:url(images/s6.jpg)"></div>
          <div class="body">
            <h5>Move In / Move Out</h5> 
            <p class="text-muted">A landlord-approved checklist clean that helps you get your deposit back.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="price">From $129</span>
              <a href="booking.html" class="btn btn-sm btn-orange">Book</a>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>

<?php
  include_once ('elements/footer.php');
?>
