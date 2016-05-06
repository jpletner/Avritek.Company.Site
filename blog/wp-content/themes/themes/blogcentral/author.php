<?php
/**
 * Template for author archive pages.
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage author.php
 */
	
// Display the header and call wp_head().
get_header();

// Get saved theme options.
$posts_opts = $blogcentral_opts['posts_landing'];
$posts_general = $blogcentral_opts['posts_general'];
$blogcentral_layout_opts = array_merge( (array)$posts_opts, (array)$posts_general );

// Do not display header or tagline text on archive pages.
$blogcentral_layout_opts['title_text'] = '';
$blogcentral_layout_opts['tagline_text'] = '';

// Displaying main posts, used to display correct heading tag for the post title.
$blogcentral_layout_opts['main_posts'] = true;

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

$page_header_title = __( 'About the Author', BLOGCENTRAL_TXT_DOMAIN );

// Output the precontent area.
blogcentral_output_precontent_frag( $blogcentral_opts, $page_header_title );
?>

<div id="main-content"<?php echo $main_class;?>>
	<div<?php echo $main_style; ?> id="main-area">
		<div class="gutter">
			<div class="main-content-header">
				<?php if ( ! isset( $blogcentral_opts["page_header"] ) || is_active_sidebar( 'precontent-widget' ) ) : ?>
				<header class="page-header">
					<h2 class="page-title"><?php echo $page_header_title; ?></h2>
				</header>
				<?php endif;
				if ( have_posts() ) { 
					// Get information for author description display
					the_post();
					
					// Rewind the posts 
					rewind_posts();
					
					// Display author bio and contact information, if provided.
					if ( get_the_author_meta( 'description' ) ) : 
						get_template_part( 'author-bio' ); 
					endif;
					?>
					
					<p class="results-title widget-title"><?php echo $wp_query->found_posts;
						printf( __( ' Posts By %s', BLOGCENTRAL_TXT_DOMAIN ), '<span><a href="' .
						esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' .
						esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' ); ?></p>
				<?php } ?>					
			</div>
			<div id="main-posts-cont">
			<?php 
				if ( have_posts() ) { 
					echo $blogcentral_wrapper[0]; 
			
					// Output the wrapping <ul> and tag with necessary attributes.
					echo $blogcentral_header;
				
					// Now display all posts by author.
					while ( have_posts() ) {
						// Reset variables
						$blogcentral_img_wrap_class = $blogcentral_media_wrap_style = $blogcentral_meta_wrap_class =
							$blogcentral_meta_wrap_style = $blogcentral_extra_li_class = $extra_li_attrs = $blogcentral_media = '';
					
						the_post();
					
						// Get post meta
						$post_id = $wp_query->post->ID;
						
						$blogcentral_post_meta = '';
						global $fourbzcore_plugin;
						
						if ( isset( $fourbzcore_plugin ) && method_exists( $fourbzcore_plugin, 'get_post_meta' ) ) {
							$blogcentral_post_meta = $fourbzcore_plugin->get_post_meta( $post_id );
						}
						
						$blogcentral_post_opts = array_merge( $blogcentral_layout_opts, array( $blogcentral_post_meta ) );
						
						// Html fragments for classes and styles to be added to each post item
						$blogcentral_post_opts = blogcentral_construct_inner_classes_styles( $blogcentral_post_opts );
						
						// Add class for sticky post	
						if ( is_sticky() && ! empty( $posts_general['sticky_display'] ) ) {
							$blogcentral_post_opts['blogcentral_extra_li_class'] .= ' blogcentral-sticky';
						}
							
						$format = get_post_format(); 
						if ( $format && 'status' !== $format && 'chat' !== $format && 'aside' !== $format ) {
							$format = '-' . $format;
						} else {
							$format = '';
						}
						
						// Get the thumbnail if has one.
						$blogcentral_thumb = '';
						if ( has_post_thumbnail() ) :
							$blogcentral_thumb = get_the_post_thumbnail( $post_id, 'full' );
						endif;
					
						/**
						 * Template for post type content.
						 */
						include( locate_template( 'content' . $format . '.php', false, false ) );
					} // End while
					
					// Output matching ending tags for the <div> wrapper and <ul> tags
					echo $blogcentral_ender . $blogcentral_wrapper[1]; 
					
					// Output page navigation
					if ( 1 < $wp_query->max_num_pages ) {
						blogcentral_traditional_posts_nav();
					} 
				} // End if have_posts().
				?>
			</div>
		</div>
	</div><!-- #main-area --><?php //no space here or will break layout ?><?php 
	if ( isset( $blogcentral_opts['show_sidebar'] ) ) {
		get_sidebar();  
	}
	
get_footer();