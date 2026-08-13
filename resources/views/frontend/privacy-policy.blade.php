<?php
	include_once ('elements/header.php');  
?> 

    <style>
   
        .privacy-section{
            background:#fffaf5;
            min-height:100vh;
        }

        .privacy-header{
            max-width:750px;
            margin:auto;
        }

        .privacy-badge{
            display:inline-block;
            padding:8px 18px;
            border-radius:50px;
            background:rgba(255,122,0,.12);
            color:var(--orange);
            font-weight:600;
            margin-bottom:15px;
        }

        .privacy-title{
            font-size:clamp(2rem,4vw,3.3rem);
            font-weight:700;
            color:#222;
        }

        .privacy-subtitle{
            color:#666;
            margin-top:10px;
            line-height:1.7;
        }

        .privacy-card{
            background:#fff;
            border-radius:24px;
            padding:40px;
            box-shadow:0 10px 35px rgba(0,0,0,.08);
        }

        .privacy-item{
            padding:25px 0;
            border-bottom:1px solid #eeeeee;
        }

        .privacy-item h4{
            display:flex;
            align-items:center;
            gap:12px;
            color:#222;
            font-size:1.25rem;
            font-weight:600;
            margin-bottom:12px;
        }

        .privacy-item h4 i{
            color:var(--orange);
        }

        .privacy-item p{
            color:#666;
            line-height:1.8;
            margin:0;
        }

        @media (max-width:767px){

            .privacy-section{
                padding-top:30px !important;
                padding-bottom:30px !important;
            }

            .privacy-card{
                padding:20px;
                border-radius:18px;
            }

            .privacy-title{
                font-size:2rem;
            }

            .privacy-item{
                padding:18px 0;
            }

            .privacy-item h4{
                font-size:1.2rem;
                line-height:1.4;
            }

            .privacy-item p{
                font-size:1rem;
            }
        }
    </style> 

    <!-- header section -->
    <section class="page-header">
        <div class="overlay"></div>

        <div class="container position-relative"> 
            <h1 data-aos="fade-up">Privacy Policy</h1>

            <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
                <li class="breadcrumb-item">
                    <a href="index.html">
                        <i class="bi bi-house-door-fill me-1"></i> Home
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    Privacy Policy
                </li>
            </ol>
        </div>
    </section>

    <section class="privacy-section py-5">
        <div class="container">

            <!-- Header -->
            <div class="privacy-header text-center mb-5">
                <span class="privacy-badge" data-aos="fade-up">Legal Information</span>
                <h1 class="privacy-title" data-aos="fade-up">Privacy Policy</h1>
                <p class="privacy-subtitle" data-aos="fade-up">
                    Your privacy is important to us. This policy explains how we collect, use, and protect your information.
                </p>
            </div>

            <!-- Privacy Content -->
            <div class="privacy-card">

                <div class="privacy-item" data-aos="fade-up">
                    <h4>
                        <i class="bi bi-person-lock"></i>
                        Information We Collect
                    </h4>
                    <p>
                        We may collect personal information such as your name, email address,
                        phone number, and booking details when you use our services or contact us.
                    </p>
                </div>

                <div class="privacy-item" data-aos="fade-up">
                    <h4 >
                        <i class="bi bi-database-fill"></i>
                        How We Use Your Information
                    </h4>
                    <p>
                        Your information is used to process bookings, improve our services,
                        communicate with you, and provide customer support.
                    </p>
                </div>

                <div class="privacy-item" data-aos="fade-up">
                    <h4>
                        <i class="bi bi-shield-check"></i>
                        Data Protection
                    </h4>
                    <p>
                        We implement appropriate security measures to protect your personal
                        information from unauthorized access, disclosure, or misuse.
                    </p>
                </div>

                <div class="privacy-item" data-aos="fade-up">
                    <h4>
                        <i class="bi bi-share-fill"></i>
                        Information Sharing
                    </h4>
                    <p>
                        We do not sell, trade, or rent your personal information to third parties.
                        Information may only be shared when required by law or to provide services.
                    </p>
                </div>

                <div class="privacy-item" data-aos="fade-up">
                    <h4>
                        <i class="bi bi-cookie"></i>
                        Cookies & Tracking
                    </h4>
                    <p>
                        Our website may use cookies and analytics tools to enhance your browsing
                        experience and understand website usage patterns.
                    </p>
                </div>

                <div class="privacy-item" data-aos="fade-up">
                    <h4>
                        <i class="bi bi-check-circle-fill"></i>
                        Your Rights
                    </h4>
                    <p>
                        You have the right to access, update, or request deletion of your
                        personal information, subject to applicable laws.
                    </p>
                </div>

                <div class="privacy-item" data-aos="fade-up">
                    <h4>
                        <i class="bi bi-arrow-repeat"></i>
                        Policy Updates
                    </h4>
                    <p>
                        We may update this Privacy Policy periodically. Changes will be posted
                        on this page with an updated effective date.
                    </p>
                </div>

                <div class="privacy-item border-0">
                    <h4 data-aos="fade-up">
                        <i class="bi bi-envelope-fill"></i>
                        Contact Us
                    </h4>
                    <p data-aos="fade-up">
                        If you have any questions regarding this Privacy Policy,
                        please contact our support team for assistance.
                    </p>
                </div>

            </div>

        </div>
    </section>

<?php
    include_once ('elements/footer.php');
?>
