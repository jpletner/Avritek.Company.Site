<?php
/** 
 * Template to output an image and text
 * 
 * Overrides the default template provided by the 4bzCore plugin.
 *
 * @since BlogCentral 1.1.0
 *
 * @param global variable $blogcentral_layout_opts.
 *
 * @package BlogCentral
 * @subpackage image-text.php
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

global $fourbzcore_plugin;

$extra_class = '';
			
if ( 'img-float' === $blogcentral_layout_opts['layout'] ) {
	$extra_class = ' img-float';
} elseif( 'img-overlay' === $blogcentral_layout_opts['layout'] ) {
	$extra_class = ' img-overlay';
} else {
	$extra_class = ' img-top';
}

echo '<div class="blogcentral-image-text-cont">';
  
$img = '';
$width = isset( $blogcentral_layout_opts['image_url_width'] ) ?
	' width="' . esc_attr( $blogcentral_layout_opts['image_url_width'] ) . '"' :
	'';
	
$height = isset( $blogcentral_layout_opts['image_url_height'] ) ?
	' height="' . esc_attr( $blogcentral_layout_opts['image_url_height'] ) . '"' :
	'';
		
$alt = ( isset( $blogcentral_layout_opts['image_url_alt'] ) && $blogcentral_layout_opts['image_url_alt'] ) ?
	esc_attr( $blogcentral_layout_opts['image_url_alt'] ) :
	'';
	
$url = isset( $blogcentral_layout_opts['image_url'] ) ? esc_url( $blogcentral_layout_opts['image_url'] ) : '';
		
$img .=  '<img src="' . $url . '"';

if ( $alt ) {
	$img .= ' alt="' . $alt  . '"';
}

if ( $width ) {
	$img .= ' ' . $width;
}

if ( $height ) {
	$img .= ' ' . $height;
}

$img .= ' />';

echo '<figure>' . $img . '<p class="content post-content text-format">' . $blogcentral_layout_opts['content'] . '</p></figure>';

echo '</div>';
?>