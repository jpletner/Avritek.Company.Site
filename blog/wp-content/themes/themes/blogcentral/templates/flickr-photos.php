<?php 
/** 
 * Template to display flickr photos
 * 
 * Overrides the default template provided by the 4bzCore plugin.
 *
 * @since BlogCentral 1.0.0
 *
 * @param global variable $blogcentral_layout_opts, passed to this template from the blogcentral_flickr_photos function.
 *
 * @package BlogCentral
 * @subpackage flickr-photos.php
 */
	
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

echo '<div class="flickr-photos"><script type="text/javascript" src="' . esc_url( 'http://www.flickr.com/badge_code_v2.gne?count=' . $blogcentral_layout_opts['limit'] . '&amp;display=latest&amp;size=s&amp;layout=x&amp;source=user&amp;user=' . $blogcentral_layout_opts['user_id'] ) . '"></script></div>';