<?php

/**
 * Text Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_Text' ) ) :
	/**
	 * Class to handle HTML generation for Text Module
	 *
	 */
	class PTPB_Module_Text extends PTPB_Module {

		/**
		 *	PTPB_Module_Text Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'dashicons-editor-paragraph';
			$this->label       = __( 'Text', 'pace-builder' );
			$this->description = __( 'A rich-text tinymce Text editor', 'pace-builder' );
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'content' => array(
					'type'  => 'tinymce',
					'label' => __( 'Content', 'pace-builder' )
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
			<#
				var c = jQuery('<div>'+ptPbApp.stripslashes(data.content)+'</div>');
				c.find('script,style').each(function(){
					jQuery(this).replaceWith(jQuery('<pre>'+this.innerHTML+'</pre>'));
				});
			#>
			{{{c.html()}}}
			<?php
		}

		/**
		 * Generate module content
		 * @param $module
		 *
		 * @return string
		 */
		public function get_content( $module ) {
			return "<div class='module-text'>" . ptpb_get_content( $module ) . '</div>';
		}

	}
endif;