<?php 
/* 
 * Template name: About Me
 *
 * Use this template to create an about me page. It is recommended that you create a backup copy of this file
 * before modifying, and modify very carefully, as you might delete important layout data.
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage page-template-about-me.php
 */

// Display the header and call wp_head().
get_header();

if ( isset( $blogcentral_opts['show_color_chooser'] ) ) {
	// Display the dynamic color chooser.
	blogcentral_output_color_chooser();
}

// Output precontent area.
blogcentral_output_precontent_frag( $blogcentral_opts, __( 'About Me', BLOGCENTRAL_TXT_DOMAIN ), true );
?>
<div id="main-content" class="about-me-template full-width"> 
	<!-- Start about-me section -->
	<div class="section components-wrap layout2 layout3">
		<ul class="components-list">
			<li class="component layout2 layout3 auto-width">
				<div class="component-media-wrap opp">
					<!-- Image that goes with the description below, change to suit your needs.	-->
					<img src="<?php echo BLOGCENTRAL_THEME_URL; ?>/images/preview/hatback.jpg" alt="<?php _e( 'hat back', BLOGCENTRAL_TXT_DOMAIN ); ?>" />
					<!-- Start skills section -->
	<?php
	if ( isset( $fourbzcore_plugin ) && method_exists( $fourbzcore_plugin->fourbzcore_shortcodes, 'progressbars' ) ) {
	?>
	<div style="margin-top: 18px;" class="skills section components-wrap layout2">
		<h3 class="section-title"><?php _e( 'My Skills', BLOGCENTRAL_TXT_DOMAIN ); ?></h3>
		<?php
		// Call function to display progressbars for skills. You can also use the shortcode builder available on the posts edit page, if the 4bzCore plugin is installed and activated.
		$progressbar_atts = array(
			"cols"					=>	"1",
			"items"					=>	array(
											"java"	=>	"98",
											"js"	=>	"23",
										),
		);
		
		echo $fourbzcore_plugin->fourbzcore_shortcodes->progressbars( $progressbar_atts );
		?>
	</div><!-- End .skills -->
	<?php
	}// End if isset plugin
	?>
				</div><!-- End component-media-wrap -->
				<div class="component-content-wrap">
					<div>
						<!-- Description content, change to suite your needs. -->
						<div class="post-title-top">
							<h2><?php _e( 'Programmer Extraordinaire', BLOGCENTRAL_TXT_DOMAIN ); ?></h2>
							<span class="header-tagline"><?php _e( 'Doing it since 1996', BLOGCENTRAL_TXT_DOMAIN ); ?></span>
						</div><!-- End .post-title-top -->
						<div class="content">
							<p>
								<?php echo "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
									tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
									quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
									consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
									cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
									proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
									Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
							?></p>
							<?php
								if ( isset( $fourbzcore_plugin ) && method_exists( $fourbzcore_plugin->fourbzcore_shortcodes, 'contact_info' ) ) {
										// Customize as needed.
										$atts = array(
											'show_social'			=>	true,
											'contact_facebook'		=>	'facebook.com',
											'facebook_icon'			=>	'fa-facebook',
											'contact_twitter'		=>	'twitter.com',
											'twitter_icon'			=>	'fa-twitter',
											'contact_google'		=>	'google.com',
											'google_icon'			=>	'fa-google',
											'contact_instagram'		=>	'instagram.com',
											'instagram_icon'		=>	'fa-instagram',
											'contact_linkedin'		=>	'linkedin.com',
											'linkedin_icon'			=>	'fa-linkedin',
											'contact_tumblr'		=>	'tumblr.com',
											'tumblr_icon'			=>	'fa-tumblr',
											'contact_pinterest'		=>	'pinterest.com',
											'pinterest_icon'		=>	'fa-pinterest',
										);
										
										echo $fourbzcore_plugin->fourbzcore_shortcodes->contact_info( $atts );
									}
							?>
							
						</div><!-- End .content -->
					</div> <!-- End .post-title -->
				</div><!-- End .component-content-wrap -->	 
			</li><!-- End .component -->
		</ul><!-- End .components-list -->
	</div><!-- End .components-wrap -->
	
	<?php
	/**
	 * Requires the 4bzCore Plugin. Modify or delete this section depending on your needs. You can also use the shortcode builder available on the posts edit page, if the 4bzCore plugin is installed and activated.
	 */	
	if ( isset( $fourbzcore_plugin ) && method_exists( $fourbzcore_plugin->fourbzcore_shortcodes, 'recent_posts' ) ) {
	?>
	<div class="min-div section components-wrap layout2">
	<?php
		/**
		 * Call shortcode function to display recent posts. You can use the shortcode builder on the post edit 
		 * toolbar to customize the recent posts.
		 */
		$posts_atts = array(
			"layout"		=>	"layout1",
			"cols"			=>	'3',
			"limit"			=>	'3',
			"title_text"	=>	__( "Some of My Recent Work", BLOGCENTRAL_TXT_DOMAIN ),
			"title_class"	=>	"section-title",
			"title_border"	=> 	"0",
			"show_title"	=>	true,
		);
		
		echo $fourbzcore_plugin->fourbzcore_shortcodes->recent_posts( $posts_atts );
	?>
	</div><!-- End .components-wrap -->
	<?php
	}// End isset plugin ?>
	<div>
		<div class="call-to-action connect">
				<?php _e( 'Do You Like What You See?', BLOGCENTRAL_TXT_DOMAIN ); ?> <span class="border-2"><?php _e( 'Hire Me', BLOGCENTRAL_TXT_DOMAIN ); ?> <i class="fa fa-caret-down"></i></span>
		</div><!-- End .call-to-action -->

		<?php 
		// Output contact form, if plugin is available
		if( isset( $fourbzcore_plugin ) && method_exists( $fourbzcore_plugin->fourbzcore_shortcodes, 'contact_form' ) ) {
			$posts_atts = array(
				'wrap_class'	=>	'hide-show', 
				'show_map'		=>	true,
			);
		
			echo $fourbzcore_plugin->fourbzcore_shortcodes->contact_form( $posts_atts );
		}// End if
		?>
	</div>
<?php 
get_footer();
	