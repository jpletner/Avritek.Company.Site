<?php
/**
 * Template for post quote format content
 *
 * @since BlogCentral 1.0.0
 *
 * @global array $blogcentral_layout_opts Passed from various blog pages.
 * @global array $blogcentral_post_opts Passed from various blog pages.
 * @global array $blogcentral_post_meta. Passed from various blog pages.
 *
 * @package BlogCentral
 * @subpackage content-quote.php
 */

// Reset variables
$media_wrap_width = $media_wrap_height = $blogcentral_media = $blogcentral_post_format_icon = '';
$media_flag = false;

// Extract variables for use in the layout-item-wrapper-begin.php template.
extract( $blogcentral_post_opts );

$blogcentral_extra_li_class .= ' quote';

// Add layout class to post quote if enabled
if ( isset( $blogcentral_layout_opts['quote_layout'] ) && '2' === $blogcentral_layout_opts['quote_layout'] ) {
	$blogcentral_extra_li_class .= ' quote-layout2';
}

// If layout3, then do not display media or post format icon.
if ( isset( $blogcentral_layout_opts['layout'] ) && ( 'layout3' !== $blogcentral_layout_opts['layout'] ) ) {
	/**
	 * If height is set for the media container, then add full-height class to stretch the 
	 * media to the whole container, else the media will have auto height.
	 */
	if ( isset( $blogcentral_layout_opts['media_wrap_height'] ) && $blogcentral_layout_opts['media_wrap_height'] ) {
		$blogcentral_extra_li_class .= ' full-height';
	} elseif ( isset( $blogcentral_layout_opts['img_size'] ) && 'scale' === $blogcentral_layout_opts['img_size'] ) {
		$blogcentral_extra_li_class .= ' auto-height';
	}
	
	if ( ! empty( $blogcentral_thumb ) ) {
		$blogcentral_media .= '<a href="' . esc_url( get_permalink() ) . '">' . $blogcentral_thumb . '</a>';
		$media_flag = true;
	}
	
	// Construct the $blogcentral_media variable. This variable is used in the layout-item-wrapper-begin template.
	if ( ! empty( $blogcentral_post_meta['media'] ) ) {
		$media_flag = true;
		
		if ( is_array( $blogcentral_post_meta['media'] ) && count( $blogcentral_post_meta['media'] ) > 0 ) {
			foreach( $blogcentral_post_meta['media'] as $media ) {
				$width = $height = '';
				
				if ( isset( $blogcentral_layout_opts['img_size'] ) ) {
					if ( 'default' === $blogcentral_layout_opts['img_size'] ) {
						$width = ! empty( $media['width'] ) ? ' width="' . esc_attr( $media['width'] ) . '"' : '';
						$height = ! empty( $media['height'] ) ? ' height="' . esc_attr( $media['height'] ) . '"' : '';
					}
				}
				
				$alt = ! empty( $media['alt_txt'] ) ? esc_attr( $media['alt_txt'] ) : '';
				$url = ! empty( $media['url'] ) ? esc_url( $media['url'] ) : '';
				
				$blogcentral_media .= '<a href="' . esc_url( get_permalink() ) . '"><img src="' . $url . '"';
				
				if ( $alt ) {
					$blogcentral_media .= ' alt="' . $alt  . '"';
				}
				if ( $width ) {
					$blogcentral_media .= ' ' . $width;
				}
				if ( $height ) {
					$blogcentral_media .= ' ' . $height;
				}
				
				$blogcentral_media .= ' /></a>';
			} // End foreach
		} // End if
	} // End if
	
	if ( $media_flag ) {
		$blogcentral_extra_li_class .= ' has-media';
	}
	
	// Construct the $blogcentral_post_format_icon variable. This variable is used in the postmeta-layout template.
	if ( ! empty( $blogcentral_layout_opts['display_format_icon'] ) && ! empty( $blogcentral_layout_opts['quote_icon'] ) ) {
		$blogcentral_post_format_icon = '<i class="post-format-icon fa ' . $blogcentral_layout_opts['quote_icon'] . '"></i>';
	}
}

/**
 * If quote layout is 2, then only output the content and post format icon, but have to preserve the original
 * theme options first, and only if this is not a shortcode or widget.
 */
if ( isset( $blogcentral_layout_opts['quote_layout'] ) && '2' === $blogcentral_layout_opts['quote_layout'] &&
	( ! isset( $blogcentral_query ) || ! $blogcentral_query ) ) {
	$temp = array();
	$temp['show_title'] = isset( $blogcentral_layout_opts['show_title'] ) ? $blogcentral_layout_opts['show_title'] : '';
	$temp['show_author'] = isset( $blogcentral_layout_opts['show_author'] ) ? $blogcentral_layout_opts['show_author'] : '';
	$temp['show_date'] = isset( $blogcentral_layout_opts['show_date'] ) ? $blogcentral_layout_opts['show_date'] : '';
	$temp['show_categories'] = isset( $blogcentral_layout_opts['show_categories'] ) ? $blogcentral_layout_opts['show_categories'] : '' ;
	$temp['show_tags'] = isset( $blogcentral_layout_opts['show_tags'] ) ? $blogcentral_layout_opts['show_tags'] : '';
	$temp['show_social'] = isset( $blogcentral_layout_opts['show_social'] ) ? $blogcentral_layout_opts['show_social'] : '';
	$temp['show_stats'] = isset( $blogcentral_layout_opts['show_stats'] ) ? $blogcentral_layout_opts['show_stats'] : '';
	$temp['show_content'] = isset( $blogcentral_layout_opts['show_content'] ) ? $blogcentral_layout_opts['show_content'] : '';
	
	$blogcentral_layout_opts['show_content'] = true;
	if ( ! is_single() ) {
		$blogcentral_layout_opts['show_title'] = $blogcentral_layout_opts['show_author'] =
			$blogcentral_layout_opts['show_date'] = $blogcentral_layout_opts['show_categories'] = $blogcentral_layout_opts['show_tags'] =
			$blogcentral_layout_opts['show_social'] = $blogcentral_layout_opts['show_stats'] = false;
	}
} // End if
		
/**
 * Template file that constructs the various html fragments for the layout-item-wrapper-begin template.
 */
include( locate_template( 'templates/postmeta-layout.php', false, false ) );

// Call the post_class function, and pass it $blogcentral_extra_li_class variable, after converting to array.
$class_array = explode( ' ', trim( $blogcentral_extra_li_class ) );
ob_start();
post_class( $class_array );
$blogcentral_extra_li_class = ob_get_clean();

/**
 * Template file for each post item.
 */
include( locate_template( 'templates/layout-item-wrapper-begin.php', false, false ) );

// Restore original theme options
if ( isset( $blogcentral_layout_opts['quote_layout'] ) && '2' === $blogcentral_layout_opts['quote_layout'] &&
	( ! isset( $blogcentral_query ) || ! $blogcentral_query ) ) {
	$blogcentral_layout_opts['show_title'] = $temp['show_title'];
	$blogcentral_layout_opts['show_author'] = $temp['show_author'];
	$blogcentral_layout_opts['show_date'] = $temp['show_date'];
	$blogcentral_layout_opts['show_categories'] = $temp['show_categories'];
	$blogcentral_layout_opts['show_tags'] = $temp['show_tags'];
	$blogcentral_layout_opts['show_social'] = $temp['show_social']; 
	$blogcentral_layout_opts['show_stats'] = $temp['show_stats'];
	$blogcentral_layout_opts['show_content'] = $temp['show_content'];
}