<?php
/**
 * Template for author bios.
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage author-bio.php
 */
global $blogcentral_opts;
?>
<div class="author-bio">
	<div class="author-avatar">
		<?php
		// Get the author meta.
		$cust_fields = '';
		
		global $fourbzcore_plugin;
		
		if ( isset( $fourbzcore_plugin ) && method_exists( $fourbzcore_plugin, 'get_user_meta' ) ) {
			$cust_fields = $fourbzcore_plugin->get_user_meta( $wp_query->post->post_author );
		}
		
		/* 
		 * Get the avatar in this order
		 * 	- Avatar option on the user profile page.
		 * 	- Default avatar image option on the theme options page.
		 * 	- Wordpress gravatar image.
		 */
		if ( ! empty( $cust_fields['avatar'] ) ) {
			$alt = ! empty( $cust_fields['avatar_alt'] )  ?
				esc_attr( $cust_fields['avatar_alt'] ) :
				'';
			$url = ! empty( $cust_fields['avatar'] ) ? esc_url( $cust_fields['avatar'] ) : '';
			$width = ! empty( $cust_fields['avatar_width'] ) ?
				esc_attr( $cust_fields['avatar_width'] ) :
				'';
			$height = ! empty( $cust_fields['avatar_height'] ) ?
				esc_attr( $cust_fields['avatar_height'] ) :
				'';
			
			$avatar =  '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) .
				'" rel="author"><img src="' . $url . '"';
			
			if ( $alt ) {
				$avatar .= ' alt="' . $alt  . '"';
			}
			if ( $width ) {
				$avatar .= ' width="' . $width . '"';
			}
			if ( $height ) {
				$avatar .= ' height="' . $height . '"';
			}
			
			$avatar .= ' /></a>';
			
			echo $avatar;
		} elseif( ! empty( $blogcentral_opts['default_user_img'] ) ) {
			$alt = ! empty( $blogcentral_opts['default_user_img_alt'] ) ?
				esc_attr( $blogcentral_opts['default_user_img_alt'] ) :
				'';
			$url = ! empty( $blogcentral_opts['default_user_img'] ) ? esc_url( $blogcentral_opts['default_user_img'] ) : '';
			$width = ! empty( $blogcentral_opts['default_user_img_width'] ) ?
				esc_attr( $blogcentral_opts['default_user_img_width'] ) :
				'';
			$height = ! empty( $blogcentral_opts['default_user_img_height'] ) ?
				esc_attr( $blogcentral_opts['default_user_img_height'] ) :
				'';
			
			$avatar =  '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) .
				'" rel="author"><img src="' . $url . '"';
			
			if(  $alt ) {
				$avatar .= ' alt="' . $alt  . '"';
			}
			if ( $width ) {
				$avatar .= ' width="' . $width . '"';
			}
			if ( $height ) {
				$avatar .= ' height="' . $height . '"';
			}
			
			$avatar .= ' /></a>';
			
			echo $avatar;
		} else {
			echo get_avatar( get_the_author_meta( 'user_email' ) );
		}
		?>
	</div><!-- End author-avatar --><?php //no space here or will break layout ?><div class="author-details text-format">
		<!-- Display the author details. -->
		<h3 class="author-name title">
		 <?php echo esc_html( get_the_author_meta( 'display_name' ) );  
			if ( ! empty( $cust_fields['title'] ) ) {
				echo ', ';
				echo esc_html( $cust_fields['title'] );
			}
		 ?>
		</h3>
		<div class="author-description">
			<?php the_author_meta( 'description' ); ?>
			<div class="author-link">
				<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" rel="author">
					<?php printf( __( 'All posts by %s ', BLOGCENTRAL_TXT_DOMAIN ), get_the_author() ); ?>
				</a>
			</div><!-- End .author-link	-->
			<?php $author_url = get_the_author_meta( 'url' );
			if ( ! empty( $author_url ) ) { ?>
			<p>
				<span class="title-lbl"><?php _e( 'Website:', BLOGCENTRAL_TXT_DOMAIN ); ?></span>
				<a href="<?php echo $author_url; ?>"><?php echo $author_url; ?></a>
			</p>
			<?php }
			
			$author_email = get_the_author_meta( 'email' );
			if ( ! empty( $author_email ) ) { ?>
			<p class="author-extra">
				<span class="title-lbl"><?php _e( 'Email:', BLOGCENTRAL_TXT_DOMAIN ); ?></span>
				<a href="mailto:<?php echo $author_email; ?>"><?php echo $author_email; ?></a>
			</p>
			<?php }		
					
			//Output custom contact fields
			if ( ! empty( $cust_fields['contact_facebook'] ) ) : ?>
				<a class="author-contact" href="<?php echo esc_url( $cust_fields['contact_facebook'] ); ?>"><i class="fa fa-facebook"></i></a>
			<?php endif;
			if ( ! empty( $cust_fields['contact_twitter'] ) ) : ?>
				<a class="author-contact" href="<?php echo esc_url( $cust_fields['contact_twitter'] ); ?>"><i class="fa fa-twitter"></i></a>
			<?php endif; 
			if ( ! empty( $cust_fields['contact_google'] ) ) : ?>
				<a class="author-contact" href="<?php echo esc_url( $cust_fields['contact_google'] ); ?>"><i class="fa fa-google-plus"></i></a>
			<?php endif;
			if ( ! empty( $cust_fields['contact_linkedin'] ) ) : ?>
				<a class="author-contact" href="<?php echo esc_url( $cust_fields['contact_linkedin'] ); ?>"><i class="fa fa-linkedin"></i></a>
			<?php endif;
			if ( ! empty( $cust_fields['contact_instagram'] ) ) : ?>
				<a class="author-contact" href="<?php echo esc_url( $cust_fields['contact_instagram'] ); ?>"><i class="fa fa-instagram"></i></a>
			<?php endif;
			if ( ! empty( $cust_fields['contact_tumblr'] ) ) : ?>
				<a class="author-contact" href="<?php echo esc_url( $cust_fields['contact_tumblr'] ); ?>"><i class="fa fa-tumblr"></i></a>
			<?php endif;
			if ( ! empty( $cust_fields['contact_pinterest'] ) ) : ?>
				<a class="author-contact" href="<?php echo esc_url( $cust_fields['contact_pinterest'] ); ?>"><i class="fa fa-pinterest"></i></a>
			<?php endif; ?>
		</div><!-- End author-description -->
	</div><!-- End author-details -->
</div><!-- End entry-author-info -->