<?php
	include_once ('elements/header.php');  
?>
 
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
                <a href="index.html" class="text-dark">
                    <i class="bi bi-x-lg fs-4"></i>
                </a>
            </div>
        </div>

          <style>
            .company-brand{
                text-align:center;
                padding:25px 15px;
                background:linear-gradient(135deg,#ff7a00,#ff9a2f);
                border-radius:20px;
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
        <div class="company-brand" data-aos="fade-up">
            <img src="images/clients/dubai.png" alt="Work Home Safety Solution">

            <h4>WORK HOME SAFETY SOLUTION</h4>

            <p>
                Invisible Safety Grills • Premium Mosquito Protection Systems • Bird Control Netting Solutions • Anti-Bird Spike Protection Systems • Professional Sports & Cricket Netting Solutions   
            </p>
        </div>

        <!-- User Info -->
        <div class="profile-card" data-aos="fade-up">
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

            <a href="login.html" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-box-arrow-in-right me-1"></i>
                    </div>
                    Login / Signup
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>


            <a href="about.html" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-info-circle"></i>
                    </div>
                    About Us
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="brochure.html" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-file-earmark-arrow-down-fill"></i>
                    </div>
                    Our Brochure
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="reviews.html" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    Clients Reviews
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="gallery.html" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-image-fill"></i>
                    </div>
                    Gallery
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="services.html" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    Services
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="booking.html" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    Booking
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="contact.html" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-envelope"></i>
                    </div>
                    Contact Us
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="privacy-policy.html" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    Privacy Policy
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="our-clients.html" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                    <i class="bi bi-people-fill"></i>
                    </div>
                    Our Clients
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="faq.html" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    Faqs
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="terms-condition.html" class="menu-item">
                <div class="d-flex align-items-center">
                    <div class="icon-box">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    Terms & Conditions
                </div>
                <i class="bi bi-chevron-right"></i>
            </a>

            <a href="cookie-policy.html" class="menu-item">
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

            <a href="tel:+91 9173307640" class="btn call-btn">
                <i class="bi bi-telephone-fill me-2"></i>
                Call Now
            </a>

            <a href="mail:hardikprajapati8104@gmail.com" class="btn call-btn mt-2">
                <i class="bi bi-envelope-fill me-2"></i>
                hardikprajapati8104@gmail.com
            </a>
        </div>

    </div>

<?php
	include_once ('elements/footer.php');
?>
