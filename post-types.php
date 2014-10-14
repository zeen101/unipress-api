<?php

function unipress_api_post_types() {
	$labels = array(
		'name' 					=> _x( 'UniPress Ads', 'post type general name', 'unipress-api' ),
		'singular_name' 		=> _x( 'Ad', 'post type singular name', 'unipress-api' ),
		'menu_name' 			=> _x( 'UniPress Ads', 'admin menu', 'unipress-api' ),
		'name_admin_bar' 		=> _x( 'Ad', 'add new on admin bar', 'unipress-api' ),
		'add_new' 				=> _x( 'Add New', 'ad', 'unipress-api' ),
		'add_new_item' 			=> __( 'Add New Ad', 'unipress-api' ),
		'new_item' 				=> __( 'New Ad', 'unipress-api' ),
		'edit_item' 			=> __( 'Edit Ad', 'unipress-api' ),
		'view_item' 			=> __( 'View Ad', 'unipress-api' ),
		'all_items' 			=> __( 'All Ads', 'unipress-api' ),
		'search_items' 			=> __( 'Search Ads', 'unipress-api' ),
		'parent_item_colon' 	=> __( 'Parent Ads:', 'unipress-api' ),
		'not_found' 			=> __( 'No ads found.', 'unipress-api' ),
		'not_found_in_trash' 	=> __( 'No ads found in Trash.', 'unipress-api' )
	);
	
	$args = array(
		'labels' 				=> $labels,
		'public' 				=> false,
		'publicly_queryable' 	=> false,
		'show_ui' 				=> true,
		'show_in_menu' 			=> false,
		'query_var' 			=> false,
		'rewrite' 				=> array( 'slug' => 'unipress-ad' ),
		'capability_type' 		=> 'post',
		'register_meta_box_cb' 	=> 'unipress_api_ad_metaboxes',
		'has_archive' 			=> false,
		'hierarchical' 			=> false,
		'menu_position' 		=> null,
		'supports' 				=> array( 'title', 'thumbnail' )
	);
	
	register_post_type( 'unipress-ad', $args );
}
add_action( 'init', 'unipress_api_post_types' );

if ( !function_exists( 'unipress_api_ad_metaboxes' ) ) {
		
	/**
	 * Registers metaboxes for IssueM Articles
	 *
	 * @since 1.0.0
	 */
	function unipress_api_ad_metaboxes() {
		
		remove_meta_box( 'leaky_paywall_content_visibility', 'unipress-ad', 'side' );
		add_meta_box( 'unipress_ad_meta_box', __( 'UniPress Ad Options', 'unipress-api' ), 'unipress_ad_meta_box', 'unipress-ad', 'normal', 'high' );
		
		do_action( 'unipress_api_ad_metaboxes' );
		
	}

}

if ( !function_exists( 'unipress_ad_meta_box' ) ) {
	
	function unipress_ad_meta_box( $post ) {
		$ad_type = get_post_meta( $post->ID, '_ad_type', true );
		$ad_link = get_post_meta( $post->ID, '_ad_link', true );
		?>
		
		<div id="unipress-ad-metabox">
		
			<p>
			<label for="unipress-ad-type"><?php _e( 'Ad Type:', 'issuem' ); ?></label>&nbsp;
			<select id="unipress-ad-type" name="ad-type">
				<option value="phone" <?php selected( 'phone', $ad_type ) ?>><?php _e( 'Phone', 'unipress-api' ); ?></option>
				<option value="tablet" <?php selected( 'tablet', $ad_type ) ?>><?php _e( 'Tablet', 'unipress-api' ); ?></option>
			</select>
			</p>
			
			<p>
			<label for="unipress-ad-link"><?php _e( 'Ad Link:', 'issuem' ); ?></label>&nbsp;
			<input id="unipress-ad-link" type="text" name="ad-link" value="<?php echo $ad_link; ?>" />
			</p>

		</div>
		
		<?php	
	}
	
}

if ( !function_exists( 'save_unipress_ad_meta_box' ) ) {
	
	/**
	 * Saves Article meta
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id WordPress post ID
	 */
	function save_unipress_ad_meta_box( $post_id ) {
	
		// verify if this is an auto save routine. 
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;
			
		if ( isset( $_REQUEST['_inline_edit'] ) || isset( $_REQUEST['doing_wp_cron'] ) )
			return;
			
		if ( !empty( $_POST['ad-type'] ) )
			update_post_meta( $post_id, '_ad_type', $_POST['ad-type'] );
		else
			delete_post_meta( $post_id, '_ad_type' );
			
		if ( !empty( $_POST['ad-link'] ) )
			update_post_meta( $post_id, '_ad_link', $_POST['ad-link'] );
		else
			delete_post_meta( $post_id, '_ad_link' );
			
		do_action( 'save_unipress_ad_meta_box', $post_id );
				
	}
	add_action( 'save_post_unipress-ad', 'save_unipress_ad_meta_box' );

}