<?php
/** 
 * Template to output an author bio
 *
 * Overrides the default template provided by the 4bzCore plugin.
 *
 * @since BlogCentral 1.1.0
 *
 * @package BlogCentral
 * @subpackage author-bio.php
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

echo '<div class="blogcentral-author-bio-cont">';
 
// Display author bio and contact information, if provided.
if ( get_the_author_meta( 'description' ) ) {
	get_template_part( 'author-bio' ); 
}

echo '</div>';
?>