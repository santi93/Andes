<?php
/**
 * Searchform Template for Andes
*/
?>
	<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="text" class="field" name="s" id="s" placeholder="<?php esc_attr_e( 'Search' ); ?>" value="<?php if(is_search() ) {     the_search_query(); }?>" />
	</form>

   