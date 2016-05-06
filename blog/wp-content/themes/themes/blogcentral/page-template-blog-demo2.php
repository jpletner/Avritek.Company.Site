<?php
/**
 * Template name: Blog Demo 2
 *
 * Demo 2 of the blog. Only for demo purposes, can safely be deleted.
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage page-template-blog-demo2.php
 */

// Display the header and call wp_head().
get_header();

// Retrieve the posts.
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$posts = new WP_Query( array(
	'posts_per_page'	=>	10,
	'orderby'			=>	'post_date',
	'order'				=>	'DESC',
	'paged'				=>	$paged,
)
);

// Get saved theme options.
$posts_opts = $blogcentral_opts['posts_landing'];
$posts_general = $blogcentral_opts['posts_general'];
$blogcentral_layout_opts = array_merge( (array)$posts_opts, (array)$posts_general );

// Displaying main posts, used to display correct heading tag for the post title.
$blogcentral_layout_opts['main_posts'] = true;

// Call function to construct the common div wrapper.
$blogcentral_wrapper = blogcentral_common_template_wrapper( $blogcentral_layout_opts );

/**
 * Template that constructs and outputs the beginning <ul> tag for the post items list with all necessary 
 * attributes in the $blogcentral_header variable.
 */
include( locate_template('/templates/layout-wrapper-begin.php', false, false ) );

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

$page_header_title = __( 'Demo 2', BLOGCENTRAL_TXT_DOMAIN );
	
if ( isset( $blogcentral_opts['blog_info'] ) ) {
	$page_header_title .= '<span class="blog-info">' . get_bloginfo( 'description' ) . '</span>';
}

// Output precontent area.
blogcentral_output_precontent_frag( $blogcentral_opts, $page_header_title ); 
?>
			
<div id="main-content"<?php echo $main_class; ?>>
	<div<?php echo $main_style; ?> id="main-area">
		<div class="gutter">
		<?php if ( ! isset( $blogcentral_opts["page_header"] ) ) : ?>
			<header class="page-header">
				<h1 class="page-title"><?php echo $page_header_title; ?></h1>
			</header>
		<?php endif; ?>
		<?php 
			// Preposts widget area. If no active widgets in this area, then output no code.
			if ( is_active_sidebar( 'preposts-widget-left' ) || is_active_sidebar( 'preposts-widget-right' ) ) : ?>
			<div id="preposts-widget">
				<?php if ( is_active_sidebar( 'preposts-widget-left' ) ) : ?>
				<div id="preposts-left">
					<?php dynamic_sidebar( 'preposts-widget-left' ); ?>
				</div><!-- End #preposts-left --><?php endif; ?><?php //no space here or will break layout ?><?php
				if ( is_active_sidebar( 'preposts-widget-right' ) ) : ?><?php //no space here or will break layout ?><div id="preposts-right">
					<?php dynamic_sidebar( 'preposts-widget-right' ); ?>
				</div><!-- End #preposts-right --><?php endif; ?>
			</div>
			<?php endif; ?>
			<div id="main-posts-cont">
			<?php 
			if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( 'main-widget' ) ) {
				// Output the wrapping <div> and beginning <ul> tags, with necessary attributes.
				echo $blogcentral_wrapper[0]; 
				echo $blogcentral_header;
				
				while ( $posts->have_posts() ) { 
					// Reset variables
					$blogcentral_img_wrap_class = $blogcentral_media_wrap_style = $blogcentral_meta_wrap_class =
						$blogcentral_meta_wrap_style = $blogcentral_extra_li_class = $extra_li_attrs = $blogcentral_media = '';
					
					$posts->the_post(); 
					
					// Get post meta
					$post_id = $posts->post->ID;
					
					$blogcentral_post_meta = '';
					global $fourbzcore_plugin;
					
					if ( isset( $fourbzcore_plugin ) && method_exists( $fourbzcore_plugin, 'get_post_meta' ) ) {
						$blogcentral_post_meta = $fourbzcore_plugin->get_post_meta( $post_id );
					}
					
					$blogcentral_post_opts = array_merge( $blogcentral_layout_opts, (array)$blogcentral_post_meta );
					
					// Html fragments for classes and styles to be added to each post
					$blogcentral_post_opts = blogcentral_construct_inner_classes_styles( $blogcentral_post_opts );
					
					// Add class for sticky post
					if ( is_sticky() && ! empty( $posts_general['sticky_display'] ) && 1 >= $paged ) {
						$blogcentral_post_opts['blogcentral_extra_li_class'] .= ' blogcentral-sticky';
					}
					
					$format = get_post_format(); 
					if ( $format && 'status' !== $format && 'chat' !== $format && 'aside' !== $format ) {
						$format = '-' . $format;
					} else {
						$format = '';
					}
					
					// Get the default featured image if has one.
					$blogcentral_thumb = '';
					
					if ( has_post_thumbnail() ) :
						$blogcentral_thumb = get_the_post_thumbnail( $post_id, 'full' );
					endif;
					
					/**
					 * Template for post format content.
					 */
					include( locate_template( 'content' . $format . '.php', false, false ) );
				} // End while

				 // Matching ending tags for the <div> wrapper and <ul> tags
				 echo $blogcentral_ender . $blogcentral_wrapper[1];
				
				// Output page navigation
				if ( $posts->max_num_pages > 1 ) {
					blogcentral_traditional_posts_nav( $posts );
				}
			} // End if
			?>
			</div><!-- End #main-posts-cont -->
		</div><!-- End .gutter -->
	</div><!-- End #main-area --><?php //no space here or will break layout ?><?php 
	if( isset( $blogcentral_opts['show_sidebar'] ) ) {
		get_sidebar();  
	}
		
	/**
	 * Post Posts widget area. Displays underneath the main area and the sidebar. If no active widgets in this
	 * area then display nothing here.
	 */
	if ( is_active_sidebar( 'post-posts-widget' ) ) {
		echo '<div id="post-posts-widget">';
		dynamic_sidebar( 'post-posts-widget' );
		echo '</div>';
	}
	
get_footer();