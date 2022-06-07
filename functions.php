<?php
 
if ( !function_exists( 'get_unipress_api_settings' ) ) {

	/**
	 * Helper function to get UniPress API settings for current site
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

if ( !function_exists( 'unipress_api_add_new_subscription_matching_row_ajax' ) ) {

	/**
	 * AJAX Wrapper
	 *
	 * @since 1.0.0
	 */
	function unipress_api_add_new_subscription_matching_row_ajax() {
		$lp_settings = get_leaky_paywall_settings();
		if ( !empty( $lp_settings['levels'] ) ) {
			$return  = '<p class="subscription-id-match">';
			$return .= '<input type="text" class="in_app_purchase_id" name="subscription-ids[' . $_POST['count'] . '][app-id]" value="" placeholder="In-App Product ID" />';
			$return .= '&nbsp;';
			$return .= '<select name="subscription-ids[' . $_POST['count'] . '][level-id]">';
			foreach( $lp_settings['levels'] as $level_id => $level ) {
				$return .= '<option value="' . $level_id .'">' . $level['label'] . '</option>';
			}
			$return .= '</select>';
			$return .= '&nbsp;';
            $return .= '<a class="subscription-id-delete" href="#">&times;</a>'; 
			$return .= '</p>';
		} else {
			$return = '<p>' . __( 'No Subscriptions Found. Please add some to the Leaky Paywall settings.', 'unipress-api' ) . '</p>';
		}
		die( $return );
	}
	add_action( 'wp_ajax_unipress-api-add-new-subscription-matching-row', 'unipress_api_add_new_subscription_matching_row_ajax' );
	
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
			$return .= '<span data-device-id="' . $device . '" class="delete-device">&times;</span>';
			$return .= '</div>';
			$return .= '<div class="device-row-column-2">';
			$return .= $device;
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
	add_action( 'wp_ajax_norpiv_unipress-api-add-new-device-row', 'unipress_api_add_new_device_row_ajax' );
	
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
	add_action( 'wp_ajax_unipress-api-delete-device-row', 'unipress_api_delete_device_ajax' );
	add_action( 'wp_ajax_nopriv_unipress-api-delete-device-row', 'unipress_api_delete_device_ajax' );
	
}

if ( !function_exists( 'unipress_api_get_unique_token' ) ) {
	
	function unipress_api_get_unique_token() {
		
		$token = strtolower( substr( md5( uniqid( rand(), true ) ), 0, 7 ) );

		if ( false === get_option( 'unipress_api_token_' . $token, false ) ) {
			$current_user = wp_get_current_user();
			update_option( 'unipress_api_token_expires_' . $token, current_time( 'timestamp' ) + ( 60 * 5 ) );
			update_option( 'unipress_api_token_' . $token, $current_user->ID );
			return strtoupper( $token );
		} else {
			return unipress_api_get_unique_token();
		}
		
	}
	
}

/**
 * Clean up expired tokens by removing data and their expiration entries from
 * the WordPress options table.
 *
 * This method should never be called directly and should instead be triggered as part
 * of a scheduled task or cron job.
 */
function unipress_api_token_cleanup() {
	global $wpdb;
	
	if ( defined( 'WP_SETUP_CONFIG' ) ) {
		return;
	}
	
	if ( !defined( 'WP_INSTALLING' ) ) {
	
		// Allow other plugins to hook in to the token cleanup process.
		do_action( 'before_unipress_api_token_cleanup' );
		
		$results = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'unipress_api_token_expires_%'" );

		$now = current_time( 'timestamp' );
		$expired_tokens = array();

		foreach( $results as $result ) {
			// If the session has expired
			if ( $now > intval( $result->option_value ) ) {
				$expired_keys[] = $result->option_name;
				$expired_keys[] = 'unipress_api_token_' . substr( $result->option_name, 27 );
			}
		}

		// Delete all expired sessions in a single query
		if ( ! empty( $expired_keys ) ) {
			$formatted = implode( ', ', array_fill( 0, count( $expired_keys ), '%s' ) );
			$query     = $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name IN ($formatted)", $expired_keys );
			$wpdb->query( $query );
		}
	
		// Allow other plugins to hook in to the token cleanup process.
		do_action( 'after_unipress_api_token_cleanup' );
	}
}
add_action( 'unipress_api_token_cleanup_schedule', 'unipress_api_token_cleanup' );

if ( !function_exists( 'unipress_api_get_user_restrictions_by_device_id' ) ) {
	
	function unipress_api_get_user_restrictions_by_device_id( $device_id ) {
	    $lp_settings  = get_leaky_paywall_settings();
	    $restrictions = $lp_settings['restrictions'];
	    
	    $subscription = unipress_api_get_user_subscription_by_device_id( $device_id );
		    
		if ( 'active' == $subscription['status'] && !empty( $lp_settings['levels'][$subscription['level_id']] ) ) {
			
			$restrictions = $lp_settings['levels'][$subscription['level_id']];
			
		} else if ( !empty( $lp_settings['combined_restrictions_total_allowed'] ) ) {
			
		    $restrictions = array( 
		    	'post_types' => array(
		    		array( 
			    		'post_type' => 'ALL',
			    		'allowed_value' => $lp_settings['combined_restrictions_total_allowed']
					)
				)
		    );
		    
	    }
	    
	    $restrictions['sub'] = $subscription;
	    
		return $restrictions;
	}
	
}

if ( !function_exists( 'unipress_api_get_user_subscription_by_device_id' ) ) {
	
	function unipress_api_get_user_subscription_by_device_id( $device_id ) {
		
		if ( $user = unipress_api_get_user_by_device_id( $device_id ) ) {
			
			$settings = get_leaky_paywall_settings();
			$mode = 'off' === $settings['test_mode'] ? 'live' : 'test';

			$subscription['level_id'] = get_user_meta( $user->ID, '_issuem_leaky_paywall_' . $mode . '_level_id', true );
			$subscription['level_id'] = apply_filters( 'get_leaky_paywall_subscription_level_level_id', $subscription['level_id'] );

			$subscription['expires'] = get_user_meta( $user->ID, '_issuem_leaky_paywall_' . $mode . '_expires', true );
			$subscription['expires'] = apply_filters( 'get_leaky_paywall_subscription_level_expires', $subscription['expires'] );

			$subscription['status'] = get_user_meta( $user->ID, '_issuem_leaky_paywall_' . $mode . '_payment_status', true );
			$subscription['status'] = apply_filters( 'get_leaky_paywall_subscription_level_status', $subscription['status']  );
			
		    if ( !empty( $subscription['expires'] ) && time() > strtotime( $subscription['expires'] ) ) {
			    
			    $subscription['status'] = 'expired';
		    
		    } else if ( 'active' != $subscription['status'] ) {
			
				$subscription['expires'] = strtotime( 'yesterday' );
			    
		    }

			return $subscription;

		}
		
		return false;
		
	}
	
}

if ( !function_exists( 'unipress_api_get_user_level_id_by_device_id' ) ) {
	
	function unipress_api_get_user_level_id_by_device_id( $device_id ) {
		if ( $user = unipress_api_get_user_by_device_id( $device_id ) ) {
			
			$subscription = unipress_api_get_user_subscription_by_device_id( $device_id );
			return $subscription['level_id'];
			
		}
		
		return false;
	}
	
}

if ( !function_exists( 'unipress_api_get_user_level_id_by_user_id' ) ) {
	
	function unipress_api_get_user_level_id_by_user_id( $user_id ) {
		$settings = get_leaky_paywall_settings();
		$mode = 'off' === $settings['test_mode'] ? 'live' : 'test';

		$level_id = get_user_meta( $user->ID, '_issuem_leaky_paywall_' . $mode . '_level_id', true );
		$level_id = apply_filters( 'get_leaky_paywall_subscription_level_level_id', $level_id );

		$expires = get_user_meta( $user->ID, '_issuem_leaky_paywall_' . $mode . '_expires', true );
		$expires = apply_filters( 'get_leaky_paywall_subscription_level_expires', $expires );

		$status = get_user_meta( $user->ID, '_issuem_leaky_paywall_' . $mode . '_payment_status', true );
		$status = apply_filters( 'get_leaky_paywall_subscription_level_status', $status  );

		return $level_id;
	}
	
}

if ( !function_exists( 'unipress_api_get_user_leaky_paywall_created_timestamp_by_user_id' ) ) {
	
	function unipress_api_get_user_leaky_paywall_created_timestamp_by_user_id( $user_id ) {
		$settings = get_leaky_paywall_settings();
		$mode = 'off' === $settings['test_mode'] ? 'live' : 'test';

		return get_user_meta( $user_id, '_issuem_leaky_paywall_' . $mode . '_created', true );
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
		
		$settings = get_unipress_api_settings();

		if ( 'mobile' === $level_id ) { //deprecated
			return $settings['subscription-id'];
		} else if ( isset( $settings['subscription-ids'][$level_id] ) ) {
			return $settings['subscription-ids'][$level_id];
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

if ( !function_exists( 'unipress_api_leaky_paywall_has_user_paid' ) ) {
	
	function unipress_api_leaky_paywall_has_user_paid( $return, $payment_gateway, $payment_status, $subscriber_id, $plan, $expires ) {
		
		switch ( strtolower( $payment_gateway ) ) {
			
			case 'ios':
			case 'android':					
				switch( strtolower( $payment_status ) ) {
				
					case 'active':
					case 'refunded':
					case 'refund':
						if ( empty( $expires ) || '0000-00-00 00:00:00' === $expires )
							return 'unlimited';
							
						if ( strtotime( $expires ) > time() )
							return $expires;
						else
							return false;
						break;
					case 'cancelled':
					case 'canceled':
						if ( empty( $expires ) || '0000-00-00 00:00:00' === $expires )
							return false;
						else
							return 'canceled';
					case 'reversed':
					case 'buyer_complaint':
					case 'denied' :
					case 'expired' :
					case 'failed' :
					case 'voided' :
					case 'deactivated' :
						return false;
						break;
					
				}

				break;
			
		}
		
		return $return;
		
	}
	add_filter( 'leaky_paywall_has_user_paid', 'unipress_api_leaky_paywall_has_user_paid', 10, 6 );
}

if ( !function_exists( 'unipress_leaky_paywall_subscriber_payment_gateways' ) ) {
	function unipress_leaky_paywall_subscriber_payment_gateways( $payment_gateways ) {
		$payment_gateways['ios']     = __( 'Apple Payment Gateway', 'unipress-api' );
		$payment_gateways['android'] = __( 'Google Payment Gateway', 'unipress-api' );
		return $payment_gateways;
	}
	add_filter( 'leaky_paywall_subscriber_payment_gateways', 'unipress_leaky_paywall_subscriber_payment_gateways' );
}

if ( !function_exists( 'unipress_get_all_device_ids' ) ) {
	function unipress_get_all_device_ids() {
		global $wpdb;
		$device_ids = array();
		$sql = $wpdb->prepare(
			"
			SELECT meta_value
			FROM $wpdb->usermeta
			WHERE 
			meta_key LIKE '%s'
			",
			'unipress-devices'
		);
		return $wpdb->get_col( $sql );
	}
}

if ( !function_exists( 'unipress_get_device_ids_exclude_from_term_id' ) ) {
	function unipress_get_device_ids_exclude_from_term_id( $term_id ) {
		global $wpdb;
		$device_ids = array();
		$sql = $wpdb->prepare(
			"
			SELECT meta_key
			FROM $wpdb->usermeta
			WHERE 
			meta_key LIKE '%s'
			AND
			meta_value = '%d'
			",
			'upepc-%', //epc = excluded push categories
			$term_id
		);
		$results = $wpdb->get_col( $sql );
		if ( !empty( $results ) ) {
			foreach( $results as $result ) {
				$device_ids[] = str_replace( 'upepc-', '', $result ); //epc = excluded push categories
			}
		}
		return $device_ids;
	}
}

if ( !function_exists( 'unipress_is_device_id_unsubscribed_from_all_categories' ) ) {
	function unipress_is_device_id_unsubscribed_from_all_categories( $device_id ) {
		global $wpdb;
		$sql = $wpdb->prepare(
			"
			SELECT meta_value
			FROM $wpdb->usermeta
			WHERE 
			meta_key LIKE '%s'
			AND
			meta_value = 1
			",
			'uppcu-' . $device_id //pcu = push categories unsubscribed
		);
		return $wpdb->get_col( $sql );
	}
}

if ( !function_exists( 'unipress_get_article_device_ids' ) ) {
	function unipress_get_article_device_ids( $post ) {
		$terms = wp_get_post_terms( $post->ID, 'unipress-push-category' );
		$device_ids = unipress_get_all_device_ids();
		$excluded_device_ids = array();
		if ( !empty( $terms ) ) {
			foreach( $device_ids as $device_id ) {
				if ( unipress_is_device_id_unsubscribed_from_all_categories( $device_id ) ) {
					//If a user has unsubscribed from every category we want to remove the user's device from this push
					$excluded_device_ids[] = $device_id;
				}
			}
			foreach( $terms as $term ) {
				$devices = unipress_get_device_ids_exclude_from_term_id( $term->term_id );
				$excluded_device_ids = array_merge( $excluded_device_ids, $devices );
			}
			$excluded_device_ids = array_unique( $excluded_device_ids );
		} else {
			return array();
		}
		$device_ids = array_diff( $device_ids, $excluded_device_ids );
		return array_values( $device_ids ); //rekey the array
	}
}

if ( !function_exists( 'unipress_recursive_order_comments' ) ) {
	function unipress_recursive_order_comments( $_comments, $parent_id = 0 ) {
		$parents = array();
		if ( !empty( $_comments ) ) {
			foreach( $_comments as $_key => $_comment ) {
				if ( $parent_id == $_comment->comment_parent ) {
					$parents[] = $_comment;
					unset( $_comments[$_key] );
					$parents = array_merge( $parents, unipress_recursive_order_comments( $_comments, $_comment->comment_ID ) );
				}
			}
		}
		return $parents;
	}
}