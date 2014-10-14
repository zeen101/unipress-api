<?php
/**
 * Main PHP file used to for initial calls to zeen101's Leak Paywall classes and functions.
 *
 * @package zeen101's Leak Paywall - UniPress API
 * @since 1.0.0
 */
 
/*
Plugin Name: Leaky Paywall - UniPress API
Plugin URI: http://zeen101.com/
Description: A premium leaky paywall add-on for the Leaky Paywall for WordPress plugin.
Author: zeen101 Development Team
Version: 1.0.0
Author URI: http://zeen101.com/
Tags:
*/

//Define global variables...
if ( !defined( 'ZEEN101_STORE_URL' ) )
	define( 'ZEEN101_STORE_URL',	'http://zeen101.com' );
	
define( 'ISSUEM_LP_UPAPI_NAME', 	'Leaky Paywall - UniPress API' );
define( 'ISSUEM_LP_UPAPI_SLUG', 	'leaky-paywall-unipress-api' );
define( 'ISSUEM_LP_UPAPI_VERSION', 	'1.0.0' );
define( 'ISSUEM_LP_UPAPI_DB_VERSION', '1.0.0' );
define( 'ISSUEM_LP_UPAPI_URL', 		plugin_dir_url( __FILE__ ) );
define( 'ISSUEM_LP_UPAPI_PATH', 	plugin_dir_path( __FILE__ ) );
define( 'ISSUEM_LP_UPAPI_BASENAME', plugin_basename( __FILE__ ) );
define( 'ISSUEM_LP_UPAPI_REL_DIR', 	dirname( ISSUEM_LP_UPAPI_BASENAME ) );

/**
 * Instantiate Pigeon Pack class, require helper files
 *
 * @since 1.0.0
 */
function leaky_paywall_up_api_plugins_loaded() {
	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	if ( is_plugin_active( 'issuem-leaky-paywall/issuem-leaky-paywall.php' ) ) {

		require_once( 'class.php' );
	
		// Instantiate the Pigeon Pack class
		if ( class_exists( 'Leaky_Paywall_UniPress_API' ) ) {
			
			global $unipress_api;
			
			$unipress_api = new Leaky_Paywall_UniPress_API();
			
			require_once( 'functions.php' );
			require_once( 'post-types.php' );
				
			//Internationalization
			load_plugin_textdomain( 'unipress-api', false, ISSUEM_LP_UPAPI_REL_DIR . '/i18n/' );
				
		}
	
	} else {
	
		add_action( 'admin_notices', 'leaky_paywall_up_api_requirement_nag' );
		
	}

}
add_action( 'plugins_loaded', 'leaky_paywall_up_api_plugins_loaded', 4815162342 ); //wait for the plugins to be loaded before init

function leaky_paywall_up_api_requirement_nag() {
	?>
	<div id="leaky-paywall-requirement-nag" class="update-nag">
		<?php _e( 'You must have the Leaky Paywall plugin activated to use the Leaky Paywall UniPress API plugin.' ); ?>
	</div>
	<?php
}