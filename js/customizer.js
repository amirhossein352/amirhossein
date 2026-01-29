/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title, .site-description' ).css( {
					clip: 'rect(1px, 1px, 1px, 1px)',
					position: 'absolute',
				} );
			} else {
				$( '.site-title, .site-description' ).css( {
					clip: 'auto',
					position: 'relative',
				} );
				$( '.site-title a, .site-description' ).css( {
					color: to,
				} );
			}
		} );
	} );

	// Color customizations
	wp.customize( 'primary_color', function( value ) {
		value.bind( function( to ) {
			// Update CSS custom properties
			document.documentElement.style.setProperty( '--primary-color', to );
			
			// Update specific elements
			$( 'a' ).css( 'color', to );
			$( '.site-title a' ).css( 'color', to );
			$( '.main-navigation a:hover' ).css( 'background-color', to + '20' );
		} );
	} );

	wp.customize( 'secondary_color', function( value ) {
		value.bind( function( to ) {
			// Update CSS custom properties
			document.documentElement.style.setProperty( '--secondary-color', to );
			
			// Update specific elements
			$( '.cta-button' ).css( 'background-color', to );
			$( '.product-price' ).css( 'color', to );
		} );
	} );

	wp.customize( 'accent_color', function( value ) {
		value.bind( function( to ) {
			// Update CSS custom properties
			document.documentElement.style.setProperty( '--accent-color', to );
			
			// Update specific elements
			$( '.add-to-cart-btn' ).css( 'background-color', to );
		} );
	} );

	// Typography customizations
	wp.customize( 'body_font_family', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'font-family', to + ', sans-serif' );
		} );
	} );

	wp.customize( 'heading_font_family', function( value ) {
		value.bind( function( to ) {
			$( 'h1, h2, h3, h4, h5, h6' ).css( 'font-family', to + ', sans-serif' );
		} );
	} );

	// Layout customizations
	wp.customize( 'container_width', function( value ) {
		value.bind( function( to ) {
			$( '.container' ).css( 'max-width', to + 'px' );
		} );
	} );

	wp.customize( 'sidebar_position', function( value ) {
		value.bind( function( to ) {
			if ( to === 'left' ) {
				$( '.content-area' ).removeClass( 'sidebar-right' ).addClass( 'sidebar-left' );
			} else if ( to === 'right' ) {
				$( '.content-area' ).removeClass( 'sidebar-left' ).addClass( 'sidebar-right' );
			} else {
				$( '.content-area' ).removeClass( 'sidebar-left sidebar-right' );
			}
		} );
	} );

	// Footer customizations
	wp.customize( 'footer_text', function( value ) {
		value.bind( function( to ) {
			$( '.footer-bottom p:first-child' ).html( to );
		} );
	} );

	wp.customize( 'show_social_links', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '.social-links' ).show();
			} else {
				$( '.social-links' ).hide();
			}
		} );
	} );

	// Social media customizations
	wp.customize( 'facebook_url', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '.facebook-link' ).attr( 'href', to ).show();
			} else {
				$( '.facebook-link' ).hide();
			}
		} );
	} );

	wp.customize( 'instagram_url', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '.instagram-link' ).attr( 'href', to ).show();
			} else {
				$( '.instagram-link' ).hide();
			}
		} );
	} );

	wp.customize( 'telegram_url', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '.telegram-link' ).attr( 'href', to ).show();
			} else {
				$( '.telegram-link' ).hide();
			}
		} );
	} );

	wp.customize( 'whatsapp_number', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '.whatsapp-link' ).attr( 'href', 'https://wa.me/' + to.replace( /[^0-9]/g, '' ) ).show();
			} else {
				$( '.whatsapp-link' ).hide();
			}
		} );
	} );

	// WooCommerce customizations
	if ( typeof wp.customize( 'products_per_page' ) !== 'undefined' ) {
		wp.customize( 'products_per_page', function( value ) {
			value.bind( function( to ) {
				// This will be handled by WooCommerce on page reload
				$( '.woocommerce-products-header' ).addClass( 'customizer-updated' );
			} );
		} );
	}

	if ( typeof wp.customize( 'show_product_rating' ) !== 'undefined' ) {
		wp.customize( 'show_product_rating', function( value ) {
			value.bind( function( to ) {
				if ( to ) {
					$( '.star-rating' ).show();
				} else {
					$( '.star-rating' ).hide();
				}
			} );
		} );
	}

	if ( typeof wp.customize( 'show_product_price' ) !== 'undefined' ) {
		wp.customize( 'show_product_price', function( value ) {
			value.bind( function( to ) {
				if ( to ) {
					$( '.price' ).show();
				} else {
					$( '.price' ).hide();
				}
			} );
		} );
	}

	// Background color
	wp.customize( 'background_color', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'background-color', to );
		} );
	} );

	// Background image
	wp.customize( 'background_image', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( 'body' ).css( 'background-image', 'url(' + to + ')' );
			} else {
				$( 'body' ).css( 'background-image', 'none' );
			}
		} );
	} );

	// Header image
	wp.customize( 'header_image', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '.custom-header' ).css( 'background-image', 'url(' + to + ')' );
			} else {
				$( '.custom-header' ).css( 'background-image', 'none' );
			}
		} );
	} );

	// Custom logo
	wp.customize( 'custom_logo', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '.site-logo img' ).attr( 'src', to );
				$( '.site-logo' ).show();
				$( '.site-title' ).hide();
			} else {
				$( '.site-logo' ).hide();
				$( '.site-title' ).show();
			}
		} );
	} );

	// Live preview for custom CSS
	wp.customize( 'custom_css', function( value ) {
		value.bind( function( to ) {
			$( '#custom-css' ).html( to );
		} );
	} );

	// Add custom CSS for live preview
	$( '<style id="custom-css"></style>' ).appendTo( 'head' );

	// Handle responsive preview
	$( '#customize-preview' ).on( 'click', '.preview-device', function() {
		var device = $( this ).data( 'device' );
		$( '#customize-preview' ).removeClass( 'preview-desktop preview-tablet preview-mobile' ).addClass( 'preview-' + device );
	} );

	// Add smooth transitions for customizer changes
	$( 'head' ).append( '<style>.customizer-transition { transition: all 0.3s ease; }</style>' );

	// Add transition class to elements that will be customized
	$( '.site-title, .site-description, a, h1, h2, h3, h4, h5, h6, body, .container, .footer-bottom p' ).addClass( 'customizer-transition' );

	wp.customize.section( 'colors' ).expanded.bind( function( expanded ) {
		if ( expanded ) {
			$( 'body' ).addClass( 'customizer-highlight' );
		} else {
			$( 'body' ).removeClass( 'customizer-highlight' );
		}
	} );

	wp.customize.section( 'typography_section' ).expanded.bind( function( expanded ) {
		if ( expanded ) {
			$( 'body' ).addClass( 'customizer-highlight' );
		} else {
			$( 'body' ).removeClass( 'customizer-highlight' );
		}
	} );

	wp.customize.section( 'layout_section' ).expanded.bind( function( expanded ) {
		if ( expanded ) {
			$( '.container, .content-area' ).addClass( 'customizer-highlight' );
		} else {
			$( '.container, .content-area' ).removeClass( 'customizer-highlight' );
		}
	} );

	wp.customize.section( 'footer_section' ).expanded.bind( function( expanded ) {
		if ( expanded ) {
			$( '.site-footer' ).addClass( 'customizer-highlight' );
		} else {
			$( '.site-footer' ).removeClass( 'customizer-highlight' );
		}
	} );

	wp.customize.section( 'social_section' ).expanded.bind( function( expanded ) {
		if ( expanded ) {
			$( '.social-links' ).addClass( 'customizer-highlight' );
		} else {
			$( '.social-links' ).removeClass( 'customizer-highlight' );
		}
	} );

	// Add customizer highlight styles
	$( 'head' ).append( '<style>.customizer-highlight { outline: 2px solid #0073aa !important; outline-offset: 2px !important; }</style>' );

	// Handle customizer panel visibility
	wp.customize.panel( 'nav_menus' ).expanded.bind( function( expanded ) {
		if ( expanded ) {
			$( '.main-navigation' ).addClass( 'customizer-highlight' );
		} else {
			$( '.main-navigation' ).removeClass( 'customizer-highlight' );
		}
	} );

	wp.customize.panel( 'widgets' ).expanded.bind( function( expanded ) {
		if ( expanded ) {
			$( '.widget-area' ).addClass( 'customizer-highlight' );
		} else {
			$( '.widget-area' ).removeClass( 'customizer-highlight' );
		}
	} );

	// Add loading indicator for customizer changes
	$( 'head' ).append( '<style>.customizer-loading::after { content: "⏳"; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 20px; }</style>' );

	// Show loading indicator for complex changes
	wp.customize( 'custom_logo' ).bind( function( to ) {
		$( '.site-logo' ).addClass( 'customizer-loading' );
		setTimeout( function() {
			$( '.site-logo' ).removeClass( 'customizer-loading' );
		}, 500 );
	} );

	// Handle customizer save
	wp.customize.bind( 'save', function() {
		$( 'body' ).addClass( 'customizer-saving' );
	} );

	wp.customize.bind( 'saved', function() {
		$( 'body' ).removeClass( 'customizer-saving' );
		$( 'body' ).addClass( 'customizer-saved' );
		setTimeout( function() {
			$( 'body' ).removeClass( 'customizer-saved' );
		}, 2000 );
	} );

	// Add save indicator styles
	$( 'head' ).append( `
		<style>
			.customizer-saving::before { content: "💾"; position: fixed; top: 20px; right: 20px; z-index: 9999; font-size: 24px; }
			.customizer-saved::before { content: "✅"; position: fixed; top: 20px; right: 20px; z-index: 9999; font-size: 24px; }
		</style>
	` );

} )( jQuery );
