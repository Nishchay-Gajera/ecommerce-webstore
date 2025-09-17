document.addEventListener('DOMContentLoaded', function () {
    console.log('Universal script loaded');

    // --- UNIVERSAL Mobile Hamburger Menu Logic ---
    function initializeMobileMenu() {
        const hamburger = document.querySelector('.hamburger-menu');
        const mainNav = document.querySelector('.main-nav');
        const navOverlay = document.querySelector('.nav-overlay');
        const body = document.body;

        console.log('Mobile menu elements:', {
            hamburger: !!hamburger,
            mainNav: !!mainNav,
            navOverlay: !!navOverlay,
            page: window.location.pathname
        });

        if (hamburger && mainNav) {
            // Remove any existing event listeners
            hamburger.replaceWith(hamburger.cloneNode(true));
            const freshHamburger = document.querySelector('.hamburger-menu');

            freshHamburger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Hamburger clicked on:', window.location.pathname);
                
                const isOpen = mainNav.classList.contains('is-open');
                
                if (isOpen) {
                    closeMobileMenu();
                } else {
                    openMobileMenu();
                }
            });
            
            // Close menu when overlay is clicked
            if (navOverlay) {
                navOverlay.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeMobileMenu();
                });
            }
            
            // Close menu when navigation link is clicked
            const navLinks = mainNav.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    closeMobileMenu();
                });
            });
            
            // Close menu on window resize if open
            window.addEventListener('resize', function() {
                if (window.innerWidth > 992 && mainNav.classList.contains('is-open')) {
                    closeMobileMenu();
                }
            });

            function openMobileMenu() {
                console.log('Opening mobile menu');
                mainNav.classList.add('is-open');
                if (navOverlay) navOverlay.classList.add('is-open');
                body.classList.add('no-scroll');
                
                // Change hamburger icon to X
                const icon = freshHamburger.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-times';
                }
            }

            function closeMobileMenu() {
                console.log('Closing mobile menu');
                mainNav.classList.remove('is-open');
                if (navOverlay) navOverlay.classList.remove('is-open');
                body.classList.remove('no-scroll');
                
                // Change X icon back to hamburger
                const icon = freshHamburger.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-bars';
                }
            }
            
        } else {
            console.error('Mobile menu elements not found on page:', window.location.pathname);
            console.error('Elements found:', {
                hamburger: document.querySelector('.hamburger-menu'),
                mainNav: document.querySelector('.main-nav'),
                navOverlay: document.querySelector('.nav-overlay')
            });
        }
    }

    // --- UNIVERSAL AJAX Search Functionality ---
    function initializeSearch() {
        // Try both possible search element IDs/selectors
        const searchInput = document.getElementById('searchInput') || document.querySelector('.search-input');
        const searchCategory = document.getElementById('searchCategory') || document.querySelector('.search-category');
        const searchButton = document.getElementById('searchButton') || document.querySelector('.search-button');
        const searchResults = document.getElementById('searchResults') || document.querySelector('.search-results-dropdown');
        
        let searchTimeout;
        let currentFocusIndex = -1;
        let isSearching = false;

        console.log('Search elements found:', {
            searchInput: !!searchInput,
            searchCategory: !!searchCategory,
            searchButton: !!searchButton,
            searchResults: !!searchResults,
            page: window.location.pathname
        });

        // Only initialize search if we have the basic elements
        if (searchInput && searchResults) {
            console.log('Initializing search functionality');
            
            // Input event listener for real-time search
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    hideSearchResults();
                    return;
                }

                showLoadingState();
                
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            });

            // Category change event
            if (searchCategory) {
                searchCategory.addEventListener('change', function() {
                    const query = searchInput.value.trim();
                    if (query.length >= 2) {
                        performSearch(query);
                    }
                });
            }

            // Search button click
            if (searchButton) {
                searchButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    const query = searchInput.value.trim();
                    if (query.length >= 2) {
                        const category = searchCategory ? searchCategory.value : '';
                        const searchUrl = `search.php?query=${encodeURIComponent(query)}${category ? '&category=' + encodeURIComponent(category) : ''}`;
                        window.location.href = searchUrl;
                    }
                });
            }

            // Enter key to search
            searchInput.addEventListener('keydown', function(e) {
                const items = searchResults.querySelectorAll('.search-result-item:not(.view-all-results)');
                
                switch(e.key) {
                    case 'Enter':
                        e.preventDefault();
                        if (currentFocusIndex >= 0 && items[currentFocusIndex]) {
                            items[currentFocusIndex].click();
                        } else {
                            const query = this.value.trim();
                            if (query.length >= 2) {
                                const category = searchCategory ? searchCategory.value : '';
                                const searchUrl = `search.php?query=${encodeURIComponent(query)}${category ? '&category=' + encodeURIComponent(category) : ''}`;
                                window.location.href = searchUrl;
                            }
                        }
                        break;
                        
                    case 'ArrowDown':
                        e.preventDefault();
                        currentFocusIndex = Math.min(currentFocusIndex + 1, items.length - 1);
                        updateFocusedItem(items);
                        break;
                        
                    case 'ArrowUp':
                        e.preventDefault();
                        currentFocusIndex = Math.max(currentFocusIndex - 1, -1);
                        updateFocusedItem(items);
                        break;
                        
                    case 'Escape':
                        hideSearchResults();
                        this.blur();
                        break;
                }
            });

            // Focus events
            searchInput.addEventListener('focus', function() {
                const query = this.value.trim();
                if (query.length >= 2 && searchResults.innerHTML.trim() !== '') {
                    showSearchResults();
                }
            });

            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && 
                    !searchResults.contains(e.target) && 
                    (!searchButton || !searchButton.contains(e.target)) &&
                    (!searchCategory || !searchCategory.contains(e.target))) {
                    hideSearchResults();
                }
            });

            searchResults.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            searchInput.addEventListener('blur', function() {
                setTimeout(() => {
                    if (!searchResults.matches(':hover')) {
                        hideSearchResults();
                    }
                }, 200);
            });

            // Perform AJAX search
            function performSearch(query) {
                if (isSearching) return;
                
                isSearching = true;
                const category = searchCategory ? searchCategory.value : '';
                
                const params = new URLSearchParams({ query: query });
                if (category) {
                    params.append('category', category);
                }

                fetch(`ajax_search.php?${params.toString()}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        displaySearchResults(data, query);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        showErrorState();
                    })
                    .finally(() => {
                        isSearching = false;
                    });
            }

            // Display search results
            function displaySearchResults(products, query) {
                searchResults.innerHTML = '';
                currentFocusIndex = -1;

                if (products.length === 0) {
                    searchResults.innerHTML = '<div class="search-no-results">No products found for "' + escapeHtml(query) + '"</div>';
                    showSearchResults();
                    return;
                }

                products.forEach(product => {
                    const item = createSearchResultItem(product);
                    searchResults.appendChild(item);
                });

                const viewAllLink = document.createElement('a');
                viewAllLink.href = `search.php?query=${encodeURIComponent(query)}${searchCategory && searchCategory.value ? '&category=' + encodeURIComponent(searchCategory.value) : ''}`;
                viewAllLink.className = 'view-all-results';
                viewAllLink.innerHTML = `<i class="fas fa-search"></i> View all ${products.length}+ results`;
                searchResults.appendChild(viewAllLink);

                showSearchResults();
            }

            function createSearchResultItem(product) {
                const item = document.createElement('a');
                item.href = `product_details.php?id=${product.id}`;
                item.className = 'search-result-item';

                const img = document.createElement('img');
                img.src = product.image_url ? `uploads/product-image/${product.image_url}` : 'https://placehold.co/50x60/f0f0f0/ccc?text=N/A';
                img.alt = product.name;
                img.className = 'search-result-image';
                img.onerror = function() {
                    this.src = 'https://placehold.co/50x60/f0f0f0/ccc?text=N/A';
                };

                const infoDiv = document.createElement('div');
                infoDiv.className = 'search-result-info';

                const nameSpan = document.createElement('span');
                nameSpan.className = 'search-result-name';
                nameSpan.textContent = product.name;

                const priceSpan = document.createElement('span');
                priceSpan.className = 'search-result-price';
                priceSpan.textContent = `₹${product.price}`;

                if (product.category_name) {
                    const categorySpan = document.createElement('span');
                    categorySpan.className = 'search-result-category';
                    categorySpan.textContent = product.category_name;
                    infoDiv.appendChild(categorySpan);
                }

                infoDiv.appendChild(nameSpan);
                infoDiv.appendChild(priceSpan);
                item.appendChild(img);
                item.appendChild(infoDiv);

                return item;
            }

            function showLoadingState() {
                if (searchResults) {
                    searchResults.innerHTML = `
                        <div class="search-loading">
                            <div class="search-loading-spinner"></div>
                            <span>Searching...</span>
                        </div>
                    `;
                    showSearchResults();
                }
            }

            function showErrorState() {
                if (searchResults) {
                    searchResults.innerHTML = '<div class="search-no-results">Something went wrong. Please try again.</div>';
                    showSearchResults();
                }
            }

            function showSearchResults() {
                if (searchResults) {
                    searchResults.classList.add('show');
                    searchResults.style.display = 'block';
                }
            }

            function hideSearchResults() {
                if (searchResults) {
                    searchResults.classList.remove('show');
                    setTimeout(() => {
                        if (searchResults) {
                            searchResults.style.display = 'none';
                        }
                    }, 300);
                    currentFocusIndex = -1;
                }
            }

            function updateFocusedItem(items) {
                items.forEach(item => item.classList.remove('keyboard-focus'));
                
                if (currentFocusIndex >= 0 && items[currentFocusIndex]) {
                    items[currentFocusIndex].classList.add('keyboard-focus');
                    items[currentFocusIndex].scrollIntoView({ block: 'nearest' });
                }
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        } else {
            console.log('Search not initialized - missing elements on page:', window.location.pathname);
        }
    }

    // --- Initialize Everything ---
    initializeMobileMenu();
    initializeSearch();

    // --- Initialize other components if they exist ---
    
    // Hero Carousel (only on pages that have it)
    const heroCarousel = document.querySelector('.hero-swiper');
    if (heroCarousel && typeof Swiper !== 'undefined') {
        const heroSwiper = new Swiper(heroCarousel, {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            speed: 1000,
            navigation: {
                nextEl: '.hero-button-next',
                prevEl: '.hero-button-prev',
            },
            pagination: {
                el: '.hero-pagination',
                clickable: true,
                dynamicBullets: false,
            }
        });
    }

    // Product Carousels (only on pages that have them)
    const productCarousels = document.querySelectorAll('.product-carousel');
    if (productCarousels.length > 0 && typeof Swiper !== 'undefined') {
        productCarousels.forEach(carousel => {
            new Swiper(carousel, {
                loop: true,
                slidesPerView: 1,
                spaceBetween: 20,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 30
                    },
                    1024: {
                        slidesPerView: 4,
                        spaceBetween: 30
                    }
                }
            });
        });
    }

    // Related Products Carousel (only on product details pages)
    const relatedProductsCarousel = document.querySelector('.related-products-carousel');
    if (relatedProductsCarousel && typeof Swiper !== 'undefined') {
        new Swiper(relatedProductsCarousel, {
            loop: true,
            slidesPerView: 1,
            spaceBetween: 20,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 30
                },
                1024: {
                    slidesPerView: 4,
                    spaceBetween: 30
                }
            }
        });
    }

    // Intersection Observer for Animations
    const animatedElements = document.querySelectorAll('.fade-in-up');
    if (animatedElements.length > 0) {
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
    }

    console.log('Universal script initialization complete for:', window.location.pathname);
});// Add this to your universal JavaScript file after the existing search initialization

// --- MOBILE SEARCH MODAL FUNCTIONALITY ---
function initializeMobileSearch() {
    const mobileSearchIcon = document.querySelector('.mobile-search-icon');
    const body = document.body;
    
    if (mobileSearchIcon) {
        console.log('Mobile search icon found, initializing mobile search');
        
        // Remove any existing href to prevent # in URL
        if (mobileSearchIcon.tagName === 'A') {
            mobileSearchIcon.removeAttribute('href');
        }
        
        // Create mobile search modal
        createMobileSearchModal();
        
        mobileSearchIcon.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Mobile search icon clicked');
            openMobileSearchModal();
        });
        
    } else {
        console.log('Mobile search icon not found');
    }
}

function createMobileSearchModal() {
    // Check if modal already exists
    if (document.getElementById('mobileSearchModal')) {
        return;
    }
    
    const modal = document.createElement('div');
    modal.id = 'mobileSearchModal';
    modal.className = 'mobile-search-modal';
    
    // Get categories for dropdown
    const desktopCategorySelect = document.querySelector('.search-category');
    let categoryOptions = '<option value="">All Products</option>';
    
    if (desktopCategorySelect) {
        const options = desktopCategorySelect.querySelectorAll('option');
        options.forEach(option => {
            if (option.value !== '') {
                categoryOptions += `<option value="${option.value}">${option.textContent}</option>`;
            }
        });
    }
    
    modal.innerHTML = `
        <div class="mobile-search-header">
            <button class="mobile-search-close" aria-label="Close Search">
                <i class="fas fa-times"></i>
            </button>
            <h3>Search Products</h3>
        </div>
        <div class="mobile-search-content">
            <form class="mobile-search-form" action="search.php" method="GET">
                <div class="mobile-search-input-container">
                    <input 
                        type="text" 
                        name="query" 
                        class="mobile-search-input" 
                        placeholder="Search for products..."
                        autocomplete="off"
                        required
                    >
                </div>
                <div class="mobile-search-category-container">
                    <select name="category" class="mobile-search-category">
                        ${categoryOptions}
                    </select>
                </div>
                <button type="submit" class="mobile-search-submit">
                    <i class="fas fa-search"></i>
                    Search
                </button>
            </form>
            <div id="mobileSearchResults" class="mobile-search-results"></div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add event listeners
    const closeBtn = modal.querySelector('.mobile-search-close');
    const searchInput = modal.querySelector('.mobile-search-input');
    const searchForm = modal.querySelector('.mobile-search-form');
    const searchResults = modal.querySelector('#mobileSearchResults');
    
    closeBtn.addEventListener('click', closeMobileSearchModal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeMobileSearchModal();
        }
    });
    
    // Escape key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeMobileSearchModal();
        }
    });
    
    // Add AJAX search to mobile input
    let mobileSearchTimeout;
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(mobileSearchTimeout);
        
        if (query.length < 2) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            return;
        }
        
        searchResults.innerHTML = `
            <div class="mobile-search-loading">
                <div class="search-loading-spinner"></div>
                <span>Searching...</span>
            </div>
        `;
        searchResults.style.display = 'block';
        
        mobileSearchTimeout = setTimeout(() => {
            performMobileSearch(query, searchResults);
        }, 300);
    });
    
    // Handle form submission
    searchForm.addEventListener('submit', function(e) {
        const query = searchInput.value.trim();
        if (query.length < 2) {
            e.preventDefault();
            searchInput.focus();
        }
        // If valid, let the form submit normally
    });
}

function openMobileSearchModal() {
    const modal = document.getElementById('mobileSearchModal');
    const searchInput = modal.querySelector('.mobile-search-input');
    
    if (modal) {
        modal.classList.add('active');
        document.body.classList.add('modal-open');
        
        // Focus on input after animation
        setTimeout(() => {
            searchInput.focus();
        }, 300);
    }
}

function closeMobileSearchModal() {
    const modal = document.getElementById('mobileSearchModal');
    const searchResults = modal.querySelector('#mobileSearchResults');
    
    if (modal) {
        modal.classList.remove('active');
        document.body.classList.remove('modal-open');
        
        // Clear search results
        if (searchResults) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
        }
        
        // Clear input
        const searchInput = modal.querySelector('.mobile-search-input');
        if (searchInput) {
            searchInput.value = '';
        }
    }
}

function performMobileSearch(query, resultsContainer) {
    const categorySelect = document.querySelector('.mobile-search-category');
    const category = categorySelect ? categorySelect.value : '';
    
    const params = new URLSearchParams({ query: query });
    if (category) {
        params.append('category', category);
    }

    fetch(`ajax_search.php?${params.toString()}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            displayMobileSearchResults(data, query, resultsContainer);
        })
        .catch(error => {
            console.error('Mobile search error:', error);
            resultsContainer.innerHTML = '<div class="mobile-search-error">Something went wrong. Please try again.</div>';
        });
}

function displayMobileSearchResults(products, query, resultsContainer) {
    if (products.length === 0) {
        resultsContainer.innerHTML = `<div class="mobile-search-no-results">No products found for "${query}"</div>`;
        return;
    }
    
    let html = '<div class="mobile-search-results-list">';
    
    products.forEach(product => {
        const imageUrl = product.image_url ? `uploads/product-image/${product.image_url}` : 'https://placehold.co/60x80/f0f0f0/ccc?text=N/A';
        
        html += `
            <a href="product_details.php?id=${product.id}" class="mobile-search-result-item">
                <img src="${imageUrl}" alt="${product.name}" class="mobile-search-result-image">
                <div class="mobile-search-result-info">
                    <span class="mobile-search-result-name">${product.name}</span>
                    <span class="mobile-search-result-price">₹${product.price}</span>
                    ${product.category_name ? `<span class="mobile-search-result-category">${product.category_name}</span>` : ''}
                </div>
            </a>
        `;
    });
    
    html += '</div>';
    
    // Add view all link
    const categoryParam = document.querySelector('.mobile-search-category')?.value || '';
    html += `
        <div class="mobile-search-view-all">
            <a href="search.php?query=${encodeURIComponent(query)}${categoryParam ? '&category=' + encodeURIComponent(categoryParam) : ''}" class="mobile-search-view-all-btn">
                View All ${products.length}+ Results
            </a>
        </div>
    `;
    
    resultsContainer.innerHTML = html;
}

// Add to your existing initialization
document.addEventListener('DOMContentLoaded', function () {
    // ... your existing code ...
    
    // Add mobile search initialization
    initializeMobileSearch();
});