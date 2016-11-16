<?php

/**
 * Button Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes/modules
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_Button' ) ) :
	/**
	 * Class to handle HTML generation for Image Module
	 *
	 */
	class PTPB_Module_Button extends PTPB_Module {

		/**
		 * PTPB_Module_Button Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'fa fa-hand-o-up';
			$this->label       = __( 'Button', 'pace-builder' );
			$this->description = __( 'Simple yet customizable button', 'pace-builder' );
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'href'            => array(
					'type'    => 'text',
					'default' => '#',
					'label'   => __( 'Link', 'pace-builder' ),
					'desc'    => __( 'Link the button should point to.', 'pace-builder' ),
				),
				'text'            => array(
					'type'  => 'text',
					'label' => __( 'Text', 'pace-builder' ),
					'desc'  => __( 'Text for the button', 'pace-builder' )
				),
				'btn_icon'            => array(
					'type'  => 'icon',
					'label' => 'Button Icon'
				),
				'align'           => array(
					'type'    => 'select',
					'default' => 'left',
					'label'   => __( 'Alignment', 'pace-builder' ),
					'options' => array(
						'left'   => __( 'Left', 'pace-builder' ),
						'center' => __( 'Center', 'pace-builder' ),
						'right'  => __( 'Right', 'pace-builder' )
					)
				),
				'target'          => array(
					'type'    => 'select',
					'default' => '_blank',
					'label'   => __( 'URL should open in New Tab ?', 'pace-builder' ),
					'desc'    => __( 'Should the URL open in a new tab or the same tab ?', 'pace-builder' ),
					'options' => array(
						'_blank' => __( 'Yes', 'pace-builder' ),
						'_self'  => __( 'No', 'pace-builder' )
					)
				),
				'size'            => array(
					'type'    => 'select',
					'default' => 'normal',
					'label'   => __( 'Size', 'pace-builder' ),
					'options' => array(
						'normal' => __( 'Normal', 'pace-builder' ),
						'small'  => __( 'Small', 'pace-builder' ),
						'big'    => __( 'Big', 'pace-builder' ),
					)
				),
				'rounded'         => array(
					'type'    => 'select',
					'default' => 'no',
					'label'   => __( 'Button should have Rounded edges ?', 'pace-builder' ),
					'options' => $this->yes_no_option
				),
				'style'           => array(
					'type'    => 'select',
					'default' => 'normal',
					'label'   => __( 'Style', 'pace-builder' ),
					'desc'    => __( 'Normal will let you choose the button colors, If Light or Dark is selected, then respective colors will be applied.', 'pace-builder' ),
					'options' => array(
						'normal' => __( 'Normal', 'pace-builder' ),
						'light'  => __( 'Light', 'pace-builder' ),
						'dark'   => __( 'Dark', 'pace-builder' ),
					)
				),
				'color'           => array(
					'type'    => 'color',
					'default' => '#27ae60',
					'label'   => __( 'Background Color', 'pace-builder' ),
					'dependency' => 'style',
					'condition'  => "style == 'normal'"
				),
				'txt_color'       => array(
					'type'    => 'color',
					'default' => '#fff',
					'label'   => __( 'Text Color', 'pace-builder' ),
					'dependency' => 'style',
					'condition'  => "style == 'normal'"
				),
				'hover_color'     => array(
					'type'    => 'color',
					'default' => '#229955',
					'label'   => __( 'Background Color on Hover', 'pace-builder' ),
					'dependency' => 'style',
					'condition'  => "style == 'normal'"
				),
				'hover_txt_color' => array(
					'type'    => 'color',
					'default' => '#fff',
					'label'   => __( 'Text Color on Hover', 'pace-builder' ),
					'dependency' => 'style',
					'condition'  => "style == 'normal'"
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
			<a class="button" href="{{{data.href}}}" target="{{{data.target}}}">{{{data.text}}}</a></div>
			<?php
		}

		/**
		 * Generate module content
		 * @param $module
		 *
		 * @return string
		 */
		public function get_content( $module ) { 
			$classes = array(
				$module['size'] !== 'normal' ? $module['size'] : false,
				$module['rounded'] === 'yes' ? 'rounded' : false,
				$module['style'] !== 'normal' ? $module['style'] : false,
			);

			return sprintf( '<div style="text-align: %1$s;"><a href="%2$s" class="pt-pb-button %3$s" target="%4$s">%5$s %6$s</a></div>',
				esc_html( $module['align'] ),
				esc_url( $module['href'] ),
				esc_attr( implode( ' ', array_filter( $classes ) ) ),
				esc_attr( $module['target'] ),
				empty( $module['btn_icon'] ) ? '' : "<i class='{$module['btn_icon']}'></i> ",
				esc_html( $module['text'] )
			);
		}

		/**
		 * Generate module CSS
		 * @param $module
		 *
		 * @return string|void
		 */
		public function get_css( $module ) {
			if ( $module['style'] !== 'normal' ) {
				return;
			}

			return sprintf( '
                        #pt-pb-content #%1$s a.pt-pb-button { color : %2$s; background-color: %3$s; }
						#pt-pb-content #%1$s a.pt-pb-button:hover { color : %4$s; background-color: %5$s; }
                        ',
				$module['id'],
				$module['txt_color'],
				$module['color'],
				$module['hover_txt_color'],
				$module['hover_color']
			);
		}

	}
endif;
