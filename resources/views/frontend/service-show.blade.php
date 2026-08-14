{{-- resources/views/frontend/services/show.blade.php --}}

@extends('frontend.layouts.app')

@section('title', $service->seo_title ?: $service->name)

@push('meta')

    @if ($service->meta_description)
        <meta name="description" content="{{ $service->meta_description }}">
    @endif

    <meta property="og:title" content="{{ $service->seo_title ?: $service->name }}">

    @if ($service->meta_description)
        <meta property="og:description" content="{{ $service->meta_description }}">
    @endif

    @if ($service->og_image)
        <meta
            property="og:image"
            content="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($service->og_image) }}"
        >
    @endif

@endpush


@section('content')

    {{-- Hero --}}
    <div class="service-hero"style="background-image: url('{{ url('public/frontend/images/s1.jpg') }}');">
        <div class="service-hero-overlay">

            <div class="container">

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb service-breadcrumb">

                        <li class="breadcrumb-item">
                            <a href="{{ url('/') }}">Home</a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="{{ route('services') }}">Services</a>
                        </li>

                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $service->name }}
                        </li>

                    </ol>
                </nav>

                <h1
                    class="service-hero-title"
                    data-aos="fade-up"
                >
                    {{ $service->name }}
                </h1>

                @if ($service->short_description)
                    <p
                        class="service-hero-subtitle"
                        data-aos="fade-up"
                        data-aos-delay="100"
                    >
                        {{ $service->short_description }}
                    </p>
                @endif

                <a
                    href="#bookForm"
                    class="btn btn-orange btn-lg mt-3"
                    data-aos="fade-up"
                    data-aos-delay="200"
                >
                    @if ($service->icon)
                        <i class="{{ $service->icon }} me-2"></i>
                    @endif

                    Book This Service
                </a>

            </div>

        </div>
    </div>

    <style>
        .service-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 1.5rem auto;
            border-radius: 8px;
        }

        .service-content figure {
            max-width: 100%;
            margin: 1.5rem 0;
        }

        .service-content table {
            max-width: 100%;
            overflow-x: auto;
            display: block;
        }
    </style>

    {{-- Main Content --}}
    <div class="container py-5">

        <div class="row g-5">

            {{-- Main Content --}} 
            <div class="col-lg-8">

                <div class="service-content" data-aos="fade-up">

                    @if ($service->featured_image)
                        <div class="service-featured-image mb-4">
                            <img 
                                src="{{ asset('storage/app/public/' . $service->featured_image) }}" 
                                alt="{{ $service->name }}" 
                                class="img-fluid rounded shadow-sm w-100"
                                style="object-fit: cover; max-height: 450px;"
                                loading="lazy"
                            >
                        </div>
                    @endif

                    @if ($service->full_description)

                        {!! $service->full_description !!}

                    @else

                        <p class="text-muted">
                            {{ $service->short_description }}
                        </p>

                    @endif

                </div>

            </div>


            {{-- Booking Form --}}
            <div class="col-lg-4">

                <div class="service-book-card" id="bookForm" data-aos="fade-up">

                    <h5 class="mb-1">
                        Book {{ $service->name }}
                    </h5>

                    <p class="text-muted small mb-3">
                        Fill in your details and we'll get back to you shortly.
                    </p>


                    <form action="#" method="POST">

                        @csrf

                        <input type="hidden" name="service_id" value="{{ $service->id }}"> 
                        <input type="hidden" name="subject" value="Booking request: {{ $service->name }}"> 

                        {{-- Category --}}
                        <div class="mb-3">

                            <label class="form-label small fw-medium">Service Category</label>
                            <select name="service_category_id" class="form-select @error('service_category_id') is-invalid @enderror" required>
                                <option value="" disabled {{ old('service_category_id') ? '' : 'selected' }}>Select a Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ (old('service_category_id', $service->service_category_id) == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_category_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        {{-- Name --}}
                        <div class="mb-3">

                            <label class="form-label small fw-medium"> Full Name</label>
                            <input type="text" name="name" placeholder="Enter Your Full Name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>
 
                        {{-- Email --}}
                        <div class="mb-3">

                            <label class="form-label small fw-medium">Email</label>
                            <input type="email" placeholder="Enter Your Email Address" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>
 
                        {{-- Phone --}}
                        <div class="mb-3">

                            <label class="form-label small fw-medium">Phone</label>
                            <input type="tel" placeholder="Enter Your Phone Number" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>
 
                        {{-- Preferred Date --}}
                        <div class="mb-3">

                            <label class="form-label small fw-medium">Preferred Date</label>
                            <input type="date" name="preferred_date" class="form-control @error('preferred_date') is-invalid @enderror" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d') }}"> 
                            @error('preferred_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>
 
                        {{-- Message --}}
                        <div class="mb-3">

                            <label class="form-label small fw-medium">Message</label> 
                            <textarea name="message" rows="3" class="form-control @error('message') is-invalid @enderror" placeholder="Anything we should know before we arrive?">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <button type="submit" class="btn btn-orange w-100">Request Booking</button>

                    </form>

                </div>

            </div>

        </div>


        {{-- Related Services --}}
        @if ($relatedServices->isNotEmpty())

            <div class="mt-5 pt-4 border-top">

                <h4 class="mb-4" data-aos="fade-up">Related Services</h4>
                <div class="row g-4">

                    @foreach ($relatedServices as $related)

                        <div class="col-md-6 col-lg-4" data-aos="fade-up">

                            <div class="service-card">

                                <div class="img" style="background-image: url('{{ $related->featured_image ? url('storage/app/public/' . $related->featured_image) : url('public/frontend/images/s1.jpg') }}');"></div>

                                <div class="body">

                                    <h5>
                                        {{ $related->name }}
                                    </h5>

                                    <p class="text-muted">
                                        {{ $related->short_description }}
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center">

                                        <a href="{{ route('services.show', $related->slug) }}" class="btn btn-sm btn-orange">
                                            Book
                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        @endif

    </div>


    <style>

        .service-hero {
            position: relative;
            background-size: cover;
            background-position: center;
            min-height: 340px;
            display: flex;
            align-items: flex-end;
        }

        .service-hero-overlay {
            width: 100%;
            background: linear-gradient(
                180deg,
                rgba(0, 0, 0, .15) 0%,
                rgba(0, 0, 0, .65) 100%
            );
            padding: 60px 0 40px;
        }

        .service-breadcrumb {
            --bs-breadcrumb-divider-color: #fff;
        }

        .service-breadcrumb .breadcrumb-item,
        .service-breadcrumb .breadcrumb-item a {
            color: rgba(255, 255, 255, .8);
        }

        .service-breadcrumb .breadcrumb-item.active {
            color: #fff;
        }

        .service-hero-title {
            color: #fff;
            font-weight: 700;
            margin: 12px 0 6px;
        }

        .service-hero-subtitle {
            color: rgba(255, 255, 255, .9);
            max-width: 600px;
        }

        .service-content h2,
        .service-content h3 {
            margin-top: 1.5rem;
        }

        .service-content p {
            line-height: 1.7;
        }

        .service-book-card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
            position: sticky;
            top: 100px;
        }

    </style>

@endsection

 