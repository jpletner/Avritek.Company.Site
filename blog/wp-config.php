<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
define('WP_CACHE', true); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/home3/avr/public_html/avritek.com/blog/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
/** The name of the database for WordPress */
define('DB_NAME', 'avr_wrdp4');

/** MySQL database username */
define('DB_USER', 'avr_wrdp4');

/** MySQL database password */
define('DB_PASSWORD', 'n87kYyiOLmvX7k');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '5DrQ-/2B1A1-yFc#a-qtRQ~mcCJdw)Tmyy0og|TCc$kRO$<P;*zbFgl?)!ho0t~qp=2A');
define('SECURE_AUTH_KEY',  'JrnK7aK#WllV;Ob6ib(-3Rx*jtZrM2Fv0Q=z_R@RmFW$Tl-?MfycjXya\`oR5patD_jWjQ;T*');
define('LOGGED_IN_KEY',    'Edo3J~3y>(xlc/!bj3Xal3WI*>c@>tI6xrrl|4_KzSJ~c2bSQO$uanydU-LI94SN/');
define('NONCE_KEY',        'NR?ZEraEhx?Jx_JH#/oif(1$wFm>zV!GIc\`wX<8-f#)yh^e@6s5>VZ/LLJT!cgXIKyYNF517');
define('AUTH_SALT',        '1c@hc)WQe@m8l4E83eT8~<vOy71ZwFdU/!jO0|S:ZxuUH6t:S7ujnt\`di0!~(');
define('SECURE_AUTH_SALT', 'yj8ivxidXi1Oe#HB*S<U^2Q)5Dw/cl:)fg\`EXu#*M_l^B_q;5m=h^fY~WLRx*P3e@;Q\`#u:B)1W');
define('LOGGED_IN_SALT',   'bnq*=sr5xXkJ1b;/rjMPNbCUfl^5*F@_f6YMA\`AHtmG~dEI3t);uHCpYiriRd0UVn<\`wl/W7Adp/=\`exDFUt');
define('NONCE_SALT',       '0WtKUyAOaq\`cSGwNopkvFX$~s@3_j>Y(Fi@y>AL2uH<gwDHmy3XV?q4(ihbA');


/**#@-*/
define('AUTOSAVE_INTERVAL', 600 );
define('WP_POST_REVISIONS', 1);
define( 'WP_CRON_LOCK_TIMEOUT', 120 );
define( 'WP_AUTO_UPDATE_CORE', true );
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
add_filter( 'auto_update_plugin', '__return_true' );
add_filter( 'auto_update_theme', '__return_true' );
