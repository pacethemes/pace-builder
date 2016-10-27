<?php

/**
 * Styled List Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_StyledList' ) ) :
	/**
	 * Class to handle HTML generation for StyledList Module
	 *
	 */
	class PTPB_Module_StyledList extends PTPB_Module {

		/**
		 * PTPB_Module_StyledList Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'dashicons-editor-ul';
			$this->label       = __( 'Styled List', 'pace-builder' );
			$this->description = __( 'An Unordered List with Icons', 'pace-builder' );

			// required for module items
			$this->has_items  = true;
			$this->item_label = __( 'Item', 'pace-builder' );
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'icon_size'    => array(
					'type'    => 'slider',
					'default' => '10px',
					'label'   => __( 'Icon Size', 'pace-builder' ),
					'max'     => 50,
					'min'     => 5,
					'step'    => 1,
					'unit'    => 'px'
				),
				'icon_padding' => array(
					'type'    => 'slider',
					'default' => '10px',
					'label'   => __( 'Icon Spacing', 'pace-builder' ),
					'max'     => 30,
					'min'     => 0,
					'step'    => 1,
					'unit'    => 'px'
				),
				'item_mb'      => array(
					'type'    => 'slider',
					'default' => '5px',
					'label'   => __( 'Item Margin Botton', 'pace-builder' ),
					'max'     => 50,
					'min'     => 0,
					'step'    => 1,
					'unit'    => 'px'
				)
			);
		}

		/**
		 * All Fields for this Module Items
		 * @return array
		 */
		public function item_fields() {
			return array(
				'icon'        => array(
					'type'    => 'icon',
					'default' => '',
					'label'   => __( 'Icon', 'pace-builder' )
				),
				'icon_clr'    => array(
					'type'    => 'color',
					'default' => '',
					'label'   => __( 'Icon Color', 'pace-builder' )
				),
				'icon_bg_clr' => array(
					'type'    => 'color',
					'default' => '',
					'label'   => __( 'Icon Background Color', 'pace-builder' )
				),
				'content'     => array(
					'type'    => 'tinymce',
					'default' => '',
					'label'   => __( 'Content', 'pace-builder' ),
					'desc'    => __( 'This will be the List Item Content', 'pace-builder' )
				)
			);
		}

		/**
		 * Generate module content
		 * @param $module
		 *
		 * @return string
		 */
		public function get_content( $module ) {
			$width    = intval( trim( str_replace( 'px', '', $module['icon_size'] ) ) );
			$bg_width = ( $width * 2 ) . 'px';
			$pt       = ( $width / 2 ) . 'px';
			$ml       = ( intval( trim( str_replace( 'px', '', $module['icon_padding'] ) ) ) + $bg_width ) . 'px';
			$width 	 .= 'px';

			$items = '';

			if ( isset( $module['hasItems'] ) && is_array( $module['items'] ) ) {
				foreach ( $module['items'] as $item ) {
					$icon      = '';

					if ( ! empty( $item['icon'] ) ) {
						$icon = sprintf( '<span class="styled-list-item-icon" style="background-color:%1$s; width:%2$s; height:%2$s;">
								        	<i class="icon %3$s" style="font-size:%4$s; color:%5$s; padding-top:%6$s;"></i>
								    	</span>',
								    esc_attr( $item["icon_bg_clr"] ),
								    esc_attr( $bg_width ),
								    esc_attr( $item["icon"] ),
								    esc_attr( $width ),
								    esc_attr( $item["icon_clr"] ),
								    esc_attr( $pt )
								);
					}

					$items .= sprintf( '<li class="styled-list-item" style="margin-bottom:%s;">
											%s
											<div class="styled-list-item-content" style="margin-left:%s;min-height:%s;">
										        %s
										    </div>
										</li>',
									esc_attr( isset( $module['item_mb'] ) ? $module['item_mb'] : $module['mb'] ),
									$icon,
									esc_attr( $ml ),
									esc_attr( $bg_width ),
									ptpb_get_content( $item )
								 );
				}
			}

			return sprintf( '<div class="styled-list"><ul>%s</ul></div>', $items );
		}

	}
endif;