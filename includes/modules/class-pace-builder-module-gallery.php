<?php

/**
 * Gallery Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes/modules
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_Gallery' ) ) :
	/**
	 * Class to handle HTML generation for Gallery Module
	 *
	 */
	class PTPB_Module_Gallery extends PTPB_Module {

		/**
		 * PTPB_Module_Gallery Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'dashicons-format-gallery';
			$this->label       = __( 'Gallery', 'pace-builder' );
			$this->description = __( 'An Image Gallery', 'pace-builder' );
			$this->has_items   = true;
			$this->item_label  = __( 'Image', 'pace-builder' );
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'shape'   => array(
					'type'    => 'select',
					'default' => 'rounded',
					'label'   => __( 'Thumbnails Shape', 'pace-builder' ),
					'desc'    => __( 'Do you want Rounded or Square Thumbnails ?', 'pace-builder' ),
					'options' => array(
						'rounded' => __( 'Round', 'pace-builder' ),
						'square'  => __( 'Square', 'pace-builder' )
					)
				),
				'columns' => array(
					'type'    => 'select',
					'default' => 'four',
					'label'   => __( 'Columns', 'pace-builder' ),
					'desc'    => __( 'Number of columns', 'pace-builder' ),
					'options' => array(
						'three' => __( 'Three', 'pace-builder' ),
						'four'  => __( 'Four', 'pace-builder' ),
						'five'  => __( 'Five', 'pace-builder' ),
						'six'   => __( 'Six', 'pace-builder' )
					)
				),
				'padding' => array(
					'type'    => 'select',
					'default' => 'yes',
					'label'   => __( 'Padding/Spacing', 'pace-builder' ),
					'desc'    => __( 'Do you want to show spacing between the images ?', 'pace-builder' ),
					'options' => array(
						'yes' => __( 'Yes', 'pace-builder' ),
						'no'  => __( 'No', 'pace-builder' )
					)
				),
			);
		}

		/**
		 * All Fields for this Module Items
		 * @return array
		 */
		public function item_fields() {
			return array(
				'src'   => array(
					'type'  => 'image',
					'label' => __( 'Image', 'pace-builder' )
				),
				'href'  => array(
					'type'  => 'text',
					'label' => __( 'Link', 'pace-builder' ),
					'desc'  => __( 'Link the image should be pointing to, leave it blank to open the image in a Lightbox', 'pace-builder' )
				),
				'title' => array(
					'type'  => 'text',
					'label' => __( 'Title', 'pace-builder' ),
					'desc'  => __( 'This will be the heading/title below the Image thumbnail', 'pace-builder' )
				),
				'desc'  => array(
					'type'  => 'textarea',
					'label' => __( 'Description', 'pace-builder' ),
					'desc'  => __( 'This will be the text below the Image in the lightbox/colorbox preview', 'pace-builder' )
				),
				'post_id' => array(
					'type'  => 'hidden'
				)
			);
		}

		/**
		 * Prints Preview and Edit BackboneJS templates for this module item
		 * @return void
		 */
		public function js_item_templates() {
			?>
			<script type="text/template" id="pt-pb-tmpl-module-<?php echo $this->slug(); ?>-item">
				<div class="pt-pb-item-content-inner clearfix">
					{{{ptPbApp.partial('module-item-header', { label: data.label
					})}}}
					<# if( data.src && data.src.trim() !== '' ) { #>
						<img src="{{{data.src}}}" />
					<# } #>
				</div>
			</script>

			<script type="text/template" id="pt-pb-tmpl-module-<?php echo $this->slug(); ?>-item-edit">
				<div class="bbm-modal__topbar">
					<h2><?php echo $this->item_label . ' ' . __( 'Settings ', 'pace-builder' ) ; ?></h2>
				</div>
				<div class="bbm-modal__section">
					<div class="pt-tab-pane-item">
						<?php $this->item_form(); ?>
					</div>
				</div>
				<div class="bbm-modal__bottombar">
					<input type="button" class="button button-primary save-module-item"
					       value="<?php _e( 'Save', 'pace-builder' ); ?>"/>
					<input type="button" class="button close-model" value="<?php _e( 'Close', 'pace-builder' ); ?>"/>
				</div>
			</script>
		<?php
		}

		/**
		 * Prints HTML for Module Item Preview in the PaceBuilder Stage area
		 * @return void
		 */
		public function item_preview() {
			?>
			<div class="item-content-wrap masonry-grid clearfix columns-{{{data.columns}}}"></div>
			<div class="pt-pb-column-foot">
				<a href="#" class="pt-pb-insert-item button"><span> <i
							class="fa fa-plus-circle"></i> <?php echo __( 'Add ', 'pace-builder' ) . $this->item_label; ?></span></a>
			</div>
		<?php
		}

		/**
		 * Generate module content
		 * @param $module
		 *
		 * @return string
		 */
		public function get_content( $module ) {

			$images = ''; 

			if ( is_array( $module['items'] ) ) {
				foreach ( $module['items'] as $image ) { 
					if ( ! is_array( $image ) ) {
						continue;
					}

					$images .=  sprintf('<div class="ptpb-gallery-thumb-wrap">
											<a href="%s" class="ptpb-gallery-thumb %s" title="%s" data-gallery="%s">
						             			%s
						             			<span class="overlay">
													<div class="image-title">%s</div>
												</span>
											</a>
										</div>',
										esc_url( $image['href'] ) == '' ? $image['src'] : esc_url( $image['href'] ),
										empty( $image['href'] ) ? 'gallery' : '',
										trim( $image["desc"] ) == '' ? esc_attr( $image["title"] ) : esc_attr( $image["desc"] ),
										esc_attr( $module["id"] ),
										isset( $image['post_id'] ) && $image['post_id'] != '' && is_numeric( $image['post_id'] ) 
											? wp_get_attachment_image( $image["post_id"], "ptpb-gallery" )
											: '<img src="' . $image['src'] . '" class="attachment-ptpb-gallery size-ptpb-gallery" />',
										esc_html( $image["title"] )
								);
				}
			} 

			return sprintf( '<div class="ptpb-gallery clearfix %s %s-col %s">%s</div>',
						$module["shape"],
						$module["columns"],
						$module['padding'] === 'no' ? 'no-pad' : '',
						$images
					);
		}

	}
endif;