<?php
/**
* Plugin Name: Hostgator WalkMe Plugin
* Plugin URI: http://hostgator.com/
* Description: Hostgator.com's WalkMe plugin
* Version: 1.0
* Author: Snappy 
* Author URI: http://www.hostgator.com
* License: A "Slug" license name e.g. GPL12
*/


add_action( 'admin_enqueue_scripts', 'beacon_admin_enqueue' );
function beacon_admin_enqueue() {
wp_enqueue_script( 'beacon_admin_enqueue', plugins_url( '/js/walkme.js', __FILE__ ),array());
}
