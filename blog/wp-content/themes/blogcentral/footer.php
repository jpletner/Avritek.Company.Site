<?php
/** 
 * Template for footer
 *
 * Contains footer content and the closing of the #main-content and #main-div div elements.
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage footer.php
 */
global $blogcentral_opts;
?>	

			</div><!-- End main-content -->
			<footer class="footer">
				<?php if ( isset( $blogcentral_opts['footer_extended'] ) && ( is_active_sidebar( 'footer-widget-lft' ) ||
					is_active_sidebar( 'footer-widget-mid' ) || is_active_sidebar( 'footer-widget-rght' ) ) ) : ?>
				<div id="footer-top-cont">
						<?php if ( is_active_sidebar( 'footer-widget-lft' ) ) : ?>
						<div id="footer-lft">
							<div class="gutter">
								<?php dynamic_sidebar( 'footer-widget-lft' ); ?>
							</div><!-- End .gutter -->
						</div><!-- End #footer-lft --><?php //No space here or will break layout ?><?php endif; 
						if ( is_active_sidebar( 'footer-widget-mid' ) ) : ?><div id="footer-mid">
							<div class="gutter">
								<?php dynamic_sidebar( 'footer-widget-mid' ); ?>
							</div><!-- End .gutter -->
						</div><!-- End #footer-mid --><?php //No space here or will break layout ?><?php endif;
						if ( is_active_sidebar( 'footer-widget-rght' ) ) : ?><div id="footer-rght">
							<div class="gutter">
								<?php dynamic_sidebar( 'footer-widget-rght' ); ?>
							</div><!-- End .gutter -->
						</div><!-- End #footer-rght -->
						<?php endif; ?>
				</div><!-- End #footer-top -->
				<?php endif; ?>
				<div id="footer-btm">
					<div id="footer-widget-btm-lft">
						<?php if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( 'footer-widget-btm-lft' ) ) : ?>
						<div class="copyright">
							<?php if ( isset( $blogcentral_opts['copyright'] ) ) {
								echo stripslashes( $blogcentral_opts['copyright'] );
							} ?>
						</div>
						<?php endif; ?>
					</div>
					<?php if ( is_active_sidebar( 'footer-widget-btm-rght' ) ) : ?>
					<div id="footer-widget-btm-rght">
						<?php dynamic_sidebar( 'footer-widget-btm-rght' ); ?>
					</div>	
					<?php endif; ?>
				</div><!-- End #footer-btm -->
			</footer><!-- End .footer -->
		</div> <!-- End #main-div -->
	<?php wp_footer(); ?>
	</body>
</html>