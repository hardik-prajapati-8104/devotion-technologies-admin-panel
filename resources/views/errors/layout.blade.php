@php
    $code = $code ?? '500';
    $title = $title ?? 'Something went wrong';
    $message = $message ?? 'An unexpected error occurred. Please try again.';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }} — {{ $title }} | Devotion Technology</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-code {
            font-size: 96px;
            font-weight: 700;
            color: #aa8038;
            line-height: 1;
        }
        .error-card {
            max-width: 480px;
            text-align: center;
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">{{ $code }}</div>
        <h4 class="fw-semibold mt-2 mb-2">{{ $title }}</h4>
        <p class="text-muted mb-4">{{ $message }}</p>
        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : url('/') }}" class="btn btn-primary">
            <i class="bi bi-house-door me-1"></i> Back to Dashboard
        </a>
    </div>
</body>
</html>
