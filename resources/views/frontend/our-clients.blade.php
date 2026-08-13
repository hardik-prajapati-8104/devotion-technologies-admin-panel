<?php
	include_once ('elements/header.php'); 
?> 

    <style>
            
            .clients-page{
                background:#fffaf5;
                min-height:100vh;
            }

            .client-badge{
                display:inline-block;
                padding:8px 18px;
                border-radius:50px;
                background:rgba(255,122,0,.12);
                color:var(--orange);
                font-weight:600;
                margin-bottom:15px;
            }

            .client-title{
                font-size:clamp(2rem,4vw,3.5rem);
                font-weight:700;
                color:#222;
            }

            .client-subtitle{
                color:#666;
                max-width:700px;
                margin:auto;
            }

            .stat-card{
                background:#fff;
                padding:25px;
                border-radius:20px;
                text-align:center;
                box-shadow:0 10px 25px rgba(0,0,0,.06);
                transition:.3s;
            }

            .stat-card:hover{
                transform:translateY(-5px);
            }

            .stat-card h2{
                color:var(--orange);
                font-weight:700;
                margin-bottom:5px;
            }

            .stat-card p{
                margin:0;
                color:#666;
            }

            .client-card{
                background:#fff;
                border-radius:20px;
                padding:25px;
                text-align:center;
                box-shadow:0 10px 25px rgba(0,0,0,.06);
                transition:.3s;
                height:100%;
            }

            .client-card:hover{
                transform:translateY(-8px);
                border:1px solid rgba(255,122,0,.3);
            }

            .client-card img{
                width:100%;
                max-width:120px;
                height:80px;
                object-fit:contain;
                margin-bottom:15px;
            }

            .client-card h6{
                font-weight:600;
                margin-bottom:5px;
            }

            .client-card small{
                color:#777;
            }

            .client-cta{
                background:linear-gradient(135deg,#ff7a00,#ff9a2f);
                padding:50px 30px;
                border-radius:25px;
                text-align:center;
                color:#fff;
            }

            .client-cta h3{
                font-weight:700;
            }

            .client-cta p{
                opacity:.9;
            }

            .btn-orange{
                background:#fff;
                color:#ff7a00;
                font-weight:600;
                border-radius:12px;
                padding:12px 25px;
            }

            .btn-orange:hover{
                background:#fff;
                color:#ff7a00;
            }

            @media(max-width:767px){

                .clients-page{
                    padding-top:30px !important;
                    padding-bottom:30px !important;
                }

                .client-card{
                    padding:15px;
                    border-radius:15px;
                }

                .client-card img{ 
                    height:200px;
                    margin-bottom: 0px;
                }

                .client-cta{
                    padding:30px 20px;
                    border-radius:18px;
                }

                .client-title{
                    font-size:2rem;
                }
            }
    </style>
  
    <!-- header section -->
    <section class="page-header">
        <div class="overlay"></div>

        <div class="container position-relative"> 
            <h1 data-aos="fade-up">Our Clients</h1>

            <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
                <li class="breadcrumb-item">
                    <a href="index.html">
                        <i class="bi bi-house-door-fill me-1"></i> Home
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    Our Clients
                </li>
            </ol>
        </div>
    </section>
    
    <section class="clients-page py-5">
        <div class="container">

            <!-- Header -->
            <div class="text-center mb-5">
                <span class="client-badge" data-aos="fade-up">Trusted Partners</span>
                <h1 class="client-title" data-aos="fade-up">Our Valued Clients</h1>
                <p class="client-subtitle" data-aos="fade-up">
                    We proudly serve homes, offices, hotels, restaurants, and businesses with exceptional cleaning services.
                </p>
            </div>

            <!-- Stats -->
            <div class="row g-4 mb-5">

                <div class="col-6 col-md-3" data-aos="fade-up">
                    <div class="stat-card">
                        <h2>500+</h2>
                        <p>Happy Clients</p>
                    </div>
                </div>

                <div class="col-6 col-md-3" data-aos="fade-up">
                    <div class="stat-card">
                        <h2>1000+</h2>
                        <p>Projects Completed</p>
                    </div>
                </div>

                <div class="col-6 col-md-3" data-aos="fade-up">
                    <div class="stat-card">
                        <h2>4.9★</h2>
                        <p>Customer Rating</p>
                    </div>
                </div>

                <div class="col-6 col-md-3" data-aos="fade-up">
                    <div class="stat-card">
                        <h2>10+</h2>
                        <p>Years Experience</p>
                    </div>
                </div>

            </div>

            <!-- Clients Grid -->
            <div class="row g-4">

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="client-card">
                        <img src="images/clients/binghati.png" alt=""> 
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="client-card">
                        <img src="images/clients/bnw.png" alt=""> 
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="client-card">
                        <img src="images/clients/damac.png" alt=""> 
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="client-card">
                        <img src="images/clients/danube.png" alt=""> 
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="client-card">
                        <img src="images/clients/Meraas.png" alt=""> 
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="client-card">
                        <img src="images/clients/nashama.png" alt=""> 
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="client-card">
                        <img src="images/clients/octa.png" alt=""> 
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="client-card">
                        <img src="images/clients/ellingtone.png" alt=""> 
                    </div>
                </div>

            </div>

            <!-- CTA -->
            <div class="client-cta mt-5" data-aos="fade-up">
                <h3>Join Our Growing Family of Happy Clients</h3>
                <p>Experience premium cleaning services trusted by hundreds of customers.</p>
                <a href="booking.html" class="btn btn-orange">
                    Book Your Service
                </a>
            </div>

        </div>
    </section>

  <?php
	  include_once ('elements/footer.php');
  ?>
