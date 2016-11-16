<?php

/**
 * Button Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes/modules
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_Title' ) ) :
	/**
	 * Class to handle HTML generation for Image Module
	 *
	 */
	class PTPB_Module_Title extends PTPB_Module {

		/**
		 * PTPB_Module_Title Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'fa fa-header';
			$this->label       = __( 'Title/Headline', 'pace-builder' );
			$this->description = __( 'A title/headline for your content', 'pace-builder' );
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'title'            => array(
					'type'  => 'text',
					'label' => __( 'Text', 'pace-builder' ),
					'desc'  => __( 'Title Text', 'pace-builder' )
				),
				'size'           => array(
					'type'    => 'select',
					'default' => 'h3',
					'label'   => __( 'Tag', 'pace-builder' ),
					'options' => array(
						'h1'   => __( 'H1', 'pace-builder' ),
						'h2' => __( 'H2', 'pace-builder' ),
						'h3'  => __( 'H3', 'pace-builder' ),
						'h4'   => __( 'H4', 'pace-builder' ),
						'h5' => __( 'H5', 'pace-builder' ),
						'h6'  => __( 'H6', 'pace-builder' )
					)
				),
				'text_align'    => array(
					'type'    => 'select',
					'default' => 'left',
					'label'   => __( 'Alignment', 'pace-builder' ),
					'desc'    => __( 'Alignment of the Title', 'pace-builder' ),
					'options' => array(
						'left'   => __( 'Left', 'pace-builder' ),
						'center' => __( 'Center', 'pace-builder' ),
						'right'  => __( 'Right', 'pace-builder' )
					)
				),
				'seperator'          => array(
					'type'    => 'select',
					'default' => '',
					'label'   => __( 'Separator', 'pace-builder' ),
					'desc'    => __( 'What type of Separator do you want for this Title ?', 'pace-builder' ),
					'options' => array(
						'' => __( 'None', 'pace-builder' ),
						'double-line'  => __( 'Double Line', 'pace-builder' ),
						'line-below' => __( 'Line Below Title', 'pace-builder' ),
						'line-below-simple' => __( 'Simple Line Below Title', 'pace-builder' ),
					)
				),
				'sep_color'           => array(
					'type'    => 'color',
					'default' => '#747474',
					'label'   => __( 'Separator Color', 'pace-builder' )
				),
				'max_width'            => array(
					'type'    => 'slider',
					'default' => '200px',
					'label'   => __( 'Separator Max Width', 'pace-builder' ),
					'desc'    => __( 'Max Width of the Separator, applicable only for "Line Below"', 'pace-builder' ),
					'max'     => 1000,
					'min'     => 10,
					'step'    => 5,
					'unit'    => 'px'
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
			<{{{ data.size }}}>{{{ data.title }}}</{{{ data.size }}}>
			<?php
		}

		/**
		 * Generate module content
		 * @param $module
		 *
		 * @return string
		 */
		public function get_content( $module ) {

			switch ( $module['seperator'] ) {
				case 'double-line':
					$html = sprintf( '<div class="ptpb-title ptpb-title-size-%1$s">
									    <%1$s class="title title-heading-%2$s %3$s">
									    	<span>%4$s</span>
									    </%1$s>
									</div>' ,
								$module['size'],
								$module['text_align'],
								$module['seperator'],
								$module['title']
							);
					break;

				case 'line-below':
					$html = sprintf( '<div class="ptpb-title %1$s ptpb-title-size-%2$s">
										    <%2$s class="title title-heading-%1$s %3$s">
										    	%4$s
										    </%2$s>
										    <div class="ptpb-sep-line-below" style="max-width: %5$s;">
												<span class="inner"> <span class="inner-icon"></span> </span>
											</div>
										</div>' ,
								$module['text_align'],
								$module['size'],
								$module['seperator'],
								$module['title'],
								$module['max_width']
							);
					break;

				case 'line-below-simple':
					$html = sprintf( '<div class="ptpb-title %1$s ptpb-title-size-%2$s">
									    <%2$s class="title title-heading-%1$s %3$s">
									    	%4$s
									    </%2$s>
									    <div class="ptpb-sep-line-below-simple">
										</div>
									</div>' ,
								$module['text_align'],
								$module['size'],
								$module['seperator'],
								$module['title']
							);
					break;
				
				default:
					$html = sprintf( '<div class="ptpb-title %1$s ptpb-title-size-%2$s">
									    <%2$s class="title title-heading-%1$s">
									    	%3$s
									    </%2$s>
									</div>',
								$module['text_align'],
								$module['size'],
								$module['title']
							 );
					break;
			}

			return $html;

		}

		/**
		 * Generate module CSS
		 * @param $module
		 *
		 * @return string|void
		 */
		public function get_css( $module ) {

			switch ( $module['seperator'] ) {
				case 'double-line':
					$css = sprintf( '#%1$s .title span:before{ border-color: %2$s !important; }
									#%1$s .title span:after{ border-color: %2$s !important; } ' ,
								$module['id'],
								$module['sep_color']
							);
					break;

				case 'line-below':
					$css = sprintf( '#%1$s .ptpb-sep-line-below .inner:before{ background-color: %2$s !important; }
									#%1$s .ptpb-sep-line-below .inner:after{ background-color: %2$s !important; }
									#%1$s .ptpb-sep-line-below .inner .inner-icon{ border-color: %2$s !important; }' ,
								$module['id'],
								$module['sep_color']
							);
					break;

				case 'line-below-simple':
					$css = sprintf( '#%1$s .ptpb-sep-line-below-simple{ background-color: %2$s !important; }' ,
								$module['id'],
								$module['sep_color']
							);
					break;
				
				default:
					$css = '';
					break;
			}

			return $css;

		}

	}
endif;