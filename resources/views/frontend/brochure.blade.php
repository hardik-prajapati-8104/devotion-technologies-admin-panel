<?php
	include_once ('elements/header.php'); 
?>

<style> 

    .brochure-section{
        background:#f8f9fa;
        min-height:70vh;
        display:flex;
        align-items:center;
    }

    .brochure-card{
        background:#fff;
        border-radius:25px;
        padding:50px;
        text-align:center;
        box-shadow:0 15px 40px rgba(0,0,0,.08);
    }

    .brochure-icon{
        width:90px;
        height:90px;
        margin:auto;
        border-radius:50%;
        background:var(--orange-soft);
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .brochure-icon i{
        font-size:42px;
        color:var(--orange);
    }

    .brochure-badge{
        display:inline-block;
        margin-top:20px;
        background:var(--orange-soft);
        color:var(--orange);
        padding:8px 18px;
        border-radius:50px;
        font-weight:600;
    }

    .brochure-title{
        font-size:clamp(2rem,4vw,3.2rem);
        font-weight:800;
        margin-top:15px;
    }

    .brochure-text{
        color:#666;
        max-width:650px;
        margin:20px auto;
        line-height:1.8;
    }

    .feature-item{
        background:#fff8f2;
        border-radius:15px;
        padding:20px;
        transition:.3s;
    }

    .feature-item:hover{
        background:var(--orange);
        color:#fff;
        transform:translateY(-5px);
    }

    .feature-item i{
        font-size:28px;
        color:var(--orange);
        display:block;
        margin-bottom:10px;
    }

    .feature-item:hover i{
        color:#fff;
    }

    .brochure-preview{
        display:flex;
        align-items:center;
        gap:20px;
        background:#fafafa;
        border:1px solid #eee;
        border-radius:20px;
        padding:20px;
        text-align:left;
    }

    .preview-cover{
        width:80px;
        height:80px;
        border-radius:15px;
        background:var(--orange-soft);
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .preview-cover i{
        font-size:40px;
        color:var(--orange);
    }

    .preview-info h5{
        margin-bottom:5px;
        font-weight:700;
    }

    .preview-info p{
        margin:0;
        color:#666;
    }

    .brochure-btn{
        background:var(--orange);
        color:#fff;
        padding:14px 35px;
        border-radius:50px;
        font-weight:600;
        border:none;
    }

    .brochure-btn:hover{
        background:#e56f00;
        color:#fff;
    }

    /* Mobile */
    @media(max-width:767px){

        .brochure-card{
            padding:25px;
        }

        .brochure-preview{
            flex-direction:column;
            text-align:center;
        }

        .brochure-btn{
            width:100%;
        }

        .feature-item{
            padding:15px;
        }
    }
</style>

<!-- header section -->
<section class="page-header">
    <div class="overlay"></div>

    <div class="container position-relative"> 
        <h1 data-aos="fade-up">Brochure</h1>

        <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
            <li class="breadcrumb-item" data-aos="fade-up">
                <a href="index.html">
                    <i class="bi bi-house-door-fill me-1"></i> Home
                </a>
            </li>

            <li class="breadcrumb-item active" data-aos="fade-up">
                Brochure
            </li>
        </ol>
    </div>
</section>
 
<section class="brochure-section py-5">
    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <div class="brochure-card">

                    <div class="brochure-icon">
                        <i class="bi bi-file-earmark-arrow-down-fill"></i>
                    </div>

                    <span class="brochure-badge">
                        Company Profile
                    </span>

                    <h1 class="brochure-title">
                        Download Our Brochure
                    </h1>

                    <p class="brochure-text">
                        Discover our complete range of safety and protection
                        solutions including Sports Netting, Bird Control Systems,
                        Invisible Grills, Mosquito Protection, Cleaning Services,
                        and more.
                    </p>

                    <!-- Features -->
                    <div class="row g-3 mt-4">

                        <div class="col-md-4">
                            <div class="feature-item">
                                <i class="bi bi-shield-check"></i>
                                <span>Safety Solutions</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="feature-item">
                                <i class="bi bi-award"></i>
                                <span>Premium Quality</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="feature-item">
                                <i class="bi bi-people"></i>
                                <span>Expert Support</span>
                            </div>
                        </div>

                    </div>

                    <!-- PDF Preview -->
                    <div class="brochure-preview mt-4">

                        <div class="preview-cover">
                            <i class="bi bi-filetype-pdf"></i>
                        </div>

                        <div class="preview-info">
                            <h5>Work Home Safety Solution</h5>
                            <p>Company Profile Brochure PDF</p>
                        </div>

                    </div>

                    <!-- Download Button -->
                    <a href="assets/brochure/company-profile.pdf"
                       download
                       class="btn brochure-btn mt-4">
                        <i class="bi bi-download me-2"></i>
                        Download Brochure
                    </a>

                </div>

            </div>

        </div>

    </div>
</section>

  <?php
	  include_once ('elements/footer.php');
  ?>
