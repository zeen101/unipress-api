<?php
/**
 * Registers UniPress API shortcodes
 *
 * @package UniPress API
 * @since 1.0.0
 */
 
 if ( !function_exists( 'do_unipress_hide_content' ) ) { 

	/**
	 * Shortcode for UniPress to hide content that you don't want to show on the app
	 *
	 * @since 1.0.0
	 */
	function do_unipress_hide_content( $atts, $content=null ) {
	
		if ( !empty( $_GET['unipress-api'] ) ) {
			return '';
		}
		
		return do_shortcode( $content );
		
	}
	add_shortcode( 'unipress_hide_content', 'do_unipress_hide_content' );
	
}