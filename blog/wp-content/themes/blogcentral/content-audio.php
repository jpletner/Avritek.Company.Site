<?php
/** 
 * Template for post audio format content.
 *
 * @since BlogCentral 1.0.0
 *
 * @global array $blogcentral_layout_opts Passed from various blog pages.
 * @global array $blogcentral_post_opts Passed from various blog pages.
 * @global array $blogcentral_post_meta. Passed from various blog pages.
 *
 * @package BlogCentral
 * @subpackage content-audio.php
 */
	
// Reset variables.
$media_wrap_width = $media_wrap_height = $blogcentral_media = $blogcentral_post_format_icon = '';

// Extract variables for use in the layout-item-wrapper-begin.php template.
extract( $blogcentral_post_opts );

// If the layout is 3, then do not display media or post format icon.
if ( isset( $blogcentral_layout_opts['layout'] ) && ( 'layout3' !== $blogcentral_layout_opts['layout'] ) ) {
	/*
	 * If height is set for the media container, then add full-height class to stretch the 
	 * media to the whole container, else the media will have auto height.
	 */
	if ( ! empty( $blogcentral_layout_opts['media_wrap_height'] ) ) {
		$blogcentral_extra_li_class .= ' full-height';
	} else {
		$blogcentral_extra_li_class .= ' auto-height';
	}
	
	// Construct the $blogcentral_media variable. This variable is used in the layout-item-wrapper-begin template.
	if ( ! empty( $blogcentral_post_meta['media_embed'] ) ) {
		$blogcentral_extra_li_class .= ' has-media';
		
		// Is an iframe, so do not html escape.
		$blogcentral_media = str_replace( "&", "&amp;", $blogcentral_post_meta['media_embed']['code'] );
	}
	
	// Construct the $blogcentral_post_format_icon variable. This variable is used in the postmeta-layout template.
	if ( ! empty( $blogcentral_layout_opts['display_format_icon'] ) && ! empty( $blogcentral_layout_opts['audio_icon'] ) ) {
		$blogcentral_post_format_icon = '<i class="post-format-icon fa ' . esc_attr( $blogcentral_layout_opts['audio_icon'] ) .
			'"></i>';	
	}
} // End if 'layout3' !== $blogcentral_layout_opts['layout'].

/**
 * Template file that constructs the various html fragments for the layout-item-wrapper-begin template.
 */
include( locate_template( 'templates/postmeta-layout.php', false, false ) );

// Call the post_class function and pass it $blogcentral_extra_li_class variable, after converting to array.
$class_array = explode( ' ', trim( $blogcentral_extra_li_class ) );
ob_start();
post_class( $class_array );
$blogcentral_extra_li_class = ob_get_clean();

/**
 * Template file for each post item.
 */
include( locate_template( 'templates/layout-item-wrapper-begin.php', false, false ) );