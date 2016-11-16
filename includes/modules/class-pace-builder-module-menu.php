<?php

/**
 * Menu Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_Menu' ) ) :
	/**
	 * Class to handle HTML generation for Menu Module
	 *
	 */
	class PTPB_Module_Menu extends PTPB_Module {

		/**
		 * PTPB_Module_Menu Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'dashicons-menu';
			$this->label       = __( 'Menu', 'pace-builder' );
			$this->description = __( 'A registered WordPress Menu', 'pace-builder' );
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'menu'       => array(
					'type'  => 'menu',
					'label' => __( 'Select Menu', 'pace-builder' )
				),
				'align'      => array(
					'type'    => 'select',
					'default' => 'right',
					'label'   => __( 'Alignment', 'pace-builder' ),
					'options' => array(
						'right'  => __( 'Right', 'pace-builder' ),
						'left'   => __( 'Left', 'pace-builder' ),
						'center' => __( 'Center', 'pace-builder' )
					)
				),
				'mi_txt'     => array(
					'type'    => 'color',
					'default' => '#666',
					'label'   => __( 'Menu Item Text Color', 'pace-builder' )
				),
				'mi_hvr'     => array(
					'type'    => 'color',
					'default' => '#E74C3C',
					'label'   => __( 'Menu Item Text Hover Color', 'pace-builder' )
				),
				'sm_bg'      => array(
					'type'    => 'color',
					'default' => '#fff',
					'label'   => __( 'Sub Menu Background Color', 'pace-builder' )
				),
				'sm_br'      => array(
					'type'    => 'color',
					'default' => '#e5e5e5',
					'label'   => __( 'Sub Menu Border Color', 'pace-builder' )
				),
				'smi_txt'    => array(
					'type'    => 'color',
					'default' => '#555',
					'label'   => __( 'Sub Menu Item Text Color', 'pace-builder' )
				),
				'smi_hvr'    => array(
					'type'    => 'color',
					'default' => '#E74C3C',
					'label'   => __( 'Sub Menu Item Text Hover Color', 'pace-builder' )
				),
				'smi_hvr_bg' => array(
					'type'    => 'color',
					'default' => '#ecf0f1',
					'label'   => __( 'Sub Menu Item Text Hover Background Color', 'pace-builder' )
				),
				'mm_top'    => array(
					'type'    => 'slider',
					'default' => '0px',
					'label'   => __( 'Mobile Menu Toggle Button Top offset', 'pace-builder' ),
					'desc'   => __( 'Set the vertical offset of the mobile menu toggle button, negative value moves the toggle button up and positive value moves it down.', 'pace-builder' ),
					'max'     => 120,
					'min'     => -120,
					'step'    => 1,
					'unit'    => 'px'
				),
				'mm_align'      => array(
					'type'    => 'select',
					'default' => 'right',
					'label'   => __( 'Mobile Menu Toggle Alignment', 'pace-builder' ),
					'options' => array(
						'right'  => __( 'Right', 'pace-builder' ),
						'left'   => __( 'Left', 'pace-builder' ),
						'center' => __( 'Center', 'pace-builder' ),
					)
				),
			);
		}

		/**
		 * HTML for Module Preview in the PaceBuilder Stage area
		 * @param $module
		 *
		 * @return string
		 */
		public function preview() {
			?>
			<?php _e( 'Menu ID', 'pace-builder' ) ?>:  {{{ data.menu }}}
			<?php
		}

		/**
		 * Generate module content
		 * @param $module
		 *
		 * @return string
		 */
		public function get_content( $module ) {

			return sprintf( "<div class='pt-pb-module-menu' %s><div class='pt-pb-module-menu-mob'></div></div>",
				ptpb_generate_data_attr( 
					array( 
						'pb-process' => 'true', 
						'type' => 'menu', 
						'menu-id' => $module['menu'], 
						'align' =>  $module['align'] 
					)
				)
			);
		}

		/**
		 * Generate module CSS
		 * @param $module
		 *
		 * @return string
		 */
		public function get_css( $module ) {
			$module['mm_align'] = empty( $module['mm_align'] ) ? 'right' : $module['mm_align'];
			return sprintf( '
                        #%1$s .navbar-nav > li > a { color : %2$s; }
                        #%1$s .navbar-nav > li > a:hover, .navbar-nav > li.active > a, .navbar-nav > li.open > a, .navbar-nav > .active > a:hover, .navbar-nav > .active > a:focus, .navbar-nav > .open > a:hover { color : %3$s !important; }
                        #%1$s .dropdown .dropdown-menu > li > a, #%1$s .slicknav_menu ul.slicknav_nav > li .slicknav_row a, #%1$s .slicknav_nav .slicknav_arrow { color : %4$s; }
                        #%1$s .dropdown .dropdown-menu > li > a:hover, #%1$s .slicknav_menu ul.slicknav_nav > li .slicknav_row:hover { color : %5$s; background-color: %6$s; }
						#%1$s .slicknav_menu ul.slicknav_nav > li .slicknav_row:hover a { color : %5$s; }
                        #%1$s .dropdown .dropdown-menu { background-color: %7$s; border-color: %8$s; }
                        #%1$s .slicknav_menu .slicknav_icon-bar { background-color: %2$s; }
                        #%1$s .slicknav_btn { border-color: %2$s; margin-top: %9$s; float: %10$s; margin-left: %11$s; margin-right: %11$s; }
                        #%1$s .slicknav_menu .slicknav_btn:hover .slicknav_icon-bar { background-color: %4$s; }
                        #%1$s .slicknav_menu .slicknav_btn:hover { border-color: %7$s;  background: %7$s; }
						#%1$s .slicknav_menu ul.slicknav_nav { background: %7$s; border-color: %8$s; }
                        ',
				$module['id'],
				$module['mi_txt'],
				$module['mi_hvr'],
				$module['smi_txt'],
				$module['smi_hvr'],
				$module['smi_hvr_bg'],
				$module['sm_bg'],
				$module['sm_br'],
				empty( $module['mm_top'] ) ? 0 : $module['mm_top'],
				$module['mm_align'] === 'center' ? 'none' : $module['mm_align'],
				$module['mm_align'] === 'center' ? 'auto' : '15px'
			);
		}

		/**
		 * Filter post_content to add/insert the Menu HTML
		 * @param $content
		 * @param $col
		 *
		 * @return mixed
		 */
		public function filter_content( $content, $col ) {
			$data = ptpb_extract_data_attr( $col );
			$menu = $col . $this->render_menu( $data['menu-id'], $data['align'] );
			$menu .= $this->render_mobile_menu( $data['menu-id'] );

			return str_replace( $col, $menu, $content );
		}

		/**
		 * Render a Menu
		 *
		 * @param $menu_id
		 * @param string $align
		 *
		 * @return mixed|string The Menu
		 *
		 */
		private function render_menu( $menu_id, $align = 'left' ) {

			if ( ! $menu_id || ! is_numeric( $menu_id ) ) {
				return;
			}

			ob_start();
			wp_nav_menu( array(
				'menu'            => $menu_id,
				'menu_class'      => "nav navbar-nav navbar-$align",
				'container_class' => 'collapse navbar-collapse',
				'walker'          => new PTPB_Menu()
			) );

			return ob_get_clean();
		}

		/**
		 * Render a Mobile Menu
		 *
		 * @param $menu_id
		 * @return mixed|string The Menu
		 *
		 */
		private function render_mobile_menu( $menu_id ) {

			if ( ! $menu_id || ! is_numeric( $menu_id ) ) {
				return;
			}

			ob_start();
			wp_nav_menu( array(
				'menu'       => $menu_id,
				'menu_class' => 'ptpb-mobile-menu',
				'container'  => 'false'
			) );

			return ob_get_clean();
		}

	}
endif;