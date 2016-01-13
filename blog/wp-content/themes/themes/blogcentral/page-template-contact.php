<?php
/**
 * Template name: Contact
 *
 * Use this template to create a contact page. It is recommended that you create a backup copy of this file
 * before modifying, and modify very carefully, as you might delete important layout data.
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage page-template-contact.php
 */
 
// Display the header and call wp_head().
get_header();
	
// Dynamic style for main area.
$main_style = '';
$main_class = '';

if ( ! isset( $blogcentral_opts['show_sidebar'] ) ) {
	$main_style = ' style="width:100%;" '; 
	$main_class = ' class="contact-page no-sidebar"';
} else {
	$main_class = ' class="contact-page show-sidebar"';
}

if ( isset( $blogcentral_opts['show_color_chooser'] ) ) {
	// Display the dynamic color chooser.
	blogcentral_output_color_chooser();
}

// Output precontent area
blogcentral_output_precontent_frag( $blogcentral_opts, __( 'Contact', BLOGCENTRAL_TXT_DOMAIN ) );
?>		
<div id="main-content"<?php echo $main_class;?> >
	<div<?php echo $main_style; ?> id="main-area">
		<div class="gutter">
		<?php	
			while ( have_posts() ) {
				the_post(); 
				get_template_part( 'content-page' );
			} // End while
		?>
		</div><!-- End .gutter -->
	</div><!-- End #main-area --><?php //no space here or will break layout ?><?php 
	if ( isset( $blogcentral_opts['show_sidebar'] ) ) {
		get_sidebar();  
	}
get_footer();