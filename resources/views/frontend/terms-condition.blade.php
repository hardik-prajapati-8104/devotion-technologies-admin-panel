<?php
	include_once ('elements/header.php'); 
?>

    <style>
            
            .terms-section{
                background:#fffaf5;
                min-height:100vh;
            }

            .terms-header{
                max-width:700px;
                margin:auto;
            }

            .terms-badge{
                display:inline-block;
                background:rgba(255,122,0,.12);
                color:var(--orange);
                padding:8px 18px;
                border-radius:50px;
                font-weight:600;
                margin-bottom:15px;
            }

            .terms-title{
                font-size:clamp(2rem,4vw,3.2rem);
                font-weight:700;
                color:#222;
            }

            .terms-subtitle{
                color:#666;
                font-size:1rem;
            }

            .terms-card{
                background:#fff;
                border-radius:25px;
                padding:40px;
                box-shadow:0 10px 40px rgba(0,0,0,.08);
            }

            .terms-item{
                padding:25px 0;
                border-bottom:1px solid #eee;
            }

            .terms-item h4{
                color:#222;
                font-size:1.25rem;
                font-weight:600;
                display:flex;
                align-items:center;
                gap:12px;
                margin-bottom:12px;
            }

            .terms-item h4 i{
                color:var(--orange);
            }

            .terms-item p{
                color:#666;
                margin:0;
                line-height:1.8;
            }

            @media(max-width:767px){

                .terms-section{
                    padding-top:30px !important;
                    padding-bottom:30px !important;
                }

                .terms-card{
                    padding:20px;
                    border-radius:18px;
                }

                .terms-title{
                    font-size:2rem;
                }

                .terms-item{
                    padding:18px 0;
                }

                .terms-item h4{
                    font-size:1.2rem;
                }

                .terms-item p{
                    font-size:1rem;
                }
            }
    </style> 

    <!-- header section -->
    <section class="page-header">
        <div class="overlay"></div>

        <div class="container position-relative"> 
            <h1 data-aos="fade-up">Terms & Condition</h1>

            <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
                <li class="breadcrumb-item">
                    <a href="index.html">
                        <i class="bi bi-house-door-fill me-1"></i> Home
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    Terms & Condition
                </li>
            </ol>
        </div>
    </section>

    <section class="terms-section py-5">
        <div class="container">

            <div class="terms-header text-center mb-5">
                <span class="terms-badge" data-aos="fade-up">Legal Information</span>
                <h1 class="terms-title" data-aos="fade-up">Terms & Conditions</h1>
                <p class="terms-subtitle" data-aos="fade-up">
                    Please read these terms carefully before using our services.
                </p>
            </div>

            <div class="terms-card">

                <div class="terms-item" data-aos="fade-up">
                    <h4><i class="bi bi-file-earmark-text-fill"></i> Acceptance of Terms</h4>
                    <p>
                        By accessing and using our services, you agree to comply with
                        and be bound by these Terms and Conditions.
                    </p>
                </div>

                <div class="terms-item" data-aos="fade-up">
                    <h4><i class="bi bi-calendar-check-fill"></i> Booking Policy</h4>
                    <p>
                        All bookings are subject to availability. We reserve the right
                        to reschedule services due to unforeseen circumstances.
                    </p>
                </div>

                <div class="terms-item" data-aos="fade-up">
                    <h4><i class="bi bi-cash-stack"></i> Payments</h4>
                    <p>
                        Payment must be completed before or immediately after service
                        completion unless otherwise agreed.
                    </p>
                </div>

                <div class="terms-item" data-aos="fade-up">
                    <h4><i class="bi bi-arrow-counterclockwise"></i> Cancellation Policy</h4>
                    <p>
                        Cancellations made within 24 hours of the scheduled appointment
                        may be subject to cancellation fees.
                    </p>
                </div>

                <div class="terms-item" data-aos="fade-up">
                    <h4><i class="bi bi-shield-check"></i> Liability</h4>
                    <p>
                        We take reasonable care while providing services but are not
                        liable for damages caused by pre-existing conditions or events
                        beyond our control.
                    </p>
                </div>

                <div class="terms-item" data-aos="fade-up">
                    <h4><i class="bi bi-lock-fill"></i> Privacy</h4>
                    <p>
                        Personal information collected during booking will be handled
                        according to our Privacy Policy.
                    </p>
                </div>

                <div class="terms-item" data-aos="fade-up">
                    <h4><i class="bi bi-exclamation-triangle-fill"></i> Service Refusal</h4>
                    <p>
                        We reserve the right to refuse or discontinue service if safety
                        concerns arise or terms are violated.
                    </p>
                </div>

                <div class="terms-item border-0" data-aos="fade-up">
                    <h4><i class="bi bi-envelope-fill"></i> Contact Information</h4>
                    <p>
                        If you have questions regarding these Terms & Conditions,
                        please contact our support team.
                    </p>
                </div>

            </div>

        </div>
    </section>
     
<?php
	include_once ('elements/footer.php');
?>
