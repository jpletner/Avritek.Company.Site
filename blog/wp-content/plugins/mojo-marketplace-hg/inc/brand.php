<?php
if( ! defined( 'MMAFF' ) ) {
	define( 'MMAFF', 'hostgator' );
}

function mm_brand_page_title_icon() {
	return "<img style='height: 55px; margin-top: -20px; position: relative; top: 20px; width: auto;' src='" . plugin_dir_url( dirname( __FILE__ ) ) . "img/mojo-hg-icon.png' title='MOJO Marketplace provided by Hostgator' />";
}
add_filter( 'mm_before_page_title', 'mm_brand_page_title_icon' );
