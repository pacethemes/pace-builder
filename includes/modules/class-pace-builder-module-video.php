<?php

/**
 * Button Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes/modules
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_Video' ) ) :
	/**
	 * Class to handle HTML generation for Image Module
	 *
	 */
	class PTPB_Module_Video extends PTPB_Module {

		/**
		 * PTPB_Module_Video Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'dashicons-video-alt3';
			$this->label       = __( 'Video', 'pace-builder' );
			$this->description = __( 'Youtube, Vimeo or Daily Motion Video', 'pace-builder' );
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'video_type'	=> array(
					'type'  => 'select',
					'label' => __( 'Video Type', 'pace-builder' ),
					'desc'  => sprintf( '%s<br/>%s<br/>', 
										__( 'Internal - Video is located in the site Media Library.', 'pace-builder' ),
										__( 'External - Video is located on an external video sharing site (eg. YouTube, Vimeo etc.).', 'pace-builder' )
									),
					'options' => array(
						'internal'	=> __( 'Internal Media Library', 'pace-builder' ),
						'external'	=> __( 'External Website', 'pace-builder' )
					)
				),
				'link'            => array(
					'type'    => 'text',
					'default' => '',
					'label'   => __( 'Video Link', 'pace-builder' ),
					'desc'    => sprintf( __( 'Link to the video (check WordPress %scodex page%s for available formats).', 'pace-builder' ), '<a href="http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target="_blank">', '</a>' ),
					'dependency' => 'video_type',
					'condition'  => "video_type == 'external'"
				),
				'video_src'  => array(
					'type'    => 'video',
					'label'   => __( 'Video', 'pace-builder' ),
					'dependency' => 'video_type',
					'condition'  => "video_type == 'internal'"
				),
				'width'            => array(
					'type'    => 'slider',
					'default' => 100,
					'label'   => __( 'Video Width', 'pace-builder' ),
					'max'     => 100,
					'min'     => 10,
					'step'    => 5,
					'unit'    => '%%'
				),
				'aspect'           => array(
					'type'    => 'select',
					'default' => '169',
					'label'   => __( 'Video Aspect Ratio', 'pace-builder' ),
					'options' => array(
						'169'   => '16:9',
						'43' => '4:3',
						'235'  => '2.35:1'
					)
				),
				'align'          => array(
					'type'    => 'select',
					'default' => 'left',
					'label'   => __( 'Video Alignment', 'pace-builder' ),
					'desc'    => __( 'Select the video alignment', 'pace-builder' ),
					'options' => array(
						'left' => __( 'Left', 'pace-builder' ),
						'right'  => __( 'Right', 'pace-builder' ),
						'center'  => __( 'Center', 'pace-builder' )
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
			_e( 'Video Link', 'pace-builder' ); ?>
			: {{{data.link}}}
			<?php
		}

		/**
		 * Generate module content
		 * @param $module
		 *
		 * @return string
		 */
		public function get_content( $module ) {
			$width   	= esc_attr( intval( $module['width'] ) );
			$padding 	= esc_attr( $module['align'] == 'center' ? ( 100 - $width ) / 2 : (
							 $module['align'] == 'right' ? 100 - $width : 0
							) );

			$video = '';
			$embed = '';

			if( $module['video_type'] === 'external' ) {
				$embed = sprintf( '[embed]%s[/embed]', $module['link'] );
				$cls = 'ptpb-video-ar-%1' . esc_attr( $module['aspect'] ) . ' ptpb-video-width-' . $width;
			} else {
				$video = ptpb_generate_data_attr( 
								array( 
									'pb-process' => 'true', 
									'type'    	=> 'video', 
									'video'   	=> $module['video_src']
								)
							);
				$cls = 'ptpb-video-library';
			}

			return sprintf( '<div class="ptpb-module-video %1$s">
								<div class="ptpb-video-wrap" style="width: %2$s%%;margin-left:%3$s%%;">
									<div class="ptpb-video-wrap-inner" %4$s>
										%5$s
									</div>
								</div>
							</div>',
				$cls,
				$width,
				$padding,
				$video,
				$embed
			);
		}

		/**
		 * Filter post_content to add video shortcode
		 * @param $content
		 * @param $col
		 *
		 * @return mixed
		 */
		public function filter_content( $content, $col ) { 
			global $content_width;

			$data   = ptpb_extract_data_attr( $col );

			if( empty( $data['video'] ) ) {
				return $content;
			}

			$video = wp_video_shortcode( array( 'src' => $data['video'], 'width' => empty( $content_width ) ? 960 : $content_width ) );

			return str_replace( $col, $col . $video, $content );
		}

	}
endif;
