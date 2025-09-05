document.addEventListener('DOMContentLoaded', function () {

    // --- Hamburger Menu Logic ---
    const hamburger = document.querySelector('.hamburger-menu');
    const mainNav = document.querySelector('.main-nav');
    const body = document.body;

    if (hamburger && mainNav) {
        hamburger.addEventListener('click', () => {
            mainNav.classList.toggle('is-open');
            body.classList.toggle('no-scroll'); // Prevents scrolling when menu is open
        });
    }

    // --- Intersection Observer for Animations ---
    const animatedElements = document.querySelectorAll('.fade-in-up');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });

    animatedElements.forEach(el => {
        observer.observe(el);
    });

    // --- Hero Carousel Initialization ---
    const heroCarousel = document.querySelector('.hero-swiper');
    if (heroCarousel) {
        const heroSwiper = new Swiper(heroCarousel, {
            loop: true,
            autoplay: {
                delay: 5000, // 5 seconds
                disableOnInteraction: false, // Keep autoplay running after user interaction
                pauseOnMouseEnter: true, // Pause on hover
            },
            effect: 'fade', // Smooth fade transition between slides
            fadeEffect: {
                crossFade: true
            },
            speed: 1000, // Transition speed in milliseconds
            navigation: {
                nextEl: '.hero-button-next',
                prevEl: '.hero-button-prev',
            },
            pagination: {
                el: '.hero-pagination',
                clickable: true,
                dynamicBullets: false,
            },
            // Event handlers
            on: {
                // Restart autoplay after any interaction
                slideChange: function () {
                    this.autoplay.start();
                },
                // Handle touch/drag events
                touchEnd: function () {
                    this.autoplay.start();
                },
                // Handle navigation click events
                navigationNext: function () {
                    this.autoplay.start();
                },
                navigationPrev: function () {
                    this.autoplay.start();
                },
                // Handle pagination click events
                paginationClick: function () {
                    this.autoplay.start();
                }
            }
        });

        // Additional manual event listeners for navigation arrows
        const nextBtn = document.querySelector('.hero-button-next');
        const prevBtn = document.querySelector('.hero-button-prev');
        const pagination = document.querySelectorAll('.hero-pagination .swiper-pagination-bullet');

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                setTimeout(() => {
                    heroSwiper.autoplay.start();
                }, 100);
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                setTimeout(() => {
                    heroSwiper.autoplay.start();
                }, 100);
            });
        }

        // Handle pagination clicks
        pagination.forEach(bullet => {
            bullet.addEventListener('click', function() {
                setTimeout(() => {
                    heroSwiper.autoplay.start();
                }, 100);
            });
        });
    }

    // --- Product Carousels Initialization ---
    const productCarousels = document.querySelectorAll('.product-carousel');
    
    productCarousels.forEach(carousel => {
        new Swiper(carousel, {
            loop: true,
            slidesPerView: 1, // Default for mobile
            spaceBetween: 20,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            // Responsive breakpoints
            breakpoints: {
                // when window width is >= 640px
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20
                },
                // when window width is >= 768px
                768: {
                    slidesPerView: 3,
                    spaceBetween: 30
                },
                // when window width is >= 1024px
                1024: {
                    slidesPerView: 4,
                    spaceBetween: 30
                }
            }
        });
    });

});