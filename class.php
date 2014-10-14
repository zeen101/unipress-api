<?php
/**
 * Registers zeen101's Leaky Paywall's UniPress API class
 *
 * @package zeen101's Leaky Paywall
 * @since 1.0.0
 */

/**
 * This class registers the main issuem functionality
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Leaky_Paywall_UniPress_API' ) ) {
	
	class Leaky_Paywall_UniPress_API {
		
		/**
		 * Class constructor, puts things in motion
		 *
		 * @since 1.0.0
		 */
		function __construct() {
			
			register_nav_menu( 'unipress-app-menu', 'UniPress Device Menu' );
			
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_wp_enqueue_scripts' ) );
			add_action( 'admin_print_styles', array( $this, 'admin_wp_print_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			
			add_filter( 'leaky_paywall_subscriber_info_paid_subscriber_end', array( $this, 'leaky_paywall_subscriber_info_paid_subscriber_end' ) );
			
			add_action( 'wp', array( $this, 'process_requests' ), 15 );

		}
		
		function admin_wp_enqueue_scripts( $hook_suffix ) {
			if ( 'leaky-paywall_page_unipress-settings' === $hook_suffix )
				wp_enqueue_script( 'leaky_paywall_js', ISSUEM_LP_UPAPI_URL . 'js/admin.js', array( 'jquery' ), ISSUEM_LP_UPAPI_VERSION );
		}
		
		function admin_wp_print_styles() {
			global $hook_suffix;
			if ( 'leaky-paywall_page_unipress-settings' === $hook_suffix )
				wp_enqueue_style( 'leaky_paywall_admin_style', ISSUEM_LP_UPAPI_URL . 'css/admin.css', '', ISSUEM_LP_UPAPI_VERSION );
		}
		
		function wp_enqueue_scripts() {
			wp_enqueue_script( 'unipress-api', ISSUEM_LP_UPAPI_URL . '/js/unipress.js', array( 'jquery' ), ISSUEM_LP_UPAPI_VERSION );
			wp_localize_script( 'unipress-api', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			wp_enqueue_style( 'unipress-api', ISSUEM_LP_UPAPI_URL . '/css/unipress.css', '', ISSUEM_LP_UPAPI_VERSION );
		}
		
		function admin_menu() {
			add_submenu_page( 'issuem-leaky-paywall', __( 'UniPress', 'unipress-api' ), __( 'UniPress', 'unipress-api' ), apply_filters( 'manage_leaky_paywall_settings', 'manage_options' ), 'unipress-settings', array( $this, 'settings_page' ) );
			add_submenu_page( 'issuem-leaky-paywall', __( 'UniPress Ads', 'unipress-ad' ), __( 'UniPress Ads', 'unipress-ad' ), apply_filters( 'manage_leaky_paywall_settings', 'manage_options' ), 'edit.php?post_type=unipress-ad' );
			add_submenu_page( 'issuem-leaky-paywall', __( 'New Ad', 'unipress-ad' ), __( 'New Ad', 'unipress-ad' ), apply_filters( 'manage_leaky_paywall_settings', 'manage_options' ), 'post-new.php?post_type=unipress-ad' );
		}
		
		/**
		 * Get zeen101's Leaky Paywall options
		 *
		 * @since 1.0.0
		 */
		function get_settings() {
			
			$defaults = array( 
				'device-limit' => 5,
				'subscription-id' => 0,
			);
		
			$defaults = apply_filters( 'unipress_api_settings_defaults', $defaults );
			
			$settings = get_option( 'unipress-api' );
												
			return wp_parse_args( $settings, $defaults );
			
		}
		
		/**
		 * Update zeen101's Leaky Paywall options
		 *
		 * @since 1.0.0
		 */
		function update_settings( $settings ) {
			
			update_option( 'unipress-api', $settings );
			
		}
		
		/**
		 * Create and Display IssueM settings page
		 *
		 * @since 1.0.0
		 */
		function settings_page() {
			
			// Get the user options
			$settings = $this->get_settings();
			$settings_saved = false;

			if ( isset( $_REQUEST['unipress_api_settings_nonce'] ) 
				&& wp_verify_nonce( $_REQUEST['unipress_api_settings_nonce'], 'save_unipress_api_settings' ) ) {
									
				if ( isset( $_REQUEST['device-limit'] ) && is_numeric( $_REQUEST['device-limit'] ) )
					$settings['device-limit'] = $_REQUEST['device-limit'];
				else
					$settings['device-limit'] = 5;
					
				if ( isset( $_REQUEST['subscription-id'] ) && is_numeric( $_REQUEST['subscription-id'] ) )
					$settings['subscription-id'] = $_REQUEST['subscription-id'];
				else
					$settings['subscription-id'] = 0;
				
				$this->update_settings( $settings );
				$settings_saved = true;
				
				do_action( 'update_unipress_api_settings', $settings );
				
			}
			
			if ( $settings_saved ) {
				
				// update settings notification ?>
				<div class="updated"><p><strong><?php _e( "UniPress Settings Updated.", 'unipress-api' );?></strong></p></div>
				<?php
				
			}
			
			// Display HTML form for the options below
			?>
			<div class=wrap>
            <div style="width:70%;" class="postbox-container">
            <div class="metabox-holder">	
            <div class="meta-box-sortables ui-sortable">
            
                <form id="issuem" method="post" action="">

                    <h2 style='margin-bottom: 10px;' ><?php _e( "zeen101's UniPress Settings", 'unipress-api' ); ?></h2>
                
                    <?php do_action( 'unipress_api_settings_form_start', $settings ); ?>
                    
                    <div id="modules" class="postbox">
                    
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        
                        <h3 class="hndle"><span><?php _e( 'UniPress Options', 'unipress-api' ); ?></span></h3>
                        
                        <div class="inside">
                        
                        <table id="leaky_paywall_administrator_options" class="leaky-paywall-table">
                                               
                        	<tr>
                                <th><?php _e( 'Device Limit', 'issuem-leaky-paywall' ); ?></th>
                                <td>
                                	<input type="number" id="device-limit" class="small-text" name="device-limit" value="<?php echo htmlspecialchars( stripcslashes( $settings['device-limit'] ) ); ?>" />
                                	<p class="description"><?php _e( 'The number of mobile devices a user can register', 'unipress-api' ); ?></p>
                                </td>
                            </tr>
                        	<tr>
                                <th><?php _e( 'Subscription ID', 'issuem-leaky-paywall' ); ?></th>
                                <td>
									<?php
									$lp_settings = get_leaky_paywall_settings();
									if ( !empty( $lp_settings['levels'] ) ) {
										echo '<select name="subscription-id">';
										foreach( $lp_settings['levels'] as $level_id => $level ) {
											echo '<option value="' . $level_id .'" ' . selected( $level_id, $settings['subscription-id'], true ) . '>' . $level['label'] . '</option>';
										}
										echo '</select>';
										echo '<p class="description">' . __( 'This is the subscription access that mobile users will get when they pay through the mobile app.', 'unipress-api' ) . '</p>';
									} else {
										echo '<p>' . __( 'No Subscription Found. Please add some to the Leaky Paywall settings.', 'unipress-api' ) . '</p>';
									}
									?>
                                </td>
                            </tr>
                            
                        </table>
                                                                          
                        <p class="submit">
                            <input class="button-primary" type="submit" name="update_leaky_paywall_settings" value="<?php _e( 'Save Settings', 'issuem-leaky-paywall' ) ?>" />
                        </p>

                        </div>
                        
                    </div>
                    
                    <?php wp_nonce_field( 'save_unipress_api_settings', 'unipress_api_settings_nonce' ); ?>             
                    <?php do_action( 'unipress_api_settings_form_end', $settings ); ?>
                    
                </form>
                
            </div>
            </div>
            </div>
			</div>
			<?php
			
		}
		
		function leaky_paywall_subscriber_info_paid_subscriber_end( $content ) {
			
			$settings = $this->get_settings();
			$current_user = wp_get_current_user();
			
			if ( 0 !== $current_user->ID ) {
				$devices = get_user_meta( $current_user->ID, 'unipress-devices' );
				$content .= '<div id="unipress-device-list">';
				if ( !empty( $devices ) ) {
					foreach( $devices as $device ) {
						$content .= unipress_api_device_row( $device );
					}
				}
				$content .= '</div>';
				
				$content .= '<div id="unipress-device-options">';
				if ( count( $devices ) < $settings['device-limit'] ) {
					$content .= '<a class="button unipress-add-new-device" href="#">Add New Mobile Device</a>';
				} else {
					$content .= '<p>' . __( 'You have reached your device limit, you must remove a device before adding new ones', 'unipress-api' ) . '</p>';
				}
				$content .= '</div>';
			}
			
			return $content;
			
		}
		
		function process_requests() {
			
			if ( !empty( $_REQUEST['unipress-api'] ) ) {
				
				switch ( $_REQUEST['unipress-api'] ) {
					
					case 'get-menu':
						$this->api_response( $this->get_menu() );
						break;
						
					case 'get-content-list':
						$this->api_response( $this->get_content_list() );
						break;
						
					case 'get-article':
						$this->api_response( $this->get_article() );
						break;
						
					case 'authorize-device':
						$this->api_response( $this->authorize_device() );
						break;
						
					case 'get-ad-data':
						$this->api_response( $this->get_ad_data() );
						break;
						
					case 'set-subscription':
						$this->api_response( $this->set_subscription() );
						break;
						
					case 'get-post-types':
						$this->api_response( $this->get_post_types() );
						break;
						
					case 'get-comments':
						$this->api_response( $this->get_comments() );
						break;
						
					case 'add-comment':
						$this->api_response( $this->add_comment() );
						break;
						
					default:
						$response = array(
							'http_code' => 502,
							'body' 		=> __( 'Unrecognized Request Sent', 'unipress-api' ),
						);
						$this->api_response( $response );
						break;
					
				}
				
			}
			
		}
		
		function api_response( $response ) {
				
	        header( 'HTTP/1.1 ' . $response['http_code'] . ' ' . $this->http_code_string( $response['http_code'] ) );
	        header( 'Content-type: application/json' );
	
	        // this should be templatized in a real-world solution
	        echo json_encode( $response['body'] );
			exit;
			
		}
		
		function http_code_string( $code ) {
			switch( $code ) {
				case '200':
					return __( 'Success', 'unipress-api' );
				case '201':
					return __( 'Created', 'unipress-api' );
				case '400':
					return __( 'Bad Request', 'unipress-api' );
				case '401':
					return __( 'Device not authorized', 'unipress-api' );
				case '402':
					return __( 'Subscription required', 'unipress-api' );
				case '502':
					return __( 'Bad Gateway', 'unipress-api' );
				default:
					return __( 'Unknown', 'unipress-api' );
			}
		}
		
		function get_menu() {
			if ( ( $locations = get_nav_menu_locations() ) && isset( $locations['unipress-app-menu'] ) ) {
				$menu = wp_get_nav_menu_object( $locations['unipress-app-menu'] );
		        if ( $menu && ! is_wp_error( $menu ) ) {
					$menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );
				} else {
					$menu_items = array();
				}
			} else {
				$menu_items = array();
			}
			
			$response = array(
				'http_code' => 200,
				'body' 		=> $menu_items,
			);
			return $response;
		}
		
		//parse data
		function get_content_list() {
			$args['page'] 			= !empty( $_REQUEST['page'] ) 			? $_REQUEST['page'] 			: 0;
			$args['posts_per_page'] = !empty( $_REQUEST['posts_per_page'] ) ? $_REQUEST['posts_per_page'] 	: 10;
			$args['orderby'] 		= !empty( $_REQUEST['orderby'] ) 		? $_REQUEST['orderby'] 			: 'post_date';
			$args['order'] 			= !empty( $_REQUEST['order'] ) 			? $_REQUEST['order'] 			: 'DESC';
			$args['post_type'] 		= !empty( $_REQUEST['post_type'] ) 		? $_REQUEST['post_type'] 		: array( 'post' );

			if ( !empty( $_REQUEST['taxonomy'] ) && !empty( $_REQUEST['term'] ) ) {
			
				if ( is_numeric( $_REQUEST['term'] ) ) {
					$field = 'term_id';
				} else {
					$field = 'slug';
				}
				
				$args['tax_query'] = array(
					array(
						'taxonomy' => $_REQUEST['taxonomy'],
						'field'    => $field,
						'terms'    => $_REQUEST['term'],
					),
				);

			}
			
			$upload_dir = wp_upload_dir();
			$posts = get_posts( $args );
			
			foreach( $posts as &$post ) {
				$args = array(
					'post_type' 		=> 'attachment',
					'posts_per_page' 	=> -1,
					'post_parent' 		=> $post->ID,
					'exclude' 			=> get_post_thumbnail_id( $post->ID )
				);
				$attachment_posts = get_posts( $args );
				$attachments = array();
		
				if ( !empty( $attachment_posts ) ) {
					foreach ( $attachment_posts as $attachment ) {
						$attachments[] = wp_get_attachment_metadata( $attachment->ID );
					}
				}
				
				$post->attachment_baseurl = $upload_dir['baseurl'];
				$post->attachments = $attachments;
				$post->featured_image = wp_get_attachment_metadata( get_post_thumbnail_id( $post->ID ) );
				
				$post->author_meta = new stdClass();
				$post->author_meta->user_login 		= get_the_author_meta( 'user_login', 		$post->post_author );
				$post->author_meta->user_nicename 	= get_the_author_meta( 'user_nicename', 	$post->post_author );
				$post->author_meta->display_name 	= get_the_author_meta( 'display_name', 		$post->post_author );
				$post->author_meta->nickname 		= get_the_author_meta( 'nickname', 			$post->post_author );
				$post->author_meta->first_name 		= get_the_author_meta( 'first_name', 		$post->post_author );
				$post->author_meta->last_name 		= get_the_author_meta( 'last_name', 		$post->post_author );
				$post->author_meta->nickname 		= get_the_author_meta( 'nickname', 			$post->post_author );
				$post->author_meta->user_firstname 	= get_the_author_meta( 'user_firstname', 	$post->post_author );
				$post->author_meta->user_lastname 	= get_the_author_meta( 'user_lastname', 	$post->post_author );
				$post->author_meta->description 	= get_the_author_meta( 'description', 		$post->post_author );
				
				$post->formatted_post_content = apply_filters( 'the_content', $post->post_content );
				
				$taxonomies = get_taxonomies();
				
				if ( !empty( $taxonomies ) ) {
					$post->taxonomies = new stdClass();
					
					foreach( $taxonomies as $taxonomy ) {
						
						$terms = wp_get_post_terms( $post->ID, $taxonomy );
						
						if ( !empty( $terms ) ) {
						
							foreach( $terms as &$term ) {
								$term->link = get_term_link( $term, $taxonomy );
							}
							
							$post->taxonomies->$taxonomy = $terms;
							
						}
					
					}
					
				}
			}
			
			$response = array(
				'http_code' => 200,
				'body' 		=> $posts,
			);
			return $response;
		}
		
		//tonight
		function get_article() {
			try {
				if ( empty( $_REQUEST['article-id'] ) ) {
					throw new Exception( __( 'Missing Article ID.', 'unipress-api' ), 400 );
				} else if ( !is_numeric( $_REQUEST['article-id'] ) ) {
					throw new Exception( __( 'Invalid Article Format.', 'unipress-api' ), 400 );
				}
				
				if ( empty( $_REQUEST['device-id'] ) ) {
					throw new Exception( __( 'Missing Device ID.', 'unipress-api' ), 400 );
				} else {
					$restrictions = unipress_api_get_user_restrictions_by_device_id( $_REQUEST['device-id'] );
				}
				
				$upload_dir = wp_upload_dir();
				$post = get_post( $_REQUEST['article-id'] );
			
				if ( !empty( $post ) ) {
					$args = array(
						'post_type' 		=> 'attachment',
						'posts_per_page' 	=> -1,
						'post_parent' 		=> $post->ID,
						'exclude' 			=> get_post_thumbnail_id( $post->ID )
					);
					$attachment_posts = get_posts( $args );
					$attachments = array();
			
					if ( !empty( $attachment_posts ) ) {
						foreach ( $attachment_posts as $attachment ) {
							$attachments[] = wp_get_attachment_metadata( $attachment->ID );
						}
					}
					
					$post->attachment_baseurl = $upload_dir['baseurl'];
					$post->attachments = $attachments;
					$post->featured_image = wp_get_attachment_metadata( get_post_thumbnail_id( $post->ID ) );
					
					$post->author_meta = new stdClass();
					$post->author_meta->user_login 		= get_the_author_meta( 'user_login', 		$post->post_author );
					$post->author_meta->user_nicename 	= get_the_author_meta( 'user_nicename', 	$post->post_author );
					$post->author_meta->display_name 	= get_the_author_meta( 'display_name', 		$post->post_author );
					$post->author_meta->nickname 		= get_the_author_meta( 'nickname', 			$post->post_author );
					$post->author_meta->first_name 		= get_the_author_meta( 'first_name', 		$post->post_author );
					$post->author_meta->last_name 		= get_the_author_meta( 'last_name', 		$post->post_author );
					$post->author_meta->nickname 		= get_the_author_meta( 'nickname', 			$post->post_author );
					$post->author_meta->user_firstname 	= get_the_author_meta( 'user_firstname', 	$post->post_author );
					$post->author_meta->user_lastname 	= get_the_author_meta( 'user_lastname', 	$post->post_author );
					$post->author_meta->description 	= get_the_author_meta( 'description', 		$post->post_author );
					
					$post->formatted_post_content = apply_filters( 'the_content', $post->post_content );
					
					$taxonomies = get_taxonomies();
					
					if ( !empty( $taxonomies ) ) {
						$post->taxonomies = new stdClass();
						
						foreach( $taxonomies as $taxonomy ) {
							
							$terms = wp_get_post_terms( $post->ID, $taxonomy );
							
							if ( !empty( $terms ) ) {
							
								foreach( $terms as &$term ) {
									$term->link = get_term_link( $term, $taxonomy );
								}
								
								$post->taxonomies->$taxonomy = $terms;
								
							}
						
						}
						
					}
					
				}
					
				$response = array(
					'http_code' => 200,
					'body' 		=> $post,
				);
				return $response;
			}
			catch ( Exception $e ) {
				$response = array(
					'http_code' => $e->getCode(),
					'body' 		=> $e->getMessage(),
				);
				return $response;
			}
		}
		
		function authorize_device() {
			try {
				$settings = $this->get_settings();

				$input = file_get_contents('php://input');
				$post = json_decode( $input, TRUE ); 
			
				if ( empty( $post['device-id'] ) ) {
					throw new Exception( __( 'Missing Device ID.', 'unipress-api' ), 400 );
				}
				
				if ( empty( $post['user-token'] ) ) {
					throw new Exception( __( 'Missing User Token.', 'unipress-api' ), 400 );
				} else if ( false === ( $user_id = get_transient( 'unipress_api_token_' . $post['user-token'] ) ) ) {
					throw new Exception( __( 'User Token has Expired. Please generate a new one.', 'unipress-api' ), 400 );
				}
				
				$devices = get_user_meta( $user_id, 'unipress-devices' );
				
				if ( !empty( $devices ) && in_array( $post['device-id'], $devices ) ) {
					$response = array(
						'http_code' => 201,
						'body' 		=> __( "Device already exists on user's device list", 'unipress-api' ),
					);
				} else if ( count( $devices ) >= $settings['device-limit'] ) {
					$response = array(
						'http_code' => 401,
						'body' 		=> __( "User has reached device limit. Please remove devices before attempting to add new devices.", 'unipress-api' ),
					);
				} else {
					add_user_meta( $user_id, 'unipress-devices', $post['device-id'] );
					$response = array(
						'http_code' => 200,
						'body' 		=> __( "Device added to user's device list", 'unipress-api' ),
					);
				}
				
				delete_transient( 'unipress_api_token_' . $post['user-token'] ); //We don't want to use this one again
				return $response;
			}
			catch ( Exception $e ) {
				$response = array(
					'http_code' => $e->getCode(),
					'body' 		=> $e->getMessage(),
				);
				return $response;
			}
		}
		
		function get_ad_data() {
			try {
				if ( empty( $_REQUEST['device-type'] ) ) {
					throw new Exception( __( 'Missing Device Type.', 'unipress-api' ), 400 );
				} else {
					if ( !( 'tablet' === strtolower( $_REQUEST['device-type'] ) || 'phone' === strtolower( $_REQUEST['device-type'] ) ) ) {
						throw new Exception( __( 'Invalid Device Type. Must be Table or Phone.', 'unipress-api' ), 400 );						
					}
				}
				
				$args = array(
					'post_type' 	=> 'unipress-ad',
					'meta_query' 	=> array(
						array(
							'key' 	=> '_ad_type',
							'value' => $_REQUEST['device-type'],
						),
					),
				);
				
				$ads = get_posts( $args );
				$body = array();
				
				if ( !empty( $ads ) ) {
					
					foreach ( $ads as $ad ) {
						$ad_link = get_post_meta( $ad->ID, '_ad_link', true );
						
						if ( !empty( $ad_link ) ) {
							$img_id = get_post_thumbnail_id( $ad->ID );
							$ad_img = wp_get_attachment_image_src( $img_id, 'full' ); ;
							
							if ( !empty( $ad_img ) ) {
								$body[] = array(
									'ad_link' 	=> $ad_link,
									'ad_img'	=> $ad_img[0], //URL
								);
							}
							
						}
						
					}
					
				}
				
				$response = array(
					'http_code' => 200,
					'body' 		=> $body,
				);
				return $response;
			}
			catch ( Exception $e ) {
				$response = array(
					'http_code' => $e->getCode(),
					'body' 		=> $e->getMessage(),
				);
				return $response;
			}
		}
		
		//curl -H "Content-Type: application/json" -d '{"device-id":"1234","device-type":"iOS","receipt":"true"}' http://issuem.lewayotte.com/?unipress-api=set-subscription
		function set_subscription() {
			try {
				$input = file_get_contents('php://input');
				$post = json_decode( $input, TRUE ); 
				
				if ( empty( $post['device-id'] ) ) {
					throw new Exception( __( 'Missing Device ID.', 'unipress-api' ), 400 );
				}
				
				if ( empty( $post['device-type'] ) ) {
					throw new Exception( __( 'Missing Device Type.', 'unipress-api' ), 400 );
				} else {
					if ( !( 'ios' === strtolower( $post['device-type'] ) || 'android' === strtolower( $post['device-type'] ) ) ) {
						throw new Exception( __( 'Invalid Device Type. Must be iOS or Android.', 'unipress-api' ), 400 );						
					}
				}
				
				if ( empty( $post['receipt'] ) ) {
					throw new Exception( __( 'Missing Receipt Data.', 'unipress-api' ), 400 );
				} else {
					//verify receipt data with Brisk server
					if ( 'fail' === $post['receipt'] ) {
						throw new Exception( __( 'Invalid Receipt Data.', 'unipress-api' ), 400 );						
					} else {
						$receipt = $post['receipt'];
					}
				}
								
				if ( $user = unipress_api_get_user_by_device_id( $post['device-id'] ) ) {
					$existing_customer = true;
					$login = $user->user_login;
					$email = $user->user_email;
					$unique_hash = leaky_paywall_hash( $email );
				} else {
					$existing_customer = false;
					//Create Guest User
					do {
						$username = 'unipress_' . uniqid();
						$user = get_user_by( 'login', $username ); 
					} while( false !== $user );
					
					$url = parse_url( get_bloginfo( 'wpurl' ) );
					
					//We need to generate a fake user for iOS subscribers
					$login = $username;
					$email = $username . '@' . $url['host'];
					$unique_hash = leaky_paywall_hash( $email );
				}
				
				$level = get_leaky_paywall_subscription_level( 'mobile' );
				$customer_id = uniqid(); //need to get transaction ID
				
				$meta = array(
					'level_id'			=> 'mobile',
					'subscriber_id' 	=> $customer_id, //need to get transaction ID
					'subscriber_email' 	=> $email,
					'price' 			=> '0.00', //need to get receipt price
					'description' 		=> sprintf( __( '%s Subscription', 'unipress-api' ), $post['device-type'] ),
					'payment_gateway' 	=> $post['device-type'],
					'payment_status' 	=> 'active',
					'expires' 			=> date_i18n( 'Y-m-d H:i:s', strtotime( '+1 month' ) ), //need to get receipt expiry
					'interval' 			=> $level['interval'],
					'interval_count' 	=> $level['interval_count'],
					'plan' 				=> $level['interval_count'] . ' ' . strtoupper( substr( $level['interval'], 0, 1 ) ),
				);
										
				if ( $existing_customer ) {
					leaky_paywall_update_subscriber( $unique_hash, $email, $customer_id, $meta );
					$response = array(
						'http_code' => 201,
						'body' 		=> __( 'Subscription Update', 'unipress-api' ),
					);
				} else {
					$meta['created'] = date_i18n( 'Y-m-d H:i:s' );
					leaky_paywall_new_subscriber( $unique_hash, $email, $customer_id, $meta );
					$response = array(
						'http_code' => 200,
						'body' 		=> __( 'Subscription Created', 'unipress-api' ),
					);
				}
				
				return $response;
			}
			catch ( Exception $e ) {
				$response = array(
					'http_code' => $e->getCode(),
					'body' 		=> $e->getMessage(),
				);
				return $response;
			}
		}	
		
		function get_post_types() {
			$post_types = get_post_types();
			unset( $post_types['attachment'] );
			unset( $post_types['revision'] );
			unset( $post_types['nav_menu_item'] );
			$response = array(
				'http_code' => 200,
				'body' 		=> array_values( $post_types ),
			);
			return $response;
		}
		
		function get_comments() {
			try {
				if ( empty( $_REQUEST['article-id'] ) ) {
					throw new Exception( __( 'Missing Article ID.', 'unipress-api' ), 400 );
				} else if ( !is_numeric( $_REQUEST['article-id'] ) ) {
					throw new Exception( __( 'Invalid Article Format.', 'unipress-api' ), 400 );
				} else {
					$args['post_id'] = $_REQUEST['article-id'];
				}
				
				$args['number'] 	= !empty( $_REQUEST['number'] ) 	? $_REQUEST['number'] 	: 10;
				$args['offset'] 	= !empty( $_REQUEST['offset'] ) 	? $_REQUEST['offset'] 	: 0;
				$args['orderby'] 	= !empty( $_REQUEST['orderby'] ) 	? $_REQUEST['orderby'] 	: 'comment_date_gmt';
				$args['order'] 		= !empty( $_REQUEST['order'] ) 		? $_REQUEST['order'] 	: 'DESC';
				$args['status']		= !empty( $_REQUEST['status'] ) 	? $_REQUEST['status'] 	: 'approve';
				
				$comments = get_comments( $args );
					
				$response = array(
					'http_code' => 200,
					'body' 		=> $comments,
				);
				return $response;
			}
			catch ( Exception $e ) {
				$response = array(
					'http_code' => $e->getCode(),
					'body' 		=> $e->getMessage(),
				);
				return $response;
			}
		}	
		
		function add_comment() {
			try {
				$input = file_get_contents('php://input');
				$post = json_decode( $input, TRUE ); 

				if ( empty( $post['article-id'] ) ) {
					throw new Exception( __( 'Missing Article ID.', 'unipress-api' ), 400 );
				} else if ( !is_numeric( $post['article-id'] ) ) {
					throw new Exception( __( 'Invalid Article Format.', 'unipress-api' ), 400 );
				}

				if ( empty( $post['device-id'] ) ) {
					throw new Exception( __( 'Missing Device ID.', 'unipress-api' ), 400 );
				}
				
				if ( empty( $post['device-type'] ) ) {
					throw new Exception( __( 'Missing Device Type.', 'unipress-api' ), 400 );
				} else {
					if ( !( 'ios' === strtolower( $post['device-type'] ) || 'android' === strtolower( $post['device-type'] ) ) ) {
						throw new Exception( __( 'Invalid Device Type. Must be iOS or Android.', 'unipress-api' ), 400 );						
					}
				}
								
				$user = unipress_api_get_user_by_device_id( $post['device-id'] );
				
				if ( empty( $user ) ) {
					throw new Exception( __( 'Unable to locate user for this device.', 'unipress-api' ), 400 );
				}
				
				if ( empty( $post['comment'] ) ) {
					throw new Exception( __( 'Empty Comment.', 'unipress-api' ), 400 );
				}
												
				$args = array(
					'comment_post_ID' 		=> $post['article-id'],
					'comment_author' 		=> $user->user_login,
					'comment_author_email' 	=> $user->user_email,
					'comment_content' 		=> $post['comment'],
					'comment_type' 			=> '',
					'comment_parent' 		=> 0, //we may want to include parent comment replies in the future
					'user_id' 				=> $user->ID,
					'comment_author_IP' 	=> unipress_api_get_ip_address(),
					'comment_agent' 		=> $post['device-type'],
					'comment_date' 			=> current_time('mysql'),
					'comment_approved' 		=> 1,
				);
				
				$comment_id = wp_insert_comment( $args );
										
				if ( !empty( $comment_id ) ) {
					$response = array(
						'http_code' => 201,
						'body' 		=> __( 'Comment added.', 'unipress-api' ),
					);
				} else {
					$response = array(
						'http_code' => 417,
						'body' 		=> __( 'Unable to add comment.', 'unipress-api' ),
					);
				}
				
				return $response;
			}
			catch ( Exception $e ) {
				$response = array(
					'http_code' => $e->getCode(),
					'body' 		=> $e->getMessage(),
				);
				return $response;
			}
		}		

	}

}