@extends('frontend.layouts.app')
 
@section('content')

<!-- header section -->
<section class="page-header">
    <div class="overlay"></div>

    <div class="container position-relative"> 
        <h1 data-aos="fade-up">Our Services</h1>

        <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">
                    <i class="bi bi-house-door-fill me-1"></i> Home
                </a>
            </li>

            <li class="breadcrumb-item active">
                Our Services
            </li>
        </ol>
    </div>
</section>

<style>
      
    .services-filter-section{
        background:#fff;
    }

    .services-filter-wrapper{
        display:flex;
        gap:15px;
        overflow-x:auto;
        scrollbar-width:none;
        -ms-overflow-style:none;
        padding-bottom:5px;
    }

    .services-filter-wrapper::-webkit-scrollbar{
        display:none;
    }

    .service-filter{
        min-width:max-content;
        display:flex;
        align-items:center;
        gap:10px;
        padding:14px 22px;
        background:#fff;
        border:1px solid #eee;
        border-radius:50px;
        cursor:pointer;
        transition:.3s;
        white-space:nowrap;
        font-weight:500;
    }

    .service-filter i{
        color:var(--orange);
        font-size:18px;
    }

    .service-filter:hover,
    .service-filter.active{
        background:var(--orange);
        border-color:var(--orange);
        color:#fff;
        /* box-shadow:0 8px 20px rgba(255,122,0,.25); */
    }

    .service-filter:hover i,
    .service-filter.active i{
        color:#fff;
    }

    @media(max-width:767px){

        section{
          padding: 20px 0 !important;
        }

        .services-filter-wrapper{
            gap:10px;
        }

        .service-filter{
            padding:12px 18px;
            font-size:14px;
        }

        .service-filter i{
            font-size:16px;
        }
    }

</style>

<!-- Services Search Bar -->
<section class="services-filter-section py-4">
    <div class="container">
        <div class="services-filter-wrapper">

            <div class="service-filter active" data-aos="fade-up" data-filter="all">
                <i class="bi bi-grid"></i>
                <span>All</span>
            </div>

            @foreach ($categories as $category)
                <div class="service-filter" data-aos="fade-up" data-filter="{{ $category->slug }}">
                    <i class="bi {{ $category->icon ?? 'bi-trophy' }}"></i>
                    <span>{{ $category->name }}</span>
                </div>
            @endforeach

        </div>
    </div>
</section>

<section>
  <div class="container">

    <div class="text-center">
        <span class="eyebrow" data-aos="fade-up">What We Offer</span>
        <h2 class="section-title" data-aos="fade-up" style="font-size: 2rem;">Safety & Protection Solutions for Every Space</h2>
        <p class="section-sub" data-aos="fade-up">Reliable, customized solutions designed to protect your home, office, apartment, and commercial property while keeping your space safer, cleaner, and more comfortable.</p>
    </div>

    <div class="row g-4" id="services-grid">

            @foreach ($services as $service)

                <div class="col-md-6 col-lg-4 service-item"
                    data-aos="fade-up"
                    data-category="{{ $service->category->slug ?? '' }}">

                    <div class="service-card">
                        <a href="{{ route('services.show', $service->slug) }}" >
                            <div class="img"
                                style="background-image: url('{{ 
                                    $service->featured_image 
                                        ? url('storage/app/public/' . $service->featured_image) 
                                        : url('public/frontend/images/s1.jpg') 
                                }}');">
                            </div>
                        </a>

                        <div class="body">
                                
                            <a href="{{ route('services.show', $service->slug) }}" style="color: var(--orange);">
                                <h5>{{ $service->name }}</h5>
                            </a>

                            <p class="text-muted">
                                {{ $service->short_description }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center">

                                <a href="{{ route('services.show', $service->slug) }}"
                                class="btn btn-sm btn-orange">
                                    View Service
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach


            {{-- No services message --}}
            <div class="col-12 text-center py-5"
                id="no-services-msg"
                style="display: none;">

                <div class="no-services-content">

                    <i class="bi bi-info-circle"></i>

                    <h4>No Services Available</h4>

                    <p>
                        There are currently no services available in this category.
                        Please check another category.
                    </p>

                </div>

            </div>

        </div>
  </div>
</section>

<style>
    .no-services-content {
        max-width: 500px; 
        padding: 40px 25px;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 16px;
    }

    .no-services-content i {
        font-size: 42px;
        color: var(--orange);
        margin-bottom: 15px;
    }

    .no-services-content h4 {
        margin-bottom: 8px;
        font-weight: 700;
    }

    .no-services-content p {
        margin: 0;
        color: #777;
        line-height: 1.6;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const filters = document.querySelectorAll('.service-filter');
        const items = document.querySelectorAll('.service-item');
        const noMsg = document.getElementById('no-services-msg');

        function applyFilter(selected) {

            let visibleCount = 0;

            // Update active button
            filters.forEach(filter => {

                filter.classList.toggle(
                    'active',
                    filter.dataset.filter === selected
                );

            });


            // Filter services
            items.forEach(item => {

                const category = item.dataset.category;

                const match =
                    selected === 'all' ||
                    category === selected;

                if (match) {

                    item.style.display = '';

                    visibleCount++;

                } else {

                    item.style.display = 'none';

                }

            });


            // Show / hide no services message
            if (noMsg) {

                noMsg.style.display =
                    visibleCount === 0 ? 'block' : 'none';

            }

        }


        // Category click
        filters.forEach(filter => {

            filter.addEventListener('click', function () {

                applyFilter(this.dataset.filter);

            });

        });


        // Read category from URL
        const params = new URLSearchParams(
            window.location.search
        );

        const initial = params.get('category');


        // Apply URL category
        if (
            initial &&
            [...filters].some(
                filter => filter.dataset.filter === initial
            )
        ) {

            applyFilter(initial);

            document
                .getElementById('services-grid')
                ?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

        } else {

            // Default = All
            applyFilter('all');

        }

    });
</script>

 @endsection