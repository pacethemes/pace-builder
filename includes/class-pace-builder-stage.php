<?php

/**
 * Handles all HTML for Pace Builder display and 'stage' in the admin backend.
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */

class PTPB_Stage extends PTPB_Singleton {

	private $pb_dir;
	private $pb_url;

	/**
	 *
	 */
	public function __construct() {

		//set the Page Builder specific variables
		$this->pb_dir = trailingslashit( dirname( __FILE__ ) );
		$this->pb_url = plugin_dir_url( __FILE__ );

		//setup required Hooks and Filters
		add_action( 'edit_form_after_title', array( $this, 'pb_button' ), 10, 1 );
		add_action( 'after_setup_theme', array( $this, 'initialize_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 10, 1 );

		// Pace Builder Ajax Action hooks
		add_action( 'wp_ajax_ptpb_widget_form', array( $this, 'widget_form' ) );
		add_action( 'wp_ajax_ptpb_import_layout', array( $this, 'import_layout' ) );
		add_action( 'wp_ajax_ptpb_export_layout', array( $this, 'export_layout' ) );
		add_action( 'wp_ajax_ptpb_get_layout', array( $this, 'get_layout' ) );
		add_action( 'wp_ajax_ptpb_save_layout', array( $this, 'save_layout' ) );
		add_action( 'wp_ajax_ptpb_delete_layout', array( $this, 'delete_layout' ) );

		add_action( 'wp_ajax_ptpb_get_prebuilt_layouts', array( $this, 'get_prebuilt_layouts' ) );

		if ( in_array( $GLOBALS['pagenow'], array( 'edit.php', 'post.php', 'post-new.php' ) ) ) {
			add_action( 'admin_footer', array( $this, 'js_templates' ) );
			// add_action( 'admin_footer', array( $this, 'pagebuilder_tour' ) );
		}

		// tinymce buttons
		add_action( 'init', array( $this, 'tinymce_buttons' ) );
	}

	/**
	 * @param $post
	 */
	public function pb_button( $post ) {
		if ( in_array( $post->post_type, $this->get_pb_post_types() ) ) {
			printf( "<a href='#' class='button button-primary' id='pt_enable_pb'>%s</a>", __( 'Pace Builder', 'pace-builder' ) );
		}
	}

	/**
	 * @return mixed|void
	 */
	public function get_pb_post_types() {
		return apply_filters( 'ptpb_builder_post_types', ptpb_get_setting( 'post_types' ) );
	}

	/**
	 * calls add_action on 'add_meta_boxes' hook and attaches the 'add_meta_box' function
	 *
	 * @return void
	 */
	public function initialize_meta_box() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
	}

	/**
	 * Adds the Page Builder Metabox to allowed post types, Post types can be modified using the 'ptpb_builder_post_types' filter
	 *
	 * @return void
	 */
	public function add_meta_box() {
		$post_types = $this->get_pb_post_types();

		foreach ( $post_types as $post_type ) {
			add_meta_box( 'pt-pb-stage', __( 'Pace Builder', 'pace-builder' ), array(
				$this,
				'stage_html'
			), $post_type, 'normal', 'high' );
		}
	}

	/**
	 * Enqueues required js and css for the pagebuilder
	 *
	 * @param $hook
	 */
	public function enqueue_assets( $hook ) {
		global $typenow, $post;

		if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) {
			return;
		}

		/*
		 * Load the builder javascript and css files for custom post types
		 * custom post types can be added using ptpb_builder_post_types filter
		*/
		if ( isset( $typenow ) && in_array( $typenow, $this->get_pb_post_types() ) ) {

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'pb-font-awesome-css', PTPB()->plugin_url() . '/assets/plugins/font-awesome/css/font-awesome.min.css' );
			wp_enqueue_style( 'pb-themify-icons', PTPB()->plugin_url() . '/assets/plugins/themify-icons/themify-icons.css' );
			wp_enqueue_style( 'pb-rangeslider', PTPB()->plugin_url() . '/assets/plugins/ion-rangeslider/css/ion.rangeSlider.css' );
			wp_enqueue_style( 'pt-chosen', PTPB()->plugin_url() . '/assets/plugins/chosen/chosen.min.css' );
			wp_enqueue_style( 'pb-backbone-modal', PTPB()->plugin_url() . '/assets/plugins/backbone-modal/backbone.modal.css' );
			wp_enqueue_style( 'pb-jquery-onoff', PTPB()->plugin_url() . '/assets/plugins/jquery.onoff/jquery.onoff.css' );
			wp_enqueue_style( 'pb-animate-css', PTPB()->plugin_url() . '/assets/plugins/animate/animate.css' );
			wp_enqueue_style( 'pb-admin-builder', PTPB()->plugin_url() . '/assets/css/pacebuilder-admin.css' );

			if( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-slider' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'jquery-ui-draggable' );
				wp_enqueue_script( 'jquery-ui-droppable' );
				wp_enqueue_script( 'imagesloaded' );
				wp_enqueue_script( 'jquery-masonry' );
				wp_enqueue_script( 'underscore' );
				wp_enqueue_script( 'backbone' );

				wp_enqueue_script( 'jquery-ui-timepicker', PTPB()->plugin_url() . '/assets/plugins/timepicker/jquery-ui-timepicker-addon.min.js', array( 'jquery-ui-datepicker' ) );
				wp_enqueue_script( 'backbone-marionette', PTPB()->plugin_url() . '/assets/plugins/backbone-marionette/backbone.marionette.min.js', array( 'backbone' ) );
				wp_enqueue_script( 'backbone-modal', PTPB()->plugin_url() . '/assets/plugins/backbone-modal/backbone.modal.js', array( 'backbone' ) );
				wp_enqueue_script( 'backbone-marionette-modals', PTPB()->plugin_url() . '/assets/plugins/backbone-modal/backbone.marionette.modals.js', array( 'backbone' ) );
				wp_enqueue_script( 'jquery-onoff', PTPB()->plugin_url() . '/assets/plugins/jquery.onoff/jquery.onoff.min.js', array( 'jquery' ) );
				wp_enqueue_script( 'serialize-object', PTPB()->plugin_url() . '/assets/plugins/serialize-object/jquery.serialize-object.min.js', array( 'jquery' ) );
				wp_enqueue_script( 'pb-rangeslider', PTPB()->plugin_url() . '/assets/plugins/ion-rangeslider/js/ion.rangeSlider.js' );
				wp_enqueue_script( 'wp-color-picker-alpha', PTPB()->plugin_url() . '/assets/plugins/wp-color-picker-alpha/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), PTPB()->version, true );
				wp_enqueue_script( 'jquery-chosen', PTPB()->plugin_url() . '/assets/plugins/chosen/chosen.jquery.min.js', array( 'jquery' ), PTPB()->version, true );
				wp_enqueue_script( 'google-maps', $this->google_maps_url(), array( ), PTPB()->version, true );
				wp_enqueue_script( 'mapsed', PTPB()->plugin_url() . '/assets/plugins/mapsed/mapsed.min.js', array( 'google-maps' ), PTPB()->version, true );

				wp_enqueue_script( 'ptpb_util_js', PTPB()->plugin_url() . '/assets/js/builder/util.js', array(), PTPB()->version, true );
				wp_enqueue_script( 'ptpb_models_js', PTPB()->plugin_url() . '/assets/js/builder/models.js', array( 'ptpb_util_js' ), PTPB()->version, true );
				wp_enqueue_script( 'ptpb_collections_js', PTPB()->plugin_url() . '/assets/js/builder/collections.js', array( 'ptpb_models_js' ), PTPB()->version, true );
				wp_enqueue_script( 'ptpb_views_js', PTPB()->plugin_url() . '/assets/js/builder/views.js', array( 'ptpb_collections_js' ), PTPB()->version, true );
				wp_enqueue_script( 'ptpb_admin_js', PTPB()->plugin_url() . '/assets/js/builder/app.js', array( 'ptpb_collections_js', 'ptpb_views_js' ), PTPB()->version, true );
			} else {
				$required = array( 'jquery', 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-datepicker', 'jquery-ui-draggable', 'jquery-ui-droppable', 'imagesloaded', 'jquery-masonry', 'underscore', 'backbone', 'wp-color-picker' );
				wp_enqueue_script( 'google-maps', $this->google_maps_url(), array( ), PTPB()->version, true );

				wp_enqueue_script( 'ptpb_plugins', PTPB()->plugin_url() . '/assets/js/admin-plugins.min.js', $required, PTPB()->version, true );
				wp_enqueue_script( 'ptpb_admin_js', PTPB()->plugin_url() . '/assets/js/admin-builder.min.js', array( 'ptpb_plugins' ),PTPB()->version, true );
			}			

			wp_localize_script( 'ptpb_admin_js', 'ptPbOptions',
				array(
					'ajaxurl'     => wp_nonce_url( admin_url( 'admin-ajax.php' ), 'ptpb_action', '_ptpb_nonce' ),
					'isPb'        => ptpb_is_pb(),
					'data'        => ptpb_get_data(),
					'pageOptions' => ptpb_get_page_options(),
					'widgets'     => $this->get_widgets(),
					'formFields'  => $this->get_form_fields(),
					'icons'		  => $this->get_icons(),
					'layouts'	  => $this->get_extra_layouts(),
					'plupload'    => array(
						'max_file_size'       => wp_max_upload_size() . 'b',
						'url'                 => wp_nonce_url( admin_url( 'admin-ajax.php' ), 'ptpb_action', '_ptpb_nonce' ),
						'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
						'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
						'filter_title'        => __( 'Pace Builder layouts', 'pace-builder' ),
						'error_message'       => __( 'Error uploading or importing file.', 'pace-builder' ),
					),
					'i18n'        => array(
						'default_editor'     => __( 'Would you like to clear Pace Builder content and revert to using the default visual editor ?', 'pace-builder' ),
						'builder_text'       => __( 'Pace Builder', 'pace-builder' ),
						'editor_text'        => __( 'Default Editor', 'pace-builder' ),
						'remove_section'     => __( 'Are you sure you want to delete this Section ? This step cannot be undone.', 'pace-builder' ),
						'remove_row'         => __( 'Are you sure you want to delete this Row ? This step cannot be undone.', 'pace-builder' ),
						'remove_slide'       => __( 'Are you sure you want to delete this Slide ? This step cannot be undone.', 'pace-builder' ),
						'remove_image'       => __( 'Are you sure you want to delete this Image ? This step cannot be undone.', 'pace-builder' ),
						'remove_module'      => __( 'Are you sure you want to delete this Module ? This step cannot be undone.', 'pace-builder' ),
						'resize_columns'     => __( 'You are about to resize the columns to a lower size than the existing columns, it may remove the last columns and will result in data/module loss. Do you really want to do this ?', 'pace-builder' ),
						'module_options'     => __( 'Module Style Options', 'pace-builder' ),
						'widget_options'     => __( 'Widget Style Options', 'pace-builder' ),
						'full_screen'        => __( 'Full Screen Mode', 'pace-builder' ),
						'full_screen_exit'   => __( 'Exit Full Screen', 'pace-builder' ),
						'empty_layout_name'  => __( 'Please enter a layout name.', 'pace-builder' ),
						'empty_layout'       => __( 'Looks like you are trying to save an empty layout, add some elements before saving the layout.', 'pace-builder' ),
						'empty_db_layouts'   => __( 'You do not have any layouts saved to the Database.', 'pace-builder' ),
						'delete_layout'      => __( 'Are you sure you want to delete this layout from the Database ?', 'pace-builder' ),
						'replace_layout'     => __( 'Are you sure you want to replace the current layout with this layout ?', 'pace-builder' ),
						'clear_layout'       => __( 'Are you sure you want to clear/remove the current layout ?', 'pace-builder' ),
						'prebuilt_get_error' => __( 'An error occured while fetching layouts', 'pace-builder' ),
					),
					'fonts'       => PTPB()->fonts()->get_all_fonts_dropdown()
				)
			);

		}
	}

	/**
	 * URL for Google Maps
	 * @return string
	 */
	private function google_maps_url() {

		$instance = ptpb_get_module_instance( 'PTPB_Module_GoogleMap' );

		if( ! $instance ) {
			return '//maps.googleapis.com/maps/api/js?libraries=places';
		}

		return $instance->maps_url();

	}

	/**
	 * Get an array of all the available widgets.
	 *
	 * @return array
	 */
	private function get_widgets() {
		global $wp_widget_factory;
		$widgets = array();
		foreach ( $wp_widget_factory->widgets as $class => $widget_obj ) {
			$widgets[ $class ] = array(
				'class'       => $class,
				'label'       => ! empty( $widget_obj->name ) ? $widget_obj->name : __( 'Untitled Widget', 'pace-builder' ),
				'description' => ! empty( $widget_obj->widget_options['description'] ) ? $widget_obj->widget_options['description'] : '',
				'installed'   => true
			);
		}

		return apply_filters('ptpb_widgets', $widgets);
	}

	/**
	 * Get all Backbone PaceBuilder Form Fields.
	 *
	 * @return array
	 */
	public function get_form_fields(){
		return array(
				'section' 	=> $this->section_js_options(),
				'row' 		=> $this->row_js_options(),
				'column' 	=> $this->column_js_options(),
				'modules' 	=> $this->get_modules(),
				'items'     => $this->get_module_items()
			);
	}

	/**
	 * Get all registered icon fonts.
	 *
	 * @return array
	 */
	public function get_icons() {
		$icons = array();
		foreach( PTPB_Icons::instance()->icons() as $name => $family ) {
			if( ! empty( $family['icons'] ) && is_array( $family['icons'] ) ) {
				$icons[$name] = $family['icons'];
			}
		}
		return $icons;
	}

	/**
	 * Get all PaceBuilder layouts registered by theme
	 *
	 * @return array
	 */
	public function get_extra_layouts() {
		return apply_filters( 'ptpb_prebuilt_layouts', array() );
	}

	/**
	 * Get all Backbone section options
	 *
	 * @return array
	 */
	private function section_js_options(){
		return $this->js_options( PTPB_Section::instance()->fields() );
	}

	/**
	 * Get all Backbone row options
	 *
	 * @return array
	 */
	private function row_js_options(){
		return $this->js_options( PTPB_Row::instance()->fields() );
	}

	/**
	 * Get all Backbone column options
	 *
	 * @return array
	 */
	private function column_js_options(){
		return $this->js_options( PTPB_Column::instance()->fields() );
	}

	/**
	 * convert normal options into backbone options format
	 *
	 * @return array
	 */
	private function js_options( $options ){
		$js_options = array();
		if( isset( $options ) && is_array( $options ) ) {
			foreach ( $options as $name => $args ) {
				$js_options[ $name ] = isset( $args['default'] ) ? $args['default'] : '';
			}
		}
		return $js_options;
	}

	/**
	 * Get all registered PaceBuilder modules
	 * 
	 * @return mixed|void
	 */
	public function get_modules() {

		$js_modules = array();

		foreach ( PTPB()->modules() as $key => $module_class ) {
			$instance = ptpb_get_module_instance( $key );
			if ( ! $instance || ! method_exists( $instance, 'fields' ) || 'PTPB_Module_Widget' === $module_class ) {
				continue;
			}

			$common_options = array(
				'ic'          => $instance->icon,
				'label'       => $instance->label,
				'description' => $instance->description,
				'hasItems'    => $instance->has_items,
				'tabPanes'	  => $instance->tab_panes
			);
			
			$js_modules[ $key ] = array_merge( $this->js_options( $instance->fields() ), $common_options );
		}

		return apply_filters( 'ptpb_modules', $js_modules );

	}

	/**
	 * Get all registered PaceBuilder modules' items
	 *
	 * @return mixed|void
	 */
	public function get_module_items() {

		$js_module_items = array();

		foreach ( PTPB()->modules() as $key => $module_class ) {
			$instance = ptpb_get_module_instance( $key );
			if ( ! $instance || ! method_exists( $instance, 'item_fields' ) ) {
				continue;
			}

			$common_options = array(
				'label'       => $instance->item_label
			);
			
			$js_module_items[ $key ] = array_merge( $this->js_options( $instance->item_fields() ), $common_options );
		}

		return apply_filters( 'ptpb_module_items', $js_module_items );
	}

	/**
	 * Prints all the HTML required by the Pace Builder
	 *
	 * @return void
	 */
	public function stage_html() {
		wp_nonce_field( 'save', 'pt-pb-nonce' );
		?>
		<input type="hidden" id="pt_is_pb" name="pt_is_pb" value="0"/>
		<input type="hidden" id="ptpb_data" name="ptpb_data" value=""/>
		<input type="hidden" id="ptpb_options" name="ptpb_options" value=""/>

		<div id="pt-pb-main-wrap"></div>

		<div id="pt-pb-editor-hidden">
			<?php
			wp_editor( '', 'ptpb_editor', array(
				'tinymce'       => array(
					'wp_autoresize_on' => false,
					'resize'           => false
				),
				'editor_height' => 300
			) );
			?>
		</div>

		<script type="text/template" id="pt-pb-tmpl-layout">
			<div id="ptpb_loader" class="pt-pb-loader">
				<div class="pt-pb-spinner"></div>
			</div>

			<div id="ptpb_actions"></div>

			<?php
			/*
			* Action hook to add custom html before PaceBuilder Stage
			*/
			do_action( 'ptpb_before_stage' );
			?>

			<div id="ptpb_stage">
				<div id="pt-pb-main-container">
				</div>
				<div class="pt-pb-add-section">
					<a href="#" class="pt-pb-insert-section button button-primary button-large"><i
							class="fa fa-cubes"></i> <?php _e( 'Add Section', 'pace-builder' ); ?></a>
				</div>
			</div>

			<?php
			/*
			* Action hook to add custom html after PaceBuilder Stage
			*/
			do_action( 'ptpb_after_stage' );
			?>

		</script>

		<script type="text/template" id="modals-template">
			<div id="ptpb_edit_dialog" class="pt-pb-dialog"></div>
			<div id="ptpb_columns_dialog" class="pt-pb-dialog"></div>
			<div id="ptpb_modules_dialog" class="pt-pb-dialog"></div>
			<div id="ptpb_layouts_dialog" class="pt-pb-dialog"></div>
			<div id="ptpb_icons_dialog" class="pt-pb-dialog"></div>
		</script>

	<?php

	}

	/**
	 * Prints all the backbone templates required by the Pace Builder
	 *
	 * @return void
	 */
	public function js_templates() {

		/*
		* Action hook to add custom templates
		*/
		do_action( 'ptpb_before_templates' );

		/* Common Templates */
		$this->load_pb_template( 'common' );

		/* Section Templates */
		PTPB_Section::instance()->js_templates();

		/* Row Templates */
		PTPB_Row::instance()->js_templates();

		/* Column Templates */
		PTPB_Column::instance()->js_templates();

		/* Modules Templates */
		foreach ( PTPB()->modules() as $key => $module_class ) {
			$instance = ptpb_get_module_instance( $key );
			if ( $instance ) {
				$instance->js_templates();
			}
		}

		/*
		* Action hook to add custom templates
		*/
		do_action( 'ptpb_after_templates' );
	}

	/**
	 * Prints a page builder template
	 *
	 * @param $name
	 */
	private function load_pb_template( $name ) {

		ob_start(); // turn on output buffering
		include( $this->pb_dir . "/templates/$name.php" );
		$template = ob_get_clean(); // get the contents of the output buffer

		/*
		* Filter to overwrite any template
		*/
		echo apply_filters( "ptpb_load_template_$name", $template );
	}

	/**
	 * Display a widget form with the provided data
	 */
	public function widget_form() {
		if ( empty( $_REQUEST['widget'] ) ) {
			wp_die();
		}
		if ( empty( $_REQUEST['_ptpb_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_ptpb_nonce'], 'ptpb_action' ) ) {
			wp_die();
		}

		$request = array_map( 'stripslashes_deep', $_REQUEST );

		$widget = $request['widget'];

		$instance = ! empty( $request['instance'] ) ? json_decode( $request['instance'], true ) : array();

		$form = $this->render_widget_form( $widget, $instance, $_REQUEST['update'] == 'true' );
		$form = apply_filters( 'ptpb_ajax_widget_form', $form, $widget, $instance );

		echo $form;
		wp_die();
	}

	/**
	 * Render a widget form with all the Page Builder specific fields
	 *
	 * @param string $widget The class of the widget
	 * @param array $instance Widget values
	 *
	 * @return mixed|string The form
	 */
	private function render_widget_form( $widget, $instance = array() ) {
		global $wp_widget_factory;

		// This is a chance for plugins to replace missing widgets
		$the_widget = ! empty( $wp_widget_factory->widgets[ $widget ] ) ? $wp_widget_factory->widgets[ $widget ] : false;
		$the_widget = apply_filters( 'ptpb_widget_object', $the_widget, $widget );

		if ( empty( $the_widget ) || ! is_a( $the_widget, 'WP_Widget' ) ) {

		}

		ob_start();
		$return = $the_widget->form( $instance );
		do_action_ref_array( 'in_widget_form', array( &$the_widget, &$return, $instance ) );
		$form = ob_get_clean();

		// rename the widget form inputs name
		$form = preg_replace( '/name="[^\[]+\[[0-9]+\]\[([^\]]+)\]([^"]+)?"/', 'name="instance[$1]$2"', $form );

		return apply_filters( 'ptpb_widget_form', $form, $widget, $instance );
	}

	/**
	 * Ajax handler to import a layout
	 */
	public function import_layout() {
		if ( empty( $_REQUEST['_ptpb_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_ptpb_nonce'], 'ptpb_action' ) ) {
			wp_die();
		}

		if ( ! empty( $_FILES['ptpb_import_data']['tmp_name'] ) ) {
			header( 'content-type:application/json' );
			$json = file_get_contents( $_FILES['ptpb_import_data']['tmp_name'] );
			@unlink( $_FILES['ptpb_import_data']['tmp_name'] );
			echo $json;
		}
		wp_die();
	}

	/**
	 * Ajax handler to export a layout
	 */
	public function export_layout() {
		if ( empty( $_REQUEST['_ptpb_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_ptpb_nonce'], 'ptpb_action' ) ) {
			wp_die();
		}

		header( 'content-type: application/json' );
		header( 'Content-Disposition: attachment; filename=layout-' . date( 'dmY-his' ) . '.json' );

		$export_data = wp_unslash( $_POST['ptpb_export_data'] );
		echo $export_data;

		wp_die();
	}

	/**
	 * Ajax handler to get layouts or a single layout
	 */
	public function get_layout() {
		if ( empty( $_REQUEST['_ptpb_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_ptpb_nonce'], 'ptpb_action' ) ) {
			wp_die();
		}

		$layout_name = empty( $_REQUEST['layout_name'] ) ? false : $_REQUEST['layout_name'];
		$layouts     = $this->get_saved_layouts();

		if ( $layout_name && array_key_exists( $layout_name, $layouts ) ) {
			echo json_encode( array( 'layout' => $layouts[ $layout_name ] ) );
		} else {
			echo json_encode( array( 'layouts' => $layouts ) );
		}

		wp_die();

	}

	/**
	 * @return mixed|void
	 */
	private function get_saved_layouts() {
		return get_option( 'ptpb_layouts', array() );
	}

	/**
	 * Ajax handler to save a layout
	 */
	public function save_layout() {
		if ( empty( $_REQUEST['_ptpb_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_ptpb_nonce'], 'ptpb_action' ) ) {
			wp_die();
		}

		$layout_name = $_REQUEST['layout_name'];
		$layout      = $_REQUEST['layout'];
		$layouts     = $this->get_saved_layouts();

		if ( array_key_exists( $layout_name, $layouts ) ) {
			echo json_encode( array(
				'success' => false,
				'message' => __( sprintf( 'A layout with the name "%s" already exists in the Database, please choose a different name or delete the existing layout from Load From DB tab.', $layout_name ), 'pace-builder' )
			) );
			wp_die();
		}

		$layouts[ $layout_name ] = wp_unslash( $layout );

		update_option( 'ptpb_layouts', $layouts );
		echo json_encode( array(
			'success' => true,
			'message' => __( 'Layout saved to the database successfully.', 'pace-builder' )
		) );
		wp_die();

	}

	/**
	 * Ajax handler to delete a layout
	 */
	public function delete_layout() {
		if ( empty( $_REQUEST['_ptpb_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_ptpb_nonce'], 'ptpb_action' ) || empty( $_REQUEST['layout_name'] ) ) {
			wp_die();
		}

		$layout_name = $_REQUEST['layout_name'];
		$layouts     = $this->get_saved_layouts();

		if ( array_key_exists( $layout_name, $layouts ) ) {
			unset( $layouts[ $layout_name ] );
			update_option( 'ptpb_layouts', $layouts );
			echo json_encode( array(
				'success' => true,
				'message' => __( 'Layout deleted from Database.', 'pace-builder' )
			) );
			wp_die();
		}
		wp_die();
	}

	/**
	 * Ajax handler to get layouts or a single layout
	 */
	public function get_prebuilt_layouts() {
		if ( empty( $_REQUEST['_ptpb_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_ptpb_nonce'], 'ptpb_action' ) ) {
			wp_die();
		}
		
	    $response = get_transient( 'ptpb_prebuilt_layouts' );
	  
	    if( empty( $response ) ) {
		    $response = wp_remote_get( 'http://demo.pacethemes.com/pace-builder/wp-admin/admin-ajax.php?action=pb_get_layouts' );
	    }

		header( 'content-type: application/json' );

		if ( is_array( $response ) && $response['response']['code'] === 200 ) {
			set_transient( 'ptpb_prebuilt_layouts', $response, HOUR_IN_SECONDS );
			echo json_encode( json_decode( $response['body'], true ) );
		} else {
			echo json_encode( array( 'error' => $response['response'] ) );
		}

		wp_die();

	}

	public function pagebuilder_tour() {
		// to do
		// implement pace builder tour
	}

	public function tinymce_buttons() {
		add_filter( 'mce_external_plugins', array( $this, 'tinymce_add_buttons' ) );
    	add_filter( 'mce_buttons', array( $this, 'tinymce_register_buttons' ) );

    	// Qneue Icon Fonts for TinyMCE editor
    	foreach( PTPB_Icons::instance()->icons() as $name => $font ) {
			if ( ! empty( $font['icons'] ) && is_array( $font['icons'] ) && $font['css_path'] ) {
				add_editor_style( $font['css_path'] );
			}
		}

	}

	public function tinymce_add_buttons( $plugin_array ) {
	    $plugin_array['ptpb'] = PTPB()->plugin_url() . '/assets/js/builder/tinymce-plugin.js';
	    return $plugin_array;
	}

	public function tinymce_register_buttons( $buttons ) {
	    array_push( $buttons, 'icon' );
	    return $buttons;
	}

}
