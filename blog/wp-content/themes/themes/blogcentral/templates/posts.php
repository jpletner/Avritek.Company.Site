<?php 
/** 
 * Template to display posts. 
 * 
 * Overrides the default template provided by the 4bzCore plugin.
 *
 * @since BlogCentral 1.0.0
 *
 * @param global variable $blogcentral_header, passed to this template by the layout-wrapper-begin template file.
 * @param global variable $blogcentral_ender, passed to this template by the layout-wrapper-begin template file.
 * @param global variable $blogcentral_layout_opts, passed to this template by the various post-based shortcodes filter functions.
 * @param global variable $blogcentral_query, the posts object. Passed to this template by the various post-based shortcodes filter functions.
 */
	
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

// Output the wrapping <ul> tag, with necessary attributes.
include( locate_template( 'templates/layout-wrapper-begin.php', false, false ) );

if ( isset( $blogcentral_header ) ) {
	echo $blogcentral_header;
}
	
if ( ! empty( $blogcentral_query ) ) {
	if ( $blogcentral_query->have_posts() ) {
		// The loop
		while ( $blogcentral_query->have_posts() ) : 
			// Reset variables
			$blogcentral_img_wrap_class = $blogcentral_media_wrap_style = $blogcentral_meta_wrap_class =
				$blogcentral_meta_wrap_style = $blogcentral_extra_li_class = $extra_li_attrs = $blogcentral_media = $blogcentral_post_opts = '';
			
			$blogcentral_query->the_post();
			
			$post_id = $blogcentral_query->post->ID;
			
			$blogcentral_post_meta = '';
			global $fourbzcore_plugin;
			
			if ( isset( $fourbzcore_plugin ) && method_exists( $fourbzcore_plugin, 'get_post_meta' ) ) {
				$blogcentral_post_meta = $fourbzcore_plugin->get_post_meta( $post_id );
			}
			
			$blogcentral_post_opts = array_merge( $blogcentral_layout_opts, (array)$blogcentral_post_meta );
			
			// Html fragments for classes and styles to be added to each post item
			$blogcentral_post_opts = blogcentral_construct_inner_classes_styles( $blogcentral_post_opts );
			
			$format = get_post_format(); 
			if ( $format && 'status' !== $format && 'chat' !== $format && 'aside' !== $format ) {
				$format = '-' . $format;
			} else {
				$format = '';
			}
			
			// Get the default featured image if has one.
			$blogcentral_thumb = '';
			
			if ( has_post_thumbnail() ) {
				$blogcentral_thumb = get_the_post_thumbnail( $post_id, 'full' );
			}
			
			include( locate_template( 'content' . $format . '.php', false, false ) );
		endwhile;
		
		// Restore original Post Data 
		wp_reset_postdata();
	}	
}

// Matching ending tags for the <ul> tag
if ( isset( $blogcentral_ender ) ) {
	echo $blogcentral_ender;
}