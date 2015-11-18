<?php
	
function unipress_push_taxonomies() {

	$labels = array(
		'name'                       => _x( 'Push Categories', 'Taxonomy General Name', 'unipess-api' ),
		'singular_name'              => _x( 'Push Category', 'Taxonomy Singular Name', 'unipess-api' ),
		'menu_name'                  => __( 'Push Category', 'unipess-api' ),
		'all_items'                  => __( 'All Items', 'unipess-api' ),
		'parent_item'                => __( 'Parent Item', 'unipess-api' ),
		'parent_item_colon'          => __( 'Parent Item:', 'unipess-api' ),
		'new_item_name'              => __( 'New Item Name', 'unipess-api' ),
		'add_new_item'               => __( 'Add New Item', 'unipess-api' ),
		'edit_item'                  => __( 'Edit Item', 'unipess-api' ),
		'update_item'                => __( 'Update Item', 'unipess-api' ),
		'view_item'                  => __( 'View Item', 'unipess-api' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'unipess-api' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'unipess-api' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'unipess-api' ),
		'popular_items'              => __( 'Popular Items', 'unipess-api' ),
		'search_items'               => __( 'Search Items', 'unipess-api' ),
		'not_found'                  => __( 'Not Found', 'unipess-api' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
	);
	register_taxonomy( 'unipress-push-category', apply_filters( 'unipress_push_taxonomies_post_types', array( 'post', ' article', 'unipress-push' ) ), $args );

}
add_action( 'init', 'unipress_push_taxonomies', 0 );