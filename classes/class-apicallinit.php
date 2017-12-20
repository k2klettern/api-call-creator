<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('apiCallCreate')) {
	class apiCallCreate {

		private $options;
		/**
		 * The Class Constructor
		 */
		public function __construct() {
			$this->options = get_option('api_call_options');
			$this->initHooks();
		}

		/**
		 * init_hooks
		 *
		 * Load all needed hooks
		 *
		 * @author Eric Zeidan <eric@zeidan.info>
		 * @since 1.0
		 */
		public function initHooks() {
			add_action( 'admin_init', array( $this, 'apiRedirect' ) );
			add_action( 'init', array($this, 'api_create_posttypes') );
		}

		public function apiActivate() {
			add_option( 'api_do_activation_redirect', true );
		}

		/**
		 * st_redirect
		 *
		 * Redirect to admin on activation
		 *
		 * @author Eric Zeidan <eric@zeidan.info>
		 * @since 1.0
		 */
		public function apiRedirect() {
			if ( get_option( 'pfi_do_activation_redirect', false ) ) {
				delete_option( 'pfi_do_activation_redirect' );

				//add_action( 'wp_after_admin_bar_render', array( $this, 'createDatabase' ) );

			}
		}

		public function api_create_posttypes() {
			register_post_type( 'apicalls',
				array(
					'labels' => array(
						'name' => __( 'Api Call', API_TEXT_DOMAIN ),
						'singular_name' => __( 'Api Calls', API_TEXT_DOMAIN ),
						'search_items'      => __( 'Search Api Calls', API_TEXT_DOMAIN ),
						'all_items'         => __( 'All Api Calls', API_TEXT_DOMAIN ),
						'parent_item'       => __( 'Parent Api Calls', API_TEXT_DOMAIN ),
						'parent_item_colon' => __( 'Parent Api Calls:', API_TEXT_DOMAIN ),
						'edit_item'         => __( 'Edit Api Calls', API_TEXT_DOMAIN ),
						'update_item'       => __( 'Update Api Calls', API_TEXT_DOMAIN ),
						'add_new_item'      => __( 'Add New Api Calls', API_TEXT_DOMAIN ),
						'new_item_name'     => __( 'New Api Calls', API_TEXT_DOMAIN ),
						'menu_name'         => __( 'Api Calls', API_TEXT_DOMAIN ),
					),
					'public' => true,
					'supports' => array( 'title' ),
					'has_archive' => true,
					'rewrite' => array('slug' => 'apicalls'),
					'menu_icon' => 'dashicons-share'
				)
			);
		}


		public function api_add_custom_endpoint() {
			add_action( 'rest_api_init', function () {
				register_rest_route( $this->options['main_name'] . '/' . $this->options['version'], '/author/(?P<id>\d+)', array(
					'methods' => 'GET',
					'callback' => 'my_awesome_func',
				) );
			} );
		}

		public function api_checkPHP($string = null) {
			if($string) {
				$checkResult = exec( 'echo \'<?php ' . escapeshellarg( $string ) . '\' | php -l >/dev/null 2>&1; echo $?' );
				if ( $checkResult != 0 ) {
					return false;
					//throw new \RuntimeException( "Invalid php" );
				} else {
					$result = eval( $string );
				}
			}
		}
	}
}
