<?php
/**
 * Created by PhpStorm.
 * User: ezeidan
 * Date: 20/12/17
 * Time: 17:46
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('metaboxes')) {
	class metaboxes {
		/**
		 * The Class Constructor
		 */
		public function __construct() {
			add_action('add_meta_boxes', array($this, 'api_add_metabox'));
			add_action('save_post', array($this, 'save'));
			add_action('the_content', array($this, 'custom_message'));
		}

		public function api_add_metabox($api_type) {
			$api_types = array('apicalls');

			//limit meta box to certain post types
			if (in_array($api_type, $api_types)) {
				add_meta_box('apicallsbox',
					__('Build Endpoint',API_TEXT_DOMAIN),
					array($this, 'api_fields_metabox'),
					$api_type,
					'normal',
					'high');
			}
		}

		public function api_fields_metabox($post) {

			// Add an nonce field so we can check for it later.
			wp_nonce_field('apicall_nonce_check', 'apicall_nonce_check_value');


			$this->apicall_custom_taxonomy_dropdown('regex', 'date', 'DESC', 'regex', 'Select All', 'Select None');


			$this->apicall_custom_taxonomy_dropdown('options', 'date', 'DESC', 'regex', 'Select All', 'Select None');

		}

		public function save($api_id) {

			/*
			* We need to verify this came from the our screen and with
			* proper authorization,
			* because save_post can be triggered at other times.
			*/

			// Check if our nonce is set.
			if (!isset($_POST['apicall_nonce_check_value']))
				return $api_id;

			$nonce = $_POST['apicall_nonce_check_value'];

			// Verify that the nonce is valid.
			if (!wp_verify_nonce($nonce, 'apicall_nonce_check'))
				return $api_id;

			// If this is an autosave, our form has not been submitted,
			//     so we don't want to do anything.
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
				return $api_id;

			// Check the user's permissions.
			if ('page' == $_POST['api_type']) {

				if (!current_user_can('edit_page', $api_id))
					return $api_id;

			} else {

				if (!current_user_can('edit_post', $api_id))
					return $api_id;
			}

			/* OK, its safe for us to save the data now. */

			// Sanitize the user input.
			if (isset($_POST['input'])) {
				$data = $_POST['input'];
				$data = array_filter($data, function($value) { return $value['message'] !== ''; });
				// Update the meta field.
				$update = update_api_meta($api_id, '_apicall_details', $data);
				$updateref = update_api_meta($api_id, '_reference', $_POST['referencia']);
				$last = array_pop($data);
				$poapi_author = get_poapi_field( 'poapi_author', $api_id );
				if($update) {
					$mail = StHelpers::getInstance()->email_trigger($last['type'], $poapi_author, $api_id);
					if(!is_wp_error($mail)) {
						echo "se envio correo";
					} else {
						wp_die(json_enconde($mail));
					}
				}
			}

		}

		public function custom_message($content) {
			global $post;
			//retrieve the metadata values if they exist
			$data = get_poapi_meta($post->ID, '_apicall_details', true);
			if (!empty($data) && !is_array($data)) {
				$custom_message = "<div style='background-color: #FFEBE8;border-color: #C00;padding: 2px;margin:2px;font-weight:bold;text-align:center'>";
				$custom_message .= $data['description']."<br/>";
				$custom_message .= "</div>";
				$content = $custom_message . $content;
			}

			return $content;
		}

		public function apicall_custom_taxonomy_dropdown( $taxonomy, $orderby = 'date', $order = 'DESC', $limit = '-1', $name, $show_option_all = null, $show_option_none = null ) {
			$args = array(
				'orderby' => $orderby,
				'order' => $order,
				'number' => $limit,
				'hide_empty' => false
			);
			$terms = get_terms( $taxonomy, $args );

			$name = ( $name ) ? $name : $taxonomy;
			if ( $terms ) {
				printf( '<select name="%s" class="postform">', esc_attr( $name ) );
				if ( $show_option_all ) {
					printf( '<option value="0">%s</option>', esc_html( $show_option_all ) );
				}
				if ( $show_option_none ) {
					printf( '<option value="-1">%s</option>', esc_html( $show_option_none ) );
				}
				foreach ( $terms as $term ) {
					printf( '<option value="%s">%s</option>', esc_attr( $term->slug ), esc_html( $term->name ) );
				}
				print( '</select>' );
			}
		}
	}

	new metaboxes();
}