<?php
/** 
 * Template to construct the beginning <ul> tag of a component list, with all necessary attributes
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage layout-wrapper-begin.php
 */

 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

// Add class to component list 
$items_wrap = 'components-list';
$extr_attrs = $sizer = '';

// Construct masonry options	
if ( isset( $blogcentral_layout_opts['masonry'] ) && $blogcentral_layout_opts['masonry'] &&
	( isset( $blogcentral_layout_opts['cols'] ) && 1 < $blogcentral_layout_opts['cols'] ) ) {
	$masonry_opts = array(
		'cols'			=>	$blogcentral_layout_opts['cols'], 
		'gutter'		=>	'.gutter-sizer',
		'selector'		=>	'.component',
		'isInitLayout'	=>	false,
	);
	
	$masonry_attrs = blogcentral_construct_masonry_options( $masonry_opts );
	$items_wrap .= " blogcentral-masonry";
	$extr_attrs .= " data-masonry-options='$masonry_attrs'";
	$sizer = "<li class='cols-$blogcentral_layout_opts[cols]-sizer grd-sizer'></li><li class='gutter-sizer'></li>";
}

// Construct the $blogcentral_header and $blogcentral_ender variables for use in the various template files.
$blogcentral_header = "<ul class='$items_wrap'$extr_attrs>$sizer";
$blogcentral_ender = '</ul>';