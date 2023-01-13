<?php
/**
 * Main PHP file used to for initial calls to UniPress API classes and functions.
 *
 * @package UniPress API
 * @since 1.0.0
 */
 
/*
Plugin Name: UniPress API Extension
Plugin URI: 
Description: Unipress API Extension
Author: ZEEN101
Version: 1.0.0
Author URI: https://zeen101.com/
Tags:
*/

//Define global variables...
define( 'UNIPRESS_API_EXTENSION_NAME', 		'UniPress API Extension' );
define( 'UNIPRESS_API_EXTENSION_SLUG', 		'unipress-api-extension' );
define( 'UNIPRESS_API_EXTENSION_VERSION',             '1.0.0' );
define( 'UNIPRESS_API_EXTENSION_URL', 		plugin_dir_url( __FILE__ ) );
define( 'UNIPRESS_API_EXTENSION_PATH', 		plugin_dir_path( __FILE__ ) );
define( 'UNIPRESS_API_EXTENSION_BASENAME', 	plugin_basename( __FILE__ ) );
define( 'UNIPRESS_API_EXTENSION_REL_DIR', 	dirname( UNIPRESS_API_BASENAME ) );

/**
 * Instantiate UniPress API GEO class, require helper files
 *
 * @since 1.0.0
 */
function unipress_api_extension_plugins_loaded() {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    if ( is_plugin_active( 'unipress-api/unipress-api.php' ) ) {	
	require_once( 'class.php' );
        
	// Instantiate the Pigeon Pack class
	if ( class_exists( 'UniPress_API_Extension' ) ) {
		
            $unipress_api_extension = new UniPress_API_Extension();
	}
    } else {
        add_action( 'admin_notices', 'unipress_api_extension_requirement_nag' );
    }
}

add_action( 'plugins_loaded', 'unipress_api_extension_plugins_loaded', 4815162341 ); //wait for the plugins to be loaded before init

function unipress_api_extension_requirement_nag() {
	?>
    <div id="leaky-paywall-requirement-nag" class="update-nag">
        <?php _e( 'You must have the UniPress API plugin activated to use the '.UNIPRESS_API_EXTENSION_NAME.' Add-on.' ); ?>
    </div>
    <?php
}
