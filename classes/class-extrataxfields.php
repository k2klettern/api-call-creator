<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('extraTaxFields')) {
	class extraTaxFields {

		/**
		 * The Class Constructor
		 */
		public function __construct() {
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
			add_action( 'init', array($this, 'api_create_regex_tax') );
			add_action( 'init', array($this, 'api_create_options_tax') );
			add_action( 'regex_edit_form_fields', array($this, 'regex_taxonomy_custom_fields'), 10, 2 );
			add_action( 'regex_add_form_fields',array($this, 'regex_taxonomy_custom_fields'), 10, 2);
			add_action( 'options_edit_form_fields', array($this, 'options_taxonomy_custom_fields'), 10, 2 );
			add_action( 'options_add_form_fields',array($this, 'options_taxonomy_custom_fields'), 10, 2);
			add_action( 'edited_regex', array($this, 'save_taxonomy_custom_fields'), 10, 2 );
			add_action( 'created_regex', array($this, 'save_taxonomy_custom_fields'), 10, 2 );
			add_action( 'edited_options', array($this, 'save_taxonomy_custom_fields'), 10, 2 );
			add_action( 'created_options', array($this, 'save_taxonomy_custom_fields'), 10, 2 );
			add_filter( 'manage_edit-regex_columns', array($this, 'add_regex_columns'));
			add_filter( 'manage_edit-options_columns', array($this, 'add_options_columns'));
			add_filter( 'manage_regex_custom_column', array($this, 'add_regex_column_content'),10,3);
			add_filter( 'manage_options_custom_column', array($this, 'add_options_column_content'),10,3);

		}

		public function api_create_regex_tax() {

			$labels = array(
				'name'              => _x( 'Regex', 'taxonomy general name', API_TEXT_DOMAIN ),
				'singular_name'     => _x( 'Regex', 'taxonomy singular name', API_TEXT_DOMAIN ),
				'search_items'      => __( 'Search Regex', API_TEXT_DOMAIN ),
				'all_items'         => __( 'All Regex', API_TEXT_DOMAIN ),
				'parent_item'       => __( 'Parent Regex', API_TEXT_DOMAIN ),
				'parent_item_colon' => __( 'Parent Regex:', API_TEXT_DOMAIN ),
				'edit_item'         => __( 'Edit Regex', API_TEXT_DOMAIN ),
				'update_item'       => __( 'Update Regex', API_TEXT_DOMAIN ),
				'add_new_item'      => __( 'Add New Regex', API_TEXT_DOMAIN ),
				'new_item_name'     => __( 'New Regex Name', API_TEXT_DOMAIN ),
				'menu_name'         => __( 'Regex', API_TEXT_DOMAIN ),
			);

			$args = array(
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_in_quick_edit'=> false,
				'meta_box_cb'       => false,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'regex' ),
			);

			register_taxonomy(
				'regex',
				'apicalls',
				$args
			);
		}

		public function api_create_options_tax() {

			$labels = array(
				'name'              => _x( 'Options', 'taxonomy general name', API_TEXT_DOMAIN ),
				'singular_name'     => _x( 'Options', 'taxonomy singular name', API_TEXT_DOMAIN ),
				'search_items'      => __( 'Search Options', API_TEXT_DOMAIN ),
				'all_items'         => __( 'All Options', API_TEXT_DOMAIN ),
				'parent_item'       => __( 'Parent Options', API_TEXT_DOMAIN ),
				'parent_item_colon' => __( 'Parent Options:', API_TEXT_DOMAIN ),
				'edit_item'         => __( 'Edit Options', API_TEXT_DOMAIN ),
				'update_item'       => __( 'Update Options', API_TEXT_DOMAIN ),
				'add_new_item'      => __( 'Add New Options', API_TEXT_DOMAIN ),
				'new_item_name'     => __( 'New Options Name', API_TEXT_DOMAIN ),
				'menu_name'         => __( 'Options', API_TEXT_DOMAIN ),
			);

			$args = array(
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_in_quick_edit'=> false,
				'meta_box_cb'       => false,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'options' ),
			);

			register_taxonomy(
				'options',
				'apicalls',
				$args
			);
		}

		public function options_taxonomy_custom_fields($tag) {
			// Check for existing taxonomy meta for the term you're editing
            if(isset($tag->term_id)) {
	            $t_id      = $tag->term_id; // Get the ID of the term you're editing
	            $term_meta = get_option( "taxonomy_term_$t_id" ); // Do the check
            }
			?>

			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="basename"><?php _e('Basename'); ?></label>
				</th>
				<td>
					<input type="text" name="term_meta[basename]" id="term_meta[basename]" size="25" style="width:60%;" value="<?php echo isset($term_meta['basename']) ? $term_meta['basename'] : ''; ?>"><br />
					<span class="description"><?php _e('Basename for the API'); ?></span>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="basename"><?php _e('Version'); ?></label>
				</th>
				<td>
					<input type="text" name="term_meta[version]" id="term_meta[version]" size="25" style="width:60%;" value="<?php echo isset($term_meta['version']) ? $term_meta['version'] : ''; ?>"><br />
					<span class="description"><?php _e('Version for the API'); ?></span>
				</td>
			</tr>

			<?php
		}

		public function regex_taxonomy_custom_fields($tag) {
			// Check for existing taxonomy meta for the term you're editing
            if(isset($tag->term_id)) {
	            $t_id      = $tag->term_id; // Get the ID of the term you're editing
	            $term_meta = get_option( "taxonomy_term_$t_id" ); // Do the check
            }
			?>

			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="regexcode"><?php _e('Regex to Filter'); ?></label>
				</th>
				<td>
					<input type="text" name="term_meta[regexcode]" id="term_meta[regexcode]" size="25" style="width:60%;" value="<?php echo isset($term_meta['regexcode']) ? $term_meta['regexcode'] : ''; ?>"><br />
					<span class="description"><?php _e('The Filtering Regex'); ?></span>
				</td>
			</tr>

			<?php
		}


		public function save_taxonomy_custom_fields( $term_id ) {
			if ( isset( $_POST['term_meta'] ) ) {
				$t_id = $term_id;
				$term_meta = get_option( "taxonomy_term_$t_id" );
				$cat_keys = array_keys( $_POST['term_meta'] );
				foreach ( $cat_keys as $key ){
					if ( isset( $_POST['term_meta'][$key] ) ){
						$term_meta[$key] = $_POST['term_meta'][$key];
					}
				}
				//save the option array
				update_option( "taxonomy_term_$t_id", $term_meta );
			}
		}

		function add_regex_columns($columns){
			$columns['regexcode'] = 'Regex Code';
			return $columns;
		}

		function add_options_columns($columns){
			$columns['basename'] = 'Base Name';
			$columns['version'] = 'Version';
			return $columns;
		}

		function add_regex_column_content($content,$column_name,$term_id){
			switch ($column_name) {
				case 'regexcode':
					//do your stuff here with $term or $term_id
					$term_meta = get_option( "taxonomy_term_$term_id" );
                    $content = $term_meta['regexcode'];
					break;
				default:
					break;
			}
			return $content;
		}

		function add_options_column_content($content,$column_name,$term_id){
			switch ($column_name) {
				case 'basename':
					$term_meta = get_option( "taxonomy_term_$term_id" );
					$content = $term_meta['basename'];
					break;
                case 'version':
	                $term_meta = get_option( "taxonomy_term_$term_id" );
	                $content = $term_meta['version'];
	                break;
				default:
					break;
			}
			return $content;
		}

	}

	new extraTaxFields();
}
