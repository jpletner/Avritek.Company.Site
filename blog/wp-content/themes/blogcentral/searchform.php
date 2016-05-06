<?php
/**
 * Template for search form
 *
 * @since BlogCentral 1.0.0
 *
 * @package BlogCentral
 * @subpackage searchform.php
 */
?>
<form method="get" role="search" class="search_form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<p class="text_input_search"><input type="text" name="s" placeholder="<?php _e( 'Search', BLOGCENTRAL_TXT_DOMAIN ); ?>" /></p><button class="fa fa-search search-submit"></button>
</form>