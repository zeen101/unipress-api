<?php

if ( !function_exists( 'unipress_register_meta_boxes' ) ) {
	function unipress_register_meta_boxes() {
		$settings = get_unipress_api_settings();
    	add_meta_box( 'unipress-meta-box', __( 'UniPress Options', 'unipress-api' ), 'unipress_meta_box', apply_filters( 'unipress_push_notification_article_notification_post_types', array( 'post', 'article' ) ) );
	}
	add_action( 'add_meta_boxes', 'unipress_register_meta_boxes' );
}

if ( !function_exists( 'save_unipress_meta_box' ) ) {
	function save_unipress_meta_box( $post_id ) {
		global $post;

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post ) ) {
            return;
        }
		
		if ( !empty( $_REQUEST['article-notification'] ) ) {
			update_post_meta( $post_id, 'unipress_article_notification', 'on' );
		} else {
			update_post_meta( $post_id, 'unipress_article_notification', 'off' );
		}
	}
	add_action( 'save_post', 'save_unipress_meta_box' );
}

if ( !function_exists( 'unipress_meta_box' ) ) {
	function unipress_meta_box( $post ) {
		echo '<div style="display: block;" id="unipress-meta-box-custom" class="postbox hide-if-js">';
		echo '</div>';
		$settings = get_unipress_api_settings();
		if ( ! $article_notification = get_post_meta( $post->ID, 'unipress_article_notification', true ) ) {
			$article_notification = $settings['article-notifications'];
		}
		echo '<input type="checkbox" id="article-notification" name="article-notification" ' . checked( $article_notification, 'on', false ) . ' />';
		echo '<label for="article-notification">' . __( 'Article Notification', 'unipress-api' ) . '</label>';
	}
}