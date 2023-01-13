<?php
/**
 * Main PHP file used to for initial calls to UniPress API classes and functions.
 *
 * @package UniPress API
 * @since 1.0.0
 */
 
/*
Plugin Name: UniPress API Add-on For Advanced Ads
Plugin URI: 
Description: UniPress API Add-on For Advanced Ads
Author: ZEEN101
Version: 1.0.0
Author URI: https://zeen101.com/
Tags:
*/

//Define global variables...
define( 'UNIPRESS_API_ADVANCED_ADDS_ADDON_NAME', 		'UniPress API Add-on For Advanced Ads' );
define( 'UNIPRESS_API_ADVANCED_ADDS_ADDON_SLUG', 		'unipress-api-add-on-for-advanced-ads' );
define( 'UNIPRESS_API_ADVANCED_ADDS_ADDON_VERSION',             '1.0.0' );
define( 'UNIPRESS_API_ADVANCED_ADDS_ADDON_URL', 		plugin_dir_url( __FILE__ ) );
define( 'UNIPRESS_API_ADVANCED_ADDS_ADDON_PATH', 		plugin_dir_path( __FILE__ ) );
define( 'UNIPRESS_API_ADVANCED_ADDS_ADDON_BASENAME', 	plugin_basename( __FILE__ ) );
define( 'UNIPRESS_API_ADVANCED_ADDS_ADDON_REL_DIR', 	dirname( UNIPRESS_API_BASENAME ) );

/**
 * Instantiate UniPress API GEO class, require helper files
 *
 * @since 1.0.0
 */
function unipress_api_add_on_for_advanced_ads_plugins_loaded() {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    if ( is_plugin_active( 'unipress-api/unipress-api.php' ) 
            && is_plugin_active( 'advanced-ads/advanced-ads.php')) {	
	require_once( 'class.php' );
        
	// Instantiate the Pigeon Pack class
	if ( class_exists( 'UniPress_API_AdvancedAds_Addon' ) ) {
		
            $unipress_api_advanced_ads = new UniPress_API_AdvancedAds_Addon();
	}
    } else {
        add_action( 'admin_notices', 'unipress_api_add_on_for_advanced_ads_requirement_nag' );
    }
}

add_action( 'plugins_loaded', 'unipress_api_add_on_for_advanced_ads_plugins_loaded', 4815162340 ); //wait for the plugins to be loaded before init

function unipress_api_add_on_for_advanced_ads_requirement_nag() {
	?>
    <div id="leaky-paywall-requirement-nag" class="update-nag">
        <?php _e( 'You must have the UniPress API and Advanced Ads plugin activated to use the '.UNIPRESS_API_ADVANCED_ADDS_ADDON_NAME.' Add-on.' ); ?>
    </div>
    <?php
}
