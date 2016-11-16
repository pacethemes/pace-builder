<?php

/**
 * Contact Form 7 Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes/modules
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_ContactForm7' ) ) :
	/**
	 * Class to handle HTML generation for Contact Form 7 Module
	 *
	 */
	class PTPB_Module_ContactForm7 extends PTPB_Module {

		private $cf7_forms;
		private $forms;

		/**
		 * PTPB_Module_ContactForm7 Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'dashicons-email-alt';
			$this->label       = __( 'Contact Form 7', 'pace-builder' );
			$this->description = __( 'Form created with Contact Form 7 Plugin', 'pace-builder' );

			// Get all CF7 forms and populate the Dropdown
			$this->cf7_forms = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'posts_per_page' => - 1 ) );
			$this->forms     = array();
			foreach ( $this->cf7_forms as $form ) {
				$this->forms[ $form->ID ] = $form->ID . ' - ' . ucwords( $form->post_title );
			}
			wp_reset_postdata();
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'form_id' => array(
					'type'    => 'select',
					'label'   => __( 'Select Form', 'pace-builder' ),
					'options' => $this->forms
				),
				'title'   => array(
					'type'  => 'text',
					'label' => __( 'Title', 'pace-builder' ),
				),
				'align'    => array(
					'type'    => 'select',
					'default' => 'left',
					'label'   => __( 'Alignment', 'pace-builder' ),
					'desc'    => __( 'Alignment of the Form', 'pace-builder' ),
					'options' => array(
						'left'   => __( 'Left', 'pace-builder' ),
						'center' => __( 'Center', 'pace-builder' ),
						'right'  => __( 'Right', 'pace-builder' )
					)
				),
				'theme'    => array(
					'type'    => 'select',
					'default' => 'none',
					'label'   => __( 'Form Theme', 'pace-builder' ),
					'options' => array(
						'none'   => __( 'None', 'pace-builder' ),
						'normal'   => __( 'Normal', 'pace-builder' ),
						'light' => __( 'Light', 'pace-builder' ),
						'dark'  => __( 'Dark', 'pace-builder' )
					)
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
			<?php _e( 'Form ID', 'pace-builder' ) ?>:  {{{ data.form_id }}}
			<?php
		}

		/**
		 * Generate module content
		 * @param $module
		 *
		 * @return string
		 */
		public function get_content( $module ) {

			return sprintf( "<div class='ptpb-cf7 theme-%s' style='text-align: %s;' %s></div>",
							$module['theme'],
							$module['align'],
							ptpb_generate_data_attr( 
								array( 
									'pb-process' => 'true', 
									'type' => 'contactform7', 
									'cf7-id' => $module['form_id'], 
									'cf7-title' =>  $module['title'] 
								)
							)
						);
		}

		/**
		 * Filter post_content to add/insert the ContactForm7 shortcode
		 * @param $content
		 * @param $col
		 *
		 * @return mixed
		 */
		public function filter_content( $content, $col ) {
			$data = ptpb_extract_data_attr( $col );

			if ( ! array_key_exists( 'cf7-id', $data ) ) {
				return $content;
			}

			$form = ( array_key_exists( 'cf7-title', $data ) && $data['cf7-title'] !== '' ) ? "<h3 class='wpcf7-title'> {$data['cf7-title']} </h3>" : '';

			$form .= do_shortcode( sprintf( '[contact-form-7 id="%s" title="%s"]' ,$data['cf7-id'], get_the_title( $data['cf7-id'] ) ) );

			return str_replace( $col, $col . $form, $content );

		}

	}
endif;