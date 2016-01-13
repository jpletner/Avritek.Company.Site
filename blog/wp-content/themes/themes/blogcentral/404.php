<?php
/**
 * Template for 404 pages (Not Found).
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage 404.php
 */
 
global $blogcentral_opts;

// Display the header and call wp_head().
get_header(); 

// Dynamic style for main area.
$main_style = '';
$main_class = '';

if ( empty( $blogcentral_opts['show_sidebar'] ) ) {
	$main_style = ' style="width:100%;" '; 
	$main_class = ' class="no-sidebar"';
} else {
	$main_class = ' class="show-sidebar"';
}

if ( isset( $blogcentral_opts['show_color_chooser'] ) ) {
	// Display the dynamic color chooser.
	blogcentral_output_color_chooser();
}

// Output the precontent area.
blogcentral_output_precontent_frag( $blogcentral_opts, __( 'Not Found', BLOGCENTRAL_TXT_DOMAIN ) ); 
?>
		
<div id="main-content"<?php echo $main_class;?>>
	<div<?php echo $main_style; ?> id="main-area">
		<div class="gutter">
			<?php 
			// Display the page header in the main area if not displaying in the precontent area.
			if ( ! isset( $blogcentral_opts["page_header"] ) || is_active_sidebar( 'precontent-widget' ) ) :
			?>
				<header class="page-header">
					<h1 class="page-title"><?php _e( 'Not Found', BLOGCENTRAL_TXT_DOMAIN ); ?></h1>
				</header>
			<?php endif; ?>
			<div class="page-content">
				<p><?php _e( 'Your request was not successful. Maybe a search will help?', BLOGCENTRAL_TXT_DOMAIN ); ?></p>
				<?php get_search_form(); ?>
			</div><!-- End .page-content -->
		</div><!-- End .gutter -->
	</div><!-- End #main-area --><?php //no space here or will break layout ?><?php 
	
	if ( isset( $blogcentral_opts['show_sidebar'] ) ) {
		get_sidebar();  
	}
	
get_footer();