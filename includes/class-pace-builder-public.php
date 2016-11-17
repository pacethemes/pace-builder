<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */
class PTPB_Public {

	/**
	 * CSS required by the PaceBuilder Page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $css
	 */
	private $css;

	/**
	 * Fonts used in the PaceBuilder Page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $fonts_used
	 */
	private $fonts_used = array();

	/**
	 * All enqueued stylesheets in the PaceBuilder Page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $stylesheets
	 */
	private $stylesheets = array();

	/**
	 * All enqueued scripts in the PaceBuilder Page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $scripts
	 */
	private $scripts = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @internal param string $plugin_name The name of the plugin.
	 * @internal param string $version The version of this plugin.
	 */
	public function __construct() {

		$this->all_fonts		= PTPB()->fonts()->get_all_fonts();

		remove_filter( 'the_content', 'wpautop' );

		add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 100 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1000 );
		add_action( 'wp_head', array( $this, 'header_output' ) );
		add_filter( 'the_content', array( $this, 'filter_content' ) );
		add_filter( 'template_include', array( $this, 'template' ) );
	}

	/**
	 * Add PaceBuilder classes to body tag
	 * @param $classes
	 *
	 * @return array
	 */
	public function body_classes( $classes ) {
		if( ! ptpb_is_pb() ) {
			return $classes;
		}

		if ( ptpb_get_layout() === 'default' && ptpb_is_fullwidth_page() ) {
			$classes[] = 'pt-pb-fullwidth-page';
		}

		$classes[] = 'pace-builder-page';

		return $classes;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		global $wp_styles, $post;
		if ( ptpb_is_pb() ) {
			if ( function_exists( 'ptpb_is_legacy' ) && ptpb_is_legacy() ) {
				return;
			}

			$theme 			= wp_get_theme();

			foreach ( $wp_styles->registered as $name => $style ) {
				if( is_string( $style->src ) && strpos( $style->src, 'wp-admin' ) === false ) {
					$this->stylesheets[$name] = $style->src;
				}
			}

			// Build Page Builder Module Styles
			$this->build_pb_css();

			if ( ptpb_get_layout() === 'none' ) {
				// remove all stylesheets added by the theme
				foreach ( preg_grep( '#\/wp-content\/themes\/#', $this->stylesheets ) as $name => $style ) {
					wp_dequeue_style( $name );
					unset( $this->stylesheets[$name] );
				}

				// enqueue normalize stylesheet
				wp_enqueue_style( 'ptpb_normalize', PTPB()->plugin_url() . '/assets/plugins/bootstrap/css/normalize.css', array(), PTPB()->version, 'all' );

				// Backward compatibility for Quest Theme's Slit Slider
				if( strpos( $post->post_content, 'sl-slider-wrapper' ) !== false && ( $theme->get( 'Name' ) === 'Quest' || $theme->get( 'Template' ) === 'quest' ) ){
					wp_enqueue_style( 'slit-slider', get_template_directory_uri() . '/assets/plugins/FullscreenSlitSlider/css/style.css' );
				}
			}
	
			// Loop through all used fonts in this page and invoke the enqueue_callback for each font group
			foreach ( $this->fonts_used as $grp_name => $fonts ) {
				if( $grp_name === 'standard' ) {
					continue;
				}
				if( ! empty( $this->all_fonts[$grp_name] ) && ! empty( $this->all_fonts[$grp_name]['enqueue_callback'] ) && is_callable( $this->all_fonts[$grp_name]['enqueue_callback'] ) ) {
					call_user_func( $this->all_fonts[$grp_name]['enqueue_callback'], $fonts );
				}
			}

			// Enqueue all registered icon fonts stylesheets
			foreach( PTPB_Icons::instance()->icons() as $name => $font ) {
				if ( ! empty( $font['icons'] ) && is_array( $font['icons'] ) ) {
					if ( ( ! empty( $font['always_enqueue'] ) && $font['always_enqueue'] ) || empty( $font['class_check'] ) ) {
						$this->enqueue_icon_font( $name, $font['css_path'], $font['check_if_enqueued'] );
					} else { 
						$cls = empty( $font['class_check'] ) ? '' : $font['class_check'];
						$regex  = '/class[ \t]*=[ \t]*".*?' . $cls . '([^"]+)?"/';
						$regex2 = str_replace( '"', "'", $regex );
						if ( preg_match( $regex , $post->post_content ) || preg_match( $regex2 , $post->post_content ) ) {
							$this->enqueue_icon_font( $name, $font['css_path'], $font['check_if_enqueued'] );
						}
					}
				}
			}

			if( ptpb_get_layout() === 'none' || ( $theme->get( 'Name' ) !== 'Quest' && $theme->get( 'Template' ) !== 'quest' ) ) {
				$this->enqueue_style_if_not( 'ptpb_bootstrap', PTPB()->plugin_url() . '/assets/plugins/bootstrap/css/bootstrap.min.css', $this->stylesheets );
				$this->enqueue_style_if_not( 'ptpb_fa', PTPB()->plugin_url() . '/assets/plugins/font-awesome/css/font-awesome.min.css', $this->stylesheets );
				$this->enqueue_style_if_not( 'ptpb_animate', PTPB()->plugin_url() . '/assets/plugins/animate/animate.css', $this->stylesheets );
				$this->enqueue_style_if_not( 'ptpb_colorbox', PTPB()->plugin_url() . '/assets/plugins/colorbox/colorbox.css', $this->stylesheets );
			}

			$this->enqueue_style_if_not( 'ptpb_flexslider', PTPB()->plugin_url() . '/assets/plugins/flexslider/flexslider.css', $this->stylesheets );
			wp_enqueue_style( 'ptpb_builder', PTPB()->plugin_url() . '/assets/css/pacebuilder.css', array(), PTPB()->version, 'all' );
		}

	}

	/**
	 * Enqueue stylesheet only if it is not enqueue already
	 * Checks if any stylesheet with same name is already enqueued
	 *
	 * @since    1.1.0
	 */
	private function enqueue_style_if_not( $name, $path, $enqueued = array() ) {
		$file =  preg_replace( '/((\.|-)min)/' , '', basename( $path, '.css' ) );
		if( count( preg_grep( "#$file((\.|-)min)?.css#", $enqueued ) ) === 0 ) {
			wp_enqueue_style( $name, $path, array(), PTPB()->version, 'all' );
		}
	}

	/**
	 * Enqueue stylesheet for icon font
	 *
	 * @since    1.1.0
	 */
	private function enqueue_icon_font( $name, $path = false, $check_if_enqueued = false, $enqueued = false ){
		$name = strtolower( str_replace( ' ', '-', $name ) );

		if( $enqueued === false ) {
			$enqueued = $this->stylesheets;
		}

		if( $path && $check_if_enqueued ) {
			$this->enqueue_style_if_not( "ptpb_$name" , $path, $enqueued );
		} else if ( $path ) {
			wp_enqueue_style( "ptpb_$name" , $path, array(), PTPB()->version, 'all' );
		} else {
			wp_enqueue_style( $name );
		}
	}

	/**
	 * Build CSS required by PaceBuilder Modules
	 *
	 * @return void
	 */
	private function build_pb_css() {

		$page_options = ptpb_get_page_options();
		$sections     = ptpb_get_data();

		$page_options['id'] = 'pt-pb-content';

		$css = $this->build_typography_css( $page_options );

		foreach ( $sections as $key => $section ) {

			$css .= $this->build_typography_css( $section );

			if ( ptpb_isset_and_array( $section['rows'] ) ) {

				foreach ( $section['rows'] as $j => $row ) {
					$css .= $this->build_typography_css( $row );

					if ( ptpb_isset_and_array( $row['columns'] ) ) {

						foreach ( $row['columns'] as $k => $col ) {
							$css .= $this->build_typography_css( $col );

							if ( ptpb_isset_and_array( $col['modules'] ) ) {

								foreach ( $col['modules'] as $l => $module ) { 
									$css .= $this->build_typography_css( $module );
									$instance   = ptpb_get_module_instance( $module );
									$module_css = '';
									if ( $instance && method_exists( $instance, 'get_css' ) ) {
										$module_css = $instance->get_css( $module );
									}
									$css .= apply_filters( "ptpb_css_module_{$module['type']}", $module_css, $module );

									// Enqueue scripts required by the module
									if( $instance && property_exists( $instance, 'scripts' ) && is_array( $instance->scripts ) ) {
										foreach ( $instance->scripts as $handle => $src ) {
											wp_enqueue_script( $handle, $src, array(), PTPB()->version );
										}
									}
								}
							}
						}
					}
				}
			}
		}

		$this->css = $css;

	}

	/**
	 * Iterate through all the sections, rows, columns and modules to generate typography CSS
	 * @param $item
	 *
	 * @return string
	 */
	private function build_typography_css( $item ) { 

		if ( ! isset( $item['f_e'] ) || ! $item['f_e'] ) {
			return '';
		}

		$heading_props = array(
			'fh_f'  => 'font-family',
			'fh_v'  => 'font-variant',
			'fh_s'  => 'font-size',
			'fh_lh' => 'line-height',
			'fh_ls' => 'letter-spacing',
			'fh_ws' => 'word-spacing',
			'fh_b'  => 'font-weight',
			'fh_i'  => 'font-style',
			'fh_u'  => 'text-decoration'
		);

		$text_props = array(
			'ft_f'  => 'font-family',
			'ft_v'  => 'font-variant',
			'ft_s'  => 'font-size',
			'ft_lh' => 'line-height',
			'ft_ls' => 'letter-spacing',
			'ft_ws' => 'word-spacing',
			'ft_b'  => 'font-weight',
			'ft_i'  => 'font-style',
			'ft_u'  => 'text-decoration'
		);

		if ( ! empty( $item['fh_f'] ) ) { 
			$this->add_font_used( $item['fh_f'], $item['fh_v'] );
		}

		if ( ! empty( $item['ft_f'] ) ) {
			$this->add_font_used( $item['ft_f'], $item['ft_v'] );
		}

		$css = sprintf( '#%1$s, #%1$s p{ %2$s color: %3$s; }' . "\n", $item['id'], $this->typography_css( $item, $text_props ), $item['ft_c'] );
		$css .= sprintf( '#%1$s h1, #%1$s h2, #%1$s h3, #%1$s h4, #%1$s h5, #%1$s h6 { %2$s color: %3$s; }' . "\n", $item['id'], $this->typography_css( $item, $heading_props ), empty( $item['fh_c'] ) ? 'inherit' : $item['fh_c'] );

		if( isset( $item['fh_s'] ) ) {
			$css .= $this->heading_typography( $item['id'], intval( $item['fh_s'] ) );
		}

		if( isset( $item['fh_st'] ) ) {
			$size = explode( ';', $item['f_tss'] );
			$css .= sprintf( "@media (min-width: %spx) and (max-width: %spx) { %s }", 
								intval( empty( $size[0] ) ? 768 : $size[0] ),
								intval( empty( $size[1] ) ? 991 : $size[1] ),
								$this->heading_typography( $item['id'], intval( $item['fh_st'] ) 
							) );
		}

		if( isset( $item['ft_st'] ) ) {
			$size = explode( ';', $item['f_tss'] );
			$css .= sprintf( '@media (min-width: %1$spx) and (max-width: %2$spx) { #%3$s, #%3$s p { font-size: %4$spx; } }', 
								intval( empty( $size[0] ) ? 768 : $size[0] ),
								intval( empty( $size[1] ) ? 991 : $size[1] ),
								$item['id'], 
								intval( $item['ft_st'] 
							) );
		}

		if( isset( $item['fh_sm'] ) ) {
			$size = intval( $item['f_mss'] );
			$css .= sprintf( "@media (max-width: %spx) { %s }", 
								empty( $size ) ? 767 : $size,
								$this->heading_typography( $item['id'], intval( $item['fh_sm'] ) 
							) );
		}

		if( isset( $item['ft_sm'] ) ) {
			$size = intval( $item['f_mss'] );
			$css .= sprintf( '@media (max-width: %1$spx) { #%2$s, #%2$s p { font-size: %3$spx; } }', 
								empty( $size ) ? 767 : $size,
								$item['id'], 
								intval( $item['ft_sm'] 
							) );
		}

		return $css;

	}

	/**
	 * Build Typography CSS for a specific item (section, row, column or module)
	 * @param $item
	 * @param $props
	 *
	 * @return string
	 */
	private function typography_css( $item, $props ) {
		$css = '';
		foreach ( $props as $key => $prop ) {

			if ( isset( $item[ $key ] ) && trim( $item[ $key ] ) !== '' && $item[ $key ] != '0' ) {

				switch ( $prop ) {

					case 'font-family':
						$css .= sprintf( "%s : '%s'; ", $prop, implode( "', '", explode( ',', $item[ $key ] ) ) );
						break;

					case 'font-variant':
						$variant = $item[ $key ];
						if ( strpos( $variant, 'italic' ) !== false ) {
							$css .= 'font-style : italic; ';
						} else {
							$css .= 'font-style : normal; ';
						}

						if ( is_numeric( preg_replace( '/[^0-9,.]/', '', $variant ) ) ) {
							$css .= 'font-weight : ' . preg_replace( '/[^0-9,.]/', '', $variant ) . '; ';
						} else {
							$css .= 'font-weight : normal; ';
						}
						break;

					case 'font-weight':
						$css .= 'font-weight : bold; ';
						break;

					case 'font-style':
						$css .= 'font-style : italic; ';
						break;

					case 'text-decoration':
						$css .= 'text-decoration : underline; ';
						break;

					default:
						$css .= sprintf( '%s : %s; ', $prop, $item[ $key ] );
						break;
				}
			}
		}

		return $css;
	}

	/**
	 * Add font to $used_fonts array
	 *
	 * @since    1.0.0
	 */
	private function add_font_used( $name, $variant ) {
		foreach ( $this->all_fonts as $grp_name => $fonts ) {
			if( isset( $fonts['fonts'][ $name ] ) ) {
				if( ! isset( $this->fonts_used[ $grp_name ] ) ) {
					$this->fonts_used[ $grp_name ] = array();
				}
				if( ! isset( $this->fonts_used[ $grp_name ][ $name ] ) ) {
					$this->fonts_used[ $grp_name ][ $name ] = array();
				}
				$this->fonts_used[ $grp_name ][ $name ][ $variant ] = true;
			}
		}
	}

	private function heading_typography( $id, $size ){
		return sprintf( '#%1$s h1{ font-size: %2$spx; } #%1$s h2{ font-size: %3$spx; } #%1$s h3{ font-size: %4$spx; } #%1$s h4{ font-size: %5$spx; } #%1$s h5{ font-size: %6$spx; } #%1$s h6{ font-size: %7$spx; }' . "\n", 
					$id, 
					floor( $size ),
					floor( $size * 0.833 ),
					floor( $size * 0.833 * 0.833 ),
					floor( $size * 0.833 * 0.833 * 0.833 ),
					floor( $size * 0.833 * 0.833 * 0.833 * 0.833 ),
					floor( $size * 0.833 * 0.833 * 0.833 * 0.833 * 0.833 )
				);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $post, $wp_scripts;
		if ( ptpb_is_pb() ) {

			if ( function_exists( 'ptpb_is_legacy' ) && ptpb_is_legacy() ) {
				return;
			}

			foreach ( $wp_scripts->registered as $name => $script ) {
				if( is_string( $script->src ) && strpos( $script->src, 'wp-admin' ) === false ) {
					$this->scripts[$name] = $script->src;
				}
			}

			if ( ptpb_get_layout() === 'none' ) {
				// remove all scripts added by the theme
				foreach ( preg_grep( '#\/wp-content\/themes\/#', $this->scripts ) as $name => $script ) {
					wp_dequeue_script( $name );
					unset( $this->scripts[$name] );
				}
			}

			wp_enqueue_script( 'jquery-masonry', array( 'jquery' ) );

			if( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				wp_enqueue_script( 'ptpb_bootstrap', PTPB()->plugin_url() . '/assets/plugins/bootstrap/js/bootstrap.min.js', array( 'jquery' ), PTPB()->version, true );
				wp_enqueue_script( 'ptpb_wow', PTPB()->plugin_url() . '/assets/plugins/wow/wow.min.js', array( 'jquery' ), PTPB()->version, true );
				wp_enqueue_script( 'ptpb_parallax', PTPB()->plugin_url() . '/assets/plugins/parallax/jquery.parallax.js', array( 'jquery' ), PTPB()->version, true );
				wp_enqueue_script( 'ptpb_slicknav', PTPB()->plugin_url() . '/assets/plugins/slicknav/jquery.slicknav.min.js', array( 'jquery' ), PTPB()->version, true );
				wp_enqueue_script( 'ptpb_colorbox', PTPB()->plugin_url() . '/assets/plugins/colorbox/jquery.colorbox-min.js', array( 'jquery' ), PTPB()->version, true );
				wp_enqueue_script( 'ptpb_flexslider', PTPB()->plugin_url() . '/assets/plugins/flexslider/jquery.flexslider-min.js', array( 'jquery' ), PTPB()->version, true );
				wp_enqueue_script( 'ptpb_builder', PTPB()->plugin_url() . '/assets/js/public.js', array( 'jquery' ), PTPB()->version, true );
			} else {
				wp_enqueue_script( 'ptpb_builder_all', PTPB()->plugin_url() . '/assets/js/public-all.min.js', array( 'jquery' ), PTPB()->version, true );
			}
		}

	}

	/**
	 * Hook into template_loader and load PaceBuilder template if the template is set to 'none' in PaceBuilder page options
	 * @param $template
	 *
	 * @return string
	 */
	public function template( $template ) {
		if ( ptpb_is_pb() && ptpb_get_layout() !== 'default' && preg_match( '/(page|single|page-builder).php/', $template ) ) {
			return ptpb_locate_template( 'page.php' );
		}

		return $template;
	}

	/**
	 * Add extra markup to head tag
	 *
	 * @return void
	 */	
	public function header_output() {
		?>
		<!--Pace Builder CSS-->
		<style type="text/css">
			<?php $this->print_pb_css(); ?>
		</style>
		<!--End Pace Builder CSS-->
	<?php
	}

	/**
	 * Print CSS required by the PaceBuilder page
	 *
	 * @return void
	 */	
	public function print_pb_css() {
		global $post;

		if ( ! ptpb_is_pb() ) {
			return;
		}

		do_action( 'ptpb_header_css' );

		echo $this->css;

	}

	/**
	 * hook into 'filter_content' and generate any Module content if the Module has dynamic content
	 *
	 * @param string $content
	 *
	 * @return void
	 */
	public function filter_content( $content ) {
		global $post;
		if ( ! ptpb_is_pb() ) {
			return $content;
		}
		$cnt = preg_match_all( "/<[^<]+data-pb-process='true'[^>]+>/", $content, $matches );

		if ( $cnt === false || $cnt < 1 ) {
			return $content;
		}

		foreach ( $matches[0] as $key => $col ) {
			$module   = $this->get_module_type( $col );
			$instance = ptpb_get_module_instance( $module );

			if ( $instance && method_exists( $instance , 'filter_content' ) ) {
				$content = $instance->filter_content( $content, $col );
			}
		}

		return $content;

	}

	/**
	 * Get the type/slug of the PaceBuilder Module
	 *
	 * @param string $html
	 *
	 * @return string
	 */
	private function get_module_type( $html ) {
		$cnt = preg_match( "/data-type='([^']+)'/", $html, $match );

		if ( $cnt === false || $cnt < 1 || ! array_key_exists( 1, $match ) ) {
			return '';
		}

		return $match[1];

	}

}

return new PTPB_Public();
