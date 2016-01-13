<?php
/**
 * Default template for pages
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage page.php
 */

// Display the header and call wp_head().
get_header(); 

// Get saved theme options.
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

if ( have_posts() ) { 
	// Get information for page title
	the_post();
	
	// Rewind the posts 
	rewind_posts();
	
	$page_header_title = '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . get_the_title() . "</a>";
} else {
	$page_header_title = __( 'Page', BLOGCENTRAL_TXT_DOMAIN );
}

// Output the precontent area.	
blogcentral_output_precontent_frag( $blogcentral_opts, $page_header_title );
?>	
<div id="main-content"<?php echo $main_class;?>>
	<div<?php echo $main_style; ?> id="main-area">
		<div class="gutter">
			<?php if ( ! isset( $blogcentral_opts["page_header"] ) || is_active_sidebar( 'precontent-widget' ) ) : ?>
			<header class="page-header">
				<h1 class="page-title"><?php echo $page_header_title; ?></h1>
			</header>
			<?php endif; ?>
			<div id="main-posts-cont">
		<?php	
			while ( have_posts() ) {
				the_post(); 
				get_template_part( 'content-page' );
				wp_link_pages();
				
				// Display comments.
				comments_template();
			} // End while
		?>
			</div><!-- End #main-posts-cont -->
		</div><!-- End .gutter -->
	</div><!-- End #main-area --><?php //no space here or will break layout ?><?php 
	if ( isset( $blogcentral_opts['show_sidebar'] ) ) {
		get_sidebar();  
	}

get_footer();