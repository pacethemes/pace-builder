<?php

/**
 * Handles PaceBuilder column html generation and column settings in the PaceBuilder backend
 *
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */


class PTPB_Column extends PTPB_Singleton {

	/**
	 * All Fields for PaceBuilder column
	 * @return array
	 */
	public function fields() {
		return apply_filters( 'ptpb_column_fields', array(
			'bg_color'      => array(
				'type'  => 'color',
				'label' => __( 'Background Color', 'pace-builder' ),
				'desc'  => __( 'Background Color for the column, leave it blank if you dont need a background color', 'pace-builder' ),
			),
			'bg_image'     => array(
				'type'  => 'image',
				'label' => __( 'Background Image', 'pace-builder' ),
				'desc'  => __( 'If set, this image will be used as the background for this column.', 'pace-builder' ),
			),
			'bg_attach'    => array(
				'type'    => 'select',
				'label'   => __( 'Background Image Attachment', 'pace-builder' ),
				'desc'    => sprintf( '%s <br/>%s <br/>', 
										__( 'Scroll - The background scrolls along with the element.', 'pace-builder' ),
										__( 'Fixed - The background is fixed with regard to the viewport', 'pace-builder' )
									),
				'options' => array(
					'fixed'  => __( 'Fixed', 'pace-builder' ),
					'scroll' => __( 'Scroll', 'pace-builder' ),
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
				'condition'  => "bg_size == 'custom'"
			),
			'label'        => array(
				'type'  => 'text',
				'default' => __( 'Column', 'pace-builder' ),
				'label' => __( 'Admin Label', 'pace-builder' ),
				'desc'  => __( 'Admin label for the column, this is the label/title you will see in the Pace Builder stage area, it lets you name your column and helps keep track of them', 'pace-builder' )
			)
		) );
	}

	/**
	 * Prints HTML form which allows users to edit Section Settings
	 * @return void
	 */
	public function js_templates(){
		?>
		<script type="text/template" id="pt-pb-tmpl-column">
			<div class="pt-pb-column-preview">
				<div title="Drag-and-drop this column into place"
				     class="pt-pb-column-header pt-pb-column-sortable ui-sortable-handle">
					<div class="sortable-background column-sortable-background">
						<?php _e( 'Column', 'pace-builder' ); ?> : {{{ data.type }}}
					</div>
					<div class="pt-pb-controls">
						<a href="#" class="pt-pb-settings-column" title="Column Settings"><i class="fa fa-cog"></i></a>
					</div>
				</div>
				<div class="pt-pb-column-content"></div>
				<div class="pt-pb-column-foot">
					<a href="#" class="pt-pb-insert-module button"><span> <i
								class="fa fa-cube"></i> <?php _e( 'Add Module', 'pace-builder' ) ?></span></a>
				</div>
			</div>
		</script>

		<script type="text/template" id="pt-pb-tmpl-column-edit">
			<div class="bbm-modal__topbar">
				<h2><?php _e( 'Edit Column', 'pace-builder' ); ?></h2>
				<div class="pt-pb-top-bar">
					<ul class="pt-topbar-tabs clearfix">
						<li class="tab-active">
							<a href="#pt-form-module-settings"><?php _e( 'Column Settings', 'pace-builder' ); ?></a>
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
						<?php ptpb_form_field( 'pt' ); ?>
						<?php ptpb_form_field( 'pb' ); ?>
						<?php ptpb_form_field( 'pl' ); ?>
						<?php ptpb_form_field( 'pr' ); ?>
						<?php ptpb_form_field( 'blw' ); ?>
						<?php ptpb_form_field( 'blc' ); ?>
						<?php ptpb_form_field( 'brw' ); ?>
						<?php ptpb_form_field( 'brc' ); ?>
						<?php ptpb_form_field( 'css_class' ); ?>
					</div>

					<div id="pt-form-typo-settings" class="pt-tab-pane">
						<?php ptpb_form_fonts(); ?>
					</div>

				</div>

			</div>
			<div class="bbm-modal__bottombar">
				<input type="button" class="button button-primary save-column" value="<?php _e( 'Save', 'pace-builder' ); ?>"/>
				<input type="button" class="button close-model" value="<?php _e( 'Close', 'pace-builder' ); ?>"/>
			</div>
		</script>
		<?php
	}

	/**
	 * Generates Column markup
	 *
	 * @param $column_html
	 * @param $column
	 * @param $anim_seq
	 * @param $i
	 * @param $col_cnt
	 * @return string $content
	 */
	public function get_content( $column_html, $column, $anim_seq, $i, $col_cnt ) {

		if ( ! isset( $column['type'] ) ) {
			return '';
		}

		if( $column['bg_size'] == 'custom' ) {
			$column['bg_size'] = $column['bg_dim'];
		}

		$css_class = sprintf( '%s %s',
			$this->get_column_class( $column['type'] ),
			$column['css_class']
		);
		$content  = '';

		if ( isset( $column['modules'] ) && is_array( $column['modules'] ) ) {
			foreach ( $column['modules'] as $module ) {
				$cls = !empty( $module['animation'] ) ? " wow {$module['animation']} " : ' ';
				$content .= sprintf( "<div id='%s' class='pt-pb-module-wrap %s' data-wow-delay='%dms' style='%s'>%s</div>",
					$module['id'],
					$cls . 'pt-pb-module-' . $module['type'] ,
					$anim_seq ? ( (int) $i ) * 200 : 0,
					ptpb_generate_css( $module ),
					$this->generate_module( $module )
				);
			}
		}

		return sprintf( '<div class="%s" style="%s" id="%s">
							<div class="pt-pb-col-wrap">
								%s
							</div>
						</div>',
			$css_class,
			ptpb_generate_css( $column ),
			$column['id'],
			$content
		);
	}

	/**
	 * Returns Bootstrap class based on the column type
	 *
	 * @param $type
	 * @return string
	 */
	private function get_column_class( $type ) {
		$cls = '';
		switch ( $type ) {
			case '1-1':
				$cls = 'col-md-12';
				break;

			case '1-2':
				$cls = 'col-md-6';
				break;

			case '1-3':
				$cls = 'col-md-4';
				break;

			case '1-4':
				$cls = 'col-md-3';
				break;

			default:
				$cls = 'col-md-12';
				break;
		}

		return apply_filters( 'ptpb_column_class', $cls, $type );
	}

	private function get_column_edge_class( $type ) {

	}

	/**
	 * Generates Module markup. Checks if a class exists which handles the Module Markup Generation, if it exists invokes the class and generates the content
	 *
	 * @param $module
	 * @return string
	 */
	private function generate_module( $module ) {
		$instance = ptpb_get_module_instance( $module['type'] );
		if ( $instance && method_exists( $instance, 'get_content' ) ) {
			return $instance->get_content( $module );
		}

		return;
	}

}

return PTPB_Column::instance();
