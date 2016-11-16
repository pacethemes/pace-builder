<?php

/**
 * Google Map Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes/modules
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_GoogleMap' ) ) :
	/**
	 * Class to handle HTML generation for Image Module
	 *
	 */
	class PTPB_Module_GoogleMap extends PTPB_Module {

		// Scripts required by this module
		public $scripts;

		/**
		 * PTPB_Module_GoogleMap Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'fa fa-map-o';
			$this->label       = __( 'Google Map', 'pace-builder' );
			$this->description = __( 'An interactive Google map', 'pace-builder' );
			$this->scripts 	   = array(
									'ptpb_base64'		=> PTPB()->plugin_url() . '/assets/plugins/base64/jquery.base64.js',
									'ptpb_google-maps'  => $this->maps_url(),
									'ptpb_mapsed'	  	=> PTPB()->plugin_url() . '/assets/plugins/mapsed/mapsed.min.js'
								);

			add_action( 'ptpb_module_googlemap_after_js_templates', array( $this, 'points_template' ), 10 );
		}

		/**
		 * URL for Google Maps, include an API Key if it's set
		 * @return string
		 */
		public function maps_url() {
			$url 	 = '//maps.googleapis.com/maps/api/js?libraries=places';
			$api_key = ptpb_get_setting( 'gmaps_api_key'  );

			if( ! empty( $api_key ) ) {
				$url = add_query_arg( 'key', $api_key, $url );
			}

			return $url;
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'_map'	=> array(
					'type'   => 'hidden',
					'label'  => 'Map',
					'append' => '<div class="google-map-select" id="{{{ data.id }}}_map"></div><br/>
								 <div><strong>Zoom: </strong><span id="gmap-zoom-level"></span></div>
								 <div><strong>Center: </strong><span id="gmap-center"></span></div><br/>
								 <div id="gmap-points"></div><br/>'
				),
				'zoom'  => array(
					'type'  => 'hidden'
				),
				'center'  => array(
					'type'  => 'hidden'
				),
				'theme'           => array(
					'type'    => 'select',
					'default' => 'none',
					'label'   => __( 'Theme', 'pace-builder' ),
					'desc'    => __( 'Theme for the Map. Source - https://snazzymaps.com', 'pace-builder' ),
					'options' => array(
						'none'   => __( 'None', 'pace-builder' ),
						'subtle-grayscale' => __( 'Subtle Grayscale', 'pace-builder' ),
						'blue-water'  => __( 'Blue Water', 'pace-builder' ),
						'pale-dawn'   => __( 'Pale Dawn', 'pace-builder' ),
						'blue-essence' => __( 'Blue Essence', 'pace-builder' ),
						'apple-maps-esque'  => __( 'Apple Maps-esque', 'pace-builder' ),
						'retro'   => __( 'Retro', 'pace-builder' ),
						'paper'   => __( 'Paper', 'pace-builder' ),
						'flat-map'   => __( 'Flat map', 'pace-builder' ),
						'gowalla'   => __( 'Gowalla', 'pace-builder' ),
						'subtle'   => __( 'Subtle', 'pace-builder' ),
					)
				),
				'height'            => array(
					'type'    => 'slider',
					'default' => '400px',
					'label'   => __( 'Map Height', 'pace-builder' ),
					'max'     => 1200,
					'min'     => 100,
					'step'    => 5,
					'unit'    => 'px'
				),
				'bd_color'           => array(
					'type'    => 'color',
					'default' => '#27ae60',
					'label'   => __( 'Popup Border Color', 'pace-builder' )
				),
				'bg_color'           => array(
					'type'    => 'color',
					'default' => '#fafafa',
					'label'   => __( 'Popup Background Color', 'pace-builder' )
				),
				'text_color'           => array(
					'type'    => 'color',
					'default' => '#333',
					'label'   => __( 'Popup Text Color', 'pace-builder' )
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
			<b><?php _e( 'Theme', 'pace-builder' ); ?>: </b>{{{ data.theme }}}<br/>
			<b><?php _e( 'Map Height', 'pace-builder' ); ?>: </b> {{{ data.height }}}<br/>
			<b><?php _e( 'Popup Border Color', 'pace-builder' ); ?>: </b> {{{ data.bd_color }}}<br/>
			<b><?php _e( 'Popup Background Color', 'pace-builder' ); ?>: </b> {{{ data.bg_color }}}<br/>
			<b><?php _e( 'Popup Text Color', 'pace-builder' ); ?>: </b> {{{ data.text_color }}}<br/>
			<# if(data.points && data.points.length) { #>
			 	<b><?php _e( 'Points', 'pace-builder' ); ?>: </b> <br/>
			 	<# _.each(data.points, function(point, i ) { #>
			 		Lat: {{{point.lat}}}, Lng: {{{point.lng}}}, Title: {{{point.title}}}, Street: {{{point.street}}} <br/>
			 	<# }) #>	
			 <# } #>
			<?php
		}

		/**
		 * Print backbonejs template for Points in the settings/edit dialog
		 * @return array
		 */
		public function points_template() {
			?>
			<script type="text/template" id="pt-pb-tmpl-module-<?php echo $this->slug(); ?>-points">
				<# if(data.points && data.points.length) { #>
				 	<div>
				 		<strong>Points:</strong><br/>
				 		<# _.each(data.points, function(point, i ) { #>
					 		<b>Lat: </b>{{{point.lat}}}, <b>Lng: </b>{{{point.lng}}}, <b>Title: </b>{{{point.title}}}, <b>Street: </b>{{{point.street}}} <br/>
					 	<# }) #>
				 	</div>
				 <# } #>
			</script>
			<?php
			
		}

		/**
		 * Generate module content
		 * @param $module
		 *
		 * @return string
		 */
		public function get_content( $module ) {

			$points = isset( $module['points'] ) && ! empty( $module['points'] ) ? $module['points'] : array();
			$points = base64_encode( json_encode( $points ) );

			return sprintf( "<div class='ptpb-google-map-wrap' data-bdcolor='%s' data-bgcolor='%s' data-color='%s'>
								<div id='%s_map' class='ptpb-google-map' style='height:%s;' data-theme='%s'  data-zoom='%s' data-center='%s' data-points='%s'></div>
							</div>",
							$module['bd_color'],
							$module['bg_color'],
							$module['text_color'],
							$module['id'],
							$module['height'],
							$module['theme'],
							$module['zoom'],
							$module['center'],
							$points
					 );
		}

	}
endif;
