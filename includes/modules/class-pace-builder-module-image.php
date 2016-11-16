<?php

/**
 * Image Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_Image' ) ) :
	/**
	 * Class to handle HTML generation for Image Module
	 *
	 */
	class PTPB_Module_Image extends PTPB_Module {

		/**
		 * PTPB_Module_Image Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'dashicons-format-image';
			$this->label       = __( 'Image', 'pace-builder' );
			$this->description = __( 'Simple Image with optional lightbox', 'pace-builder' );
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'src'      => array(
					'type'  => 'image',
					'label' => __( 'Select Image', 'pace-builder' ),
					'desc'  => __( 'Select the Image you want to insert.', 'pace-builder' ),
				),
				'align'    => array(
					'type'    => 'select',
					'default' => 'left',
					'label'   => __( 'Alignment', 'pace-builder' ),
					'desc'    => __( 'Alignment of the image', 'pace-builder' ),
					'options' => array(
						'left'   => __( 'Left', 'pace-builder' ),
						'center' => __( 'Center', 'pace-builder' ),
						'right'  => __( 'Right', 'pace-builder' )
					)
				),
				'alt'      => array(
					'type'  => 'text',
					'label' => __( 'Alt', 'pace-builder' ),
					'desc'  => __( 'HTML alt attribute for the image', 'pace-builder' )
				),
				'title'    => array(
					'type'  => 'text',
					'label' => __( 'Title', 'pace-builder' ),
					'desc'  => __( 'HTML title attribute for the image', 'pace-builder' )
				),
				'href'     => array(
					'type'  => 'text',
					'label' => __( 'URL / Hyperlink', 'pace-builder' ),
					'desc'  => __( 'If set the image will be wrapped inside an anchor tag which will be opened if the user clicks on the image', 'pace-builder' )
				),
				'target'   => array(
					'type'    => 'select',
					'label'   => __( 'URL should open in New Tab ?', 'pace-builder' ),
					'desc'    => __( 'Should the URL open in a new tab or the same tab ?', 'pace-builder' ),
					'options' => array(
						'_blank' => __( 'Yes', 'pace-builder' ),
						'_self'  => __( 'No', 'pace-builder' )
					)
				),
				'lightbox' => array(
					'type'    => 'select',
					'default' => 'yes',
					'label'   => __( 'Image Lightbox', 'pace-builder' ),
					'desc'    => __( 'Do you want to show a lightbox for the image ? If set to yes it will override the URL and the image will be displayed in a lightbox when the image is clicked', 'pace-builder' ),
					'options' => $this->yes_no_option
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
			<div style="text-align:{{{data.align}}};"><img src="{{{data.src}}}"/></div>
		<?php
		}

		/**
		 * Generate module content
		 * @param $module
		 *
		 * @return string
		 */
		public function get_content( $module ) {
			$content = "<figure style='text-align:{$module['align']};'>";

			$module['class'] = $module['animation'] != '' ? "wow {$module['animation']}" : '';

			if ( $module['lightbox'] === 'yes' || $module['href'] !== '' ) {
				$content .= '<a' . ptpb_generate_attr( $module, array( 'target' ) );
				$content .= $module['lightbox'] === 'yes' ? " href='{$module['src']}'" : " href='" . esc_url( $module['href'] ) . "'";
				$content .= $module['lightbox'] === 'yes' ? " class='lightbox gallery'>" : '>';
			}

			$content .= '<img' . ptpb_generate_attr( $module, array(
					'src',
					'title',
					'alt',
					'class'
				) ) . ' />';

			if ( $module['lightbox'] === 'yes' || $module['href'] !== '' ) {
				$content .= '</a>';
			}

			$content .= '</figure>';

			return $content;
		}

	}
endif;