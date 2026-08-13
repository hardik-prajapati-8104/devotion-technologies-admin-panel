<?php
	include_once ('elements/header.php'); 
?>

    <style>  
        .gallery-section{ background:#fffaf5; }

        .gallery-badge{
            display:inline-block;
            padding:8px 18px;
            background:rgba(255,122,0,.1);
            color:var(--orange);
            border-radius:50px;
            font-weight:600;
            margin-bottom:15px;
        }

        .gallery-title{
            font-size:clamp(2rem,4vw,3rem);
            font-weight:700;
            color:#222;
        }

        .gallery-desc{
            color:#666;
            max-width:600px;
            margin:auto;
        }

        .gallery-card{
            position:relative;
            overflow:hidden;
            border-radius:20px;
            background:#fff;
            cursor:pointer;
            box-shadow:0 10px 25px rgba(0,0,0,.08);
        }

        .gallery-card img{
            width:100%;
            height:260px;
            object-fit:cover;
            transition:.5s;
            display:block;
        }

        .gallery-card::before{
            content:"";
            position:absolute;
            inset:0;
            background:rgba(255,122,0,.75);
            opacity:0;
            transition:.4s;
            z-index:1;
        }

        .gallery-card::after{
            content:"\F52A";
            font-family:"bootstrap-icons";
            position:absolute;
            top:50%;
            left:50%;
            transform:translate(-50%,-50%);
            color:#fff;
            font-size:34px;
            opacity:0;
            transition:.4s;
            z-index:2;
        }

        .gallery-card:hover::before,
        .gallery-card:hover::after{ opacity:1; }
        .gallery-card:hover img{ transform:scale(1.1); }

        @media(max-width:767px){
            .gallery-section{ padding-top:30px !important; padding-bottom:30px !important; }
            .gallery-card{ border-radius:14px; }
            .gallery-card img{ height:160px; }
            .gallery-title{ font-size:28px; }
        }

        /* ============================
        SPLIT-PANEL LIGHTBOX POPUP
        ============================ */
        .lightbox-overlay{
            position:fixed;
            inset:0;
            background:rgba(20,14,8,.85);
            backdrop-filter:blur(6px);
            display:flex;
            align-items:center;
            justify-content:center;
            z-index:2000;
            opacity:0;
            visibility:hidden;
            transition:opacity .35s ease;
            padding:24px;
        }

        .lightbox-overlay.active{ opacity:1; visibility:visible; }

        .lightbox-modal{
            display:flex;
            max-width:960px;
            width:100%;
            max-height:85vh;
            background:#fff;
            border-radius:22px;
            overflow:hidden;
            box-shadow:0 30px 70px rgba(0,0,0,.45);
            transform:scale(.92) translateY(10px);
            opacity:0;
            transition:transform .35s ease, opacity .35s ease;
        }

        .lightbox-overlay.active .lightbox-modal{
            transform:scale(1) translateY(0);
            opacity:1;
        }

        .lightbox-image-pane{
            flex:1.4;
            position:relative;
            background:#111;
            min-width:0;
        }

        .lightbox-image-pane img{
            width:100%;
            height:100%;
            object-fit:cover;
            display:block;
        }

        .lightbox-nav{
            position:absolute;
            top:50%;
            transform:translateY(-50%);
            width:42px;
            height:42px;
            border-radius:50%;
            background:rgba(0,0,0,.35);
            backdrop-filter:blur(4px);
            border:1px solid rgba(255,255,255,.3);
            color:#fff;
            font-size:18px;
            display:flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            transition:.25s;
            z-index:3;
        }

        .lightbox-nav:hover{ background:var(--orange); border-color:var(--orange); }
        .lightbox-prev{ left:16px; }
        .lightbox-next{ right:16px; }

        .lightbox-info-pane{
            flex:1;
            display:flex;
            flex-direction:column;
            padding:38px 34px;
            min-width:280px;
        }

        .lightbox-icon-badge{
            width:38px;
            height:38px;
            border-radius:50%;
            background:rgba(255,122,0,.12);
            color:var(--orange);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:17px;
            margin-bottom:20px;
        }

        .lightbox-eyebrow{
            color:var(--orange);
            font-weight:600;
            font-size:13px;
            letter-spacing:.5px;
            text-transform:uppercase;
            margin-bottom:10px;
        }

        .lightbox-info-title{
            font-size:26px;
            font-weight:700;
            color:var(--ink);
            line-height:1.25;
            margin-bottom:14px;
        }

        .lightbox-info-desc{
            color:#666;
            font-size:15px;
            line-height:1.6;
            margin-bottom:22px;
        }

        .lightbox-meta{
            display:flex;
            align-items:center;
            gap:8px;
            font-size:13px;
            color:#999;
            margin-bottom:26px;
            padding-bottom:22px;
            border-bottom:1px solid #eee;
        }

        .lightbox-meta .lightbox-counter{
            color:var(--orange);
            font-weight:700;
        }

        .lightbox-book-btn{
            margin-top:auto;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            background:var(--orange);
            color:#fff;
            border:none;
            padding:14px 20px;
            border-radius:12px;
            font-weight:700;
            font-size:15px;
            cursor:pointer;
            transition:.25s;
            text-decoration:none;
        }

        .lightbox-book-btn:hover{
            background:var(--orange-dark);
            color:#fff;
            transform:translateY(-2px);
            box-shadow:0 10px 20px rgba(255,122,0,.35);
        }

        .lightbox-secondary-row{
            display:flex;
            gap:10px;
            margin-top:12px;
        }

        .lightbox-icon-btn{
            width:44px;
            height:44px;
            border-radius:10px;
            border:1px solid #eee;
            background:#fff;
            color:#555;
            display:flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            transition:.25s;
        }

        .lightbox-icon-btn:hover{
            border-color:var(--orange);
            color:var(--orange);
        }

        .lightbox-close{
            position:absolute;
            top:22px;
            right:22px;
            width:40px;
            height:40px;
            border-radius:50%;
            background:rgba(0,0,0,.4);
            color:#fff;
            border:none;
            font-size:18px;
            display:flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
            transition:.25s;
            z-index:4;
        }

        .lightbox-close:hover{ background:var(--orange); }

        @media(max-width:820px){
            .lightbox-modal{
                flex-direction:column;
                max-height:90vh;
                overflow-y:auto;
            }
            .lightbox-image-pane{ height:260px; flex:none; }
            .lightbox-info-pane{ padding:26px; }
            .lightbox-info-title{ font-size:21px; }
        }
    </style> 

    <!-- header section -->
    <section class="page-header">
        <div class="overlay"></div>

        <div class="container position-relative"> 
            <h1 data-aos="fade-up">Gallery</h1>

            <ol class="breadcrumb justify-content-center mb-0" data-aos="fade-up">
                <li class="breadcrumb-item">
                    <a href="index.html">
                        <i class="bi bi-house-door-fill me-1"></i> Home
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    Gallery
                </li>
            </ol>
        </div>
    </section> 

    <!-- GALLERY SECTION -->
    <section class="gallery-section py-5">
        <div class="container">

            <div class="text-center mb-5">
                <span class="gallery-badge" data-aos="fade-up">Our Work</span>
                <h2 class="gallery-title" data-aos="fade-up">Safety Solutions Gallery</h2>
                <p class="gallery-desc" data-aos="fade-up">
                    Explore our completed grill safety installations and solutions designed to protect homes with quality, reliability, and expert craftsmanship.
                </p>
            </div>

            <div class="row g-4" id="galleryRow">

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="gallery-card"
                        data-title="Deep Kitchen Clean"
                        data-desc="A full top-to-bottom kitchen detail — degreasing surfaces, sanitizing counters, and leaving appliances spotless inside and out.">
                        <img src="https://picsum.photos/seed/s1/700/700" class="img-fluid" alt="Deep kitchen clean">
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="gallery-card"
                        data-title="Spotless Living Room"
                        data-desc="Dust-free surfaces, freshened upholstery, and streak-free windows for a living space that feels brand new.">
                        <img src="https://picsum.photos/seed/s2/700/700" class="img-fluid" alt="Spotless living room">
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="gallery-card"
                        data-title="Bathroom Sanitation"
                        data-desc="Complete disinfection of tiles, grout, and fixtures using hospital-grade products safe for your whole family.">
                        <img src="https://picsum.photos/seed/s3/700/700" class="img-fluid" alt="Bathroom sanitation">
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="gallery-card"
                        data-title="Office Deep Clean"
                        data-desc="After-hours commercial cleaning covering desks, common areas, and high-touch surfaces for a healthier workplace.">
                        <img src="https://picsum.photos/seed/s4/700/700" class="img-fluid" alt="Office deep clean">
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="gallery-card"
                        data-title="Window & Glass Polish"
                        data-desc="Interior and exterior glass cleaning that leaves every pane crystal clear, streak-free, and spot-free.">
                        <img src="https://picsum.photos/seed/s5/700/700" class="img-fluid" alt="Window and glass polish">
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="gallery-card"
                        data-title="Carpet Shampooing"
                        data-desc="Deep-extraction carpet cleaning that lifts embedded dirt and odors, restoring softness and color.">
                        <img src="https://picsum.photos/seed/s6/700/700" class="img-fluid" alt="Carpet shampooing">
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="gallery-card"
                        data-title="Post-Construction Cleanup"
                        data-desc="Dust and debris removal after renovation work, leaving the space move-in ready from ceiling to floor.">
                        <img src="https://picsum.photos/seed/s7/700/700" class="img-fluid" alt="Post construction cleanup">
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="gallery-card"
                        data-title="Move-Out Cleaning"
                        data-desc="End-of-lease cleaning built to satisfy landlord checklists and get your full deposit back.">
                        <img src="https://picsum.photos/seed/s8/700/700" class="img-fluid" alt="Move out cleaning">
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- SPLIT-PANEL LIGHTBOX MARKUP -->
    <div class="lightbox-overlay" id="lightboxOverlay">
        <button class="lightbox-close" id="lightboxClose" aria-label="Close"><i class="bi bi-x-lg"></i></button>

        <div class="lightbox-modal">

            <div class="lightbox-image-pane">
                <button class="lightbox-nav lightbox-prev" id="lightboxPrev" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                <img src="" alt="" id="lightboxImg">
                <button class="lightbox-nav lightbox-next" id="lightboxNext" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
            </div>

            <div class="lightbox-info-pane">
                <div class="lightbox-icon-badge"><i class="bi bi-stars"></i></div>
                <div class="lightbox-eyebrow">Cleaning Service</div>
                <h3 class="lightbox-info-title" id="lightboxTitle"></h3>
                <p class="lightbox-info-desc" id="lightboxDesc"></p>

                <div class="lightbox-meta">
                    <span class="lightbox-counter" id="lightboxCounter"></span>
                    <span>of our recent projects</span>
                </div>

                <a href="booking.php" class="lightbox-book-btn" id="lightboxBookBtn">
                    <i class="bi bi-calendar-check"></i> Book Now
                </a>
            </div>

        </div>
    </div>

    <script>
        (function(){
            const cards      = Array.from(document.querySelectorAll('.gallery-card'));
            const overlay    = document.getElementById('lightboxOverlay');
            const imgEl      = document.getElementById('lightboxImg');
            const titleEl    = document.getElementById('lightboxTitle');
            const descEl     = document.getElementById('lightboxDesc');
            const counterEl  = document.getElementById('lightboxCounter');
            const btnClose   = document.getElementById('lightboxClose');
            const btnPrev    = document.getElementById('lightboxPrev');
            const btnNext    = document.getElementById('lightboxNext');

            let currentIndex = 0;

            function openLightbox(index){
                currentIndex = index;
                updateLightbox();
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function updateLightbox(){
                const card = cards[currentIndex];
                const img  = card.querySelector('img');
                imgEl.src   = img.src;
                imgEl.alt   = img.alt;
                titleEl.textContent = card.dataset.title || img.alt || '';
                descEl.textContent  = card.dataset.desc || '';
                counterEl.textContent = (currentIndex + 1) + ' / ' + cards.length;
            }

            function closeLightbox(){
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            function showPrev(){
                currentIndex = (currentIndex - 1 + cards.length) % cards.length;
                updateLightbox();
            }

            function showNext(){
                currentIndex = (currentIndex + 1) % cards.length;
                updateLightbox();
            }

            cards.forEach((card, i) => {
                card.addEventListener('click', () => openLightbox(i));
            });

            btnClose.addEventListener('click', closeLightbox);
            btnPrev.addEventListener('click', showPrev);
            btnNext.addEventListener('click', showNext);

            overlay.addEventListener('click', (e) => {
                if(e.target === overlay) closeLightbox();
            });

            document.addEventListener('keydown', (e) => {
                if(!overlay.classList.contains('active')) return;
                if(e.key === 'Escape') closeLightbox();
                if(e.key === 'ArrowLeft') showPrev();
                if(e.key === 'ArrowRight') showNext();
            });
        })();
    </script>

<?php
    include_once ('elements/footer.php');
?>
