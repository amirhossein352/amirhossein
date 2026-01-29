/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
    const siteNavigation = document.getElementById( 'site-navigation' );

    // Return early if the navigation doesn't exist.
    if ( ! siteNavigation ) {
        return;
    }

    const button = siteNavigation.getElementsByTagName( 'button' )[ 0 ];

    // Return early if the button doesn't exist.
    if ( 'undefined' === typeof button ) {
        return;
    }

    const menu = siteNavigation.getElementsByTagName( 'ul' )[ 0 ];

    // Hide menu toggle button if menu is empty and return early.
    if ( 'undefined' === typeof menu ) {
        button.style.display = 'none';
        return;
    }

    if ( ! menu.classList.contains( 'nav-menu' ) ) {
        menu.classList.add( 'nav-menu' );
    }

    // Toggle the .toggled class and the aria-expanded value each time the button is clicked.
    button.addEventListener( 'click', function() {
        siteNavigation.classList.toggle( 'toggled' );

        if ( button.getAttribute( 'aria-expanded' ) === 'true' ) {
            button.setAttribute( 'aria-expanded', 'false' );
        } else {
            button.setAttribute( 'aria-expanded', 'true' );
        }
    } );

    // Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
    document.addEventListener( 'click', function( event ) {
        const isClickInside = siteNavigation.contains( event.target );

        if ( ! isClickInside ) {
            siteNavigation.classList.remove( 'toggled' );
            button.setAttribute( 'aria-expanded', 'false' );
        }
    } );

    // Get all the link elements within the menu.
    const links = menu.getElementsByTagName( 'a' );

    // Get all the link elements with children within the menu.
    const linksWithChildren = menu.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

    // Toggle focus classes for links with children.
    for ( const link of linksWithChildren ) {
        link.addEventListener( 'focus', toggleFocus, true );
        link.addEventListener( 'blur', toggleFocus, true );
    }

    // Toggle focus classes for links without children.
    for ( const link of links ) {
        link.addEventListener( 'focus', toggleFocus, true );
        link.addEventListener( 'blur', toggleFocus, true );
    }

    /**
     * Sets or removes .focus class on an element.
     */
    function toggleFocus() {
        if ( event.type === 'focus' || event.type === 'blur' ) {
            let self = this;
            // Move up through the ancestors of the current link until we hit .nav-menu.
            while ( ! self.classList.contains( 'nav-menu' ) ) {
                // On li elements toggle the class .focus.
                if ( 'li' === self.tagName.toLowerCase() ) {
                    self.classList.toggle( 'focus' );
                }
                self = self.parentNode;
            }
        }

        if ( event.type === 'blur' ) {
            const self = this;
            // Move up through the ancestors of the current link until we hit .nav-menu.
            while ( ! self.classList.contains( 'nav-menu' ) ) {
                // On li elements remove the class .focus.
                if ( 'li' === self.tagName.toLowerCase() ) {
                    self.classList.remove( 'focus' );
                }
                self = self.parentNode;
            }
        }
    }

    // Close small menu when user clicks outside
    document.addEventListener( 'click', function( event ) {
        const isClickInside = siteNavigation.contains( event.target );

        if ( ! isClickInside ) {
            siteNavigation.classList.remove( 'toggled' );
            button.setAttribute( 'aria-expanded', 'false' );
        }
    } );

    // Close small menu when user presses Escape key
    document.addEventListener( 'keydown', function( event ) {
        if ( event.key === 'Escape' ) {
            siteNavigation.classList.remove( 'toggled' );
            button.setAttribute( 'aria-expanded', 'false' );
        }
    } );

    // Add smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll( 'a[href^="#"]' );
    anchorLinks.forEach( link => {
        link.addEventListener( 'click', function( event ) {
            event.preventDefault();
            const targetId = this.getAttribute( 'href' );
            const targetElement = document.querySelector( targetId );
            
            if ( targetElement ) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        } );
    } );

    // Add mobile menu toggle functionality
    const mobileMenuToggle = document.querySelector( '.mobile-menu-toggle' );
    if ( mobileMenuToggle ) {
        mobileMenuToggle.addEventListener( 'click', function() {
            const mobileMenu = document.querySelector( '.mobile-menu' );
            if ( mobileMenu ) {
                mobileMenu.classList.toggle( 'active' );
                this.classList.toggle( 'active' );
            }
        } );
    }

    // Add dropdown menu functionality for both desktop and mobile
    const dropdownMenus = document.querySelectorAll( '.menu-item-has-children, .nav-item.dropdown' );
    dropdownMenus.forEach( menuItem => {
        const link = menuItem.querySelector( 'a' );
        const submenu = menuItem.querySelector( '.sub-menu, .dropdown-menu' );
        
        if ( link && submenu ) {
            // Add click event for mobile menu
            link.addEventListener( 'click', function( event ) {
                // Only prevent default and toggle on mobile if inside mobile menu panel
                const isMobileMenu = menuItem.closest( '.mobile-menu-panel' );
                if ( isMobileMenu && window.innerWidth <= 768 ) {
                    event.preventDefault();
                    menuItem.classList.toggle( 'active' );
                    submenu.classList.toggle( 'active' );
                }
            } );
            
            // Desktop hover functionality
            if ( window.innerWidth > 768 ) {
                const isMobileMenu = menuItem.closest( '.mobile-menu-panel' );
                if ( !isMobileMenu ) {
                    menuItem.addEventListener( 'mouseenter', function() {
                        submenu.style.display = 'block';
                        submenu.style.opacity = '1';
                        submenu.style.visibility = 'visible';
                    } );
                    
                    menuItem.addEventListener( 'mouseleave', function() {
                        submenu.style.display = 'none';
                        submenu.style.opacity = '0';
                        submenu.style.visibility = 'hidden';
                    } );
                }
            }
        }
    } );
    
    // Handle nested dropdowns in mobile menu
    const mobileMenuPanel = document.getElementById( 'mobile-menu-panel' );
    if ( mobileMenuPanel ) {
        const mobileDropdowns = mobileMenuPanel.querySelectorAll( '.nav-item.dropdown' );
        mobileDropdowns.forEach( menuItem => {
            const link = menuItem.querySelector( 'a' );
            const submenu = menuItem.querySelector( '.sub-menu, .dropdown-menu' );
            
            if ( link && submenu ) {
                link.addEventListener( 'click', function( event ) {
                    if ( window.innerWidth <= 768 ) {
                        event.preventDefault();
                        menuItem.classList.toggle( 'active' );
                        submenu.classList.toggle( 'active' );
                    }
                } );
            }
        } );
    }

    // Handle window resize
    window.addEventListener( 'resize', function() {
        if ( window.innerWidth > 768 ) {
            // Reset mobile menu state on desktop
            const mobileMenu = document.querySelector( '.mobile-menu' );
            if ( mobileMenu ) {
                mobileMenu.classList.remove( 'active' );
            }
            
            const mobileToggle = document.querySelector( '.mobile-menu-toggle' );
            if ( mobileToggle ) {
                mobileToggle.classList.remove( 'active' );
            }
        }
    } );


} )();
