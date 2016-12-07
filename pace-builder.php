<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://pacethemes.com
 * @since             1.0.0
 * @package           PTPB
 *
 * @wordpress-plugin
 * Plugin Name:       Page Builder by PaceThemes
 * Plugin URI:        http://pacethemes.com/pace-builder
 * Description:       Drag and Drop Page Builder, build anything you need.
 * Version:           1.1.6
 * Author:            Pace Themes
 * Author URI:        http://pacethemes.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pace-builder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'PTPB' ) ) :

	/**
	 * Main PaceBuilder Class.
	 *
	 * @class PTPB
	 * @version    1.0.0
	 */
	final class PTPB {
		/**
		 * PaceBuilder version.
		 *
		 * @var string
		 */
		public $version = '1.1.6';

		/**
		 * The single instance of the class.
		 *
		 * @var PTPB
		 * @since 1.0.0
		 * @static
		 */
		private static $_instance = null;

		/**
		 * All PaceBuilder Modules.
		 *
		 * @var array
		 * @static
		 */
		private static $modules;

		/**
		 * Main PaceBuilder Instance.
		 *
		 * Ensures only one instance of PaceBuilder is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @see PTPB()
		 * @return PaceBuilder - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pace-builder' ), '1.0.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pace-builder' ), '1.0.0' );
		}

		/**
		 * PaceBuilder Constructor.
		 */
		public function __construct() {

			self::$modules = array();

			$this->define_constants();
			$this->includes();
			$this->init_hooks();

			do_action( 'ptpb_loaded' );
		}

		/**
		 * Hook into actions and filters.
		 * @since  1.0.0
		 */
		private function init_hooks() {
			register_activation_hook( __FILE__, array( 'PTPB_Install', 'activate' ) );
			register_deactivation_hook( __FILE__, array( 'PTPB_Install', 'deactivate' ) );

			add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
			add_action( 'after_setup_theme', array( $this, 'include_functions' ) );
			add_action( 'after_setup_theme', array( $this, 'register_modules' ), 100 );
			add_action( 'init', array( $this, 'init' ), 0 );
		}

		/**
		 * Define PaceBuilder Constants.
		 */
		private function define_constants() {
			$this->define( 'PTPB_VERSION', $this->version );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 * string $type ajax, frontend or admin.
		 *
		 * @param $type
		 *
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin() || ( defined( 'WP_CLI' ) && WP_CLI );
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {

			include_once( 'includes/class-pace-builder-install.php' );
			include_once( 'includes/class-pace-builder-singleton.php' );
			include_once( 'includes/class-pace-builder-settings.php' );

			include_once( 'includes/class-pace-builder-i18n.php' );
			include_once( 'includes/class-pace-builder-fonts.php' );
			include_once( 'includes/class-pace-builder-icons.php' );

			include_once( 'includes/class-pace-builder-section.php' );
			include_once( 'includes/class-pace-builder-row.php' );
			include_once( 'includes/class-pace-builder-column.php' );					

			/* PaceBuilder Modules */
			include_once( 'includes/modules/class-pace-builder-module.php' );
			include_once( 'includes/modules/class-pace-builder-module-blog.php' );
			include_once( 'includes/modules/class-pace-builder-module-blog-carousel.php' );
			include_once( 'includes/modules/class-pace-builder-module-button.php' );
			include_once( 'includes/modules/class-pace-builder-module-contactform7.php' );
			include_once( 'includes/modules/class-pace-builder-module-empty-space.php' );
			include_once( 'includes/modules/class-pace-builder-module-featurebox.php' );
			include_once( 'includes/modules/class-pace-builder-module-gallery.php' );
			include_once( 'includes/modules/class-pace-builder-module-google-map.php' );
			include_once( 'includes/modules/class-pace-builder-module-image.php' );
			include_once( 'includes/modules/class-pace-builder-module-hovericon.php' );
			include_once( 'includes/modules/class-pace-builder-module-menu.php' );
			include_once( 'includes/modules/class-pace-builder-module-styledlist.php' );
			include_once( 'includes/modules/class-pace-builder-module-text.php' );
			include_once( 'includes/modules/class-pace-builder-module-title.php' );
			include_once( 'includes/modules/class-pace-builder-module-video.php' );
			include_once( 'includes/modules/class-pace-builder-module-widget.php' );

			if ( $this->is_request( 'admin' ) ) {
				include_once( 'includes/class-pace-builder-form.php' );
				include_once( 'includes/class-pace-builder-stage.php' );
				include_once( 'includes/class-pace-builder-save.php' );
				include_once( 'includes/class-pace-builder-revisions.php' );
			}

			if ( $this->is_request( 'admin' ) || $this->is_request( 'ajax' ) ) {
				$this->stage();
				$this->save();
			}

			if ( $this->is_request( 'frontend' ) ) {
				include_once( 'includes/class-pace-builder-public.php' );
			}
		}

		/**
		 * Include required Pace Builder functions.
		 */
		public function include_functions() {
			include_once( 'includes/pace-builder-functions.php' );
		}

		/**
		 * Init PaceBuilder when WordPress Initialises.
		 */
		public function init() {
			// Set up localisation.
			$this->load_plugin_textdomain();
		}

		/**
		 * Load Localisation files.
		 *
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'pace-builder', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
		}

		/**
		 * Setup image sizes.
		 */
		public function setup_environment() {
			$this->add_image_sizes();
		}

		/**
		 * Add PB Image sizes to WP.
		 *
		 * @since 1.0.0
		 */
		private function add_image_sizes() {
			add_image_size( 'ptpb-gallery', 420, 420, true );
			add_image_size( 'ptpb-post-big', 640, 640, true );
		}

		/**
		 * Get the plugin url.
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get the template path.
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'ptpb_template_path', 'pace-builder/' );
		}

		/**
		 * Get all registered PaceBuilder modules.
		 * @return array
		 */
		public function modules() {
			return self::$modules;
		}

		/**
		 * Register a Module in PaceBuilder.
		 * @param $module_class
		 */
		public function register_module( $module_class ) {
			if ( strpos( $module_class, 'PTPB_Module_', 0 ) !== 0 ) {
				return;
			}
			$instance = ptpb_get_module_instance( $module_class );
			if ( $instance ) {
				self::$modules[ $instance->slug() ] = $module_class;
			}
		}

		/**
		 * Register all PaceBuilder Modules included in the plugin.
		 */
		public function register_modules() {
			/**
			 * Register all modules with PaceBuilder.
			 */
			$this->register_module( 'PTPB_Module_Image' );
			$this->register_module( 'PTPB_Module_Gallery' );
			$this->register_module( 'PTPB_Module_Text' );
			$this->register_module( 'PTPB_Module_Title' );
			$this->register_module( 'PTPB_Module_HoverIcon' );
			$this->register_module( 'PTPB_Module_FeatureBox' );
			$this->register_module( 'PTPB_Module_StyledList' );
			$this->register_module( 'PTPB_Module_Button' );
			$this->register_module( 'PTPB_Module_Video' );
			$this->register_module( 'PTPB_Module_Blog' );
			$this->register_module( 'PTPB_Module_BlogCarousel' );
			$this->register_module( 'PTPB_Module_Menu' );
			$this->register_module( 'PTPB_Module_GoogleMap' );
			$this->register_module( 'PTPB_Module_EmptySpace' );
			$this->register_module( 'PTPB_Module_Widget' );

			if ( class_exists( 'WPCF7' ) ) :
				$this->register_module( 'PTPB_Module_ContactForm7' );
			endif;
		}

		/**
		 * Get Fonts Class.
		 * @return PTPB_Fonts
		 */
		public function fonts() {
			return PTPB_Fonts::instance();
		}

		/**
		 * Get Stage Class.
		 * @return PTPB_Stage
		 */
		public function stage() {
			return PTPB_Stage::instance();
		}

		/**
		 * Get Save Class.
		 * @return PTPB_Save
		 */
		public function save() {
			return PTPB_Save::instance();
		}

	}

endif;

if( ! function_exists( 'PTPB' ) ) :
/**
 * Main instance of PaceBuilder.
 *
 * @since  1.0.0
 * @return PaceBuilder
 */
function PTPB() {
	return PTPB::instance();
}
endif;

/**
 * Initialize PaceBuilder.
 *
 * @since  1.0.0
 */
PTPB();
