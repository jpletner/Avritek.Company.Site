<?php
/**
 * BlogCentral filter functions for the 4bzCore plugin
 *
 * These functions filter options, descriptions, and display of the shortcodes and widgets included in the 4bzCore plugin.
 *
 * @since BlogCentral 1.1.0
 *
 * @package BlogCentral
 * @subpackage 4bzcore.php
 ------------------------------------------------------------------------*/

/**
 * Start of filter functions for the 4bzCore shortcodes and 
 * widgets options.
 *-----------------------------------------------------------------------*/ 

/**
 * Construct and display options for posts. Used by the featured, recent, related, and popular posts shortcodes and widgets.
 *
 * @since 1.1.0
 *
 * @param string $component Required. Name of shortcode or widget whose options are being displayed.
 * @param array $opts Optional. Only relative if displaying a widget's options because these shortcode options are not saved to the database, they are used in the shortcode builder to generate the shortcode string.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_options_posts( $component, $opts = array(), $widget = false ) {
	if ( ! isset( $component ) ) {
		return;
	}

	// Get the global variables.
	global $blogcentral_opts;
	global $blogcentral_defaults;
	global $blogcentral_initial;
	
	if ( $opts ) {
		$opts = array_map( 'stripslashes_deep', $opts );
		$opts = wp_parse_args( $opts, $blogcentral_defaults['posts_landing'] );
	} else {
		$opts = $blogcentral_initial['posts_landing'];
	}
	
	$opts['name'] = $component;

	ob_start();
	blogcentral_display_component_layout_options( $component, $opts['layout'], $widget );
		
	blogcentral_display_component_layout_wraps( $opts, $widget ); 
	
	blogcentral_display_main_options( $opts, $widget );
	
	// Display options that are specific to posts display.
	blogcentral_display_posts_specific_opts( $opts, $widget );
	
	return ob_get_clean();
}

/**
 * Construct and display options for the featured posts shortcode and widget.
 *
 * Filters 4bzCore plugin's options.
 *
 * @since 1.1.0
 *
 * @param string $opts_str Required. 4bzCore plugin's default options.
 * @param array $opts Optional. Only relative if displaying a widget's options because these shortcode options are not saved to the database, they are used in the shortcode builder to generate the shortcode string.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_options_featured_posts( $opts_str, $opts = array(), $widget = false ) {
	$opts_str = '<div class="blogcentral-wrap text-format">' . blogcentral_options_posts( 'featured_posts', $opts, $widget ) . '</div>';
	
	return $opts_str;
}

/**
 * Construct and display options for the related posts shortcode and widget.
 *
 * Filters 4bzCore plugin's options.
 *
 * @since 1.1.0
 *
 * @param string $opts_str Required. 4bzCore plugin's default options.
 * @param array $opts Optional. Only relative if displaying a widget's options because these shortcode options are not saved to the database, they are used in the shortcode builder to generate the shortcode string.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_options_related_posts( $opts_str, $opts = array(), $widget = false ) {
	$opts_str = '<div class="blogcentral-wrap text-format">' . blogcentral_options_posts( 'related_posts', $opts, $widget ) . '</div>';
	
	return $opts_str;
}

/**
 * Construct and display options for the recent posts shortcode and widget.
 *
 * Filters 4bzCore plugin's options.
 *
 * @since 1.1.0
 *
 * @param string $opts_str Required. 4bzCore plugin's default options.
 * @param array $opts Optional. Only relative if displaying a widget's options because these shortcode options are not saved to the database, they are used in the shortcode builder to generate the shortcode string.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_options_recent_posts( $opts_str, $opts = array(), $widget = false ) {
	$opts_str = '<div class="blogcentral-wrap text-format">' . blogcentral_options_posts( 'recent_posts', $opts, $widget ) . '</div>';
	
	return $opts_str;
}

/**
 * Construct and display options for popular posts shortcode and widget.
 *
 * Filters 4bzCore plugin's options.
 *
 * @since 1.1.0
 *
 * @param string $opts_str Required. 4bzCore plugin's default options.
 * @param array $opts Optional. Only relative if displaying a widget's options because these shortcode options are not saved to the database, they are used in the shortcode builder to generate the shortcode string.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_options_popular_posts( $opts_str, $opts = array(), $widget = false ) {
	$opts_str = '<div class="blogcentral-wrap text-format">' . blogcentral_options_posts( 'popular_posts', $opts, $widget ) . '</div>';
	
	return $opts_str;
}

/**
 * Construct and display options for facebook comments shortcode and widget.
 * 
 * Filters 4bzCore plugin's options.
 *
 * @since 1.1.0
 *
 * @param string $opts_str Required. 4bzCore plugin's default options.
 * @param array $opts Optional. Only relative if displaying a widget's options because these shortcode options are not saved to the database, they are used in the shortcode builder to generate the shortcode string.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_options_facebook_comments( $opts_str, $opts = array(), $widget = false ) {
	$opts['name'] = 'facebook_comments';
	$opts_str = "<div class='blogcentral-wrap text-format'>";
	
	ob_start();
	blogcentral_display_custom_text_class_options( $opts, $widget );
	$opts_str .= ob_get_clean() . '<p class="section-title div3">' . __( 'General', BLOGCENTRAL_TXT_DOMAIN ) . '</p> 
		<table class="form-table"><tbody>
			<tr>
				<th>' . __( 'Limit', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
				<td><label>' . __( 'Number of items to display', BLOGCENTRAL_TXT_DOMAIN ) . "</label> <input type='text'";
		
			if ( isset( $opts['limit'] ) ) {
				$opts_str .= " value='" . esc_attr( $opts['limit'] ) . "'";
			}
			
			$opts_str .= " name='" . blogcentral_get_field_name_wrap( 'limit', "blogcentral[facebook_comments]", $widget ) . "' />" . '</td></tr></table></div>';
				
	return $opts_str;
}

/**
 * Construct and display options for contact information for an organization or a user
 *
 * Filters 4bzCore plugin's options.
 *
 * @since 1.1.0
 *
 * @param string $opts_str Required. 4bzCore plugin's default options.
 * @param array $opts Optional. Saved options.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_options_contact_info( $opts_str, $opts = array(), $widget = false ) {
	// Get the global variables.
	global $blogcentral_opts;
	global $blogcentral_defaults;
	global $blogcentral_initial;
	global $fourbzcore_plugin;
	
	$opts_str = '<div class="blogcentral-wrap text-format">';
		
	// need to unset name to populate the contact info fields with the information saved on the theme options page, if the options have never been saved.
	unset( $opts['name'] );
	
	if ( ! empty( $opts ) ) {
		$opts = array_map( 'stripslashes_deep', $opts );
		$opts = wp_parse_args( $opts, $blogcentral_defaults['contact_info'] );
	} else {
		$opts = wp_parse_args( $blogcentral_opts['contact_info'], $blogcentral_defaults['contact_info'] );
	}	

	$opts['name'] = 'contact_info';
	
	ob_start();
	blogcentral_display_custom_text_class_options( $opts, $widget );
	$opts_str .= ob_get_clean();
	
	$opts_str .= '<div class="tabs-panel">
		<p class="section-title div3">' . __( 'Contact Info Specific Options', BLOGCENTRAL_TXT_DOMAIN ) .
		'</p><table class="form-table">
		<tbody>';
		
	$opts_str .= '<tr><th>' . __( 'General', BLOGCENTRAL_TXT_DOMAIN ) . '<td><div>';
	
	ob_start();
	blogcentral_display_contact_info_layout_opts( $opts, $widget );
	$opts_str .= ob_get_clean() . '</div></td></tr>';
	$opts_str .= '<tr><th>' . __( 'Contact Information', BLOGCENTRAL_TXT_DOMAIN ) .
			'</th>
			<td>';
						
	if ( isset( $fourbzcore_plugin ) ) {
		ob_start();
		$opts['name_attr_pre'] = 'blogcentral';
		$fourbzcore_plugin->display_contact_opts( $opts, $widget );
		
		$opts_str .= ob_get_clean();
	}
	
	$opts_str .= '</td>
			</tr>
		</tbody>
	</table></div></div>';
	
	return $opts_str;
}

/**
 * Construct and display options for a contact form.
 *
 * Filters 4bzCore plugin's options.
 *
 * @since 1.1.0
 *
 * @param string $opts_str Required. 4bzCore plugin's default options.
 * @param array $opts Optional. Saved options.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_options_contact_form( $opts_str, $opts = array(), $widget = false ) {
	// Get the global variables.
	global $blogcentral_opts;
	global $blogcentral_defaults;
	global $blogcentral_initial;
	global $fourbzcore_plugin;
	
	$opts_str = '<div class="blogcentral-wrap text-format">';
	
	// need to unset name to populate the contact info fields with the information saved on the theme options page, if the options have never been saved.
	unset( $opts['name'] );
	
	if ( $opts ) {
		$opts = array_map( 'stripslashes_deep', $opts );
		$opts = wp_parse_args( $opts, $blogcentral_defaults['contact_info'] );
	} else {
		$opts = wp_parse_args( $blogcentral_opts['contact_info'], $blogcentral_defaults['contact_info'] );
	}
	
	$opts['name'] = 'contact_form';
	
	ob_start();
	blogcentral_display_custom_text_class_options( $opts, $widget );
	$opts_str .= ob_get_clean();
		
	ob_start();
	blogcentral_contact_form_specific_opts( $opts, $widget );
	$opts_str .= ob_get_clean();
	
	$opts_str .= '</div>';
	
	return $opts_str;
}
		
/**
 * Construct and display options specific to the contact form shortcode and widget.
 *
 * Requires any plugin that displays a contact form via a shortcode.
 *
 * @since 1.1.0
 *
 * @param array $opts Optional. Saved options.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_contact_form_specific_opts( $opts = array(), & $widget = false ) {
	global $fourbzcore_plugin;
	
	$contact_opts_str = '';
	
	if ( isset( $fourbzcore_plugin ) ) {
		ob_start();
		$opts['name_attr_pre'] = 'blogcentral';
		$fourbzcore_plugin->display_contact_opts( $opts, $widget );
		$contact_opts_str = ob_get_clean();
	}
	
	$defaults = array(
		'contact_form_layout'	=>	'1',
		'show_contact'			=>	false,
		'show_map'				=>	false,
	);
					
	if ( $opts ) {
		$opts = wp_parse_args( $opts, $defaults );
	} else {
		$opts = $defaults;
	}

	echo "<p class='section-title div3'>" . __( 'Contact Form Specific Options', BLOGCENTRAL_TXT_DOMAIN ) . "</p>
		<table class='form-table'>
			<tbody>
				<tr>
					<th>" . __( 'Layout', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
					<td>
						<div class='group'>
							<input type='radio' value='1' name='" . blogcentral_get_field_name_wrap( 'contact_form_layout', "blogcentral[contact_form]", $widget ) . "'" . checked( $opts['contact_form_layout'], '1', false ) . " />
							<label>" . __( 'wide', BLOGCENTRAL_TXT_DOMAIN ) . "</label><br />
							<input type='radio' value='2' name='" .
								blogcentral_get_field_name_wrap( 'contact_form_layout', "blogcentral[contact_form]", $widget ) . "'" . checked( $opts['contact_form_layout'], '2', false ) . " />
							<label>" . __( 'boxed', BLOGCENTRAL_TXT_DOMAIN ) . "</label>
						</div>
					</td>
				</tr>
				<tr><th>" . __( 'Contact Form', BLOGCENTRAL_TXT_DOMAIN ) . '</th><td>
											<label>' . __( 'Enter the shortcode here to display a contact form, or leave blank and use the one entered on the theme options page.', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
											<textarea name="' . blogcentral_get_field_name_wrap( 'contact_form', "blogcentral[contact_form]", $widget ) . '" cols="30"  rows="10">'; 
										if ( isset( $opts['contact_form'] ) ) {
											echo esc_attr( $opts['contact_form'] );
										}
										echo "</textarea></td></tr> 
				<tr>
					<th>" . __( 'Contact Information', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
					<td>
						<input type='checkbox' class='displaymaster' name='" .
							blogcentral_get_field_name_wrap( 'show_contact', "blogcentral[contact_form]", $widget ) . "'" . checked( $opts['show_contact'], 'on', false ) . " /> 
						<label>";
							_e( 'Show Contact Information - depending on the screen size, it will be to the left of the contact form.', BLOGCENTRAL_TXT_DOMAIN );
						echo "</label><br /><br />";
						
						echo "<div ";
							if ( ! $opts['show_contact'] ) { echo ' style="display:none;" '; }
							echo "class='hideshow'>";
								
							blogcentral_display_contact_info_layout_opts( $opts, $widget ); 
						
						echo $contact_opts_str;
					echo "</div></td>
				</tr>
				<tr>
					<th>" . __( 'Google Map', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
					<td>
						<input type='checkbox' name='" . blogcentral_get_field_name_wrap( 'show_map', "blogcentral[contact_form]", $widget ) . "'" . checked( $opts['show_map'], 'on', false ) . " /> 
						<label>";
							_e( 'Show Google Map - it will be above the contact form. Enter your google application id on the theme options page.', BLOGCENTRAL_TXT_DOMAIN );
						echo "</label>							
					</td>
				</tr>
			</tbody>
		</table>";
}

/**
 * Construct and display options for a flexslider slideshow
 *
 * Filters 4bzCore plugin's options.
 *
 * @since 1.1.0
 *
 * @param string $opts_str Required. 4bzCore plugin's default options.
 * @param array $opts Optional. Saved options.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_options_slideshow( $opts_str, $opts = array(), $widget = false ) {
	$opts['name'] = 'slideshow';
	
	$opts_str = '<div class="blogcentral-wrap text-format">';
	
	ob_start();
	blogcentral_display_custom_text_class_options( $opts, $widget );
	$opts_str .= ob_get_clean();
	
	ob_start();
	blogcentral_slideshow_specific_opts( $opts, $widget );
	$opts_str .= ob_get_clean();
	
	$opts_str .= '</div>';
	return $opts_str;
}

/**
 * Construct and display options specific to the flexslider slideshow shortcode and widget.
 *
 * @since 1.1.0
 *
 * @param array $opts Optional. Saved options.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_slideshow_specific_opts( $opts = array(), & $widget = false ) {
	$defaults = array(
		'slideshow_layout'	=>	'2',
		'animation'			=>	'2',
		'useCSS'			=>	'0',
		'template'			=>	'1',
	);
					
	if ( $opts ) {
		$opts = array_map( 'stripslashes_deep', $opts );
		$opts = wp_parse_args( $opts, $defaults );
	} else {
		$opts = $defaults;
	}
	
	$slides = isset( $opts['slides'] ) ?  $opts['slides'] : '';
	$slides_name = blogcentral_get_field_name_wrap( 'slides', "blogcentral[slideshow]", $widget );
	
	echo "<p class='section-title div3'>" . __( 'Slideshow Specific Options', BLOGCENTRAL_TXT_DOMAIN ) . "</p>
		<table class='form-table'>
			<tbody>";
			echo "<tr>
				<th>" . __( 'Layout', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
				<td>
					<div class='group'>
						<input type='radio' value='1' name='" .
							blogcentral_get_field_name_wrap( 'slideshow_layout', "blogcentral[slideshow]", $widget ) . "'" . checked( $opts['slideshow_layout'], '1', false ) . " />
						<label>" . __( 'boxed', BLOGCENTRAL_TXT_DOMAIN ) . "</label><br />
						<input type='radio' value='2' name='" .
							blogcentral_get_field_name_wrap( 'slideshow_layout', "blogcentral[slideshow]", $widget ) . "'" . checked( $opts['slideshow_layout'], '2', false ) . " />
						<label>" . __( 'wide', BLOGCENTRAL_TXT_DOMAIN ) . "</label>
					</div>
				</td>
			</tr>
			<tr>
				<th>" . __( 'Animation', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
				<td>
					<div class='group'> <input type='radio' value='1' name='" .
						blogcentral_get_field_name_wrap( 'animation', "blogcentral[slideshow]", $widget ) . "'" . checked( $opts['animation'], '1', false ) . " />
						<label>" . __( 'fade', BLOGCENTRAL_TXT_DOMAIN ) . "</label><br />
						<input type='radio' value='2' name='" .
							blogcentral_get_field_name_wrap( 'animation', "blogcentral[slideshow]", $widget ) . "'" . checked( $opts['animation'], '2', false ) . " />
						<label>" . __( 'slide', BLOGCENTRAL_TXT_DOMAIN ) . "</label>
					</div>
				</td>
			</tr>
			<tr>
				<th>" . __( 'Sync', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
				<td>
					<input type='text' name='" .
						blogcentral_get_field_name_wrap( 'sync', "blogcentral[slideshow]", $widget ) . "'";
					if ( isset( $opts['sync'] ) ) {
						echo " value='" . esc_attr( $opts['sync'] ) . "'";
					}
					echo " />
				</td>
			</tr>
			<tr>
				<th>" . __( 'Navigation for', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
				<td>
					<input type='text' name='" .
						blogcentral_get_field_name_wrap( 'asNavFor', "blogcentral[slideshow]", $widget ) . "'";
					if ( isset( $opts['asNavFor'] ) ) {
						echo " value='" . esc_attr( $opts['asNavFor'] ) . "'";
					}
					echo " />
				</td>
			</tr>
			<tr>
				<th>" . __( 'Easing', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
				<td>
					<input type='text' name='" .
						blogcentral_get_field_name_wrap( 'easing', "blogcentral[slideshow]", $widget ) . "'";
					
					if ( isset( $opts['easing'] ) ) {
						echo " value='" . esc_attr( $opts['easing'] ) . "'";
					}
					
					echo " />
				</td>
			</tr>
			<tr>
				<th>" . __( 'useCSS', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
				<td>
					<div class='group'> <input type='radio' value='0' name='" .
						blogcentral_get_field_name_wrap( 'useCSS', "blogcentral[slideshow]", $widget ) . "'" . checked( $opts['useCSS'], '0', false ) . " />
						<label>" . __( 'false', BLOGCENTRAL_TXT_DOMAIN ) . "</label><br />
						<input type='radio' value='1' name='" .
							blogcentral_get_field_name_wrap( 'useCSS', "blogcentral[slideshow]", $widget ) . "'" . checked( $opts['useCSS'], '1', false ) . " />
						<label>" . __( 'true', BLOGCENTRAL_TXT_DOMAIN ) . "</label>
					</div>
				</td>
			</tr>";
			echo "<tr>
					<th>" . __( 'Slides', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
					<td>
						<p class='instruction'><small>" . __( 'Sort the slides by hovering over a slide until a hand appears, drag and drop where desired.', BLOGCENTRAL_TXT_DOMAIN ) . "</small></p><br /><div id='" . blogcentral_get_field_id_wrap( 'slides', "blogcentral-slideshow", $widget ) . "'>
						<input type='button' class='button-2 blogcentral-add-slide' value='Add Slide' data-name='" . $slides_name . "' />
						<ul class='slides-wrap sortable'>";
						
						if ( is_array( $slides ) && 0 < count( $slides ) ) {
							$i = 0;
							
							foreach ( $slides as $slide ) {
								echo "<li class='slides-cont'>
										<button type='button' class='button-2 delete-row'>" .
											__( 'Delete', BLOGCENTRAL_TXT_DOMAIN ) .
											"</button><br /><br />";
								echo "<label>";
									_e( 'Slide Template- choose a template to use for your slide, or enter custom html below.', BLOGCENTRAL_TXT_DOMAIN );
								echo "</label><br />
									<select class='template-select'>
									<option value='0'></option>
									<option value='1'>" . __( 'template 1', BLOGCENTRAL_TXT_DOMAIN ) .
									"</option>
									<option value='2'>" . __( 'template 2', BLOGCENTRAL_TXT_DOMAIN ) .
									"</option>
									<option value='3'>" . __( 'template 3', BLOGCENTRAL_TXT_DOMAIN ) .
									"</option></select><br />";
								echo "<label>" . 
											__( 'Enter the html for the slide', BLOGCENTRAL_TXT_DOMAIN ) .
											"</label><div class='loader'></div><textarea class='slide-html' name='" . $slides_name . "[]' rows='20' cols='30'>" . esc_html( $slide ) . "</textarea><div class='loader'></div>
									  </li>";
								++$i;
							}
						}
							
						echo "</ul></div>
					</td>
				</tr>
				</tbody></table>";
}
	
/**
 * Construct and display options for the progressbars shortcode and widget.
 *
 * Filters 4bzCore plugin's options.
 *
 * @since 1.1.0
 *
 * @param string $opts_str Required. 4bzCore plugin's default options.
 * @param array $opts Optional. Saved options.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_options_progressbars( $opts_str, $opts = array(), $widget = false ) {
	$progressbars_name = blogcentral_get_field_name_wrap( 'items', "blogcentral[progressbars]", $widget );
	$skills_html = '';
	$skills = isset( $opts['items'] ) ? $opts['items'] : array();
	$opts['name'] = 'progressbars';
	
	$defaults = array(
		'cols'	=>	'1',
	);
					
	if ( $opts ) {
		$opts = wp_parse_args( $opts, $defaults );
	} else {
		$opts = $defaults;
	}
	
	ob_start();
	
	echo '<div id="progressbars-cont-opts" class="blogcentral-wrap text-format">';
	
	blogcentral_display_custom_text_class_options( $opts, $widget );
	$opts_str = ob_get_clean();
	
	// Construct options for the actual progressbars items. 
	$opts_str .= "<p class='section-title div3'>" . __( 'Progressbars Specific Options', BLOGCENTRAL_TXT_DOMAIN ) . '</p><table class="form-table"><tbody>
			<tr>
				<th>' . __( 'Items', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
				<td>
					<br /><p class='instruction'><small>" . __( 'Sort the progressbars by hovering over an item until a hand appears, drag and drop where desired.', BLOGCENTRAL_TXT_DOMAIN ) . '</small></p><br /><input data-index="0" type="button" class="button-2 add-skill" value="' . __( 'Add Item', BLOGCENTRAL_TXT_DOMAIN ) . '" data-name="' . $progressbars_name . '" />
			<ul class="skills-wrap sortable">';
		  
		if ( is_array( $skills ) && 0 < count( $skills ) ) {
			$i = 0;
					
			foreach ( $skills as $key => $val ) {
				$opts_str .= '<li class="skills-cont" data-skill-index="' . $i . '"><br /><button type="button" class="button-2 delete-row">' .
				__( 'Delete', BLOGCENTRAL_TXT_DOMAIN ) . '</button><br /><br />' .
				'<div><label>' .
				__( 'Name', BLOGCENTRAL_TXT_DOMAIN ) . '</label><input type="text" class="add-skill-name" data-skill-index="' .
				$i . '" name="' . $progressbars_name . '[' . $i . '][name]" value="' . esc_attr( $key ) . '" style="padding:5px;" /></div>' .
				'<br /><label>' . __( 'Percentage', BLOGCENTRAL_TXT_DOMAIN ) . '</label><input type="text" class="add-skill-value"' . ' name="' . $progressbars_name . '[' . $i . '][value]" value="' . esc_attr( $val ) . '"/>' .
				'<span class="instruction"><small>' . __( 'Enter percentage as a number 0-100', BLOGCENTRAL_TXT_DOMAIN ) . '</small></span></li>';
				
				++$i;									
			}
		}
	$opts_str .= "</ul></td></tr><tr>
					<th>" . __( 'How many columns?', BLOGCENTRAL_TXT_DOMAIN ) . "</th>
					<td>
						<div class='options-section'>
							  <select name='" . blogcentral_get_field_name_wrap( 'cols', "blogcentral[progressbars]", $widget ) . "'>";
								
								ob_start();
								blogcentral_display_cols_options( $opts['cols'] );
								$opts_str .= ob_get_clean() . "</select></div></td></tr></tbody></table></div>";
								
	return $opts_str;
}

/**
 * Construct and display options for the image text shortcode and widget.
 * 
 * Filters 4bzCore plugin's options.
 *
 * @since 1.1.0
 *
 * @param string $opts_str Required. 4bzCore plugin's default options.
 * @param array $opts Optional. Only relative if displaying a widget's options because these shortcode options are not saved to the database, they are used in the shortcode builder to generate the shortcode string.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_options_image_text( $opts_str, $opts = array(), $widget = false ) {	
	$opts['name'] = 'image_text';

	$opts_str = "<div class='blogcentral-wrap text-format'>";

	ob_start();
	blogcentral_display_custom_text_class_options( $opts, $widget );
	$opts_str .= ob_get_clean();

	$image_url = isset( $opts['image_url'] ) ? esc_attr( $opts['image_url'] ) : '';
	$image_width = isset( $opts['image_url_width'] ) ? esc_attr( $opts['image_url_width'] ) : '';
	$image_height = isset( $opts['image_url_height'] ) ? esc_attr( $opts['image_url_height'] ) : '';
	$image_alt = isset( $opts['image_url_alt'] ) ? esc_attr( $opts['image_url_alt'] ) : '';
	$content = isset( $opts['content'] ) ? esc_textarea( $opts['content'] ) : '';
	$layout = isset( $opts['layout'] ) ? $opts['layout'] : 'img-float';

	$opts_str .= "<p class='section-title div3'>" . __( 'Image Text Specific Options', BLOGCENTRAL_TXT_DOMAIN ) . '</p><table class="form-table"><tbody><tr>
		<th>' . __( 'Layout', BLOGCENTRAL_TXT_DOMAIN ) . "</th><td><input type='radio' name='" . blogcentral_get_field_name_wrap( 'layout', 'blogcentral[image_text]', $widget ) . "' value='img-float'" . checked( $layout, "img-float", false ) . " /> 
		<label>" . __( "Float description around image", BLOGCENTRAL_TXT_DOMAIN ) . "</label><br />" .
		"<input type='radio' name='" . blogcentral_get_field_name_wrap( 'layout', 'blogcentral[image_text]', $widget ) . "' value='img-overlay'" . checked( $layout, "img-overlay", false ) . " /> 
			<label>" . __( "Overlay", BLOGCENTRAL_TXT_DOMAIN ) . "</label><br />
			<input type='radio' name='" . blogcentral_get_field_name_wrap( 'layout', 'blogcentral[image_text]', $widget ) . "' value='img-top'" . checked( $layout, "img-top", false ) . " /> 
			<label>" . __( "Description below image", BLOGCENTRAL_TXT_DOMAIN ) . '</label><br />
		</td>
		</tr>
		<tr><th>' . __( 'Image', BLOGCENTRAL_TXT_DOMAIN ) . '</th><td>
		<input class="icon icon-image" id="' . blogcentral_get_field_id_wrap( 'image-url', 'blogcentral-image-text', $widget ) . '" type="text" name="' . esc_attr( blogcentral_get_field_name_wrap( 'image_url', 'blogcentral[image_text]', $widget ) ) . '" value="' . $image_url . '" /> 
		<input class="icon icon-image-btn button" data-blogcentral-textbox="' . blogcentral_get_field_id_wrap( 'image-url', 'blogcentral-image-text', $widget ) . '" type="button" value="' . __( 'Upload Image', BLOGCENTRAL_TXT_DOMAIN ) . '" />
		<input class="icon icon-image" id="' . blogcentral_get_field_id_wrap( 'image-url', 'blogcentral-image-text', $widget ) . '-width" type="hidden" name="' . esc_attr( blogcentral_get_field_name_wrap( 'image_url_width', 'blogcentral[image_text]', $widget ) ) . '" value="' . $image_width . '" />
		<input class="icon icon-image" id="' . blogcentral_get_field_id_wrap( 'image-url', 'blogcentral-image-text', $widget ) . '-height" type="hidden" name="' . esc_attr( blogcentral_get_field_name_wrap( 'image_url_height', 'blogcentral[image_text]', $widget ) ) . '" value="' . $image_height . '" />
		<input class="icon icon-image" id="' . blogcentral_get_field_id_wrap( 'image-url', 'blogcentral-image-text', $widget ) . '-alt" type="hidden" name="' . esc_attr( blogcentral_get_field_name_wrap( 'image_url_alt', 'blogcentral[image_text]', $widget ) ) . '" value="' . $image_alt . '" /> 
		</td>
		</tr>
						
		<tr><th>' . __( 'Description', BLOGCENTRAL_TXT_DOMAIN ) . '</th><td>
			<textarea id="' . esc_attr( blogcentral_get_field_id_wrap( 'content', 'blogcentral-image-text', $widget ) ) . '" name="' . esc_attr( blogcentral_get_field_name_wrap( 'content', 'blogcentral[image_text]', $widget ) ) . '" rows="10" cols="20">' . $content . '</textarea>
		</td></tr></tbody></table></div>';
		
	return $opts_str;
}
	
/**
 * Construct and display options for the author bio shortcode and widget.
 * 
 * Filters 4bzCore plugin's options.
 *
 * @since 1.1.0
 *
 * @param string $opts_str Required. 4bzCore plugin's default options.
 * @param array $opts Optional. Only relative if displaying a widget's options because these shortcode options are not saved to the database, they are used in the shortcode builder to generate the shortcode string.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_options_author_bio( $opts_str, $opts = array(), $widget = false ) {	
	$opts['name'] = 'author_bio';
	
	$opts_str = "<div class='blogcentral-wrap text-format'>";
	
	ob_start();
	blogcentral_display_custom_text_class_options( $opts, $widget );
	$opts_str .= ob_get_clean() . '</div>';
	
	return $opts_str;
}

/**
 * Construct and display options for the flickr photos shortcode and widget.
 * 
 * Filters 4bzCore plugin's options.
 *
 * @since 1.1.0
 *
 * @param string $opts_str Required. 4bzCore plugin's default options.
 * @param array $opts Optional. Only relative if displaying a widget's options because these shortcode options are not saved to the database, they are used in the shortcode builder to generate the shortcode string.
 * @param object $widget Optional. Widget object if displaying options for a widget.
 */
function blogcentral_options_flickr_photos( $opts_str, $opts, $widget ) {	
	$opts['name'] = 'flickr_photos';
	$opts_str = '';
	
	$opts_str .=  "<div class='blogcentral-wrap text-format'>";
	
	ob_start();
	blogcentral_display_custom_text_class_options( $opts, $widget );
	$opts_str .= ob_get_clean();
	
	$opts_str .= '<p class="section-title div3">' . __( 'General', BLOGCENTRAL_TXT_DOMAIN ) . '</p> 
		<table class="form-table"><tbody>
			<tr>
				<th>' . __( 'Limit', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
				<td><label>' . __( 'Number of items to display', BLOGCENTRAL_TXT_DOMAIN ) . "</label> <input type='text'";

			if ( isset( $opts['limit'] ) ) {
				$opts_str .= " value='" . esc_attr( $opts['limit'] ) . "'";
			}
			
			$opts_str .= " name='" . blogcentral_get_field_name_wrap( 'limit', "blogcentral[flickr_photos]", $widget ) . "' />";
	$opts_str .= '</td>
			</tr>
		</table>';
		
	$opts_str .= "<table class='form-table'>
			<tbody>
				<tr>
					<th>" . __( 'User id', BLOGCENTRAL_TXT_DOMAIN ) . '</th>
					<td><label>' . __( 'User id', BLOGCENTRAL_TXT_DOMAIN ) . '</label>
								<input type="text"';
						
						if ( isset( $opts['user_id'] ) ) {
							$opts_str .= ' value="' . esc_attr( $opts['user_id'] ) . '"';
						} 
						
					$opts_str .= ' name="' . blogcentral_get_field_name_wrap( 'user_id', "blogcentral[flickr_photos]", $widget ) . '" /></td>
						</tr></tbody></table></div>';
	
	return $opts_str;
}

/**
 * Start of filter functions for the 4bzCore shortcodes and
 * widgets display
 *-----------------------------------------------------------------------*/ 
  
/**
 * 4bzCore Widget title filter function.
 *
 * @since 1.1.0
 *
 * @param string $default_title Required. The default display of the title, wrapped in $begin and $end.
 * @param string $title Required. The unfiltered title.
 * @param string $begin Required. String to display before the title.
 * @param string $end Required. String to display after the title.
 */
function blogcentral_widget_title( $default_title, $title, $begin, $end ) {
	return $title;
}

/**
 * Recent Posts shortcode filter function.
 *
 * @since 1.1.0
 *
 * @param string $default_output Required. 4bzCore plugin's default shortcode output.
 * @param array $atts Optional. Options for the shortcode or widget.
 * @param WP_POST $resultset Required. The posts to display.
 */
function blogcentral_recent_posts( $default_output, $atts, $resultset ) {		
	// Get global variables
	global $fourbzcore_plugin;
	
	if ( ! isset( $fourbzcore_plugin ) ) {
		return;
	}
	
	global $blogcentral_opts;
	global $blogcentral_defaults;
	
	
	// Get general posts options.
	$posts_general = isset( $blogcentral_opts['posts_general'] ) && is_array( $blogcentral_opts['posts_general'] ) ? $blogcentral_opts['posts_general'] : array();
	
	// Merge shortcode atts with general component layout defaults.
	$recent_posts_options = shortcode_atts( isset( $blogcentral_defaults['posts_landing'] ) && is_array( $blogcentral_defaults['posts_landing'] ) ?
		$blogcentral_defaults['posts_landing'] : array(), $atts, 'recent_posts_options' );
	
	$recent_posts_options['id'] = $fourbzcore_plugin->fourbzcore_shortcode_recent_posts->get_id();
	
	/*
	 * Html fragments for classes and styles to be added to each component item, in this shortcode, the
	 * component item is a post.
	 */
	$wrapper = blogcentral_get_styles( $recent_posts_options );
	
	$wrapper['wrapper_class'] .= ' recent-posts';
	
	// Passed to the posts shortcode display function: options, and the actual posts to display.
	$recent_posts_atts = array(
		'blogcentral_layout_opts'	=>	array_merge( $recent_posts_options, $posts_general ),
		'blogcentral_query'			=>	$resultset,
	);
	
	// Construct wrapper using the classes and styles previously constructed in the blogcentral_get_styles() function.
	$frag = blogcentral_construct_wrapper( $wrapper );

	ob_start();
	
	if ( isset( $frag ) && is_array( $frag ) ) {
		echo $frag[0];
		
		// Use the posts shortcode to display the content.
		$fourbzcore_plugin->fourbzcore_shortcode_posts->display( $recent_posts_atts );
		
		echo $frag[1];
	} else {
		$fourbzcore_plugin->fourbzcore_shortcode_posts->display( $recent_posts_atts );
	}
	
	return ob_get_clean();
}
	
/**
 * Related Posts shortcode filter function.
 *
 * @since 1.1.0
 *
 * @param string $default_output Required. 4bzCore plugin's default shortcode output.
 * @param array $atts Optional. Options for the shortcode or widget.
 * @param WP_POST $resultset Required. The posts to display.
 */
function blogcentral_related_posts( $default_output, $atts, $resultset ) {		
	// Get global variables
	global $fourbzcore_plugin;
	
	if ( ! isset( $fourbzcore_plugin ) ) {
		return;
	}
	
	global $blogcentral_opts;
	global $blogcentral_defaults;
	
	
	// Get general posts options.
	$posts_general = isset( $blogcentral_opts['posts_general'] ) && is_array( $blogcentral_opts['posts_general'] ) ? $blogcentral_opts['posts_general'] : array();
	
	// Merge shortcode atts with general component layout defaults.
	$related_posts_options = shortcode_atts( isset( $blogcentral_defaults['posts_landing'] ) && is_array( $blogcentral_defaults['posts_landing'] ) ?
		$blogcentral_defaults['posts_landing'] : array(), $atts, 'related_posts_options' );
	
	$related_posts_options['id'] = $fourbzcore_plugin->fourbzcore_shortcode_related_posts->get_id();
	
	/*
	 * Html fragments for classes and styles to be added to each component item, in this shortcode, the
	 * component item is a post.
	 */
	$wrapper = blogcentral_get_styles( $related_posts_options );
	
	$wrapper['wrapper_class'] .= ' related-posts';
	
	// Passed to the posts shortcode display function: options, and the actual posts to display.
	$related_posts_atts = array(
		'blogcentral_layout_opts'	=>	array_merge( $related_posts_options, $posts_general ),
		'blogcentral_query'			=>	$resultset,
	);
	
	// Construct wrapper using the classes and styles previously constructed in the blogcentral_get_styles() function.
	$frag = blogcentral_construct_wrapper( $wrapper );

	ob_start();
	
	if ( isset( $frag ) && is_array( $frag ) ) {
		echo $frag[0];
		
		// Use the posts shortcode to display the content.
		$fourbzcore_plugin->fourbzcore_shortcode_posts->display( $related_posts_atts );
		
		echo $frag[1];
	} else {
		$fourbzcore_plugin->fourbzcore_shortcode_posts->display( $related_posts_atts );
	}
	
	return ob_get_clean();
}

/**
 * Featured Posts shortcode filter function.
 *
 * Display only sticky posts.
 * 
 * @since 1.1.0
 *
 * @param string $default_output Required. 4bzCore plugin's default shortcode output.
 * @param array $atts Optional. Options for the shortcode or widget.
 * @param WP_POST $resultset Required. The posts to display.
 */
function blogcentral_featured_posts( $default_output, $atts, $resultset ) {
	// Get global variables.
	global $fourbzcore_plugin;
	global $blogcentral_opts;
	global $blogcentral_defaults;
	
	// Get general posts options.
	$posts_general = isset( $blogcentral_opts['posts_general'] ) && is_array( $blogcentral_opts['posts_general'] ) ? $blogcentral_opts['posts_general'] : array();
	
	// Merge shortcode atts with general component layout defaults.
	$posts_options = shortcode_atts( isset( $blogcentral_defaults['posts_landing'] ) && is_array( $blogcentral_defaults['posts_landing'] ) ?
		$blogcentral_defaults['posts_landing'] : array(), $atts, 'posts_options' );

	$posts_options['id'] = $fourbzcore_plugin->fourbzcore_shortcode_featured_posts->get_id();
	
	/*
	 * An indicator variable used to differentiate posts shortcodes from other shortcodes. If slideshow is
	 * true, then will display component with customized controls.
	 */
	$posts_options['flex_comp'] = true;
	
	/*
	 * Html fragments for classes and styles to be added to each component item, in this shortcode, the
	 * component item is a post.
	 */
	$wrapper = blogcentral_get_styles( $posts_options );
	
	$wrapper['wrapper_class'] .= ' featured-posts';
	
	// Passed to the posts shortcode display function: options, and the actual posts to display.
	$posts_atts = array(
		'blogcentral_layout_opts'	=>	array_merge( $posts_options, $posts_general ),
		'blogcentral_query'			=>	$resultset,
	);
		
	// Construct wrapper using the classes and styles previously constructed in the blogcentral_get_styles() function.
	$frag = blogcentral_construct_wrapper( $wrapper );
	
	ob_start();
	
	if ( isset( $frag ) && is_array( $frag ) ) {
		echo $frag[0];
		
		// Use the posts shortcode to display the content.
		$fourbzcore_plugin->fourbzcore_shortcode_posts->display( $posts_atts );
		
		echo $frag[1];
	} else {
		$fourbzcore_plugin->fourbzcore_shortcode_posts->display( $posts_atts );
	}
	
	return ob_get_clean();
}
	
/**
 * Popular Posts shortcode filter function.
 *
 * Display popular posts based on number of comments.
 * 
 * @since 1.1.0
 *
 * @param string $default_output Required. 4bzCore plugin's default shortcode output.
 * @param array $atts Optional. Options for the shortcode or widget.
 * @param WP_POST $resultset Required. The posts to display.
 */
function blogcentral_popular_posts( $default_output, $atts, $resultset ) {
	// Get global variables.
	global $fourbzcore_plugin;
	global $blogcentral_opts;
	global $blogcentral_defaults;

	// Get general posts options.
	$posts_general = isset( $blogcentral_opts['posts_general'] ) && is_array( $blogcentral_opts['posts_general'] ) ? $blogcentral_opts['posts_general'] : array();
	
	// Merge shortcode atts with general component layout defaults.
	$posts_options = shortcode_atts( isset( $blogcentral_defaults['posts_landing'] ) && is_array( $blogcentral_defaults['posts_landing'] ) ?
		$blogcentral_defaults['posts_landing'] : array(), $atts, 'posts_options' );

	$posts_options['id'] = $fourbzcore_plugin->fourbzcore_shortcode_popular_posts->get_id();
	
	/*
	 * An indicator variable used to differentiate posts shortcodes from other shortcodes. If slideshow is
	 * true, then will display component with customized controls.
	 */
	$posts_options['flex_comp'] = true;
	
	/*
	 * Html fragments for classes and styles to be added to each component item, in this shortcode, the
	 * component item is a post.
	 */
	$wrapper = blogcentral_get_styles( $posts_options );
	
	$wrapper['wrapper_class'] .= ' popular-posts';
	
	// Passed to the posts shortcode display function: options, and the actual posts to display.
	$posts_atts = array(
		'blogcentral_layout_opts'	=>	array_merge( $posts_options, $posts_general ),
		'blogcentral_query'			=>	$resultset,
	);
	
	// Construct wrapper using the classes and styles previously constructed in the blogcentral_get_styles() function.
	$frag = blogcentral_construct_wrapper( $wrapper );
	
	ob_start();
	
	if ( isset( $frag ) && is_array( $frag ) ) {
		echo $frag[0];
		
		// Use the posts shortcode to display the content.
		$fourbzcore_plugin->fourbzcore_shortcode_posts->display( $posts_atts );
		
		echo $frag[1];
	} else {
		$fourbzcore_plugin->fourbzcore_shortcode_posts->display( $posts_atts );
	}

	return ob_get_clean();
}

/**
 * Progressbars shortcode filter function.
 * 
 * @since 1.1.0
 *
 * @param string $default_output Required. 4bzCore plugin's default shortcode output.
 * @param array $atts Required. Options for the shortcode or widget, including the list of items to display as progressbars.
 * @param array $items Required. The items to be shown as progressbars.
 */
function blogcentral_progressbars( $default_output, $atts, $items ) {
	if ( empty( $atts ) ) {
		return;
	}
	
	// Get global variables.
	global $fourbzcore_plugin;
	global $blogcentral_opts;
	global $blogcentral_defaults;
	
	// Default settings specific to progressbars.
	$defaults = array(
		'items'	=>	array(),
	);
	
	// Merge shortcode atts with general component layout defaults.
	$options = shortcode_atts( array_merge( isset( $blogcentral_defaults['posts_landing'] ) && is_array( $blogcentral_defaults['posts_landing'] ) ?
		$blogcentral_defaults['posts_landing'] : array(), $defaults ), $atts, 'options' );
	
	$options['id'] = $fourbzcore_plugin->fourbzcore_shortcode_progressbars->get_id();
	
	// Html fragments for classes and styles to be added to each component item, in this shortcode the component item is a progressbar.
	$wrapper = blogcentral_get_styles( $options );
	
	$wrapper['wrapper_class'] .= ' progressbars';
	
	
	$progressbar_atts = array(
		'blogcentral_layout_opts'	=>	$options,
		'blogcentral_items'			=>	$items,
	);

	// Construct wrapper using the classes and styles previously constructed in the blogcentral_get_styles() function.
	$frag = blogcentral_construct_wrapper( $wrapper );
	
	ob_start();
	
	if ( isset( $frag ) && is_array( $frag ) ) {
		echo $frag[0];
		
		// Use the progressbars shortcode to display the content.
		$fourbzcore_plugin->fourbzcore_shortcode_progressbars->display( $progressbar_atts );
		
		echo $frag[1];
	} else {
		$fourbzcore_plugin->fourbzcore_shortcode_progressbars->display( $progressbar_atts );
	}		
	
	return ob_get_clean();
}
	
/**
 * Contact Info shortcode filter function.
 *
 * Method to display the contact information for an organization or a user.
 *
 * @since 1.1.0
 *
 * @param string $default_output Required. 4bzCore plugin's default shortcode output.
 * @param array $atts Optional. Options for the shortcode or widget.
 * @param array $contact_info Required. The contact info to display.
 */
function blogcentral_contact_info( $default_output, $atts, $contact_info ) {
	// Get global variables.
	global $fourbzcore_plugin;
	global $blogcentral_opts;
	global $blogcentral_defaults;
	
	// Merge shortcode atts with general component layout defaults.
	$general_info = isset( $blogcentral_defaults ) ? array_merge( $blogcentral_defaults['contact_info'],
		$blogcentral_defaults['posts_landing'] ) : array();
		
	$contact_options = shortcode_atts( $general_info, $atts, 'contact_options' );
	
	$contact_options['id'] = $fourbzcore_plugin->fourbzcore_shortcode_contact_info->get_id();
	
	// Html fragments for classes and styles to be added to each component item.
	$wrapper = blogcentral_get_styles( $contact_options );
	$wrapper['wrapper_class'] .= ' contact-info';
	
	// Passed to the contact info shortcode display function: options, and the actual posts to display.
	$contact_atts = array(
		'blogcentral_layout_opts'	=>	$contact_options,
		'blogcentral_contact_info'	=>	$contact_info,
	);
	
	// Construct wrapper using the classes and styles previously constructed in the blogcentral_get_styles() function.
	$frag = blogcentral_construct_wrapper( $wrapper );
	
	ob_start();
	
	if ( isset( $frag ) && is_array( $frag ) ) {
		echo $frag[0];
		$fourbzcore_plugin->fourbzcore_shortcode_contact_info->display( $contact_atts );
		echo $frag[1];
	} else {
		$fourbzcore_plugin->fourbzcore_shortcode_contact_info->display( $contact_atts );
	}
	
	return ob_get_clean();
}

/**
 * Contact Form shortcode filter function.
 *
 * Method to display a contact form powered by any plugin that uses a shortcode for its display, along with an optional display of contact information.
 *
 * @since 1.1.0
 *
 * @param string $default_output Required. 4bzCore plugin's default shortcode output.
 * @param array $atts Optional. Options for the shortcode or widget.
 * @param array $form_options Required. Form options augmented with extra options from the 4bzCore plugin.
 */
function blogcentral_contact_form( $default_output, $atts, $form_options ) {
	// Get global variables.
	global $fourbzcore_plugin;
	global $blogcentral_opts;
	global $blogcentral_defaults;
	
	// Defaults specific to the contact form and contact info shortcodes.
	$defaults = array(
		'contact_form_layout'	=>	'1',
		'contact_info_layout'	=> 	'1',
		'show_address'			=>	null,
		'show_phone'			=>	null,
		'show_email'			=>	null,
		'show_url'				=>	null,
		'show_social'			=>	null,
		'share_title'			=>	'',
	);
	
	// Merge contact form specific defaults with shortcode atts and general component layout defaults.
	$form_options = array_merge( $defaults, $form_options );
	$form_options = shortcode_atts( array_merge( isset( $blogcentral_defaults['posts_landing'] ) && is_array( $blogcentral_defaults['posts_landing'] ) ?
		$blogcentral_defaults['posts_landing'] : array(), $form_options ), $atts, 'form_options' );
	
	// Html fragments for classes and styles to be added to each component item, in this shortcode, the component is a contact form.
	$wrapper = blogcentral_get_styles( $form_options );
	
	$wrapper['wrapper_class'] .= ' contact-form';
	
	/*
	 * Class specific to the contact form shortcode. If contact form layout is 1 then add the boxed class to display the
	 * contact form in boxed format.
	 */
	if ( isset( $atts['contact_form_layout'] ) && '2' === $atts['contact_form_layout'] ) {
		$wrapper['wrapper_class'] .= ' boxed';
	}
	
	$form_atts = array(
		'blogcentral_layout_opts'	=>	$form_options,
	);
	
	// Construct wrapper using the classes and styles previously constructed in the blogcentral_get_styles() function.
	$frag = blogcentral_construct_wrapper( $wrapper );
	
	ob_start();
	
	if ( isset( $frag ) && is_array( $frag ) ) {
		echo $frag[0];
		$fourbzcore_plugin->fourbzcore_shortcode_contact_form->display( $form_atts );
		echo $frag[1];
	} else {
		$fourbzcore_plugin->fourbzcore_shortcode_contact_form->display( $form_atts );
	}	
	
	return ob_get_clean();
}
	
/**
 * Slideshow shortcode filter function.
 *
 * Display a flexslider slider.
 *
 * @since 1.1.0
 *
 * @param string $default_output Required. 4bzCore plugin's default shortcode output.
 * @param array $atts Required. Options for the shortcode or widget.
 * @param array $slides Required. The slides to display.
 */
function blogcentral_slideshow( $default_output, $atts, $slides ) {
	if ( empty( $atts['slides'] ) ) {
		return;
	}
	
	// Get global variables.
	global $fourbzcore_plugin;
	global $blogcentral_opts;
	global $blogcentral_defaults;

	// Defaults specific to the slideshow shortcode.
	$defaults = array(
		'slideshow'			=>	true,
		'slides'			=>	array(),
		'flex_comp'			=>	false,
		'slideshow_layout'	=>	'',
		'animation'			=>	"fade",
		'easing'			=>	"swing",
		'sync'				=>	"",
		'asNavFor'			=>	"",
		'useCSS'			=>	false,
	);
	
	// Merge slideshow specific defaults with shortcode atts and general component layout defaults.
	$slideshow_options = shortcode_atts( array_merge( isset( $blogcentral_defaults['posts_landing'] ) && is_array( $blogcentral_defaults['posts_landing'] ) ?
		$blogcentral_defaults['posts_landing'] : array(), $defaults ), $atts, 'slideshow_options' );
	
	$slideshow_options['id'] = $fourbzcore_plugin->fourbzcore_shortcode_slideshow->get_id();
	
	// Html fragments for classes and styles to be added to each component item.
	$wrapper = blogcentral_get_styles( $slideshow_options );
	$wrapper['wrapper_class'] .= ' slideshow';
	
	$slideshow_atts = array(
		'blogcentral_layout_opts'	=>	$slideshow_options,
		'blogcentral_items'			=>	$slides,
	);
	
	// Construct wrapper using the classes and styles previously constructed in the blogcentral_get_styles() function.
	$frag = blogcentral_construct_wrapper( $wrapper );
	
	ob_start();
	
	if ( isset( $frag ) && is_array( $frag ) ) {
		echo $frag[0];
		$fourbzcore_plugin->fourbzcore_shortcode_slideshow->display( $slideshow_atts );
		echo $frag[1];
	} else {
		$fourbzcore_plugin->fourbzcore_shortcode_slideshow->display( $slideshow_atts );
	}	
	
	return ob_get_clean();
}

/**
 * Facebook Comments shortcode filter function.
 *
 * @since 1.1.0
 *
 * @param string $default_output Required. 4bzCore plugin's default shortcode output.
 * @param array $atts Optional. Options for the shortcode or widget.
 * @param array $default_opts Optional. Default options.
 */
function blogcentral_facebook_comments( $default_output, $atts, $default_opts ) {
	// Get global variables.
	global $fourbzcore_plugin;
	global $blogcentral_opts;
	global $blogcentral_defaults;

	// Merge shortcode atts with general component layout defaults.
	$options = shortcode_atts( isset( $blogcentral_defaults['posts_landing'] ) && is_array( $blogcentral_defaults['posts_landing'] ) ?
		$blogcentral_defaults['posts_landing'] : array(), $atts, 'options' );
		
	$options['limit'] = isset( $atts['limit'] ) ? $atts['limit'] : 5;
	
	$options['id'] = $fourbzcore_plugin->fourbzcore_shortcode_facebook_comments->get_id();
	
	// Html fragments for classes and styles to be added to each component item.
	$wrapper = blogcentral_get_styles( $options );
	$wrapper['wrapper_class'] .= ' facebook-comments';
	
	$atts = array(
		'blogcentral_layout_opts'	=>	$options,
	);
	
	// Construct wrapper using the classes and styles previously constructed in the blogcentral_get_styles() function.
	$frag = blogcentral_construct_wrapper( $wrapper );
	
	ob_start();
	
	if ( isset( $frag ) && is_array( $frag ) ) {
		echo $frag[0];
		$fourbzcore_plugin->fourbzcore_shortcode_facebook_comments->display( $atts );
		echo $frag[1];
	} else {
		$fourbzcore_plugin->fourbzcore_shortcode_facebook_comments->display( $atts );
	}
	
	return ob_get_clean();
}
	
/**
 * Flickr Photos Shortcode Filter Function
 *
 * @since 1.1.0
 *
 * @param string $default_output Required. 4bzCore plugin's default shortcode output.
 * @param array $atts Required. Options for the shortcode or widget.
 * @param array $default_opts Optional.
 */
function blogcentral_flickr_photos( $default_output, $atts, $default_opts ) {
	if ( ! isset( $atts['user_id'] ) ) {
		return;
	}
	
	// Get global variables.
	global $fourbzcore_plugin;
	global $blogcentral_opts;
	global $blogcentral_defaults;
	
	// Merge shortcode atts with general component layout defaults.
	$options = shortcode_atts( isset( $blogcentral_defaults['posts_landing'] ) && is_array( $blogcentral_defaults['posts_landing'] ) ?
		$blogcentral_defaults['posts_landing'] : array(), $atts, 'options' );
		
	$options['limit'] = isset( $atts['limit'] ) ? $atts['limit'] : '6';
	$options['user_id'] = $atts['user_id'];
	
	$options['id'] = $fourbzcore_plugin->fourbzcore_shortcode_flickr_photos->get_id();
	
	// Html fragments for classes and styles to be added to each component item.
	$wrapper = blogcentral_get_styles( $options );
	$wrapper['wrapper_class'] .= ' flickr-photos';
	
	$atts = array(
		'blogcentral_layout_opts'	=>	$options,
	);
	
	// Construct wrapper using the classes and styles previously constructed in the blogcentral_get_styles() function.
	$frag = blogcentral_construct_wrapper( $wrapper );
	
	ob_start();
	
	if ( isset( $frag ) && is_array( $frag ) ) {
		echo $frag[0];
		$fourbzcore_plugin->fourbzcore_shortcode_flickr_photos->display( $atts );
		echo $frag[1];
	} else {
		$fourbzcore_plugin->fourbzcore_shortcode_flickr_photos->display( $atts );
	}
	
	return ob_get_clean();
}
	
/**
 * Image Text Shortcode Filter Function
 *
 * @since 1.1.0
 *
 * @param string $default_output Required. 4bzCore plugin's default shortcode output.
 * @param array $atts Required. Options for the shortcode or widget.
 * @param array $default_opts Optional.
 */
function blogcentral_image_text( $default_output, $atts, $default_opts ) {
	
	// Get global variables.
	global $fourbzcore_plugin;
	global $blogcentral_opts;
	global $blogcentral_defaults;
	
	// Merge shortcode atts with general component layout defaults.
	$options = shortcode_atts( isset( $blogcentral_defaults['posts_landing'] ) && is_array( $blogcentral_defaults['posts_landing'] ) ?
		$blogcentral_defaults['posts_landing'] : array(), $atts, 'options' );
		
	$options['content'] = isset( $atts['content'] ) ? $atts['content'] : '';
	$options['image_url'] = isset( $atts['image_url'] ) ? $atts['image_url'] : '';
	$options['image_url_width'] = isset( $atts['image_url_width'] ) ? $atts['image_url_width'] : '';
	$options['image_url_height'] = isset( $atts['image_url_height'] ) ? $atts['image_url_height'] : '';
	$options['image_url_alt'] = isset( $atts['image_url_alt'] ) ? $atts['image_url_alt'] : '';
	$options['id'] = $fourbzcore_plugin->fourbzcore_shortcode_image_text->get_id();
	
	// Html fragments for classes and styles to be added to each component item.
	$wrapper = blogcentral_get_styles( $options );
	$wrapper['wrapper_class'] .= ' image-text';
	
	$atts = array(
		'blogcentral_layout_opts'	=>	$options,
	);
	
	// Construct wrapper using the classes and styles previously constructed in the blogcentral_get_styles() function.
	$frag = blogcentral_construct_wrapper( $wrapper );
	
	ob_start();
	
	if ( isset( $frag ) && is_array( $frag ) ) {
		echo $frag[0];
		$fourbzcore_plugin->fourbzcore_shortcode_image_text->display( $atts );
		echo $frag[1];
	} else {
		$fourbzcore_plugin->fourbzcore_shortcode_image_text->display( $atts );
	}
	
	return ob_get_clean();
}

/**
 * Author Bio shortcode filter function
 *
 * @since 1.1.0
 *
 * @param string $default_output Required. 4bzCore plugin's default shortcode output.
 * @param array $atts Optional. Options for the shortcode or widget.
 * @param array $default_opts Optional.
 */
function blogcentral_author_bio( $default_output, $atts, $default_opts ) {
	
	// Get global variables.
	global $fourbzcore_plugin;
	global $blogcentral_opts;
	global $blogcentral_defaults;
	
	// Merge shortcode atts with general component layout defaults.
	$options = shortcode_atts( isset( $blogcentral_defaults['posts_landing'] ) && is_array( $blogcentral_defaults['posts_landing'] ) ?
		$blogcentral_defaults['posts_landing'] : array(), $atts, 'options' );
	
	$options['id'] = $fourbzcore_plugin->fourbzcore_shortcode_author_bio->get_id();
	
	// Html fragments for classes and styles to be added to each component item.
	$wrapper = blogcentral_get_styles( $options );
	$wrapper['wrapper_class'] .= ' author-bio-wrap';
	
	$atts = array(
		'blogcentral_layout_opts'	=>	$options,
	);
	
	// Construct wrapper using the classes and styles previously constructed in the blogcentral_get_styles() function.
	$frag = blogcentral_construct_wrapper( $wrapper );
	
	ob_start();
	
	if ( isset( $frag ) && is_array( $frag ) ) {
		echo $frag[0];
		$fourbzcore_plugin->fourbzcore_shortcode_author_bio->display( $atts );
		echo $frag[1];
	} else {
		$fourbzcore_plugin->fourbzcore_shortcode_author_bio->display( $atts );
	}
	
	return ob_get_clean();
}