document.addEventListener('DOMContentLoaded', function () {
    
    // --- Mobile Filter Toggle ---
    const filterToggleBtn = document.getElementById('filterToggle');
    const filtersSidebar = document.getElementById('filtersSidebar');
    const filtersForm = document.getElementById('filtersForm');
    let filterOverlay;

    // Create filter overlay for mobile
    function createFilterOverlay() {
        filterOverlay = document.createElement('div');
        filterOverlay.className = 'filter-overlay';
        document.body.appendChild(filterOverlay);
        
        filterOverlay.addEventListener('click', function() {
            closeFilters();
        });
    }

    function openFilters() {
        if (!filterOverlay) createFilterOverlay();
        filtersSidebar.classList.add('open');
        filterOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeFilters() {
        filtersSidebar.classList.remove('open');
        if (filterOverlay) {
            filterOverlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }

    if (filterToggleBtn) {
        filterToggleBtn.addEventListener('click', function() {
            if (filtersSidebar.classList.contains('open')) {
                closeFilters();
            } else {
                openFilters();
            }
        });
    }

    // --- Sort Functionality ---
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort', this.value);
            window.location.href = window.location.pathname + '?' + urlParams.toString();
        });
    }

    // --- Filter Form Enhancements ---
    if (filtersForm) {
        // Auto-submit on filter changes (optional)
        const autoSubmitElements = filtersForm.querySelectorAll('input[type="checkbox"]');
        autoSubmitElements.forEach(element => {
            element.addEventListener('change', function() {
                // Add a small delay to allow multiple quick selections
                clearTimeout(this.submitTimeout);
                this.submitTimeout = setTimeout(() => {
                    filtersForm.submit();
                }, 500);
            });
        });

        // Price range validation
        const minPriceInput = filtersForm.querySelector('input[name="min_price"]');
        const maxPriceInput = filtersForm.querySelector('input[name="max_price"]');

        function validatePriceRange() {
            const minPrice = parseFloat(minPriceInput.value) || 0;
            const maxPrice = parseFloat(maxPriceInput.value) || Infinity;

            if (minPrice > maxPrice) {
                maxPriceInput.value = minPriceInput.value;
            }
        }

        if (minPriceInput && maxPriceInput) {
            minPriceInput.addEventListener('blur', validatePriceRange);
            maxPriceInput.addEventListener('blur', validatePriceRange);
        }
    }

    // --- Quick Add to Cart Functionality ---
    const quickAddBtns = document.querySelectorAll('.quick-add-cart');
    quickAddBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            quickAddToCart(productId, this);
        });
    });

    function quickAddToCart(productId, buttonElement) {
        // Show loading state
        const originalText = buttonElement.innerHTML;
        buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        buttonElement.disabled = true;

        // Create form data
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', 1);

        // Send AJAX request
        fetch('add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Success feedback
            buttonElement.innerHTML = '<i class="fas fa-check"></i> Added!';
            buttonElement.style.background = '#28a745';
            
            // Update cart count if visible
            updateCartCount();
            
            // Reset button after delay
            setTimeout(() => {
                buttonElement.innerHTML = originalText;
                buttonElement.style.background = '';
                buttonElement.disabled = false;
            }, 2000);
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            buttonElement.innerHTML = '<i class="fas fa-exclamation"></i> Error';
            buttonElement.style.background = '#dc3545';
            
            setTimeout(() => {
                buttonElement.innerHTML = originalText;
                buttonElement.style.background = '';
                buttonElement.disabled = false;
            }, 2000);
        });
    }

    // --- Update Cart Count ---
    function updateCartCount() {
        // This would typically fetch the updated cart count from the server
        // For now, we'll just increment the visible counter
        const cartBadge = document.querySelector('.cart-count-badge');
        if (cartBadge) {
            const currentCount = parseInt(cartBadge.textContent) || 0;
            cartBadge.textContent = currentCount + 1;
            
            // Add animation
            cartBadge.style.transform = 'scale(1.3)';
            setTimeout(() => {
                cartBadge.style.transform = 'scale(1)';
            }, 200);
        }
    }

    

    // --- Smooth Animations ---
    function animateProductCards() {
        const productCards = document.querySelectorAll('.product-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('animate');
                    }, index * 100); // Stagger animation
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        productCards.forEach(card => {
            observer.observe(card);
        });
    }

    // Initialize animations
    animateProductCards();

    // --- Search Functionality Enhancement ---
    const searchInput = filtersForm?.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            
            // Add visual feedback
            this.style.borderColor = '#ffc107';
            
            searchTimeout = setTimeout(() => {
                this.style.borderColor = '';
            }, 1000);
        });

        // Submit on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filtersForm.submit();
            }
        });
    }

    // --- URL Parameter Management ---
    function updateURLWithoutReload(params) {
        const url = new URL(window.location);
        Object.entries(params).forEach(([key, value]) => {
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        });
        window.history.pushState({}, '', url);
    }

    // --- Keyboard Navigation ---
    document.addEventListener('keydown', function(e) {
        // ESC key closes mobile filters
        if (e.key === 'Escape') {
            closeFilters();
        }
    });

    // --- Filter Clear Functionality ---
    const clearFiltersBtn = document.querySelector('.clear-filters-btn');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Clear all form inputs
            const inputs = filtersForm.querySelectorAll('input[type="text"], input[type="number"], input[type="checkbox"]');
            inputs.forEach(input => {
                if (input.type === 'checkbox') {
                    input.checked = false;
                } else {
                    input.value = '';
                }
            });
            
            // Submit the cleared form
            filtersForm.submit();
        });
    }

    // --- Responsive Grid Adjustment ---
    function adjustGridLayout() {
        const productsGrid = document.getElementById('productsGrid');
        if (!productsGrid) return;

        const containerWidth = productsGrid.offsetWidth;
        const cardMinWidth = 280;
        const gap = 25;
        const columns = Math.floor((containerWidth + gap) / (cardMinWidth + gap));
        
        // Adjust grid based on available space
        if (columns > 0) {
            const cardWidth = (containerWidth - (gap * (columns - 1))) / columns;
            productsGrid.style.gridTemplateColumns = `repeat(${columns}, ${cardWidth}px)`;
        }
    }

    // Adjust on resize
    window.addEventListener('resize', debounce(adjustGridLayout, 250));

    // --- Utility Functions ---
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // --- Performance Optimization ---
    // Lazy load images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });

        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => imageObserver.observe(img));
    }

    // --- Page Load Complete ---
    console.log('Category page initialized successfully');
    
    // Add fade-in animation to main content
    const mainContent = document.querySelector('.category-page-content');
    if (mainContent) {
        mainContent.classList.add('fade-in');
    }

    // Close mobile filters when clicking on a product
    const productLinks = document.querySelectorAll('.product-card a');
    productLinks.forEach(link => {
        link.addEventListener('click', function() {
            closeFilters();
        });
    });

    // --- Filter Summary Display ---
    function displayActiveFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeFilters = [];
        
        // Check each filter parameter
        if (urlParams.get('search')) {
            activeFilters.push(`Search: "${urlParams.get('search')}"`);
        }
        if (urlParams.get('min_price')) {
            activeFilters.push(`Min Price: ₹${urlParams.get('min_price')}`);
        }
        if (urlParams.get('max_price')) {
            activeFilters.push(`Max Price: ₹${urlParams.get('max_price')}`);
        }
        if (urlParams.get('featured')) {
            activeFilters.push('Featured Only');
        }
        
        // Display active filters (you can enhance this further)
        if (activeFilters.length > 0) {
            console.log('Active filters:', activeFilters);
        }
    }

    displayActiveFilters();
});