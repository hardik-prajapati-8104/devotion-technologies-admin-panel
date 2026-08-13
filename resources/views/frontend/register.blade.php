<?php
	include_once ('elements/header.php'); 
?>

    
    <style>
 
        .register-section{
            min-height:100vh;
            background:linear-gradient(135deg,#fff6ee,#ffffff);
            padding:30px 0;
        }

        .register-card{
            background:#fff;
            border-radius:25px;
            padding:40px;
            box-shadow:0 20px 50px rgba(0,0,0,.08);
        }

        .register-logo{
            width:80px;
            height:80px;
            margin:auto;
            border-radius:50%;
            background:rgba(255,122,0,.12);
            display:flex;
            align-items:center;
            justify-content:center;
            color:var(--orange);
            font-size:35px;
        }

        .register-title{
            font-weight:700;
            margin-top:20px;
        }

        .register-subtitle{
            color:#777;
        }

        .form-label{
            font-weight:600;
        }

        .input-group{
            border-radius:12px;
            overflow:hidden;
        }

        .input-group-text{
            background:#fff4ea;
            border-color:#eee;
            color:var(--orange);
        }

        .form-control{
            border-color:#eee;
        }

        .form-control:focus{
            box-shadow:none;
            border-color:var(--orange);
        }

        .password-toggle{
            background:#fff;
            border:1px solid #eee;
        }

        .register-btn{
            background:var(--orange);
            color:#fff;
            border:none;
            border-radius:12px;
            padding:12px;
            font-weight:600;
        }

        .register-btn:hover{
            background:#f06f00;
            color:#fff;
        }

        .divider{
            text-align:center;
            margin:25px 0;
            position:relative;
        }

        .divider::before{
            content:'';
            position:absolute;
            top:50%;
            left:0;
            right:0;
            height:1px;
            background:#eee;
        }

        .divider span{
            background:#fff;
            padding:0 15px;
            position:relative;
            color:#999;
        }

        .social-btn{
            width:100%;
            border:1px solid #eee;
            padding:12px;
            border-radius:12px;
        }

        .login-link{
            text-align:center;
            margin-top:25px;
            color:#666;
        }

        .login-link a,
        .form-check a{
            color:var(--orange);
            text-decoration:none;
            font-weight:600;
        }

        @media (max-width: 767.98px) {

            html,
            body {
                height: 100%;
                overflow: hidden;
            }

            .register-section {
                height: calc(100vh - 70px); /* bottom nav height */
                min-height: auto;
                padding: 10px 0;
                overflow: hidden;
            }

            .register-card {
                padding: 15px;
                margin: 0;
                border-radius: 15px;
            }

            .register-logo {
                width: 50px;
                height: 50px;
                font-size: 22px;
                margin-bottom: 8px;
            }

            .register-title {
                font-size: 1.5rem;
                margin-bottom: 5px;
            }

            .register-subtitle {
                font-size: 12px;
                margin-bottom: 12px;
            }

            .mb-3 {
                margin-bottom: 10px !important;
            }

            .form-label {
                font-size: 12px;
                margin-bottom: 4px;
            }

            .form-control,
            .input-group-text,
            .password-toggle {
                min-height: 40px;
                font-size: 13px;
            }

            .register-btn {
                height: 42px;
                padding: 0;
            }

            .divider {
                margin: 12px 0;
            }

            .social-btn {
                padding: 10px;
                font-size: 13px;
            }

            .min-vh-100 {
                min-height: 90vh !important;
            }

            .login-link {
                margin-top: 12px;
                font-size: 13px;
            }

            .form-check-label {
                font-size: 12px;
            }
        }
    </style> 

    <!-- header section -->
    <section class="page-header">
        <div class="overlay"></div>

        <div class="container position-relative"> 
            <h1 data-aos="fade-up">Register</h1>

            <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
                <li class="breadcrumb-item">
                    <a href="index.html">
                        <i class="bi bi-house-door-fill me-1"></i> Home
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    Register
                </li>
            </ol>
        </div>
    </section>
 
    <section class="register-section">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">

                <div class="col-lg-6 col-md-8 col-sm-11">

                    <div class="register-card">

                        <div class="text-center mb-4">
                            <div class="register-logo">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>

                            <h2 class="register-title" data-aos="fade-up">Create Account</h2>

                            <p class="register-subtitle" data-aos="fade-up">
                                Join us today and book professional cleaning services.
                            </p>
                        </div>

                        <form data-aos="fade-up">

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <input type="text" class="form-control"
                                            placeholder="Enter your name">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mobile Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-telephone"></i>
                                        </span>
                                        <input type="tel" class="form-control"
                                            placeholder="Enter mobile number">
                                    </div>
                                </div>

                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email"
                                        class="form-control"
                                        placeholder="Enter your email">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>

                                    <input type="password"
                                        class="form-control"
                                        id="password"
                                        placeholder="Create password">

                                    <button class="btn password-toggle"
                                        type="button"
                                        onclick="togglePassword('password')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-shield-lock"></i>
                                    </span>

                                    <input type="password"
                                        class="form-control"
                                        id="confirmPassword"
                                        placeholder="Confirm password">

                                    <button class="btn password-toggle"
                                        type="button"
                                        onclick="togglePassword('confirmPassword')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox">

                                <label class="form-check-label">
                                    I agree to the
                                    <a href="terms-condition.html">Terms & Conditions</a>
                                    and
                                    <a href="privacy-policy.html">Privacy Policy</a>
                                </label>
                            </div>

                            <button type="submit"
                                class="btn register-btn w-100">
                                Create Account
                            </button>

                        </form>

                        <div class="divider" data-aos="fade-up">
                            <span>OR</span>
                        </div>

                        <button class="btn social-btn" data-aos="fade-up">
                            <i class="bi bi-google"></i>
                            Continue with Google
                        </button>

                        <p class="login-link" data-aos="fade-up">
                            Already have an account?
                            <a href="login.html">Login Now</a>
                        </p>

                    </div>

                </div>

            </div>
        </div>
    </section>

<?php
	include_once ('elements/footer.php');
?>
