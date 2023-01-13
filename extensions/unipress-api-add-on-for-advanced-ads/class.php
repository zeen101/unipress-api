<?php
/**
 * Registers UniPress API class
 *
 * @package UniPress API
 * @since 1.0.0
 */

/**
 * This class extends UniPress Module functionality
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'UniPress_API_AdvancedAds_Addon' ) ) {
    require_once( UNIPRESS_API_PATH . 'class.php' );
    
    class UniPress_API_AdvancedAds_Addon extends UniPress_API {
        protected $uniPressAPI = null;
        protected $request = [];
        /**
         * Class constructor, puts things in motion
         *
         * @since 1.0.0
         */
        public function __construct() {
            $this->request = $_REQUEST;
            
            $obj = $this;
            
            add_filter('unipress_get_content_list_get_posts_args', function($args) use (&$obj) {
                return $obj->addBehaviourGetPosts($args);
            });
        }

        /**
         * Add post type filter if it is replaced by other plugin
         * 
         * @param array $query Request query
         * @return WP_Query Query data
         */
        public function applyPostTypeFilter($query) {
            if ($query->is_search && $this->request['post_type']) {
                $query->set('post_type', $this->request['post_type']);
            }
            return $query;
        }

        /**
         * Add topics and states taxonomies of users
         * Note, it ignores taxonomy/taxonomies request parameter in this case
         * 
         * @param array $args Request filter
         * @return Filter data
         */
        public function addBehaviourGetPosts($args) {

            $obj = $this;

            add_filter('pre_get_posts', function($query) use (&$obj) {
                return $obj->applyPostTypeFilter($query);
            });

            $r = $args;
            
            $expired_posts = [];
            if ($r[post_type] === 'advanced_ads') {
                $obj = $this;
                add_filter('unipress_api_get_content_list_post', function($args) use (&$obj) {
                    return $obj->addBehaviourPost($args);
                });                
            }
            
            //exclude expired ads
            if (!empty( $_REQUEST['exclude_expired'] ) && $_REQUEST['exclude_expired']) {
                $meta = $this->getAllMeta('advanced_ads_ad_options');
                $time = time();
                $args['exclude'] = $args['exclude'] ? $args['exclude'] : [];
                foreach($meta as $m) {
                    if ($m['meta_value']['expiry_date'] && $m['meta_value']['expiry_date'] < $time) {
                        array_push($args['exclude'], $m['post_id']);
                    }
                }
            }
            
            return $args;
        }
        
        public function addBehaviourPost($post) {
            $meta = get_post_meta($post->ID, 'advanced_ads_ad_options', true);
            $post->advanced_ads = $meta;

            if ($post->advanced_ads['output'] && $post->advanced_ads['output']['image_id']) {
                $size = 'thumbnail';
                if ($post->advanced_ads['width'] && $post->advanced_ads['height']) {
                  $size = [$post->advanced_ads['width'], $post->advanced_ads['height']];
                }
                $post->advanced_ads['output']['image_url'] = wp_get_attachment_image_src($post->advanced_ads['output']['image_id'], $size);
            }
            return $post;
        }
        
        public function getAllMeta($key) {

            global $wpdb;

            if( empty( $key ) )
                return;

            $meta = $wpdb->get_results( $wpdb->prepare( "
                SELECT pm.post_id, pm.meta_value FROM {$wpdb->postmeta} pm
                WHERE pm.meta_key = %s 
            ", $key ), 'ARRAY_A');
            
            foreach($meta as $k => $value) {
                $meta[$k]['meta_value'] = maybe_unserialize($value['meta_value']);
            }
            
            return $meta;
        }
    }
}