<?php
/**
 * Registers UniPress API class
 *
 * @package UniPress API
 * @since 1.0.0
 */

/**
 * This class registers the main issuem functionality
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'UniPress_API_Extension' ) ) {
    require_once( UNIPRESS_API_PATH . 'class.php' );
    
    class UniPress_API_Extension extends UniPress_API {
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
         * Add topics and states taxonomies of users
         * Note, it ignores taxonomy/taxonomies request parameter in this case
         * 
         * @param array $args Request filter
         * @return Filter data
         */
        public function addBehaviourGetPosts($args) {
            $r = $args;
            //$this->request['custom_news_for_device_id'] = 1;
            $user_id = !empty($this->request['custom_news_for_device_id']) ? $this->getUserID($this->request['custom_news_for_device_id']) : 0;
            if ($user_id) {
                $user_taxonomies = $this->getUserMeta($user_id);
                $tax_query = [];
                foreach ($user_taxonomies as $taxonomy => $terms) {
                    foreach ($terms as $value) {
                        $tax_query[] = [
                            'taxonomy' => $taxonomy,
                            'field' => 'term_id',
                            'terms' => $value,
                        ];
                    }
                }
                
                if (!empty($tax_query)) {
                    $tax_query['relation'] = 'OR';
                    $r['tax_query'] = $tax_query;
                }
            }
            return $r;
        }
        
        /**
         * Get user's meta data
         * @param int $user_id User ID
         * @return array Meta Data
         */
        protected function getUserMeta($user_id) {
            //preset meat data for research
            $r = ['topics' => [], 'states' => []];
            if ($user_id) {
                $user_meta = get_user_meta($user_id);
                foreach ($r as $taxonomy => $value) {
                    $r[$taxonomy] = !empty($user_meta['_custom_news_' . $taxonomy][0]) ? unserialize($user_meta['_custom_news_' . $taxonomy][0]) : [];
                }
            }

            return $r;
        }
        
        /**
         * Get user Id by device ID
         * @param int $device_id
         * @return int User ID
         */
        protected function getUserID($device_id) {
            $r = unipress_api_get_user_by_device_id(trim($device_id));
            
            return $r ? $r : 0;
        }
    }
}