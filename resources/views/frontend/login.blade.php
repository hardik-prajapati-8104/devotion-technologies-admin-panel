    <?php
        include_once ('elements/header.php'); 
    ?> 
    
    <style> 
    
        .login-section{ 
            background:linear-gradient(
                135deg,
                #fff6ee,
                #ffffff
            );
        }

        .login-card{
            background:#fff;
            padding:40px;
            border-radius:25px;
            box-shadow:0 20px 50px rgba(0,0,0,.08);
        }

        .login-logo{
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

        .login-title{
            font-weight:700;
            margin-top:20px;
        }

        .login-subtitle{
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

        .login-btn{
            background:var(--orange);
            color:#fff;
            padding:12px;
            border:none;
            border-radius:12px;
            font-weight:600;
        }

        .login-btn:hover{
            background:#f06f00;
            color:#fff;
        }

        .forgot-link{
            color:var(--orange);
            text-decoration:none;
        }

        .divider{
            text-align:center;
            margin:25px 0;
            position:relative;
        }

        .divider::before{
            content:'';
            position:absolute;
            left:0;
            right:0;
            top:50%;
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

        .social-btn:hover{
            background:#fafafa;
        }

        .register-text{
            text-align:center;
            margin-top:25px;
            color:#666;
        }

        .register-text a{
            color:var(--orange);
            font-weight:600;
            text-decoration:none;
        }

        /* Mobile Only */
        @media (max-width:767.98px) and (max-height:700px){

            .login-section{
                padding:10px;
            }

            .login-card{
                padding:16px;
            }

            .login-logo{
                width:50px;
                height:50px;
                font-size:22px;
            }

            .login-title{
                font-size:1.5rem;
            }

            .login-subtitle{
                font-size:12px;
                margin-bottom:12px;
            }

            .mb-3{
                margin-bottom:10px !important;
            }

            .divider{
                margin:12px 0;
            }
        }
        
    </style>
   
    <!-- header section -->
    <section class="page-header d-none d-lg-block">
        <div class="overlay"></div>

        <div class="container position-relative"> 
            <h1 data-aos="fade-up">Login</h1>

            <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
                <li class="breadcrumb-item">
                    <a href="index.html">
                        <i class="bi bi-house-door-fill me-1"></i> Home
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    Login
                </li>
            </ol>
        </div>
    </section>
 
    <section class="login-section">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">

                <div class="col-lg-5 col-md-7 col-sm-10">

                    <div class="login-card">

                        <div class="text-center mb-4">
                            <div class="login-logo">
                                <i class="bi bi-stars"></i>
                            </div>

                            <h2 class="login-title" data-aos="fade-up">Welcome Back</h2>

                            <p class="login-subtitle" data-aos="fade-up">
                                Login to access your account and bookings.
                            </p>
                        </div>

                        <form data-aos="fade-up">

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control"
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
                                        placeholder="Enter your password">

                                    <button class="btn password-toggle"
                                        type="button"
                                        onclick="togglePassword()">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">

                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="checkbox">

                                    <label class="form-check-label">
                                        Remember Me
                                    </label>
                                </div>

                                <a href="forgot-password.html"
                                    class="forgot-link">
                                    Forgot Password?
                                </a>

                            </div>

                            <button type="submit"
                                class="btn login-btn w-100">
                                Login
                            </button>

                        </form>

                        <div class="divider" data-aos="fade-up">
                            <span>OR</span>
                        </div>

                        <div class="social-login" data-aos="fade-up">

                            <button class="btn social-btn">
                                <i class="bi bi-google"></i>
                                Continue with Google
                            </button>

                        </div>

                        <p class="register-text" data-aos="fade-up">
                            Don't have an account?
                            <a href="register.html">Register Now</a>
                        </p>

                    </div>

                </div>

            </div>
        </div>
    </section>
 
    <?php
        include_once ('elements/footer.php');
    ?>
