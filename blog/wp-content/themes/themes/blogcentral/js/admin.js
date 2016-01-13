/**
 * Theme Name: BlogCentral
 * Theme URI: http://4bzthemes.com/theme/blogcentral/
 * Author: 4bzthemes
 * Author URI: http://4bzthemes.com
 * File Description: javascript admin file included on the theme options, post edit, and user profile pages.
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage admin.js
 ------------------------------------------------------------------------
	Table of Contents
	
	1. Utility Functions 
	2. Theme Options Page Functions
	3. Media Uploader Functions
	4. Event Handlers
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
 * Get the prefix of an element's id.
 *
 * Used when there's a group of elements with the same base id, differing only in its prefix.
 *
 * @since 1.0.0
 *
 * @param string id. Id to be parsed.
 * @return string. The id's prefix.
 */
function blogcentral_get_name( id ) {
	var split = blogcentral_parse_id( id );
	
	return split[0];
}

( function( $ ) {
	"use strict";
	
	/**
	 * 2. Theme Options Page Functions
	 *-----------------------------------------------------------------------*/
	
	
	/**
	 * Handle selection to hide and show a container
	 * The target and the container to hide and show must be siblings.
	 *
	 * Works for select and checkboxes.
	 *
	 * @since 1.0.0
	 *
	 * @param object event. Object passed to handler by jquery.
	 */
	function blogcentral_hide_show( event ) {
		var parent = $( event.target ).parent(), 
			display = $( '.hideshow', parent ).css( 'display' );
		
		if ( $( event.target ).attr( 'checked' ) ) {
			display = 'table-row';
		} else {
			display = 'none';
		}
		
		$( '> .hideshow', parent ).css( 'display', display );
	}
	
	/**
	 * Handle click of the x button to close a box
	 *
	 * @since 1.0.0
	 *
	 * @param object event. Object passed to handler by jquery.
	 */
	function blogcentral_close_box( event ) {
		var target = $( event.target ), 
			parent = target.parent();
		
		parent.remove();
	}
	
	/**
	 * Handle font selection.
	 *
	 * @since 1.0.0
	 *
	 * @param object event. Object passed to handler by jquery.
	 */
	function blogcentral_font_options( event ) {
		var target = $( event.target ), 
			val = target.val(), 
			id = target.attr( 'id' ), 
			i = blogcentral_get_name( id ),
			parent = target.parent(), 
			font_class = target.find( ":selected" ).attr( 'class' ), 
			type = '',
			paramsJson = '', 
			url = target.data( 'blogcentral-font-url' );
		
		if ( 'no_font' === val ) {
			$( '#' + i + '-font-variant-cont' ).text( '' );
			$( '#' + i + '-font-subsets-cont' ).text( '' );
			return;
		}
		
		if ( 'system_fonts' === font_class ) {
			type = 'system';
		} else {
			if ( 'google_fonts' === font_class )  {
				type = 'google';
			}
		}
		
		$( 'input[type=hidden]', parent ).val( type );
		
		$( '#' + i + '-font-variant-cont' ).text( '' );
		$( '#' + i + '-font-subsets-cont' ).text( '' );
		
		paramsJson = {
			'font': val, 
			'name': i, 
			'type': font_class
			};
	
		// Make the ajax call to retrieve the font options for the font selected.
		$.ajax( { 
			url: url,
			type: "POST",
			timeout: 5000,
			dataType: "json",
			error: function( xhr ) {
					alert( 'error in ajax '+ xhr.status + ' ' + xhr.statusText );
				   },
			data: paramsJson,
			beforeSend: function() {
							$( '.loader', parent ).show();
						},
			complete: function(){
						$( '.loader', parent ).hide();
					  },
			success: function( data ) {
						/*
						 * data should hold the variants and subsets fragment that should be appended to 
						 * the fonts container.	
						 */
						$( '#' + i + '-font-variant-cont' ).append( data.variants );
						$( '#' + i + '-font-subsets-cont' ).append( data.subsets );
					 }
					
			} ); // End of ajax call.
	}
	
	/**
	 * Show component layout specific options.
	 *
	 * @since 1.0.0
	 * @param object event. Object passed to handler by jquery.
	 */
	function blogcentral_show_sub_options( event ) {
		var target = $( event.target ), 
			val = target.val(), 
			name = target.data( 'blogcentral-name' ), 
			html = '',
			img_file = '';
		
		$( '.' + name + '-layout-sub-layout' ).css( 'display', 'none' );
		$( '.' + name + '-layout-sub-' + val, $( this ).closest( 'td' ) ).css( 'display', 'block' );
		
	}
	
	/**
	 * Handle border selection
	 *
	 * @since 1.0.0
	 * @param object event. Object passed to handler by jquery.
	 */
	function blogcentral_show_border( event ) {
		var target = $( event.target ), 
			parent = target.parent(), 
			val = target.val(), 
			border = $( '.border-ex', parent );
		
		border
			.text( '' )
			.removeClass( 'div1 div2 div3' );
		
		if ( 'default' !== val && '0' !== val ) {
			border
				.text( val )
				.addClass( val );
		}
	}
	
	/**
	 * Add markup to the slides-wrap container for a new slide
	 *
	 * @since 1.0.0
	 *
	 * @param object event. Object passed to handler by jquery.
	 * @return false.
	 */
	function blogcentral_add_slide( event ) {
		var target = $( event.target ),
			name = target.attr( 'data-name' ),
			parent = $( ".slides-wrap", $( this ).closest( 'td' ) ),
			html = '<li class="slides-cont"><button type="button" class="button-2 delete-row">' + 
					blogcentral_object.Delete + '</button><br /><br />';
					
			html += '<label>' + blogcentral_object.slide_template_instr + 
				'</label><br />' + '<select class="template-select">' + '<option value="0"></option>' + '<option value="1">' + 
				blogcentral_object.slide_template_1 + '</option>' + '<option value="2">' + 
				blogcentral_object.slide_template_2 + '</option>' + '<option value="3">' + 
				blogcentral_object.slide_template_3 + '</option>' + '</select><br />';
			
			html += '<label>' + blogcentral_object.enter_slide + 
				'</label><div class="loader"></div><textarea class="slide-html" name="' + name + 
				'[]" rows="20" cols="50"></textarea></li>';
		
		parent.append( html );
		
		$( '.sortable' ).sortable();
		
		return false;
	}
	
	/**
	 * Handle slide template selection.
	 *
	 * @since 1.0.0
	 * @param object event. Object passed to handler by jquery.
	 */
	function blogcentral_get_template( event ) {
		var target = $( event.target ), 
			val = target.val(), 
			parent = target.parent(), 
			url = blogcentral_object.admin_template_ajax, 
			paramsJson = '';
		
		// Send the selected template.
		paramsJson = { 'template': val };
		
		// Make the ajax call to retrieve the template code for the template selected.
		$.ajax( { 
			url: url,
			type: "POST",
			timeout: 5000,
			dataType: "json",
			error: function( xhr ) {
				alert( 'error in ajax ' + xhr.status + ' ' + xhr.statusText );
			},
			data: paramsJson,
			beforeSend: function() {
				$( '.loader', parent ).show();
			},
			complete: function(){
				$( '.loader', parent ).hide();
			},
			success: function( data ) {
				// Insert the template code into the textarea.
				$( '.slide-html', parent ).val( data.template );
			}			
		} );
	}
	
	/**
	 * Handle click to show the font awesome icons box to choose an icon.
	 *
	 * @since 1.0.0
	 *
	 * @param object event. Object passed to handler by jquery.
	 */
	function blogcentral_display_icons_box( event ) {
		var i, item, 
			html = '<div class="blogcentral-icon-chooser"><div class="closer">' + blogcentral_object.close + ' X</div>',
			target = $( event.target ), 
			parent = target.parent(), 
			fa = blogcentral_object.fa_items;
		
		for ( i = 0; i < fa.length; ++i ) {
			item = fa[i];
			html += '<i value="' + item + '" class="fa ' + item + '"></i>';
		}
		
		html += '</div>';
		
		$( '.blogcentral-icon-chooser', parent ).remove();
		
		parent.append( html );
		
		$( '.blogcentral-icon-chooser .fa' ).on( 'click', blogcentral_add_icon_field );
	}
	
	/**
	 * Handle selection of a font awesome icon. 
	 * Inserts the icon into the corresponding text field.
	 *
	 * @since 1.0.0
	 *
	 * @param object event. Object passed to handler by jquery.
	 */
	function blogcentral_add_icon_field( event ) {
		var target = $( event.target ), 
			parent = target.parent().parent(), 
			val = target.attr( 'value' );
	
		$( '.blogcentral-icon-field', parent ).val( val );
	}
	
	/**
	 * 3. Media Uploader Functions
	 *-----------------------------------------------------------------------*/
	
	// Global variables used in the media uploader functions.
	var blogcentral_custom_uploader, blogcentral_index;
	
	/**
	 * Handle media file(s) selection in the media uploader, specifically images
	 *
	 * @since 1.0.0
	 *
	 * @param object event. Object passed to handler by jquery.
	 */
	function blogcentral_update_file_url( event ) {
		var image = '',
			width = '',
			height = '',
			alt = '',
			imgs_html = '',
			selection = blogcentral_custom_uploader.state().get( 'selection' );
		
		// Map image(s) selections.
		selection.map( function( attachment ) {
			attachment = attachment.toJSON();
			
			var id = attachment.id,
				i;
		
			if ( 'image' === attachment.type ) {
				image = attachment.url;
				width = attachment.width;
				height = attachment.height;
				alt	= attachment.alt;
				return;
			}
		});
		
		$( '#' + blogcentral_index + '-width' ).val( width );
		$( '#' + blogcentral_index + '-height' ).val( height );
		$( '#' + blogcentral_index + '-alt' ).val( alt );
		$( '#' + blogcentral_index ).val( image );
	}
	
	/**
	 * Handle the display of the WP Media Uploader
	 *
	 * Add handler for selection of files in the uploader.
	 *
	 * @since 1.0.0
	 *
	 * @param object event. Object passed to handler by jquery.
	 */
	function blogcentral_display_media_uploader( event ) {
		var id = $( event.target ).data( 'blogcentral-textbox' );
		
		blogcentral_index = id;
		
		event.preventDefault();
		
		// If the uploader object has already been created, reopen the dialog.
		if ( blogcentral_custom_uploader ) {
			blogcentral_custom_uploader.open();
			return;
		}
		
		// Extend the wp.media object.
		blogcentral_custom_uploader = wp.media.frames.file_frame = wp.media({
			title: blogcentral_object.choose_image,
			button: {
				text: blogcentral_object.choose_image
			},
			multiple: true
		});
		
		// Handle the file selection.
		blogcentral_custom_uploader.on( 'select', blogcentral_update_file_url );

		// Open the uploader dialog.
		blogcentral_custom_uploader.open();
	}
	
	/**
	 * Build the component wrapper options from the user inputs
	 *
	 * @since 1.1.0
	 *
	 * @param string shortcode. Required. The name of the shortcode that is being built.
	 * @return string. The wrapper options fragment of the shortcode.
	 */
	function blogcentral_build_wrapper_options( shortcode ) {
		if ( ! shortcode ) {
			return;
		}
		
		var shortcode_frag = '',
			tag = '',
			// The regular expression used to delete all "'" from the values, so not to close attributes prematurely.	
			regexp = /'/g;
		
		tag = $( "[name='blogcentral[" + shortcode + "][title_text]']" ).val();
		if ( tag ) {
			shortcode_frag += " title_text='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][title_border]']" ).val();
		if ( 'undefined' !== typeof tag && "0" !== tag ) {
			shortcode_frag += " title_border='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][tagline_text]']" ).val();
		if ( tag ) {
			shortcode_frag += " tagline_text='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][tagline_border]']" ).val();
		if ( 'undefined' !== typeof tag && "0" !== tag ) {
			shortcode_frag += " tagline_border='" + tag + "'";
		}
		tag = $( "[name='blogcentral[" + shortcode + "][wrap_class]']" ).val();
		if ( tag ) {
			shortcode_frag += " wrap_class='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][title_class]']" ).val();
		if( tag ) {
			shortcode_frag += " title_class='" + tag.replace( regexp, '' ) + "'";
		}
		
		return shortcode_frag;
	}
	
	/**
	 * Build the main options from the user inputs that most shortcodes share
	 *
	 * @since 1.0.0
	 *
	 * @param string shortcode. Required. The name of the shortcode that is being built.
	 * @return string. The main options fragment of the shortcode.
	 */
	function blogcentral_build_main_options( shortcode ) {
		var shortcode_frag = '', 
			tag = '',
			// The regular expression to delete all "'" from the values, so not to close attributes prematurely.
			regexp = /'/g;
		
		tag = $( 'input[name="blogcentral[' + shortcode + '][layout]"]:checked' ).val();
		if ( tag ) {
			shortcode_frag += " layout='" + tag + "'";
		}
		
		tag = $( '[name="blogcentral[' + shortcode + '][alternate]"]' ).attr( 'checked' );
		if ( tag ) {
			shortcode_frag += " alternate='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][cols]']" ).val();
		if ( tag ) {
			shortcode_frag += " cols='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][border]']" ).val();
		if ( tag ) {
			shortcode_frag += " border='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][masonry]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " masonry='" + tag + "'";	
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][alternate_color]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " alternate_color='" + tag + "'";	
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][limit]']" ).val();
		if ( tag ) {
			shortcode_frag += " limit='" + tag.replace( regexp, '' ) + "'";	
		}
		
		tag = $( "input[name='blogcentral[" + shortcode + "][post_meta_layout]']:checked" ).val();
		if ( tag ) {
			shortcode_frag += " post_meta_layout='" + tag + "'";
		}
		
		return shortcode_frag;
	}
	
	/**
	 * Build the posts specific options from the user inputs
	 *
	 * @since 1.0.0
	 *
	 * @param string shortcode. Required. The name of the shortcode that is being built.
	 * @return string. The posts specific options fragment of the shortcode.
	 */
	function blogcentral_build_posts_specific_opts( shortcode ) {
		if ( ! shortcode ) {
			return;
		}
		
		var shortcode_frag = '',
			tag = '',
			// The regular expression used to delete all "'" from the values, so not to close attributes prematurely.	
			regexp = /'/g;
		
		tag = $( "[name='blogcentral[" + shortcode + "][gallery_slideshow]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " gallery_slideshow='" + tag + "'";
		}
		
		tag = $( "input[name='blogcentral[" + shortcode + "][quote_layout]']:checked" ).val();
		if ( tag ) {
			shortcode_frag += " quote_layout='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][show_title]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " show_title='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][show_content]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " show_content='" + tag + "'";
		}
		
		tag = $("[name='blogcentral[" + shortcode + "][show_author]']").attr( "checked" );
		if ( tag ) 
			shortcode_frag += " show_author='" + tag + "'";
		
		tag = $( "[name='blogcentral[" + shortcode + "][show_date]']" ).attr( "checked" );
		if ( tag ) 
			shortcode_frag += " show_date='" + tag + "'";
		
		tag = $( "[name='blogcentral[" + shortcode + "][show_categories]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " show_categories='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][show_tags]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " show_tags='" + tag + "'"; 
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][show_stats]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " show_stats='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][show_social]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " show_social='" + tag + "'";
		}
		
		tag = $( "input[name='blogcentral[" + shortcode + "][share_title]']" ).val();
		if ( tag ) {
			shortcode_frag += " share_title='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][display_format_icon]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " display_format_icon='" + tag + "'";
		}
		
		return shortcode_frag;
	}
	
	/**
	 * Build the contact info specific options from the user inputs
	 *
	 * @since 1.1.0
	 *
	 * @param string shortcode. Required. The name of the shortcode that is being built.
	 * @return string. The contact info specific options fragment of the shortcode.
	 */
	function blogcentral_build_contact_info_specific_opts( shortcode ) {
		if ( ! shortcode ) {
			return;
		}
		
		var shortcode_frag = '',
			tag = '',
			// The regular expression used to delete all "'" from the values, so not to close attributes prematurely.
			regexp = /'/g;
		
		tag = $( "[name='blogcentral[" + shortcode + "][contact_info_layout]']:checked" ).val();
		if ( tag ) {
			shortcode_frag += " contact_info_layout='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][user_id]']" ).val();
		if ( tag ) {
			shortcode_frag += " user_id='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][show_address]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " show_address='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][show_phone]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " show_phone='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][show_email]']" ).attr( "checked" );
		if ( tag ) { 
			shortcode_frag += " show_email='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][show_url]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " show_url='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][show_social]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " show_social='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][share_title]']" ).val();
		if ( tag ) {
			shortcode_frag += " share_title='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "input[name='blogcentral[" + shortcode + "][social_share_layout]']:checked" ).val();
		if ( tag ) {
			shortcode_frag += " social_share_layout='" + tag + "'";
		}
		
		return shortcode_frag;
	}
	
	/**
	 * Build the contact info general options from the user inputs
	 *
	 * @since 1.0.0
	 *
	 * @param string shortcode. Required. The name of the shortcode that is being built.
	 * @return string. The contact info general options fragment of the shortcode.
	 */
	function blogcentral_build_contact_info_general( shortcode ) {
		if ( ! shortcode ) {
			return;
		}
		
		var shortcode_frag = '',
			tag = '',
			// The regular expression used to delete all "'" from the values, so not to close attributes prematurely.
			regexp = /'/g;
			
		tag = $( "[name='blogcentral[" + shortcode + "][contact_address]']" ).val();
		if ( tag ) {
			shortcode_frag += " contact_address='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][address_icon]']" ).val();
		if ( tag ) {
			shortcode_frag += " address_icon='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][contact_phone]']" ).val();
		if ( tag ) {
			shortcode_frag += " contact_phone='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][phone_icon]']" ).val();
		if ( tag ) {
			shortcode_frag += " phone_icon='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][contact_url]']" ).val();
		if ( tag ) {
			shortcode_frag += " contact_url='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][url_icon]']" ).val();
		if ( tag ) {
			shortcode_frag += " url_icon='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][contact_email]']" ).val();
		if ( tag ) {
			shortcode_frag += " contact_email='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][email_icon]']" ).val();
		if ( tag ) {
			shortcode_frag += " email_icon='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][contact_facebook]']" ).val();
		if ( tag ) {
			shortcode_frag += " contact_facebook='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][facebook_icon]']" ).val();
		if ( tag ) {
			shortcode_frag += " facebook_icon='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][contact_twitter]']" ).val();
		if ( tag ) {
			shortcode_frag += " contact_twitter='" + tag.replace( regexp, '' ) + "'";
		}
		tag = $( "[name='blogcentral[" + shortcode + "][twitter_icon]']" ).val();
		if ( tag ) {
			shortcode_frag += " twitter_icon='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][contact_google]']" ).val();
		if ( tag ) {
			shortcode_frag += " contact_google='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][google_icon]']" ).val();
		if ( tag ) {
			shortcode_frag += " google_icon='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][contact_linkedin]']" ).val();
		if ( tag ) {
			shortcode_frag += " contact_linkedin='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][linkedin_icon]']" ).val();
		if ( tag ) {
			shortcode_frag += " linkedin_icon='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][contact_tumblr]']" ).val();
		if ( tag ) {
			shortcode_frag += " contact_tumblr='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][tumblr_icon]']" ).val();
		if ( tag ) {
			shortcode_frag += " tumblr_icon='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][contact_instagram]']" ).val();
		if ( tag ) {
			shortcode_frag += " contact_instagram='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][instagram_icon]']" ).val();
		if ( tag ) {
			shortcode_frag += " instagram_icon='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][contact_pinterest]']" ).val();
		if ( tag ) {
			shortcode_frag += " contact_pinterest='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[" + shortcode + "][pinterest_icon]']" ).val();
		if ( tag ) {
			shortcode_frag += " pinterest_icon='" + tag.replace( regexp, '' ) + "'";
		}
		
		return shortcode_frag;
	}
	
	/**
	 * Build the contact form specific options from the user inputs
	 *
	 * @since 1.1.0
	 *
	 * @return string. The contact form specific options fragment of the shortcode.
	 */
	function blogcentral_build_contact_form_specific_opts() {
		var shortcode_frag = '',
			tag = '',
			// The regular expression used to delete all "'" from the values, so not to close attributes prematurely.
			regexp = /'/g;
		
		tag = $( "[name='blogcentral[contact_form][layout]']:checked" ).val();
		if ( tag ) {
			shortcode_frag += " contact_form_layout='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[contact_form][show_contact]']" ).attr( "checked" );
		if ( tag ) { 
			shortcode_frag += " show_contact='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[contact_form][show_map]']" ).attr( "checked" );
		if ( tag ) {
			shortcode_frag += " show_map='" + tag + "'";
		}
		
		tag = $( "[name='blogcentral[contact_form][google_app_id]']" ).val();
		if ( tag ) {
			shortcode_frag += " google_app_id='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( "[name='blogcentral[contact_form][contact_form]']" ).val();
		if ( tag ) {
			var regexp2 = /\[/g;
			
			// Replace '[' and ']' with '(' and ')' respectively, so not to cause any problems with the shortcode syntax.
			shortcode_frag += " contact_form='" + tag
				.replace( regexp, '' )
				.replace( regexp2, '(' )
				.replace( /\]/g, ')' ) + "'";
		}
		
		return shortcode_frag;
	}
		
	/**
	 * Build the slideshow specific options from the user inputs
	 *
	 * @since 1.0.0
	 *
	 * @return string. The slideshow specific options fragment of the shortcode.
	 */
	function blogcentral_build_slideshow_specific_opts() {
		var shortcode_frag = '',
			tag = '',
			// The regular expression used to delete all "'" from the values, so not to close attributes prematurely.
			regexp = /'/g,
			val = '',
			sep = '';
			
		tag = $( '[name="blogcentral[slideshow][slideshow_layout]"]:checked' ).val();
		if ( tag ) {
			shortcode_frag += " slideshow_layout='" + tag + "'";
		}
		
		tag = $( '[name="blogcentral[slideshow][animation]"]' ).val();
		if ( tag ) { 
			shortcode_frag += " animation='" + tag + "'";
		}
		
		tag = $( '[name="blogcentral[slideshow][sync]"]' ).val();
		if ( tag ) { 
			shortcode_frag += " sync='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( '[name="blogcentral[slideshow][asNavFor]"]' ).val();
		if ( tag ) { 
			shortcode_frag += " asNavFor='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( '[name="blogcentral[slideshow][easing]"]' ).val();
		if ( tag ) { 
			shortcode_frag += " easing='" + tag.replace( regexp, '' ) + "'";
		}
		
		tag = $( '[name="blogcentral[slideshow][useCSS]"]:checked' ).val();
		if ( tag ) { 
			shortcode_frag += " useCSS='" + tag + "'";
		}
		
		tag = new Array();

		// Construct the slides. Separate them with '=>'.
		$( '[name="blogcentral[slideshow][slides][]"]' ).each( function( i ) {
			val += sep + $( this ).val().replace( regexp, '' );
			sep = '=>';
		});
		
		if ( tag ) {
			shortcode_frag += " slides='" + val + "'";
		}	
		
		return shortcode_frag;
	}
		
	/**
	 * Start of functions to override the functions provided by 4bzCore plugin to construct the string for the 
	 * different shortcodes.
	 */
	if ( typeof fourbzcore != "undefined") {
		/**
		 * Build the contact info shortcode from the user inputs
		 *
		 * @since 1.1.0
		 *
		 * @return string. The constructed contact info shortcode.
		 */
		fourbzcore.fourbzcore_build_contact_info = function() {
			var shortcode_frag = "[4bzcore_contact_info";
			
			shortcode_frag += blogcentral_build_wrapper_options( 'contact_info' );
			shortcode_frag += blogcentral_build_contact_info_specific_opts( 'contact_info' );
			shortcode_frag += blogcentral_build_contact_info_general( 'contact_info' );
			shortcode_frag += ']';
			
			return shortcode_frag;
		}
		
		/**
		 * Build the contact form shortcode from the user inputs
		 *
		 * @since 1.0.0
		 *
		 * @return string. The constructed contact form shortcode.
		 */
		fourbzcore.fourbzcore_build_contact_form = function() {
			var shortcode_frag = "[4bzcore_contact_form";
			
			shortcode_frag += blogcentral_build_wrapper_options( 'contact_form' );
			shortcode_frag += blogcentral_build_contact_form_specific_opts();
			shortcode_frag += blogcentral_build_contact_info_specific_opts( 'contact_form' );
			shortcode_frag += blogcentral_build_contact_info_general( 'contact_form' );
			shortcode_frag += ']';
			
			return shortcode_frag;
		}
		
		/**
		 * Build the progressbars shortcode from the user inputs
		 *
		 * @since 1.1.0
		 *
		 * @return string. The constructed progressbars shortcode.
		 */
		fourbzcore.fourbzcore_build_progressbars = function() {
			var shortcode_frag = "[4bzcore_progressbars",
				tag = '',
				// The regular expression used to delete all "'" from the values, so not to close attributes prematurely.
				regexp = /'/g;
				
			shortcode_frag += blogcentral_build_wrapper_options( 'progressbars' );
			
			tag = $( "[name='blogcentral[progressbars][cols]']" ).val();
			if ( tag ) {
				shortcode_frag += " cols='" + tag + "'";
			}
			
			tag = {};
			
			// Get each skill and add to tag array.
			$( '.add-skill-name').each(
				function( i ) {
					var index = $( this ).data( 'skill-index' ),
						name = $( this ).val().replace( regexp, '' ),
						value = $( '[name="blogcentral[progressbars][items][' + index + '][value]"]' ).val().replace( regexp, '' );
						
					tag[name] = value;
				} );
			
			regexp = /\[/g;
			
			/*
			 * Convert the tag array into a json string, replacing the brackets with parentheses, so not to cause
			 * any problems with the brackets in the shortcode string.
			 */
			if ( tag ) {
				shortcode_frag += " items='" + JSON.stringify( tag )
					.replace( regexp, '(' )
					.replace( /\]/g, ')' ) + 
					"'";
			}
			
			shortcode_frag += ']';
			
			return shortcode_frag;
		}
		
		/**
		 * Build the slideshow shortcode from the user inputs
		 *
		 * @since 1.1.0
		 *
		 * @return string. The constructed slideshow shortcode.
		 */
		fourbzcore.fourbzcore_build_slideshow = function() {
			var shortcode_frag = "[4bzcore_slideshow";
			
			shortcode_frag += blogcentral_build_wrapper_options( 'slideshow' );
			shortcode_frag += blogcentral_build_slideshow_specific_opts();
			shortcode_frag += ']';
			
			return shortcode_frag;
		}
		
		/**
		 * Build the facebook comments shortcode from the user inputs
		 *
		 * @since 1.1.0
		 *
		 * @return string. The constructed facebook comments shortcode.
		 */
		fourbzcore.fourbzcore_build_facebook_comments = function() {
			var shortcode_frag = "[4bzcore_facebook_comments",
				tag = '';
			 
			shortcode_frag += blogcentral_build_wrapper_options( 'facebook_comments' );
			
			tag = $( '[name="blogcentral[facebook_comments][limit]"]' ).val();
			if ( tag ) { 
				shortcode_frag += " limit='" + tag + "'";
			}
			
			shortcode_frag += ']';
			
			return shortcode_frag;
		}
		
		/**
		 * Build the post-based shortcode from the user inputs
		 *
		 * @since 1.1.0
		 *
		 * @param string shortcode. Required. The name of the shortcode that is being built.
		 * @return string. The constructed post-based shortcode.
		 */
		fourbzcore.fourbzcore_build_posts = function( shortcode ) {
			if ( ! shortcode ) {
				return;
			}
			
			var shortcode_frag = "[4bzcore_" + shortcode;
			
			shortcode_frag += blogcentral_build_wrapper_options( shortcode );
			shortcode_frag += blogcentral_build_main_options( shortcode );
			shortcode_frag += blogcentral_build_posts_specific_opts( shortcode);
			shortcode_frag += ']';
			
			return shortcode_frag;
		}
		
		/**
		 * Build the flickr photos shortcode from the user inputs
		 *
		 * @since 1.1.0
		 *
		 * @return string. The constructed flickr photos shortcode.
		 */
		fourbzcore.fourbzcore_build_flickr_photos = function () {
			var shortcode_frag = "[4bzcore_flickr_photos",
				tag = '';
			 
			shortcode_frag += blogcentral_build_wrapper_options( 'flickr_photos' );
			
			tag = $( '[name="blogcentral[flickr_photos][limit]"]' ).val();
			if ( tag ) { 
				shortcode_frag += " limit='" + tag + "'";
			}
			
			tag = $( '[name="blogcentral[flickr_photos][user_id]"]' ).val();
			if ( tag ) { 
				shortcode_frag += " user_id='" + tag + "'";
			}
			
			shortcode_frag += ']';
			
			return shortcode_frag;
		}
		
		/**
		 * Build the author bio shortcode from the user inputs
		 *
		 * @since 1.0.0 
		 *
		 * @return string. The constructed author bio shortcode.
		 */
		fourbzcore.fourbzcore_build_author_bio = function () {
			var shortcode_frag = "[4bzcore_author_bio",
				tag = '';
			 
			shortcode_frag += blogcentral_build_wrapper_options( 'author_bio' );
			
			shortcode_frag += ']';
			
			return shortcode_frag;
		}
		
		/**
		 * Build the image text shortcode from the user inputs
		 *
		 * @since 1.0.0 
		 *
		 * @return string. The constructed image text shortcode.
		 */
		fourbzcore.fourbzcore_build_image_text = function () {
			var shortcode_frag = "[4bzcore_image_text",
				tag = '';
			 
			shortcode_frag += blogcentral_build_wrapper_options( 'image_text' );
			
			
			tag = $( '[name="blogcentral[image_text][layout]"]' ).val();
			if ( tag ) { 
				shortcode_frag += " layout='" + tag + "'";
			}
		
			tag = $( '[name="blogcentral[image_text][image_url]"]' ).val();
			if ( tag ) { 
				shortcode_frag += " image_url='" + tag + "'";
			}
			
			tag = $( '[name="blogcentral[image_text][image_url_width]"]' ).val();
			if ( tag ) { 
				shortcode_frag += " image_url_width='" + tag + "'";
			}
			
			tag = $( '[name="blogcentral[image_text][image_url_height]"]' ).val();
			if ( tag ) { 
				shortcode_frag += " image_url_height='" + tag + "'";
			}
			
			tag = $( '[name="blogcentral[image_text][image_url_alt]"]' ).val();
			if ( tag ) { 
				shortcode_frag += " image_url_alt='" + tag + "'";
			}
			
			tag = $( '[name="blogcentral[image_text][content]"]' ).val();
			if ( tag ) { 
				shortcode_frag += " content='" + tag + "'";
			}
			
			shortcode_frag += ']';
			
			return shortcode_frag;
		}
	}
	
	/**
	 * 4. Event Handlers
	 *-----------------------------------------------------------------------*/
	$( document ).on( 'ready', function() {	
		// Sortable
		$( '.sortable' ).sortable();
		
		// Tabs	
		$( '.tabs-layout1' ).tabs( { show: { effect: "fadeIn", duration:100 }} );
		$( '.tabs-layout2' ).tabs( { show: { effect: "fadeIn", duration:100 }} );
		
		/**
		 * WP Color Picker
		 *-----------------------------------------------------------------------*/
		
		// For theme options page.
		$( '.blogcentral-color-field' ).wpColorPicker();
		
		/**
		 * General Theme Options
		 *-----------------------------------------------------------------------*/
		$( 'body' ).on( 'click', '.layout-show', blogcentral_show_sub_options );
		$( 'body' ).on( 'change', '.header-border-select', blogcentral_show_border );
		$( 'body' ).on( 'click', '.blogcentral-add-slide', blogcentral_add_slide );
		$( 'body' ).on( 'change', '.template-select', blogcentral_get_template );
		$( '.font-select' ).change( blogcentral_font_options );
		$( 'body' ).on( 'click', '.displaymaster', blogcentral_hide_show );
		$( 'body' ).on( 'focus', '.blogcentral-icon-field', blogcentral_display_icons_box );
		$( 'body' ).on( 'click', '.closer', blogcentral_close_box );
		$( 'body' ).on( 'click', '.icon-image-btn', blogcentral_display_media_uploader );
	});
})( jQuery );