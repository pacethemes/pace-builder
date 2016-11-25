<?php

/**
 * Handles PaceBuilder row html generation and row settings in the PaceBuilder backend
 *
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */


class PTPB_Row extends PTPB_Singleton {

	/**
	 * All Fields for PaceBuilder Row
	 * @return array
	 */
	public function fields() {
		$yes_no_option = array(
			'yes' => __( 'Yes', 'pace-builder' ),
			'no'  => __( 'No', 'pace-builder' )
		);
		return apply_filters( 'ptpb_row_fields', array(
			'content_type'     => array(
				'type'    => 'select',
				'default' => 'parent',
				'label'   => __( 'Content Type', 'pace-builder' ),
				'desc'    => __( 'Default - Inherits Section "Content Type" setting. Boxed - Row content will be fixed to 1170px or corresponding device width. Fluid - Row content will be 100% width to the browser width', 'pace-builder' ),
				'options' => array(
					'parent' => __( 'Default', 'pace-builder' ),
					'boxed'  => __( 'Boxed', 'pace-builder' ),
					'fluid'  => __( 'Fluid', 'pace-builder' ),
				)
			),
			'gutter'           => array(		
				'type'    => 'select',
				'label'   => __( 'Space Between Columns', 'pace-builder' ),
				'desc'    => __( 'Show Space/Padding between Columns ?', 'pace-builder' ),
				'options' => $yes_no_option
			),
			'vertical_align'   => array(				
				'type'    => 'select',
				'label'   => __( 'Columns Vertical Alignment', 'pace-builder' ),
				'desc'    => __( 'Vertical Alignment of the columns', 'pace-builder' ),
				'options' => array(
					'default' => __( 'Default', 'pace-builder' ),
					'top'     => __( 'Top', 'pace-builder' ),
					'middle'  => __( 'Middle', 'pace-builder' ),
					'bottom'  => __( 'Bottom', 'pace-builder' ),
				)
			),
			'edge'  		 => array(				
				'type'    => 'select',
				'label'   => __( 'Force Column Edge', 'pace-builder' ),
				'desc'    => __( 'Do you want to force/push a column in this row to the edge of viewport ? If yes select the left or right column. Use this option only if the row has 2 columns.', 'pace-builder' ),
				'options' => array(
					'none' 	  => __( 'None', 'pace-builder' ),
					'left'     => __( 'Left', 'pace-builder' ),
					'right'  => __( 'Right', 'pace-builder' )
				)
			),
			'anim_seq'         => array(				
				'type'    => 'select',
				'label'   => __( 'Sequential Module Animations', 'pace-builder' ),
				'desc'    => __( 'Should all the modules in this row animate sequentially ?', 'pace-builder' ),
				'options' => $yes_no_option
			),
			'label'            => array(
				'type'  => 'text',
				'default' => __( 'Row', 'pace-builder' ),
				'label' => __( 'Admin Label', 'pace-builder' ),
				'desc'  => __( 'Admin label for the row, this is the label/title you will see in the Pace Builder stage area, it lets you name your row and helps keep track of them', 'pace-builder' )
			)
		) );
	}

	/**
	 * Prints HTML form which allows users to edit Section Settings
	 * @return void
	 */
	public function js_templates(){
		?>
		<script type="text/template" id="pt-pb-tmpl-row">
			<div class="pt-pb-row-preview">
				<div class="pt-pb-row-header">
					<h3 class="pt-pb-row-label">{{{ data.label }}}</h3>

					<div class="pt-pb-controls">
						<a href="#" class="pt-pb-settings-columns" title="<?php _e( 'Column Layout', 'pace-builder' ); ?>"><i
								class="fa fa-columns"></i></a>
						<a href="#" class="pt-pb-settings-row" title="<?php _e( 'Row Settings', 'pace-builder' ); ?>"><i
								class="fa fa-cog"></i></a>
						<a href="#" class="pt-pb-clone-row" title="<?php _e( 'Clone Row', 'pace-builder' ); ?>"><i
								class="fa fa-clone"></i></a>
						<a href="#" class="pt-pb-remove-row" title="<?php _e( 'Delete Row', 'pace-builder' ); ?>"><i
								class="fa fa-trash-o"></i></a>
					</div>
					<a href="#" class="pt-pb-row-toggle" title="Click to toggle">
						<div class="handlediv"><i class="fa fa-caret-up"></i><i class="fa fa-caret-down"></i></div>
					</a>
				</div>
				<div class="pt-pb-row-content"></div>
			</div>
		</script>

		<script type="text/template" id="pt-pb-tmpl-row-edit">
			<div class="bbm-modal__topbar">
				<h2><?php _e( 'Edit Row', 'pace-builder' ); ?></h2>
				<div class="pt-pb-top-bar">
					<ul class="pt-topbar-tabs clearfix">
						<li class="tab-active">
							<a href="#pt-form-module-settings"><?php _e( 'Row Settings', 'pace-builder' ); ?></a>
						</li>
						<li class="tab-active">
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
					</div>

					<div id="pt-form-typo-settings" class="pt-tab-pane">
						<?php ptpb_form_fonts(); ?>
					</div>

				</div>


			</div>
			<div class="bbm-modal__bottombar">
				<input type="button" class="button button-primary save-row" value="<?php _e( 'Save', 'pace-builder' ); ?>"/>
				<input type="button" class="button close-model" value="<?php _e( 'Close', 'pace-builder' ); ?>"/>
			</div>
		</script>
		<?php
	}

	/**
	 * Generates Row markup
	 *
	 * @param $row_html
	 * @param $row
	 * @param $container
	 * @return string $content
	 */
	public function get_content( $row_html, $row, $container ) {

		$container = $row['content_type'] === 'parent' ? $container
			: ( $row['content_type'] === 'fluid' ? 'container-fluid' : 'container' );

		$valign   	= ( empty( $row['vertical_align'] ) || 'default' === $row['vertical_align'] ) ? '' : "v-align-{$row['vertical_align']}";
		$gutter   	= $row['gutter'] === 'no' ? 'no-gutter' : '';
		$anim_seq 	= $row['anim_seq'] === 'yes';
		$content  	= '';
		$edge 	  	= in_array( $row['edge'] , array( 'left', 'right' ) );
		$edge_col 	= $row['edge'] === 'left' ? 0 : count( $row['columns'] ) - 1 ;
		$edge_html	= '';

		if ( isset( $row['columns'] ) && is_array( $row['columns'] ) ) {

			foreach ( $row['columns'] as $key => $col ) {
				if( $edge && $edge_col == (int) $key ) {
					$column['pl'] = '0px';
					$column['pr'] = '0px';
					$valign .= ' row-edge';
					$edge_html = apply_filters( 'ptpb_generate_column', '', $col, $anim_seq, $key, count( $row['columns'] ) );

					if( preg_match( '/col-md-([0-9]+)/', $edge_html, $col_no ) ) {
						$c = (int) $col_no[1];
						$n = 12 - $c;
						$edge_html = str_replace( "col-md-$c" , "col-md-$c p0" , $edge_html );
						if( $row['edge'] === 'right' ) {
							$edge_html = str_replace( "col-md-$c" , "col-md-$c col-md-push-$n" , $edge_html );
							$pull = "col-md-pull-$c";
						} else {
							$pull = "right";
						}
					}

					continue;
				}
				/**
				 * Filter Column markup
				 *
				 * Since 1.0.0
				 */
				$content .= apply_filters( 'ptpb_generate_column', '', $col, $anim_seq, $key, count( $row['columns'] ) );
			}
		}

		$content = isset( $pull ) ? preg_replace( '/col-md-([0-9]+)/' , "col-md-$1 $pull", $content ) : $content;

		return sprintf( '<div class="ptpb-row-wrap">%s<div class="%s"><div class="row ptpb-row %s %s" style="%s" id="%s">%s</div></div></div>',
			$edge_html,
			$container,
			$valign,
			$gutter,
			ptpb_generate_css( $row ),
			$row['id'],
			$content
		);
	}

}

return PTPB_Row::instance();