<?php	
/**
 * Template for displaying content and meta information of a post
 *
 * Displays a title, author, date, categories, etc. Is used by all content files. 
 * Checks theme options to display this information.
 *
 * For use in the wp loop.
 *
 * @since BlogCentral 1.0.0
 *
 * @param array global variable $blogcentral_layout_opts, passed to this template by the various content templates.
 *
 * @package BlogCentral
 * @subpackage postmeta-layout.php
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}	

$single = is_single();
$post_title = get_the_title();

/**
 * Depending on the layout, different information might go in different containers.
 *
 * Layout1: media on top, post meta on bottom. 
 * Layout2: media floated to left of post meta in a two column design.
 * Layout3: simple list - no media will be displayed.
 */

// Initialize variables.
$author_frag = $date_frag = $content_frag = $stats_frag = $cats_frag = $tags_frag = $share_frag = $meta_layout_frag = '';

// Construct the author html fragment if option to show author is set.
if ( ! empty( $blogcentral_layout_opts['show_author'] ) ) {
	$author_icon = '';
	
	if ( ! empty( $blogcentral_layout_opts['show_icons'] ) && ! empty( $blogcentral_layout_opts['author_icon'] ) ) {
		$author_icon = '<i class="fa fa-meta ' . esc_attr( $blogcentral_layout_opts['author_icon'] ) . '"></i>';
	}
	
	$author_frag = '<li>' . $author_icon . '<span class="author"><a class="author-url" href="' . 
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . 
					sprintf( esc_attr__( 'View all posts by %s', BLOGCENTRAL_TXT_DOMAIN ), get_the_author() ) . '" rel="author">' . esc_html( get_the_author() ) . '</a></span></li>';
}

// Construct the date html fragment if option to show date is set.
if ( ! empty( $blogcentral_layout_opts['show_date'] ) ) {
	$date_icon = '';
	
	if ( ! empty( $blogcentral_layout_opts['show_icons'] ) && ! empty( $blogcentral_layout_opts['date_icon'] ) ) {
		$date_icon = '<i class="fa fa-meta ' . esc_attr( $blogcentral_layout_opts['date_icon'] ) . '"></i>';
	}
	
	$date_format =  esc_html( get_the_date() );
	
	$date_frag = '<li>' . $date_icon . '<a href="' . esc_url( get_permalink() ) . '" title="' . 
		esc_attr( get_the_time() ) . '" rel="bookmark">' . $date_format . '</a></li>';
}

// Construct the post content html fragment if option to show content is set.
if ( ! empty( $blogcentral_layout_opts['show_content'] ) ) {
	global $more;
	
	ob_start();
	
	if ( $single ) {
		$more = 1;
	} else {
		$more = 0;
	}
	
	$format = get_post_format();
	
	// Get the excerpt or content.
	if ( ! $single && has_excerpt() || 'quote' === $format ) {
		the_excerpt();
	} else {
		the_content();
	}
			
	if ( $single ) {
		wp_link_pages(array(
				'before'      => '<div class="post-page-links"><span class="post-page-links-title">' . __( 'Pages:', BLOGCENTRAL_TXT_DOMAIN ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-txt">' . __( 'Page', BLOGCENTRAL_TXT_DOMAIN ) . ' </span>%',
				'separator'   => '<span class="screen-reader-txt">, </span>',
			));
	}
	
	$content_frag = '<div class="content post-content">' . ob_get_clean() . '</div>';
}

// Construct the comments fragment if option to show stats is set.
if ( ! empty( $blogcentral_layout_opts['show_stats'] ) ) {
	$comments_icon = '';
	
	if ( ! empty( $blogcentral_layout_opts['show_icons'] ) && ! empty( $blogcentral_layout_opts['comments_icon'] ) ) {
		$comments_icon = '<i class="fa ' . esc_attr( $blogcentral_layout_opts['comments_icon'] ) . '"></i>';
	}
	
	ob_start();
	
	comments_popup_link( '<span class="leave-reply-none">' . _x( '0', 'comments number', BLOGCENTRAL_TXT_DOMAIN ) . '</span>',
		_x( '1', 'comments number', BLOGCENTRAL_TXT_DOMAIN ), _x( '%', 'comments number', BLOGCENTRAL_TXT_DOMAIN ),
		'comments-link' );			
	
	$stats_frag = '<li>' .  $comments_icon . '<span class="post-stats">' .
		__( 'Comments: ', BLOGCENTRAL_TXT_DOMAIN ) . ob_get_clean() . '</span></li>';
}

// Construct the categories html fragment if option to show categories is set
if ( ! empty( $blogcentral_layout_opts['show_categories'] ) && ( $categories_list = get_the_category_list( ', ' ) ) ) {
	$cat_icon = '';
	
	if ( ! empty( $blogcentral_layout_opts['show_icons'] ) && ! empty( $blogcentral_layout_opts['categories_icon'] ) ) {
		$cat_icon = '<i class="fa fa-meta ' . esc_attr( $blogcentral_layout_opts['categories_icon'] ) . '"></i>';
	}
	
	$cats_frag = '<li>' . $cat_icon . '<span class="cats-list meta-list">'  .
		__( 'Categories', BLOGCENTRAL_TXT_DOMAIN ) . ': ' . $categories_list . '</span></li>';
}

// Construct the tags html fragment if option to show tags is set
if ( ! empty( $blogcentral_layout_opts['show_tags'] ) && ( $tags_list = get_the_tag_list( '', ', ' ) ) ) {
	$tags_icon = '';
	
	if ( ! empty( $blogcentral_layout_opts['show_icons'] ) && ! empty( $blogcentral_layout_opts['tags_icon'] ) ) {
		$tags_icon = '<i class="fa fa-meta ' . esc_attr( $blogcentral_layout_opts['tags_icon'] ) . '"></i>';
	}
	
	$tags_frag = '<li>' . $tags_icon . '<span class="cats-list meta-list">' .
		__( 'Tags', BLOGCENTRAL_TXT_DOMAIN ) . ': ' . $tags_list . '</span></li>';
}

// Construct the social share html fragment if option to show share is set
if ( ! empty( $blogcentral_layout_opts['show_social'] ) ) {
	$share_title = '';
	
	if ( ! empty( $blogcentral_layout_opts['show_icons'] ) && ! empty( $blogcentral_layout_opts['share_title'] ) ) {
		$share_title = '<span class="share-title">' . esc_html( $blogcentral_layout_opts['share_title'] ) . '</span>';
	}
	
	$share_frag = '<div class="share-us">' . $share_title . ' <a href="http://www.facebook.com/share.php?u=' .
		urlencode( get_permalink() ) . '"><i class="fa fa-facebook"></i></a><a href="http://twitter.com/home?status=' . urlencode( get_permalink() ) . '"><i class="fa fa-twitter"></i></a><a href="https://plus.google.com/share?url=' . urlencode( get_permalink() ) . '"><i class="fa fa-google-plus"></i></a><a href="https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode( get_permalink() ) . '&title=' . urlencode( $post_title ) . '"><i class="fa fa-linkedin"></i></a></div>';
}

if ( ! isset( $blogcentral_query ) ) {
	$title_pre = '<h2 class="post-title">';
	$title_end = '</h2>';
} else {
	$title_pre = '<span class="post-title">';
	$title_end = '</span>';
}

$meta_layout_frag = $blogcentral_post_format_icon;

if ( ! empty( $blogcentral_layout_opts['show_title'] ) ) {
	$meta_layout_frag .= $title_pre . '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' .
		$post_title . "</a>$title_end";
}

/*
 * Assemble the various html fragments constructed above according the options chosen, making sure not to output 
 * empty tags.
 */
$extra = $blogcentral_meta_content = '';
	
if ( $author_frag || $date_frag || $cats_frag || $tags_frag || $stats_frag ) {
	$blogcentral_meta_content .= '<ul class="post-meta">' . $author_frag;
	$blogcentral_meta_content .= $date_frag . $cats_frag . $tags_frag . $stats_frag ;
	$blogcentral_meta_content .= '</ul>';
}

if ( $content_frag || $blogcentral_meta_content || $share_frag ) {
	if ( ! empty( $blogcentral_layout_opts['post_meta_btm'] ) ) {
		$blogcentral_meta_content = $content_frag . $blogcentral_meta_content . $share_frag;
	} else {
		$blogcentral_meta_content .= $content_frag . $share_frag;
	}
}

if ( $meta_layout_frag ) {
	$blogcentral_meta_content = '<div>' . $meta_layout_frag . $blogcentral_meta_content . '</div>';
}
