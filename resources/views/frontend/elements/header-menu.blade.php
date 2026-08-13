

 <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">  
        <img src="images/Work_home_sefty_solution-header.png" alt="Company Logo" class="me-2" width="100%;" height="50px;">
        <span class="fs-5" style="font-style: poppins, sans-serif;">
          <b>WORK HOME</b>
          <br>
          <b>SAFETY SOLUTION</b>
        </span>
    </a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" data-page="index.php" href="{{ route('home') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link" data-page="services.php" href="{{ route('services') }}">Services</a></li>
        <li class="nav-item"><a class="nav-link" data-page="booking.php" href="{{ route('booking') }}">Booking</a></li>
        <li class="nav-item"><a class="nav-link" data-page="booking.php" href="{{ route('gallery') }}">Gallery</a></li>
        <li class="nav-item"><a class="nav-link" data-page="about.php" href="{{ route('about') }}">About Us</a></li>
        <li class="nav-item"><a class="nav-link" data-page="contact.php" href="{{ route('contact') }}">Contact Us</a></li>
        <li class="nav-item ms-lg-3"><a class="btn btn-orange" href="{{ route('booking') }}"><i class="bi bi-calendar2-check me-1"></i> Book Now</a></li>
        <li class="nav-item ms-lg-3 d-none">
          <a class="btn btn-orange" href="login.php">
            <i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
        </li>
      </ul>
    </div>
  </div>
</nav>