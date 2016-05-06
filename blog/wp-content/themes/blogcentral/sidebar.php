<?php 
/** 
 * Template for the sidebar
 *
 * If no active widgets in this sidebar, then do not display it.
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage sidebar.php
 */

if ( is_active_sidebar( 'sidebar-rght' ) ) : ?><?php //no space here or will break the layout ?><div id="sidebar-rght">
		<div class="gutter">
			<?php dynamic_sidebar( 'sidebar-rght' ); ?>
		</div><!-- End .gutter -->
	</div> <!-- End #sidebar -->
<?php endif; ?>