<?php

/**
 *	Require Once
 */
require_once( 'includes/customizer.php' );

/**
 *	Theme Setup
 */
if( !function_exists( 'minimalzerif_theme_setup' ) ) {
	add_action( 'after_setup_theme', 'minimalzerif_theme_setup' );
	function minimalzerif_theme_setup() {
		load_theme_textdomain( 'minimalzerif', get_template_directory() . '/languages' );
	}
}

/**
 *	WP Enqueue Styles
 */
if( !function_exists( 'minimalzerif_enqueue_styles' ) ) {
	add_action( 'wp_enqueue_scripts', 'minimalzerif_enqueue_styles' );
	function minimalzerif_enqueue_styles() {
        wp_enqueue_style( 'minimalzerif-style', get_stylesheet_directory_uri() . '/style.css', array( 'zerif_style' ), '1.0.2', 'all' );
        wp_enqueue_style( 'zerif_style', get_template_directory_uri() . '/style.css' );
	}
}

/**
 *	WP Enqueue Scripts
 */
if( !function_exists( 'minimalzerif_enqueue_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'minimalzerif_enqueue_scripts' );
	function minimalzerif_enqueue_scripts() {
		wp_enqueue_script( 'scripts', get_stylesheet_directory_uri() . '/js/scripts.js', array(), '1.0.2', true );
	}
}

/**
 *	WP Dequeue Scripts
 */
if( !function_exists( 'minimalzerif_dequeue_scripts' ) ) {
	add_action( 'wp_print_scripts', 'minimalzerif_dequeue_scripts', 100 );
	function minimalzerif_dequeue_scripts() {
		wp_dequeue_script( 'zerif_script' );
	}
}

/**
 *	Customizer Style
 */
if( !function_exists( 'minimalzerif_customizer_style' ) ) {
	add_action( 'wp_head', 'minimalzerif_customizer_style' );
	function minimalzerif_customizer_style() {
		echo '<style type="text/css">';
			echo '.top-navigation .container .logo-image {background: url('. get_theme_mod( 'minimalzerif_logo', get_stylesheet_directory_uri() . '/images/white-logo.png' ) .') no-repeat center;}';
			echo '.top-navigation.fixed-navigation .container .logo-image {background: url('. get_theme_mod( 'minimalzerif_stickylogo', get_stylesheet_directory_uri() . '/images/black-logo.png' ) .') no-repeat center;}';
			echo '.menu-color .top-navigation .container .logo-image {background: url('. get_theme_mod( 'minimalzerif_stickylogo', get_stylesheet_directory_uri() . '/images/black-logo.png' ) .') no-repeat center;}';
		echo '</style>';
	}
}