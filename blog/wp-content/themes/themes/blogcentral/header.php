<?php
/**
 * Template for header
 *
 * Displays everything up to <div id="main-content">
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage header.php
 */
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php 
	// Get saved theme options.
	global $blogcentral_opts;
	
	if ( isset( $blogcentral_opts['favicon'] ) ) {
	?> <link rel="icon" type="image/x-icon" href="<?php echo esc_url( $blogcentral_opts['favicon'] ); ?>" />
	<?php } ?>
	
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5shiv.min.js"></script>
	<![endif]-->
<?php
	wp_head(); 
?>
</head>
<body <?php body_class(); ?>>
<?php
	
	$logo_frag = $contact_frag = $email_frag = $phone_frag = $url_frag = $social_frag = $share_frag = $back_img_style = '';	
	
	/*
	 * Using the theme options, construct all of the html fragments to display the logo, contact information, search form, and navigation menu.
	 */
	 
	// Construct logo
	if ( isset( $blogcentral_opts['logo'] ) ) {
		$width = isset( $blogcentral_opts['logo_width'] ) ? ' width="' . esc_attr( $blogcentral_opts['logo_width'] ) . '"' : '';
		$height = isset( $blogcentral_opts['logo_height'] ) ? ' height="' . esc_attr( $blogcentral_opts['logo_height'] ) . '"' : '';
		$alt = isset( $blogcentral_opts['logo_alt'] ) ? ' alt="' . esc_attr( $blogcentral_opts['logo_alt'] ) . '"' : '';
		
		$logo_frag = '<div id="logo"><a href="' . home_url() . '"><img src="' . esc_url( $blogcentral_opts['logo'] ) . '"' . $width . $height . $alt . ' /></a>';
		
		$logo_frag .= '<i class="fa fa-list"></i></div>';
	}
	
	// Construct main menu
	$menu_frag = '<nav id="access"><a class="screen-reader-txt skip-link" href="#content" ' .
		'title="' . __( 'Skip to content', BLOGCENTRAL_TXT_DOMAIN ) . '">' .
		__( 'Skip to content', BLOGCENTRAL_TXT_DOMAIN ) . '</a>';
		
	ob_start();
	
	wp_nav_menu( array( 
		'theme_location' => 'primary', 
	) );
		
	$menu_frag .= ob_get_clean() . '</nav>';
				
	$atts = '';
	$style = '';
	$sep = '';
	$content_frag = '';
	$contact_array = array();
	
	global $fourbzcore_plugin;
	
	if ( is_active_sidebar( 'header-top-widget' ) ) { 
		ob_start();
		dynamic_sidebar( 'header-top-widget' );
		$content_frag = ob_get_clean();
	} elseif ( ! empty( $blogcentral_opts['show_contact'] ) ) {
		if ( isset( $fourbzcore_plugin ) && method_exists( $fourbzcore_plugin->fourbzcore_shortcodes, 'contact_info' ) ) {
			$content_frag = $fourbzcore_plugin->fourbzcore_shortcodes->contact_info( $blogcentral_opts['contact_info'] );
		} // End if plugin exists
	}
	?>
	<div id="main-div"> 
		<header id="header" class="site-header layout1" role="banner">
		<?php
		
		if ( $content_frag ) {
			echo '<div id="header-top">';
			echo $content_frag . '</div>';
		}
		echo '<div id="header-mid">' . $logo_frag;
		
		if ( is_active_sidebar( 'header-widget' ) ) { 
			echo '<div id="header-widget">';
			dynamic_sidebar( 'header-widget' );
			echo '</div>';
		} elseif ( ! empty( $blogcentral_opts['show_search'] ) ) {
			echo '<div id="header-widget">';
			get_search_form( true );
			echo '</div>';
		}
			
		echo '</div><div id="header-btm">' . $menu_frag . '</div>';
			
		?>
		</header> <!-- End of header-->