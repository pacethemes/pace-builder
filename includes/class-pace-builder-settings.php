<?php

/**
 * Class to handle Pace Builder settings.
 *
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */


class PTPB_Settings extends PTPB_Singleton {

	private $settings;
	private $fields;
	private $settings_saved;

	public function __construct(){

		$this->settings = get_option( 'ptpb_settings', array() );
		$this->fields = array();
		$this->settings_saved = false;

		// Admin hooks
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array($this, 'add_settings_page') );

	}

	public function enqueue_scripts(){
		wp_enqueue_style( 'ptpb-settings', PTPB()->plugin_url() . '/assets/css/settings.css' );
	}

	/**
	* Add the Page Builder settings page
	*/
	public function add_settings_page(){
		$page = add_options_page( __( 'Page Builder by PaceThemes', 'pace-builder' ), __( 'Pace Builder', 'pace-builder' ), 'manage_options', 'ptpb_settings', array( $this, 'display_settings_page' ) );
		add_action('load-' . $page, array( $this, 'save_settings'  ));
	}

	/**
	* Display the Page Builder settings page
	*/
	public function display_settings_page(){
		$settings_fields = $this->fields();
		include plugin_dir_path( __FILE__ ) . '/templates/settings.php';
	}

	public function get_setting( $name ) {
		if( empty( $name ) )
			return;

		if( isset( $this->settings[$name] ) )
			return $this->settings[$name];

		$defaults = $this->defaults();

		if( isset( $defaults[$name] ) )
			return $defaults[$name];

		return;

	}

	public function fields(){
		return apply_filters( 'ptpb_settings_fields', array(
				'post_types'    => array(
					'type' => 'select_multi',
					'label' => __('Post Types', 'pace-builder'),
					'options' => $this->get_post_types(),
					'description' => __('The post types Pace Builder should be enabled for.', 'pace-builder'),
				),
				'revisions'    => array(
					'type' => 'checkbox',
					'label' => __( 'Revisions', 'pace-builder' ),
					'options' => $this->get_post_types(),
					'description' => __('Enable Post/Page revisions for Page Builder.', 'pace-builder'),
				),
				'gmaps_api_key' => array(
					'type' => 'text',
					'label' => __( 'Google Maps API Key', 'pace-builder' ),
					'description' => sprintf( __( 'API key helps you track usage analytics. %sMore Info%s', 'pace-builder' ), '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">', '</a>' )
				)
			) );
	}

	public function defaults(){
		return array(
				'post_types'	 => array( 'page', 'post' ),
				'revisions'		 => 'on',
				'gmaps_api_key'	 => ''
			);
	}

	/**
	 * Display a form field
	 *
	 * @param $field_id
	 * @param $field
	 */
	public function form_field($field_id, $field){
		$value = $this->get_setting( $field_id ); 

		$field_name = 'ptpb_settings[' . $field_id . ']';

		switch ($field['type'] ) {
			case 'text':
			case 'float':
				?><input name="<?php echo esc_attr($field_name) ?>" class="ptpb-setting-<?php echo esc_attr($field['type']) ?>" type="text" value="<?php echo esc_attr($value) ?>" /> <?php
				break;

			case 'number':
				?>
				<input name="<?php echo esc_attr($field_name) ?>" type="number" class="ptpb-setting-<?php echo esc_attr($field['type']) ?>" value="<?php echo esc_attr($value) ?>" />
				<?php
				if( !empty($field['unit']) ) echo esc_html($field['unit']);
				break;

			case 'html':
				?><textarea name="<?php echo esc_attr($field_name) ?>" class="ptpb-setting-<?php echo esc_attr($field['type']) ?> widefat" rows="<?php echo !empty($field['rows']) ? intval($field['rows']) : 2 ?>"><?php echo esc_textarea($value) ?></textarea> <?php
				break;

			case 'checkbox':
				?>
				<label class="widefat">
					<input name="<?php echo esc_attr($field_name) ?>" type="checkbox" <?php checked( !empty($value) ) ?> />
					<?php echo !empty($field['checkbox_text']) ? esc_html($field['checkbox_text']) : __('Enabled', 'pace-builder') ?>
				</label>
				<?php
				break;

			case 'select':
				?>
				<select name="<?php echo esc_attr($field_name) ?>">
					<?php foreach( $field['options'] as $option_id => $option ) : ?>
						<option value="<?php echo esc_attr($option_id) ?>" <?php selected($option_id, $value) ?>><?php echo esc_html($option) ?></option>
					<?php endforeach; ?>
				</select>
				<?php
				break;

			case 'select_multi':
				$value = is_array( $value ) ? $value : array( $value );
				foreach( $field['options'] as $option_id => $option ) {
					?>
					<label class="widefat">
						<input name="<?php echo esc_attr($field_name) ?>[<?php echo esc_attr($option_id) ?>]" type="checkbox" <?php checked( in_array($option_id, $value) ) ?> />
						<?php echo esc_html($option) ?>
					</label>
					<?php
				}

				break;
		}
	}

	/**
	 * Save the Page Builder settings.
	 */
	public function save_settings(){
		$screen = get_current_screen();
		if( $screen->base != 'settings_page_ptpb_settings' ) return;

		if( !current_user_can('manage_options') ) return;
		if( empty($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'ptpb-settings') ) return;
		if( empty($_POST['ptpb_settings']) ) return;

		$values = array();
		$post = stripslashes_deep( $_POST['ptpb_settings'] );
		$settings_fields = $this->fields();

		if( empty($settings_fields) ) return;

		foreach( $settings_fields as $field_id => $field ) {

			switch( $field['type'] ) {
				case 'text' :
					$values[$field_id] = !empty($post[$field_id]) ? sanitize_text_field( $post[$field_id] ) : '';
					break;

				case 'number':
					if( $post[$field_id] != '' ) {
						$values[$field_id] = !empty($post[$field_id]) ? intval( $post[$field_id] ) : 0;
					}
					else {
						$values[$field_id] = '';
					}
					break;

				case 'float':
					if( $post[$field_id] != '' ) {
						$values[$field_id] = !empty($post[$field_id]) ? floatval( $post[$field_id] ) : 0;
					}
					else {
						$values[$field_id] = '';
					}
					break;

				case 'html':
					$values[$field_id] = !empty($post[$field_id]) ? $post[$field_id] : '';
					$values[$field_id] = wp_kses_post( $values[$field_id] );
					$values[$field_id] = force_balance_tags( $values[$field_id] );
					break;

				case 'checkbox':
					$values[$field_id] = !empty( $post[$field_id] );
					break;

				case 'select':
					$values[$field_id] = !empty( $post[$field_id] ) ? $post[$field_id] : '';
					if( !in_array( $values[$field_id], array_keys($field['options']) ) ) {
						unset($values[$field_id]);
					}
					break;

				case 'select_multi':
					$values[$field_id] = array();
					$multi_values = array();
					foreach( $field['options'] as $option_id => $option ) {
						$multi_values[$option_id] = !empty($post[$field_id][$option_id]);
					}
					foreach( $multi_values as $k => $v ) {
						if( $v ) $values[$field_id][] = $k;
					}

					break;
			}

		}

		// Save the values to the database
		update_option( 'ptpb_settings', $values );
		$this->settings = wp_parse_args( $values, $this->settings );
		$this->settings_saved = true;
	}

	/**
	 * Get all available post types
	 *
	 * @return array
	 */
	public function get_post_types(){
		$types = array_merge( array( 'page' => 'page', 'post' => 'post' ), get_post_types( array( '_builtin' => false, 'public' => true ) ) );

		// These are post types we know we don't want to show Page Builder on
		unset( $types['ml-slider'] );

		foreach( $types as $type_id => $type ) {
			$type_object = get_post_type_object( $type_id );

			if( !$type_object->show_ui ) {
				unset($types[$type_id]);
				continue;
			}

			$types[$type_id] = $type_object->label;
		}

		return $types;
	}

}

PTPB_Settings::instance();

function ptpb_get_setting( $name ){
	return PTPB_Settings::instance()->get_setting( $name );
}
