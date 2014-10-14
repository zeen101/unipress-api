<?php
 
if ( !function_exists( 'get_unipress_api_settings' ) ) {

	/**
	 * Helper function to get zeen101's Leaky Paywall settings for current site
	 *
	 * @since 1.0.0
	 *
	 * @return mixed Value set for the issuem options.
	 */
	function get_unipress_api_settings() {
	
		global $unipress_api;
		
		return $unipress_api->get_settings();
		
	}
	
}

if ( !function_exists( 'unipress_api_device_row' ) ) {
	
	function unipress_api_device_row( $device=false ) {

		if ( empty( $device ) ) {
			//Create a new device
			
			$token = unipress_api_get_unique_token();
			
			$return  = '<div class="unipress-new-device-row">';
			$return .= sprintf( __( 'Use this token in the next 5 minutes to register your mobile device to your account: <strong>%s</strong>', 'unipress-api' ), $token );
			$return .= '</div>';
			
		} else {

			$return  = '<div class="unipress-device-row">';
			$return .= '<div class="device-row-column-1">';
			$return .= $device;
			$return .= '</div>';
			$return .= '<div class="device-row-column-2">';
			$return .= '<span data-device-id="' . $device . '" class="delete-device">&times;</span>';
			$return .= '</div>';
			$return .= '</div>';

		}
		
		return $return;
		
	}
	
}

if ( !function_exists( 'unipress_api_add_new_device_row_ajax' ) ) {

	/**
	 * AJAX Wrapper
	 *
	 * @since 1.0.0
	 */
	function unipress_api_add_new_device_row_ajax() {
		die( unipress_api_device_row() );
	}
	add_action( 'wp_ajax_unipress-api-add-new-device-row', 'unipress_api_add_new_device_row_ajax' );
	
}

if ( !function_exists( 'unipress_api_delete_device_ajax' ) ) {
	
	/**
	 * AJAX Wrapper
	 *
	 * @since 1.0.0
	 */
	function unipress_api_delete_device_ajax() {
		$current_user = wp_get_current_user();
		if ( !empty( $_POST['device-id'] ) ) {
			delete_user_meta( $current_user->ID, 'unipress-devices', $_POST['device-id'] );
		}
		die();
	}
	add_action( 'wp_ajax_unipress-api-delect-device-row', 'unipress_api_delete_device_ajax' );
	
}

if ( !function_exists( 'unipress_api_get_unique_token' ) ) {
	
	function unipress_api_get_unique_token() {
		
		$token = strtolower( substr( md5( uniqid( rand(), true ) ), 0, 7 ) );

		if ( false === get_transient( 'unipress_api_token_' . $token ) ) {
			$current_user = wp_get_current_user();
			set_transient( 'unipress_api_token_' . $token, $current_user->ID, 60 * 5 ); //Create temp token, expires in 5 minutes
			return strtoupper( $token );
		} else {
			return unipress_api_get_unique_token();
		}
		
	}
	
}

if ( !function_exists( 'unipress_api_get_user_restrictions_by_device_id' ) ) {
	
	function unipress_api_get_user_restrictions_by_device_id( $device_id ) {
	    $lp_settings = get_leaky_paywall_settings();
	    $restrictions = $lp_settings['restrictions'];
		    
		$user = unipress_api_get_user_by_device_id( $device_id );
		  
	    if ( !empty( $user ) ) {
			$mode = 'off' === $lp_settings['test_mode'] ? 'live' : 'test';
		    $level_id = get_user_meta( $user->ID, '_issuem_leaky_paywall_' . $mode . '_level_id', true );
			$level_id = apply_filters( 'get_leaky_paywall_subscription_level_level_id', $level_id );
			if ( false !== $level_id && !empty( $lp_settings['levels'][$level_id] ) ) {
				$restrictions = $lp_settings['levels'][$level_id];
			}
	    }
		return $restrictions;
	}
	
}

if ( !function_exists( 'unipress_api_get_user_by_device_id' ) ) {
	
	function unipress_api_get_user_by_device_id( $device_id ) {
    	$args = array(
    		'meta_query' => array(
    			array(
    				'key' => 'unipress-devices',
    				'value' => $device_id,
    			)
    		)
    	);
	    $users = get_users( $args );
	    if ( !empty( $users ) ) {
		    return $users[0]; //should only be one user with this device ID
		} else {
			return false;
		}
	}
	
}

if ( !function_exists( 'unipress_get_leaky_paywall_subscription_level_level_id' ) ) {
	
	function unipress_get_leaky_paywall_subscription_level_level_id( $level_id ) {
		
		if ( 'mobile' === $level_id ) {
		
			$settings = get_unipress_api_settings();
			return $settings['subscription-id'];
			
		}
		
		return $level_id;
		
	}
	add_filter( 'get_leaky_paywall_subscription_level_level_id', 'unipress_get_leaky_paywall_subscription_level_level_id' );
}

if ( !function_exists( 'unipress_translate_payment_gateway_slug_to_name' ) ) {
	
	function unipress_translate_payment_gateway_slug_to_name( $return, $gateway_slug ) {
		switch( strtolower( $gateway_slug ) ) {
			case 'ios':
				return __( 'Apple Payment Gateway', 'unipress-api' );
			case 'android':
				return __( 'Google Payment Gateway', 'unipress-api' );	
		}
		return $return;
	}
	add_filter( 'leaky_paywall_translate_payment_gateway_slug_to_name', 'unipress_translate_payment_gateway_slug_to_name', 10, 2 );
	
}

if ( !function_exists( 'unipress_api_get_ip_address' ) ) {
		
	function unipress_api_get_ip_address() {
	
		$methods = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR'
		);
	
		foreach ( $methods as $key ) {
	
			if ( true === array_key_exists( $key, $_SERVER ) ) {
	
				foreach ( explode( ',', $_SERVER[$key] ) as $ip ) {
	
					$ip = trim( $ip ); // just to be safe
	
					if ( strrpos( $ip, ':' ) )
						$ip = substr( $ip, strrpos( $ip, ':' ) + 1 );
	
					if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) !== false )
						return $ip;
	
				}
	
			}
	
		}
	
	}

}
