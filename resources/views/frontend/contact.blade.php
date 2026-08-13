<?php
	include_once ('elements/header.php'); 
?>


<!-- header section -->
<section class="page-header">
    <div class="overlay"></div>

    <div class="container position-relative"> 
        <h1 data-aos="fade-up">Contact Us</h1>

        <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
            <li class="breadcrumb-item" data-aos="fade-up">
                <a href="index.html">
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
            <p class="text-muted mb-0">22 Maple Street, Suite 4B<br>New York, NY 10001</p>
        </div> 
      </div>

      <div class="col-md-4" data-aos="fade-up">
        <div class="contact-card">
          <div class="icon">
            <i class="bi bi-telephone"></i>
          </div>
          <h5>Call Us</h5>
          <p class="text-muted mb-0">+91 9173307640<br>Mon-Sat, 8am-8pm</p>
        </div>
      </div>

      <div class="col-md-4" data-aos="fade-up">
        <div class="contact-card">
          <div class="icon">
            <i class="bi bi-envelope"></i>
          </div>
          <h5>Email Us</h5>
          <p class="text-muted mb-0">hardikprajapati8104@gmail.com<br>support@hardikprajapati.com</p>
        </div>
      </div>
    </div>

    <div class="row g-4">

      <div class="col-lg-7" data-aos="fade-right">

        <div class="booking-card">
          <h3 class="fw-bold mb-3">Send a Message</h3> 
          <div id="contactSuccess" class="alert alert-success d-none"><i class="bi bi-check-circle me-2"></i>Thanks! We will get back to you soon.</div> 
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

              <div class="col-12"><button class="btn btn-orange px-4 py-2 fw-bold">Send Message</button></div>

            </form> 
        </div>

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
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29438.37726484894!2d72.44420714999998!3d22.73577955!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395eec89561c1877%3A0x6d8624727e1c7d31!2sDholka%2C%20Gujarat%20382225!5e0!3m2!1sen!2sin!4v1781870164532!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>

      </div>

    </div>
    
  </div>
</section>

<?php
  include_once ('elements/footer.php');
?>
