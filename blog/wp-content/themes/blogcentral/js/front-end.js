/**
 * Theme Name: BlogCentral
 * Theme URI: http://4bzthemes.com/theme/blogcentral/
 * Author: 4bzthemes
 * Author URI: http://4bzthemes.com
 * File Description: javascript front end file.
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage front-end.js
 ------------------------------------------------------------------------
	Table of Contents
	
	1. Utility Functions 
	2. Initialization
	3. Toggle Functions
	4. Fixed Header Functions
	5. Event Handlers
-------------------------------------------------------------------------*/

/**
 * 1. Utility Functions
 *-----------------------------------------------------------------------*/

/**
 * Split an id by '-'
 *
 * @since 1.0.0
 * @param string id. Required. Id to be parsed.
 * @return Array. The id parsed into parts.
 */
function blogcentral_parse_id( id ) {
	return id.split( "-" );
}

/**
 * Get the index of an element by parsing its id.
 *
 * @since 1.0.0
 *
 * @param string id. Required. Id to be parsed.
 * @param int offset. Required. Array offset.
 * @return Int. The index.
 */
function blogcentral_get_index( id, offset ) {
	var split = blogcentral_parse_id( id );
	
	return parseInt( split[split.length-offset] );
}

/**
 * Calculate number of columns to display based on the value of width
 *
 * @since 1.0.0
 *
 * @param int width. Required. Width used to determine number of columns.
 * @param int cols. Required. Original number of cols.
 * @return int. The number of columns.
 */
function get_grid_size( width, cols ) {
	return ( width <= 640 ) ? 1 : cols;	
}

( function( $ ) {
	"use strict";
	$( document ).on( 'ready', function() {
		
		/**
		 * 2. Initialization
		 *-----------------------------------------------------------------------*/
 
		/**
		 * Initialization code does the following
		 *		1. Hide all elements with class hide.
		 *		2. Add an icon to toggle the main menu if the width of the window is less than 641px.
		 *
		 * @since 1.0.0
		 */
		function initialize() {
			$( '.hide' ).css( 'display', 'none' );
			
			if ( 640 >= window.innerWidth ) {
				$( '#access li.menu-item-has-children > a' ).after( '<span class="show-sub fa fa-angle-down"></span>' );
				$( '#access li.menu-item-has-children .show-sub' ).click( toggle_submenu );
			}					
		}
		initialize();
		
		/**
		 * 3. Toggle Functions
		 *-----------------------------------------------------------------------*/
		
		/**
		 * Handles the display of submenus
		 *
		 * @since 1.0.0
		 * @param object event. Object passed to handler by jquery.
		 */
		function toggle_submenu( event ) {
			var element = $( event.target )
							.parent()
							.children( 'ul' ),
				display = element.css( 'display' );
					
			if ( 'none' === display ) {
				element.css( 'display', 'block' );
			} else {
				element.css( 'display', 'none' );
			}
		}
		
		/**
		 * Handle the display of the style chooser.
		 *
		 * @since 1.0.0
		 * @param object event. Object passed to handler by jquery.
		 */
		function toggle_style_chooser( event ) {
			var chooser = $( '.style-chooser' ),
				display = chooser.css( 'display' );
			
			if ( 'none' === display ) {
				chooser.css( 'height', 'auto' ).css( 'display', 'inline-block' );
			} else {
				chooser.css( 'display', 'none' );
			}
		}
	
		/**
		 * Handle the display of the contact form when the hire me button is clicked in the call to action container.
		 *
		 * @since 1.0.0
		 * @param object event. Object passed to handler by jquery.
		 */
		function toggle_contact_show( event ) {
			var parent = $( event.target )
							.parent()
							.parent(),
				display = $( '.hide-show', parent ).css( 'display' );
			
			if ( 'none' === display ) {
				$( '.hide-show', parent ).css( 'height', 'auto' ).show( 'slide-down' );
			} else {
				$( '.hide-show', parent ).hide( 'slide-up' ).css( 'height', '0' );
			}
		}
		
		/**
		 * 4. Fixed Header Functions
		 *-----------------------------------------------------------------------*/
		
		/**
		 * Remove the style for the fixed header.
		 *
		 * @since 1.0.0
		 */
		function remove_fixed_header() {
			// Get some of the original style.
			var menu_back = $( '#access' ).css( 'background-color' );
				
			$( '#header' ).removeClass( 'fixed-header' );
	
			$( '#header-mid' ).show( 'puff' );
			$( '#header-top' ).show();
			$( '#header-btm #logo' ).show( 'puff' );
			$( '.block-display.box-display #main-div' ).css( 'margin-top', '24px' );
			
		}
		
		/**
		 * Handle the change of the color scheme
		 *
		 * @since 1.0.0
		 * @param object event. Object passed to handler by jquery.
		 */
		function change_color_scheme( event ) {
			var target = $( event.target ),
				val = target.attr( 'class' ),
				body = $( 'body' );
			
			body.removeClass( 'color-scheme0 color-scheme1 color-scheme2 color-scheme3 color-scheme4 color-scheme5' );
			body.addClass( val );
		}
		
		/**
		 * Handle the animation of the progressbars when in viewport
		 *
		 * @since 1.0.0
		 */
		function animate_progressbars() {	
			$( '.progressbar' ).each( function( i ) {
				var id = $( this ).attr( 'id' ),
					value = $( this ).data( 'percentage' );
				
				$( this ).animate( { width: value + '%' }, 150 );
			});
		}	
		
		/**
		 * 5. Event Handlers
		 *-----------------------------------------------------------------------*/
		$( '.call-to-action.connect > span' ).click( toggle_contact_show );
		$( '.toggle-style-chooser' ).click( toggle_style_chooser );
		$( '.style-chooser span' ).click( change_color_scheme );
		
		$( '.skill-lbl-cont' ).waypoint( animate_progressbars,	
			{ offset: 700, triggerOnce: true } );
		
		// Masonry
		var blogcentral_msnry = $( '.blogcentral-masonry' );
		blogcentral_msnry.masonry({ gutter: '.gutter-sizer'});
		
		
		// Fix to apply masonry when all images are loaded.
		blogcentral_msnry.imagesLoaded( function() {
			blogcentral_msnry.masonry();
		});
		
		// Flexsliders
		
		// Slider for post type gallery if the option to display as a slideshow is enabled.
		$( '.flex-gallery' ).each( function( i ) {
			$( this ).flexslider( {
				controlNav: false,
				animationLoop: true,
				slideshow: true, 
				selector: ".gallery-slides > li",
				start: function() {
					// Since the flexslider was originally not displayed, have to reapply the masonry layout.
					blogcentral_msnry.masonry();
				}
			});
		});
		
		// Flexslider for the actual slideshow component.
		$( '.flexslider.slideshow' ).each( function( i ) {
			var show = $( this ),
				cols = show.attr( 'data-blogcentral-cols' ),
				animation = show.attr( 'data-animation' ),
				sync = show.attr( 'data-sync' ),
				asNavFor = show.attr( 'data-asNavFor' ),
				useCSS = show.attr( 'data-useCSS' ),
				easing = show.attr( 'data-easing' );
			
			$( this ).flexslider( {
				animation: animation,
				animationLoop: true,
				controlNav: false,
				easing: easing,
				useCSS: useCSS,
				slideshow: true, 
				sync: sync,
				smoothHeight: true,
				asNavFor: asNavFor,
				itemWidth: 3000,
				minItems: cols, 
				maxItems: cols
			} );
		});
		
		// Sticky Header
		if ( blogcentral_object.header_sticky ) {
			// Display the top fixed menu on scroll.
			var header_height = $( '#header' ).outerHeight(),
				header = $( '#header' ),
				headTop = header.offset().top;
				
			$( document ).scroll( function () {
				if ( 640 < window.innerWidth ) {
					var y = $( this ).scrollTop(),
						header_width = $( '#main-div' ).innerWidth();
						
					if ( y > header_height ) {
						$( '#header' ).addClass( 'fixed-header' );
						$( '#header' ).css( 'min-height', '0' );
						$( '#header-top' ).css( 'display', 'none' );
						$( '#header-mid' ).css( 'display', 'none' );
						$( '#header-btm #logo' ).css( 'display', 'none' );
						$( '#main-div' ).css( 'margin-top', '0' );
						
					} else if( 0 === y ) {
						remove_fixed_header();
					}
				}	
			} );	
		}
		
		// Responsive main menu. Toggle the menu when the icon is clicked.
		$( '#logo .fa' ).click( function() {
			var menu = $( '#access' ).css( 'display' );
		
			if ( 'none' === menu ) {
				$( '#access' ).show( 'slide-up' );
			} else {
				$( '#access' ).hide( 'slide-down' );
			}
		});
		
		/**
		 * This functionality is needed to reset any style that might be changed when the window
		 * is resized. Otherwise the user would have to reload the page to see it correctly. If this is
		 * of no concern, then you could safely delete the following lines of code.
		 */
			 
		/**	
		 * On window resize need to do the following: Reset settings that might be changed when the window is resized.
		 */
		$( window ).resize( function() {
			if ( 640 < window.innerWidth ) {
				$( '.show-sub' ).remove();
				$( '#access ul ul' ).hide(); 
				$( '#access' ).css( 'display', 'block' );
				
			} else {
				remove_fixed_header();
				$( '.show-sub' ).remove();
				$( '#access' ).css( 'display', 'none' );
				$( '#access li.menu-item-has-children > a' ).after( '<span class="show-sub fa fa-angle-down"></span>' );
				$( '#access li.menu-item-has-children .show-sub' ).click( toggle_submenu );
			}
		} );
		// End of code that could safely be deleted.
	});
})( jQuery );