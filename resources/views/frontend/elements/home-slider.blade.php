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
                position: relative;
                background-size: cover;
                background-position: center;
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
                object-fit: contain;
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
      <div class="company-logo-section">
          <img src="public/frontend/images/Work_home_sefty_solution-header.png" alt="Company Logo">
          <h5>Work Home <span style="color: var(--orange);">Safety</span>Solution</h5>
          <p>Protection & Safety Systems</p>
      </div>
  </div>
  
</section>

<style>

    .company-logo-section{
        text-align:center;
        margin:25px 0 15px;
    }

    .company-logo-section img{
        width:90px;
        height:90px;
        object-fit:contain;
        background:#fff;
        border-radius:50%;
        padding:10px;
        box-shadow:0 10px 25px rgba(0,0,0,.12);
        border:3px solid #ff7a00;
    }

    .company-logo-section h5{
        margin-top:12px;
        margin-bottom:4px;
        font-weight:700;
        color:#222;
    }

    .company-logo-section p{
        margin:0;
        color:#777;
        font-size:14px;
    }

    @media(min-width:992px){

        .company-logo-section{
            margin:40px 0 25px;
        }

        .company-logo-section img{
            width:120px;
            height:120px;
        }

        .company-logo-section h5{
            font-size:24px;
        }
    }
</style>
