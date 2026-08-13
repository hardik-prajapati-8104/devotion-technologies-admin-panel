<?php
	include_once ('elements/header.php'); 
?>

    <style>
   
        .cookie-section{
            background:#fffaf5;
            min-height:100vh;
        }

        .cookie-header{
            max-width:700px;
            margin:auto;
        }

        .cookie-badge{
            display:inline-block;
            padding:8px 18px;
            border-radius:50px;
            background:rgba(255,122,0,.12);
            color:var(--orange);
            font-weight:600;
            margin-bottom:15px;
        }

        .cookie-title{
            font-size:clamp(2rem,4vw,3.2rem);
            font-weight:700;
            color:#222;
        }

        .cookie-subtitle{
            color:#666;
            margin-top:10px;
        }

        .cookie-card{
            background:#fff;
            border-radius:24px;
            padding:40px;
            box-shadow:0 10px 35px rgba(0,0,0,.08);
        }

        .cookie-item{
            padding:25px 0;
            border-bottom:1px solid #eeeeee;
        }

        .cookie-item h4{
            display:flex;
            align-items:center;
            gap:12px;
            font-size:1.25rem;
            font-weight:600;
            color:#222;
            margin-bottom:12px;
        }

        .cookie-item h4 i{
            color:var(--orange);
        }

        .cookie-item p{
            color:#666;
            line-height:1.8;
            margin:0;
        }

        @media(max-width:767px){

            .cookie-section{
                padding-top:30px !important;
                padding-bottom:30px !important;
            }

            .cookie-card{
                padding:20px;
                border-radius:18px;
            }

            .cookie-title{
                font-size:2rem;
            }

            .cookie-item{
                padding:18px 0;
            }

            .cookie-item h4{
                font-size:1.2rem;
                line-height:1.4;
            }

            .cookie-item p{
                font-size:1rem;
            }
        }
    </style>
  
    <!-- header section -->
    <section class="page-header">
        <div class="overlay"></div>

        <div class="container position-relative" data-aos="fade-up"> 
            <h1>Cookie Policy</h1>

            <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
                <li class="breadcrumb-item">
                    <a href="index.html">
                        <i class="bi bi-house-door-fill me-1"></i> Home
                    </a>
                </li>

                <li class="breadcrumb-item active" data-aos="fade-up">
                    Cookie Policy
                </li>
            </ol>
        </div>
    </section>

    <section class="cookie-section py-5">
        <div class="container">

            <!-- Header -->
            <div class="cookie-header text-center mb-5">
                <span class="cookie-badge" data-aos="fade-up">Website Policy</span>
                <h1 class="cookie-title" data-aos="fade-up">Cookie Policy</h1>
                <p class="cookie-subtitle" data-aos="fade-up">
                    Learn how we use cookies and similar technologies to improve your experience.
                </p>
            </div>

            <!-- Content -->
            <div class="cookie-card">

                <div class="cookie-item" data-aos="fade-up">
                    <h4>
                        <i class="bi bi-cookie"></i>
                        What Are Cookies?
                    </h4>
                    <p>
                        Cookies are small text files stored on your device when you visit a website.
                        They help websites function properly and improve user experience.
                    </p>
                </div>

                <div class="cookie-item" data-aos="fade-up">
                    <h4>
                        <i class="bi bi-gear-fill"></i>
                        How We Use Cookies
                    </h4>
                    <p>
                        We use cookies to remember your preferences, analyze website traffic,
                        enhance website performance, and provide a personalized experience.
                    </p>
                </div>

                <div class="cookie-item" data-aos="fade-up">
                    <h4>
                        <i class="bi bi-bar-chart-fill"></i>
                        Analytics Cookies
                    </h4>
                    <p>
                        These cookies help us understand how visitors interact with our website
                        by collecting anonymous information and usage statistics.
                    </p>
                </div>

                <div class="cookie-item" data-aos="fade-up">
                    <h4>
                        <i class="bi bi-person-check-fill"></i>
                        Functional Cookies
                    </h4>
                    <p>
                        Functional cookies allow the website to remember your preferences
                        such as language, login details, and other personalized settings.
                    </p>
                </div>

                <div class="cookie-item" data-aos="fade-up">
                    <h4>
                        <i class="bi bi-shield-lock-fill"></i>
                        Security Cookies
                    </h4>
                    <p>
                        Security cookies help identify malicious activity and protect
                        user information from unauthorized access.
                    </p>
                </div>

                <div class="cookie-item" data-aos="fade-up">
                    <h4>
                        <i class="bi bi-sliders"></i>
                        Managing Cookies
                    </h4>
                    <p>
                        You can control and delete cookies through your browser settings.
                        However, disabling certain cookies may affect website functionality.
                    </p>
                </div>

                <div class="cookie-item" data-aos="fade-up">
                    <h4>
                        <i class="bi bi-arrow-repeat"></i>
                        Policy Updates
                    </h4>
                    <p>
                        We may update this Cookie Policy periodically. Any changes will
                        be posted on this page with the revised effective date.
                    </p>
                </div>

                <div class="cookie-item border-0" data-aos="fade-up"> 
                    <h4>
                        <i class="bi bi-envelope-fill"></i>
                        Contact Us
                    </h4>
                    <p>
                        If you have any questions regarding our Cookie Policy,
                        please contact our support team.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <?php
	    include_once ('elements/footer.php');
    ?>
