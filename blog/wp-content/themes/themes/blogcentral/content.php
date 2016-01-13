<?php 
/**
 * Default template for post content. 
 *
 * @since BlogCentral 1.0.0
 *
 * @global array $blogcentral_layout_opts Passed from various blog pages.
 * @global array $blogcentral_post_opts Passed from various blog pages.
 * @global array $blogcentral_post_meta. Passed from various blog pages.
 *
 * @package BlogCentral
 * @subpackage content.php
 */
 
// Reset variables
$media_wrap_width = $media_wrap_height = $blogcentral_media = $blogcentral_post_format_icon = '';
$media_flag = false;

// Extract variables for use in the layout-item-wrapper-begin.php template.
extract( $blogcentral_post_opts );

// If layout3, then do not display media or post format icon.
if ( isset( $blogcentral_layout_opts['layout'] ) && ( 'layout3' !== $blogcentral_layout_opts['layout'] ) ) {
	/*
	 * If height is set for the media container, then add full-height class to stretch the 
	 * media to the whole container, else the media will have auto height.
	 */
	if ( ! empty( $blogcentral_layout_opts['media_wrap_height'] ) ) {
		$blogcentral_extra_li_class .= ' full-height';
	} elseif ( isset( $blogcentral_layout_opts['img_size'] ) && 'scale' === $blogcentral_layout_opts['img_size'] ) {
		$blogcentral_extra_li_class .= ' auto-height';
	}
	
	if ( ! empty( $blogcentral_thumb ) ) {
		$blogcentral_media .= '<a href="' . esc_url( get_permalink() ) . '">' . $blogcentral_thumb . '</a>';
		$media_flag = true;
	}
	
	// Construct the html for the media.
	if ( ! empty( $blogcentral_post_meta['media'] ) ) {
		$media_flag = true;
		$blogcentral_extra_li_class .= ' has-media';
		
		foreach ( $blogcentral_post_meta['media'] as $media ) {
			$width = $height = '';
			
			// Set the width and height of the media based on theme options.
			if ( isset( $blogcentral_layout_opts['img_size'] ) ) {
				if ( 'default' === $blogcentral_layout_opts['img_size'] ) {
					$width = ! empty( $media['width'] ) ? ' width="' . esc_attr( $media['width'] ) . '"' : '';
					$height = ! empty( $media['height'] ) ? ' height="' . esc_attr( $media['height'] ) . '"' : '';
				}
			}
			
			// Construct the $blogcentral_media variable. This variable is used in the layout-item-wrapper-begin template.
			$alt = ! empty( $media['alt_txt'] ) ? esc_attr( $media['alt_txt'] ) : '';
			$url = ! empty( $media['url'] ) ? esc_url( $media['url'] ) : '';
		
			$blogcentral_media .=  '<a href="' . esc_url( get_permalink() ) . '"><img src="' . $url . '"';
			
			if ( $alt ) {
				$blogcentral_media .= ' alt="' . $alt  . '"';
			}
			if ( $width ) {
				$blogcentral_media .= ' ' . $width;
			}
			if( $height ) {
				$blogcentral_media .= ' ' . $height;
			}
			
			$blogcentral_media .= ' /></a>';
		} // End foreach	
	} // End if
	
	if ( $media_flag ) {
		$blogcentral_extra_li_class .= ' has-media';
	}
	
	// Construct the $blogcentral_post_format_icon variable. This variable is used in the postmeta-layout template.
	$blogcentral_post_format_icon = '';
	
	if ( ! empty( $blogcentral_layout_opts['display_format_icon'] ) && ! empty( $blogcentral_layout_opts['standard_icon'] ) ) {
		$blogcentral_post_format_icon = '<i class="post-format-icon fa ' . esc_attr( $blogcentral_layout_opts['standard_icon'] ) . '"></i>';
	}
} // End if 'layout5' !== $blogcentral_layout_opts['layout']

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
