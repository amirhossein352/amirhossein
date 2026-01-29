// Custom cursor disabled - using default browser cursor
// Remove any existing cursor classes and ensure default cursor is restored
(function() {
	function restoreDefaultCursor() {
		if (document.body) {
			document.body.classList.remove('zs-cursor-enabled');
			// Remove custom cursor elements if they exist
			var existingCursor = document.getElementById('zs-cursor');
			var existingCursorDot = document.getElementById('zs-cursor-dot');
			if (existingCursor) existingCursor.remove();
			if (existingCursorDot) existingCursorDot.remove();
		}
	}
	
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', restoreDefaultCursor);
	} else {
		restoreDefaultCursor();
	}
})();

/*
(function () {
	// Desktop-only: skip on coarse pointers (touch)
	var isFinePointer = window.matchMedia && window.matchMedia('(pointer: fine)').matches;
	if (!isFinePointer) { return; }

	// Create custom cursor elements
	var cursor = document.createElement('div');
	var cursorDot = document.createElement('div');
	var icon = document.createElement('span');
	cursor.id = 'zs-cursor';
	cursorDot.id = 'zs-cursor-dot';
	icon.id = 'zs-cursor-icon';
	cursor.appendChild(icon);

	// Resolve brand color from CSS variable with fallback
	function getBrandColor() {
		var c = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim();
		return c || 'rgba(52,152,219,1)';
	}

	// Inject styles
	var style = document.createElement('style');
	style.textContent = `
		body.zs-cursor-enabled { cursor: none; }
		#zs-cursor, #zs-cursor-dot { position: fixed; top: 0; left: 0; pointer-events: none; z-index: 99999; transform: translate(-50%, -50%); }
		#zs-cursor { width: 34px; height: 34px; border-radius: 50%; border: 2px solid ${getBrandColor()}; transition: transform .14s ease-out, opacity .2s ease, border-color .3s ease; backdrop-filter: blur(0.5px); display: flex; align-items: center; justify-content: center; }
		#zs-cursor-dot { width: 6px; height: 6px; background: ${getBrandColor()}; border-radius: 50%; transition: transform .08s ease-out, opacity .2s ease, background .3s ease; }
		#zs-cursor.zs-hidden, #zs-cursor-dot.zs-hidden { opacity: 0; }
		#zs-cursor.zs-active { transform: translate(-50%, -50%) scale(1.6); border-color: ${getBrandColor()}; }
		#zs-cursor.zs-link, #zs-cursor-dot.zs-link { filter: drop-shadow(0 0 6px ${getBrandColor()}99); }
		#zs-cursor-icon { font-size: 14px; line-height: 1; color: ${getBrandColor()}; opacity: .95; transition: opacity .15s ease; }
		#zs-cursor.zs-has-icon { width: 38px; height: 38px; }
	`;

	function enable() {
		if (!document.body) return;
		if (!document.getElementById('zs-cursor')) {
			document.head.appendChild(style);
			document.body.appendChild(cursor);
			document.body.appendChild(cursorDot);
			document.body.classList.add('zs-cursor-enabled');
		}
	}

	var mouseX = 0, mouseY = 0; // raw mouse
	var renderX = 0, renderY = 0; // smoothed position
	var visible = true;
	var currentMagTarget = null;

	function onMove(e) {
		mouseX = e.clientX;
		mouseY = e.clientY;
		if (!visible) {
			cursor.classList.remove('zs-hidden');
			cursorDot.classList.remove('zs-hidden');
			visible = true;
		}
	}

	function onLeave() {
		cursor.classList.add('zs-hidden');
		cursorDot.classList.add('zs-hidden');
		visible = false;
	}

	function onDown() { cursor.classList.add('zs-active'); }
	function onUp() { cursor.classList.remove('zs-active'); }

	function isInteractive(el) {
		if (!el || el === document.body) return false;
		var tag = (el.tagName || '').toLowerCase();
		if (tag === 'a' || tag === 'button' || tag === 'input' || tag === 'textarea' || tag === 'select') return true;
		var role = el.getAttribute && el.getAttribute('role');
		if (role === 'button' || role === 'link') return true;
		if (getComputedStyle(el).cursor === 'pointer') return true;
		return false;
	}

	function pickIcon(el) {
		if (!el) return '';
		var tag = (el.tagName || '').toLowerCase();
		if (tag === 'a') return '↗';
		if (tag === 'button') return '↗';
		if (tag === 'video') return '▶';
		if (tag === 'img' || (el.closest && el.closest('figure'))) return '🔍';
		if (tag === 'input' || tag === 'textarea') return '✎';
		return '';
	}

	function updateHoverState(target) {
		var el = target;
		var interactive = null;
		while (el && el !== document.body) {
			if (isInteractive(el) || ['img','video','figure'].indexOf((el.tagName||'').toLowerCase()) !== -1) { interactive = el; break; }
			el = el.parentElement;
		}
		var has = !!interactive;
		cursor.classList.toggle('zs-link', has);
		cursorDot.classList.toggle('zs-link', has);
		currentMagTarget = interactive;

		var ic = pickIcon(interactive);
		if (ic) {
			icon.textContent = ic;
			cursor.classList.add('zs-has-icon');
		} else {
			icon.textContent = '';
			cursor.classList.remove('zs-has-icon');
		}
	}

	// Animation loop with smoothing and magnetic attraction
	var lastTs = 0;
	function tick(ts) {
		var dt = Math.min(32, ts - lastTs) || 16;
		lastTs = ts;

		// Base follow smoothing
		var followSpeed = 0.22;
		renderX += (mouseX - renderX) * followSpeed;
		renderY += (mouseY - renderY) * followSpeed;

		// Magnetic attraction when near an interactive target
		if (currentMagTarget && currentMagTarget.getBoundingClientRect) {
			var r = currentMagTarget.getBoundingClientRect();
			var cx = r.left + r.width / 2;
			var cy = r.top + r.height / 2;
			var dx = mouseX - cx;
			var dy = mouseY - cy;
			var dist = Math.hypot(dx, dy);
			var radius = Math.max(60, Math.min(120, Math.max(r.width, r.height)));
			if (dist < radius) {
				var strength = 1 - (dist / radius); // 0..1
				var pull = 0.30 * strength;
				renderX += (cx - renderX) * pull;
				renderY += (cy - renderY) * pull;
				cursor.classList.add('zs-active');
			} else {
				cursor.classList.remove('zs-active');
			}
		}

		cursor.style.transform = 'translate(' + renderX + 'px,' + renderY + 'px) translate(-50%, -50%)';
		cursorDot.style.transform = 'translate(' + mouseX + 'px,' + mouseY + 'px) translate(-50%, -50%)';

		requestAnimationFrame(tick);
	}

	// Initialize when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function(){ enable(); requestAnimationFrame(tick); });
	} else {
		enable();
		requestAnimationFrame(tick);
	}

	window.addEventListener('mousemove', function (e) { onMove(e); updateHoverState(e.target); }, { passive: true });
	window.addEventListener('mouseover', function (e) { updateHoverState(e.target); }, { passive: true });
	window.addEventListener('mousedown', onDown, { passive: true });
	window.addEventListener('mouseup', onUp, { passive: true });
	window.addEventListener('mouseleave', onLeave, { passive: true });

	// Update brand color on theme changes (in case CSS var changes)
	var colorObserver = new MutationObserver(function(){
		var c = getBrandColor();
		cursor.style.borderColor = c;
		cursorDot.style.background = c;
		icon.style.color = c;
	});
	colorObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['style', 'class'] });
})();
*/

// Mobile Menu Functionality
(function() {
    'use strict';
    
    // Wait for DOM to be ready
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }
    
    function initMobileMenu() {
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenuPanel = document.getElementById('mobile-menu-panel');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        
        // Debug: Check if elements exist
        console.log('Mobile Menu Elements:', {
            toggle: !!mobileMenuToggle,
            panel: !!mobileMenuPanel,
            overlay: !!mobileMenuOverlay,
            close: !!mobileMenuClose
        });
        
        if (!mobileMenuToggle || !mobileMenuPanel || !mobileMenuOverlay || !mobileMenuClose) {
            console.error('Mobile menu elements not found!');
            return;
        }
        
        function openMobileMenu() {
            mobileMenuToggle.classList.add('active');
            mobileMenuPanel.classList.add('active');
            mobileMenuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            mobileMenuToggle.setAttribute('aria-expanded', 'true');
        }
        
        function closeMobileMenu() {
            mobileMenuToggle.classList.remove('active');
            mobileMenuPanel.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = '';
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
        }
        
        // Toggle menu on button click
        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Mobile menu toggle clicked');
            if (mobileMenuPanel.classList.contains('active')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
        
        // Close menu on close button click
        mobileMenuClose.addEventListener('click', function(e) {
            e.preventDefault();
            closeMobileMenu();
        });
        
        // Close menu on overlay click
        mobileMenuOverlay.addEventListener('click', function(e) {
            e.preventDefault();
            closeMobileMenu();
        });
        
        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileMenuPanel.classList.contains('active')) {
                closeMobileMenu();
            }
        });
        
        // Handle sub-menus with sliding/modal approach
        function initMobileSubMenus() {
            const dropdownItems = mobileMenuPanel.querySelectorAll('.nav-item.dropdown, .menu-item-has-children');
            
            dropdownItems.forEach(function(item) {
                const link = item.querySelector('.nav-link, a');
                const submenu = item.querySelector('.dropdown-menu, .sub-menu');
                
                if (!link || !submenu) {
                    return;
                }
                
                // Add click handler for dropdown items
                link.addEventListener('click', function(e) {
                    // Only handle on mobile
                    if (window.innerWidth > 768) {
                        return;
                    }
                    
                    // Check if this link has a real URL (not just #)
                    const hasRealUrl = link.getAttribute('href') && 
                                      link.getAttribute('href') !== '#' && 
                                      link.getAttribute('href') !== '';
                    
                    // If it has a submenu, toggle it
                    if (submenu) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Close other open dropdowns at the same level
                        const parentItem = item.parentElement;
                        if (parentItem) {
                            const siblings = parentItem.querySelectorAll('.nav-item.dropdown, .menu-item-has-children');
                            siblings.forEach(function(sibling) {
                                if (sibling !== item && sibling.classList.contains('active')) {
                                    sibling.classList.remove('active');
                                    const siblingSubmenu = sibling.querySelector('.dropdown-menu, .sub-menu');
                                    if (siblingSubmenu) {
                                        siblingSubmenu.classList.remove('active');
                                    }
                                }
                            });
                        }
                        
                        // Toggle current dropdown
                        const isActive = item.classList.contains('active');
                        if (isActive) {
                            item.classList.remove('active');
                            submenu.classList.remove('active');
                        } else {
                            item.classList.add('active');
                            submenu.classList.add('active');
                        }
                    } else if (!hasRealUrl) {
                        // If no submenu and no real URL, prevent default
                        e.preventDefault();
                    }
                    // If it has a real URL and no submenu, allow normal navigation
                });
            });
            
            // Close menu when clicking on non-dropdown links
            const nonDropdownLinks = mobileMenuPanel.querySelectorAll('.nav-item:not(.dropdown):not(.menu-item-has-children) .nav-link, .nav-item:not(.dropdown):not(.menu-item-has-children) a');
            nonDropdownLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    // Small delay to allow navigation
                    setTimeout(function() {
                        closeMobileMenu();
                    }, 100);
                });
            });
        }
        
        // Initialize sub-menus after menu is ready
        setTimeout(function() {
            initMobileSubMenus();
        }, 100);
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && mobileMenuPanel.classList.contains('active')) {
                closeMobileMenu();
            }
        });
    }
    
    ready(initMobileMenu);
})();

// Header Scroll Hide/Show Functionality
(function() {
    'use strict';
    
    let lastScrollTop = 0;
    let scrollTimeout;
    let isScrolling = false;
    
    function initHeaderScroll() {
        const header = document.querySelector('.site-header');
        
        if (!header) {
            console.error('Header element not found!');
            return;
        }

        if (header.dataset && header.dataset.sticky === 'false') {
            header.classList.remove('hidden', 'scrolled');
            return;
        }
        
        // بررسی موبایل - در موبایل هدر باید fixed باشد
        const isMobile = window.innerWidth <= 768;
        
        // در موبایل: هدر fixed است و فقط در بالای صفحه نمایش داده می‌شود
        if (isMobile) {
            header.style.position = 'fixed';
            header.style.top = '0';
            header.style.left = '0';
            header.style.right = '0';
            header.style.zIndex = '9999';
            header.style.width = '100%';
            
            function updateMobileHeader() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // فقط در بالای صفحه (scrollTop < 100) هدر نمایش داده می‌شود
                if (scrollTop < 100) {
                    header.classList.remove('hidden');
                    if (scrollTop > 50) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                } else {
                    // وقتی به پایین رفته، هدر مخفی می‌شود
                    header.classList.add('hidden');
                }
                
                lastScrollTop = scrollTop;
            }
            
            window.addEventListener('scroll', updateMobileHeader, { passive: true });
            updateMobileHeader();
            return;
        }
        
        // Throttle scroll event for better performance
        let ticking = false;
        
        function updateHeader() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollDirection = scrollTop > lastScrollTop ? 'down' : 'up';
            const scrollDistance = Math.abs(scrollTop - lastScrollTop);
            
            // Only trigger if scrolled more than 5px to avoid jitter
            if (scrollDistance > 5) {
                if (scrollDirection === 'down' && scrollTop > 100) {
                    // Scrolling down and past 100px - hide header
                    header.classList.add('hidden');
                    header.classList.remove('scrolled');
                } else if (scrollDirection === 'up' || scrollTop <= 100) {
                    // Scrolling up or at top - show header
                    header.classList.remove('hidden');
                    
                    if (scrollTop > 50) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                }
                
                lastScrollTop = scrollTop;
            }
            
            ticking = false;
        }
        
        function onScroll() {
            if (!ticking) {
                requestAnimationFrame(updateHeader);
                ticking = true;
            }
            
            // Clear existing timeout
            clearTimeout(scrollTimeout);
            
            // Set new timeout to detect scroll end
            scrollTimeout = setTimeout(function() {
                isScrolling = false;
                // Show header when scroll stops
                if (window.pageYOffset > 100) {
                    header.classList.remove('hidden');
                }
            }, 150);
        }
        
        // Add scroll event listener
        window.addEventListener('scroll', onScroll, { passive: true });
        
        // Handle mobile menu visibility during scroll
        const mobileMenuPanel = document.getElementById('mobile-menu-panel');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        
        function handleMobileMenuScroll() {
            if (mobileMenuPanel && mobileMenuPanel.classList.contains('active')) {
                // Close mobile menu when scrolling
                mobileMenuPanel.classList.remove('active');
                if (mobileMenuOverlay) {
                    mobileMenuOverlay.classList.remove('active');
                }
                document.body.style.overflow = '';
                
                // Reset mobile menu toggle
                const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
                if (mobileMenuToggle) {
                    mobileMenuToggle.classList.remove('active');
                    mobileMenuToggle.setAttribute('aria-expanded', 'false');
                }
            }
        }
        
        // Close mobile menu on scroll
        window.addEventListener('scroll', handleMobileMenuScroll, { passive: true });
        
        // Handle resize events
        window.addEventListener('resize', function() {
            // Reset header state on resize
            const currentIsMobile = window.innerWidth <= 768;
            if (currentIsMobile) {
                // در موبایل: هدر همیشه نمایش داده می‌شود
                header.classList.remove('hidden');
            }
            header.classList.remove('scrolled');
            lastScrollTop = window.pageYOffset || document.documentElement.scrollTop;
        });
        
        console.log('Header scroll functionality initialized');
    }
    
    // Initialize when DOM is ready
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }
    
    ready(initHeaderScroll);
})();

// Mobile Sidebar Functionality for Single Post Pages
(function() {
    'use strict';
    
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }
    
    function initMobileSidebar() {
        const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
        const mobileSidebarPanel = document.getElementById('mobile-sidebar-panel');
        const mobileSidebarOverlay = document.getElementById('mobile-sidebar-overlay');
        const mobileSidebarClose = document.getElementById('mobile-sidebar-close');
        
        if (!mobileSidebarToggle || !mobileSidebarPanel || !mobileSidebarOverlay || !mobileSidebarClose) {
            return; // Not on single post page or elements don't exist
        }
        
        function openMobileSidebar() {
            mobileSidebarPanel.classList.add('active');
            mobileSidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            mobileSidebarToggle.setAttribute('aria-expanded', 'true');
        }
        
        function closeMobileSidebar() {
            mobileSidebarPanel.classList.remove('active');
            mobileSidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
            mobileSidebarToggle.setAttribute('aria-expanded', 'false');
        }
        
        // Toggle sidebar on button click
        mobileSidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (mobileSidebarPanel.classList.contains('active')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        });
        
        // Close sidebar on close button click
        mobileSidebarClose.addEventListener('click', function(e) {
            e.preventDefault();
            closeMobileSidebar();
        });
        
        // Close sidebar on overlay click
        mobileSidebarOverlay.addEventListener('click', function(e) {
            e.preventDefault();
            closeMobileSidebar();
        });
        
        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileSidebarPanel.classList.contains('active')) {
                closeMobileSidebar();
            }
        });
        
        // Close sidebar when clicking on sidebar links
        const sidebarLinks = mobileSidebarPanel.querySelectorAll('a');
        sidebarLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                // Small delay to allow navigation
                setTimeout(closeMobileSidebar, 100);
            });
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && mobileSidebarPanel.classList.contains('active')) {
                closeMobileSidebar();
            }
        });
    }
    
    ready(initMobileSidebar);
})();

// Desktop Menu Click to Open Dropdown (Remove Hover)
(function() {
    'use strict';
    
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }
    
    function initDesktopMenuClick() {
        // Only on desktop
        if (window.innerWidth <= 768) {
            return;
        }
        
        // Get all dropdown items (including nested ones)
        const dropdownItems = document.querySelectorAll('.main-navigation .nav-item.dropdown, .header-middle .desktop-nav .nav-item.dropdown');
        
        dropdownItems.forEach(function(item) {
            const link = item.querySelector('.nav-link');
            const submenu = item.querySelector('.dropdown-menu, .sub-menu');
            
            if (!link || !submenu) {
                return;
            }
            
            // Remove any existing click listeners to avoid duplicates
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            
            // Remove hover behavior - use click instead
            newLink.addEventListener('click', function(e) {
                // Check if this is a nested dropdown
                const isNested = item.closest('.dropdown-menu, .sub-menu') !== null;
                
                // If link has href, don't prevent default for non-dropdown items
                const hasRealUrl = newLink.getAttribute('href') && newLink.getAttribute('href') !== '#' && newLink.getAttribute('href') !== '';
                
                // Toggle active class
                const isActive = item.classList.contains('active');
                
                // For nested dropdowns, only close siblings at the same level
                if (isNested) {
                    const parentDropdown = item.closest('.dropdown-menu, .sub-menu');
                    const siblings = parentDropdown.querySelectorAll('.nav-item.dropdown');
                    siblings.forEach(function(sibling) {
                        if (sibling !== item) {
                            sibling.classList.remove('active');
                        }
                    });
                } else {
                    // Close all other top-level dropdowns
                    dropdownItems.forEach(function(otherItem) {
                        const isOtherNested = otherItem.closest('.dropdown-menu, .sub-menu') !== null;
                        if (!isOtherNested && otherItem !== item) {
                            otherItem.classList.remove('active');
                        }
                    });
                }
                
                // Toggle current dropdown
                if (isActive) {
                    item.classList.remove('active');
                } else {
                    item.classList.add('active');
                    // If link has a real URL, allow navigation
                    if (hasRealUrl) {
                        // Don't prevent default - allow navigation
                        return;
                    } else {
                        // Prevent default only if no real URL
                        e.preventDefault();
                    }
                }
            });
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            dropdownItems.forEach(function(item) {
                if (!item.contains(e.target)) {
                    item.classList.remove('active');
                }
            });
        });
    }
    
    ready(initDesktopMenuClick);
    
    // Re-initialize on resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            initDesktopMenuClick();
        }
    });
})();


