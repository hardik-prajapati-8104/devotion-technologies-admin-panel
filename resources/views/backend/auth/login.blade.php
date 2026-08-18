<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Devotion Technology</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ url('public/backend/css/style.css') }}">
</head>
<body>

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="brand-mark">
                <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">  
                    <img src="{{ url('public/frontend/images/Work_home_sefty_solution-header.png') }}" alt="Company Logo" class="me-2" width="70px;" height="70px;">
                    <span class="fs-3 text-dark" style="font-style: poppins, sans-serif; text-align:start;">
                        <b>WORK HOME</b>
                        <br>
                        <b>SAFETY SOLUTION</b>
                    </span>
                </a>
                {{-- <img src="{{ url('public/backend/images/devotion-technology.png') }}" alt="Devotion Technology" width="100%" onerror="this.style.display='none'"> --}}
                <h5 class="fw-semibold mt-2 mb-0">Admin Panel Login</h5> 
            </div>

            @include('backend.layouts.partials.messages')

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label small fw-medium">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-medium">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label small" for="remember">Remember me</label>
                    </div>
                    <a href="#" class="small">Forgot password?</a>
                </div>
                @if(isset($settings) && $settings->recaptcha_enabled)
                   <div class="mb-3">
                       <div class="g-recaptcha" data-sitekey="{{ $settings->recaptcha_site_key }}"></div>

                       @error('recaptcha')
                           <div class="text-danger small mt-1">{{ $message }}</div>
                       @enderror
                   </div>
               @endif

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                </button>
 
            </form>
        </div>
    </div>
    @if(isset($settings) && $settings->recaptcha_enabled)
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
</body>
</html>
