<?php
/**
 * BlogCentral functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used as custom template tags, and helpers for shortcodes and widgets. Others are 
 * attached to action and filter hooks.
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage functions.php
 ------------------------------------------------------------------------
	Table of Contents
	
	1.0 Define Constant + Global Variables
	2.0 Theme Setup
	3.0 Actions + Filters
		3.1 Front End Only
		3.2 Admin Side Only
	4.0 Setup + Utility Functions
		- Default, initial, and demos options
		- Register Navigation Menu
		- Register Widgets Areas
	5.0 Admin Side Functions
		5.1 Enqueue Scripts & Style
		5.2 Add Theme Options Page + Help Tabs 
		5.3 Controller + Save Theme Settings 
		5.4 Theme Options
			5.4.1 Main
			5.4.2 Font Options
			5.4.3 General Component Options
			5.4.4 Posts Options
			5.4.5 Shortcodes + Widgets Options: Not Post Based
		5.5 Admin Ajax
	6.0 Front End Functions
		6.1 Enqueue Scripts + Inline Scripts
		6.2 Fonts
		6.3 Switch Demo
		6.4 Body Class
		6.5 Page Header/Precontent Area
		6.6 Post Content
		6.7 Posts Navigation
		6.8 Comments
		6.9 Components
-------------------------------------------------------------------------*/
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * 1.0 Define Constant + Global Variables
 *-----------------------------------------------------------------------*/
// Constant variables used throughout the theme.
define( 'BLOGCENTRAL_THEME_PREFIX', 'blogcentral' );
define( 'BLOGCENTRAL_TXT_DOMAIN', 'blogcentral' );
define( 'BLOGCENTRAL_DB_OPTIONS_NAME', 'blogcentral_options' );
define( 'BLOGCENTRAL_THEME_NAME', 'BlogCentral' );
define( 'BLOGCENTRAL_THEME_URL', get_template_directory_uri() );
define( 'BLOGCENTRAL_THEME_DOCS', 'http://4bzthemes.com/knowledgebase/' );
define( 'BLOGCENTRAL_SITE_URL', esc_url( trailingslashit( get_option( 'siteurl' ) ) ) );
define( 'BLOGCENTRAL_PAGE_SLUG', BLOGCENTRAL_THEME_PREFIX . '_options' );

$blogcentral_defaults = blogcentral_get_option_defaults();
$blogcentral_initial = blogcentral_get_option_initial();

$blogcentral_opts = blogcentral_initialize_global_opts();

// Demos options.
$blogcentral_blog_demos_opts = blogcentral_get_demos_opts();

if ( isset( $fourbzcore_plugin ) ) {
	// Include filter functions for the 4bzCore plugin.
	require get_template_directory() . '/includes/4bzcore.php';
}

/**
 * 2.0 Theme Setup
 *-----------------------------------------------------------------------*/
 
// Sets up the content width value.
if ( ! isset( $content_width ) ) {
	$content_width = 604;
}

if ( ! function_exists( '_wp_render_title_tag' ) && ! function_exists( 'blogcentral_render_title' ) ) {
	function blogcentral_render_title() {
?>
<title><?php wp_title( '|', true, 'right' ); ?></title>
<?php
	}
	add_action( 'wp_head', 'blogcentral_render_title' );
}

/**
 * 3.0 Actions + Filters
 *-----------------------------------------------------------------------*/
// This theme uses its own gallery styles.
add_filter( 'use_default_gallery_style', '__return_false' );

// Register widgets areas.
add_action( 'widgets_init', 'blogcentral_register_widgets_areas' );

// Theme setup.
add_action( 'after_setup_theme', 'blogcentral_theme_setup' );
add_action( 'parse_query', 'blogcentral_initialize_global_opts' );

if ( ! is_admin() ) {
	/**
	 * 3.1 Front End Only
	 *-----------------------------------------------------------------------*/
	
	// Add action to switch demo.
	// Can safely be deleted if not using the predefined pages to demonstrate the demos.
	add_action( 'wp_head', 'blogcentral_choose_demo', 0 );
	
	// Add theme's classes to the body.
	add_filter( 'body_class', 'blogcentral_body_class' );
	
	// Enqueue scripts.
	add_action( 'wp_enqueue_scripts', 'blogcentral_enqueue_scripts' );
	
	// Use the post excerpt if available.
	add_filter( 'the_excerpt', 'blogcentral_custom_excerpt_more' );
	add_filter( 'the_content_more_link', 'blogcentral_auto_excerpt_more' );
	
	/*
	 * add filters to override the display of the 4bzCore plugin's shortcodes and widgets, if the plugin
	 * is installed and active.
	 */
	if ( isset( $fourbzcore_plugin ) ) {
		add_filter( '4bzcore_widget_title', 'blogcentral_widget_title', 10, 4 );
		add_filter( '4bzcore_recent_posts', 'blogcentral_recent_posts', 10, 3 );
		add_filter( '4bzcore_related_posts', 'blogcentral_related_posts', 10, 3 );
		add_filter( '4bzcore_featured_posts', 'blogcentral_featured_posts', 10, 3 );
		add_filter( '4bzcore_popular_posts', 'blogcentral_popular_posts', 10, 3 );
		add_filter( '4bzcore_progressbars', 'blogcentral_progressbars', 10, 3 );
		add_filter( '4bzcore_contact_info', 'blogcentral_contact_info', 10, 3 );
		add_filter( '4bzcore_contact_form', 'blogcentral_contact_form', 10, 3 );
		add_filter( '4bzcore_slideshow', 'blogcentral_slideshow', 10, 3 );
		add_filter( '4bzcore_facebook_comments', 'blogcentral_facebook_comments', 10, 3 );
		add_filter( '4bzcore_flickr_photos', 'blogcentral_flickr_photos', 10, 3 );
		add_filter( '4bzcore_image_text', 'blogcentral_image_text', 10, 3 );
		add_filter( '4bzcore_author_bio', 'blogcentral_author_bio', 10, 3 );
	}

} else {
	/**
	 * 3.2 Admin Side Only
	 *-----------------------------------------------------------------------*/
	
	// Enqueue scripts needed on admin side.
	add_action( 'admin_enqueue_scripts', 'blogcentral_admin_scripts' );

	// Add theme's options page to the admin menu.
	add_action( 'admin_menu', 'blogcentral_theme_options_add_page' ); 
	
	// Register theme options with the settings api.
	add_action( 'admin_init', 'blogcentral_register_theme_options' );
	
	// Ouput any admin notices.
	add_action( 'admin_notices', 'blogcentral_admin_notices' );
	
	// Route the action in the post variable set on the theme's options page.
	if ( isset( $_GET['page'] ) ) {
		if ( 'blogcentral_options' === $_GET['page'] ) {
			if ( isset( $_POST['blogcentral_change_blog_demo'] ) ) {
				add_action( 'admin_init', 'blogcentral_change_blog_demo' );
			}
		}
	}

	/*
	 * Ajax actions, specifically actions to retrieve options when a font a selected and to retrieve a template 
	 * for a slide.
	 */
	add_action( "wp_ajax_blogcentral_font_options", "blogcentral_font_options" );
	add_action( "wp_ajax_nopriv_blogcentral_font_options", "blogcentral_no_go" );
	add_action( "wp_ajax_blogcentral_display_template_ajax", "blogcentral_display_template_ajax" );
	add_action( "wp_ajax_nopriv_blogcentral_display_template_ajax", "blogcentral_no_go" );
	
	/*
	 * add filters to override the options for the 4bzCore plugin's shortcodes and widgets, if the plugin
	 * is installed and active.
	 */
	if ( isset( $fourbzcore_plugin ) ) {
		add_filter( '4bzcore_options_featured_posts', 'blogcentral_options_featured_posts', 10, 3 );
		add_filter( '4bzcore_options_popular_posts', 'blogcentral_options_popular_posts', 10, 3 );
		add_filter( '4bzcore_options_recent_posts', 'blogcentral_options_recent_posts', 10, 3 );
		add_filter( '4bzcore_options_related_posts', 'blogcentral_options_related_posts', 10, 3 );
		add_filter( '4bzcore_options_facebook_comments', 'blogcentral_options_facebook_comments', 10, 3 );
		add_filter( '4bzcore_options_contact_info', 'blogcentral_options_contact_info', 10, 3 );
		add_filter( '4bzcore_options_contact_form', 'blogcentral_options_contact_form', 10, 3 );
		add_filter( '4bzcore_options_slideshow', 'blogcentral_options_slideshow', 10, 3 );
		add_filter( '4bzcore_options_progressbars', 'blogcentral_options_progressbars', 10, 3 );
		add_filter( '4bzcore_options_image_text', 'blogcentral_options_image_text', 10, 3 );
		add_filter( '4bzcore_options_author_bio', 'blogcentral_options_author_bio', 10, 3 );
		add_filter( '4bzcore_options_flickr_photos', 'blogcentral_options_flickr_photos', 10, 3 );
	}
}

/**
 * 4.0 Setup + Utility Functions
 *-----------------------------------------------------------------------*/

/**
 * Default values for options
 *
 * @since 1.0.0
 */
function blogcentral_get_option_defaults() {	
	$blogcentral_defaults = array(
		"blog_demo"							=>	'0',
		"block_display"						=>	null,
		"box_display"						=>	null,
		"color_scheme"						=>	'1',
		"body_txt_color"					=>	null,
		"body_lnk_color"					=>	null,
		"body_lnk_hover_color"				=>	null,
		"body_lnk_visited_color"			=>	null,
		"main_content_back_color"			=>	null,
		"h1"								=>	array(
			"size"	=>	null,
			'color'	=>	null,
		),
		"h2"								=>	array(
			"size"	=>	null,
			'color'	=>	null,
		),
		"h3"								=>	array(
			"size"	=>	null,
			'color'	=>	null,
		),
		"h4"								=>	array(
			"size"	=>	null,
			'color'	=>	null,
		),
		"h5"								=>	array(
			"size"	=>	null,
			'color'	=>	null,
		),
		"h6"								=>	array(
			"size"	=>	null,
			'color'	=>	null,
		),
		"widget_border"						=>	null,
		"page_header"						=>	null,
		"page_header_bck_img"				=>	null,
		"page_header_bck_img_width"			=>	null,
		"page_header_bck_img_height"		=>	null,
		"page_header_bck_img_alt"			=>	null,
		"page_header_bck_img_attachment"	=>	'scroll',
		"page_header_bck_img_position"		=>	null,
		"page_header_bck_img_repeat"		=>	"repeat",
		"page_header_height"				=>	null,
		"page_header_color"					=>	null,
		"page_header_layout"				=>	'1',
		"show_todays_date"					=>	null,
		"breadcrumbs"						=>	null,
		"blog_info"							=> 	null,
		"show_sidebar"						=>	null,
		"default_user_img"					=>	null,
		"default_user_img_width"			=>	null,
		"default_user_img_height"			=>	null,
		"default_user_img_alt"				=>	null,
		"show_color_chooser"				=>	null,
		"contact_info"						=>	array(
			"contact_address"		=>	null,
			"address_icon"			=>	null,
			"contact_phone"			=>	null,
			"phone_icon"			=>	null,
			"contact_url"			=>	null,
			"url_icon"				=>	null,
			"contact_email"			=>	null,
			"email_icon"			=>	null,
			"contact_facebook"		=>	null,
			"facebook_icon"			=>	null,
			"contact_twitter"		=>	null,
			"twitter_icon"			=>	null,
			"contact_google"		=>	null,
			"google_icon"			=>	null,
			"contact_linkedin"		=>	null,
			"linkedin_icon"			=>	null,
			"contact_instagram"		=>	null,
			"instagram_icon"		=>	null,
			"contact_tumblr"		=>	null,
			"tumblr_icon"			=>	null,
			"contact_pinterest"		=>	null,
			"pinterest_icon"		=>	null,
			"contact_info_layout"	=>	'1',
			"user_id"				=>	null,
			"show_address"			=>	null,
			"show_phone"			=>	null,
			"show_email"			=>	null,
			"show_url"				=>	null,
			"show_social"			=>	null,
			"share_title"			=>	null,
		),
		"contact_form"						=>	null,
		"fonts"								=>	array(
			"body_txt_font_family"		=>	"no_font",
			"body_txt_font_type"		=>	"system",
			"body_txt_font_weight"		=>	"regular",
			"body_txt_font_subsets"		=>	null,
			"headers_font_family"		=>	"no_font",
			"headers_font_type"			=>	"system",
			"headers_font_weight"		=>	"regular",
			"headers_font_subsets"		=>	null,
			"main_menu_font_family"		=>	"no_font",
			"main_menu_font_type"		=>	"system",
			"main_menu_font_weight"		=>	"regular",
			"main_menu_font_subsets"	=>	null,
		),
		"facebook_app_id"					=>	null,
		"google_app_id"						=>	null,
		"header_sticky"						=>	null,
		"header_back_color"					=>	null,
		"header_txt_color"					=>	null,
		"header_lnk_color"					=>	null,
		"logo"								=>	null,
		"logo_width"						=>	null,
		"logo_height"						=>	null,
		"logo_alt"							=>	null,
		"favicon"							=>	null,
		"favicon_width"						=>	null,
		"favicon_height"					=>	null,
		"favicon_alt"						=>	null,
		"show_contact"						=>	null,
		"show_search"						=>	null,
		"menu_back_color"					=>	null,
		"menu_lnk_color"					=>	null,
		"menu_active_color"					=>	null,
		"submenu_back_color"				=>	null,
		"submenu_lnk_color"					=>	null,
		"posts_general"						=>	array(
			"img_size"			=>	"scale",
			"sticky_display"	=>	null,
			"standard_icon"		=>	null,
			"image_icon"		=>	null,
			"audio_icon"		=>	null,
			"video_icon"		=>	null,
			"gallery_icon"		=>	null,
			"link_icon"			=>	null,
			"quote_icon"		=>	null,
			"author_icon"		=>	null,
			"date_icon"			=>	null,
			"categories_icon"	=>	null,
			"tags_icon"			=>	null,
			"comments_icon"		=>	null,
		),
		"posts_landing"						=>	array(
			"layout"					=>	"layout1",
			'title_text'				=>	null,
			"title_border"				=>	"default",
			'tagline_text'				=>	null,
			"tagline_border"			=>	"0",
			"wrap_class"				=>	null,
			"title_class"				=>	null,
			"cols"						=>	"1",
			"border"					=>	"0",
			"masonry"					=>	null,
			"alternate_color"			=>	null,
			'sticky_only'				=>	false,
			'ignore_sticky'				=>	false,
			'limit'						=>	null,
			"show_title"				=>	null,
			"show_content"				=>	null,
			"show_author"				=>	null,
			"show_date"					=>	null,
			"show_categories"			=>	null,
			"show_tags"					=>	null,
			"show_stats"				=>	null,
			"show_social"				=>	null,
			"share_title"				=>	null,
			"show_icons"				=>	null,
			"post_meta_layout"			=>	'1',
			"gallery_slideshow"			=>	null,
			"quote_layout"				=>	'2',
			"display_format_icon"		=>	null,
		),
		"posts_single"						=>	array(
			"override_landing"			=>	null,
			"layout"					=>	"layout1",
			'title_text'				=>	null,
			"title_border"				=>	"default",
			'tagline_text'				=>	null,
			"tagline_border"			=>	"0",
			"wrap_class"				=>	null,
			"title_class"				=>	null,
			"border"					=>	"0",
			"show_title"				=>	false,
			"show_content"				=>	"on",
			"show_author"				=>	null,
			"show_date"					=>	null,
			"show_categories"			=>	null,
			"show_tags"					=>	null,
			"show_stats"				=>	null,
			"show_social"				=>	null,
			"share_title"				=>	null,
			"show_icons"				=>	null,
			"post_meta_layout"			=>	'1',
			"gallery_slideshow"			=>	null,
			"quote_layout"				=>	'2',
			"display_format_icon"		=>	null,
		),
		"footer_extended"					=>	null,
		"footer_bck_color"					=>	null,
		"footer_txt_color"					=>	null,
		"footer_lnk_color"					=>	null,
		"footer_lnk_hover_color"			=>	null,
		"footer_lnk_visited_color"			=>	null,
		"copyright"							=>	null,
		"custom_css"						=>	null,
		"google_fonts_url"					=>	null,
	);

	return apply_filters( 'blogcentral_option_defaults', $blogcentral_defaults );
}

/**
 * Initial values for options
 *
 * Used if the $blogcentral_opts variable is empty, meaning it has never been saved to the database.
 *
 * @since 1.0.0
 */
function blogcentral_get_option_initial() {
	global $blogcentral_defaults;
	
	$blogcentral_initial = array_merge( $blogcentral_defaults, array( 
		"color_scheme"						=>	'4',
		"logo"								=>	BLOGCENTRAL_THEME_URL . "/images/preview/full-dark-logo.png",
		"show_sidebar"						=>	'on',
		"show_contact"						=>	'on',
		'show_search'						=>	'on',
		"show_color_chooser"				=>	'on',
		"contact_info"						=>	array_merge( $blogcentral_defaults['contact_info'],
			array(
				"contact_address"		=>	'Your address',
				"address_icon"			=>	'fa-home',
				"contact_phone"			=>	'Your phone number',
				"phone_icon"			=>	'fa-phone',
				"contact_url"			=>	'Your website',
				"url_icon"				=>	'fa-globe',
				"contact_email"			=>	'Your email',
				"email_icon"			=>	'fa-envelope',
				"contact_facebook"		=>	'facebook',
				"facebook_icon"			=>	'fa-facebook',
				"contact_twitter"		=>	'twitter',
				"twitter_icon"			=>	'fa-twitter',
				"contact_google"		=>	'google',
				"google_icon"			=>	'fa-google-plus',
				"contact_linkedin"		=>	'linkedin',
				"linkedin_icon"			=>	'fa-linkedin',
				"contact_instagram"		=>	'instagram',
				"instagram_icon"		=>	'fa-instagram',
				"contact_tumblr"		=>	'tumblr',
				"tumblr_icon"			=>	'fa-tumblr',
				"contact_pinterest"		=>	'pinterest',
				"pinterest_icon"		=>	'fa-pinterest',
				"contact_info_layout"	=>	'1',
				"show_address"			=>	'on',
				"show_phone"			=>	'on',
				"show_email"			=>	'on',
				"show_url"				=>	'on',
				"show_social"			=>	'on',
			)
		),
		"widget_border"						=>	'div3',
		"page_header"			=>	'on',
		"page_header_bck_img"				=>	BLOGCENTRAL_THEME_URL . "/images/preview/hatback.jpg",
		"page_header_bck_img_alt"			=>	__( 'hat back', BLOGCENTRAL_TXT_DOMAIN ),
		"page_header_bck_img_attachment"	=>	'fixed',
		"page_header_bck_img_repeat"		=>	"no-repeat",
		"page_header_color"					=>	"black",
		"page_header_layout"	=>	'1',
		"posts_general"						=>	array_merge( $blogcentral_defaults['posts_general'],
			array(
				"standard_icon"		=>	'fa-file-o',
				"image_icon"		=>	'fa-picture-o',
				"audio_icon"		=>	'fa-volume-up',
				"video_icon"		=>	'fa-video-camera',
				"gallery_icon"		=>	'fa-film',
				"link_icon"			=>	'fa-chain',
				"quote_icon"		=>	'fa-quote-right',
				"author_icon"		=>	'fa-user',
				"date_icon"			=>	'fa-clock-o',
				"categories_icon"	=>	'fa-folder',
				"tags_icon"			=>	'fa-tags',
				"comments_icon"		=>	'fa-comments-o',
			)
		),
		'posts_landing'	=>	array_merge( $blogcentral_defaults['posts_landing'],
			array(
				'cols'				=>	2,
				"masonry"					=>	'on',
				'show_title'		=>	'on',
				'show_author'		=>	'on',
				'show_date'			=>	'on',
				'show_stats'		=>	'on',
				'show_categories'	=>	'on',
				'show_tags'			=>	'on',
				'show_content'		=>	'on',
				'show_social'		=>	'on',
				'show_icons'		=>	'on',
				"gallery_slideshow"			=>	'on',
				"display_format_icon"		=>	'on',
			)
		),
		'posts_single'	=>	array_merge( $blogcentral_defaults['posts_single'],
			array(
				'show_author'		=>	'on',
				'show_date'			=>	'on',
				'show_stats'		=>	'on',
				'show_categories'	=>	'on',
				'show_tags'			=>	'on',
				'show_social'		=>	'on',
				'show_icons'		=>	'on',
				"gallery_slideshow"			=>	'on',
				"display_format_icon"		=>	'on',
			)
		),
		"copyright"				=>	"<img src='" . BLOGCENTRAL_THEME_URL . "/images/preview/full-light-logo.png'" . " alt='logo' />" . sprintf( __( "Copyright &#169; 2015 %s All Rights Reserved", BLOGCENTRAL_TXT_DOMAIN ), get_bloginfo( 'name' ) ),
		)
	);
	
	return apply_filters( 'blogcentral_option_initial', $blogcentral_initial );
}

/**
 * Initialize global theme option variable
 *
 * @since 1.0.1
 */
function blogcentral_initialize_global_opts() {
	global $blogcentral_initial;
	global $blogcentral_opts;
	
	// Get cached theme options, if not cached, then use get_option.
	if ( ! is_admin() && ! is_home() ) {
		$cached_opts = get_transient( 'blogcentral_options' );

		if ( ! $cached_opts ) {
			$blogcentral_opts = get_option( BLOGCENTRAL_DB_OPTIONS_NAME );
			set_transient( 'blogcentral_options', $blogcentral_opts, 21 * DAY_IN_SECONDS );
		} else {
			$blogcentral_opts = $cached_opts;
		}
	} else {
		$blogcentral_opts = get_option( BLOGCENTRAL_DB_OPTIONS_NAME );
	}
	
	if ( empty( $blogcentral_opts ) ) {
		$blogcentral_opts = $blogcentral_initial;
	}
	
	return $blogcentral_opts;
}

/**
 * Predefined options for the demos.
 *
 * @since 1.0.1
 */
function blogcentral_get_demos_opts() {
	$blogcentral_demos_opts = array(
		array(
			"blog_demo"				=>	'1',
			"color_scheme"			=>	"4",
			"widget_border"			=>	"div3",
			"page_header"			=>	'on',
			"page_header_bck_img"				=>	BLOGCENTRAL_THEME_URL . "/images/preview/hatback.jpg",
			"page_header_bck_img_alt"			=>	__( 'hat back', BLOGCENTRAL_TXT_DOMAIN ),
			"page_header_bck_img_attachment"	=>	'fixed',
			"page_header_bck_img_repeat"		=>	"no-repeat",
			"page_header_color"					=>	"#222",
			"page_header_layout"	=>	'1',
			"breadcrumbs"			=>	"on",
			"show_sidebar"=>"on",
			"default_user_img"		=>	BLOGCENTRAL_THEME_URL . "/images/preview/hatback.jpg",
			"default_user_img_alt"	=>	__( 'default user image', BLOGCENTRAL_TXT_DOMAIN ),
			"show_color_chooser"	=>	'on',
			"header_sticky"			=>	"on",
			"logo"					=>	BLOGCENTRAL_THEME_URL . "/images/preview/full-dark-logo.png",
			"logo_alt"				=>	__( "logo", BLOGCENTRAL_TXT_DOMAIN ),
			"show_contact"						=>	'on',
			'show_search'			=>	'on',
			"contact_info"						=>	array(
				"contact_address"		=>	'Your address',
				"address_icon"			=>	'fa-home',
				"contact_phone"			=>	'Your phone number',
				"phone_icon"			=>	'fa-phone',
				"contact_url"			=>	'Your website',
				"url_icon"				=>	'fa-globe',
				"contact_email"			=>	'Your email',
				"email_icon"			=>	'fa-envelope',
				"contact_facebook"		=>	'facebook',
				"facebook_icon"			=>	'fa-facebook',
				"contact_twitter"		=>	'twitter',
				"twitter_icon"			=>	'fa-twitter',
				"contact_google"		=>	'google',
				"google_icon"			=>	'fa-google-plus',
				"contact_linkedin"		=>	'linkedin',
				"linkedin_icon"			=>	'fa-linkedin',
				"contact_instagram"		=>	'instagram',
				"instagram_icon"		=>	'fa-instagram',
				"contact_tumblr"		=>	'tumblr',
				"tumblr_icon"			=>	'fa-tumblr',
				"contact_pinterest"		=>	'pinterest',
				"pinterest_icon"		=>	'fa-pinterest',
				"contact_info_layout"	=>	'1',
				"show_address"			=>	'on',
				"show_phone"			=>	'on',
				"show_email"			=>	'on',
				"show_url"				=>	'on',
				"show_social"			=>	'on',
			),
			"submenu_back_color"	=>	"#333",
			"submenu_lnk_color"		=>	"white",
			"posts_general"			=>	array(
				"img_size"			=>	"scale",
				"standard_icon"		=>	"fa-pencil",
				"image_icon"		=>	"fa-camera",
				"audio_icon"		=>	"fa-music",
				"video_icon"		=>	"fa-video-camera",
				"gallery_icon"		=>	"fa-film",
				"link_icon"			=>	"fa-chain-broken",
				"quote_icon"		=>	"fa-quote-left",
				"author_icon"		=>	"fa-user",
				"date_icon"			=>	"fa-clock-o",
				"categories_icon"	=>	"fa-folder",
				"tags_icon"			=>	"fa-tags",
				"comments_icon"		=>	"fa-comments-o",
			),
			"posts_landing"			=>	array(
				"layout"					=>	"layout1",
				"cols"						=>	"1",
				"border"					=>	"0",
				"masonry"					=>	"on",
				"show_title"				=>	"on",
				"show_content"				=>	"on",
				"show_author"				=>	"on",
				"show_date"					=>	"on",
				"show_categories"			=>	"on",
				"show_tags"					=>	"on",
				"show_stats"				=>	"on",
				"show_social"				=>	"on",
				"share_title"				=>	null,
				"show_icons"				=>	"on",
				"post_meta_layout"			=>	'1',
				"gallery_slideshow"			=>	"on",
				"quote_layout"				=>	'2',
			),
			"posts_single"			=>	array(),
			"footer_extended"		=>	"on",
			"copyright"				=>	"<img src='" . BLOGCENTRAL_THEME_URL . "/images/preview/full-light-logo.png'" . " alt='logo' />" . sprintf( __( "Copyright &#169; 2015 %s All Rights Reserved", BLOGCENTRAL_TXT_DOMAIN ), get_bloginfo( 'name' ) ),
			"custom_css"			=>	"
										#main-content {
											background-color: transparent;
										}
										
										.single-post #main-posts-cont {
											border-bottom: 3px double #eee;
										}
									
										#comments,#main-area .contact-form, #main-area .author-bio, #main-content .widget-container {
											padding:36px 20px !important;
											background-color: white;
											margin-bottom: 32px;
										}
									",
		),
		array(
			"blog_demo"				=>	'2',
			"color_scheme"			=>	"4",
			"widget_border"			=>	"div3",
			"page_header"			=>	'on',
			"page_header_bck_img"				=>	BLOGCENTRAL_THEME_URL . "/images/preview/hatback.jpg",
			"page_header_bck_img_alt"			=>	__( 'hat back', BLOGCENTRAL_TXT_DOMAIN ),
			"page_header_bck_img_attachment"	=>	'fixed',
			"page_header_bck_img_repeat"		=>	"no-repeat",
			"page_header_color"					=>	"#ffffff",
			"page_header_layout"	=>	'2',
			"breadcrumbs"			=>	"on",
			"default_user_img"		=>	BLOGCENTRAL_THEME_URL . "/images/preview/hatback.jpg",
			"default_user_img_alt"	=>	__( 'default user image', BLOGCENTRAL_TXT_DOMAIN ),
			"show_color_chooser"	=>	'on',
			"header_sticky"			=>	"on",
			"logo"					=>	BLOGCENTRAL_THEME_URL . "/images/preview/full-dark-logo.png",
			"logo_alt"				=>	__( "logo", BLOGCENTRAL_TXT_DOMAIN ),
			"show_contact"						=>	'on',
			'show_search'			=>	'on',
			"contact_info"						=>	array(
				"contact_address"		=>	'Your address',
				"address_icon"			=>	'fa-home',
				"contact_phone"			=>	'Your phone number',
				"phone_icon"			=>	'fa-phone',
				"contact_url"			=>	'Your website',
				"url_icon"				=>	'fa-globe',
				"contact_email"			=>	'Your email',
				"email_icon"			=>	'fa-envelope',
				"contact_facebook"		=>	'facebook',
				"facebook_icon"			=>	'fa-facebook',
				"contact_twitter"		=>	'twitter',
				"twitter_icon"			=>	'fa-twitter',
				"contact_google"		=>	'google',
				"google_icon"			=>	'fa-google-plus',
				"contact_linkedin"		=>	'linkedin',
				"linkedin_icon"			=>	'fa-linkedin',
				"contact_instagram"		=>	'instagram',
				"instagram_icon"		=>	'fa-instagram',
				"contact_tumblr"		=>	'tumblr',
				"tumblr_icon"			=>	'fa-tumblr',
				"contact_pinterest"		=>	'pinterest',
				"pinterest_icon"		=>	'fa-pinterest',
				"contact_info_layout"	=>	'1',
				"show_address"			=>	'on',
				"show_phone"			=>	'on',
				"show_email"			=>	'on',
				"show_url"				=>	'on',
				"show_social"			=>	'on',
			),
			"submenu_back_color"	=>	"#333",
			"submenu_lnk_color"		=>	"white",
			"posts_general"			=>	array(
				"img_size"			=>	"scale",
				"standard_icon"		=>	"fa-pencil",
				"image_icon"		=>	"fa-camera",
				"audio_icon"		=>	"fa-music",
				"video_icon"		=>	"fa-video-camera",
				"gallery_icon"		=>	"fa-picture-o",
				"link_icon"			=>	"fa-chain-broken",
				"quote_icon"		=>	"fa-quote-left",
				"author_icon"		=>	"fa-user",
				"date_icon"			=>	"fa-clock-o",
				"categories_icon"	=>	"fa-folder",
				"tags_icon"			=>	"fa-tags",
				"comments_icon"		=>	"fa-comments-o",
			),
			"posts_landing"			=>	array(
				"layout"					=>	"layout1",
				"cols"						=>	"3",
				"border"					=>	"0",
				"masonry"					=>	"on",
				"show_title"				=>	"on",
				"show_content"				=>	"on",
				"show_author"				=>	"on",
				"show_date"					=>	"on",
				"show_categories"			=>	"on",
				"show_tags"					=>	"on",
				"show_stats"				=>	"on",
				"show_social"				=>	"on",
				"share_title"				=>	null,
				"show_icons"				=>	"on",
				"post_meta_layout"			=>	'1',
				"gallery_slideshow"			=>	"on",
				"quote_layout"				=>	'2',
			),
			"posts_single"			=>	array(),
			"footer_extended"		=>	"on",
			"copyright"				=>	"<img src='" . BLOGCENTRAL_THEME_URL . "/images/preview/full-light-logo.png'" . " alt='logo' />" . sprintf( __( "Copyright &#169; 2015 %s All Rights Reserved", BLOGCENTRAL_TXT_DOMAIN ), get_bloginfo( 'name' ) ),
			"custom_css"			=>	"
										#main-content {
											background-color: transparent;
										}
										
										.single-post #main-posts-cont {
											border-bottom: 3px double #eee;
										}
										
										#comments,#main-area .contact-form, #main-area .author-bio, #main-content .widget-container {
											padding:36px 20px !important;
											background-color: white;
											margin-bottom: 32px;
										}
										
									",
		),
		array(
			"blog_demo"				=>	'3',
			"color_scheme"			=>	"4",
			"widget_border"			=>	"div3",
			"page_header"			=>	'on',
			"page_header_bck_img"				=>	BLOGCENTRAL_THEME_URL . "/images/preview/hatback.jpg",
			"page_header_bck_img_alt"			=>	__( 'hatback', BLOGCENTRAL_TXT_DOMAIN ),
			"page_header_bck_img_attachment"	=>	'fixed',
			"page_header_bck_img_repeat"		=>	"no-repeat",
			"page_header_color"					=>	"#222",
			"page_header_layout"	=>	'3',
			"breadcrumbs"			=>	"on",
			"default_user_img"		=>	BLOGCENTRAL_THEME_URL . "/images/preview/hatback.jpg",
			"default_user_img_alt"	=>	__( 'default user image', BLOGCENTRAL_TXT_DOMAIN ),
			"show_color_chooser"	=>	'on',
			"contact_info"			=> 	array(
				"contact_address"		=>	__( "Cincinnati OH", BLOGCENTRAL_TXT_DOMAIN ),
				"address_icon"			=>	"fa-home",
				"contact_info_layout"	=>	'1',
			),
			"header_sticky"			=>	"on",
			"logo"					=>	BLOGCENTRAL_THEME_URL . "/images/preview/full-dark-logo.png",
			"logo_alt"				=>	__( "logo", BLOGCENTRAL_TXT_DOMAIN ),
			"show_contact"						=>	'on',
			'show_search'			=>	'on',
			"contact_info"						=>	array(
				"contact_address"		=>	'Your address',
				"address_icon"			=>	'fa-home',
				"contact_phone"			=>	'Your phone number',
				"phone_icon"			=>	'fa-phone',
				"contact_url"			=>	'Your website',
				"url_icon"				=>	'fa-globe',
				"contact_email"			=>	'Your email',
				"email_icon"			=>	'fa-envelope',
				"contact_facebook"		=>	'facebook',
				"facebook_icon"			=>	'fa-facebook',
				"contact_twitter"		=>	'twitter',
				"twitter_icon"			=>	'fa-twitter',
				"contact_google"		=>	'google',
				"google_icon"			=>	'fa-google-plus',
				"contact_linkedin"		=>	'linkedin',
				"linkedin_icon"			=>	'fa-linkedin',
				"contact_instagram"		=>	'instagram',
				"instagram_icon"		=>	'fa-instagram',
				"contact_tumblr"		=>	'tumblr',
				"tumblr_icon"			=>	'fa-tumblr',
				"contact_pinterest"		=>	'pinterest',
				"pinterest_icon"		=>	'fa-pinterest',
				"contact_info_layout"	=>	'1',
				"show_address"			=>	'on',
				"show_phone"			=>	'on',
				"show_email"			=>	'on',
				"show_url"				=>	'on',
				"show_social"			=>	'on',
			),
			"submenu_back_color"	=>	"#333",
			"submenu_lnk_color"		=>	"white",
			"posts_general"			=>	array(
				"img_size"			=>	"scale",
				"standard_icon"		=>	"fa-pencil",
				"image_icon"		=>	"fa-camera",
				"audio_icon"		=>	"fa-music",
				"video_icon"		=>	"fa-video-camera",
				"gallery_icon"		=>	"fa-picture-o",
				"link_icon"			=>	"fa-chain-broken",
				"quote_icon"		=>	"fa-quote-left",
				"author_icon"		=>	"fa-user",
				"date_icon"			=>	"fa-clock-o",
				"categories_icon"	=>	"fa-folder",
				"tags_icon"			=>	"fa-tags",
				"comments_icon"		=>	"fa-comments-o",
			),
			"posts_landing"			=>	array(
				"layout"					=>	"layout2",
				"cols"						=>	"1",
				"border"					=>	"0",
				"masonry"					=>	"on",
				"show_title"				=>	"on",
				"show_content"				=>	"on",
				"show_author"				=>	"on",
				"show_date"					=>	"on",
				"show_categories"			=>	"on",
				"show_tags"					=>	"on",
				"show_stats"				=>	"on",
				"show_social"				=>	"on",
				"share_title"				=>	null,
				"show_icons"				=>	"on",
				"post_meta_layout"			=>	'1',
				"gallery_slideshow"			=>	"on",
				"quote_layout"				=>	'2',
			),
			"posts_single"			=>	array(	
				"override_landing"			=>	"on",
				"layout"					=>	"layout1",
				"cols"						=>	"1",
				"border"					=>	"0",
				"masonry"					=>	"on",
				"show_title"				=>	"on",
				"show_author"				=>	"on",
				"show_date"					=>	"on",
				"show_categories"			=>	"on",
				"show_tags"					=>	"on",
				"show_stats"				=>	"on",
				"show_social"				=>	"on",
				"share_title"				=>	null,
				"show_icons"				=>	"on",
				"post_meta_layout"			=>	'1',
				"gallery_slideshow"			=>	"on",
				"quote_layout"				=>	'2',
			),
			"footer_extended"		=>	"on",
			"copyright"				=>	"<img src='" . BLOGCENTRAL_THEME_URL . "/images/preview/full-light-logo.png'" . " alt='logo' />" . sprintf( __( "Copyright &#169; 2015 %s All Rights Reserved", BLOGCENTRAL_TXT_DOMAIN ), get_bloginfo( 'name' ) ),
			"custom_css"			=>	"
										#main-content {
											background-color: transparent;
										}
										.single-post #main-posts-cont {
											border-bottom: 3px double #eee;
										}
										#comments,#main-area .contact-form, #main-area .author-bio, #main-content .widget-container {
											padding:36px 20px !important;
											background-color: white;
											margin-bottom: 32px;
										}
									",
		),
	);
	
	return apply_filters( 'blogcentral_demos_opts', $blogcentral_demos_opts );
}

if ( ! function_exists( 'blogcentral_theme_setup' ) ) {
	/**
	 * Theme setup
	 *
	 * @since 1.0.0
	 */
	function blogcentral_theme_setup() {
		// Register the main navigation menu
		register_nav_menu( 'primary', __( 'Primary Menu', BLOGCENTRAL_TXT_DOMAIN ) );
		
		/*
		 * Make theme available for translation.
		 * Translations can be saved in the /languages/ directory.
		 */
		load_theme_textdomain( BLOGCENTRAL_TXT_DOMAIN, get_template_directory() . '/languages' );
		
		// This theme styles the visual editor to resemble the theme style.
		add_editor_style( array( 'css/editor-style.css' ) );
		
		// Add RSS feed links to <head> for posts and comments.
		add_theme_support( 'automatic-feed-links' );

		// Add title-tag support.
		add_theme_support( "title-tag" );
		
		/**
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

		// This theme supports custom background color and image.
		add_theme_support( 'custom-background' );

		// This theme supports the following post formats by default.
		add_theme_support( 'post-formats', array( 'audio', 'aside', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video', ) );

		// Support post thumbnails.
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 604, 270, true );
	}
}

if ( ! function_exists( 'blogcentral_register_widgets_areas' ) ) {
	/**
	 * Register widgetized areas
	 *
	 * @since 1.0.0
	 */
	function blogcentral_register_widgets_areas() {
		$extra_class = '';
		global $blogcentral_opts;
		
		// Add class for default widget border, if set
		if ( isset( $blogcentral_opts['widget_border'] ) ) {
			$extra_class .= ' ' . $blogcentral_opts['widget_border'];
		}
		
		register_sidebar( array(
			'name'			=>	__( 'Header Top Widget', BLOGCENTRAL_TXT_DOMAIN ),     
			'id' 			=> 	'header-top-widget',
			'description' 	=> 	__( 'Widgets in this area will be shown in the header top area. Ideal for contact information.', BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container %2$s">',
			'after_widget' 	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		
		register_sidebar( array(
			'name'			=>	__( 'Header Widget', BLOGCENTRAL_TXT_DOMAIN ),     
			'id' 			=> 	'header-widget',
			'description' 	=> 	__( 'Widgets in this area will be shown in the header area to the left of the logo. Ideal for advertisement banner or search form. The size is 77% by 50px.', BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container %2$s">',
			'after_widget' 	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		
		register_sidebar( array(
			'name'			=>	__( 'PreContent Widget', BLOGCENTRAL_TXT_DOMAIN ),     
			'id' 			=> 	'precontent-widget',
			'description' 	=> 	__( 'Widgets in this area will be shown in the main content, above the posts and 
									sidebar. Ideal for a slideshow. Is full width with no left and right spacing.', 
									BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container %2$s">',
			'after_widget' 	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		
		register_sidebar( array(
			'name'			=>	__( 'PreContent Widget About Me', BLOGCENTRAL_TXT_DOMAIN ),     
			'id' 			=> 	'precontent-widget-about-me',
			'description' 	=> 	__( 'Widgets in this area will be shown on the about me page in the main content, 
									above the posts and sidebar. It is ideal for a slideshow. Is full width with no
									left and right spacing.', BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container %2$s">',
			'after_widget' 	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		register_sidebar( array(
			'name'			=>	__( 'Preposts Left Widget', BLOGCENTRAL_TXT_DOMAIN ),     
			'id'			=> 	'preposts-widget-left',
			'description' 	=> 	__( 'Widgets in this area will be shown in the main content area of the blog 
									landing page on the left,above the main posts. It is recommended that if you 
									place widgets in this area, do so in the preposts right widget area also. If no
									widgets are dragged into the area, will not show anything.', BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container %2$s">',
			'after_widget' 	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		
		register_sidebar( array(
			'name'			=>	__( 'PrePosts Right Widget', BLOGCENTRAL_TXT_DOMAIN ),     
			'id' 			=> 	'preposts-widget-right',
			'description' 	=> 	__(	'Widgets in this area will be shown in the main content area of the blog 
									landing page on the right,above the main posts. It is recommended that if you 
									place widgets in this area, do so in the preposts left widget area also. If no
									widgets are dragged into the area, will not show anything.', BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container %2$s">',
			'after_widget' 	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		
		register_sidebar( array(
			'name'			=>	__( 'Main Widget', BLOGCENTRAL_TXT_DOMAIN ),     
			'id' 			=> 	'main-widget',
			'description' 	=> 	__( 'This area is where the main wordpress loop is shown. You can override this 
									behavior by dragging widgets to this area.', BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container %2$s">',
			'after_widget' 	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		
		register_sidebar( array(
			'name'			=>	__( 'Post Posts Widget', BLOGCENTRAL_TXT_DOMAIN ),     
			'id' 			=> 	'post-posts-widget',
			'description' 	=> 	__( 'Widgets in this area will be shown in the main content, below the posts and 
									sidebar. It is ideal for advertisement or a call to action box. Is full width,
									with no side paddings.', BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container %2$s">',
			'after_widget' 	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		
		register_sidebar( array(
			'name'			=>	__( 'Sidebar Right', BLOGCENTRAL_TXT_DOMAIN ),    
			'id' 			=> 	'sidebar-rght',
			'description' 	=> 	__( 'Widgets in this area will be shown in the right sidebar', BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container widget %2$s">',
			'after_widget' 	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		
		register_sidebar( array(
			'name'			=>	__( 'Single Post After Widget', BLOGCENTRAL_TXT_DOMAIN ),    
			'id' 			=> 	'single-post-after-widget',
			'description' 	=> 	__( 'Widgets in this area will be shown on single post pages, immediately below the author bio. It is ideal for related posts. ', BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container widget %2$s">',
			'after_widget' 	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		
		register_sidebar( array(
			'name'			=>	__( 'Footer Left Widget', BLOGCENTRAL_TXT_DOMAIN ),                                            
			'id'			=>	'footer-widget-lft',
			'description' 	=> 	__( 'Widgets in this area will be shown in the left footer column', BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => '<aside id="%1$s" class="widget-container %2$s">',
			'after_widget' 	=> '</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		
		register_sidebar( array(
			'name'			=>	__( 'Footer Middle Widget', BLOGCENTRAL_TXT_DOMAIN ),     
			'id' 			=> 	'footer-widget-mid',                                                
			'description' 	=> 	__( 'Widgets in this area will be shown in the center footer column', BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container %2$s">',
			'after_widget'	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		
		register_sidebar( array(
			'name'			=>	__( 'Footer Right Widget', BLOGCENTRAL_TXT_DOMAIN ),     
			'id' 			=> 	'footer-widget-rght',                                                
			'description' 	=> 	__( 'Widgets in this area will be shown in the right footer column', BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container %2$s">',
			'after_widget' 	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		
		register_sidebar( array(
			'name'			=>	__( 'Footer Bottom Left Widget', BLOGCENTRAL_TXT_DOMAIN ),     
			'id' 			=> 	'footer-widget-btm-lft',                                                
			'description' 	=> 	__( 'Widgets in this area will be shown in the bottom left footer column', 
									BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container %2$s">',
			'after_widget' 	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
		
		register_sidebar( array(
			'name'			=>	__( 'Footer Bottom Right Widget', BLOGCENTRAL_TXT_DOMAIN ),     
			'id' 			=> 	'footer-widget-btm-rght',                                                
			'description' 	=> 	__( 'Widgets in this area will be shown in the bottom right footer column',
									BLOGCENTRAL_TXT_DOMAIN ),
			'before_widget' => 	'<aside id="%1$s" class="widget-container %2$s">',
			'after_widget' 	=> 	'</aside>',
			'before_title' 	=> 	'<div class="header-wrap"><h3 class="widget-title' . $extra_class . '">',
			'after_title' 	=> 	'</h3></div>',
		) );
	}
}

/**
 * 5.0 Admin Side Functions
 *-----------------------------------------------------------------------*/

/**
 * 5.1 Enqueue Scripts & Style
 *-----------------------------------------------------------------------*/
if ( ! function_exists( 'blogcentral_admin_scripts' ) ) {
	/**
	 * Enqueue scripts and style needed on admin side.
	 *
	 * @since 1.0.0
	 */
	function blogcentral_admin_scripts() {
		$screen = get_current_screen();
		
		if ( ( isset( $_GET['page'] ) && 'blogcentral_options' === $_GET['page'] ) || 'post' === $screen->id || 'page' === $screen->id || 'product' === $screen->id || 'widgets' === $screen->id ) {
			// Enqueue jquery and jquery ui.
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			
			// Enqueue wordpress media uploader.
			wp_enqueue_media();
			
			// Enqueue color picker.
			wp_enqueue_style( 'wp-color-picker' );
			
			global $fourbzcore_plugin;
			
			// Enqueue administration script.
			if ( isset( $fourbzcore_plugin ) ) {
				wp_enqueue_script( 'blogcentral-admin-script', BLOGCENTRAL_THEME_URL .'/js/admin.js', array( 'jquery', 'jquery-ui-core',
					'jquery-ui-tabs', 'jquery-ui-sortable', 'wp-color-picker', 'fourbzcore-admin-script' ) );
			} else {
				wp_enqueue_script( 'blogcentral-admin-script', BLOGCENTRAL_THEME_URL .'/js/admin.js', array( 'jquery', 'jquery-ui-core',
					'jquery-ui-tabs', 'jquery-ui-sortable', 'wp-color-picker', ) );
			}
			
			// Enqueue styles.
			wp_enqueue_style( 'blogcentral-font-awesome', BLOGCENTRAL_THEME_URL . '/font-awesome/css/font-awesome.min.css' );
			
			// Load the main stylesheet.
			wp_enqueue_style( 'blogcentral-admin', BLOGCENTRAL_THEME_URL . '/css/admin.css' );
			
			// Load the mailchimp stylesheet.
			wp_enqueue_style( 'blogcentral-mailchimp', "//cdn-images.mailchimp.com/embedcode/classic-081711.css" );
			
			$nonce = wp_create_nonce( "blogcentral-display-template-nonce" );
			
			// Font awesome variable.
			$fa = array( 'fa-glass','fa-music','fa-search','fa-envelope-o','fa-heart','fa-star','fa-star-o',
				'fa-user','fa-film','fa-th-large', 'fa-th', 'fa-th-list', 'fa-check', 'fa-times',
				'fa-search-plus', 'fa-search-minus', 'fa-power-off', 'fa-signal', 'fa-gear', 'fa-cog',
				'fa-trash-o', 'fa-home', 'fa-file-o', 'fa-clock-o', 'fa-road', 'fa-download',
				'fa-arrow-circle-o-down', 'fa-arrow-circle-o-up', 'fa-inbox', 'fa-play-circle-o',
				'fa-rotate-right', 'fa-repeat', 'fa-refresh', 'fa-list-alt', 'fa-lock', 'fa-flag',
				'fa-headphones', 'fa-volume-off', 'fa-volume-down', 'fa-volume-up', 'fa-qrcode', 'fa-barcode',
				'fa-tag', 'fa-tags', 'fa-book', 'fa-bookmark', 'fa-print', 'fa-camera',	'fa-font', 'fa-bold',
				'fa-italic', 'fa-text-height', 'fa-text-width', 'fa-align-left', 'fa-align-center',
				'fa-align-right', 'fa-align-justify', 'fa-list', 'fa-dedent', 'fa-outdent', 'fa-indent',
				'fa-video-camera', 'fa-picture-o', 'fa-pencil', 'fa-map-marker', 'fa-adjust', 'fa-tint', 'fa-edit',
				'fa-pencil-square-o', 'fa-share-square-o', 'fa-check-square-o', 'fa-arrows', 'fa-step-backward',
				'fa-fast-backward', 'fa-backward', 'fa-play', 'fa-pause', 'fa-stop', 'fa-forward',
				'fa-fast-forward', 'fa-step-forward', 'fa-eject', 'fa-chevron-left', 'fa-chevron-right',
				'fa-plus-circle', 'fa-minus-circle', 'fa-times-circle', 'fa-check-circle', 'fa-question-circle',
				'fa-info-circle', 'fa-crosshairs', 'fa-times-circle-o', 'fa-check-circle-o', 'fa-ban',
				'fa-arrow-left', 'fa-arrow-right', 'fa-arrow-up', 'fa-arrow-down', 'fa-mail-forward', 'fa-share',
				'fa-expand', 'fa-compress', 'fa-plus', 'fa-minus', 'fa-asterisk', 'fa-exclamation-circle',
				'fa-gift', 'fa-leaf', 'fa-fire', 'fa-eye', 'fa-eye-slash', 'fa-warning', 'fa-exclamation-triangle',
				'fa-plane', 'fa-calendar', 'fa-random', 'fa-comment', 'fa-magnet', 'fa-chevron-up',
				'fa-chevron-down', 'fa-retweet', 'fa-shopping-cart', 'fa-folder', 'fa-folder-open', 'fa-arrows-v',
				'fa-arrows-h', 'fa-bar-chart-o', 'fa-twitter-square', 'fa-facebook-square', 'fa-camera-retro',
				'fa-key', 'fa-gears', 'fa-cogs', 'fa-comments', 'fa-thumbs-o-up',' fa-thumbs-o-down',
				'fa-star-half', 'fa-heart-o', 'fa-sign-out', 'fa-linkedin-square', 'fa-thumb-tack',
				'fa-external-link', 'fa-sign-in', 'fa-trophy', 'fa-github-square', 'fa-upload', 'fa-lemon-o',
				'fa-phone', 'fa-square-o', 'fa-bookmark-o', 'fa-phone-square', 'fa-twitter', 'fa-facebook',
				'fa-github', 'fa-unlock', 'fa-credit-card', 'fa-rss', 'fa-hdd-o', 'fa-bullhorn', 'fa-bell',
				'fa-certificate', 'fa-hand-o-right', 'fa-hand-o-left', 'fa-hand-o-up', 'fa-hand-o-down',
				'fa-arrow-circle-left', 'fa-arrow-circle-right', 'fa-arrow-circle-up', 'fa-arrow-circle-down',
				'fa-globe', 'fa-wrench','fa-tasks', 'fa-filter', 'fa-briefcase', 'fa-arrows-alt', 'fa-group',
				'fa-users', 'fa-chain', 'fa-link', 'fa-cloud', 'fa-flask', 'fa-cut', 'fa-scissors', 'fa-copy',
				'fa-files-o', 'fa-paperclip', 'fa-save', 'fa-floppy-o', 'fa-square', 'fa-bars', 'fa-list-ul',
				'fa-list-ol', 'fa-strikethrough', 'fa-underline', 'fa-table', 'fa-magic', 'fa-truck',
				'fa-pinterest', 'fa-pinterest-square', 'fa-google-plus-square', 'fa-google-plus', 'fa-money',
				'fa-caret-down', 'fa-caret-up', 'fa-caret-left', 'fa-caret-right', 'fa-columns', 'fa-unsorted',
				'fa-sort', 'fa-sort-down', 'fa-sort-asc', 'fa-sort-up', 'fa-sort-desc', 'fa-envelope',
				'fa-linkedin', 'fa-rotate-left', 'fa-undo', 'fa-legal', 'fa-gavel', 'fa-dashboard',
				'fa-tachometer', 'fa-comment-o', 'fa-comments-o', 'fa-flash', 'fa-bolt', 'fa-sitemap',
				'fa-umbrella', 'fa-paste', 'fa-clipboard', 'fa-lightbulb-o', 'fa-exchange', 'fa-cloud-download',
				'fa-cloud-upload', 'fa-user-md', 'fa-stethoscope', 'fa-suitcase', 'fa-bell-o', 'fa-coffee',
				'fa-cutlery', 'fa-file-text-o', 'fa-building-o', 'fa-hospital-o', 'fa-ambulance', 'fa-medkit',
				'fa-fighter-jet', 'fa-beer', 'fa-h-square', 'fa-plus-square', 'fa-angle-double-left',
				'fa-angle-double-right', 'fa-angle-double-up', 'fa-angle-double-down', 'fa-angle-left',
				'fa-angle-right', 'fa-angle-up', 'fa-angle-down', 'fa-desktop', 'fa-laptop', 'fa-tablet',
				'fa-mobile-phone', 'fa-mobile', 'fa-circle-o', 'fa-quote-left', 'fa-quote-right',
				'fa-spinner', 'fa-circle', 'fa-mail-reply', 'fa-reply', 'fa-github-alt', 'fa-folder-o',
				'fa-folder-open-o', 'fa-smile-o', 'fa-frown-o', 'fa-meh-o', 'fa-gamepad', 'fa-keyboard-o',
				'fa-flag-o', 'fa-flag-checkered', 'fa-terminal', 'fa-code', 'fa-reply-all', 'fa-mail-reply-all',
				'fa-star-half-empty', 'fa-star-half-full', 'fa-star-half-o', 'fa-location-arrow', 'fa-crop',
				'fa-code-fork', 'fa-unlink', 'fa-chain-broken', 'fa-question', 'fa-info', 'fa-exclamation',
				'fa-superscript', 'fa-subscript', 'fa-eraser', 'fa-puzzle-piece', 'fa-microphone',
				'fa-microphone-slash', 'fa-shield', 'fa-calendar-o', 'fa-fire-extinguisher', 'fa-rocket',
				'fa-maxcdn', 'fa-chevron-circle-left', 'fa-chevron-circle-right', 'fa-chevron-circle-up',
				'fa-chevron-circle-down', 'fa-html5', 'fa-css3', 'fa-anchor', 'fa-unlock-alt', 'fa-bullseye',
				'fa-ellipsis-h', 'fa-ellipsis-v', 'fa-rss-square', 'fa-play-circle', 'fa-ticket', 'fa-minus-square',
				'fa-minus-square-o', 'fa-level-up', 'fa-level-down', 'fa-check-square', 'fa-pencil-square',
				'fa-external-link-square', 'fa-share-square', 'fa-compass', 'fa-toggle-down',
				'fa-caret-square-o-down', 'fa-toggle-up', 'fa-caret-square-o-up', 'fa-toggle-right',
				'fa-caret-square-o-right', 'fa-euro', 'fa-eur', 'fa-gbp', 'fa-dollar', 'fa-usd', 'fa-rupee',
				'fa-inr', 'fa-cny', 'fa-rmb', 'fa-yen', 'fa-jpy', 'fa-ruble', 'fa-rouble', 'fa-rub', 'fa-won',
				'fa-krw', 'fa-bitcoin', 'fa-btc', 'fa-file', 'fa-file-text', 'fa-sort-alpha-asc',
				'fa-sort-alpha-desc', 'fa-sort-amount-asc', 'fa-sort-amount-desc', 'fa-sort-numeric-asc',
				'fa-sort-numeric-desc', 'fa-thumbs-up', 'fa-thumbs-down', 'fa-youtube-square', 'fa-youtube',
				'fa-xing', 'fa-xing-square', 'fa-youtube-play', 'fa-dropbox', 'fa-stack-overflow',
				'fa-instagram', 'fa-flickr', 'fa-adn', 'fa-bitbucket', 'fa-bitbucket-square', 'fa-tumblr',
				'fa-tumblr-square', 'fa-long-arrow-down', 'fa-long-arrow-up', 'fa-long-arrow-left',
				'fa-long-arrow-right', 'fa-apple', 'fa-windows', 'fa-android', 'fa-linux', 'fa-dribbble',
				'fa-skype', 'fa-foursquare', 'fa-trello', 'fa-female', 'fa-male', 'fa-gittip', 'fa-sun-o',
				'fa-moon-o', 'fa-archive', 'fa-bug', 'fa-vk', 'fa-weibo', 'fa-renren', 'fa-pagelines',
				'fa-stack-exchange', 'fa-arrow-circle-o-right', 'fa-arrow-circle-o-left', 'fa-toggle-left',
				'fa-caret-square-o-left', 'fa-dot-circle-o', 'fa-wheelchair', 'fa-vimeo-square', 'fa-turkish-lira',
				'fa-try', 'fa-plus-square-o', 'fa-area-chart', 'fa-at', 'fa-angellist', 'fa-bell-slash', 'fa-bell-slash-o',
				'fa-bicycle', 'fa-binoculars', 'fa-birthday-cake', 'fa-bus', 'fa-calculator', 'fa-cc', 'fa-cc-amex',
				'fa-cc-discover', 'fa-cc-mastercard', 'fa-cc-paypal', 'fa-cc-stripe', 'fa-cc-visa', 'fa-copyright',
				'fa-eyedropper', 'fa-futbol-o', 'fa-google-wallet', 'fa-ils', 'fa-ioxhost', 'fa-lastfm',
				'fa-lastfm-square', 'fa-line-chart', 'fa-meanpath', 'fa-newspaper-o', 'fa-paint-brush', 'fa-paypal',
				'fa-pie-chart', 'fa-plug', 'fa-shekel', 'fa-sheqel', 'fa-slideshare', 'fa-soccer-ball-o',
				'fa-toggle-off', 'fa-toggle-on', 'fa-trash', 'fa-tty', 'fa-twitch', 'fa-wifi', 'fa-yelp',
				'fa-bed', 'fa-buysellads', 'fa-cart-arrow-down', 'fa-cart-plus', 'fa-connectdevelop', 'fa-dashcube',
				'fa-diamond', 'fa-facebook-official', 'fa-forumbee', 'fa-heartbeat', 'fa-hotel', 'fa-leanpub',
				'fa-mars', 'fa-mars-double', 'fa-mars-stroke', 'fa-mars-stroke-h', 'fa-mars-stroke-v', 'fa-medium',
				'fa-mercury', 'fa-motorcycle', 'fa-neuter', 'fa-pinterest-p', 'fa-sellsy', 'fa-server', 'fa-ship',
				'fa-shirtsinbulk', 'fa-simplybuilt', 'fa-skyatlas', 'fa-street-view', 'fa-subway', 'fa-train',
				'fa-transgender', 'fa-transgender-alt', 'fa-user-plus', 'fa-user-secret', 'fa-user-times', 'fa-venus',
				'fa-venus-double', 'fa-venus-mars', 'fa-viacoin', 'fa-whatsapp',
			);
				
			// Localize admin script.
			wp_localize_script( 'blogcentral-admin-script', 'blogcentral_object', 
				array(
					'close'					=>	__( 'close', BLOGCENTRAL_TXT_DOMAIN ),
					'Delete'				=>	__( 'Delete', BLOGCENTRAL_TXT_DOMAIN ),
					'name'					=>	__( 'name', BLOGCENTRAL_TXT_DOMAIN ),
					'choose_image'			=>	__( 'Choose Image', BLOGCENTRAL_TXT_DOMAIN ),
					'enter_slide'			=>	__( 'Enter the html for the slide', BLOGCENTRAL_TXT_DOMAIN ),
					'slide_template_instr'	=>	__( 'Slide Template- choose a template to use for your slide, or enter custom html below.', BLOGCENTRAL_TXT_DOMAIN ),
					'slide_template_1'		=>	__( 'template 1', BLOGCENTRAL_TXT_DOMAIN ),
					'slide_template_2'		=>	__( 'template 2', BLOGCENTRAL_TXT_DOMAIN ),
					'slide_template_3'		=>	__( 'template 3', BLOGCENTRAL_TXT_DOMAIN ),
					'admin_template_ajax'	=>	admin_url( 'admin-ajax.php?action=blogcentral_display_template_ajax&amp;nonce=' . $nonce ),
					'fa_items'				=>	$fa,					
				)
			);
		}
	}
}

/**
 * 5.2 Add Theme Options Page + Help Tabs
 *-----------------------------------------------------------------------*/

if ( ! function_exists( 'blogcentral_theme_options_add_page' ) ) {
	/**
	 * Add the theme's options page to the wordpress administrative menu
	 *
	 * @since 1.0.0
	 */
	function blogcentral_theme_options_add_page() {
		$options_page = add_theme_page( 
			__( 'BlogCentral', BLOGCENTRAL_TXT_DOMAIN ), 
			__( 'BlogCentral', BLOGCENTRAL_TXT_DOMAIN ),
			'edit_theme_options',
			BLOGCENTRAL_PAGE_SLUG,
			'blogcentral_front_controller' );
		
		if ( ! $options_page ) {
			return;
		}
		
		// Add my_help_tab when theme options page loads.
		add_action( 'load-'.$options_page, 'blogcentral_add_help_tab' );
	}
}


if ( ! function_exists( 'blogcentral_add_help_tab' ) ) {
	/**
	 * Add help tabs on theme's options page
	 *
	 * @since 1.0.0
	 */
	function blogcentral_add_help_tab() {
		$screen = get_current_screen();

		// Add help tab for each blogcentral options tab, if deemed necessary.
		$screen->add_help_tab( array(
			'id'		=>	'blogcentral-help-general-tab',
			'title'		=> 	__( 'General', BLOGCENTRAL_TXT_DOMAIN ),
			'content'	=> 	blogcentral_construct_general_help_tab(),
		) );	
		$screen->add_help_tab( array(
			'id'		=>	'blogcentral-help-colors-api-tab',
			'title'		=> 	__( 'General Background, Color, & Sizes', BLOGCENTRAL_TXT_DOMAIN ),
			'content'	=> 	blogcentral_construct_colors_help_tab(),
		) );
		$screen->add_help_tab( array(
			'id'		=>	'blogcentral-help-typography-tab',
			'title'		=> 	__( 'Typography', BLOGCENTRAL_TXT_DOMAIN ),
			'content'	=> 	blogcentral_construct_typography_help_tab(),
		) );
		$screen->add_help_tab( array(
			'id'		=>	'blogcentral-help-social-api-tab',
			'title'		=> 	__( 'Social Apps', BLOGCENTRAL_TXT_DOMAIN ),
			'content'	=> 	blogcentral_construct_social_api_help_tab(),
		) );
		$screen->add_help_tab( array(
			'id'		=>	'blogcentral-help-contact-tab',
			'title'		=> 	__( 'Contact Information', BLOGCENTRAL_TXT_DOMAIN ),
			'content'	=> 	blogcentral_construct_contact_help_tab(),
		) );
		$screen->add_help_tab( array(
			'id'		=>	'blogcentral-help-header-tab',
			'title'		=> 	__( 'Header', BLOGCENTRAL_TXT_DOMAIN ),
			'content'	=> 	blogcentral_construct_header_help_tab(),
		) );
		$screen->add_help_tab( array(
			'id'		=>	'blogcentral-help-nav-menu-tab',
			'title'		=> 	__( 'Main Navigation Menu', BLOGCENTRAL_TXT_DOMAIN ),
			'content'	=> 	blogcentral_construct_nav_menu_help_tab(),
		) );
		$screen->add_help_tab( array(
			'id'		=>	'blogcentral-help-page-header-tab',
			'title'		=> 	__( 'Page Header', BLOGCENTRAL_TXT_DOMAIN ),
			'content'	=> 	blogcentral_construct_page_header_help_tab(),
		) );
		$screen->add_help_tab( array(
			'id'		=>	'blogcentral-help-posts-tab',
			'title'		=> 	__( 'Posts', BLOGCENTRAL_TXT_DOMAIN ),
			'content'	=> 	blogcentral_construct_posts_help_tab(),
		) );	
		
	}
}

if ( ! function_exists( 'blogcentral_construct_general_help_tab' ) ) {
	/**
	 * Construct help panel for the general tab
	 *
	 * @since 1.0.0
	 *
	 * @return string help instructions.
	 */
	function blogcentral_construct_general_help_tab() {
		$html = '<h4>' . __( 'First we would like to say Thank You for downloading the BlogCentral Theme. The following provides some information about using the theme options page. For more detailed 
			information, consult the readme.txt file and the theme\'s online documentation at ', BLOGCENTRAL_TXT_DOMAIN ) . ' <a href="' . BLOGCENTRAL_THEME_DOCS . '" target="_blank">' . __( 'Documentation on ', BLOGCENTRAL_TXT_DOMAIN ) . BLOGCENTRAL_THEME_NAME . '</a>.' .
			'</h4> <p class="section-title div3">' . __( 'Demos', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'BlogCentral provides 3 predefined demos to choose. The demos define values for some of the theme options. Choosing a demo should be the first step, since it will delete all of your saved options. When you select the demo you would like to use, immediately click on the "Change Demo" button, this will save a copy of the demo options as the theme options. You can further customize the options, if desired, by using the theme options page. The demo settings, just like the theme options, if applicable, will apply to all pages.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>' .
			'<p class="section-title div3">' . __( 'Making your site look like the demos', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'Your site will almost look exactly like the demos except for maybe, your logo, posts, and widgets. You can download an xml file for the demo content, which will include the posts. For the widgets, you will have to download the widgets.json file, and have the Widget Import/Export plugin installed and activated. For more detailed information <a href="http://4bzthemes.com/support/articles/blogcentral-installing-the-demo-content/"> read this tutorial.</a>', BLOGCENTRAL_TXT_DOMAIN ) . '</p>' .
			'<p class="section-title div3">' . __( 'Front End Demo Pages', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'BlogCentral provides page templates used to demonstrate the demos. You have the choice to use them or not, but if you do, please read the following. Whenever a user chooses a demo to view on the front end, its options will be the active theme options, until the home page, or another demo is chosen on the front end, for example from the main navigation menu. If you change the theme options on the admin side, and do not see these changes on the front end, you will have to navigate to the home page, effectively changing the options from the previously viewed demo, to the options saved from the theme options page.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>';
		
		return $html;
	}
}

if ( ! function_exists( 'blogcentral_construct_contact_help_tab' ) ) {
	/**
	 * Construct help panel for the contact tab
	 *
	 * @since 1.0.0
	 *
	 * @return string help instructions.
	 */
	function blogcentral_construct_contact_help_tab() {
		$html = '<p class="section-title div3">' . __( '4bzCore Plugin', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'This theme uses the 4bzCore plugin to display contact information and/or a contact form. It is available for download from the WordPress.org repository. <a href="http://wordpress.org/plugins/4bzcore">Download Now</a>', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p class="section-title div3">' . __( 'Icons', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'To choose an icon, click inside the input textbox.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p class="section-title div3">' . __( 'Contact Form', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'This theme is compatible with any plugin that displays contact forms via a shortcode. If you would like to display a contact form, enter the shortcode generated by the plugin in the textarea.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>';
		
		return $html;
	}
}

if ( ! function_exists( 'blogcentral_construct_typography_help_tab' ) ) {
	/**
	 * Construct help panel for the typography tab
	 *
	 * @since 1.0.0
	 *
	 * @return string help instructions.
	 */
	function blogcentral_construct_typography_help_tab() {
		$html = '<p>' . __( 'You can customize the font family, weight and style, and subsets for the body text, headers, and menu items. Choose from traditional system fonts and google fonts.', BLOGCENTRAL_TXT_DOMAIN ) .
			'</p>';
			
		return $html;
	}
}

if ( ! function_exists( 'blogcentral_construct_social_api_help_tab' ) ) {
	/**
	 * Construct help panel for the social api tab
	 *
	 * @since 1.0.0
	 *
	 * @return string help instructions.
	 */
	function blogcentral_construct_social_api_help_tab() {
		$html = '<p class="section-title div3">' . __( 'Third Party APIs', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'If you would like to use google maps and fonts, you must provide your API key. Visit their website to obtain this information.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>';
			
		return $html;
	}
}

if ( ! function_exists( 'blogcentral_construct_colors_help_tab' ) ) {
	/**
	 * Construct help panel for the general tab
	 *
	 * @since 1.0.0
	 *
	 * @return string help instructions.
	 */
	function blogcentral_construct_colors_help_tab() {
		$html = '<p>' . __( 'The "General Background, Colors, & Sizes" tab is where you can choose a color scheme, customize the color for the body text, links, and headers, the background color for the main content, and the size of the header tags.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p class="section-title div3">' . __( 'Choosing Colors', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'BlogCentral uses the native Wordpress color picker to choose a color. You can also enter the color manually. Examples: #ffffff, rgb(255,255,255) or white.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p class="section-title div3">' . __( 'Background Images', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'To choose a background image or color for the body, navigate to "Appearance->Background" on the admin sidebar', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p class="section-title div3">' . __( 'Font sizes, padding, margins, widths and heights.', BLOGCENTRAL_TXT_DOMAIN ) . '</p><p>' . __( 'Enter font sizes, padding, margins, widths, and heights with unit of measurement, eg. 12px, 1.2rem etc. For dimensions of an image, do not include a unit of measurement.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>';
			
		return $html;
	}
}

if ( ! function_exists( 'blogcentral_construct_header_help_tab' ) ) {
	/**
	 * Construct help panel for the header tab
	 *
	 * @since 1.0.0
	 *
	 * @return string help instructions.
	 */
	function blogcentral_construct_header_help_tab() {
		$html = '<p>' . __( 'The "Header" tab is where you customize the appearance of the header. The header is the top area of the page that holds the logo, header widget, and the main navigation menu. The favicon option is also under this tab.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p class="section-title div3">' . __( 'Contact Information Display', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'BlogCentral provides options to display the contact information for a company or an individual. If you would like to display the contact information, the 4bzCore plugin must be installed and activated. If the option for a user id is blank, then the contact information entered on the contact information tab on the theme\'s options page will be used, otherwise the contact information for the user of the provided user id will be displayed. For social media contacts, enter the full url, ie. http://www.facebook.com/username.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>';
				
		return $html;
	}
}

if ( ! function_exists( 'blogcentral_construct_nav_menu_help_tab' ) ) {
	/**
	 * Construct help panel for the header tab
	 *
	 * @since 1.0.0
	 *
	 * @return string help instructions.
	 */
	function blogcentral_construct_nav_menu_help_tab() {
		$html = '<p>' . __( 'The "Main Navigation Menu" tab is where you customize the appearance of the main navigation menu and its submenus. You can customize the color for the background, links, and active links.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>';
		
		return $html;
	}
}

if ( ! function_exists( 'blogcentral_construct_page_header_help_tab' ) ) {
	/**
	 * Construct help panel for the page header tab
	 *
	 * @since 1.0.0
	 *
	 * @return string help instructions.
	 */
	function blogcentral_construct_page_header_help_tab() {
		$html = '<p>' . __( 'The "Page Header" tab is where you customize the appearance of the page header. The page header sits immediately below the header. This area is widgetized and is also called the precontent widget. If no widgets are active, then it serves as the page header and a box with an image and an overlay that shows what page is being viewed, the blog description, today\'s date, and the breadcrumbs will be shown, if enabled.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p class="section-title div3">' . __( 'Height', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'You can define a height for the page header, enter it with the unit of measurement, ex. 150px.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p class="section-title div3">' . __( 'Page Header Layouts', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'BlogCentral provides 3 page header layouts.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>';
				
		return $html;
	}
}

if ( ! function_exists( 'blogcentral_construct_posts_help_tab' ) ) {
	/**
	 * Construct help panel for the posts tab
	 *
	 * @since 1.0.0
	 *
	 * @return string help instructions.
	 */
	function blogcentral_construct_posts_help_tab() {
		$html = '<p class="section-title div3">' . __( 'Posts Listing Pages', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'These options will affect the appearance of the posts listing in the main area on all pages that display a posts listing in a div with id #main-area, eg. index, archive, author, search, category and tags pages.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p class="section-title div3">' . __( 'Single Post Page', BLOGCENTRAL_TXT_DOMAIN ) . '</p>
			<p>' . __( 'BlogCentral offers the option to use the same settings of the posts listing page for the single post page.  If you choose to override the settings, then none of them will be used for the single post page.', BLOGCENTRAL_TXT_DOMAIN ) . '</p>';
			
		return $html;
	}
}

/**
 * 5.3 Controller + Save Theme Settings
 *-----------------------------------------------------------------------*/
if ( ! function_exists( 'blogcentral_front_controller' ) ) {
	/**
	 * Route url requests to the appropriate function.
	 *
	 * @since 1.0.0
	 */
	function blogcentral_front_controller() {
		$action = isset( $_GET['action'] ) ? $_GET['action'] : ( isset( $_POST['action'] ) ? $_POST['action'] : '' );
		
		if ( isset( $_GET['page'] ) && 'blogcentral_options' === $_GET['page'] ) {
			switch ( $action ) {
				case 'blogcentral_options':
				default:			
					blogcentral_theme_options();						
			}
		}// End if
	}
}

if ( ! function_exists( 'blogcentral_validate_options' ) ) {
	/**
	 * Perform security checks on theme options
	 *
	 * Construct the google fonts url for any google font option selected.
	 *
	 * @since 1.0.0
	 */
	function blogcentral_validate_options() {

		// Check if the 'Change Demo' button was clicked.
		if ( isset( $_POST['blogcentral_change_blog_demo'] ) ) {
			$options = blogcentral_change_blog_demo();
			
			if ( ! empty( $options ) ) {
				return $options;
			}
		}
		
		global $blogcentral_opts;
		global $blogcentral_defaults;
		
		$check_text = array( "default_user_img_alt", 'contact_address', "contact_phone", "address_icon", "phone_icon",
			"url_icon", "email_icon", "facebook_icon", "twitter_icon", "google_icon", "linkedin_icon", "instagram_icon",
			"tumblr_icon", "pinterest_icon", "facebook_app_id", "google_app_id", "logo_alt", "img_size", "standard_icon",
			"image_icon", "audio_icon", "video_icon", "gallery_icon", "link_icon", "quote_icon", "author_icon",
			"date_icon", "categories_icon", "tags_icon", "comments_icon",
		);
		
		$check_int = array( "color_scheme", "page_header_bck_img_width", "page_header_bck_img_height", "default_user_img_width", "default_user_img_height", "user_id",
		);
		
		$check_url = array( 'logo', 'fav_icon', 'contact_url', 'contact_facebook', 'contact_twitter', 'contact_google', 'contact_linkedin', 'contact_instagram', 'contact_tumblr', 'contact_pinterest',
		);
		
		$check_email = array( 'contact_email', );
		$check_html = array( 'copyright', );  
		$check_html_limited = array( 'title_text', 'tagline_text', "share_title", );
		$check_html_class = array( "wrap_class", "title_class", );
			
		// Only allow these tags with these attributes.
		$kses_allow = array(
			'a'		=> array(
				'href'	=> array(),
				'title'	=> array(),
			),
			'br' 	=> array(),
			'span'	=> array(
				'id'	=>	array(),
				'class'	=>	array(),
			),
		);
		
		// Do server-side validations
		$excpts = false;
		
		$options = array();
		
		if ( ! is_array( $_POST ) ) {
			// Error do not save.
			add_settings_error(
				'Post-not-array',
				'not-array',
				__( 'Error: The post variable is corrupted. Please contact the administrator.', BLOGCENTRAL_TXT_DOMAIN ),
				'error'
			);
			$excpts = true;
		} else {
			foreach ( $_POST['blogcentral'] as $post => $vpost ) {
				// First check if it is a valid theme option.
				if ( ! array_key_exists( $post, $blogcentral_defaults ) ) {
					continue;
				}
				
				if ( is_array( $vpost ) ) {
					$flag = false;
					$temp = array();
					
					foreach (  $vpost as $key => $val ) {
						if ( '' !== $val ) {
							// Do security checks
							if ( in_array( $val, $check_text ) ) {
								$val = sanitize_text_field( $val );
							} elseif ( in_array( $val, $check_url ) ) {
								$val = esc_url_raw( $val );
							} elseif ( in_array( $val, $check_email ) ) {
								$val = sanitize_email( $val );
							} elseif ( in_array( $val, $check_int ) ) {
								$val = absint( $val );
							} elseif ( in_array( $val, $check_html ) ) {
								$val = wp_kses_post( $val );
							} elseif ( in_array( $val, $check_html_limited ) ) {
								$val = wp_kses( $val, $kses_allow );
							} elseif ( in_array( $val, $check_html_class ) ) {
								$val = sanitize_html_class( $val );
							}
							
							$flag = true;
							$temp[$key] = $val;
						}
					}
					
					if ( $flag ) {
						$options[$post] = array();
						$options[$post] = $temp;
					}
				} else {
					if ( '' !== $vpost ) {
						// Do security checks
						if ( in_array( $vpost, $check_text ) ) {
							$vpost = sanitize_text_field( $vpost );
						} elseif ( in_array( $vpost, $check_url ) ) {
							$vpost = esc_url_raw( $vpost );
						} elseif ( in_array( $vpost, $check_email ) ) {
							$vpost = sanitize_email( $vpost );
						} elseif ( in_array( $vpost, $check_int ) ) {
							$vpost = absint( $vpost );
						} elseif ( in_array( $vpost, $check_html ) ) {
							$vpost = wp_kses_post( $vpost );
						} elseif ( in_array( $vpost, $check_html_limited ) ) {
							$vpost = wp_kses( $vpost, $kses_allow );
						} elseif ( in_array( $vpost, $check_html_class ) ) {
							$vpost = sanitize_html_class( $vpost );
						}
						
						$options[$post] = $vpost;
					}
				}
			}
		
			// Construct google fonts url to save to the database.
			$fonts = blogcentral_construct_google_fonts_url();
			$options['google_fonts_url'] = $fonts;
		
			// Update global variable with new options
			$blogcentral_opts = $options;
		}
		
		return $options;
	}
}

if ( ! function_exists( 'blogcentral_admin_notices' ) ) {
	/**
	 * Output any admin notices
	 *
	 * @since 1.0.0
	 */
	function blogcentral_admin_notices() {
		settings_errors();
	}
}

if ( ! function_exists( 'blogcentral_change_blog_demo' ) ) {
	/**
	 * Change the demo.
	 *
	 * @since 1.0.0
	 *
	 */
	function blogcentral_change_blog_demo() {
		// Check nonce.	
		$nonce = $_REQUEST['blogcentral_demo_nonce'];
		
		if ( ! wp_verify_nonce( $nonce, 'blogcentral-change-demo' ) ) {
			// This nonce is not valid.
			die( 'No Swiping' ); 
		} else {
			global $blogcentral_opts;
			global $blogcentral_blog_demos_opts;
			
			$template = isset( $_POST['blogcentral']['blog_demo'] ) ? $_POST['blogcentral']['blog_demo'] : '';
			
			if ( empty( $template ) && '0' !== $template ) {
				// Error
				add_settings_error(
					'demo-empty',
					'demo-empty',
					__( 'Error: Please select a demo.', BLOGCENTRAL_TXT_DOMAIN ),
					'error'
				);
				return;
			}
			
			// Update global variable with new options
			$blogcentral_opts = $blogcentral_blog_demos_opts[ $template - 1 ];
				
			return $blogcentral_blog_demos_opts[ $template - 1 ];
		}
	}
}

/**
 * 5.4 Theme Options
 *-----------------------------------------------------------------------*/

/**
 * 5.4.1 Main
 *-----------------------------------------------------------------------*/

if ( ! function_exists( 'blogcentral_get_field_name_wrap' ) ) {
	/**
	 * Construct name of input field for use on the theme options page, and if the 4bzCore plugin is installed, on the shortcode, and widget options pages.
	 *
	 * A wrapper used to name a field regularly or, if displaying options for a widget, use the widgets'
	 * get_field_name method.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Required. Name of input field.
	 * @param string $name_attr_pre Required. Name prefix of input field.
	 * @param object $widget Optional. Widget object to display options for.
	 * @return string. Modified name of input field.
	 */
	function blogcentral_get_field_name_wrap( $name, $name_attr_pre, & $widget = false ) {
		if ( empty( $name ) || empty( $name_attr_pre ) ) {
			return;
		} else {
			$frag = "[$name]";
		}
		
		return ( $widget ? $widget->get_field_name( $name ) : $name_attr_pre . $frag );
	}
}

if ( ! function_exists( 'blogcentral_get_field_id_wrap' ) ) {
	/**
	 * Construct id for use on the theme options page, and if the 4bzCore plugin is installed, on the shortcode, and widget options pages.
	 *
	 * A wrapper used to create an id. if displaying options for a widget, uses the widgets'
	 * get_field_id method.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Required. Id of input field.
	 * @param string $name_attr_pre Optional. Prefix for name attribute.
	 * @param object $widget Optional. Widget object if displaying options for a widget.
	 * @return string. Modified name of input field.
	 */
	function blogcentral_get_field_id_wrap( $id, $name_attr_pre, & $widget = false ) {
		if ( empty( $name_attr_pre ) ) {
			return;
		}
		return ( $widget ? $widget->get_field_id( $id ) : $name_attr_pre . '-' . $id );
	}
}

if ( ! function_exists( 'blogcentral_register_theme_options' ) ) {
	/**
	 * Register theme options with the settings api
	 *
	 * @since 1.0.0
	 */
	function blogcentral_register_theme_options() {
		register_setting( 'blogcentral', BLOGCENTRAL_DB_OPTIONS_NAME, 'blogcentral_validate_options' );
	}
}

if ( ! function_exists( 'blogcentral_theme_options' ) ) {
	/**
	 * Display the theme options page.
	 *
	 * @since 1.0.0
	 */
	function blogcentral_theme_options() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', BLOGCENTRAL_TXT_DOMAIN ) );
		}
		
		// Get the saved settings.
		global $blogcentral_opts;
		global $blogcentral_defaults;
		global $blogcentral_initial;
		
		// Global plugin object.
		global $fourbzcore_plugin;
		
		$saved = $blogcentral_opts;

		if ( ! empty( $saved ) ) {
			$saved = array_map( 'stripslashes_deep', $saved );
			foreach ( $saved as $key => $val ) {
				if ( is_array( $val ) ) {
					$saved[$key] =  wp_parse_args( $saved[$key], $blogcentral_defaults[$key] );
				}
			}
			$saved = wp_parse_args( $saved, $blogcentral_defaults );
		} else {
			$saved = $blogcentral_initial;
		}
		
		$show_contact =  $saved['show_contact'] ? true : false;
		$widget_border = $saved['widget_border'] ? $saved['widget_border'] : '';
		
		?>
		<br />
		<br />
		<div class="theme-options blogcentral-wrap text-format">
			<div id="theme-options-header">
				<div class="options-logo"> 
					<?php _e( 'BlogCentral', BLOGCENTRAL_TXT_DOMAIN ); ?> <span class="version">
						<small><?php _e( 'Version: 1.1.3', BLOGCENTRAL_TXT_DOMAIN ); ?></small>
					</span>
				</div>
				<strong><?php _e( 'For tutorials, and more visit', BLOGCENTRAL_TXT_DOMAIN ); ?>
					<a href="http://4bzthemes.com"><?php _e( 'Our Website', BLOGCENTRAL_TXT_DOMAIN ); ?></a>
				</strong>
				<br /><br />
				<p class="pro-version" style="font-size:22px;">
					<strong><a href="http://4bzthemes.com/theme/blogcentralpro"><?php _e( 'BlogCentral Pro- upgrade now!', BLOGCENTRAL_TXT_DOMAIN ); ?></a></strong>
				</p>
			</div>
			<form method="post" action="options.php" enctype="multipart/form-data" id="blogcentral_options_form">
				<?php 
					// Output hidden fields created by the Settings api.
					settings_fields( 'blogcentral' );
					$nonce = wp_create_nonce( 'blogcentral-change-demo' );
					
					// Nonce used when switching the demo.
					echo '<input type="hidden" name="blogcentral_demo_nonce" value="' . $nonce . '" />';
				?>
				<div class="blogcentral_err_msgs"></div>
				<div class="section">
					<div class="tabs-layout2">
						<ul>
							<li><a class="section-title" href="#blogcentral-opts"><i class="fa fa-wrench"></i>
								<?php _e( 'General', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>
							<li><a class="section-title" href="#blogcentral-background-colors"><i class="fa fa-list"></i>
								<?php _e( 'General Background, Colors, & Sizes', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>	
							<li><a class="section-title" href="#blogcentral-fonts"><i class="fa fa-font"></i>
								<?php _e( 'Typography', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>						
							<li><a class="section-title" href="#blogcentral-social"><i class="fa fa-thumbs-up"></i>
								<?php _e( 'Social Apps', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>
							<li><a class="section-title" href="#blogcentral-contact-info"><i class="fa fa-list"></i>
								<?php _e( 'Contact Information', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>						
							<li><a class="section-title" href="#blogcentral-header"><i class="fa fa-list"></i>
								<?php _e( 'Header', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>	
							<li><a class="section-title" href="#blogcentral-main-menu"><i class="fa fa-list"></i>
								<?php _e( 'Main Navigation Menu', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>
							<li><a class="section-title" href="#blogcentral-page-header"><i class="fa fa-list"></i>
								<?php _e( 'Page Header', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>
							<li><a class="section-title" href="#blogcentral-posts"><i class="fa fa-list-alt"></i>
								<?php _e( 'Posts', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>
							<li><a class="section-title" href="#blogcentral-footer-cont"><i class="fa fa-list"></i>
								<?php _e( 'Footer', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>
							<li><a class="section-title" href="#blogcentral-custom-css"><i class="fa fa-css3"></i>
								<?php _e( 'Custom CSS', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>
						</ul>
						<div id="blogcentral-opts" class="tabs-panel">
							<h4><?php _e( 'General Settings', BLOGCENTRAL_TXT_DOMAIN ); ?></h4>
							<p><?php _e( 'Customize these options to fit your needs, or leave blank to use the default value.', BLOGCENTRAL_TXT_DOMAIN ); ?></p>
							<p class='section-title div3'><?php
								_e( 'Demos - choose from 3 different demos. <span class="warning">Warning:</span> Choosing a demo will delete all of your saved options. If this is not desired, backup your options before changing the demo. You have to click on the "Change Demo" button to change the demo.', BLOGCENTRAL_TXT_DOMAIN );
								?>
							</p>
							<table class="form-table">
								<tbody>
									<tr>
										<th> <?php _e( 'Demos', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<div class="group">
												<input type="radio" name="blogcentral[blog_demo]" value="0"<?php checked( $saved['blog_demo'], '0' ); ?> /> 
												<label><?php _e( "Do not use a demo.", BLOGCENTRAL_TXT_DOMAIN ); ?></label><br /><br />
												<input type="radio" name="blogcentral[blog_demo]" value="1"<?php checked( $saved['blog_demo'], '1' ); ?> /> 
												<label><?php _e( "Demo 1", BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
												<img src='<?php echo BLOGCENTRAL_THEME_URL; ?>/images/preview/blog-demo1.jpg' alt='blog demo 1' /><br /><br />
												<input type="radio" name="blogcentral[blog_demo]" value="2"<?php checked( $saved['blog_demo'], '2' ); ?> /> 
												<label><?php _e( "Demo 2", BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
												<img src='<?php echo BLOGCENTRAL_THEME_URL; ?>/images/preview/blog-demo2.jpg' alt='blog demo 2' /><br /><br />
												<input type="radio" name="blogcentral[blog_demo]" value="3"<?php checked( $saved['blog_demo'], '3' ); ?> /> 
												<label><?php _e( "Demo 3", BLOGCENTRAL_TXT_DOMAIN ); ?></label><br /><br />
												<img src='<?php echo BLOGCENTRAL_THEME_URL; ?>/images/preview/blog-demo3.jpg' alt='blog demo 3' /><br /><br />
												<input type="submit" class="button" name="blogcentral_change_blog_demo" value="Change Demo" />
											</div>
										</td>
									</tr>
								</tbody>
							</table>						
							<p class='section-title div3'><?php _e( 'Box Display', BLOGCENTRAL_TXT_DOMAIN ); ?></p> 
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php _e( 'Enable block display', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<input type="checkbox" name="blogcentral[block_display]"<?php checked( $saved['block_display'], 'on' ); ?> />
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Enable box display', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<input type="checkbox" name="blogcentral[box_display]"<?php checked( $saved['box_display'], 'on' ); ?> />
											<p class="instruction fixed"><small>
											<?php echo __( 'Box display adds a box shadow, and top and bottom margin to the main-div. Block display has to be enabled.', BLOGCENTRAL_TXT_DOMAIN ); ?></small></p>
										</td>
									</tr> 
								</tbody>
							</table>
							<p class='section-title div3'><?php
								_e( 'Default title border', BLOGCENTRAL_TXT_DOMAIN );
							?></p>
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php _e( 'Shortcodes & Widgets border', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<select id='widget-border-select' class='header-border-select' name='blogcentral[widget_border]'>
											<?php 
												$border_opts = blogcentral_display_border_items_select( $widget_border ); 
												echo $border_opts; 
											?>
											</select>
											<?php 
											if ( '0' !== $widget_border ) :
												echo "<span class='border-ex " . esc_attr( $widget_border ) . "'>" . esc_html( $widget_border ) . "</span>";
											else :
												echo "<span class='border-ex'></span>";
											endif;
											?>
											<p class='instruction fixed'><small>
											<?php echo __( 'Default border style for all widget and shortcode titles. Can be changed per shortcode or widget provided by the 4bzCore plugin.', BLOGCENTRAL_TXT_DOMAIN ); ?>
											</small></p>
										</td>
									</tr>
								</tbody>
							</table>
							<p class='section-title div3'><?php _e( 'Show Sidebar', BLOGCENTRAL_TXT_DOMAIN ); ?></p> 
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php _e( 'Enable sidebar', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<input type="checkbox" name="blogcentral[show_sidebar]"<?php checked( $saved['show_sidebar'], 'on' ); ?> />
										</td>
									</tr> 
								</tbody>
							</table>
							<p class='section-title div3'><?php _e( 'User Avatar', BLOGCENTRAL_TXT_DOMAIN ); ?></p> 
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php _e( 'Default image for users', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<?php blogcentral_construct_upload_image( $saved, 'default_user_img', false ); ?>
										</td>
									</tr>
								</tbody>
							</table>
							<p class='section-title div3'><?php _e( 'Color Chooser', BLOGCENTRAL_TXT_DOMAIN ); ?></p> 
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php _e( 'Display the color chooser', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<input type="checkbox" name="blogcentral[show_color_chooser]"<?php checked( $saved['show_color_chooser'], 'on' ); ?> />
										</td>
									</tr>
								</tbody>
							</table>
						</div><!-- End #blogcentral-opts -->
							<div id="blogcentral-background-colors" class="tabs-panel">
							<p class='section-title div3'><?php _e( 'Color Scheme', BLOGCENTRAL_TXT_DOMAIN ); ?></p> 
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php _e( 'Color scheme', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<input type="radio" name="blogcentral[color_scheme]" value="0"<?php checked( $saved["color_scheme"], '0' ); ?> />
											<span><?php _e( 'No color scheme', BLOGCENTRAL_TXT_DOMAIN ); ?></span><br /><br />
											<input type="radio" name="blogcentral[color_scheme]" value="1"<?php checked( $saved["color_scheme"], '1' ); ?> />
											<div class="color-scheme color-scheme1"></div>
											<br /><br /><input type="radio" name="blogcentral[color_scheme]" value="2"<?php checked( $saved["color_scheme"], '2' ); ?> />
											<div class="color-scheme color-scheme2"></div>
											<br /><br /><input type="radio" name="blogcentral[color_scheme]" value="3"<?php checked( $saved["color_scheme"], '3' ); ?> />
											<div class="color-scheme color-scheme3"></div>
											<br /><br /><input type="radio" name="blogcentral[color_scheme]" value="4"<?php checked( $saved["color_scheme"], '4' ); ?> />
											<div class="color-scheme color-scheme4"></div>
											<br /><br /><input type="radio" name="blogcentral[color_scheme]" value="5"<?php checked( $saved["color_scheme"], '5' ); ?> />
											<div class="color-scheme color-scheme5"></div>
										</td>
									</tr> 
								</tbody>
							</table>
							<p class='section-title div3'><?php _e( 'Body', BLOGCENTRAL_TXT_DOMAIN ); ?></p> 
							<p class="instruction fixed"><?php _e( 'To change the background of the body, navigate to "Appearance->Background" on the admin sidebar.', BLOGCENTRAL_TXT_DOMAIN ); ?></p><br />
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php _e( 'Text color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label><?php _e( 'Choose a color.', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<div class="group">
												<input id="blogcentral_body_txt_color" type="text" name="blogcentral[body_txt_color]"<?php if ( $saved["body_txt_color"] ) { echo ' value="' . 
													esc_attr( $saved['body_txt_color'] ) . '"'; } ?> class="blogcentral-color-field" data-default-color="#777777"  />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Default link color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label><?php _e( 'Default color of the links.', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<div class="group">
												<input id="blogcentral_body_lnk_color" type="text" name="blogcentral[body_lnk_color]"
												<?php if ( $saved["body_lnk_color"] ) { echo ' value="' . esc_attr( $saved['body_lnk_color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#0071d8" />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Default link hover color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label>
											<?php _e( 'Default color of the links when hovered.', BLOGCENTRAL_TXT_DOMAIN ); ?>
											</label>
											<div class="group">
												<input id="blogcentral_body_lnk_hover_color" type="text" name="blogcentral[body_lnk_hover_color]"<?php if ( $saved["body_lnk_hover_color"] ) 
													{ echo ' value="' . esc_attr( $saved['body_lnk_hover_color'] ) . '"'; } ?> class='blogcentral-color-field' />
												<p class="instruction fixed"><small><?php _e( 'The default hover color is the color scheme, if chosen.', BLOGCENTRAL_TXT_DOMAIN ); ?></small></p><br />	
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Default link visited color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label>
												<?php _e( 'Default color of visited links.', BLOGCENTRAL_TXT_DOMAIN ); ?>
											</label>
											<div class="group">
												<input id="blogcentral_body_lnk_visited_color" type="text" name="blogcentral[body_lnk_visited_color]"<?php if ( $saved["body_lnk_visited_color"] ) { 
													echo ' value="' . esc_attr( $saved['body_lnk_visited_color'] ) . '"'; } ?> class="blogcentral-color-field" />
											</div> 
										</td>
									</tr> 
								</tbody>
							</table>
							<p class='section-title div3'><?php _e( 'Main Content', BLOGCENTRAL_TXT_DOMAIN ); ?></p> 
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php _e( 'Background color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label>
												<?php _e( 'This is the area that holds the main area and sidebar.', BLOGCENTRAL_TXT_DOMAIN ); ?>
											</label>
											<div class="group">
												<input id="blogcentral_main_content_back_color" type="text" name="blogcentral[main_content_back_color]"<?php if ( $saved["main_content_back_color"] ) { 
													echo ' value="' . esc_attr( $saved['main_content_back_color'] ) . '"'; } ?> class='blogcentral-color-field' />
											</div> 
										</td>
									</tr> 
								</tbody>
							</table>
							<p class='section-title div3'><?php
								_e( 'Headers- enter size including unit of measurement, ex. 12px', BLOGCENTRAL_TXT_DOMAIN );
							?></p> 
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php _e( 'H1', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label>
												<?php _e( 'Font Size', BLOGCENTRAL_TXT_DOMAIN ); ?>
											</label>
											<input id="blogcentral_h1_size" type="text" name="blogcentral[h1][size]"<?php if ( $saved['h1']['size'] ) { 
												echo 'value="' . esc_attr( $saved['h1']['size'] ) . '"'; } ?> /> 
											<label><?php _e( 'Color', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<div class="group">
												<input id="blogcentral_h1_color" type="text" name="blogcentral[h1][color]"<?php if( $saved['h1']['color'] ) { 
													echo ' value="' . esc_attr( $saved['h1']['color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#222222" />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'H2', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<p class="instruction fixed"><small><?php _e( 'Setting a font size or color on this will affect the post titles in the main posts content area.', BLOGCENTRAL_TXT_DOMAIN ); ?></small></p><br />
											<label><?php _e( 'Font Size', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<input id="blogcentral_h2_size" type="text" name="blogcentral[h2][size]"<?php if ( $saved['h2']['size'] ) {  
												echo ' value="' . esc_attr( $saved['h2']['size'] ) . '"'; } ?> /> 
											<label><?php _e( 'Color', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<div class="group">
												<input id="blogcentral_h2_color" type="text" name="blogcentral[h2][color]"<?php if ( $saved['h2']['color'] ) { 
													echo ' value="' . esc_attr( $saved['h2']['color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#222222" />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'H3', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label>
											<?php _e( 'Font Size', BLOGCENTRAL_TXT_DOMAIN ); ?>
											</label>
											<input id="blogcentral_h3_size" type="text" name="blogcentral[h3][size]" <?php if ( $saved['h3']['size'] ) { 
												echo ' value="' . esc_attr( $saved['h3']['size'] ) . '"'; } ?> /> 
											<label><?php _e( 'Color', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<div class="group">
												<input id="blogcentral_h3_color" type="text" name="blogcentral[h3][color]"<?php if ( $saved['h3']['color'] ) { 
												echo ' value="' . esc_attr( $saved['h3']['color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#222222" />
											</div>
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'H4', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label><?php _e( 'Font Size', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<input id="blogcentral_h4_size" type="text" name="blogcentral[h4][size]"<?php if ( $saved['h4']['size'] ) {
												echo ' value="' . esc_attr( $saved['h4']['size'] ) . '"'; } ?> /> 
											<label><?php _e( 'Color', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<div class="group">
												<input id="blogcentral_h4_color" type="text" name="blogcentral[h4][color]"<?php if ( $saved['h4']['color'] ) {
												echo ' value="' . esc_attr( $saved['h4']['color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#222222" />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'H5', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label><?php _e( 'Font Size', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<input id="blogcentral_h5_size" type="text" name="blogcentral[h5][size]"<?php if ( $saved['h5']['size'] ) {
												echo ' value="' . esc_attr( $saved['h5']['size'] ) . '"'; } ?> /> 
											<label><?php _e( 'Color', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<div class="group">
												<input id="blogcentral_h5_color" type="text" name="blogcentral[h5][color]"<?php if ( $saved['h5']['color'] ) {
													echo ' value="' . esc_attr( $saved['h5']['color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#222222" />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'H6', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label><?php _e( 'Font Size', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<input id="blogcentral_h6_size" type="text" name="blogcentral[h6][size]"<?php if ( $saved['h6']['size'] ) {
												echo ' value="' . esc_attr( $saved['h6']['size'] ) . '"'; } ?> /> 
											<label><?php _e( 'Color', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<div class="group">
												<input id="blogcentral_h6_color" type="text" name="blogcentral[h6][color]"<?php if ( $saved['h6']['color'] ) {
													echo ' value="' . esc_attr( $saved['h6']['color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#222222" />
											</div> 
										</td>
									</tr> 
								</tbody>
							</table>
						</div>
						<div id="blogcentral-fonts" class="tabs-panel">
							<p><?php
								_e( 'Customize these options to fit your needs, or leave blank to use the default value.', BLOGCENTRAL_TXT_DOMAIN ); ?></p>
							<?php
								$fonts = isset( $saved['fonts'] ) ? $saved['fonts'] : array();
							?>
							<table class='form-table'>
								<tbody>
									<tr>
										<th><?php  _e( 'Body text', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td id="body_txt">
											<?php blogcentral_display_options_fonts( $fonts, 'body_txt' ); ?>
										</td>
									</tr>
									<tr>
										<th><?php  _e( 'Headers', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td id="headers">
											<?php blogcentral_display_options_fonts( $fonts, 'headers' ); ?>
										</td>
									</tr>
									<tr>
										<th><?php  _e( 'Main menu', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td id="main_menu">
											<?php blogcentral_display_options_fonts( $fonts, 'main_menu' ); ?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div id="blogcentral-social" class="tabs-panel">
							<p class='section-title div3'><?php _e( 'Social', BLOGCENTRAL_TXT_DOMAIN ); ?></p> 
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php _e( 'Facebook', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label><?php _e( 'Application id', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<input type="text" name="blogcentral[facebook_app_id]"<?php if ( isset( $saved['facebook_app_id'] ) ) {
												echo ' value="' . esc_attr( $saved['facebook_app_id'] ) . '"'; } ?> /> 
										</td>
									</tr>
									<tr>
										<th><?php _e( 'Google', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label><?php _e( 'Application id', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<input type="text" name="blogcentral[google_app_id]"<?php
												if ( isset( $saved['google_app_id'] ) ) {
													echo ' value="' . esc_attr( $saved['google_app_id'] ) . '"'; } ?> />
										</td>
									</tr> 
								</tbody>
							</table>
						</div>
						<div id="blogcentral-contact-info" class="tabs-panel">
							<p><?php _e( 'Customize these options to fit your needs, or leave blank to use the default value. To choose an icon, click in the text box.', BLOGCENTRAL_TXT_DOMAIN ); ?></p>
							<p class="section-title div3"><?php _e( 'Requires the 4bzCore plugin to be installed and activated.', BLOGCENTRAL_TXT_DOMAIN ); ?></p>
							<table class='form-table'>
								<tbody>	
									<?php if ( isset( $fourbzcore_plugin ) ) { ?>
									<tr>
										<th><?php _e( 'Contact info', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td><?php 
												$saved['contact_info']['name'] = 'contact_info';
												$saved['contact_info']['name_attr_pre'] = 'blogcentral';
												$fourbzcore_plugin->display_contact_opts( $saved['contact_info'] );
											?>
										</td>
									</tr>
									<tr>
										<th><?php _e( 'Contact form', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
											<td>
												<label><?php 
													_e( 'Enter the shortcode here to display a contact form.', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
												<textarea name="blogcentral[contact_form]" cols="30"  rows="10"><?php 
													if ( isset( $saved['contact_form'] ) ) {
														echo esc_attr( $saved['contact_form'] ); } ?></textarea> 
											</td>
									</tr>
									<?php } else { ?>
									<tr>
										<td><?php _e( 'This theme uses the 4bzCore plugin to display contact information and/or a contact form. It is available for download from the WordPress.org repository. <a href="http://wordpress.org/plugins/4bzcore">Download Now</a>', BLOGCENTRAL_TXT_DOMAIN ); ?></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<div id="blogcentral-header" class="tabs-panel">
							<p><?php 
								_e( 'Customize these options to fit your needs, or leave blank to use the default value.', BLOGCENTRAL_TXT_DOMAIN ); ?></p>
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php _e( 'Sticky header', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<div>
												<input type="checkbox" name="blogcentral[header_sticky]"<?php checked( $saved['header_sticky'], 'on' ); ?> /> 
												<label><?php 
													_e( 'Would you like the header to be fixed on top of the page on scroll?', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											</div>
										</td>
									</tr>
									<tr>
										<th><?php _e( 'Background color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<div class="group">
												<input type="text" name="blogcentral[header_back_color]"<?php if ( isset( $saved['header_back_color'] ) ) {
													echo ' value="' . esc_attr( $saved['header_back_color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#ffffff" />
											</div> 
										</td>
									</tr>
									<tr>
										<th><?php _e( 'Text color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<div class="group">
												<input type="text" name="blogcentral[header_txt_color]"<?php if ( isset( $saved['header_txt_color'] ) ) {
													echo ' value="' . esc_attr( $saved['header_txt_color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#777777" />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Link Color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<div class="group">
												<input type="text" name="blogcentral[header_lnk_color]"<?php if ( isset( $saved['header_lnk_color'] ) ) {
													echo ' value="' . esc_attr( $saved['header_lnk_color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#777777" />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Logo', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
										<?php 
											blogcentral_construct_upload_image( $saved, 'logo', false ); 
										?>
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Favicon' , BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<?php blogcentral_construct_upload_image( $saved, 'favicon', false ); ?>
										</td>
									</tr>
									<?php if ( isset( $fourbzcore_plugin ) ) { ?>
									<tr>
										<th><?php _e( 'Contact Information' , BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<div>
												<input type="checkbox" name="blogcentral[show_contact]" class="displaymaster"<?php checked( $saved['show_contact'], 'on' ); ?> /> 
												<label><?php _e( 'Display contact information in the header top widget area. Active widgets in this area override this option.', BLOGCENTRAL_TXT_DOMAIN ); ?>
												</label><br /><br />
												<div <?php if ( ! $show_contact  ) { echo 'style="display:none;" '; } ?> class="hideshow">
												<?php 
												blogcentral_display_contact_info_layout_opts( 
													isset( $saved['contact_info'] ) ?
													$saved['contact_info'] :
													'', $widget ); ?>
												</div>
											</div>
										</td>
									</tr>
									<?php } ?>
									<tr>
										<th><?php _e( 'Search Form' , BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<div>
												<input type="checkbox" name="blogcentral[show_search]"<?php checked( $saved['show_search'], 'on' ); ?> /> 
												<label><?php _e( 'Display a search form in the header widget area. Active widgets in this area override this option.', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											</div>
										</td>
									</tr> 
								</tbody>
							</table>
						</div>
						<div id="blogcentral-main-menu" class="tabs-panel">
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php _e( 'Background Color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<input id="blogcentral_menu_back_color" type="text" name="blogcentral[menu_back_color]"<?php if ( isset( $saved['menu_back_color'] ) ) {
												echo ' value="' . esc_attr( $saved['menu_back_color'] ) . '"'; } ?> class='blogcentral-color-field' /> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Link Color' ,BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label>
												<?php _e( 'Color of the menu links', BLOGCENTRAL_TXT_DOMAIN ); ?>
											</label>
											<div class="group">
												<input id="blogcentral_menu_lnk_color" type="text" name="blogcentral[menu_lnk_color]"<?php if ( isset( $saved['menu_lnk_color'] ) ) {
													echo ' value="' . esc_attr( $saved['menu_lnk_color'] ) . '"'; } ?> class='blogcentral-color-field' />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Active Link Color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<p class="instruction fixed"><small><?php
												echo __( "The default color is the color scheme, if set.", BLOGCENTRAL_TXT_DOMAIN ); ?>
											</small></p><br />
											<label>
												<?php _e( 'Color of the current page menu link.', BLOGCENTRAL_TXT_DOMAIN ); ?>
											</label>
											<div class="group">
												<input id="blogcentral_menu_active_color" type="text" name="blogcentral[menu_active_color]"<?php if ( isset( $saved['menu_active_color'] ) ) {
													echo ' value="' . esc_attr( $saved['menu_active_color'] ) . '"'; } ?> class='blogcentral-color-field' />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Sub Menus', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label>
												<?php _e( 'Background color for all sub menus.', BLOGCENTRAL_TXT_DOMAIN ); ?>
											</label>
											<div class="group">
												<input id="blogcentral_submenu_back_color" type="text" name="blogcentral[submenu_back_color]"<?php if ( isset( $saved['submenu_back_color'] ) ) {
													echo ' value="' . esc_attr( $saved['submenu_back_color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#ededed" />
											</div> 
											<label>
												<?php _e( 'Color of the submenu links.', BLOGCENTRAL_TXT_DOMAIN ); ?>
											</label>
											<div class="group">
												<input id="blogcentral_submenu_lnk_color" type="text" name="blogcentral[submenu_lnk_color]"<?php if ( isset( $saved['submenu_lnk_color'] ) ) {
													echo ' value="' . esc_attr( $saved['submenu_lnk_color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#222222" />
											</div> 
										</td>
									</tr> 
								</tbody>
							</table>
						</div>
						<div id="blogcentral-page-header" class="tabs-panel">
							<table class="form-table">
								<tbody>	
									<tr>
										<td>
											<p class="instruction fixed"><small><?php
												echo __( "This is the widgetized area called precontent widget. If no widgets are active, then it serves as the page header and a box with an image and an overlay that shows what page is being viewed, the blog description, today's date, and the breadcrumbs will be shown, if enabled. To disable this, leave the following field unchecked.", BLOGCENTRAL_TXT_DOMAIN ); ?>
											</small></p><br />
											<input type="checkbox" class="displaymaster" name="blogcentral[page_header]" <?php checked( $saved["page_header"], "on" ); ?> />
											<label><?php _e( 'Enable Page Header', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<div <?php if ( ! $saved['page_header'] ) { echo 'style="display:none;" '; } ?> class="hideshow"><br />
												<div class="group"><?php
													blogcentral_construct_upload_image( $saved, 'page_header' ); ?>
													<label>
													<?php _e( 'Enter height with unit of measurement - leave blank to use default.', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
													<input id="blogcentral_page_header_height" type="text" name="blogcentral[page_header_height]"<?php if ( isset( $saved['page_header_height'] ) ) {
														echo ' value="' . esc_attr( $saved['page_header_height'] ) . '"'; } ?> />
												</div>
												<p><?php echo __( 'Layout', BLOGCENTRAL_TXT_DOMAIN ); ?></p>
												<div class="group">
													<input type="radio" name="blogcentral[page_header_layout]" value="1"<?php checked( $saved["page_header_layout"], '1' ); ?> />
													<label><?php _e( 'layout 1', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
													<img src='<?php echo BLOGCENTRAL_THEME_URL; ?>/images/preview/page-header1.jpg' alt='page header 1' /><br /><br />
													<input type="radio" name="blogcentral[page_header_layout]" value="2"<?php checked( $saved["page_header_layout"], '2' ); ?> />
													<label><?php _e( 'layout 2', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
													<img src='<?php echo BLOGCENTRAL_THEME_URL; ?>/images/preview/page-header2.jpg' alt='page header 2' /><br /><br />
													<input type="radio" name="blogcentral[page_header_layout]" value="3"<?php checked( $saved["page_header_layout"], '3' ); ?> />
													<label><?php _e( 'layout 3', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
													<img src='<?php echo BLOGCENTRAL_THEME_URL; ?>/images/preview/page-header3.jpg' alt='page header 3' /><br /><br />
												</div>
												<label><?php _e( 'Page Header Text Color', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
												<div class="group">
													<input type="text" name="blogcentral[page_header_color]"<?php if ( isset( $saved['page_header_color'] ) ) {
														echo ' value="' . esc_attr( $saved['page_header_color'] ) . '"'; } ?> class='blogcentral-color-field' />
												</div>
												<div class="group">
													<input type="checkbox" name="blogcentral[show_todays_date]"<?php checked( $saved['show_todays_date'], 'on' ); ?> />
													<label><?php _e( "Enable show today's date", BLOGCENTRAL_TXT_DOMAIN ); ?></label>										
												</div>	
												<div class="group">
													<input type="checkbox" name="blogcentral[breadcrumbs]"<?php checked( $saved['breadcrumbs'], 'on' ); ?> />
													<label><?php _e( 'Enable breadcrumbs', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
												</div>
											</div>
											<br /><br />
											<p><?php _e( 'Blog description', BLOGCENTRAL_TXT_DOMAIN ); ?></p><br />
										
											<div>
												<input type="checkbox" name="blogcentral[blog_info]"<?php checked( $saved['blog_info'], 'on' ); ?> /> 
												<label><?php 
													_e( 'Would you like to display the blog description? It will be displayed with the page title on the front posts listing page.', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											</div>
										</td>
									</tr> 
								</tbody>
							</table>
						</div>
						<div id="blogcentral-footer-cont" class="tabs-panel">
							<p><?php _e( 'Customize these options to fit your needs, or leave blank to use the default value.', BLOGCENTRAL_TXT_DOMAIN ); ?></p>
							<p class='section-title div3'><?php _e( 'Footer- this area has 5 widget areas. The extended option will show all of these areas, simple option will show only two of these areas. The default is to show the simple version. As always, you can add widgets on the widgets.php page.', BLOGCENTRAL_TXT_DOMAIN ); ?></p> 
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php _e( 'Use extended version', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<input type="checkbox" name="blogcentral[footer_extended]"<?php checked( $saved['footer_extended'], 'on' ); ?> /> 			
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Background Color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<div class="group">
												<input type="text" name="blogcentral[footer_bck_color]"<?php if ( isset( $saved['footer_bck_color'] ) ) {
													echo ' value="' . esc_attr( $saved['footer_bck_color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#2b2b2b" />
											</div> 
										</td>
									</tr>
									<tr>
										<th><?php _e( 'Text Color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<div class="group">
												<input type="text" name="blogcentral[footer_txt_color]"<?php if ( isset( $saved['footer_txt_color'] ) ) {
													echo ' value="' . esc_attr( $saved['footer_txt_color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#646464" />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Link Color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<div class="group">
												<input type="text" name="blogcentral[footer_lnk_color]"<?php if ( isset( $saved['footer_lnk_color'] ) ) {
													echo ' value="' . esc_attr( $saved['footer_lnk_color'] ) . '"'; } ?> class='blogcentral-color-field' data-default-color="#afafaf" />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Link Hover Color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<div class="group">
												<input type="text" name="blogcentral[footer_lnk_hover_color]"<?php if ( isset( $saved['footer_lnk_hover_color'] ) ) {
													echo ' value="' . esc_attr( $saved['footer_lnk_hover_color'] ) . '"'; } ?> class='blogcentral-color-field' />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Link Visited Color', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<div class="group">
												<input type="text" name="blogcentral[footer_lnk_visited_color]"<?php if ( isset( $saved['footer_lnk_visited_color'] ) ) {
													echo ' value="' . esc_attr( $saved['footer_lnk_visited_color'] ) . '"'; } ?> class='blogcentral-color-field' />
											</div> 
										</td>
									</tr> 
									<tr>
										<th><?php _e( 'Copyright Text', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label><?php _e( 'Enter the copyright information.', BLOGCENTRAL_TXT_DOMAIN ); ?>
											</label>
											<textarea id="blogcentral-copyright" name="blogcentral[copyright]" cols="50" rows="10"><?php
												if ( isset( $saved['copyright'] ) ) {
													echo esc_attr( $saved['copyright'] ); } ?></textarea> 
										</td>
									</tr> 
								</tbody>
							</table>
						</div>
						<div id="blogcentral-posts" class="tabs-panel">
							<p> <?php _e( 'Customize these options to fit your needs, or leave as-is to use the default value.', BLOGCENTRAL_TXT_DOMAIN ); ?> </p>
							<div class="tabs-layout1 no-border">
								<ul>
									<li><a class="section-title" href="#posts-general"><?php _e( 'General', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>
									<li><a class="section-title" href="#posts-landing-page"><?php _e( 'Posts Listing Pages', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>
									<li><a class="section-title" href="#posts-single-page"><?php _e( 'Single Post Page', BLOGCENTRAL_TXT_DOMAIN ); ?></a></li>						
								</ul>
								<div id="posts-general" class="tabs-panel">
									<?php blogcentral_display_options_posts_general( isset( $saved['posts_general'] ) ?
										$saved['posts_general'] :
										'' ); ?>
								</div>  
								<div id="posts-landing-page" class="tabs-panel"> 
									<p><?php _e( 'These options will affect the appearance of the posts listing in the main area on all pages that display a posts listing in a div with id #main-area.', BLOGCENTRAL_TXT_DOMAIN ); ?></p>
									<?php blogcentral_display_options_posts_landing( isset( $saved['posts_landing'] ) ?
										$saved['posts_landing'] :
										'' ); ?>	
								</div>  
								<div id="posts-single-page" class="tabs-panel"> 
									<?php blogcentral_display_options_posts_single( isset( $saved['posts_single'] ) ?
										$saved['posts_single'] :
										'' ); ?>	
								</div>
							</div>
						</div>
						<div id="blogcentral-custom-css" class="tabs-panel">
							<p> <?php _e( 'Customize these options to fit your needs, or leave blank to use the default value.', BLOGCENTRAL_TXT_DOMAIN ); ?> </p>
							<table class='form-table'>
								<tbody>
									<tr>
										<th><?php _e( 'Custom CSS', BLOGCENTRAL_TXT_DOMAIN ); ?></th>
										<td>
											<label><?php _e( 'Enter the css without style tags.', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
											<textarea id="custom-css" name="blogcentral[custom_css]" cols="50" rows="10"><?php
												if ( isset( $saved['custom_css'] ) ) {
													echo esc_attr( $saved['custom_css'] ); } ?></textarea> 
										</td>
									</tr>
								</tbody>
							</table>
						</div>				
					</div>
				</div>
				<div class="submit">
					<input class="button-3" name="blogcentral_save_options" value="<?php _e( 'Save Options', BLOGCENTRAL_TXT_DOMAIN );?>" type="submit" /> 
				</div>
			</form>
			<div id="theme-options-footer">
				<div class="rate-theme"> 
					<?php _e( 'If you like BlogCentral and find it useful, please leave a ', BLOGCENTRAL_TXT_DOMAIN ); ?> <a href="https://wordpress.org/support/view/theme-reviews/blogcentral#postform"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i></a><?php _e( ' rating. Thank you.', BLOGCENTRAL_TXT_DOMAIN ); ?>
				</div>
				<div id="newsletter-signup">
					<!-- Begin MailChimp Signup Form -->
					<div id="mc_embed_signup">
					<form action="//4bzthemes.us11.list-manage.com/subscribe/post?u=9fb12b9ef798e5920165be51e&amp;id=05db095603" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						<div id="mc_embed_signup_scroll">
						<h2><?php _e( 'Subscribe to our newsletter and get updates, tips, and more in your inbox.', BLOGCENTRAL_TXT_DOMAIN ); ?></h2>
					<div class="indicates-required"><span>*</span> <?php _e( 'indicates required field', BLOGCENTRAL_TXT_DOMAIN ); ?></div>
					<div class="mc-field-group">
						<label for="mce-EMAIL"><?php _e( 'Email address', BLOGCENTRAL_TXT_DOMAIN ); ?>  <span>*</span></label>
						<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" />
					</div><br />
					<div class="mc-field-group">
						<label for="mce-FNAME"><?php _e( 'First Name', BLOGCENTRAL_TXT_DOMAIN ); ?> </label>
						<input type="text" value="" name="FNAME" class="" id="mce-FNAME" />
					</div>
					<div class="mc-field-group">
						<label for="mce-LNAME"><?php _e( 'Last Name', BLOGCENTRAL_TXT_DOMAIN ); ?> </label>
						<input type="text" value="" name="LNAME" class="" id="mce-LNAME" />
					</div>
					<div style="display:none" class="mc-field-group input-group">
						 <ul class="interestgroup_field checkbox-group"> <li class="below12"> <label class="checkbox" for="group_1"><input type="checkbox" data-dojo-type="dijit/form/CheckBox" id="group_1" name="group[6017][1]" value="1"  class="av-checkbox" checked="checked"><span>BlogCentral</span> </label> </li> </ul> 
					</div>
						<div id="mce-responses" class="clear">
							<div class="response" id="mce-error-response" style="display:none"></div>
							<div class="response" id="mce-success-response" style="display:none"></div>
						</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
						<div style="position: absolute; left: -5000px;"><input type="text" name="b_9fb12b9ef798e5920165be51e_05db095603" tabindex="-1" value=""></div>
						<div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
						</div>
					</form>
					</div>
					<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script><script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
					<!--End mc_embed_signup-->
				</div>
			</div>
		</div>
	<?php
	}
}

if ( ! function_exists( 'blogcentral_construct_options' ) ) {
	/**
	 * Construct select option tags
	 *
	 * Value of options will be numerical, starting with 1
	 *
	 * @since 1.0.0
	 *
	 * @param int $num Optional. Number of options to display.
	 * @param int $selected Optional. The selected option.
	 */
	function blogcentral_construct_options( $num = 5, $selected = '' ) {
		for ( $i = 1; $i <= $num; ++$i ) {
			echo "<option value='$i'" . selected( $selected, $i, false ) . ">";
			echo $i; 
			echo "</option>";
		}
	}
}

if ( ! function_exists( 'blogcentral_construct_upload_image' ) ) {
	/**
	 * Construct the html to upload an image
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Optional. Saved options, if any.
	 * @param string $name Required. Name of the input field.
	 * @param boolean $background Optional. Construct image for background?
	 */
	function blogcentral_construct_upload_image( $opts, $name, $background = true ) {
		if ( ! isset( $name ) ) {
			return;
		}
		
		if ( $background ) {
			$suffix = '-bck-img';
			$suffix_name = '_bck_img';
		} else {
			$suffix = $suffix_name = '';
		}
		
		?>
		<input class="icon icon-image" id="blogcentral-<?php echo $name; ?>" type="text" name="blogcentral[<?php echo $name . $suffix_name; ?>]"<?php if ( isset( $opts[$name . $suffix_name] ) ) { echo ' value="' . esc_attr( $opts[$name . $suffix_name] ) . '"'; } ?> /> 
		<input class="icon icon-image-btn button" data-blogcentral-textbox="blogcentral-<?php echo $name; ?>" type="button" value="<?php _e( 'Upload image', BLOGCENTRAL_TXT_DOMAIN ); ?>" />
		<label><?php _e( 'Width', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
		<input type="text" id="blogcentral-<?php echo $name; ?>-width" name="blogcentral[<?php echo $name . $suffix_name; ?>_width]"<?php if ( isset( $opts[$name . $suffix_name . "_width"] ) ) {
			echo ' value="' . esc_attr( $opts[$name . $suffix_name . '_width'] ) . '"'; } ?> />
		<label><?php _e( 'Height', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
		<input type="text" id="blogcentral-<?php echo $name; ?>-height" name="blogcentral[<?php echo $name . $suffix_name; ?>_height]"<?php 
		if ( isset( $opts[$name . $suffix_name . "_height"] ) ) {
			echo ' value="' . esc_attr( $opts[$name . $suffix_name . '_height'] ) . '"'; } ?> />
		<label><?php _e( 'Alt Text', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
		<input type="text" id="blogcentral-<?php echo $name; ?>-alt" name="blogcentral[<?php echo $name . $suffix_name; ?>_alt]"<?php
		if ( isset( $opts[$name . $suffix_name . "_alt"] ) ) {
			echo ' value="' . esc_attr( $opts[$name . $suffix_name . '_alt'] ) . '"'; } ?> />
		
		<?php if ( $background ) { ?>
		<label><?php _e( 'Background Attachment', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
		
		<div class="group">
			<input type="radio" id="blogcentral-<?php echo $name; ?>-bck-img-attachment" name="blogcentral[<?php echo $name; ?>_bck_img_attachment]" value="fixed"<?php if ( isset( $opts[$name . "_bck_img_attachment"] ) ) { checked( $opts[$name . "_bck_img_attachment"], 'fixed' ); } ?> />
			<label><?php _e( 'fixed', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
			<input type="radio" name="blogcentral[<?php echo $name; ?>_bck_img_attachment]" value="scroll"<?php if ( isset( $opts[$name . "_bck_img_attachment"] ) ) {
					checked( $opts[$name . "_bck_img_attachment"], 'scroll' ); } ?> />
			<label><?php _e( 'scroll', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
		</div>
		<label><?php _e( 'Background Position', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
		<input type="text" id="blogcentral-<?php echo $name; ?>-bck-img-position" name="blogcentral[<?php echo $name; ?>_bck_img_position]"
			<?php if ( isset( $opts[$name . '_bck_img_position'] ) ) {
			echo ' value="' . esc_attr( $opts[$name . '_bck_img_position'] ) . '"'; } ?> />
		<label><?php _e( 'Background Repeat', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
		<div class="group">
			<input type="radio" name="blogcentral[<?php echo $name; ?>_bck_img_repeat]" value="repeat"<?php if ( isset( $opts[$name . "_bck_img_repeat"] ) ) {
				checked( $opts[$name . "_bck_img_repeat"], 'repeat' ); } ?> />
			<label><?php _e( 'repeat', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
			<input type="radio" name="blogcentral[<?php echo $name; ?>_bck_img_repeat]" value="repeat-x"<?php if ( isset( $opts[$name . "_bck_img_repeat"] ) ) {
				checked( $opts[$name . "_bck_img_repeat"], 'repeat-x' ); } ?> />
			<label><?php _e( 'repeat-x', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
			<input type="radio" name="blogcentral[<?php echo $name; ?>_bck_img_repeat]" value="repeat-y"<?php if ( isset( $opts[$name . "_bck_img_repeat"] ) ) {
				checked( $opts[$name . "_bck_img_repeat"], 'repeat-y' ); } ?> />
			<label><?php _e( 'repeat-y', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
			<input type="radio" name="blogcentral[<?php echo $name; ?>_bck_img_repeat]" value="no-repeat"<?php if ( isset( $opts[$name . "_bck_img_repeat"] ) ) {
				checked( $opts[$name . "_bck_img_repeat"], 'no-repeat' ); } ?> />
			<label><?php _e( 'no-repeat', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
		</div>
		<?php } 
	}
}

if ( ! function_exists( 'blogcentral_display_border_items_select' ) ) {
	/**
	 * Construct and display options for borders
	 *
	 * @since 1.0.0
	 *
	 * @param string $selected Optional. Saved border.
	 */
	function blogcentral_display_border_items_select( $selected = '' ) {
		$borders = array( 'div1', 'div2', 'div3', );
		$html = '<option value="0"' . selected( $selected, 0, false ) . '>' . __( 'no border', BLOGCENTRAL_TXT_DOMAIN ) .
			'</option>';
			
		for ( $i = 0; $i < 3; ++$i ) {
			$select = '';
			$html .= "<option value='$borders[$i]'" . selected( $selected, $borders[$i], false ) . ">$borders[$i]
				</option>"; 
		}
		
		return $html;
	}
}

/**
 * 5.4.2 Font Options
 *-----------------------------------------------------------------------*/

if ( ! function_exists( 'blogcentral_display_options_fonts' ) ) {
	/**
	 * Display font options
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Optional. Saved font options
	 * @param string $name Required. Name to use in constructing the name of the input field.
	 */
	function blogcentral_display_options_fonts( $opts = '', $name ) {
		if ( ! isset( $name ) ) {
			return;
		}
			
		$system_fonts = array( 'arial', 'baskerville', 'book antiqua', 'century gothic', 'helvetica', 'gill sans',
			'impact', 'calibri', 'cambria', 'consolas', 'courier new', 'georgia', 'impact', 'lucida console',
			'lucida sans unicode', 'palatino linotype', 'sans serif', 'serif', 'tahoma', 'times new roman',
			'trebuchet ms', 'verdana' ); 
		
		$defaults = array(
			$name . "_font_family"	=>	'no_font',
			$name . "_font_type"	=>	'system',
			$name . "_font_weight"	=>	'regular',
			$name . "_font_subsets"	=>	array(),
		);
		
		if ( $opts ) {
			$opts = wp_parse_args( $opts, $defaults );
		} else {
			$opts = $defaults;
		}
		
		$selected = '';
		
		if ( is_array( $opts ) ) {
			$selected = isset( $opts[$name . '_font_family'] ) ? $opts[$name . '_font_family'] : '';
		}

		// Create nonce for use in the ajax request for a font's options
		$nonce = wp_create_nonce( "blogcentral-font-select-nonce" );
		$link = admin_url( 'admin-ajax.php?action=blogcentral_font_options&amp;nonce=' . $nonce );

		$font_family_select = '<label>' .
			__( 'Font Family', BLOGCENTRAL_TXT_DOMAIN ) . '</label><select name="blogcentral[fonts][' . esc_attr( $name ) . '_font_family]" class="font-select" id="' . $name  . '-font-select" data-blogcentral-font-url="' . $link . '" >
			<option value="no_font"' . selected( $opts[$name . '_font_family'], 'no_font', false ) . '>' .
			__( 'no font', BLOGCENTRAL_TXT_DOMAIN ) . '</option><option class="disabled" disabled>' .
			__( 'System Fonts', BLOGCENTRAL_TXT_DOMAIN ) . '</option>';
		
		$font_family_opts = '';
		$type = '';
		
		$fonts_count =  count( $system_fonts );
		
		// Construct the system fonts first
		for ( $i = 0; $i < $fonts_count; ++$i ) {
			$select = '';
			$font = $system_fonts[$i];
			
			if ( $selected === $font ) {
				$type = 'system';
			}
			$font_family_opts .= '<option class="system_fonts" value="' . $font . '"' . 
				selected( $selected, $font, false ) . '>' . $font . '</option>';
		}
		
		// Now let's construct google fonts.
		$google_fonts = blogcentral_get_google_fonts();
		
		// For each items get its family variants and subsets. Initially, if none chosen, then display nothing.
		
		$variants = $subsets = '';
		$font_family_opts .= '<option class="disabled" disabled>' . __( 'Google Fonts', BLOGCENTRAL_TXT_DOMAIN ) . '</option>';
		
		if ( is_array( $google_fonts ) && 0 < count( $google_fonts ) ) {
			foreach ( $google_fonts as $key => $val ) {
				$select = '';
				$font_esc = esc_html( $key );
				
				if ( $selected === $key ) {
					$variants = isset( $val['variants'] ) ? $val['variants'] : array();
					$subsets = isset( $val['subsets'] ) ? $val['subsets'] : array();
					$type = 'google';
				}
				
				$font_family_opts .= '<option class="google_fonts" value="' . $font_esc . '"' . 
					selected( $selected, $key, false ) . '>' . $font_esc . '</option>';
			}
		}
		
		echo $font_family_select . $font_family_opts . '</select>';	
		echo '<div class="loader"></div>';
		
		$display = $clean = $system_clean = '';
		
		echo '<div id="' . $name  . '-font-variant-cont" class="font-variant-cont">';
		
		if ( 'no_font' !== $selected ) {
			if ( $type !== 'system' ) {
				$display = " style='display:none;'";
				blogcentral_display_options_variants( $variants, $selected, isset( $opts[$name . '_font_weight'] ) ? 
					$opts[$name . '_font_weight'] : '', $name, 'google_fonts' );
				ob_start();
				blogcentral_display_options_subsets( $subsets, $selected, isset( $opts[$name . '_font_subsets'] ) ?
					$opts[$name . '_font_subsets'] : '', $name );
				$clean = ob_get_clean();
			} else {
				blogcentral_display_options_variants( '', $selected, isset( $opts[$name . '_font_weight'] ) ? 
					$opts[$name . '_font_weight'] : '', $name, 'system_fonts' );
			}
		}
		
		echo '</div><div id="' . $name  . '-font-subsets-cont" class="font-subsets-cont">' . $clean . '</div>'
			. '<input type="hidden" id="' . $name . '-font-type" name="blogcentral[fonts][' . esc_attr( $name ) . '_font_type]"';
			
		if ( $type ) {
			echo ' value="' . $type . '"';
		}
		
		echo ' />';
	}
}

if ( ! function_exists( 'blogcentral_display_options_variants' ) ) {
	/**
	 * Construct and display options for font weight.
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Optional. List of previously selected font weights.
	 * @param string $font Optional. Selected font.
	 * @param string $selected Optional. Selected font weight.
	 * @param string $name Required. Name to use to construct the name of the input field.
	 * @param string $type Required. Either system_fonts or google_fonts
	 */
	function blogcentral_display_options_variants( $opts = '', $font = '', $selected, $name, $type ) {
		if ( ! isset( $name ) ) {
			return;
		}
		
		$variants = array(
			'100'		=>	__( '100 light', BLOGCENTRAL_TXT_DOMAIN ),
			'100italic'	=>	__(	'100 light italic',	BLOGCENTRAL_TXT_DOMAIN ),
			'200'		=>	__( '200 light', BLOGCENTRAL_TXT_DOMAIN ),
			'200italic'	=>	__( '200 light italic', BLOGCENTRAL_TXT_DOMAIN ),
			'300'		=>	__( '300 light', BLOGCENTRAL_TXT_DOMAIN ),
			'300italic'	=>	__( '300 light italic', BLOGCENTRAL_TXT_DOMAIN ),
			'regular'	=>	__( '400 regular', BLOGCENTRAL_TXT_DOMAIN ),
			'italic'	=>	__( '400 italic', BLOGCENTRAL_TXT_DOMAIN ),
			'500'		=>	__( 'medium', BLOGCENTRAL_TXT_DOMAIN ),
			'500italic'	=>	__( 'medium italic', BLOGCENTRAL_TXT_DOMAIN ),
			'600'		=>	__( 'bold', BLOGCENTRAL_TXT_DOMAIN ),
			'600italic'	=>	__( 'bold italic', BLOGCENTRAL_TXT_DOMAIN ),
			'700'		=>	__( '700 bold', BLOGCENTRAL_TXT_DOMAIN ),
			'700italic'	=>	__( '700 bold italic', BLOGCENTRAL_TXT_DOMAIN ),
			'800'		=>	__( '800 bold', BLOGCENTRAL_TXT_DOMAIN ),
			'800italic'	=>	__( '800 bold italic', BLOGCENTRAL_TXT_DOMAIN ),
			'900'		=>	__( '900 bold', BLOGCENTRAL_TXT_DOMAIN ),
			'900italic'	=>	__( '900 bold italic', BLOGCENTRAL_TXT_DOMAIN )
		);
		
		$font_variants = '<div class="group">';
		
		if ( 'system_fonts' === $type ) {
			foreach ( $variants as $key => $val ) {
				$select = '';
				
				$font_variants .= '<input type="radio" name="blogcentral[fonts][' . $name . '_font_weight]" value="' .
					esc_attr( $key ) . '"' . checked( $selected, $key, false ) . ' /><label>' . esc_attr( $val ) . '</label><br />';
			}
		} else {
			/*
			 * If no weights selected, then need a selected font to display its available weights, 
			 * otherwise return.
			 */
			if ( ! $opts ) {
				if ( ! $font ) {
					return;
				}
				
				$google_fonts = blogcentral_get_google_fonts();
				$google_variants = $google_fonts[$font]['variants'];
			} else {
				$google_variants = $opts;
			}
		
			if ( is_array( $google_variants ) && count( $google_variants ) > 0 ) {
				foreach ( $google_variants as $variant ) {
					$select = '';
					
					$font_variants .= '<input type="radio" name="blogcentral[fonts][' . $name . '_font_weight]" value="' .
						esc_attr( $variant ) . '"' . checked( trim( $selected ), trim( $variant ), false ) . ' />
						<label>' . $variants[$variant] . '</label><br />';
				}
			}
		}
		
		echo '<label>' .  __( 'Font Weight', BLOGCENTRAL_TXT_DOMAIN ) . '</label>' . $font_variants . '</div>';
	}
}

if ( ! function_exists( 'blogcentral_display_options_subsets' ) ) {
	/**
	 * Construct and display options for font subset
	 *
	 * Only if a google font is selected will this be invoked.
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Saved subsets options.
	 * @param string $font Required. Display subsets of font
	 * @param string $selected Optional. List of saved subsets. 
	 * @param string $name Required. Name to use to construct the name of the input field.
	 */
	function blogcentral_display_options_subsets( $opts = '', $font, $selected, $name ) {
		if ( ! $opts ) {
			if ( ! $font ) {
				return;
			}
			
			$google_fonts = blogcentral_get_google_fonts();
			$google_subsets = $google_fonts[$font]['subsets'];
		} else {
			$google_subsets = $opts;
		}
		
		$font_subsets = '<div class="group">';
		
		foreach ( $google_subsets as $subset ) {
			$select = '';
			
			if ( is_array( $selected ) && in_array( $subset, $selected ) ) {
				$select = checked( $subset, $subset, false );
			}
			$font_subsets .= '<input type="checkbox" name="blogcentral[fonts][' . $name . '_font_subsets][]" value="' . esc_attr( $subset ) . '"' . $select . ' /><label>' . esc_html( $subset ) . '</label><br />';
		}
		
		echo '<label>' .  __( 'Subsets', BLOGCENTRAL_TXT_DOMAIN ) . '</label>' . $font_subsets . '</div>';
	}
}

if ( ! function_exists( 'blogcentral_get_google_fonts' ) ) {
	/**
	 * Retrieve google fonts. Check transients, if not there try google server.
	 *
	 * Requires the google fonts application id to be set.
	 *
	 * @since 1.0.0
	 */
	function blogcentral_get_google_fonts() {
		global $blogcentral_opts;
		
		if ( empty( $blogcentral_opts['google_app_id'] ) ) {
			return;
		}
		
		$fonts = $json  = array();
		
		// Get the saved api key.
		$api_key = $blogcentral_opts['google_app_id'];
		$api_url = $api_key ? "&key=$api_key" : "";
		
		// Check transient.
		if ( ! get_transient( 'blogcentral_google_fonts' ) ) {
			// Get list of fonts from Google.
			$response = wp_remote_get( "https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha$api_url",
				array( 
					'sslverify' => false,
					'timeout'	=> 120,
				) );
				
			// Check if response is valid.
			if ( ! is_wp_error( $response ) ) {
				$fonts_list = json_decode( $response['body'], true );
				
				// Check if response is an error message.
				if ( ! isset( $fonts_list['error'] ) ) {
					$json = $response['body'];
				}
			} else {
				echo __( 'Error: Unable to connect to the Google Webfont server at this time. Please review the list of errors below returned by Google.', BLOGCENTRAL_TXT_DOMAIN );
				
				foreach ( $response->errors as $error ) {
					foreach ( $error as $message ) {
						echo $message;
					}
				}
			}
			
			$font_array = json_decode( $json, true );

			if ( is_array( $font_array ) && 0 < count( $font_array ) ) {
				foreach ( $font_array['items'] as $item ) {
					$atts = array( 
						'name'      =>	$item['family'],
						'variants'	=>	$item['variants'],
						'subsets'   => 	$item['subsets'],
					);

					// Add this font to the fonts list.
					$id = $item['family'];
					$fonts[$id] = $atts;
				}
			}
			
			// Set transient.
			set_transient( 'blogcentral_google_fonts', $fonts, 21 * DAY_IN_SECONDS );

		} else {
			$fonts = get_transient( 'blogcentral_google_fonts' );
		}

		return $fonts;
	}
}

if ( ! function_exists( 'blogcentral_construct_google_fonts_url' ) ) {
	/**
	 * Construct the url to retrieve a google font.
	 *
	 * @since 1.0.0
	 *
	 * @return string. Google font url.
	 */
	function blogcentral_construct_google_fonts_url() {
		global $blogcentral_opts;
		
		$saved = isset( $blogcentral_opts['fonts'] ) ? $blogcentral_opts['fonts'] : '';
		$font_elements = array( 'body_txt', 'headers', 'main_menu' );
		$google_url = '';
		$fonts_url = array();
		$num_elements = count( $font_elements );
		
		// 	First create a fonts array indexed by font family	
		for ( $i = 0; $i < $num_elements; ++$i ) {
			$type = isset( $saved[$font_elements[$i] . '_font_type'] ) ?
				$saved[$font_elements[$i] . '_font_type'] :
				'';
			if ( 'google' === $type ) {
				$font = $saved[$font_elements[$i] . '_font_family'];
				$variant = isset( $saved[$font_elements[$i] . '_font_weight'] ) ? 
					$saved[$font_elements[$i] . '_font_weight'] :
					'';
				$subsets = isset( $saved[$font_elements[$i] . '_font_subsets'] ) ? 
					$saved[$font_elements[$i] . '_font_subsets'] :
					array();
				
				if ( isset( $fonts_url[$font] ) ) {
					if ( $variant ) {
						$fonts_url[$font]['font_weights'] = isset( $fonts_url[$font]['font_weights'] ) ? 
							array_merge( $fonts_url[$font]['font_weights'], array( $variant ) ) : 
							array( $variant );
					}
					if ( 0 < count( $subsets ) ) {
						$fonts_url[$font]['font_subsets'] = isset( $fonts_url[$font]['font_subsets'] ) ?
							array_merge( $fonts_url[$font]['font_subsets'], $subsets ) :
							$subsets;
					}
				} else {
					$fonts_url[$font] = array( 
						'font_weights'	=>	array( $variant ),
						'font_subsets'	=>	$subsets
					);
				}
			}
		}
		
		// Now construct the google url.
		$sep = '';
		
		foreach ( $fonts_url as $key => $val ) {
			$weights_ids = $subsets_ids = '';
			$font_id = str_replace( '_', '+', $key );
			
			// Do not want duplicate data in google url.
			if ( 0 < count( $val['font_weights'] ) ) {
				$weights_ids = implode( ',', array_unique( $val['font_weights'] ) );
			}
			if ( 0 < count( $val['font_subsets'] ) ) {
				$subsets_ids = implode( ',', array_unique( $val['font_subsets'] ) );
			}
			
			$google_url .= $sep . $font_id;
			
			if ( $weights_ids ) {
				$google_url .= ':' . $weights_ids;
			}
			
			if ( $subsets_ids ) {
				$google_url .= '%26subset=' . $subsets_ids;
			}
			
			$sep = '|';
		}
		
		return $google_url;
	}
}

/**
 * 5.4.3 General Component Options
 *-----------------------------------------------------------------------*/

if ( ! function_exists( 'blogcentral_display_component_layout_options' ) ) {
	/**
	 * Construct and display options for a component layout.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Required. Name used to construct the input fields.
	 * @param string $selected Optional. Saved option for layout. 
	 * @param object $widget Optional. Widget object if displaying options for a widget.
	 */
	function blogcentral_display_component_layout_options( $name, $selected = '', & $widget = false ) {
		if ( ! isset( $name ) ) {
			return;
		}
		
		$selected = $selected ? $selected : 'layout1';
		
		$name = isset( $name ) ? esc_attr( $name ) : '';	
			
		echo "<p class='section-title div3'>";
				_e( 'Layout - choose from 3 different layouts.', BLOGCENTRAL_TXT_DOMAIN );
		echo "</p>
				<table class='form-table'>
				<tbody>
					<tr>
						<th>" . __( 'Layout', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
						<td>
							<div class='layout-float'>";
							
							for ( $i = 1; $i < 4; ++$i ) {
								echo "<input type='radio' data-blogcentral-name='$name' class='$name-layout layout-show' name='" . blogcentral_get_field_name_wrap( 'layout', "blogcentral[$name]", $widget ) . 
									"' value='layout$i'" . checked( $selected, "layout$i", false ) . " /> 
									<label>";
								_e( "Layout", BLOGCENTRAL_TXT_DOMAIN );
								echo $i;
								echo "</label><br />";
							}
							
							echo '</div>';
						// Ending <td> tag is in the display_component_layout_wraps function.
	}
}

if ( ! function_exists( 'blogcentral_display_component_layout_wraps' ) ) {
	/**
	 * Construct and display options for layout containers.
	 *
	 * These containers holds options that are specific to the layout chosen.
	 * 
	 * @since 1.0.0
	 *
	 * @param array $opts Optional. Saved options.
	 * @param object $widget Optional. Widget object if displaying options for a widget.
	 */
	function blogcentral_display_component_layout_wraps( $opts, & $widget = false ) {
		$layout = $opts ? ( isset( $opts['layout'] ) ? $opts['layout'] : 'layout1' ) : 'layout1';
		$name = isset( $opts['name'] ) ?  esc_html( $opts['name'] ) :  '';
		
		echo "<div ";
		
		if ( 'layout1' !== $layout ) {
			echo "style='display:none;' ";
		}
		
		echo "class='$name-layout-sub-layout $name-layout-sub-layout1 layout layout1 layout-wrap'>
			<div class='layout-img'>
				<img src='" . BLOGCENTRAL_THEME_URL . "/images/preview/hatback.jpg' alt='" . __( 'hat back', BLOGCENTRAL_TXT_DOMAIN ) . "' />
				<p>" . __( 'Content under media', BLOGCENTRAL_TXT_DOMAIN ) . "</p>
			</div>
		</div>
		<div ";
		
		if ( 'layout2' !== $layout ) {
			echo "style='display:none;' ";
		}
		
		echo "class='$name-layout-sub-layout $name-layout-sub-layout2 layout layout2 layout-wrap'>
			<div class='layout-img'>
				<img src='" . BLOGCENTRAL_THEME_URL . "/images/preview/hatback.jpg' alt='" . __( 'Hat Back', BLOGCENTRAL_TXT_DOMAIN ) . "' />
				<p>" . __( 'Content floated to right of media, in a two column design.', BLOGCENTRAL_TXT_DOMAIN ) . "</p>
			</div>
		</div>
		<div ";
		
		if ( 'layout3' !== $layout ) {
			echo "style='display:none;' ";
		}
		
		echo "class='$name-layout-sub-layout $name-layout-sub-layout3 layout-wrap'>" . __( 'Text Layout, no media or post format icon will be displayed.', BLOGCENTRAL_TXT_DOMAIN ) . "<br /><br />
			</div>
			</td>
			</tr>
			</tbody>
		</table>";
	}
}

if ( ! function_exists( 'blogcentral_display_main_options' ) ) {
	/**
	 * Construct and display main options for components.
	 * 
	 * @since 1.0.0
	 *
	 * @param array $opts Optional. Saved options.
	 * @param object $widget Optional. Widget object if displaying options for a widget.
	 */
	function blogcentral_display_main_options( $opts, & $widget = false ) {
		$defaults = array(
			'cols'		 		=>	'1',
			'border'			=>	'0',
			'masonry'			=>	false,
			'alternate_color'	=>	false,
		);
					
		if ( $opts ) {
			$opts = wp_parse_args( $opts, $defaults );
		} else {
			$opts = $defaults;
		}
		
		$name =  $opts ? ( isset( $opts['name'] ) ? esc_html( $opts['name'] ) : 'posts_landing' ) : 'posts_landing';
		
		$border_name = blogcentral_get_field_name_wrap( 'border', "blogcentral[$opts[name]]", $widget );
		
		blogcentral_display_custom_text_class_options( $opts, $widget );
		
			echo "<p class='section-title div3'>";
				_e( 'General Options', BLOGCENTRAL_TXT_DOMAIN );
			echo "</p>
			<table class='form-table'>
				<tbody>";
					// If displaying options for the single post page, do not display these options.
					if ( 'posts_single' !== $name ) :
			echo	"<tr>
						<th>" . __( 'How many columns?', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
						<td>
							<select name='" . blogcentral_get_field_name_wrap( 'cols', "blogcentral[$name]", $widget ) . "'>";
							blogcentral_display_cols_options( $opts['cols'] );
							echo "</select>
							<p class='instruction fixed'><small>";
								_e( 'Note: the number of columns shown may change due to the size of the screen and/or the layout chosen for the posts. Layout 2, at the most, will be displayed in 2 columns, even if you choose 3 or 4 columns.', BLOGCENTRAL_TXT_DOMAIN );
							echo "</small></p>
						</td>
					</tr>";
					endif;
			echo	"<tr>
						<th>" . __( 'Border for each post', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
						<td>
							<div class='group'>
								<input type='radio' value='0' name='" . $border_name . "'" . checked( $opts['border'], '0', false ) . " /> 
								<label>";
									_e( 'No border', BLOGCENTRAL_TXT_DOMAIN );
								echo "</label><br />
								<input type='radio' value='1' name='" . $border_name . "'" . checked( $opts['border'], '1', false ) . " /> 
								<label style='border-bottom: 3px double #eeeeee;'>";
								_e( 'Bottom Border', BLOGCENTRAL_TXT_DOMAIN );
								echo "</label>
							</div>
						</td>
					</tr>";
					// If displaying options for the single post page, do not display these options
					if ( 'posts_single' !== $name ) :
			echo	"<tr>
						<th>" . __( 'Masonry display', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
						<td>
							<div>
								<input class='displaymaster' type='checkbox' name='" . blogcentral_get_field_name_wrap( 'masonry', "blogcentral[$name]", $widget ) . "'" . checked( $opts['masonry'], 'on', false ) . " />
								<label>";
									_e( 'Display in a masonry layout', BLOGCENTRAL_TXT_DOMAIN );
								echo "</label>
							</div>
						</td>
					</tr>
					<tr>
						<th>" . __( 'Alternate background color', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
						<td>
							<div class='group'>
								<input type='checkbox' name='" . blogcentral_get_field_name_wrap( 'alternate_color', "blogcentral[$name]", $widget ) . "' class='displaymaster'" . checked( $opts['alternate_color'], 'on', false ) . " />
								<label>";
								 _e( 'Even items children will have a background color. You can customize the color by entering code in the custom css box. To target the children use this code: .alternate-color .component:nth-child(2n) .component-content-wrap { background-color: #your color here !important; }', BLOGCENTRAL_TXT_DOMAIN );
								echo "</label></div>
						</td>
					</tr>";
					endif;
			echo "</tbody>
			</table>";
		
			// If displaying options for the main posts page ie index.php, do not display these options.
			if ( 'posts_landing' !== $name && 'posts_single' !== $name ) {
				$opts['name'] = $name;
				
				blogcentral_display_query_opts( $opts, $widget );
			}
	}
}

if ( ! function_exists( 'blogcentral_display_custom_text_class_options' ) ) {
	/**
	 * Construct and display options for custom text and classes for a component.
	 *
	 * A component can have a title and a tagline that is displayed right beneath the title.
	 * 
	 * @since 1.0.0
	 *
	 * @param array $opts Optional. Saved options.
	 * @param object $widget Optional. Widget object if displaying options for a widget.
	 */
	function blogcentral_display_custom_text_class_options( $opts, & $widget = false ) {
		$defaults = array(
			'title_border'		=>	'default',
			'tagline_border'	=>	'0',
		);
		
		if ( $opts ) {
			$opts = wp_parse_args( $opts, $defaults );
		} else {
			$opts = $defaults;
		}
		
		$name = isset( $opts['name'] ) ? esc_html( $opts['name'] ) : 'posts_landing';
		$title_text = isset( $opts['title_text'] ) ? ' value="' . esc_html( $opts['title_text'] ) . '"' : '';
		$tagline_text = isset( $opts['tagline_text'] ) ? ' value="' . esc_html( $opts['tagline_text'] ) . '"' : '';
		
		$wrap_class =  $opts ? ( isset( $opts['wrap_class'] ) ? ' value="' . esc_html( $opts['wrap_class'] ) .
			'"' : '' ) : '';
		$title_class =  $opts ? ( isset( $opts['title_class'] ) ? ' value="' . esc_html( $opts['title_class'] ) .
			'"' : '' ) : '';
		
		echo "<p class='section-title div3'>";
				_e( 'Custom Text- title and tagline will be shown on the front page.', BLOGCENTRAL_TXT_DOMAIN );
		echo "</p>
			<table class='form-table'>
				<tbody>
					<tr>
						<th>" . __( 'Title', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
						<td>
							<input type='text' name='" . blogcentral_get_field_name_wrap( 'title_text', "blogcentral[$name]", $widget ) . "'" . $title_text . " />
						</td>
					</tr>
					<tr>
						<th>" . __( 'Title Border Style', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
						<td>
							<select class='header-border-select' name='" . blogcentral_get_field_name_wrap( 'title_border', "blogcentral[$name]", $widget ) . "'>
								<option value='default'" . selected( $opts['title_border'], 'default', false ) . ">" . 
								__( 'use default border', BLOGCENTRAL_TXT_DOMAIN ) . "</option>";
													
							$border_opts = blogcentral_display_border_items_select( $opts['title_border'] );
							echo $border_opts;
							
							echo "</select>";
							if ( 'default' !== $opts['title_border'] &&  ( '0' !== $opts['title_border'] ) ) :
								echo "<span class='border-ex $opts[title_border]'>$opts[title_border]</span>";
							else :
								echo "<span class='border-ex'></span>";
							endif;
							
							echo "<p class='instruction fixed'><small>";
								_e( 'Border style for the title.', BLOGCENTRAL_TXT_DOMAIN );
							echo "</small></p>
						</td>
					</tr>
					<tr>
						<th>" . __( 'Tagline Text', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
						<td>
							<input type='text' name='" . blogcentral_get_field_name_wrap( 'tagline_text', "blogcentral[$name]", $widget ) . "'" . $tagline_text . " /><p class='instruction'><small>";
							_e( 'The text below the title', BLOGCENTRAL_TXT_DOMAIN );
							echo "</small></p>
						</td>
					</tr>
					<tr>
						<th>" . __( 'Tagline Border Style', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
						<td>
							<select class='header-border-select' name='" . blogcentral_get_field_name_wrap( 'tagline_border', "blogcentral[$name]", $widget ) . "'>
							<option value='default'" . selected( $opts['tagline_border'], 'default', false ) . ">" . __( 'use default border', BLOGCENTRAL_TXT_DOMAIN ) . "</option>";
							
							$border_opts = blogcentral_display_border_items_select( $opts['tagline_border'] );
							echo $border_opts; 
							echo "</select>";
							if ( 'default' !== $opts['tagline_border'] &&  ( '0' !== $opts['tagline_border'] ) ) :
								echo "<span class='border-ex $opts[tagline_border]'>$opts[tagline_border]</span>";
							else :
								echo "<span class='border-ex'></span>";
							endif;
							
							echo "<p class='instruction fixed'><small>";
										_e( 'Border style for the tagline.', BLOGCENTRAL_TXT_DOMAIN );
								echo "</small></p>
						</td>
					</tr>
				</tbody>
			</table>";
		echo "
			<p class='section-title div3'>";
				_e( 'Custom Classes', BLOGCENTRAL_TXT_DOMAIN );

		// The wrap class will be added to the most outer div of the component.
		echo "</p>
			<table class='form-table'>
				<tbody>
					<tr>
						<th>" . __( 'Wrap Classes', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
						<td>
							<input type='text' name='" . blogcentral_get_field_name_wrap( 'wrap_class', "blogcentral[$name]", $widget ) . "'" . $wrap_class . " /><p class='instruction'><small>";
							_e( 'Default class is components-wrap, you can add more classes, separate them with a space.', BLOGCENTRAL_TXT_DOMAIN );
							echo "</small></p>
						</td>
					</tr>
					<tr>
						<th>" . __( 'Header Classes', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
						<td>
							<input type='text' name='" . blogcentral_get_field_name_wrap( 'title_class', "blogcentral[$name]", $widget ) . "'" . $title_class . " />
							<p class='instruction'><small>";
							_e( 'Separate classes with a space.', BLOGCENTRAL_TXT_DOMAIN );
							echo "</small></p>
						</td>
					</tr>
				</tbody>
			</table>";
	}
}

if ( ! function_exists( 'blogcentral_display_cols_options' ) ) {
	/**
	 * Construct and display options for the column layout of a component.
	 *
	 * @since 1.0.0
	 *
	 * @param string $selected Optional. Saved option for number of columns.
	 */
	function blogcentral_display_cols_options( $selected = 1 ) {
		for ( $i = 1; $i < 5; ++$i ) {
			echo "<option value='$i' " . selected( $selected, $i, false ) . ">";
			echo $i; 
			echo "</option>";
		}
	}
}

if ( ! function_exists( 'blogcentral_display_social_share_opts' ) ) {
	/**
	 * Construct and display options for social sharing
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Optional. Saved options.
	 * @param object $widget Optional. Widget object if displaying options for a widget.
	 */
	function blogcentral_display_social_share_opts( $opts, & $widget = false ) {
		$name = isset( $opts['name'] ) ? esc_html( $opts['name'] ) : '';
		
		if ( $name ) {
			$name = "blogcentral[$name]";
		} else {
			$name = "blogcentral";
		}
		
		echo '<input type="checkbox" class="displaymaster" name="' . blogcentral_get_field_name_wrap( 'show_social', $name, $widget ) . '"' . checked( $opts['show_social'], 'on', false ) . ' /> 
			<label>' . __( 'Display Social Share', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
			<div';
			
		if ( ! $opts['show_social'] ) {
			echo ' style="display:none;"';
		}
		
		echo ' class="options-section hideshow">
			<br /><label>' . __( 'Title', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
			<input type="text" name="' . blogcentral_get_field_name_wrap( 'share_title', $name, $widget ) . '"';
		
		if ( isset( $opts['share_title'] ) ) {
			echo ' value="' . esc_attr( $opts['share_title'], BLOGCENTRAL_TXT_DOMAIN ) . '"';
		}
		
		echo '/>
		</div>';
	}
}

/**
 * 5.4.4 Posts Options
 *-----------------------------------------------------------------------*/

if ( ! function_exists( 'blogcentral_display_options_posts_general' ) ) {
	/**
	 * Display general options for posts
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Optional. Saved options, if any.
	 * @param $widget Optional. If set then will use the $widget object's methods
	 * blogcentral_get_name and get_id to construct the name and id of the input field, respectively.
	 */
	function blogcentral_display_options_posts_general( $opts, & $widget = false ) {
		if ( ! isset( $opts ) ) {
			$opts['sticky_display'] = 'on';
		}
		
		echo '<table class="form-table">
				<tbody>
					<tr>
						<th>' . __( 'Featured Post Image(s) Size', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<div class="group">
								<input type="radio" value="default" name="blogcentral[posts_general][img_size]"' . checked( $opts['img_size'], 'default', false ) . ' class="displaymaster-img" />
								<label>' . __( 'Use the dimensions of the image', BLOGCENTRAL_TXT_DOMAIN ) . '</label><br />
								<input type="radio" value="scale" name="blogcentral[posts_general][img_size]"' . checked( $opts['img_size'], 'scale', false ) . ' class="displaymaster-img" />
								<label>' . __( 'Scale the image to fit its container. Width will be 100% and height=auto', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
							</div>
						</td>
					</tr> 
					<tr>
						<th>' . __( 'Sticky Posts Display', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td><input type="checkbox" name="blogcentral[posts_general][sticky_display]"' . checked( $opts['sticky_display'], 'on', false ) . ' />
						<label>' . __( 'If using a multiple column layout for posts display, check this box to display sticky posts full width.', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
						</td>
					</tr> 
					<tr>
						<th>' . __( 'Default Icon', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<label>' . __( 'Choose an icon to represent the standard, aside, chat, and status post formats. Leave blank to not use an icon.', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
							<input type="text"';
							if ( isset( $opts['standard_icon'] ) ) {
								echo ' value="' . esc_attr( $opts['standard_icon'] ) . '"';
							}
							echo ' class="blogcentral-icon-field" name="blogcentral[posts_general][standard_icon]" />
						</td>
					</tr> 
					<tr>
						<th>' . __( 'Image Icon', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<label>' . __( 'Choose an icon to represent the image post format. Leave blank to not use an icon.', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
							<input type="text"';
							if ( isset( $opts['image_icon'] ) ) {
								echo ' value="' . esc_attr( $opts['image_icon'] ) . '"';
							}
							echo ' class="blogcentral-icon-field" name="blogcentral[posts_general][image_icon]" />
						</td>
					</tr> 
					<tr>
						<th>' . __( 'Audio Icon', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<label>' . __( 'Choose an icon to represent the audio post format. Leave blank to not use an icon.', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
							<input type="text"';
							if ( isset( $opts['audio_icon'] ) ) {
								echo ' value="' . esc_attr( $opts['audio_icon'] ) . '"';
							}
							echo ' class="blogcentral-icon-field" name="blogcentral[posts_general][audio_icon]" />
						</td>
					</tr> 
					<tr>
						<th>' . __( 'Video Icon', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<label>' . __( 'Choose an icon to represent the video post format. Leave blank to not use an icon.', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
							<input type="text"';
							if ( isset( $opts['video_icon'] ) ) {
								echo ' value="' . esc_attr( $opts['video_icon'] ) . '"';
							}
							echo ' class="blogcentral-icon-field" name="blogcentral[posts_general][video_icon]" />
						</td>
					</tr> 
					<tr>
						<th>' . __( 'Gallery Icon', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<label>' . __( 'Choose an icon to represent the gallery post format. Leave blank to not use an icon.', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
							<input type="text"';
							if ( isset( $opts['gallery_icon'] ) ) {
								echo ' value="' . esc_attr( $opts['gallery_icon'] ) . '"';
							}
							echo ' class="blogcentral-icon-field" name="blogcentral[posts_general][gallery_icon]" />
						</td>
					</tr> 
					<tr>
						<th>' . __( 'Link Icon', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<label>' . __( 'Choose an icon to represent the link post format. Leave blank to not use an icon.', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
							<input type="text"';
							if ( isset( $opts['link_icon'] ) ) {
								echo ' value="' . esc_attr( $opts['link_icon'] ) . '"';
							}
							echo ' class="blogcentral-icon-field" name="blogcentral[posts_general][link_icon]" />
						</td>
					</tr> 
					<tr>
						<th>' . __( 'Quote Icon', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<label>' . __( 'Choose an icon to represent the quote post format. Leave blank to not use an icon.', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
							<input type="text"';
							if ( isset( $opts['quote_icon'] ) ) {
								echo ' value="' . esc_attr( $opts['quote_icon'] ) . '"';
							}
							echo ' class="blogcentral-icon-field" name="blogcentral[posts_general][quote_icon]" />
						</td>
					</tr> 
					<tr>
						<th>' . __( 'Author Icon', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<label>' . __( 'Choose Icon to be shown with the author\'s name. Leave blank to not use an icon.', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
							<input type="text"';
							if ( isset( $opts['author_icon'] ) ) {
								echo ' value="' . esc_attr( $opts['author_icon'] ) . '"';
							}
							echo ' class="blogcentral-icon-field" name="blogcentral[posts_general][author_icon]" />
						</td>
					</tr> 
					<tr>
						<th>' . __( 'Date Icon', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<label>' . __( 'Choose Icon to be shown with the post date', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
							<input type="text"';
							if ( isset( $opts['date_icon'] ) ) {
								echo ' value="' . esc_attr( $opts['date_icon'] ) . '"';
							}
							echo ' class="blogcentral-icon-field" name="blogcentral[posts_general][date_icon]" />
						</td>
					</tr> 
					<tr>
						<th>' . __( 'Categories List Icon', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<label>' . __( 'Choose Icon to be shown with the categories list', BLOGCENTRAL_TXT_DOMAIN ) .
							'</label>
							<input type="text"';
							if ( isset( $opts['categories_icon'] ) ) {
								echo ' value="' . esc_attr( $opts['categories_icon'] ) . '"';
							}
							echo ' class="blogcentral-icon-field" name="blogcentral[posts_general][categories_icon]" />
						</td>
					</tr> 
					<tr>
						<th>' . __( 'Tags List Icon', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<label>' . __( 'Choose Icon to be shown with the tags list', BLOGCENTRAL_TXT_DOMAIN ) .
							'</label>
							<input type="text"';
							if(  isset( $opts['tags_icon'] ) ) {
								echo ' value="' . esc_attr( $opts['tags_icon'] ) . '"';
							}
							echo ' class="blogcentral-icon-field" name="blogcentral[posts_general][tags_icon]" />
						</td>
					</tr> 
					<tr>
						<th>' . __( 'Comments Icon', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<label>' . __( 'Choose Icon to be shown with the comment number', BLOGCENTRAL_TXT_DOMAIN ) .
							'</label>
							<input type="text"';
							if ( isset( $opts['comments_icon'] ) ) {
								echo ' value="' . esc_attr( $opts['comments_icon'] ) . '"';
							}
							echo ' class="blogcentral-icon-field" name="blogcentral[posts_general][comments_icon]" />
						</td>
					</tr>
				</tbody>
			</table>';			
	}
}

if ( ! function_exists( 'blogcentral_display_options_posts_landing' ) ) {
	/**
	 * Display the options for the main posts displayed on the front, archives, categories, tags, author, and search pages
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Optional. Saved options, if any.
	 * @param $widget Optional. If set then will use the $widget object's methods blogcentral_get_name and get_id to 
	 * construct the name and id of the input field, respectively.
	 */
	function blogcentral_display_options_posts_landing( $opts, & $widget = false ) {
		blogcentral_display_component_layout_options( 'posts_landing', $opts['layout'] );
		
		$opts['name'] = 'posts_landing';
		
		blogcentral_display_component_layout_wraps( $opts, $widget ); 
		
		blogcentral_display_main_options( $opts, $widget );
		blogcentral_display_posts_specific_opts( $opts, $widget );
	}
}

if ( ! function_exists( 'blogcentral_display_options_posts_single' ) ) {
	/**
	 * Display options for the single post page
	 *
	 * Can use the same options set for the posts landing page, or override them.
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Optional. Saved options, if any.
	 */
	function blogcentral_display_options_posts_single( $opts ) {
		echo "<div>
				<br /><input class='displaymaster' type='checkbox' name='" . blogcentral_get_field_name_wrap( 'override_landing', "blogcentral[posts_single]", $widget ) . "'" . checked( $opts['override_landing'], 'on', false ) . " /> 
				<label>";
				_e( 'Override posts listing settings. If checked none of the settings for the listing page will be applied to the single post page.', BLOGCENTRAL_TXT_DOMAIN );
		echo "</label><div ";
		
		if ( ! $opts['override_landing'] ) {
			echo "style='display:none;'";
		}
		
		echo " class='hideshow'>";
		
		blogcentral_display_component_layout_options( 'posts_single', $opts['layout'] );
		
		$opts['name'] = 'posts_single';
		
		blogcentral_display_component_layout_wraps( $opts ); 
		blogcentral_display_main_options( $opts );
		blogcentral_display_posts_specific_opts( $opts );
		echo '</div></div>';						
	}
}

if ( ! function_exists( 'blogcentral_display_posts_specific_opts' ) ) {
	/**
	 * Construct and display options specific to a posts component
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Optional. Saved options.
	 * @param object $widget Optional. Widget object if displaying options for a widget.
	 */
	function blogcentral_display_posts_specific_opts( $opts, & $widget = false ) {
		$name = isset( $opts['name'] ) ? esc_html( $opts['name'] ) : '';
			
		echo '<p class="section-title div3">' . __( 'Post Data', BLOGCENTRAL_TXT_DOMAIN ) .
			'</p> 
			<table class="form-table">
				<tbody>';
			if ( 'posts_single' !== $name ) {
		echo		'<tr>
						<th>' . __( 'Title', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<div>
								<input type="checkbox" class="displaymaster" name="' . blogcentral_get_field_name_wrap( 'show_title', "blogcentral[$name]", $widget ) . '"' . checked( $opts['show_title'], 'on', false ) . ' /> 
								<label>' . __( 'Show post title', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
							</div>
						</td>
					</tr>
					<tr>
						<th>' . __( 'Post Content', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<input type="checkbox" name="' . blogcentral_get_field_name_wrap( 'show_content', "blogcentral[$name]", $widget ) . '"' . checked( $opts['show_content'], 'on', false ) . ' /> 
							<label>' . __( 'Show Content', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
						</td>
					</tr>';
		}
		echo		'<tr>
						<th>' . __( 'Author', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<input type="checkbox" name="' . blogcentral_get_field_name_wrap( 'show_author', "blogcentral[$name]", $widget ) . '"' . checked( $opts['show_author'], 'on', false ) . ' /> 
							<label>' . __( 'Show Author', BLOGCENTRAL_TXT_DOMAIN ) . 
							'</label>
						</td>
					</tr>
					<tr>
						<th>' . __( 'Post Date', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<input type="checkbox" name="' . blogcentral_get_field_name_wrap( 'show_date', "blogcentral[$name]", $widget ) . '"' . checked( $opts['show_date'], 'on', false ) . ' /> 
							<label>' . __('Show Date', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
						</td>
					</tr>
					<tr>
						<th>' . __( 'Categories', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<input type="checkbox" name="' . blogcentral_get_field_name_wrap( 'show_categories', "blogcentral[$name]", $widget ) . '"' . checked( $opts['show_categories'], 'on', false ) . ' />
							<label>' . __( 'Show Categories', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
						</td>
						
					</tr>
					<tr>
						<th>' . __( 'Tags', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<input type="checkbox" name="' . blogcentral_get_field_name_wrap( 'show_tags', "blogcentral[$name]", $widget ) . '"' . checked( $opts['show_tags'], 'on', false ) . ' /> 
							<label>' . __( 'Show Tags', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
						</td>
					</tr>
					<tr>
						<th>' . __( 'Stats', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<input type="checkbox" name="' . blogcentral_get_field_name_wrap( 'show_stats', "blogcentral[$name]", $widget ) . '"' . checked( $opts['show_stats'], 'on', false ) . ' /> 
							<label>' . __( 'Show Comments', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
						</td>
					</tr>
					<tr>
						<th>' . __( 'Social Share', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>';
							blogcentral_display_social_share_opts( $opts, $widget ); 
				echo	 '</td>
					</tr>
					<tr>
						<th>' . __( 'Show Icons', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<input type="checkbox" name="' . blogcentral_get_field_name_wrap( 'show_icons', "blogcentral[$name]", $widget ) . '"' . checked( $opts['show_icons'], 'on', false ) . ' /> 
							<label>' . __( 'Show icons - if disabled, will not show any icons.', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
						</td>
					</tr>
					<tr>
						<th>' . __( 'Post Meta Layout', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<div class="options-section">
								<p>';
									_e( 'The post meta includes the author, date, categories, tags, and comment number.', BLOGCENTRAL_TXT_DOMAIN );
								echo '</p>
								<div class="group">
									<input type="radio" name="' . blogcentral_get_field_name_wrap( 'post_meta_layout', "blogcentral[$name]", $widget ) . '" value="1"' . checked( $opts['post_meta_layout'], '1', false ) . ' /> 
									<label>' . __( "Layout 1", BLOGCENTRAL_TXT_DOMAIN ) . '</label><br />
									<img src="' . BLOGCENTRAL_THEME_URL . '/images/preview/postmeta-layout1.png" alt="' . __( 'Layout 1', BLOGCENTRAL_TXT_DOMAIN ) . '" /><br /><br />
									<input type="radio" name="' . blogcentral_get_field_name_wrap( 'post_meta_layout', "blogcentral[$name]", $widget ) . '" value="2"' . checked( $opts['post_meta_layout'], '2', false ) . ' /> 
									<label>' . __( "Layout 2", BLOGCENTRAL_TXT_DOMAIN ) . '</label><br />
									<img src="' . BLOGCENTRAL_THEME_URL . '/images/preview/postmeta-layout2.png" alt="' . __( 'Layout 2', BLOGCENTRAL_TXT_DOMAIN ) . '" /><br /><br />
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<th>' . __( 'Gallery Posts', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<div class="group">
								<input type="checkbox" name="' . blogcentral_get_field_name_wrap( 'gallery_slideshow', "blogcentral[$name]", $widget ) . '"' . checked( $opts['gallery_slideshow'], 'on', false ) . ' /> 
								<label>' . __( 'Show gallery posts featured images as a slideshow. The 4bzCore plugin must be installed and activated.', BLOGCENTRAL_TXT_DOMAIN ) . '</label></div>
						</td>
					</tr>
					<tr>
						<th>' . __( 'Quote Posts Layout', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<div class="options-section">
								<p>';
									_e( 'Layout used for posts of format quote.', BLOGCENTRAL_TXT_DOMAIN );
								echo '</p>
								<div class="group">
									<input type="radio" name="' . blogcentral_get_field_name_wrap( 'quote_layout', "blogcentral[$name]", $widget ) . '" value="1"' . checked( $opts['quote_layout'], '1', false ) . ' /> 
									<label>' . __( "Layout 1 - default, show like all other post formats", BLOGCENTRAL_TXT_DOMAIN ) . '</label><br />
									<input type="radio" name="' . blogcentral_get_field_name_wrap( 'quote_layout', "blogcentral[$name]", $widget ) . '" value="2"' . checked( $opts['quote_layout'], '2', false ) . ' /> 
									<label>';
									_e( "Layout 2", BLOGCENTRAL_TXT_DOMAIN );
								echo '</label>
								<img src="' . BLOGCENTRAL_THEME_URL . '/images/preview/quote-layout2.png" alt="' . __( 'Quote layout 2', BLOGCENTRAL_TXT_DOMAIN ) . '" /><br /><br />
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<th>' . __( 'Post Format Icon', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
						<td>
							<input type="checkbox" name="' . blogcentral_get_field_name_wrap( 'display_format_icon', "blogcentral[$name]", $widget ) . '"' . checked( $opts['display_format_icon'], 'on', false ) . ' /> 
							<label>' . __( 'Display post format icon', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
						</td>
					</tr>
				</tbody>
			</table>';
	}
}

if ( ! function_exists( 'blogcentral_display_query_opts' ) ) {
	/**
	 * Construct and display query options for a posts component
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Optional. Saved options.
	 * @param object $widget Optional. Widget object if displaying options for a widget.
	 */
	function blogcentral_display_query_opts( $opts, & $widget = false ) {
		$name = isset( $opts['name'] ) ? esc_html( $opts['name'] ) : '';
		
		echo '<p class="section-title div3">' .  __( 'Query Options', BLOGCENTRAL_TXT_DOMAIN ) . '</p> 
			<table class="form-table">
			<tbody>
				<tr>
					<th>' . __( 'Ignore Sticky Posts', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
					<td>
						<input type="checkbox" name="' . blogcentral_get_field_name_wrap( 'ignore_sticky', "blogcentral[$name]", $widget ) . '"' . checked( $opts['ignore_sticky'], 'on', false );
					echo ' /> <label>' . __( 'Check this box if you do not want sticky posts to be placed first.', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
					</td>
				</tr>
				<tr>
					<th>' . __( 'Show only sticky posts', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
					<td>
						<input type="checkbox" name="' . blogcentral_get_field_name_wrap( 'sticky_only', "blogcentral[$name]", $widget ) . '"' . checked( $opts['sticky_only'], 'on', false ) . ' />
						<label>' . __( 'Show only sticky posts', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
					</td>
				</tr>
				<tr>
					<th>' . __( 'Number of posts to display', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
					<td>
						<input type="text" name="' . blogcentral_get_field_name_wrap( 'limit', "blogcentral[$name]", $widget ) . '"';
						if ( isset( $opts["limit"] ) ) {
							echo ' value="' . esc_attr( $opts['limit'] ) . '"';
						}
						echo ' /><p class="instruction"><small>';
						_e( 'Default is to display all posts.', BLOGCENTRAL_TXT_DOMAIN );
		echo 			'</small></p>
					</td>
				</tr>
			</tbody>
		</table>';
	}
}

/**
 * 5.4.5 Shortcodes + Widgets Options: Not Post Based
 *-----------------------------------------------------------------------*/
if ( ! function_exists( 'blogcentral_display_contact_info_layout_opts' ) ) {
	/**
	 * Construct and display options specific to the contact information shortcode and widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Optional. Saved options.
	 * @param object $widget Optional. Widget object if displaying options for a widget.
	 */
	function blogcentral_display_contact_info_layout_opts( $opts = null, & $widget = false ) {	
		$name = $opts['name'] = isset( $opts['name'] ) ? esc_html( $opts['name'] ) : 'contact_info';
		?>
		
		<label><?php _e( 'Layout', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
		<div class="group">
			<input type="radio" value="1" name="<?php echo blogcentral_get_field_name_wrap( 'contact_info_layout', "blogcentral[$name]", $widget ); ?>"<?php checked( $opts['contact_info_layout'], '1' ); ?> />
			<label><?php  _e( 'horizontal', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
			<input type="radio" value="2" name="<?php echo blogcentral_get_field_name_wrap( 'contact_info_layout', "blogcentral[$name]", $widget ); ?>"<?php checked( $opts['contact_info_layout'], '2' ); ?> />
			<label><?php  _e( 'vertical', BLOGCENTRAL_TXT_DOMAIN ); ?></label>
		</div>

		<div>
			<div class="group">
				<input type="checkbox" name="<?php echo blogcentral_get_field_name_wrap( 'show_address', "blogcentral[$name]", $widget ); ?>"<?php checked( $opts['show_address'], 'on' ); ?> /> 
				<label><?php _e( 'Display address', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
				<input type="checkbox" name="<?php echo blogcentral_get_field_name_wrap( 'show_phone', "blogcentral[$name]", $widget ); ?>"<?php checked( $opts['show_phone'], 'on' ); ?> /> 
				<label><?php _e( 'Display phone', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
				<input type="checkbox" name="<?php echo blogcentral_get_field_name_wrap( 'show_email', "blogcentral[$name]", $widget ); ?>"<?php checked( $opts['show_email'], 'on' ); ?> /> 
				<label><?php _e( 'Display email', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
				<input type="checkbox" name="<?php echo blogcentral_get_field_name_wrap( 'show_url', "blogcentral[$name]", $widget ); ?>"<?php checked( $opts['show_url'], 'on' ); ?> /> 
				<label><?php _e( 'Display website', BLOGCENTRAL_TXT_DOMAIN ); ?></label><br />
				<?php blogcentral_display_social_share_opts( $opts, $widget ); ?>
			</div>		
		</div>
		<?php
	}
}

/**
 * 5.5 Admin Ajax
 *-----------------------------------------------------------------------*/ 
if ( ! function_exists( 'blogcentral_font_options' ) ) {
	/**
	 * Serves an ajax request to retrieve the font options when a font is selected
	 *
	 * @since 1.0.0
	 *
	 * @return string json encoded font options.
	 */
	function blogcentral_font_options() {
		if ( ! isset( $_REQUEST['nonce'] ) ) {
			return;
		}
		
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], "blogcentral-font-select-nonce" ) ) {
			exit( __( "No swiping", BLOGCENTRAL_TXT_DOMAIN ) );
		}
		
		$result = array();
			
		$json =  array();
		$font = isset( $_POST['font'] ) ? $_POST['font'] : '';
		$name = isset( $_POST['name'] ) ? $_POST['name'] : '';
		$type = isset( $_POST['type'] ) ? $_POST['type'] : '';
		
		// Need to call the functions to construct the variants and subsets.
		ob_start();
		blogcentral_display_options_variants( '', $font, 'regular', $name, $type );
		
		$result['variants'] = ob_get_clean();
		
		// Only google fonts may have subsets.
		if ( 'google_fonts' === $type ) {
			ob_start();
			blogcentral_display_options_subsets( '', $font, '', $name );
			$result['subsets'] = ob_get_clean();
		}
		
		echo json_encode( $result );
		die();
	}
}

if ( ! function_exists( 'blogcentral_display_template_ajax' ) ) {
	/**
	 * Serves an ajax request to retrieve the template when one is selected
	 *
	 * @since 1.0.0
	 *
	 * @return string json encoded template.
	 */
	function blogcentral_display_template_ajax() {
		if ( ! isset( $_REQUEST['nonce'] ) ) {
			return;
		}
		
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], "blogcentral-display-template-nonce" ) ) {
			exit( __( "No swiping", BLOGCENTRAL_TXT_DOMAIN ) );
		}   
		
		$template = isset( $_POST['template'] ) ? $_POST['template'] : '1';
		
		$result = array();
		
		ob_start();
		
		switch( $template ) {
			case '1':
				// Get the template.
				include( locate_template( 'templates/slide-template-1.php', false, false ) );	
				break;
		
			case '2':
				// Get the template.
				include( locate_template( 'templates/slide-template-2.php', false, false ) );		
				break;
			case '3':
				// Get the template.				
				include( locate_template( 'templates/slide-template-3.php', false, false ) );	
				break;
		}
		
		$result['template'] = ob_get_clean();
		
		echo json_encode( $result );
		die();
	}
}

if ( ! function_exists( 'blogcentral_no_go' ) ) {
	/**
	 * Admin ajax error function
	 *
	 * @since 1.0.0
	 */
	function blogcentral_no_go() {
		echo __( "No Swiping", BLOGCENTRAL_TXT_DOMAIN );
		die();
	}
}

/**
 * 6.0 Front End Functions
 *-----------------------------------------------------------------------*/

/**
 * 6.1 Enqueue Scripts + Inline Scripts
 *-----------------------------------------------------------------------*/

if ( ! function_exists( 'blogcentral_enqueue_scripts' ) ) {
	/**
	 * Enqueue all required scripts and styles for the front-end
	 *
	 * @since 1.0.0
	 */
	function blogcentral_enqueue_scripts() {
		global $blogcentral_opts;
		
		// Enqueue jquery and jquery ui core.
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		
		/*
		 * Add JavaScript to pages with the comment form to support sites with
		 * threaded comments.
		 */
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
		
		// Enqueue masonry.
		wp_enqueue_script( 'masonry', BLOGCENTRAL_THEME_URL . '/js/masonry.pkgd.min.js', array( 'jquery' ), false, false );
		
		// Enqueue waypoints.
		wp_enqueue_script( 'waypoints', BLOGCENTRAL_THEME_URL . '/js/jquery.waypoints.min.js', array( 'jquery' ), false, false );
		
		// Enqueue imagesloaded.
		wp_enqueue_script( 'imagesloaded', BLOGCENTRAL_THEME_URL . '/js/imagesloaded.pkgd.min.js', array( 'jquery' ), false, true );
		
		// Enqueue front end script.
		wp_enqueue_script( BLOGCENTRAL_THEME_PREFIX . '-frontend', BLOGCENTRAL_THEME_URL . '/js/front-end.js', array( 'jquery' ), false, true );
		
		// Enqueue font awesome icons
		wp_enqueue_style( 'font-awesome', BLOGCENTRAL_THEME_URL . '/font-awesome/css/font-awesome.min.css' );
		
		global $fourbzcore_plugin;
		
		if ( isset( $fourbzcore_plugin ) ) {
			// Enqueue flexslider.
			wp_enqueue_script( 'flexslider', BLOGCENTRAL_THEME_URL .'/js/jquery.flexslider-min.js', array( 'jquery' ), false, true );
			
			// Enqueue jquery easing plugin.
			wp_enqueue_script( 'easing', BLOGCENTRAL_THEME_URL . '/js/jquery.easing.min.js', array( 'jquery' ), false, true );
		
			// Enqueue flexslider style.
			wp_enqueue_style( 'flexslider', BLOGCENTRAL_THEME_URL . '/css/flexslider.css' );
			
			// Enqueue flexslider style.
			wp_enqueue_style( 'blogcentral-4bzcore', BLOGCENTRAL_THEME_URL . '/css/4bzcore.css' );
		}
										
		// Localize script
		$localize = array();
		
		if ( isset( $blogcentral_opts['header_sticky'] ) ) {
			$localize['header_sticky'] = $blogcentral_opts['header_sticky'];
		}
		
		wp_localize_script( BLOGCENTRAL_THEME_PREFIX . '-frontend' , "blogcentral_object", $localize );
		
		// Load the main stylesheet.
		wp_enqueue_style( BLOGCENTRAL_THEME_PREFIX . '-style', get_stylesheet_uri(), array(), '2015-02-10' );

		// Enqueue inline css.
		$inline_css = blogcentral_enqueue_inline_css();
		wp_add_inline_style( 'blogcentral-style', $inline_css );
		
		// Load the responsive stylesheet.
		wp_enqueue_style( 'blogcentral-responsive', BLOGCENTRAL_THEME_URL . '/css/responsive.css' );
		
		
		// Enqueue google fonts.
		$protocol = is_ssl() ? 'https' : 'http';
		
		if ( isset( $blogcentral_opts['google_fonts_url'] ) ) {
			wp_enqueue_style( 'blogcentral-google-fonts', "$protocol://fonts.googleapis.com/css?family=" . urlencode( $blogcentral_opts['google_fonts_url'] ), array(), null );
		}
	}
}

if ( ! function_exists( 'blogcentral_enqueue_inline_css' ) ) {
	/**
	 * Enqueue inline css based on the options set on the theme's options page
	 *
	 * Check colors against default values, and if the same, do not output inline css.
	 *
	 * @since 1.0.0
	 */
	function blogcentral_enqueue_inline_css() {
		// Get the global variable that holds the theme options.
		global $blogcentral_opts;
		
		// Initialize variables.
		$header_style = $header_link_style = $menu_style = $menu_lnk_style = $menu_lnk_active_style =
			$submenu_style = $submenu_lnk_style = $body_style = $link_style = $link_hover_style = 
			$link_visited_style = $page_header_style = $page_header_style_color = $main_content_style = $headers_style = 
			$h1_style = $h2_style = $h3_style = $h4_style = $h5_style = $h6_style = $logo_style =
			$header_extra_style = $footer_extra_style = $alternate_color_style = $custom_css = $inline_css = '';
		
		// Construct the html fragments for inline css.
		
		// Inline css for the page header.
		if ( ! empty( $blogcentral_opts['page_header'] ) && ! is_active_sidebar( 'precontent-widget' ) ) {
			if ( ! empty( $blogcentral_opts['page_header_color'] ) ) {
				$page_header_style_color = 'color:' . esc_html( $blogcentral_opts['page_header_color'] ) . ';';
			}
			
			if ( ! empty( $blogcentral_opts['page_header_bck_img'] ) ) {
				$page_header_style .= ' border:none; background-size: cover; background-image:url(' . 
					esc_url( $blogcentral_opts['page_header_bck_img'] ) . ');';
			}
			if ( ! empty( $blogcentral_opts['page_header_bck_img_attachment'] ) ) {
				$page_header_style .= 'background-attachment:' . esc_html( $blogcentral_opts['page_header_bck_img_attachment'] ) .
				';';
			}
			if ( ! empty( $blogcentral_opts['page_header_bck_img_position'] ) ) {
				$page_header_style .= 'background-position:' . esc_html( $blogcentral_opts['page_header_bck_img_position'] ) . 
					';';
			}
			if ( ! empty( $blogcentral_opts['page_header_bck_img_repeat'] ) ) {
				$page_header_style .= 'background-repeat:' . esc_html( $blogcentral_opts['page_header_bck_img_repeat'] ) . ';';
			}
			
			if ( ! empty( $blogcentral_opts['page_header_height'] ) ) {
				$page_header_style .= 'height:' . esc_html( $blogcentral_opts['page_header_height'] ) . ';';
			}
		}
		
		if ( ! empty( $blogcentral_opts['custom_css'] ) ) {
			$custom_css = stripslashes( $blogcentral_opts['custom_css'] );
		}
		
		// Inline css for the logo.
		if ( ! empty( $blogcentral_opts['logo'] ) &&  ! empty( $blogcentral_opts['logo_width'] ) ) {
			$logo_style .= "max-width:" . intval($blogcentral_opts['logo_width']) . "px;";
		}
		
		// Inline css for the body font.
		if ( ! empty( $blogcentral_opts['fonts']['body_txt_font_family'] ) && 'no_font' !== 
			$blogcentral_opts['fonts']['body_txt_font_family'] ) {
			$body_style .= 'font-family:"' . $blogcentral_opts['fonts']['body_txt_font_family'] . '";';
			
			if ( ! empty( $blogcentral_opts['fonts']['body_txt_font_weight'] ) ) {
				$weight = $blogcentral_opts['fonts']['body_txt_font_weight'];
				$font_weight = blogcentral_construct_font_weight_style( $weight );
				$body_style .= $font_weight['weight'];
				
				if ( ! empty( $font_weight['style'] ) ) {
					$body_style .= $font_weight['style'];
				}
			}
		}
		
		// Inline css for the main menu font
		if ( ! empty( $blogcentral_opts['fonts']['main_menu_font_family'] ) && 'no_font' !==
			$blogcentral_opts['fonts']['main_menu_font_family'] ) {
			$menu_style .= 'font-family:"' . $blogcentral_opts['fonts']['main_menu_font_family']. '";';
			$menu_lnk_style .= 'font-family:"' . $blogcentral_opts['fonts']['main_menu_font_family'] . '";';
			
			if ( ! empty( $blogcentral_opts['fonts']['main_menu_font_weight'] ) ) {
				$weight = $blogcentral_opts['fonts']['main_menu_font_weight'];
				$font_weight = blogcentral_construct_font_weight_style( $weight );
				$menu_style .= $font_weight['weight'];
				$menu_lnk_style .= $font_weight['weight'];
				
				if ( ! empty( $font_weight['style'] ) ) {
					$style = $font_weight['style'];
					$menu_style .= $style;
					$menu_lnk_style .= $style;
				}
			}
		}
		
		// Inline css for the headers font.
		if ( ! empty( $blogcentral_opts['fonts']['headers_font_family'] ) && 'no_font' !== 
			$blogcentral_opts['fonts']['headers_font_family'] ) {
			$headers_style .= 'font-family:"' . $blogcentral_opts['fonts']['headers_font_family'] . '";';
			
			if ( ! empty( $blogcentral_opts['fonts']['headers_font_weight'] ) ) {
				$weight = $blogcentral_opts['fonts']['headers_font_weight'];
				$font_weight = blogcentral_construct_font_weight_style( $weight );
				$headers_style .= $font_weight['weight'];
				
				if ( ! empty( $font_weight['style'] ) ) {
					$style = $font_weight['style'];
					$headers_style .= $style;
				}
			}
		}
		
		// Inline css for the body.
		if ( ! empty( $blogcentral_opts['body_txt_color'] ) && '#777777' !== $blogcentral_opts['body_txt_color'] ) {
			$body_style .= 'color:' . $blogcentral_opts['body_txt_color'] . ';';
		}
		
		if ( ! empty( $blogcentral_opts['body_lnk_color'] ) && '#0071d8' !== $blogcentral_opts['body_lnk_color'] ) {
			$link_style .= 'color:' . $blogcentral_opts['body_lnk_color'] . ';';
		}
		
		if ( ! empty( $blogcentral_opts['body_lnk_hover_color'] ) ) {
			$link_hover_style .= 'color:' . $blogcentral_opts['body_lnk_hover_color'] . ' !important;';
		}
		
		if ( ! empty( $blogcentral_opts['body_lnk_visited_color'] ) ) {
			$link_visited_style .= 'color:' . $blogcentral_opts['body_lnk_visited_color'] . ';';
		}
		
		// Inline css for the main content.
		if ( ! empty( $blogcentral_opts['main_content_back_color'] ) ) {
			$main_content_style .= 'background-color:' . $blogcentral_opts['main_content_back_color'] . ';';
		}
		
		// Inline css for the header.
		if ( ! empty( $blogcentral_opts['header_back_color'] ) && '#ffffff' !== $blogcentral_opts['header_back_color'] ) {
			$header_style .= 'background-color:' . $blogcentral_opts['header_back_color'] . ';';
		}
		
		if ( ! empty( $blogcentral_opts['header_lnk_color'] ) && '#777777' !== $blogcentral_opts['header_lnk_color'] ) {
			$header_link_style .= 'color:' . $blogcentral_opts['header_lnk_color'] . ';';
		}
		
		if ( ! empty( $blogcentral_opts['header_txt_color'] ) && '#777777' !== $blogcentral_opts['header_txt_color'] ) {
			$header_style .= 'color:' . $blogcentral_opts['header_txt_color'] . ';';
			$header_extra_style .= 'color:' . $blogcentral_opts['header_txt_color'] . ';';
		}
		
		// Inline css for the menu.
		if ( ! empty( $blogcentral_opts['menu_lnk_color'] ) ) {
			$menu_lnk_style .= 'color:' . $blogcentral_opts['menu_lnk_color'] . ';';
			$menu_style .= 'color:' .  $blogcentral_opts['menu_lnk_color'] . ';';
		}
		
		if ( ! empty( $blogcentral_opts['menu_back_color'] ) ) {
			$menu_style .= 'background-color:' . $blogcentral_opts['menu_back_color'] . ';';
		}
		
		if ( ! empty( $blogcentral_opts['menu_active_color'] ) ) {
			$menu_lnk_active_style .= 'color:' . $blogcentral_opts['menu_active_color'] . ' !important;';
		}
		
		// Inline css for the submenu.
		if ( ! empty( $blogcentral_opts['submenu_back_color'] ) && '#ededed' !== $blogcentral_opts['submenu_back_color'] ) {
			$submenu_style .= 'background-color:' . $blogcentral_opts['submenu_back_color'] . ';';
		}
		
		if ( ! empty( $blogcentral_opts['submenu_lnk_color'] ) && '#222222' !== $blogcentral_opts['submenu_lnk_color'] ) {
			$submenu_lnk_style .= 'color:' . $blogcentral_opts['submenu_lnk_color'] . ';';
		}
		
		// Inline css for the header tags.
		if ( ! empty( $blogcentral_opts['h1']['size'] ) ) {
			$h1_style .= 'font-size:' . $blogcentral_opts['h1']['size'] . ' !important;';
		}
		if ( ! empty( $blogcentral_opts['h1']['color'] ) && '#222222' !== $blogcentral_opts['h1']['color'] ) {
			$h1_style .= 'color:' . $blogcentral_opts['h1']['color'] . ';';
		}
		if ( ! empty( $blogcentral_opts['h2']['size'] ) ) {
			$h2_style .= 'font-size:' . $blogcentral_opts['h2']['size'] . ' !important;';
		}
		if ( ! empty( $blogcentral_opts['h2']['color'] ) && '#222222' !== $blogcentral_opts['h2']['color'] ) {
			$h2_style .= 'color:' . $blogcentral_opts['h2']['color'] . ' !important;';
		}
		if ( ! empty( $blogcentral_opts['h3']['size'] ) ) {
			$h3_style .= 'font-size:' . $blogcentral_opts['h3']['size'] . ' !important;';
		}
		if ( ! empty( $blogcentral_opts['h3']['color'] ) && '#222222' !== $blogcentral_opts['h3']['color'] ) {
			$h3_style .= 'color:' . $blogcentral_opts['h3']['color'] . ' !important;';
		}
		if ( ! empty( $blogcentral_opts['h4']['size'] ) ) {
			$h4_style .= 'font-size:' . $blogcentral_opts['h4']['size'] . ' !important;';
		}
		if ( ! empty( $blogcentral_opts['h4']['color'] ) && '#222222' !== $blogcentral_opts['h4']['color'] ) {
			$h4_style .= 'color:' . $blogcentral_opts['h4']['color'] . ';';
		}
		if ( ! empty( $blogcentral_opts['h5']['size'] ) ) {
			$h5_style .= 'font-size:' . $blogcentral_opts['h5']['size'] . ' !important;';
		}
		if ( ! empty( $blogcentral_opts['h5']['color'] ) && '#222222' !== $blogcentral_opts['h5']['color'] ) {
			$h5_style .= 'color:' . $blogcentral_opts['h5']['color'] . ';';
		}
		if ( ! empty( $blogcentral_opts['h6']['size'] ) ) {
			$h6_style .= 'font-size:' . $blogcentral_opts['h6']['size'] . ' !important;';
		}
		if ( ! empty( $blogcentral_opts['h6']['color'] ) && '#222222' !== $blogcentral_opts['h6']['color'] ) {
			$h6_style .= 'color:' . $blogcentral_opts['h6']['color'] . ';';
		}
		
		// Now put all the html fragments together.
		$sep = '';
		if ( $page_header_style ) {
			$inline_css .= $sep . "#precontent-widget-cont,#precontent-widget{ $page_header_style }";
			$sep = ' ';
		}
		if ( $page_header_style_color ) {
			$inline_css .= $sep . "#precontent-widget-cont,#precontent-widget-cont h1,#precontent-widget-cont h2, #precontent-widget-cont h3, #precontent-widget-cont h4, #precontent-widget-cont h5, #precontent-widget-cont h6, #precontent-widget-cont a { $page_header_style_color }";
			$sep = ' ';
		}
		if ( $logo_style ) {
			$inline_css .= $sep . "#logo img { $logo_style }";
			$sep = ' ';
		}
		
		if ( $body_style ) {
			$inline_css .= $sep . "body { $body_style }";
			$sep = ' ';
		}
		
		if ( $main_content_style ) {
			$inline_css .= $sep . "#main-content{ $main_content_style }";
			$sep = ' ';
		}
		
		if ( $link_style ) {
			$inline_css .= $sep . "a { $link_style }";
			$sep = ' ';
		}
		
		if ( $link_hover_style ) {
			$inline_css .= $sep . "a:hover { $link_hover_style }";
			$sep = ' ';
		}
		
		if ( $link_visited_style ) {
			$inline_css .= $sep . "a:visited { $link_visited_style }";
			$sep = ' ';
		}
		
		if ( $h1_style ) {
			$inline_css .= $sep . "h1, h2 a{ $h1_style }";
			$sep = ' ';
		}
		
		if ( $h2_style ) {
			$inline_css .= $sep . "h2, h2 a{ $h2_style }";
			$sep = ' ';
		}
		
		if ( $h3_style ) {
			$inline_css .= $sep . "h3, h3 a{ $h3_style }";
			$sep = ' ';
		}
		
		if ( $h4_style ) {
			$inline_css .= $sep . "h4, h4 a{ $h4_style }";
			$sep = ' ';
		}
		
		if ( $h5_style ) {
			$inline_css .= $sep . "h5, h5 a{ $h5_style }";
			$sep = ' ';
		}
		
		if ( $h6_style ) {
			$inline_css .= $sep . "h6, h6 a{ $h6_style }";
			$sep = ' ';
		}
		
		if ( $headers_style ) {
			$inline_css .= $sep . "h1,h2,h3,h4,h5,h6,.footer h1,.footer h2,.footer h3,.footer h4,.footer h5,.footer h6,.footer .post-title a{ $headers_style }";
			$sep = ' ';
		}
		
		if ( $header_style ) {
			$inline_css .= $sep . "#header { $header_style }";
			$sep = ' ';
		}
		
		if ( $header_link_style ){
			$inline_css .= $sep . "#header a { $header_link_style }";
			$sep = ' ';
		}
		
		if ( $header_extra_style ) {
			$inline_css .= $sep . "#header .fa{ $header_extra_style }";
			$sep = ' ';
		}
		
		if ( $menu_style ) {
			$inline_css .= $sep . "#access { $menu_style }";
			$sep = ' ';
		}
		
		if ( $menu_lnk_style ) {
			$inline_css .= $sep . "#access a { $menu_lnk_style }";
			$sep = ' ';
		}
		
		if ( $menu_lnk_active_style ) { 
			$inline_css .= $sep . "#access .current_page_item > a { $menu_lnk_active_style }";
			$sep = ' ';
		}
		
		if ( $submenu_style ) {
			$inline_css .= $sep . "#access ul ul li { $submenu_style }";
			$sep = ' ';
		}
		
		if ( $submenu_lnk_style ) {
			$inline_css .= $sep . "#access ul ul a{ $submenu_lnk_style }";
			$sep = ' ';
		}
		
		$footer_style = $footer_link_style = $footer_link_hover_style = $footer_link_visited_style = $menu_style = 
			$menu_lnk_style = $submenu_style = $submenu_lnk_style = '';
		
		// Inline css for the footer.
		if ( ! empty( $blogcentral_opts['footer_bck_color'] ) && '#2b2b2b' !== $blogcentral_opts['footer_bck_color'] ) {
			$footer_style .= 'background-color:' . $blogcentral_opts['footer_bck_color'] . ';';
		}
		
		if ( ! empty( $blogcentral_opts['footer_txt_color'] ) && '#646464' !== $blogcentral_opts['footer_txt_color'] ) {
			$footer_style .= 'color:' . $blogcentral_opts['footer_txt_color'] . ';';
			$footer_extra_style .= 'color:' . $blogcentral_opts['footer_txt_color'] . ' !important;';
		}
		if ( ! empty( $blogcentral_opts['footer_lnk_color'] ) && '#afafaf' !== $blogcentral_opts['footer_lnk_color'] ) {
			$footer_link_style .= 'color:' . $blogcentral_opts['footer_lnk_color'] . ';';
		}
		
		if ( ! empty( $blogcentral_opts['footer_lnk_hover_color'] ) ) {
			$footer_link_hover_style .= 'color:' . $blogcentral_opts['footer_lnk_hover_color'] . ' !important;';
		}
		
		if ( ! empty( $blogcentral_opts['footer_lnk_visited_color'] ) ) {
			$footer_link_visited_style .= 'color:' . $blogcentral_opts['footer_lnk_visited_color'] . ';';
		}
		
		if ( $footer_style ) {
			$inline_css .= $sep . ".footer { $footer_style }";
			$sep = ' ';
		}
		
		if ( $footer_link_style ) {
			$inline_css .= $sep . ".footer a { $footer_link_style }";
			$sep = ' ';
		}
		
		if ( $footer_link_hover_style ) {
			$inline_css .= $sep . ".footer a:hover { $footer_link_hover_style }";
			$sep = ' ';
		}
		
		if ( $footer_link_visited_style ) {
			$inline_css .= $sep . ".footer a:visited { $footer_link_visited_style }";
			$sep = ' ';
		}
		
		if ( $footer_extra_style ) {
			$inline_css .= $sep . ".footer h1,.footer h2, .footer h3, .footer .post-title a, .footer h4,.footer h5,.footer h5, .footer .fa{ $footer_extra_style }";
			$sep = ' ';
		}
		
		if ( $custom_css ) {
			$inline_css .= $sep . $custom_css;
		}
		
		// Inline css for the slide templates.
		$inline_css .= $sep . ".slide-template {
			background-image: url(" . BLOGCENTRAL_THEME_URL . "/images/preview/hatback.jpg); }

		.slide-template-2 {
			background-image: url(" . BLOGCENTRAL_THEME_URL . "/images/preview/hatback.jpg);
		}";
		
		return $inline_css;
	}
}

/**
 * 6.2 Fonts
 *-----------------------------------------------------------------------*/
if ( ! function_exists( 'blogcentral_construct_font_weight_style' ) ) {
	/**
	 * Construct and display css for saved weight and style font options
	 *
	 * @since 1.0.0
	 *
	 * @param string $weight Required. Weight value for font and maybe style.
	 * @return array. Contains css for weight and style options indexed by weight and style, respectively.
	 */
	function blogcentral_construct_font_weight_style( $weight ) {
		$frag = array();
		
		if ( 'regular' === $weight ) {
			$weight = '400';
		}
		
		if ( 'italic' === $weight ) {
			$weight = '400italic';
		}
		
		$frag['weight'] = 'font-weight:' . esc_html( substr( $weight, 0 ,3 ) ) . ';';
		
		$font_length = strlen( $weight );
		
		/*
		 *	Example of weight: 400italic. The first 3 characters always represents the font weight.
		 *	If length is greater than 3, then has a font style.
		 */
		if (  3 < $font_length ) {
			if ( $font_style = substr( $weight, 3, $font_length-3 ) ) {
				$frag['style'] = 'font-style:' . esc_html( $font_style ) . ';';
			}
		}
		
		return $frag;
	}
}

/**
 * 6.3 Switch Demo
 *-----------------------------------------------------------------------*/

if ( ! function_exists( 'blogcentral_choose_demo' ) ) {
	/**
	 * Switch the demo.
	 *
	 * For demonstration purposes only, can safely be deleted if the add_action( 'wp_head', 'blogcentral_choose_demo', 0 ) action is deleted also.
	 *
	 * @since 1.0.0
	 */
	function blogcentral_choose_demo() {
		global $blogcentral_opts;
		global $blogcentral_blog_demos_opts;
		global $wp_query; 
		global $fourbzcore_plugin;
		
		if ( ! isset( $wp_query->post ) ) {
			return;
		}
		
		if ( is_page_template() ) {
			$page_template = get_page_template_slug( $wp_query->post->ID );

			if ( 'page-template-blog-demo1.php' === $page_template ) {	
				$blogcentral_opts = $blogcentral_blog_demos_opts[0];
				set_transient( 'blogcentral_options', $blogcentral_opts, 21 * DAY_IN_SECONDS );
			} elseif ( 'page-template-blog-demo2.php' === $page_template ) {	
				$blogcentral_opts = $blogcentral_blog_demos_opts[1];
				set_transient( 'blogcentral_options', $blogcentral_opts, 21 * DAY_IN_SECONDS );
			} elseif ( 'page-template-blog-demo3.php' === $page_template ) {	
				$blogcentral_opts = $blogcentral_blog_demos_opts[2];
				set_transient( 'blogcentral_options', $blogcentral_opts, 21 * DAY_IN_SECONDS );
			} 
		} elseif ( is_home() ) {
			$blogcentral_opts = blogcentral_initialize_global_opts();
			delete_transient( 'blogcentral_options' );
		}
		
		if ( isset( $fourbzcore_plugin ) ) {
			// Have to do this so the widgets will have the updated options.
			do_action( 'widgets_init' );
		}
	}
}

/**
 * 6.4 Body Class
 *-----------------------------------------------------------------------*/
if ( ! function_exists( 'blogcentral_body_class' ) ) {
	/**
	 * Construct the classes to be added to the body tag
	 *
	 * Uses the body_class filter.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Required. Array of classes.
	 * @return array $classes. Classes to be added to the body tag, augmented with the theme's classes.
	 */
	function blogcentral_body_class( $classes ) {
		// Global variable that holds the theme options.
		global $blogcentral_opts;
		
		if ( ! is_multi_author() ) {
			$classes[] = 'single-author';
		}
		
		if ( is_active_sidebar( 'sidebar-rght' ) && ! is_attachment() && ! is_404() ) {
			$classes[] = 'sidebar';
		}
		
		if ( ! get_option( 'show_avatars' ) ) {
			$classes[] = 'no-avatars';
		}
		
		if ( isset( $blogcentral_opts['color_scheme'] ) ) {
			$scheme = $blogcentral_opts['color_scheme'];
		}
		
		if ( isset( $scheme ) && 0 !== absint( $scheme ) ) {
			$classes[] = 'color-scheme' . esc_html( $scheme );
		}
		
		$classes[] = ' color-scheme';
		
		if ( isset( $blogcentral_opts['block_display'] ) ) {
			$classes[] = 'block-display';
		}
		
		if ( isset( $blogcentral_opts['box_display'] ) ) {
			$classes[] = 'box-display';
		}
		
		return $classes;
	}
}

/**
 * 6.5 Page Header/Precontent Area
 *-----------------------------------------------------------------------*/

if ( ! function_exists( 'blogcentral_get_todays_date' ) ) {
	/**
	 * Display today's date
	 *
	 * @since 1.0.0
	 */
	function blogcentral_get_todays_date() {
		print strftime('%A %B %d, %Y');
	}
}

if ( ! function_exists( 'blogcentral_display_breadcrumbs' ) ) {
	/**
	 * Construct and display the breadcrumbs
	 *
	 * @since 1.0.0
	 */
	function blogcentral_display_breadcrumbs() {
		global $post;
		
		// If on home page, no need to display the breadcrumbs.
		if ( is_home() ) {
			return '';
		}	
		
		echo '<ul id="breadcrumbs">';
		
			// Link to home will be displayed first.
			echo '<li><a href="';
			echo home_url();
			echo '">';
			echo __( 'Home', BLOGCENTRAL_TXT_DOMAIN );
			echo '</a><span class="sep">/</span></li>';
			
			if ( is_category() || is_tag() || is_author() || is_search() || is_single() ) {
				if ( is_single() ) {
					echo '<li>';
					the_title();
					echo '</li>';
				} elseif ( is_category() ) {
					echo '<li>' . __( 'Category: ', BLOGCENTRAL_TXT_DOMAIN ) . single_cat_title( '', false ) . '</li>';
				} elseif ( is_tag() ) {
					echo '<li>' . __( 'Tag: ', BLOGCENTRAL_TXT_DOMAIN ) . single_tag_title( '', false ) . '</li>';
				}  elseif ( is_author() ) {
					echo '<li>' . __( 'Author', BLOGCENTRAL_TXT_DOMAIN ) . '</li>';
				}  elseif ( is_search() ) {
					echo '<li>' . __( 'Search Results', BLOGCENTRAL_TXT_DOMAIN ) . '</li>';
				} elseif ( is_404() ) {
					echo '<li>' . __( 'Not Found', BLOGCENTRAL_TXT_DOMAIN ) . '</li>';
				}
			} elseif ( is_page() ) {
				// If has parents, then display links to them first.
				if ( $post->post_parent ) {
					$output = '';
					$ancestors = get_post_ancestors( $post->ID );
					$title = get_the_title();
					
					foreach ( $ancestors as $ancestor ) {
						$output .= '<li><a href="'. esc_url( get_permalink( $ancestor ) ). '" title="'. get_the_title( $ancestor ).'">'. get_the_title( $ancestor ) .'</a>
							<span class="sep">/</span></li>';
					}
					
					echo $output;
					echo '<strong title="' . $title . '"> ' . $title . '</strong>';
				} else {
					echo '<li><strong>' . get_the_title() . '</strong></li>';
				}
			}
			
			echo '</ul>';
	}
}

if ( ! function_exists( 'blogcentral_output_precontent_frag' ) ) {
	/**
	 * Construct and display html for the precontent widget area, which will hold the breadcrumbs, today's date, and
	 * title of the page, only if no active widgets in this area
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Required. Various options set on the theme's options page
	 * @param string $title Optional. Text to display to indicate what page is being viewed.
	 * @param boolean $about_me Optional. Is this the precontent area for about me page?.
	 */
	function blogcentral_output_precontent_frag( $opts, $title = '', $about_me = false ) {
		$breadcrumbs = isset( $opts['breadcrumbs'] ) ? $opts['breadcrumbs'] : '';
		$page_header = isset( $opts['page_header'] ) ? $opts['page_header'] : '';
		
		$page_header_style = $crumbs = $todays_date = $crumbs_style = $page_class = $page_title_class = 
			$page_header_title = '';
			
		// Add classes for an overlay
		if ( isset( $opts['page_header_bck_img'] ) ) {
			$page_class .= ' fixed-height'; 
		}
		
		if ( isset( $opts['page_header_bck_img'] ) && $opts['page_header_bck_img'] ) {
			$page_title_class = ' class="overlay"';
		} else {
			$page_title_class = ' class="no-overlay"';
		}
		
		// Add class for the page header layout.
		if ( isset( $opts['page_header_layout'] ) ) {
			if ( '2' === $opts['page_header_layout'] ) {
				$page_class .= ' layout2';
			} elseif ( '3' === $opts['page_header_layout'] ) {
				$page_class .= ' layout3';
			}
		}
		
		// Construct breadcrumbs.
		if ( $breadcrumbs && ! is_home() ) {
			ob_start();
			blogcentral_display_breadcrumbs();
			
			$crumbs .=	ob_get_clean();
		}
		
		// Construct today's date.
		if ( isset( $opts['show_todays_date'] ) ) {
			$todays_date = '<span class="todays-date"><span class="todays-date-label">' . __( 'Today is ', BLOGCENTRAL_TXT_DOMAIN ) . '</span>';
			ob_start();                
			blogcentral_get_todays_date();
			$todays_date .= ob_get_clean() . '</span>';
		}
		
		/*
		 * If no active widgets, then show a background image with an overlay that shows what the user is 
		 * viewing, e.g. blog, category, etc.
		 */
		$active_sidebar = false;
		$container_id = '';
		
		if ( $about_me ) {
			$active_sidebar = is_active_sidebar( 'precontent-widget-about-me' );
			$dynamic_sidebar = $container_id = "precontent-widget-about-me";
			
		} else {
			$dynamic_sidebar = "precontent-widget";
			$active_sidebar = is_active_sidebar( 'precontent-widget' );
			$container_id = "precontent-widget-cont";
		}
		
		if ( $active_sidebar ) {
			echo '<div id="' . $container_id . '" class="fixed-height">';
			dynamic_sidebar( $dynamic_sidebar ); 
			echo '</div>';
		} elseif( ( $page_header && ( $breadcrumbs || $todays_date || $title ) ) ) {
			echo '<div id="precontent-widget-cont">';
			
			if ( $title ) {
				$page_header_title = '<h1 class="page-header-title">' . $title . '</h1>';
			}
		
			?>
				<div id="precontent-widget" class="<?php echo $page_class; ?>">
					<?php echo '<div' . $page_title_class . '><div class="caption">' . $page_header_title; 
							echo '<div class="page-trail">' . $todays_date . $crumbs . '</div>';
							echo '</div></div>';
					?> 
				</div>
			</div>
			<?php
		}// End if page_header 
	}
}

if ( ! function_exists( 'blogcentral_output_color_chooser' ) ) {
	/**
	 * Display the color chooser on the front end, to dynamically change the color scheme via jquery
	 *
	 * @since 1.0.0
	 */
	function blogcentral_output_color_chooser() {
		?>
		<div id="dynamic-style">
			<div class="style-chooser hide">
				<h5><?php _e( 'Choose color scheme', BLOGCENTRAL_TXT_DOMAIN ); ?></h5><br />
				<span class="color-scheme1"></span><span class="color-scheme2"></span><span class="color-scheme3"></span><span class="color-scheme4"></span><span class="color-scheme5"></span><span class="color-scheme6"></span><span class="color-scheme7"></span><span class="color-scheme8"></span><span class="color-scheme9"></span>
			</div><i class="fa fa-gears toggle-style-chooser"></i>
		</div>
		<?php
	}
}

/**
 * 6.6 Post Content
 *-----------------------------------------------------------------------*/
if ( ! function_exists( 'blogcentral_more_link' ) ) {
	/**
	 * Construct the more link
	 *
	 * @since 1.0.0
	 * @return string more link.
	 */
	function blogcentral_more_link() {
		return '<p class="read-more-link"><a href="'. esc_url( get_permalink() ) . '">' . __( 'Read more', BLOGCENTRAL_TXT_DOMAIN ) . '<i class="fa fa-arrow-right"></i></a></p>';
	}
}

if ( ! function_exists( 'blogcentral_auto_excerpt_more' ) ) {
	/**
	 * Construct more link for auto excerpts
	 *
	 * @since 1.0.0
	 *
	 * @see blogcentral_more_link()
	 *
	 * @return string more link.
	 */
	function blogcentral_auto_excerpt_more( $output ) {
		return blogcentral_more_link();
	}
}

if ( ! function_exists( 'blogcentral_custom_excerpt_more' ) ) {
	/**
	 * Attach more link to excerpts
	 *
	 * @since 1.0.0
	 *
	 * @see blogcentral_more_link()
	 *
	 * @return string $output. The excerpt appended with the more link.
	 */
	function blogcentral_custom_excerpt_more( $output ) {
		if ( ! is_single() ) {
			$output .= blogcentral_more_link();
		}
		
		return $output;
	}
}

if ( ! function_exists( 'blogcentral_get_link_url' ) ) {
	/**
	 * Get the first link in the post content
	 *
	 * Uses the wordpress get_url_in_content function.
	 *
	 * @since 1.0.0
	 *
	 * @return string. The found link or the permalink.
	 */
	function blogcentral_get_link_url() {
		$content = get_the_content();
		$has_url = get_url_in_content( $content );

		return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
	}
}

/**
 * 6.7 Posts Navigation
 *-----------------------------------------------------------------------*/
if ( ! function_exists( 'blogcentral_traditional_posts_nav' ) ) {
	/**
	 * Construct and display main posts navigation
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query Object $query Optional. Isset if this function is called on any of the blog demo pages.
	 */
	function blogcentral_traditional_posts_nav( $query = false ) { 
		if ( is_single() || is_attachment() )  {
			 return;  
		} 
			 
		if ( ! $query ) {
			global $wp_query;  
			$query = $wp_query;
		}

		// Stop execution if there's only 1 page 
		if (  1 >= $query->max_num_pages ) {
			return;  
		}
		
		$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;  

		$max = intval( $query->max_num_pages );  

		echo '<div class="navigation"><ul>';  
		
		// Previous Post Link
		if ( get_previous_posts_link() ) {  
			printf( '<li>%s</li>', get_previous_posts_link('<') ); 
		}
		
		// Link to current page. 
		for ( $i = 1; $i <= $max; ++$i ) {
			$class = $paged === $i ? ' class="active"' : '';  

			printf( '<li%s><a href="%s">%s</a></li>', $class, esc_url( get_pagenum_link( $i ) ), $i );
		}  

		// Next Post Link
		printf( '<li>%s</li>', get_next_posts_link( __( '>', BLOGCENTRAL_TXT_DOMAIN ), $max ) );  
		
		echo '</ul></div>';  
	}
}

if ( ! function_exists( 'blogcentral_post_nav' ) ) {
	/**
	 * Construct and display navigation for single post page
	 *
	 * @since 1.0.0
	 */
	function blogcentral_post_nav() {
		global $post;

		// Don't print empty markup if there's nowhere to navigate.
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
			return;
		}
		?>
		<nav class="navigation post-navigation" role="navigation">
			<h3 class="screen-reader-txt"><?php _e( 'Post navigation', BLOGCENTRAL_TXT_DOMAIN ); ?></h3>
			<div class="nav-links">
				<?php previous_post_link( '%link', _x( '<span class="meta-nav">&larr;</span> %title', 'Previous post link', BLOGCENTRAL_TXT_DOMAIN ) ); ?>
				<?php next_post_link( '%link', _x( '%title <span class="meta-nav">&rarr;</span>', 'Next post link', BLOGCENTRAL_TXT_DOMAIN ) ); ?>
			</div><!-- .nav-links -->
		</nav><!-- .navigation -->
		<?php
	}
}

/**
 * 6.8 Comments
 *-----------------------------------------------------------------------*/
if ( ! function_exists( 'blogcentral_comments_layout' ) ) {
	/**
	 * Construct and display comments
	 *
	 * For use in the wp loop
	 *
	 * @since 1.0.0
	 */
	function blogcentral_comments_layout() {
		$plural = __( 'Comments', BLOGCENTRAL_TXT_DOMAIN );
		$single = __( 'Comment', BLOGCENTRAL_TXT_DOMAIN );
		$count = get_comments_number();
		
		global $wp_query;
		?>
		<div id="comments">
			<div>
			<?php if ( post_password_required() ) : ?>
				<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', BLOGCENTRAL_TXT_DOMAIN ); ?>
				</p>
			</div>
		</div><!-- End #comments -->
		<?php
				// Stop processing the comments.
				return;
			endif;
			
			if ( have_comments() ) : ?>
				<h3 class="title" id="comments-title">
				<?php
					echo $count . ' ';
					
					if ( $count == 1 ) {
						echo $single;
					} else {
						echo $plural;
					}
				?>
				</h3>

				<ul class="commentlist">
					<?php
					wp_list_comments( array(
						'callback'	=>	'blogcentral_comment'
					) );
					?>
				</ul>
			
				<?php 
				if ( 1 < get_comment_pages_count() && get_option( 'page_comments' ) ) : ?>
					<div class="cmt-nav">
						<div class="nav-previous">
							<?php previous_comments_link( '<span class="meta-nav">&laquo;</span>' .
								__( ' Older Comments', BLOGCENTRAL_TXT_DOMAIN ) ); ?>
						</div>
						<div class="nav-next">
							<?php next_comments_link( __( 'Newer Comments', BLOGCENTRAL_TXT_DOMAIN ) .
								'<span class="meta-nav">&raquo;</span>' ); ?>
						</div>
					</div><!-- End .cmt-nav -->
				<?php endif; ?>

		<?php
			endif; // End have_comments() ?>
		<?php 		
			$comments_open = comments_open();
			
			if ( $comments_open ) :
				comment_form();
			elseif ( null === $comments_open ) :
			?>
				<p class="nocomments text-format"><?php _e( 'You already left a comment for this post.', BLOGCENTRAL_TXT_DOMAIN ); ?></p>
			<?php
			elseif ( ! comments_open() && post_type_supports( get_post_type(), 'comments' ) ) :
			?>
				<span class="nocomments"><?php _e( 'Comments are closed.', BLOGCENTRAL_TXT_DOMAIN ); ?></span>
			<?php endif; // End comments_open() ?>

		</div></div><!-- End #comments -->
		<?php
	}
}

if ( ! function_exists( 'blogcentral_comment' ) ) {
	/**
	 * Callback to display a comment 
	 *
	 * @since 1.0.0
	 *
	 * @param object $comment Required. Comment to display.
	 * @param string $args.
	 * @param string $depth. 
	 */
	function blogcentral_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		
		global $blogcentral_opts;
		
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
				?>
				<li class="post pingback">
					<p><?php _e( 'Pingback:', BLOGCENTRAL_TXT_DOMAIN );
						comment_author_link();
						edit_comment_link( __( 'Edit', BLOGCENTRAL_TXT_DOMAIN ), '<span class="edit-link">', '</span>' ); ?>
					</p>
				<?php
				break;
			default :
				?>
				<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
					<div id="comment-<?php comment_ID(); ?>" class="comment">
						<footer class="comment-meta">
							<div class="comment-author vcard">
								<div class="cmt-avatar">
									<?php
									$avatar_size = 68;
									
									if ( '0' !== $comment->comment_parent ) {
										$avatar_size = 39;
									}
									
									$custom_fields = '';
									
									if ( isset( $fourbzcore_plugin ) && method_exists( $fourbzcore_plugin, 'get_user_meta' ) ) {
										$cust_fields = $fourbzcore_plugin->get_user_meta( $comment->user_id );
									}
										
									/*
									 *	Construct the avatar for the comment author in this order
									 *		1. Avatar set on profile page.
									 *		2. Default user avatar set on the theme options page.
									 *		3. Default gravatar provided by WordPress.
									 */
									 
									// Construct the different attributes for the <img> tag
									if ( ! empty( $cust_fields['avatar'] ) ) {
										$alt = isset( $cust_fields['avatar_alt'] ) && ! empty( $cust_fields['avatar_alt'] )
											? esc_attr( $cust_fields['avatar_alt'] )
											: '';
										$url = isset( $cust_fields['avatar'] ) ?
												esc_url( $cust_fields['avatar'] ) :
												'';
										$width = isset( $cust_fields['avatar_width'] ) && ! empty( $cust_fields['avatar_width'] )
											? esc_attr( $cust_fields['avatar_width'] ) : '';
										$height = isset( $cust_fields['avatar_height'] ) && ! empty( $cust_fields['avatar_height'] )
											? esc_attr( $cust_fields['avatar_height'] ) : '';
										
										$avatar =  '<img class="avatar avatar-68 photo" src="' . $url . '"';
										
										if ( $alt ) {
											$avatar .= ' alt="' . $alt  . '"';
										}
										
										if( $width ) {
											$avatar .= ' width="' . $width . '"';
										}
										
										if( $height ) {
											$avatar .= ' height="' . $height . '"';
										}
										
										$avatar .= ' />';
										
										echo $avatar;
									} elseif ( ! empty( $blogcentral_opts['default_user_img'] ) ) {
										$alt = isset( $blogcentral_opts['default_user_img_alt'] ) &&
											! empty( $blogcentral_opts['default_user_img_alt'] ) ? 
												esc_attr( $blogcentral_opts['default_user_img_alt'] ) :
												'';
												
										$url = isset( $blogcentral_opts['default_user_img'] ) ? 
											esc_url( $blogcentral_opts['default_user_img'] ) :
											'';
											
										$width = isset( $blogcentral_opts['default_user_img_width'] ) &&
											! empty( $blogcentral_opts['default_user_img_width'] )
											? esc_attr( $blogcentral_opts['default_user_img_width'] )
											: '';
											
										$height = isset( $blogcentral_opts['default_user_img_height'] ) &&
											! empty( $blogcentral_opts['default_user_img_height'] )
											? esc_attr( $blogcentral_opts['default_user_img_height'] )
											: '';
										
										$avatar =  '<img src="' . $url . '"';
										
										if ( $alt ) {
											$avatar .= ' alt="' . $alt  . '"';
										}
										
										if( $width ) {
											$avatar .= ' width="' . $width . '"';
										}
										
										if( $height ) {
											$avatar .= ' height="' . $height . '"';
										}
										
										$avatar .= ' />';
										
										echo $avatar;
									} else {
										echo get_avatar( $comment->comment_author_email, 50 );
									}
								?>
								</div><div class="cmt-author-meta"><?php
									if ( $comment->user_id ) {
										$user = get_userdata( $comment->user_id );
										$comment_name = $user->display_name;
									} else { 
										$comment_name = get_comment_author_link();
									}
									printf( '<span class="cmt-authorlnk">%s</span>', $comment_name );
									printf( '<span class="cmt-datelnk"><a href="%1$s">
										<time datetime="%2$s">%3$s</time></a></span><br />',
											esc_url( get_comment_link( $comment->comment_ID ) ),
											get_comment_time( 'c' ),
											/* translators: 1: date, 2: time */
											sprintf( __( '%1$s at %2$s', BLOGCENTRAL_TXT_DOMAIN ), get_comment_date(),
												get_comment_time() )
									);
									
									?><span class="reply"><?php comment_reply_link( array_merge( $args,
											array( 'reply_text' => __( 'Reply', BLOGCENTRAL_TXT_DOMAIN ), 'depth' => $depth,
												'max_depth' => $args['max_depth'] ) ) ); ?>
									</span><!-- .reply --><?php edit_comment_link( __( 'Edit', BLOGCENTRAL_TXT_DOMAIN ), 
										'<span class="cmt-editlnk">', '</span>' );
									?>
								</div>
							</div><!-- .comment-author .vcard -->
							<?php if ( '0' === $comment->comment_approved ) : ?>
								<em class="comment-awaiting-moderation"><?php 
									_e( "Your comment is awaiting approval.", BLOGCENTRAL_TXT_DOMAIN ); ?></em>
								<br />
						</footer>
							<?php else : ?>
						</footer>
						<div class="comment-content"><?php comment_text(); ?></div>
							<?php endif; ?>
						
					</div><!-- End #comment-## -->
				<?php
			break;
		endswitch;
	}
}

/**
 * 6.9 Components
 *-----------------------------------------------------------------------*/
if ( ! function_exists( 'blogcentral_component_wrapper' ) ) {
	/**
	 * Invoke a function and wrap its result in a div, if $wrapper is set to true
	 *
	 * Is used in shortcodes display, which in turn is used in some widgets display.
	 *
	 * @since 1.0.0
	 *
	 * @param object $function Required. The function to invoke.
	 * @param bool $wrapper Optional. Whether to wrap the result of the function call in a div.
	 * @return string 
	 */
	function blogcentral_component_wrapper( $function, $wrapper = false ) {
		$before = $after = '';
		ob_start();

		// If wrapper is true, then will contain class for wrapper, and texts for title and taglines.
		if ( $wrapper ) {
			$frag = blogcentral_construct_wrapper( $wrapper );
			$before = $frag[0];
			$after = $frag[1];
		}
		
		echo $before;
		call_user_func( $function[0], $function[1] );
		echo $after;	

		return ob_get_clean();
	}
}

if ( ! function_exists( 'blogcentral_construct_wrapper_classes_styles' ) ) {
	/**
	 * Construct classes and style for the wrapper div that encompasses components
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Required. Options.
	 * @return array that contains all of the classes and styles that should be added to the component wrapper, 
	 * title text, and tagline text containers.
	 */
	 function blogcentral_construct_wrapper_classes_styles( $opts ) {
		if ( ! isset( $opts ) ) {
			return;
		}
		
		global $blogcentral_opts;
		
		$wrap_class = $title_class = $cols = $slideshow = $border = $alternate_color = $layout = $title_border =
			$tagline_border = $tagline_class = '';
			
		extract( $opts );
		
		$wrap_class = ' ' . trim( $wrap_class );
		$classes = array();
		$extra_class = $classes['extra_tagline'] = $wrap_style = $wrap_id = $flex_controls = '';
		
		
		if ( isset( $id ) ) {
			$wrap_id = " id='" . esc_attr( $id ) . "'";
		}
		
		// Add class for column display
		if ( $cols ) {
			$wrap_class .= " cols-$cols";
			if ( 1 < $cols  )
				$wrap_class .= " cols";
		}
		
		// Add classes to implement the layout of the component.
		$layout_class = '';
		
		if ( $layout ) {
			$layout_class = ' ' . $layout;
			
			if ( 'layout3' === $layout ) {
				$layout_class .= ' layout1';
			}			
		}
		
		$classes['layout_class'] = $layout_class;
		
		// Add class for the border of the component title.
		if ( isset( $title_border ) && $title_border ) {
			if ( 'default' === $title_border  ) {
				if ( isset( $blogcentral_opts['widget_border'] ) ) {
					$title_border = $blogcentral_opts['widget_border'];
				} else {
					$title_border = '';
				}
			}
			
			$extra_class .= $title_border;
		} elseif( isset( $blogcentral_opts['widget_border'] ) ) {
			$title_border = $blogcentral_opts['widget_border'];
		}
		
		// Add custom class for the title.
		$classes['title_class'] = $title_class;
		
		// Add class for the tagline border.
		if ( $tagline_border ) {
			if ( 'default' === $tagline_border  ) {
				if ( isset( $blogcentral_opts['widget_border'] ) ) {
					$tagline_border = $blogcentral_opts['widget_border'];
				} else {
					$tagline_border = '';
				}
			}
			$classes['extra_tagline'] .= ' ' . $tagline_border;
		}
		
		// Add custom class for the tagline.
		if ( $tagline_class ) {
			$classes['extra_tagline'] .= ' ' . $tagline_class;
		}
		
		// Add class to display a border.
		if ( '' !== $border ) {
			if ( '0' === $border ) {
				$wrap_class .= " no-border";
			} elseif ( '1' === $border ) {
				$wrap_class .= " btm-border";
			}
		} else {
			$wrap_class .= " no-border";
		}
		
		// Add class to alternate the background color of component items.
		if ( $alternate_color ) {
			$wrap_class .= " alternate-color";
		}
		
		if ( $wrap_style ) {
			$wrap_style = " style='$wrap_style'";
		}
			
		if ( $wrap_id ) {
			$wrap_style .= $wrap_id;
		}
		
		global $fourbzcore_plugin;
		if ( isset( $fourbzcore_plugin ) ) {
			// Add class for slideshow display and create custom controls if the slideshow is for a component.
			if ( $slideshow ) {
				// Else if not for a component.
				$wrap_class .= ' flexslider';
			}
		
			/*
			 * Add class to create a boxed slideshow if slideshow_layout is 1, otherwise add class to create a 
			 * full-width slideshow.
			 */
			if ( isset( $slideshow_layout ) && '1' === $slideshow_layout ) {
				$wrap_class .= " boxed";
			} elseif ( isset( $slideshow_layout ) && '2' === $slideshow_layout ) {
				$wrap_class .= " full-width";
			}
			
			// Create data attributes for slideshow to be used in jquery.
			if ( isset( $animation ) ) {
				if ( '1' === $animation ) {
					$wrap_style .= " data-animation='fade'";
				} else {
					$wrap_style .= " data-animation='slide'";
				}
			}
			if ( isset( $sync ) && $sync ) {
				$wrap_style .= " data-sync='" . esc_html( $sync ) . "'";
			}
			if( isset( $asNavFor ) && $asNavFor ) {
				$wrap_style .= " data-asNavFor='" . esc_html( $asNavFor ) . "'";
			}
			if( isset( $useCSS ) && $useCSS ) {
				$wrap_style .= " data-useCSS='" . esc_html( $useCSS ) . "'";
			}
			if( isset( $easing ) && $easing ) {
				$wrap_style .= " data-easing='" . esc_html( $easing ) . "'";
			}
		}
		
		$classes['wrap_style'] = $wrap_style;
		$classes['wrap_style'] .= " data-blogcentral-cols='$cols'";
		$classes['wrap_class'] = $wrap_class;
		
		$classes['extra_class'] = $extra_class;
		
		return $classes;
	}
}

if ( ! function_exists( 'blogcentral_construct_wrapper' ) ) {
	/**
	 * Construct component wrapper, title and tagline with the classes and styles created by the blogcentral_construct_classes() function
	 *
	 * @since 1.0.0
	 * @see blogcentral_construct_classes()
	 *
	 * @param array $wrapper Required. Contains all the required html for the class and style attributes of the
	 * wrapper <div>
	 * @return array that contains the before and after tags for a component.
	 */
	function blogcentral_construct_wrapper( $wrapper ) {
		if ( ! isset( $wrapper ) ) {
			return;
		}
		
		$wrapper_class = isset( $wrapper['wrapper_class'] ) ? esc_attr( $wrapper['wrapper_class'] ) : '';
		$wrapper_style = isset( $wrapper['wrapper_style'] ) ? $wrapper['wrapper_style']  : '';
		$title_class = isset( $wrapper['title_class'] ) ? esc_attr( $wrapper['title_class'] ) : '';
		
		// Do not escape the title_text and tagline because html markup is allowed in these.
		$title_text = isset( $wrapper['title_text'] ) ? html_entity_decode( $wrapper['title_text'] ) : '';
		$tagline = isset( $wrapper['tagline_text'] ) ? html_entity_decode( $wrapper['tagline_text'] ) : '';
		
		$tagline_class = isset( $wrapper['tagline_class'] ) ? esc_attr( $wrapper['tagline_class'] ) : '';
		$class = '';
		
		$before = $after = '';
		
		$before = '<div' . $wrapper_style . ' class="' . $wrapper_class . '">';
		
		// Add title and tagline texts to component.
		if ( $title_text ) {
			$before .= '<div';
			
			if ( 'header-style1' !== trim( $title_class ) ) {
				$before .= " class='header-wrap'";
			}
			$before .=  '><h3 class="' . $title_class .  '">' . $title_text . '</h3>';
			
			if ( $tagline ) {
				$before .= '<h5 class="header-tagline' . $tagline_class . '">' . $tagline . '</h5>';
			}
			
			$before .= '</div>';
		}
		
		$after = '</div>';
		
		return array( $before, $after );
	}
}

if ( ! function_exists( 'blogcentral_common_template_wrapper' ) ) {
	/**
	 * Wrapper function to create common template beginning and ending tags
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Required. Various options set on the theme's options page
	 * @return array. Contains the html fragments for the beginning and ending wrapper <div>s.
	 */
	function blogcentral_common_template_wrapper( $opts ) {
		$classes = blogcentral_construct_wrapper_classes_styles( $opts );
		
		$wrapper = array( 
			'wrapper_class'	=>	"components-wrap$classes[layout_class]$classes[wrap_class]",
			'wrapper_style'	=>	$classes['wrap_style'],
			'title_text'	=>	isset( $opts['title_text'] ) ? $opts['title_text'] : '',
			'tagline_text'	=>	isset( $opts['tagline_text'] ) ? $opts['tagline_text'] : '',
			'title_class'	=>	"$classes[title_class] $classes[extra_class]",
			'tagline_class'	=>	$classes['extra_tagline']
		);
		
		$frag = blogcentral_construct_wrapper( $wrapper );
		
		return $frag;
	}
}

if ( ! function_exists( 'blogcentral_construct_inner_classes_styles' ) ) {
	/**
	 * Construct html for the classes and styles of the various containers in the component, eg. media wrap, and meta/content wrap
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Required. Various options for the containers, set on the theme's options page
	 * @return array. Contains the html fragments for classes and styles of the various containers in a component.
	 */
	function blogcentral_construct_inner_classes_styles( $opts ) {
		// Initialize variables
		$blogcentral_media_wrap_style = $blogcentral_li_wrap_style = $blogcentral_component_item_style =
			$align_style = $blogcentral_meta_wrap_class = $blogcentral_meta_wrap_style = $blogcentral_extra_li_class = 
			$layout_class = $layout = $cols_span = $cols = $post_meta_layout = '';
		
		extract( $opts );
		
		$blogcentral_extra_li_class .= ' component';
		
		// Add classes to implement the layout of the component item.
		$layout_class = '';
		if ( $layout ) {
			$layout_class = ' ' . $layout;
			
			if ( 'layout3' === $layout ) {
				$layout_class .= ' layout1';
			}
			$blogcentral_extra_li_class .= $layout_class;
		}
		
		// Add class for post meta layout.
		if (  '2' === $post_meta_layout ) {
			$blogcentral_meta_wrap_class .= " meta-layout2";
		}
		
		if ( $blogcentral_li_wrap_style || $blogcentral_component_item_style ) {
			$blogcentral_component_item_style = " style='" . trim( $blogcentral_li_wrap_style ) . trim( $blogcentral_component_item_style ) . "'";	
		}
		
		return compact( "blogcentral_component_item_style", "blogcentral_li_wrap_style", "blogcentral_media_wrap_style", "blogcentral_meta_wrap_class", "blogcentral_meta_wrap_style", "blogcentral_extra_li_class" );
	}
}

if ( ! function_exists( 'blogcentral_construct_masonry_options' ) ) {
	/**
	 * Construct html for masonry display 
	 *
	 * @since 1.0.0
	 *
	 * @param array $opts Required. Options for the masonry display.
	 * @return string. Contains the html for masonry display.
	 */
	function blogcentral_construct_masonry_options( $opts ) {
		$selector = $col_width = '';
		extract( $opts );
		
		if ( isset( $cols ) ) {
			$col_width = ".cols-$cols-sizer";
		}
		
		return '{ "columnWidth": "' . esc_attr( $col_width ) . '" "itemSelector": "' . esc_attr( $selector ) . '", "gutter": "' . esc_attr( $gutter ) . '" }';
	}
}

if ( ! function_exists( 'blogcentral_get_styles' ) ) {
	/**
	 * Construct styles and classes for the component wrapper, title, and tagline
	 *
	 * @since 1.1.0
	 *
	 * @param array $options Required. Options for the component.
	 * @return array. The constructed classes and styles.
	 */
	function blogcentral_get_styles( $options ) {
		if ( empty( $options ) ) {
			return;
		}
		
		/*
		 * Variable to be passed to the component wrapper function, it holds the various html fragments for
		 * the necessary classes and styles for the wrapper. It also holds the title and tagline texts and their classes and styles.
		 */
		$wrapper = array();
		
		$wrapper['wrapper_class'] = 'components-wrap';
		
		$classes = blogcentral_construct_wrapper_classes_styles( $options );
		
		if ( isset( $classes['layout_class'] ) ) {
			$wrapper['wrapper_class'] .= $classes['layout_class'];
		}
		
		if ( isset( $classes['wrap_class'] ) ) {
			$wrapper['wrapper_class'] .= ' ' . $classes['wrap_class'];
		}
		
		if ( isset( $classes['wrap_style'] ) ) {
			$wrapper['wrapper_style'] = $classes['wrap_style'];
		}
		
		$wrapper['title_class'] = '';
		
		if ( isset( $classes['title_class'] ) ) {
			$wrapper['title_class'] .= $classes['title_class'];
		}
		
		if ( isset( $classes['extra_class'] ) ) {
			$wrapper['title_class'] .= ' ' . $classes['extra_class'];
		}
		
		if ( isset( $classes['extra_tagline'] ) ) {
			$wrapper['tagline_class'] = $classes['extra_tagline'];
		}
		
		$wrapper['title_text'] = isset( $options['title_text'] ) ? $options['title_text'] : '';
		$wrapper['tagline_text']= isset ( $options['tagline_text'] ) ? $options['tagline_text'] : '';

		return $wrapper;
	}
}