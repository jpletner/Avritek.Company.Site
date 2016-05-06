<?php 
/** 
 * Template to display facebook comments
 * 
 * Overrides the default template provided by the 4bzCore plugin.
 *
 * @since BlogCentral 1.0.0
 *
 * @param global variable $blogcentral_layout_opts, passed to this template from the blogcentral_facebook_comments function.
 *
 * @package BlogCentral
 * @subpackage facebook-comments.php
 */
	

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

global $wp_query;

if ( ! empty( $wp_query->post ) ) {
$id = $wp_query->post->ID;
$permalink = get_permalink( $id );
?>
<div class="fb-comments" data-href="<?php echo esc_html( $permalink ); ?>" data-numposts="<?php echo esc_attr( $blogcentral_layout_opts['limit'] ); ?>" data-colorscheme="light" data-width="100%"></div>
<?php }