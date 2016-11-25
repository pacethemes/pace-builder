<?php

/**
 * Handles PaceBuilder section html generation and section settings in the PaceBuilder backend
 *
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */


class PTPB_Section extends PTPB_Singleton {

	/**
	 * All Fields for PaceBuilder section
	 * @return array
	 */
	public function fields() {
		return apply_filters( 'ptpb_section_fields', array(
			'bg_image'     => array(
				'type'  => 'image',
				'label' => __( 'Background Image', 'pace-builder' ),
				'desc'  => __( 'If set, this image will be used as the background for this section.', 'pace-builder' ),
			),
			'bg_parallax'     => array(
				'type'  => 'select',
				'default' => 'no',
				'label' => __( 'Background Image Parallax Effect', 'pace-builder' ),
				'desc'  => __( 'If this is enabled, it will override "Background Image Attachment" setting.', 'pace-builder' ),
				'options' => array(
					'yes' => __( 'Yes', 'pace-builder' ),
					'no'  => __( 'No', 'pace-builder' )
				),
				'dependency' => 'bg_image',
				'condition'  => "jQuery.trim(bg_image) != '' && ptPbApp.isUrl(bg_image)"
			),
			'bg_attach'    => array(
				'type'    => 'select',
				'default' => 'scroll',
				'label'   => __( 'Background Image Attachment', 'pace-builder' ),
				'desc'    => sprintf( '%s <br/>%s <br/>', 
										__( 'Scroll - The background scrolls along with the element.', 'pace-builder' ),
										__( 'Fixed - The background is fixed with regard to the viewport', 'pace-builder' )
									),
				'options' => array(
					'scroll' => __( 'Scroll', 'pace-builder' ),
					'fixed'  => __( 'Fixed', 'pace-builder' )
				),
				'dependency' => 'bg_image',
				'condition'  => "jQuery.trim(bg_image) != '' && ptPbApp.isUrl(bg_image)"
			),
			'bg_repeat'    => array(
				'type'    => 'select',
				'label'   => __( 'Background Image Repeat', 'pace-builder' ),
				'desc'    => sprintf( '%s <br/>%s <br/>%s <br/>%s <br/>', 
									__( 'No Repeat - The background image will not be repeated.', 'pace-builder' ),
									__( 'Repeat - The background image will be repeated both vertically and horizontally.', 'pace-builder' ),
									__( 'Repeat X - The background image will be repeated only horizontally.', 'pace-builder' ),
									__( 'Repeat Y - The background image will be repeated only vertically.', 'pace-builder' )
								),
				'options' => array(
					'no-repeat'  => __( 'No Repeat', 'pace-builder' ),
					'repeat' => __( 'Repeat', 'pace-builder' ),
					'repeat-x'  => __( 'Repeat X', 'pace-builder' ),
					'repeat-y' => __( 'Repeat Y', 'pace-builder' )
				),
				'dependency' => 'bg_image',
				'condition'  => "jQuery.trim(bg_image) != '' && ptPbApp.isUrl(bg_image)"
			),
			'bg_pos'    => array(
				'type'    => 'select',
				'label'   => __( 'Background Image Position', 'pace-builder' ),
				'desc'    => __( 'Starting position of the Background Image.', 'pace-builder' ),
				'options' => array(
					'center center' => __( 'Center Center', 'pace-builder' ),
					'center top' => __( 'Center Top', 'pace-builder' ),
					'center bottom' => __( 'Center Bottom', 'pace-builder' ),
					'left top' => __( 'Left Top', 'pace-builder' ),
					'left center' => __( 'Left Center', 'pace-builder' ),
					'left bottom' => __( 'Left Bottom', 'pace-builder' ),
					'right top' => __( 'Right Top', 'pace-builder' ),
					'right center' => __( 'Right Center', 'pace-builder' ),
					'right bottom' => __( 'Right Bottom', 'pace-builder' )
				),
				'dependency' => 'bg_image',
				'condition'  => "jQuery.trim(bg_image) != '' && ptPbApp.isUrl(bg_image)"
			),
			'bg_size'    => array(
				'type'    => 'select',
				'label'   => __( 'Background Image Size', 'pace-builder' ),
				'desc'    => sprintf( '%s <br/>%s <br/>%s <br/>%s <br/>', 
										__( 'Cover - Scale the background image to be as large as possible so that the background area is completely covered by the background image. Some parts of the background image may not be in view within the background positioning area.' , 'pace-builder' ),
										__( 'Contain - Scale the image to the largest size such that both its width and its height can fit inside the content area.' , 'pace-builder' ),
										__( 'Auto - The background-image contains its width and height.' , 'pace-builder' ),
										__( 'Custom - Set the width and height of the background image in the "Background Image Dimensions" field.' , 'pace-builder' )
									),
				'options' => array(
					'cover'  => __( 'Cover', 'pace-builder' ),
					'contain' => __( 'Contain', 'pace-builder' ),
					'auto' => __( 'Auto', 'pace-builder' ),
					'custom' => __( 'Custom', 'pace-builder' ),
				),
				'dependency' => 'bg_image',
				'condition'  => "jQuery.trim(bg_image) != '' && ptPbApp.isUrl(bg_image)"
			),
			'bg_dim'   => array(
				'type'  => 'text',
				'label' => __( 'Background Image Dimensions', 'pace-builder' ),
				'desc'  => sprintf( '%s <br/>%s <br/>', 
									__( 'Width and Height of the image either in percentage or pixels. The first value sets the width, the second value sets the height. If only one value is given, the second is set to "auto"', 'pace-builder' ),
									'Eg: 50% 50% OR 1080px 720px'
								),
				'dependency' => 'bg_size',
				'condition'  => "jQuery.trim(bg_image) != '' && ptPbApp.isUrl(bg_image) && bg_size == 'custom'"
			),
			'bg_color'     => array(
				'type'  => 'color',
				'label' => __( 'Background Color', 'pace-builder' ),
				'desc'  => __( 'Background Color for the section, leave it blank to set a transparent color', 'pace-builder' ),
			),
			'content_type' => array(
				'type'    => 'select',
				'default' => 'fluid',
				'label'   => __( 'Content Type', 'pace-builder' ),
				'desc'    => __( 'Boxed - Section content will be fixed to 1170px or corresponding device width. Fluid - Section content will be 100%% width to the browser width', 'pace-builder' ),
				'options' => array(
					'fluid' => __( 'Fluid', 'pace-builder' ),
					'boxed' => __( 'Boxed', 'pace-builder' )
				)
			),
			'label'        => array(
				'type'  => 'text',
				'default' => __( 'Section', 'pace-builder' ),
				'label' => __( 'Admin Label', 'pace-builder' ),
				'desc'  => __( 'Admin label for the section, this is the label/title you will see in the Pace Builder stage area, it lets you name your section and helps keep track of them', 'pace-builder' )
			)
		) );
	}

	/**
	 * Prints HTML form which allows users to edit Section Settings
	 * @return void
	 */
	public function js_templates(){
		?>
		<script type="text/template" id="pt-pb-tmpl-section">
			<div class="pt-pb-wrap">
				<div class="pt-pb-section-preview">
					<div class="pt-pb-header">
						<h3 class="pt-pb-section-label">{{{data.label}}}</h3>

						<div class="pt-pb-controls">
							<a href="#" class="pt-pb-settings pt-pb-settings-section"
							   title="<?php _e( 'Section Settings', 'pace-builder' ); ?>"><i class="fa fa-cog"></i></a>
							<a href="#" class="pt-pb-clone pt-pb-clone-section"
							   title="<?php _e( 'Clone Section', 'pace-builder' ); ?>"><i class="fa fa-clone"></i></a>
							<a href="#" class="pt-pb-remove remove-section" title="<?php _e( 'Delete Section', 'pace-builder' ); ?>"><i
									class="fa fa-trash-o"></i></a>
						</div>
						<a href="#" class="pt-pb-section-toggle" title="<?php _e( 'Click to toggle', 'pace-builder' ); ?>">
							<div class="handlediv"><i class="fa fa-caret-up"></i><i class="fa fa-caret-down"></i></div>
						</a>
					</div>
					<div class="pt-pb-content-wrap">
						<div class="pt-pb-content clearfix"></div>
						<div class="pt-pb-content-foot">
							<a href="#" class="section-type pt-pb-insert-column button"><i
									class="fa fa-columns"></i> <?php _e( 'Add Row', 'pace-builder' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		</script>

		<script type="text/template" id="pt-pb-tmpl-section-edit">
			<div class="bbm-modal__topbar">
				<h2><?php _e( 'Edit Section', 'pace-builder' ); ?></h2>
				<div class="pt-pb-top-bar">
					<ul class="pt-topbar-tabs clearfix">
						<li class="tab-active">
							<a href="#pt-form-module-settings"><?php _e( 'Section Settings', 'pace-builder' ); ?></a>
						</li>
						<li>
							<a href="#pt-form-design-settings"><?php _e( 'Style Settings', 'pace-builder' ); ?></a>
						</li>
						<li>
							<a href="#pt-form-typo-settings"><?php _e( 'Typography Settings', 'pace-builder' ); ?></a>
						</li>
					</ul>
				</div>
			</div>
			<div class="bbm-modal__section has-tabs">

				<div class="edit-content-wrap">
					<div id="pt-form-module-settings" class="pt-tab-pane">
						<?php if ( method_exists( $this, 'fields' ) ) : ?>
							<?php foreach ( $this->fields() as $name => $args ) : ?>
								<?php ptpb_form_field( $name, $args ); ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>

					<div id="pt-form-design-settings" class="pt-tab-pane">
						<?php ptpb_form_field( 'btw' ); ?>
						<?php ptpb_form_field( 'btc' ); ?>
						<?php ptpb_form_field( 'bbw' ); ?>
						<?php ptpb_form_field( 'bbc' ); ?>
						<?php ptpb_form_field( 'pt' ); ?>
						<?php ptpb_form_field( 'pb' ); ?>
						<?php ptpb_form_field( 'css_class' ); ?>
					</div>

					<div id="pt-form-typo-settings" class="pt-tab-pane">
						<?php ptpb_form_fonts(); ?>
					</div>

				</div>
			</div>
			<div class="bbm-modal__bottombar">
				<input type="button" class="button button-primary save-section" value="<?php _e( 'Save', 'pace-builder' ); ?>"/>
				<input type="button" class="button close-model" value="<?php _e( 'Close', 'pace-builder' ); ?>"/>
			</div>
		</script>
		<?php
	}

	/**
	 * Iterates through each row in the section and generates content
	 *
	 * @param $section_html
	 * @param $section
	 * @return string $content
	 */
	public function get_content( $section_html, $section, $prev_section = array(), $next_section = array() ) {

		$css_class   = isset( $section['css_class'] ) ? $section['css_class'] : '';
		$css_class  .= $section['bg_parallax'] === 'yes' && esc_url( $section['bg_image'] ) !== '' ? ' parallax-bg' : '';
		$container   = ( isset( $section['content_type'] ) && $section['content_type'] === 'fluid' ) ? 'container-fluid' : 'container';

		$content = '';

		if ( isset( $section['rows'] ) && is_array( $section['rows'] ) ) {
			foreach ( $section['rows'] as $row ) {
				/**
				 * Filter Row markup
				 *
				 * Since 1.0.0
				 */
				$content .= apply_filters( 'ptpb_generate_row', '', $row, $container );
			}
		}

		return sprintf( '<section class="pt-pb-section %s" style="%s" id="%s">
							%s
						</section>',
			esc_attr( $css_class ),
			ptpb_generate_css( $section ),
			$section['id'],
			$content
		);
	}

}

return PTPB_Section::instance();
