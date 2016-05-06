<?php 
/** 
 * Template to output progressbars. 
 * 
 * Overrides the default template provided by the 4bzCore plugin.
 *
 * @since BlogCentral 1.0.0
 *
 * @param array global variable $blogcentral_layout_opts, passed to this template from the blogcentral_progressbars function.
 * @param array global variable $blogcentral_items, holds the labels and percentages for the 4bzcore progressbars. Passed to this template from the blogcentral_progressbars function.
 *
 * @package BlogCentral
 * @subpackage progressbars.php
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

$items_html = '';

if ( isset( $blogcentral_items ) && is_array( $blogcentral_items ) && 0 < count( $blogcentral_items ) ) {
	foreach ( $blogcentral_items as $key => $val ) {
		$name = esc_html( $key );
		$value = esc_html( $val );
		$items_html .= "<li class='component'><div class='component-gutter'>
		<div class='skill-lbl-cont'><span class='skill-lbl'>$name</span><span class='skill-percent'>$value</span></div><div class='progressbar-cont'><div data-percentage='$value' class='progressbar'></div>
		</div></div></li>";
	}
	
	echo $items_html;
}

// Matching ending tags for the <div> wrapper and <ul> tags
if ( isset( $blogcentral_ender ) ) {
	echo $blogcentral_ender;
}