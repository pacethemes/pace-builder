<?php

/**
 * The Pace Builder save functionality of the plugin.
 * Responsible for saving Pace Builder page content and invoking the proper modules
 * provides functionality to generate HTML markup based on the sections & modules built using the Page Builder
 *
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */


class PTPB_Save extends PTPB_Singleton {

	//Holds the prepared sections posted by Pace Builder
	private $_sections = array();

	private $_data = array();
	private $_options = array();


	/**
	 * PTPB_Save constructor
	 * hook into admin_init and add PaceBuilder save conditional hooks
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'init' ) );
	}

	public function init() {
		// If Page was saved with Pace Builder then save the PB meta and generate Page Content
		// check if the save was triggered by manual update button or WP autosave
		if ( isset( $_POST['data'] ) && isset( $_POST['data']['wp_autosave'] ) && isset( $_POST['data']['ptpb_data'] ) ) {
			$this->_data    = json_decode( wp_unslash( $_POST['data']['ptpb_data'] ), true );
			$this->_options = json_decode( wp_unslash( $_POST['data']['ptpb_options'] ), true );
			$this->hooks();
		} else if ( isset( $_POST['pt-pb-nonce'] ) && wp_verify_nonce( $_POST['pt-pb-nonce'], 'save' ) ) {
			$this->_data    = json_decode( wp_unslash( $_POST['ptpb_data'] ), true );
			$this->_options = json_decode( wp_unslash( $_POST['ptpb_options'] ), true );
			$this->hooks();
		} 
	}

	public function hooks() {

		// Save the post's meta data
		add_action( 'save_post', array( $this, 'save_pb_meta' ), 10, 2 );

		// If PageBuilder not enabled for page, don't proceed further
		if( empty( $_POST['pt_is_pb'] ) || $_POST['pt_is_pb'] != 1 ) {
			return;
		}

		// Combine the input into the post's content
		add_filter( 'wp_insert_post_data', array( $this, 'insert_post_data' ), 30, 2 );

		/**
		 * Add filters to generate Pace Builder content, this lets users override any section/module markup generation
		 *
		 * Since 1.0.0
		 */
		add_filter( 'ptpb_generate_section', array( PTPB_Section::instance(), 'get_content' ), 10, 4 );
		add_filter( 'ptpb_generate_row', array( PTPB_Row::instance(), 'get_content' ), 10, 3 );
		add_filter( 'ptpb_generate_column', array( PTPB_Column::instance(), 'get_content' ), 10, 5 );

	}

	/**
	 * Updates Post Meta key '_ptpb_sections' with the prepared sections array
	 *
	 * @param $post_id
	 * @param $post
	 */
	public function save_pb_meta( $post_id, $post ) {

		update_metadata( 'post', $post_id, '_ptpb_enabled', intval( $_POST['pt_is_pb'] ) );

		// If PageBuilder not enabled for page, don't proceed further
		if ( empty( $_POST['pt_is_pb'] ) || $_POST['pt_is_pb'] != 1 ) {
			return;
		}

		// Add Pace Builder data
		/*
		 * Use the underlying update_metadata function
		 * vs update_post_meta() to make sure we're working
		 * with the actual revision meta.
		 */
		update_metadata( 'post', $post_id, '_ptpb_sections', $this->get_sections( true ) );
		update_metadata( 'post', $post_id, '_ptpb_options', json_encode( $this->_options ) );
	}

	/**
	 * Updates Post Content with the HTML markup generated based on the sections/modules built by the user using Page Builder
	 *
	 * @param $data
	 * @param $postarr
	 */
	public function insert_post_data( $data, $postarr ) {

		if ( ! isset( $postarr['ptpb_data'] ) && empty( $this->_data ) ) {
			return $data;
		}

		/**
		 * Custom action before updating page content
		 *
		 * Since 1.0.0
		 */
		do_action( 'ptpb_insert_post_data', $postarr, $this->get_sections() );

		$data['post_content'] = $this->generate_post_content();

		return $data;
	}

	/**
	 * @param bool $encode
	 *
	 * @return array|string
	 */
	private function get_sections( $encode = false ) {
		if ( empty( $this->_sections ) && ! empty( $this->_data ) ) {
			$this->_sections = $this->prepare_sections( $this->_data );
		}

		if ( $encode ) {
			return ptpb_encode_data( $this->_sections );
		}

		return $this->_sections;
	}

	/**
	 * Prepares Sections by sorting all rows
	 *
	 * @param $sections
	 *
	 * @return array $sorted
	 */
	private function prepare_sections( $sections ) {
		$sorted = array();
		foreach ( $sections as $key => $section ) {
			if ( $section == '' || empty( $section ) || ! is_array( $section ) ) {
				continue;
			}
			
			if ( isset( $section['rows'] ) && is_array( $section['rows'] ) ) {
				$sorted[] = $this->sort_rows( $section );
			} else {
				$sorted[] = $section;
			}
		}

		return $sorted;
	}

	/**
	 * Sorts rows in the order they are submitted and returns the sorted section
	 *
	 * @param $section
	 * @return array $section
	 */
	private function sort_rows( $section ) {

		$sorted = array();

		foreach ( $section['rows'] as $row ) {
			if ( ptpb_isset_and_array( $row['columns'] ) ) {
				$sorted[] = $this->sort_columns( $row );
			} else {
				$sorted[] = $row;
			}
		}

		$section['rows'] = $sorted;

		return $section;
	}

	/**
	 * Sorts columns in the order they are submitted and returns the sorted row
	 *
	 * @param $row
	 * @return array $row
	 */
	private function sort_columns( $row ) {

		$columns = array();

		foreach ( $row['columns'] as $column ) {
			$column    = $this->sanitize_column( $column );
			$columns[] = $this->sort_modules( $column );
		}

		$row['columns'] = $columns;

		return $row;
	}

	/**
	 * Sorts modules in the order they are submitted and returns the sorted column
	 *
	 * @param $column
	 * @return array $column
	 */
	private function sort_modules( $column ) {

		$modules = array();

		if ( array_key_exists( 'modules', $column ) && is_array( $column['modules'] ) ) {

			foreach ( $column['modules'] as $module ) {
				$modules[] = apply_filters( 'ptpb_sort_items', $module );
			}

			$column['modules'] = $modules;
		}

		return $column;
	}

	/**
	 * Iterates through each section and generates section content
	 *
	 * @return string $content
	 */
	private function generate_post_content() {
		if ( $this->_sections === '' || empty( $this->_sections ) ) {
			return '';
		}

		$content = "<div class='pt-pb-page-content-wrap' id='pt-pb-content'>";

		foreach ( $this->_sections as $i => $section ) {

			$prev_section = empty( $this->_sections[$i-1] ) ? array() : $this->_sections[$i-1] ;
			$next_section = empty( $this->_sections[$i+1] ) ? array() : $this->_sections[$i+1] ;

			/**
			 * Filter Section markup
			 *
			 * Since 1.0.0
			 */
			$content .= apply_filters( 'ptpb_generate_section', '', $section, $prev_section, $next_section );
		}

		return $content . '</div>';

	}

	/**
	 * Sanitizes a Column
	 *
	 * @param $column
	 * @return array $column
	 */
	private function sanitize_column( $column ) {

		if ( isset( $column['modules'] ) && is_array( $column['modules'] ) ) {
			foreach ( $column['modules'] as $ind => $module ) {
				foreach ( $module as $name => $value ) {
					if ( $name === 'items' ) {
						continue;
					}
					$module[ $name ] = $this->sanitize_value( $name, $value );
				}

				if ( isset( $module['items'] ) && is_array( $module['items'] ) ) {
					foreach ( $module['items'] as $i => $item ) {
						foreach ( $item as $k => $v ) {
							$item[ $k ] = $this->sanitize_value( $k, $v );
						}
						$module['items'][ $i ] = $item;
					}
				}
				$column['modules'][ $ind ] = $module;
			}
		}

		return $column;
	}

	/**
	 * Sanitizes any value based on type
	 *
	 * @param $name
	 * @param $value
	 * @return mixed $value
	 */
	private function sanitize_value( $name, $value ) {

		if ( strpos( $name, 'url' ) !== false ) {
			$value = esc_url( $value );
		} else if ( strpos( $name, 'content' ) !== false ) {
			// do nothing
		} else if ( strpos( $name, 'color' ) !== false ) {
			$value = $this->sanitize_color( $value );
		}

		return $value;
	}

	/**
	 * Sanitizes hex color
	 *
	 * @param $color
	 * @return string $color
	 */
	private function sanitize_color( $color ) {
		if ( '' === $color ) {
			return '';
		}

		// 3 or 6 hex digits, or the empty string.
		// Updated to match rgba string
		if ( preg_match( '/^(#([A-Fa-f0-9]{3}){1,2})|(rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*(\d*(?:\.\d+)?)\))$/', $color ) ) {
			return $color;
		}

		return null;
	}

}
