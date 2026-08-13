<?php
	include_once ('elements/header.php'); 
?>
 
    <style>
        
        .review-badge{
            display:inline-block;
            padding:8px 18px;
            border-radius:50px;
            background:rgba(255,122,0,.12);
            color:var(--orange);
            font-weight:600;
            margin-bottom:15px;
        }

        .review-title{
            font-size:clamp(2rem,4vw,3.5rem);
            font-weight:700;
            color:#222;
        }

        .review-subtitle{
            max-width:700px;
            margin:auto;
            color:#666;
        }

        .review-stat-card{
            background:#fff;
            padding:25px;
            text-align:center;
            border-radius:20px;
            box-shadow:0 10px 25px rgba(0,0,0,.06);
        }

        .review-stat-card h2{
            color:var(--orange);
            font-weight:700;
        }

        .review-card{
            background:#fff;
            padding:25px;
            border-radius:20px;
            height:100%;
            box-shadow:0 10px 25px rgba(0,0,0,.06);
            transition:.3s;
            position:relative;
        }

        .review-card:hover{
            transform:translateY(-8px);
        }

        .review-card::before{
            content:"❝";
            position:absolute;
            top:15px;
            right:20px;
            font-size:50px;
            color:rgba(255,122,0,.15);
        }

        .review-top{
            display:flex;
            align-items:center;
            gap:15px;
            margin-bottom:15px;
        }

        .review-top img{
            width:60px;
            height:60px;
            border-radius:50%;
            object-fit:cover;
        }

        .review-top h5{
            margin-bottom:2px;
            font-weight:600;
        }

        .review-top small{
            color:#888;
        }

        .stars{
            color:#ffb400;
            font-size:18px;
            margin-bottom:12px;
        }

        .review-card p{
            color:#666;
            margin-bottom:0;
            line-height:1.8;
        }

        .review-cta{
            background:linear-gradient(135deg,#ff7a00,#ff9a2f);
            padding:50px 30px;
            border-radius:25px;
            text-align:center;
            color:#fff;
        }

        .btn-review{
            background:#fff;
            color:#ff7a00;
            font-weight:600;
            border-radius:12px;
            padding:12px 30px;
        }

        .btn-review:hover{
            color:#ff7a00;
            background:#fff;
        }

        @media(max-width:767px){

            .reviews-section{
                padding-top:30px !important;
                padding-bottom:30px !important;
            }

            .review-card{
                padding:20px;
                border-radius:15px;
            }

            .review-top img{
                width:50px;
                height:50px;
            }

            .review-title{
                font-size:2rem;
            }

            .review-cta{
                padding:30px 20px;
                border-radius:18px;
            }
        }
    </style> 

    <!-- header section -->
    <section class="page-header">
        <div class="overlay"></div>

        <div class="container position-relative"> 
            <h1 data-aos="fade-up">Client Reviews</h1>

            <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
                <li class="breadcrumb-item">
                    <a href="index.html">
                        <i class="bi bi-house-door-fill me-1"></i> Home
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    Client Reviews
                </li>
            </ol>
        </div>
    </section>

    <section class="reviews-section py-5">
        <div class="container">

            <!-- Header -->
            <div class="text-center mb-5">
                <span class="review-badge" data-aos="fade-up">Customer Testimonials</span>
                <h1 class="review-title" data-aos="fade-up">What Our Clients Say</h1>
                <p class="review-subtitle" data-aos="fade-up">
                    Discover why hundreds of customers trust us for their cleaning and housekeeping needs.
                </p>
            </div>

            <!-- Review Stats -->
            <div class="row g-4 mb-5">

                <div class="col-6 col-md-3" data-aos="fade-up">
                    <div class="review-stat-card">
                        <h2>4.9★</h2>
                        <p>Average Rating</p>
                    </div>
                </div>

                <div class="col-6 col-md-3" data-aos="fade-up">
                    <div class="review-stat-card">
                        <h2>500+</h2>
                        <p>Happy Clients</p>
                    </div>
                </div>

                <div class="col-6 col-md-3" data-aos="fade-up">
                    <div class="review-stat-card">
                        <h2>98%</h2>
                        <p>Satisfaction Rate</p>
                    </div>
                </div>

                <div class="col-6 col-md-3" data-aos="fade-up">
                    <div class="review-stat-card">
                        <h2>1000+</h2>
                        <p>Services Completed</p>
                    </div>
                </div>

            </div>

            <!-- Reviews -->
            <div class="row g-4">

                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="review-card">
                        <div class="review-top">
                            <img src="images/testimonial/team-1.jpg" alt="">
                            <div>
                                <h5>John Smith</h5>
                                <small>Residential Cleaning</small>
                            </div>
                        </div>

                        <div class="stars">
                            ★★★★★
                        </div>

                        <p>
                            The team did an amazing job. My home has never looked this clean.
                            Professional staff and excellent service.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="review-card">
                        <div class="review-top">
                            <img src="images/testimonial/team-2.jpg" alt="">
                            <div>
                                <h5>Sarah Johnson</h5>
                                <small>Office Cleaning</small>
                            </div>
                        </div>

                        <div class="stars">
                            ★★★★★
                        </div>

                        <p>
                            Reliable and punctual service. Our office looks spotless after every visit.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="review-card">
                        <div class="review-top">
                            <img src="images/testimonial/team-3.jpg" alt="">
                            <div>
                                <h5>Michael Brown</h5>
                                <small>Deep Cleaning</small>
                            </div>
                        </div>

                        <div class="stars">
                            ★★★★★
                        </div>

                        <p>
                            Outstanding deep cleaning service. Every corner was cleaned perfectly.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="review-card">
                        <div class="review-top">
                            <img src="images/testimonial/team-4.jpg" alt="">
                            <div>
                                <h5>Emily Davis</h5>
                                <small>Apartment Cleaning</small>
                            </div>
                        </div>

                        <div class="stars">
                            ★★★★★
                        </div>

                        <p>
                            Friendly staff and easy booking process. Highly recommended.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="review-card">
                        <div class="review-top">
                            <img src="images/testimonial/team-1.jpg" alt="">
                            <div>
                                <h5>David Wilson</h5>
                                <small>Commercial Cleaning</small>
                            </div>
                        </div>

                        <div class="stars">
                            ★★★★★
                        </div>

                        <p>
                            Excellent service quality and attention to detail. Great experience.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="review-card">
                        <div class="review-top">
                            <img src="images/testimonial/team-2.jpg" alt="">
                            <div>
                                <h5>Lisa Taylor</h5>
                                <small>Move-In Cleaning</small>
                            </div>
                        </div>

                        <div class="stars">
                            ★★★★★
                        </div>

                        <p>
                            The cleaners exceeded expectations. Everything was fresh and spotless.
                        </p>
                    </div>
                </div>

            </div>

            <!-- CTA -->
            <div class="review-cta mt-5" data-aos="fade-up">
                <h3>Ready for a Spotless Space?</h3>
                <p>
                    Join hundreds of satisfied customers and book your cleaning service today.
                </p>
                <a href="booking.html" class="btn btn-review">
                    Book Now
                </a>
            </div>

        </div>
    </section>

<?php
	include_once ('elements/footer.php');
?>
