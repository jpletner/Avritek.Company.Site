<?php
/** 
 * Template to construct and output a post item in a component
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage layout-item-wrapper-begin.php
 */
  
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?><li <?php echo $blogcentral_extra_li_class . $blogcentral_component_item_style;?>> 
<div class="component-gutter">	
	<div class="component-main<?php if ( isset( $blogcentral_meta_wrap_class ) ) { echo $blogcentral_meta_wrap_class; } ?>">
		<?php 
		// Output media
		if ( isset( $blogcentral_media ) ) : ?>
			<figure<?php if ( isset( $blogcentral_media_wrap_style ) ) { echo $blogcentral_media_wrap_style;} ?> class="component-media-wrap<?php
				if ( isset( $blogcentral_img_wrap_class ) ) {
					echo $blogcentral_img_wrap_class;
				} ?>">
				<?php echo $blogcentral_media; ?>
			</figure>
		<?php endif; 
		
		// Depending on the layout, this div will be displayed below the media, floated right of media, etc..
		if ( isset( $blogcentral_meta_content ) ) : ?>
			<div<?php if ( isset( $blogcentral_meta_wrap_style ) ) { echo $blogcentral_meta_wrap_style; } ?> class="component-content-wrap">
				<?php if ( isset( $blogcentral_meta_content ) ) { echo $blogcentral_meta_content; }	?>
			</div>	 
		<?php endif; ?>				
	</div><!-- End .component-main -->
</div><!-- End .component-gutter --></li>