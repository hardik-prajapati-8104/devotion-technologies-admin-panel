<!-- HERO -->
<section class="p-0" style="padding:0!important">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">

            @forelse ($banners as $banner)
                <div class="swiper-slide hero-slide" style="background-image:url('{{  url('storage/app/public/'.$banner->image)  }}')">
                    <div class="hero-logo">
                        <img src="{{ url('public/frontend/images/Work_home_sefty_solution-footer.png') }}" alt="Company Logo">
                    </div>

                    {{-- @if ($banner->title || $banner->subtitle || $banner->button_text)
                        <div class="hero-slide-content">
                            @if ($banner->title)
                                <h2 class="hero-slide-title">{{ $banner->title }}</h2>
                            @endif
                            @if ($banner->subtitle)
                                <p class="hero-slide-subtitle">{{ $banner->subtitle }}</p>
                            @endif
                            @if ($banner->button_text && $banner->button_link)
                                <a href="{{ $banner->button_link }}" class="btn btn-orange hero-slide-btn">{{ $banner->button_text }}</a>
                            @endif
                        </div>
                    @endif --}}
                </div>
            @empty
                {{-- Fallback so the homepage never shows an empty slider if no
                    banners have been added yet in the admin panel. --}}
                <div class="swiper-slide hero-slide" style="background-image:url('{{ url('public/frontend/images/hero1.jpg') }}')">
                    <div class="hero-logo">
                        <img src="{{ url('public/frontend/images/Work_home_sefty_solution-header.png') }}" alt="Company Logo">
                    </div>
                </div>
            @endforelse

        </div>

        <style>
            
        

            .hero-slide {
                background-size: 100% auto;
                background-position: top center;
                background-repeat: no-repeat;
            }

            .hero-logo {
                position: absolute;
                top: 10px;
                left: 10px;
                width: 60px;
                height: 60px;
                background: transparent;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 12px;
                z-index: 10;
            }

            .hero-logo img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 50%;
            }

            .hero-slide-content {
                position: absolute;
                left: 40px;
                bottom: 80px;
                z-index: 10;
                color: #fff;
                max-width: 500px;
                text-shadow: 0 2px 8px rgba(0,0,0,.5);
            }

            .hero-slide-title {
                font-weight: 700;
                margin-bottom: 8px;
            }

            .hero-slide-subtitle {
                margin-bottom: 16px;
                opacity: .95;
            }
        </style>
        <div class="swiper-pagination"></div>
    </div>

    <!-- Search bar overlay -->
    {{-- <div class="container search-wrapper">
        <form class="search-bar">

            <div class="search-input">
                <i class="bi bi-search"></i>
                <input type="text"
                    class="form-control"
                    placeholder="Search Your Services">
            </div>

            <button type="submit" class="btn btn-orange search-btn">
                <i class="bi bi-search"></i>
            </button>

        </form>
    </div> --}}
    @include('frontend.elements.service-search')
    <!-- Company Logo -->
    <div class="container">
        <div class="company-logo">
            <img 
                src="{{ url('public/frontend/images/work-home-full-logo.png') }}"
                alt="Work Home Safety Solution"
                class="company-logo-img"
            >
        </div>
    </div>
    <style>
                .company-logo {
                    width: 100%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    padding: 20px 15px;
                }

                .company-logo-img {
                    display: block;
                    width: 100%;
                    max-width: 400px;
                    height: auto;
                    object-fit: contain;
                }

                /* Tablet */
                @media (max-width: 768px) {
                    .company-logo-img {
                        max-width: 320px;
                    }

                    .company-logo {
                        padding: 15px 10px;
                    }
                }

                /* Mobile */
                @media (max-width: 480px) {
                    .company-logo-img {
                        max-width: 280px;
                    }

                    .company-logo {
                        padding: 12px 10px;
                    }
                }
    </style>
  
</section>
 