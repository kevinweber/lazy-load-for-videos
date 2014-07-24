<?php
/*
 * Remove branding
 */
function removeBranding() {
	return 'displayBranding: false,';
}

/*
 * Hook for Youtube JS options
 */
add_filter( 'lly_set_options', 'lazyload_premium_lly_set_options' );
function lazyload_premium_lly_set_options() {
	echo removeBranding();
}
/*
 * Hook for Vimeo JS options
 */
add_filter( 'llv_set_options', 'lazyload_premium_llv_set_options' );
function lazyload_premium_llv_set_options() {
	echo removeBranding();
}