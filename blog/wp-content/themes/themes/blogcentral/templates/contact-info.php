<?php
/** 
 * Template to construct and output contact information
 * 
 * Overrides the default template provided by the 4bzCore plugin.
 *
 * @since BlogCentral 1.0.0
 *
 * @param global variable $blogcentral_layout_opts, passed to this template from the blogcentral_contact_info function.
 * @param global variable $blogcentral_contact_info, passed to this template from the blogcentral_contact_info function.
 *
 * @package BlogCentral
 * @subpackage contact-info.php
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

// Initialize variables.
$contact_frag = $address_frag = $email_frag = $phone_frag = $url_frag = $share_frag = $login_frag = '';

// Construct the html fragments for contact information.
if ( ! empty( $blogcentral_layout_opts['show_address'] ) && ! empty( $blogcentral_contact_info['contact_address'] ) ) {
	$address_icon = '';
	
	if ( ! empty( $blogcentral_contact_info['address_icon'] ) ) {
		$address_icon = '<i class="fa ' . esc_attr( $blogcentral_contact_info['address_icon'] ) . '"></i>';
	}
	
	$address_frag = $address_icon . '<span class="contact-address"><a href="#">' . esc_html( $blogcentral_contact_info['contact_address'] ) .
		'</a></span>';
}

if ( ! empty( $blogcentral_layout_opts['show_email'] ) && ! empty( $blogcentral_contact_info['contact_email'] ) ) {
	$email_icon = '';
	
	if ( ! empty( $blogcentral_contact_info['email_icon'] ) ) {
		$email_icon = '<i class="fa ' . esc_attr( $blogcentral_contact_info['email_icon'] ) . '"></i>';
	}
	
	$email_frag = $email_icon . '<span class="contact-email"><a href="' . esc_url( $blogcentral_contact_info['contact_email'] ) . '">' .
		esc_html( $blogcentral_contact_info['contact_email'] ) . '</a></span>';
}

if ( ! empty( $blogcentral_layout_opts['show_phone'] ) && ! empty( $blogcentral_contact_info['contact_phone'] ) ) {
	$phone_icon = '';
	
	if ( ! empty( $blogcentral_contact_info['phone_icon'] ) ) {
		$phone_icon = '<i class="fa ' . esc_attr( $blogcentral_contact_info['phone_icon'] ) . '"></i>';
	}
	
	$phone_frag = $phone_icon . '<span class="contact-phone"><a href="#">' . esc_html( $blogcentral_contact_info['contact_phone'] ) . '</a></span>';
}

if ( ! empty( $blogcentral_layout_opts['show_url'] ) && ! empty( $blogcentral_contact_info['contact_url'] ) ) {
	$url_icon = '';
	
	if ( ! empty( $blogcentral_contact_info['url_icon'] ) ) {
		$url_icon = '<i class="fa ' . esc_attr( $blogcentral_contact_info['url_icon'] ) . '"></i>';
	}
	
	$url_frag = $url_icon . '<span class="contact-url"><a href="' . esc_url( $blogcentral_contact_info['contact_url'] ) . '">' .
		esc_html( $blogcentral_contact_info['contact_url'] ) . '</a></span>';
}

if ( ! empty( $blogcentral_layout_opts['show_social'] ) ) {
	$share_title = '';
	$share_frag = '<div class="social-share';
	
	$share_frag .= '">';
	
	if ( ! empty( $blogcentral_layout_opts['share_title'] ) ) {
		$share_frag .= '<span class="share-title">' . esc_html( $blogcentral_layout_opts['share_title'] ) . '</span>';
	}
	
	if ( ! empty( $blogcentral_contact_info['contact_facebook'] ) ) {
		$facebook_icon = '';
	
		if ( ! empty( $blogcentral_contact_info['facebook_icon'] ) ) {
			$facebook_icon = '<i class="fa ' . esc_attr( $blogcentral_contact_info['facebook_icon'] ) . '"></i>';
		}
		
		$share_frag .= "<a href='" . esc_url( $blogcentral_contact_info['contact_facebook'] ) . "'>$facebook_icon</a>"; 
	}
	
	if ( ! empty( $blogcentral_contact_info['contact_twitter'] ) ) {
		$twitter_icon = '';
	
		if ( ! empty( $blogcentral_contact_info['twitter_icon'] ) ) {
			$twitter_icon = '<i class="fa ' . esc_attr( $blogcentral_contact_info['twitter_icon'] ) . '"></i>';
		}
		
		$share_frag .= "<a href='" . esc_url( $blogcentral_contact_info['contact_twitter'] ) . "'>$twitter_icon</a>"; 
	
	}
	
	if ( ! empty( $blogcentral_contact_info['contact_google'] ) ) {
		$google_icon = '';
	
		if ( ! empty( $blogcentral_contact_info['google_icon'] ) ) {
			$google_icon = '<i class="fa ' . esc_attr( $blogcentral_contact_info['google_icon'] ) . '"></i>';
		}
		
		$share_frag .= "<a href='" . esc_url( $blogcentral_contact_info['contact_google'] ) . "'>$google_icon</a>"; 
	}
	
	if ( ! empty( $blogcentral_contact_info['contact_linkedin'] ) ) {
		$linkedin_icon = '';
	
		if ( ! empty( $blogcentral_contact_info['linkedin_icon'] ) ) {
			$linkedin_icon = '<i class="fa ' . esc_attr( $blogcentral_contact_info['linkedin_icon'] ) . '"></i>';
		}
		
		$share_frag .= "<a href='" . esc_url( $blogcentral_contact_info['contact_linkedin'] ) . "'>$linkedin_icon</a>"; 
	}
	
	if ( ! empty( $blogcentral_contact_info['contact_instagram'] ) ) {
		$instagram_icon = '';
	
		if ( ! empty( $blogcentral_contact_info['instagram_icon'] ) ) {
			$instagram_icon = '<i class="fa ' . esc_attr( $blogcentral_contact_info['instagram_icon'] ) . '"></i>';
		}
		
		$share_frag .= "<a href='" . esc_url( $blogcentral_contact_info['contact_instagram'] ) . "'>$instagram_icon</a>"; 
	}
	
	if ( ! empty( $blogcentral_contact_info['contact_tumblr'] ) ) {
		$tumblr_icon = '';
	
		if ( ! empty( $blogcentral_contact_info['tumblr_icon'] ) ) {
			$tumblr_icon = '<i class="fa ' . esc_attr( $blogcentral_contact_info['tumblr_icon'] ) . '"></i>';
		}
		
		$share_frag .= "<a href='" . esc_url( $blogcentral_contact_info['contact_tumblr'] ) . "'>$tumblr_icon</a>"; 
	}
	
	if ( ! empty( $blogcentral_contact_info['contact_pinterest'] ) ) {
		$pinterest_icon = '';
	
		if ( ! empty( $blogcentral_contact_info['pinterest_icon'] ) ) {
			$pinterest_icon = '<i class="fa ' . esc_attr( $blogcentral_contact_info['pinterest_icon'] ) . '"></i>';
		}
		
		$share_frag .= "<a href='" . esc_url( $blogcentral_contact_info['contact_pinterest'] ) . "'>$pinterest_icon</a>"; 
	}
	
	$share_frag .= '</div>';
}

// If there is no contact information to display, then output nothing.
if ( $address_frag || $phone_frag || $email_frag || $url_frag || $share_frag ) : 

// Add class for vertical or horizontal list layout, depending on the option set on theme options page.
 ?>
	<ul class="contact-info-list<?php 
		if ( isset( $blogcentral_layout_opts['contact_info_layout'] ) && ( '1' === $blogcentral_layout_opts['contact_info_layout'] ) ) { 
			echo ' list-horizontal"';
		} else { 
			echo ' list-vertical"'; 
		}
		?>>
		<?php if ( $address_frag ) :	?>	
		<li><?php echo $address_frag; ?>
		</li>
		<?php endif;
		if ( $phone_frag ) : ?>
			<li><?php echo $phone_frag; ?>
			</li>
		<?php endif;
		if ( $email_frag ) : ?>
			<li><?php echo $email_frag; ?>
			</li>
		<?php endif;
		if ( $url_frag ) : ?>
			<li><?php echo $url_frag; ?>
			</li>
		<?php endif;
		if ( $share_frag ) : ?>
			<li><?php echo $share_frag; ?>
			</li>
		<?php endif; ?>
	</ul>	
<?php endif;