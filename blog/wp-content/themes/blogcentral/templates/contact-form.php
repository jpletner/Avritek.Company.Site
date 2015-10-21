<?php
/** 
 * Template to output a contact form
 *
 * Overrides the default template provided by the 4bzCore plugin.
 *
 * @since BlogCentral 1.0.0
 *
 * @param global variable $blogcentral_layout_opts, passed to this template from the shortcode display method.
 *
 * @package BlogCentral
 * @subpackage contact-form.php
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

// Global variable that holds the theme options.
global $blogcentral_opts;

$extra_class = '';

$contact_form = '';
if ( empty( $blogcentral_layout_opts['contact_form'] ) ) {
	if ( ! empty( $blogcentral_opts['contact_form'] ) ) {
		$contact_form = $blogcentral_opts['contact_form'];
	}
} else {
	$contact_form = $blogcentral_layout_opts['contact_form'];
}

if ( $contact_form && ! empty( $blogcentral_layout_opts['show_contact'] ) ) {
	$extra_class = ' contact-form-cols-2';
}

echo '<div class="blogcentral-contact-form-cont' . $extra_class . '">';

$key = isset( $blogcentral_layout_opts['google_app_id'] ) ? $blogcentral_layout_opts['google_app_id'] : 
	( isset( $blogcentral_opts['google_app_id'] ) ? $blogcentral_opts['google_app_id'] : null );

if ( $key && isset( $blogcentral_layout_opts['show_map'] ) && $blogcentral_layout_opts['show_map'] &&
	isset( $blogcentral_layout_opts['contact_address'] ) ) {
	$address = str_replace( ' ', '+', $blogcentral_layout_opts['contact_address'] );
?>
<div class="map"><iframe style="border:0" height="450" src="<?php echo esc_url('https://www.google.com/maps/embed/v1/search?key=' . $key . '&q=' . $address ); ?>">
</iframe></div>
<?php
}

if ( isset( $blogcentral_layout_opts['show_contact'] ) && $blogcentral_layout_opts['show_contact'] ) :
?>	
	<div class="contact-info-main">
		<?php 
			// Call the shortcode to construct and display the contact information.
			global $fourbzcore_plugin;
			
			if ( isset( $fourbzcore_plugin ) ) {
				// Don't show the contact form's title and tagline text again.
				$blogcentral_layout_opts['title_text'] = '';
				$blogcentral_layout_opts['tagline_text'] = '';
				
				echo $fourbzcore_plugin->fourbzcore_shortcodes->contact_info( $blogcentral_layout_opts );
			}
		?>
	</div>
<?php 
endif;

// Now call the shortcode to display the contact form.
if ( $contact_form ) {
	echo '<div class="contact-info-form">';
	echo do_shortcode( stripslashes( $contact_form ) );
	echo '</div>';
}

echo '</div>';
?>