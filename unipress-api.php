<?php
/**
 * Main PHP file used to for initial calls to UniPress API classes and functions.
 *
 * @package UniPress API
 * @since 1.0.0
 */
 
/*
Plugin Name: UniPress API
Plugin URI: http://getunipress.com/
Description: A premium WordPress plugin by UniPress.
Author: UniPress Development Team
Version: 1.0.0
Author URI: http://getunipress.com/
Tags:
*/

//Define global variables...
if ( !defined( 'UNIPRESS_STORE_URL' ) )
	define( 'UNIPRESS_STORE_URL',	'http://getunipress.com' );
	
define( 'UNIPRESS_API_NAME', 		'UniPress API' );
define( 'UNIPRESS_API_SLUG', 		'unipress-api' );
define( 'UNIPRESS_API_VERSION', 	'1.0.0' );
define( 'UNIPRESS_API_DB_VERSION', 	'1.0.0' );
define( 'UNIPRESS_API_URL', 		plugin_dir_url( __FILE__ ) );
define( 'UNIPRESS_API_PATH', 		plugin_dir_path( __FILE__ ) );
define( 'UNIPRESS_API_BASENAME', 	plugin_basename( __FILE__ ) );
define( 'UNIPRESS_API_REL_DIR', 	dirname( UNIPRESS_API_BASENAME ) );

define( 'UNIPRESS_API_IOS_MAX_CHAR', 218 ); //characters
define( 'UNIPRESS_API_ANDROID_MAX_CHAR', 4000 ); //characters


/**
 * Instantiate UniPress API class, require helper files
 *
 * @since 1.0.0
 */
function unipress_api_plugins_loaded() {
	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	if ( is_plugin_active( 'issuem-leaky-paywall/issuem-leaky-paywall.php' ) ) {

		require_once( 'class.php' );
	
		// Instantiate the Pigeon Pack class
		if ( class_exists( 'UniPress_API' ) ) {
			
			global $unipress_api;
			
			$unipress_api = new UniPress_API();
			
			require_once( 'functions.php' );
			require_once( 'post-types.php' );
					
			if ( !empty( $_REQUEST['unipress-api'] ) ) {
				add_filter( 'jetpack_check_mobile', '__return_false' ); //JetPack messes with the mobile menu, so return false on UniPress API calls
			}

			//Internationalization
			load_plugin_textdomain( 'unipress-api', false, UNIPRESS_API_REL_DIR . '/i18n/' );
				
		}
	
	} else {
	
		add_action( 'admin_notices', 'leaky_paywall_up_api_requirement_nag' );
		
	}

}
add_action( 'plugins_loaded', 'unipress_api_plugins_loaded', 4815162342 ); //wait for the plugins to be loaded before init

function leaky_paywall_up_api_requirement_nag() {
	?>
	<div id="leaky-paywall-requirement-nag" class="update-nag">
		<?php _e( 'You must have the Leaky Paywall plugin activated to use the UniPress API plugin.' ); ?>
	</div>
	<?php
}

function unipress_api_register_image_sizes() {
	add_image_size( 'unipress-phone', 640, 100, true );
	add_image_size( 'unipress-tablet', 1536, 240, true );
}
add_action( 'init', 'unipress_api_register_image_sizes' );