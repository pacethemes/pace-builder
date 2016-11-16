<?php

/**
 * HoverIcon Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_HoverIcon' ) ) :
	/**
	 * Class to handle HTML generation for Hover Icon Module
	 *
	 */
	class PTPB_Module_HoverIcon extends PTPB_Module {

		/**
		 * PTPB_Module_HoverIcon Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'dashicons-heart';
			$this->label       = __( 'Hover Icon', 'pace-builder' );
			$this->description = __( 'An Icon with a Title, Description and a cool CSS3 Hover Effect', 'pace-builder' );
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'icon'         => array(
					'type'  => 'icon',
					'label' => __( 'Select Icon', 'pace-builder' )
				),
				'size'         => array(
					'type'    => 'select',
					'default' => '3',
					'label'   => __( 'Icon Size', 'pace-builder' ),
					'options' => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5'
					)
				),
				'hover_effect' => array(
					'type'    => 'select',
					'label'   => __( 'Icon Hover Effect', 'pace-builder' ),
					'desc'    => __( 'Icon Effect when hovered', 'pace-builder' ),
					'options' => array(
						''            => __( 'Slide From Left', 'pace-builder' ),
						'from-right'  => __( 'Slide From Right', 'pace-builder' ),
						'from-top'    => __( 'Slide From Top', 'pace-builder' ),
						'from-bottom' => __( 'Slide From Bottom', 'pace-builder' ),
						'rotate'      => __( 'Rotate', 'pace-builder' )
					)
				),
				'color'        => array(
					'type'    => 'color',
					'default' => '#27ae60',
					'label'   => __( 'Icon Color', 'pace-builder' ),
					'desc'    => __( 'Color of the Icon, this will be Icon color and the Border color', 'pace-builder' )
				),
				'hover_color'  => array(
					'type'    => 'color',
					'default' => '#fff',
					'label'   => __( 'Hover Color', 'pace-builder' ),
					'desc'    => __( 'Background Color of the Icon, when a user hovers on the Icon the Color and Hover Color will be swapped', 'pace-builder' )
				),
				'title'        => array(
					'type'  => 'text',
					'label' => __( 'Icon Title', 'pace-builder' ),
					'desc'  => __( 'This will be the heading/title below the Icon', 'pace-builder' )
				),
				'content'      => array(
					'type'  => 'tinymce',
					'label' => __( 'Icon Text', 'pace-builder' ),
					'desc'  => __( 'This will be the text below the Icon Title', 'pace-builder' )
				),
				'href'         => array(
					'type'  => 'text',
					'label' => __( 'Icon Link', 'pace-builder' ),
					'desc'  => __( 'This is the link the Icon will be pointing to', 'pace-builder' )
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
				<a href="{{{data.href}}}" target="_blank" class="icon fa-{{{data.size}}}x {{{data.icon}}}"></a>
				<h3 class="icon-title">{{{data.title}}}</h3>
				{{{ptPbApp.stripslashes(data.content)}}}
			<?php
		}

		/**
		 * Generate module content
		 * @param $module
		 *
		 * @return string
		 */
		public function get_content( $module ) {

			return sprintf( '<div class="hover-icon">
								<a href="%s" class="icon fa-%sx %s %s">&nbsp;</a>
								<h3 class="icon-title">%s</h3>
								%s
							</div>',
							esc_url( $module['href'] ),
							esc_attr( $module["size"] ),
							esc_attr( $module["icon"] ),
							esc_attr( $module['hover_effect'] ),
							esc_html( $module["title"] ),
							ptpb_get_content( $module )
						 );
		}

		/**
		 * Generate module CSS
		 * @param $module
		 *
		 * @return string
		 */
		public function get_css( $module ) {
			return sprintf( '
                        #%1$s .hover-icon .icon { background-color : %2$s; color: %3$s ; box-shadow: 0 0 0 4px %3$s; }
                        #%1$s .hover-icon .icon:hover { background-color : %3$s; color: %2$s ; box-shadow: 0 0 0 8px %3$s; }
                        ',
				$module['id'],
				$module['hover_color'],
				$module['color']
			);
		}

	}
endif;
