<?php
/**
 *	Customizer
 */
if( !function_exists( 'minimalzerif_customizer' ) ) {
	add_action( 'customize_register', 'minimalzerif_customizer', 50 );
	function minimalzerif_customizer( $wp_customize ) {
		// Remove Panel
		$wp_customize->remove_panel( 'panel_about' );
		$wp_customize->remove_panel( 'panel_ribbons' );

		// Remove Section
		$wp_customize->remove_section( 'zerif_latestnews_section' );
		$wp_customize->remove_section( 'zerif_general_footer_section' );

		// Remove Setting & Control
		$wp_customize->remove_setting( 'zerif_logo' );
		$wp_customize->remove_control( 'zerif_logo' );
		$wp_customize->remove_setting( 'zerif_contactus_email' );
		$wp_customize->remove_control( 'zerif_contactus_email' );
		$wp_customize->remove_setting( 'zerif_contactus_button_label' );
		$wp_customize->remove_control( 'zerif_contactus_button_label' );
		$wp_customize->remove_setting( 'zerif_contactus_recaptcha_show' );
		$wp_customize->remove_control( 'zerif_contactus_recaptcha_show' );
		$wp_customize->remove_setting( 'zerif_contactus_sitekey' );
		$wp_customize->remove_control( 'zerif_contactus_sitekey' );
		$wp_customize->remove_setting( 'zerif_contactus_secretkey' );
		$wp_customize->remove_control( 'zerif_contactus_secretkey' );

		// Add Setting & Control: Disable logo image
		$wp_customize->add_setting( 'minimalzerif_disable_logoimage', array(
			'sanitize_callback'	=> 'zerif_sanitize_text'
		) );
		$wp_customize->add_control( 'minimalzerif_disable_logoimage', array(
			'type'		=> 'checkbox',
			'label'		=> __( 'Disable logo text?', 'minimalzerif'),
			'section'	=> 'zerif_general_section',
			'priority'	=> 4
		) );

		// Add Setting & Control: Logo
		$wp_customize->add_setting( 'minimalzerif_logo', array(
			'sanitize_callback' => 'esc_url_raw',
			'default'			=> get_stylesheet_directory_uri() . '/images/white-logo.png'
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'minimalzerif_logo', array(
			'label'		=> __( 'White Logo', 'minimalzerif' ),
			'section'	=> 'zerif_general_section',
			'settings'	=> 'minimalzerif_logo',
			'priority'	=> 5
		) ) );

		// Add Setting & Control: Sticky Logo
		$wp_customize->add_setting( 'minimalzerif_stickylogo', array(
			'sanitize_callback' => 'esc_url_raw',
			'default'			=> get_stylesheet_directory_uri() . '/images/black-logo.png'
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'minimalzerif_stickylogo', array(
			'label'		=> __( 'Black Logo', 'minimalzerif' ),
			'section'	=> 'zerif_general_section',
			'settings'	=> 'minimalzerif_stickylogo',
			'priority'	=> 6
		) ) );

		$wp_customize->add_setting( 'minimalzerif_contactus_entry', array( 'sanitize_callback' => 'zerif_sanitize_text','default' => __( '<b>Eleven Madison Park</b><br />11 Madison Ave<br />New York, NY 10010<br />U.S.A.<br />', 'minimalzerif' ) ) );
		$wp_customize->add_control( new Zerif_Customize_Textarea_Control( $wp_customize, 'minimalzerif_contactus_entry', array(
				'label'		=> __( 'Entry:', 'minimalzerif' ),
				'section'	=> 'zerif_contactus_section',
				'settings'	=> 'minimalzerif_contactus_entry',
				'priority'	=> 5
		)) );
	}
}
?>