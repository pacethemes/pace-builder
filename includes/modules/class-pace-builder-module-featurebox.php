<?php

/**
 * Feature Box Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes/modules
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_FeatureBox' ) ) :
	/**
	 * Class to handle HTML generation for Feature Box Module
	 *
	 */
	class PTPB_Module_FeatureBox extends PTPB_Module {

		/**
		 * PTPB_Module_FeatureBox Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'dashicons-awards';
			$this->label       = __( 'Feature Box', 'pace-builder' );
			$this->description = __( 'An Icon Box with a Title and Description', 'pace-builder' );
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'icon'    => array(
					'type'  => 'icon',
					'label' => __( 'Select Icon', 'pace-builder' )
				),
				'size'    => array(
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
				'color'   => array(
					'type'    => 'color',
					'default' => '#27ae60',
					'label'   => __( 'Icon Color', 'pace-builder' ),
					'desc'    => __( 'Color of the Icon, this will be Icon color and the Border color', 'pace-builder' )
				),
				'title'   => array(
					'type'  => 'text',
					'label' => __( 'Title', 'pace-builder' ),
					'desc'  => __( 'This will be the heading/title below the Icon', 'pace-builder' )
				),
				'content' => array(
					'type'  => 'tinymce',
					'label' => __( 'Text', 'pace-builder' ),
					'desc'  => __( 'This will be the text below the Icon Title', 'pace-builder' )
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
				<a href="#" target="_blank" class="fa-{{{data.size}}}x {{{data.icon}}}"></a>
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

			return sprintf( '<div class="feature-box size-%1$s">
								<div class="ptpb-fb-head">
									<div class="icon-wrap"><i class="icon fa-%1$sx %2$s" style="color:%3$s;">&nbsp;</i></div>
									<h3 class="feature-box-title" style="color:$color;">%4$s</h3>
								</div>
								<div class="feature-box-content">
									<div class="feature-box-text">%5$s</div>
								</div>
							</div>',
							esc_attr( $module["size"] ),
							esc_attr( $module["icon"] ),
							esc_attr( $module["color"] ),
							esc_attr( $module["title"] ),
							ptpb_get_content( $module )
						);
		}

	}
endif;
