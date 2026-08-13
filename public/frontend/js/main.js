// Hero Swiper
if (document.querySelector('.hero-swiper')) {
  new Swiper('.hero-swiper', {
    loop: true,
    effect: 'fade',
    autoplay: { delay: 5000, disableOnInteraction: false },
    pagination: { el: '.hero-swiper .swiper-pagination', clickable: true },
    navigation: { nextEl: '.hero-swiper .swiper-button-next', prevEl: '.hero-swiper .swiper-button-prev' },
  });
}

// Services Swiper
if (document.querySelector('.services-swiper')) {
  new Swiper('.services-swiper', {
    loop: true,
    spaceBetween: 24,
    autoplay: { delay: 3500, disableOnInteraction: false },
    pagination: { el: '.services-swiper .swiper-pagination', clickable: true },
    breakpoints: {
      0:   { slidesPerView: 1.1 },
      576: { slidesPerView: 2 },
      992: { slidesPerView: 3 },
      1200:{ slidesPerView: 4 },
    },
  });
}

// Testimonials Swiper
if (document.querySelector('.testi-swiper')) {
  new Swiper('.testi-swiper', {
    loop: true,
    spaceBetween: 24,
    autoplay: { delay: 4500 },
    pagination: { el: '.testi-swiper .swiper-pagination', clickable: true },
    breakpoints: { 0:{slidesPerView:1}, 768:{slidesPerView:2}, 1100:{slidesPerView:3} },
  });
}

// Highlight active mobile nav by page
(function(){
  const path = location.pathname.split('/').pop() || 'index.php';
  document.querySelectorAll('.mobile-nav a[data-page]').forEach(a => {
    if (a.dataset.page === path) a.classList.add('active');
  });
  document.querySelectorAll('.navbar-custom .nav-link[data-page]').forEach(a => {
    if (a.dataset.page === path) a.classList.add('active');
  });
})();

// Booking form fake submit
const bookingForm = document.getElementById('bookingForm');
if (bookingForm) {
  bookingForm.addEventListener('submit', e => {
    e.preventDefault();
    document.getElementById('bookingSuccess').classList.remove('d-none');
    bookingForm.reset();
    window.scrollTo({ top: bookingForm.offsetTop - 80, behavior: 'smooth' });
  });
}
const contactForm = document.getElementById('contactForm');
if (contactForm) {
  contactForm.addEventListener('submit', e => {
    e.preventDefault();
    document.getElementById('contactSuccess').classList.remove('d-none');
    contactForm.reset();
  });
} 



 var swiper = new Swiper(".clientsSwiper", {
    loop:true,
    speed:1000,
    autoplay:{
        delay:2500,
        disableOnInteraction:false,
    },

    pagination:{
        el:".clientsSwiper .swiper-pagination",
        clickable:true,
    },

    breakpoints:{
        0:{
            slidesPerView:2,
            spaceBetween:15,
        },
        576:{
            slidesPerView:3,
            spaceBetween:20,
        },
        768:{
            slidesPerView:4,
            spaceBetween:25,
        },
        1200:{
            slidesPerView:5,
            spaceBetween:30,
        }
    }
});  
 

const scrollTopBtn = document.getElementById("scrollTopBtn");

window.addEventListener("scroll", function() {

    if (window.pageYOffset > 300) {
        scrollTopBtn.classList.add("show");
    } else {
        scrollTopBtn.classList.remove("show");
    }

});

scrollTopBtn.addEventListener("click", function() {

    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });

}); 