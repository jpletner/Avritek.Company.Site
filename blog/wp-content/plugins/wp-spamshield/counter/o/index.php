<?php
/*
index.php
Version: 20150904.01
Author: Red Sand
http://www.redsandmarketing.com/plugins/

This script keeps search engines, bots, and unwanted visitors from viewing your private plugin directory contents.
 
You can avoid the need for pages like this by adding a single line of code to the beginning of your .htaccess file:
	## Add the following line to the beginning of your .htaccess for security and SEO.
	Options All -Indexes
	## This will turn off indexes so your site won't reveal contents of directories that don't have an index file.
*/

error_reporting(0);

/* We're going to redirect bots and human visitors to the website root. */
$new_url =  idx_get_site_url();
header( 'Location: '.$new_url, true, 301 );

function idx_get_site_url() {
	$url  = idx_is_ssl() ? 'https://' : 'http://';
	$url .= idx_get_server_name();
	return $url;
}

function idx_is_ssl() {
	if( !empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) { return TRUE; }
	if( !empty( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) { return TRUE; }
	if( !empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) { return TRUE; }
	if( !empty( $_SERVER['HTTP_X_FORWARDED_SSL'] ) && 'off' !== $_SERVER['HTTP_X_FORWARDED_SSL'] ) { return TRUE; }
	return FALSE;
}

function idx_get_server_name() {
	$site_domain 	= $server_name = '';
	$env_http_host	= getenv('HTTP_HOST');
	$env_srvr_name	= getenv('SERVER_NAME');
	if( !empty( $_SERVER['HTTP_HOST'] ) ) { $server_name = $_SERVER['HTTP_HOST']; }
	elseif( !empty( $env_http_host ) ) { $server_name = $env_http_host; }
	elseif( !empty( $_SERVER['SERVER_NAME'] ) ) { $server_name = $_SERVER['SERVER_NAME']; }
	elseif( !empty( $env_srvr_name ) ) { $server_name = $env_srvr_name; }
	return strtolower( $server_name );
}

?>