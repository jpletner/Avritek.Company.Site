<?php 
/* 
 * Template to display a flexslider slideshow. 
 * 
 * Overrides the default template provided by the 4bzCore plugin.
 *
 * @since BlogCentral 1.0.0
 *
 * @param array global variable $blogcentral_layout_opts, passed to this template from the blogcentral_slideshow function.
 * @param array global variable $blogcentral_items, holds the slides. Passed to this template from the blogcentral_slideshow function.
 *
 * @package BlogCentral
 * @subpackage slideshow.php
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

$blogcentral_items_count = count( $blogcentral_items );

if ( is_array( $blogcentral_items ) && 0 < $blogcentral_items_count ) {
	echo '<ul class="slides">'; 
	
	for ( $i = 0; $i < $blogcentral_items_count; ++$i ) {
		
		// Don't escape because slides will contain html markup.
		echo "<li>" . $blogcentral_items[$i] . "</li>";
	} 
	
	echo '</ul>';
}