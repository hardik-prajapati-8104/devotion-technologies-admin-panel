
<nav class="mobile-nav d-lg-none">
  <a href="index.html" data-page="index.html"><i class="bi bi-house-door"></i><span>Home</span></a>
  <a href="services.html" data-page="services.html"><i class="bi bi-grid"></i><span>Categories</span></a>
  <a href="booking.html" class="book" data-page="booking.html"><i class="bi bi-calendar2-plus"></i><span>Book</span></a>
  <a href="about.html" data-page="about.html"><i class="bi bi-info-circle"></i><span>About</span></a>
  <a href="setting.html" data-page="setting.html"><i class="bi bi-gear"></i><span>Settings</span></a>
</nav>
  
<footer class="footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4"> 
          <a class="navbar-brand d-flex align-items-center" href="index.php">  
              <img src="images/Work_home_sefty_solution-footer.png" alt="Company Logo" class="me-2" width="70px;" height="70px;">
              <span class="fs-5 text-white" style="font-style: poppins, sans-serif;">
                <b>WORK HOME</b>
                <br>
                <b>SAFETY SOLUTION</b>
              </span>
          </a>
        <p class="mt-3">Premium housekeeping & cleaning services for homes and businesses. Trusted by 10,000+ happy customers.</p>
        <div class="social mt-3">
          <a href="javascript:void();"><i class="bi bi-facebook"></i></a>
          <a href="javascript:void();"><i class="bi bi-instagram"></i></a>
          <a href="javascript:void();"><i class="bi bi-twitter-x"></i></a>
          <a href="javascript:void();"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <h6>Company</h6>
        <ul class="list-unstyled">
          <li><a href="index.html">Home</a></li>
          <li><a href="about.html">About Us</a></li>
          <li><a href="services.html">Services</a></li>
          <li><a href="contact.html">Contact</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-3">
        <h6>Services</h6>
        <ul class="list-unstyled">
          <li><a href="services.html">Deep Cleaning</a></li>
          <li><a href="services.html">Apartment Cleaning</a></li>
          <li><a href="services.html">Commercial Cleaning</a></li>
          <li><a href="services.html">Carpet & Sofa</a></li>
        </ul>
      </div>
      <div class="col-lg-3">
        <h6>Contact</h6>
        <ul class="list-unstyled">
          <li><i class="bi bi-geo-alt text-orange me-2"></i>Dholka, Ahemdabad</li>
          <li><a href="tel:+91 9173307640"><i class="bi bi-telephone text-orange me-2"></i>+91 9173307640</a></li>
          <li><a href="mailto:hardikprajapati8104@gmail.com"><i class="bi bi-envelope text-orange me-2"></i>hardikprajapati8104@gmail.com</a></li>
        </ul>
      </div>
    </div>
    <div class="copyright">© 2026 Work Home Safety Solution. All rights reserved.</div>
  </div>
</footer>

<a href="https://wa.me/919173307640?text=Hello%20Work%20Home%20Safety%20Solution,%0A%0AI%20would%20like%20to%20know%20more%20about%20your%20services.%20Please%20share%20the%20details.%0A%0AThank%20you."
   target="_blank"
   class="whatsapp-float"
   aria-label="Chat on WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>

<style>
    .whatsapp-float{
        position:fixed;
        left:20px;
        bottom:90px; /* above bottom nav on mobile */
        width:60px;
        height:60px;
        background: var(--orange);
        color:#fff;
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:30px;
        text-decoration:none;
        z-index:9999;
        box-shadow:0 8px 25px rgba(255,122,0,.35);
        transition:all .3s ease;
        animation:whatsappPulse 2s infinite;
    }

    .whatsapp-float:hover{
        color:#fff;
        transform:translateY(-5px) scale(1.05);
        box-shadow:0 12px 30px rgba(255,122,0,.45);
    }

    @keyframes whatsappPulse{
        0%{
            box-shadow:0 0 0 0 rgba(255,122,0,.5);
        }
        70%{
            box-shadow:0 0 0 15px rgba(255,122,0,0);
        }
        100%{
            box-shadow:0 0 0 0 rgba(255,122,0,0);
        }
    }

    /* Desktop */
    @media(min-width:768px){
        .whatsapp-float{
            width: 49px;
            height: 49px;
            font-size:32px;
            right:25px;
            bottom:25px;
        }
    }

    /* Mobile */
    @media(max-width:767.98px){
        .whatsapp-float{
            width: 50px;
            height: 50px;
            font-size: 20px;
            right: 15px;
            bottom: 85px; /* keeps clear of bottom navigation */
        }
    }
</style>

<!-- Scroll To Top Button -->
<button id="scrollTopBtn" class="scroll-top-btn">
    <i class="bi bi-arrow-up"></i>
</button>
  
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        offset: 100,
        once: true,
        easing: 'ease-in-out'
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="{{ url('public/frontend/js/main.js') }}"></script>

</body>
</html>
