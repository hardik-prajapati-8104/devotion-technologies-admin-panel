<?php
	include_once ('elements/header.php'); 
?>


    <style>
     
        .faq-section{
            background:#fffaf5;
            min-height:100vh;
        }

        .faq-badge{
            display:inline-block;
            background:rgba(255,122,0,.12);
            color:var(--orange);
            padding:8px 18px;
            border-radius:50px;
            font-weight:600;
            margin-bottom:15px;
        }

        .faq-title{
            font-size:clamp(2rem,4vw,3.5rem);
            font-weight:700;
            color:#222;
        }

        .faq-subtitle{
            color:#666;
            max-width:700px;
            margin:auto;
        }

        .faq-wrapper{
            max-width:900px;
            margin:auto;
        }

        .accordion-item{
            border:none;
            border-radius:16px !important;
            overflow:hidden;
            margin-bottom:15px;
            box-shadow:0 10px 25px rgba(0,0,0,.06);
        }

        .accordion-button{
            font-weight:600;
            padding:20px;
            background:#fff;
            color:#222;
        }

        .accordion-button:not(.collapsed){
            background:#fff4e8;
            color:var(--orange);
            box-shadow:none;
        }

        .accordion-button:focus{
            box-shadow:none;
            border:none;
        }

        .accordion-body{
            color:#666;
            line-height:1.8;
            padding:20px;
        }

        .accordion-button::after{
            background-size:18px;
        }

        .faq-contact-card{
            background:linear-gradient(135deg,#ff7a00,#ff9a2f);
            padding:40px 30px;
            border-radius:25px;
            text-align:center;
            color:#fff;
            max-width:700px;
            margin:auto;
        }

        .faq-icon{
            width:80px;
            height:80px;
            background:#fff;
            color:var(--orange);
            border-radius:50%;
            margin:auto auto 20px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:32px;
        }

        .faq-btn{
            background:#fff;
            color:var(--orange);
            padding:12px 25px;
            border-radius:12px;
            font-weight:600;
        }

        .faq-btn:hover{
            background:#fff;
            color:var(--orange);
        }

        @media(max-width:767px){

            .faq-section{
                padding-top:30px !important;
                padding-bottom:30px !important;
            }

            .faq-title{
                font-size:2rem;
            }

            .accordion-button{
                padding:15px;
                font-size:14px;
            }

            .accordion-body{
                padding:15px;
                font-size:14px;
            }

            .faq-contact-card{
                padding:25px 20px;
                border-radius:18px;
            }

            .faq-icon{
                width:60px;
                height:60px;
                font-size:24px;
            }
        }
    </style>
  
    <!-- header section -->
    <section class="page-header">
        <div class="overlay"></div>

        <div class="container position-relative"> 
            <h1 data-aos="fade-up">Faqs</h1>

            <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
                <li class="breadcrumb-item">
                    <a href="index.html">
                        <i class="bi bi-house-door-fill me-1"></i> Home
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    Faqs
                </li>
            </ol>
        </div>
    </section> 

    <section class="faq-section py-5">
        <div class="container">

            <!-- Header -->
            <div class="text-center mb-5">
                <span class="faq-badge" data-aos="fade-up">Help Center</span>
                <h1 class="faq-title" data-aos="fade-up">Frequently Asked Questions</h1>
                <p class="faq-subtitle" data-aos="fade-up">
                    Find answers to common questions about our services, booking process, and support.
                </p>
            </div>

            <!-- FAQ Accordion -->
            <div class="faq-wrapper">

                <div class="accordion" id="faqAccordion">

                    <div class="accordion-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button" data-bs-toggle="collapse"
                                data-bs-target="#faq1">
                                What services do you provide?
                            </button>
                        </h2>

                        <div id="faq1" class="accordion-collapse collapse show"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We provide Invisible Grills, Mosquito Nets, Bird Nets,
                                Bird Spikes, and Sports & Cricket Netting Solutions.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed"
                                data-bs-toggle="collapse"
                                data-bs-target="#faq2">
                                How can I book a service?
                            </button>
                        </h2>

                        <div id="faq2" class="accordion-collapse collapse"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You can book directly from our website, mobile app,
                                or contact our support team.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed"
                                data-bs-toggle="collapse"
                                data-bs-target="#faq3">
                                Do you provide free site visits?
                            </button>
                        </h2>

                        <div id="faq3" class="accordion-collapse collapse"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, we offer free site inspections and measurements
                                before installation.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed"
                                data-bs-toggle="collapse"
                                data-bs-target="#faq4">
                                How long does installation take?
                            </button>
                        </h2>

                        <div id="faq4" class="accordion-collapse collapse"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Most installations are completed within one day,
                                depending on project size.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed"
                                data-bs-toggle="collapse"
                                data-bs-target="#faq5">
                                Do your products come with a warranty?
                            </button>
                        </h2>

                        <div id="faq5" class="accordion-collapse collapse"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, our products include warranty coverage based on
                                the selected service package.
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Contact Card -->
            <div class="faq-contact-card mt-5" data-aos="fade-up">

                <div class="faq-icon">
                    <i class="bi bi-headset"></i>
                </div>

                <h4>Still Have Questions?</h4>

                <p>
                    Our team is available to help you with any inquiries.
                </p>

                <a href="contact.html" class="btn faq-btn">
                    Contact Us
                </a>

            </div>

        </div>
    </section>

<?php
	include_once ('elements/footer.php');
?>
