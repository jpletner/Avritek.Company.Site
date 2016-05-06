<?php
/**
 * Template for single post pages
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage single.php
 */
 
// Display the header and call wp_head().	 
get_header(); 

/**
 * If option is set to override the post landing page options, then use options set for 
 * single post page.
 */
if ( isset( $blogcentral_opts['posts_single']['override_landing'] ) ) {
	$posts_opts = $blogcentral_opts['posts_single'];
} else {
	$posts_opts = $blogcentral_opts['posts_landing'];
}
	
// Is a single post, so cols will not be more than 1.
$posts_opts['cols'] = '1';

// If using post landing page options, do not use its title or tagline for single post pages.
$posts_opts['title_text'] = '';
$posts_opts['tagline_text'] = '';

// Get saved theme options.
$posts_general = isset( $blogcentral_opts['posts_general'] ) ? $blogcentral_opts['posts_general'] : array();
$blogcentral_layout_opts = array_merge( (array)$posts_opts, (array)$posts_general );

// Call function to construct the common <div> wrapper.
$blogcentral_wrapper = blogcentral_common_template_wrapper( $blogcentral_layout_opts );

/**
 * Template that constructs and outputs the beginning <ul> tag for the post items list with all necessary 
 * attributes in the $blogcentral_header variable.
 */
include( locate_template( '/templates/layout-wrapper-begin.php', false, false ) );

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

if ( have_posts() ) {
	the_post();
	
	// Rewind the posts 
	rewind_posts();
	
	$page_header_title = get_the_title();
} else {
	$page_header_title = __( 'Single Blog Page', BLOGCENTRAL_TXT_DOMAIN );
}

// Output the precontent area.
blogcentral_output_precontent_frag( $blogcentral_opts, $page_header_title );
?>
<div id="main-content"<?php echo $main_class;?>>
	<div<?php echo $main_style; ?> id="main-area">
		<div class="gutter">
			<?php if ( ! isset( $blogcentral_opts["page_header"] ) ) : ?>
				<header class="page-header">
					<h1 class="page-title"><?php echo $page_header_title; ?></h1>
				</header>
			<?php endif; ?>
		<?php 
			if( have_posts() ) {
				// Reset variables
				$blogcentral_img_wrap_class = $blogcentral_media_wrap_style = $blogcentral_meta_wrap_class =
					$blogcentral_meta_wrap_style = $blogcentral_extra_li_class = $extra_li_attrs = $blogcentral_media = '';
				
				echo '<div id="main-posts-cont">';
				// Output the wrapping <div> and beginning <ul> tags, with necessary attributes.
				echo $blogcentral_wrapper[0]; 
				echo $blogcentral_header;
				
				the_post();
				
				// Get post meta
				$post_id = $wp_query->post->ID;
				
				$blogcentral_post_meta = '';
				global $fourbzcore_plugin;
				
				if ( isset( $fourbzcore_plugin ) && method_exists( $fourbzcore_plugin, 'get_post_meta' ) ) {
					$blogcentral_post_meta = $fourbzcore_plugin->get_post_meta( $post_id );
				}
				
				$blogcentral_post_opts = array_merge( $blogcentral_layout_opts, (array)$blogcentral_post_meta );
				
				// Html fragments for classes and styles to be added to each post item.
				$blogcentral_post_opts = blogcentral_construct_inner_classes_styles( $blogcentral_post_opts );
				
				$format = get_post_format(); 
				if ( $format && 'status' !== $format && 'chat' !== $format && 'aside' !== $format ) {
					$format = '-' . $format;
				} else {
					$format = '';
				}
				
				// Do not show the title because it is shown in the page header or at the top of the main posts area.
				$blogcentral_layout_opts['show_title'] = false;
				
				// Is a single post, so show the content
				$blogcentral_layout_opts['show_content'] = true;
				
				// Get the default featured image if has one.
				$blogcentral_thumb = '';
				
				if ( has_post_thumbnail() ) :
					$blogcentral_thumb = get_the_post_thumbnail( $post_id, 'full' );
				endif;
					
				/**
				 * Template for post format content.
				 */
				include( locate_template( 'content' . $format . '.php', false, false ) );
				
				// Output matching ending tags for the <div> wrapper and <ul> tags
				echo $blogcentral_ender . $blogcentral_wrapper[1];
				?>
				</div><!-- End #main-posts-cont -->
				<?php
				
				// Display author bio and contact information, if provided.
				if ( get_the_author_meta( 'description' ) ) {
					get_template_part( 'author-bio' ); 
				}
				
				if ( is_active_sidebar( 'single-post-after-widget' ) ) {
					echo '<div id="single-post-after-widget">';
					dynamic_sidebar( 'single-post-after-widget' );
					echo '</div>';
				}// End if dynamic sidebar.
				
				// Display comments.
				comments_template( '', true );
				
				// Output posts navigation.
				blogcentral_post_nav();
			} //End if have_posts()
			?>
				
		</div><!-- End .gutter -->
	</div><!-- End #main-area --><?php //no space here or will break layout ?><?php 
	
	if ( isset( $blogcentral_opts['show_sidebar'] ) ) {
		get_sidebar();  
	}
	
get_footer();