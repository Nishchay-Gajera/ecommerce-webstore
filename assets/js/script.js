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

    // --- Live Search Functionality ---
    const searchInput = document.getElementById('searchInput');
    const searchCategory = document.getElementById('searchCategory');
    const searchResultsContainer = document.getElementById('searchResults');
    let debounceTimer;

    if (searchInput && searchResultsContainer && searchCategory) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const query = this.value;
                const category = searchCategory.value;

                if (query.trim().length > 1) {
                    fetch(`ajax_search.php?query=${encodeURIComponent(query)}&category=${encodeURIComponent(category)}`)
                        .then(response => response.json())
                        .then(data => {
                            displaySearchResults(data, query);
                        })
                        .catch(error => {
                            console.error('Error fetching search results:', error);
                            searchResultsContainer.style.display = 'none';
                        });
                } else {
                    searchResultsContainer.innerHTML = '';
                    searchResultsContainer.style.display = 'none';
                }
            }, 300); // 300ms debounce delay
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResultsContainer.contains(e.target)) {
                searchResultsContainer.style.display = 'none';
            }
        });

        // Also hide on focus out if not clicking on results
        searchInput.addEventListener('blur', function() {
            // A small delay is needed to allow clicks on search results to register
            setTimeout(() => {
                if (!searchResultsContainer.matches(':hover')) {
                    searchResultsContainer.style.display = 'none';
                }
            }, 200);
        });
        
        // Show results on focus if there's text
         searchInput.addEventListener('focus', function() {
            if (this.value.trim().length > 1 && searchResultsContainer.innerHTML !== '') {
                 searchResultsContainer.style.display = 'block';
            }
        });
    }

    function displaySearchResults(products, query) {
        searchResultsContainer.innerHTML = ''; // Clear previous results

        if (products.length > 0) {
            products.forEach(product => {
                const item = document.createElement('a');
                item.href = `product_details.php?id=${product.id}`;
                item.classList.add('search-result-item');

                const imageUrl = product.image_url ? `uploads/product-image/${product.image_url}` : 'https://placehold.co/50x60/f0f0f0/ccc?text=N/A';

                item.innerHTML = `
                    <img src="${imageUrl}" alt="${escapeHTML(product.name)}" class="search-result-image">
                    <div class="search-result-info">
                        <span class="search-result-name">${escapeHTML(product.name)}</span>
                        <span class="search-result-price">₹${parseFloat(product.price).toFixed(2)}</span>
                    </div>
                `;
                searchResultsContainer.appendChild(item);
            });
            
            // Add a "View all results" link
            const viewAll = document.createElement('a');
            viewAll.href = `search.php?query=${encodeURIComponent(query)}`;
            viewAll.classList.add('view-all-results');
            viewAll.textContent = 'View all results';
            searchResultsContainer.appendChild(viewAll);

            searchResultsContainer.style.display = 'block';
        } else {
            searchResultsContainer.innerHTML = '<div class="search-result-item">No products found.</div>';
            searchResultsContainer.style.display = 'block';
        }
    }
    
    function escapeHTML(str) {
        return str.replace(/[&<>"']/g, function(match) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[match];
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

    