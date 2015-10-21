<?php
/***
* WP-SpamShield Compatibility
* Ver 1.9.6.2
***/

if( !defined( 'ABSPATH' ) || !defined( 'WPSS_VERSION' ) ) {
	if( !headers_sent() ) { header('HTTP/1.1 403 Forbidden'); }
	die( 'ERROR: Direct access to this file is not allowed.' );
}

class WPSS_Compatibility {

	/***
	* WP-SpamShield Compatibility Class
	* Compatibility deconfliction for some of the plugins listed in the Known Issues and Plugin Conflicts ( http://www.redsandmarketing.com/plugins/wp-spamshield/known-conflicts/ )
	* Where possible, apply compatibility fixes or workarounds
	***/

	function __construct() {
		/***
		* Do nothing...for now
		***/
	}

	static public function supported() {
		/***
		* Check if supported 3rd party plugins are active that require exceptions
		***/

		/* Gravity Forms ( http://www.gravityforms.com/ ) */

		if( rs_wpss_is_plugin_active( 'gravityforms/gravityforms.php', TRUE ) ) {
			if( !defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { define( 'WPSS_SOFT_COMPAT_MODE', TRUE ); }
		}

	}

	static public function conflict_check() {
		/***
		* Check if plugins with known issues are active, then deconflict
		***/

		/* New User Approve Plugin ( https://wordpress.org/plugins/new-user-approve/ ) */
		if( class_exists( 'pw_new_user_approve' ) ) {
			add_action( 'register_post', array('WPSS_Compatibility','deconflict_nua_01'), -10 );
			add_action( 'registration_errors', array('WPSS_Compatibility','deconflict_nua_02'), -10 );
		}

		/* Affiliates Plugin ( https://wordpress.org/plugins/affiliates/ ) */
		if( defined( 'AFFILIATES_CORE_VERSION' ) || class_exists( 'Affiliates_Registration' ) ) {
			if( class_exists( 'Affiliates_Registration' ) && method_exists( 'Affiliates_Registration', 'update_affiliate_user' ) ) {
				add_filter( 'user_registration_email', 'rs_wpss_sanitize_new_user_email' );
			}
			if( class_exists( 'Affiliates_Registration' ) && method_exists( 'Affiliates_Registration', 'render_form' ) ) {
				add_filter( 'affiliates_registration_after_fields', 'rs_wpss_register_form_append' );
			}
		}
	}

	static public function deconflict_nua_01() {
		if( class_exists( 'pw_new_user_approve' ) && method_exists( 'pw_new_user_approve', 'create_new_user' ) && has_filter( 'register_post', array( pw_new_user_approve::instance(), 'create_new_user' ) ) ) {
			remove_action( 'register_post', array( pw_new_user_approve::instance(), 'create_new_user' ), 10 );
			add_action( 'registration_errors', array('WPSS_Compatibility','deconflict_nua_01_01'), 9998, 3 );
		}
	}

	static public function deconflict_nua_01_01( $errors, $user_login, $user_email ) {
		if( !empty( $errors ) && is_object( $errors ) && $errors->get_error_code() ) { return $errors; }
		if( class_exists( 'pw_new_user_approve' ) && method_exists( 'pw_new_user_approve', 'create_new_user' ) ) {
			if( empty( $errors ) || !is_object( $errors ) ) { $errors = new WP_Error; }
			pw_new_user_approve::instance()->create_new_user( $user_login, $user_email, $errors );
		}
		return $errors;
	}

	static public function deconflict_nua_02() {
		if( class_exists( 'pw_new_user_approve' ) && method_exists( 'pw_new_user_approve', 'show_user_pending_message' ) && has_filter( 'registration_errors', array( pw_new_user_approve::instance(), 'show_user_pending_message' ) ) ) {
			remove_filter( 'registration_errors', array( pw_new_user_approve::instance(), 'show_user_pending_message' ), 10 );
			if( function_exists( 'login_header' ) && function_exists( 'login_footer' ) ) {
				add_filter( 'registration_errors', array('WPSS_Compatibility','deconflict_nua_02_01'), 9999 );
			}
		}
	}

	static public function deconflict_nua_02_01( $errors ) {
		if( !empty( $errors ) && is_object( $errors ) && $errors->get_error_code() ) { return $errors; }
		if( class_exists( 'pw_new_user_approve' ) && method_exists( 'pw_new_user_approve', 'show_user_pending_message' ) ) {
			if( empty( $errors ) || !is_object( $errors ) ) { $errors = new WP_Error; }
			pw_new_user_approve::instance()->show_user_pending_message( $errors );
		}
		return $errors;
	}

	static public function comment_form() {
		/***
		* Comments Form Compatibility 
		***/

		if( rs_wpss_is_admin_sproc() ) { return; }

		/* Vantage Theme by Appthemes ( https://www.appthemes.com/themes/vantage/ ) */
		global $wpss_theme_vantage;
		if( !empty( $wpss_theme_vantage ) ) { return TRUE; }
		elseif( defined( 'APP_FRAMEWORK_DIR_NAME' ) && defined( 'VA_VERSION' ) ) { return TRUE; }
		else {
			$wpss_theme = wp_get_theme();
			$theme_name = $wpss_theme->get( 'Name' );
			$theme_author = $wpss_theme->get( 'Author' );
			if( 'Vantage' === $theme_name && 'AppThemes' === $theme_author ) { return TRUE; }
		}

		/* Add next here... */

		return FALSE;
	}

	static public function footer_js() {
		/***
		* Footer JS Compatibility
		***/

		if( rs_wpss_is_admin_sproc() ) { return; }

		$js = '';

		/* Vantage Theme by Appthemes ( https://www.appthemes.com/themes/vantage/ ) */
		global $wpss_theme_vantage;
		$v_js = ', #add-review-form';
		if( !empty( $wpss_theme_vantage ) ) { $js .= $v_js; }
		elseif( defined( 'APP_FRAMEWORK_DIR_NAME' ) && defined( 'VA_VERSION' ) ) { $js .= $v_js; }
		else {
			$wpss_theme = wp_get_theme();
			$theme_name = $wpss_theme->get( 'Name' );
			$theme_author = $wpss_theme->get( 'Author' );
			if( 'Vantage' === $theme_name && 'AppThemes' === $theme_author ) { $js .= $v_js; }
		}

		/* Add next here... */

		return $js;
	}

	static public function misc_form_bypass() {
		/***
		* Miscellaneous Form Spam Check Bypass 
		***/
		
		/* Setup necessary variables */
		$url		= rs_wpss_get_url();
		$url_lc		= rs_wpss_casetrans('lower',$url);
		$req_uri	= $_SERVER['REQUEST_URI'];
		$req_uri_lc	= rs_wpss_casetrans('lower',$req_uri);
		$post_count = count( $_POST );
		$ip			= rs_wpss_get_ip_addr();
		$user_agent = rs_wpss_get_user_agent();
		$referer	= rs_wpss_get_referrer();

		/* IP / PROXY INFO - BEGIN */
		global $wpss_ip_proxy_info;
		if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
		extract( $wpss_ip_proxy_info );
		/* IP / PROXY INFO - END */

		/* GEOLOCATION */
		if( $post_count == 6 && isset( $_POST['updatemylocation'], $_POST['log'], $_POST['lat'], $_POST['country'], $_POST['zip'], $_POST['myaddress'] ) ) { return TRUE; }

		/* WP Remote */
		if( defined( 'WPRP_PLUGIN_SLUG' ) && !empty( $_POST['wpr_verify_key'] ) && preg_match( "~\ WP\-Remote$~", $user_agent ) && preg_match( "~\.amazonaws\.com$~", $reverse_dns ) ) { return TRUE; }

		/* Ecommerce Plugins */
		if( ( rs_wpss_is_ssl() || !empty( $_POST['add-to-cart'] ) || !empty( $_POST['add_to_cart'] ) || !empty( $_POST['addtocart'] ) || !empty( $_POST['product-id'] ) || !empty( $_POST['product_id'] ) || !empty( $_POST['productid'] ) || ( preg_match( "~^PayPal\ IPN~", $user_agent ) && preg_match( "~(^|\.)paypal\.com$~", $reverse_dns ) ) ) && rs_wpss_is_ecom_enabled() ) { return TRUE; }

		/* WooCommerce Payment Gateways */
		if( rs_wpss_is_woocom_enabled() ) {
			if( ( preg_match( "~^PayPal\ IPN~", $user_agent ) && preg_match( "~(^|\.)paypal\.com$~", $reverse_dns ) ) || strpos( $req_uri, 'WC_Gateway_Paypal' ) !== FALSE ) { return TRUE; }
			if( preg_match( "~(^|\.)payfast\.co\.za$~", $reverse_dns ) || ( strpos( $req_uri, 'wc-api' ) !== FALSE && strpos( $req_uri, 'WC_Gateway_PayFast' ) !== FALSE ) ) { return TRUE; }
			/* Plugin: 'woocommerce-gateway-payfast/gateway-payfast.php' */
			if( preg_match( "~((\?|\&)wc\-api\=WC_(Addons_)?Gateway_|/wc\-api/.*WC_(Addons_)?Gateway_)~", $req_uri ) ) { return TRUE; }
			/* $wc_gateways = array( 'WC_Gateway_BACS', 'WC_Gateway_Cheque', 'WC_Gateway_COD', 'WC_Gateway_Paypal', 'WC_Addons_Gateway_Simplify_Commerce', 'WC_Gateway_Simplify_Commerce' ); */
		}

		/* Easy Digital Downloads Payment Gateways */
		if( defined( 'EDD_VERSION' ) ) {
			if( ( preg_match( "~^PayPal\ IPN~", $user_agent ) && preg_match( "~(^|\.)paypal\.com$~", $reverse_dns ) ) || ( !empty( $_GET['edd-listener'] ) && $_GET['edd-listener'] === 'IPN' )  || ( strpos( $req_uri, 'edd-listener' ) !== FALSE && strpos( $req_uri, 'IPN' ) !== FALSE ) ) { return TRUE; }
			if( ( !empty( $_GET['edd-listener'] ) && $_GET['edd-listener'] === 'amazon' ) || ( strpos( $req_uri, 'edd-listener' ) !== FALSE && strpos( $req_uri, 'amazon' ) !== FALSE ) ) { return TRUE; }
			if( !empty( $_GET['edd-listener'] ) || strpos( $req_uri, 'edd-listener' ) !== FALSE ) { return TRUE; }
		}

		/* Gravity Forms PayPal Payments Standard Add-On ( http://www.gravityforms.com/add-ons/paypal/ ) */
		if( ( defined( 'GF_MIN_WP_VERSION' ) && defined( 'GF_PAYPAL_VERSION' ) ) || ( class_exists( 'GFForms' ) && class_exists( 'GF_PayPal_Bootstrap' ) ) ) {
			if( $url === WPSS_SITE_URL.'/?page=gf_paypal_ipn' && isset( $_POST['ipn_track_id'], $_POST['payer_id'], $_POST['receiver_id'], $_POST['txn_id'], $_POST['txn_type'], $_POST['verify_sign'] ) ) { return TRUE; }
		}

		/* PayPal IPN */
		if(
			isset( $_POST['ipn_track_id'], $_POST['payer_id'], $_POST['payment_type'], $_POST['payment_status'], $_POST['receiver_id'], $_POST['txn_id'], $_POST['txn_type'], $_POST['verify_sign'] ) &&
			FALSE !== strpos( $req_uri_lc, 'paypal' ) &&
			FALSE !== strpos( $req_uri_lc, 'ipn' ) &&
			$user_agent === 'PayPal IPN ( https://www.paypal.com/ipn )' &&
			$reverse_dns === 'notify.paypal.com' &&
			$fcrdns === '[Verified]'
			) { return TRUE; }

		/* Clef */
		if( defined( 'CLEF_VERSION' ) ) {
			if( preg_match( "~^Clef/[0-9](\.[0-9]+)+\ \(https\://getclef\.com\)$~", $user_agent ) && preg_match( "~((^|\.)clef\.io|\.amazonaws\.com)$~", $reverse_dns ) ) { return TRUE; }
		}

		/* OA Social Login */
		if( defined( 'OA_SOCIAL_LOGIN_VERSION' ) ) {
			$ref_dom_rev = strrev( rs_wpss_get_domain( $referer ) ); $oa_dom_rev = strrev( 'api.oneall.com' );
			if( $post_count >= 4 && isset( $_GET['oa_social_login_source'], $_POST['oa_action'], $_POST['oa_social_login_token'], $_POST['connection_token'], $_POST['identity_vault_key'] ) && $_POST['oa_action'] === 'social_login' && strpos( $ref_dom_rev, $oa_dom_rev ) === 0 ) { return TRUE; }
		}

		/* Nothing was triggered */
		return FALSE;
	}

}
