<?php

/**
 * Empty space Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes/modules
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_EmptySpace' ) ) :
	/**
	 * Class to handle HTML generation for Separator Module
	 *
	 */
	class PTPB_Module_EmptySpace extends PTPB_Module {

		/**
		 * PTPB_Module_EmptySpace Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'ti-split-v';
			$this->label       = __( 'Empty Space', 'pace-builder' );
			$this->description = __( 'Empty space with custom height', 'pace-builder' );
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'height'      => array(
					'type'    => 'slider',
					'default' => '30px',
					'label'   => __( 'Height', 'pace-builder' ),
					'max'     => 200,
					'min'     => 1,
					'step'    => 1,
					'unit'    => 'px'
				)
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
			
			<?php
		}

		/**
		 * Generate module content
		 * @param $module
		 *
		 * @return string
		 */
		public function get_content( $module ) {
			return sprintf( "<div class='ptpb-empty-space' style='height:%s;'></div>", $module['height'] );
		}

	}
endif;