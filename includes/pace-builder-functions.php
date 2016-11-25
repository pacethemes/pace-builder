<?php

if( ! function_exists( 'ptpb_is_pb' ) ) :

	/**
	 * Checks if a page is a Pace Builder page
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	function ptpb_is_pb() {
		global $post;

		return $post && get_post_meta( $post->ID, '_ptpb_enabled', true ) == 1;
	}

endif;

if( ! function_exists( 'ptpb_get_data' ) ) :

	/**
	 * Get the PaceBuilder Data for current page
	 * @return array|bool
	 */
	function ptpb_get_data() {
		global $post;
		if ( ptpb_is_pb() ) {
			return ptpb_decode_data( get_post_meta( $post->ID, '_ptpb_sections', true ) );
		}

		return false;
	}

endif;

if( ! function_exists( 'ptpb_get_page_options' ) ) :

	/**
	 * Get the PaceBuilder Page Options for current page
	 * @return array|bool|mixed|object
	 */
	function ptpb_get_page_options() {
		global $post;
		if ( ptpb_is_pb() ) {
			$options = get_post_meta( $post->ID, '_ptpb_options', true );
			if ( empty( $options ) || trim( $options ) === '' ) {
				return array();
			}

			return json_decode( $options, true );
		}

		return false;
	}

endif;	

if( ! function_exists( 'ptpb_get_layout' ) ) :

	/**
	 * Returns the Current PaceBuilder page layout
	 * @return string
	 */
	function ptpb_get_layout() {
		$options = ptpb_get_page_options();

		return isset( $options['layout'] ) ? $options['layout'] : 'default';
	}

endif;

if( ! function_exists( 'ptpb_is_fullwidth_page' ) ) :

	/**
	 * Checks if the current PaceBuilder page is Full Width
	 * @return bool
	 */
	function ptpb_is_fullwidth_page() {
		$options = ptpb_get_page_options();

		return ( isset( $options['fullwidth'] ) && $options['fullwidth'] === 'yes' ) ? true : false;
	}
endif;

if( ! function_exists( 'ptpb_get_module_instance' ) ) :

	/**
	 * Returns an instance of the module by slug or class name
	 * @param $module
	 *
	 * @return bool|mixed
	 */
	function ptpb_get_module_instance( $module ) {
		if ( is_array( $module ) && ( ! isset( $module['type'] ) || empty( $module['type'] ) ) ) {
			return false;
		}

		$cls = is_array( $module ) ? $module['type'] : $module;

		if ( strpos( $cls, 'PTPB_Module_', 0 ) === false ) {
			$modules = PTPB()->modules();
			if( isset( $modules[ strtolower( $cls ) ] ) ) {
				$cls = $modules[ strtolower( $cls ) ];
			} else {
				$cls = 'PTPB_Module_' . ucwords( $cls );
			}
		}

		if ( ! class_exists( $cls ) ) {
			return false;
		}

		return call_user_func( array( $cls, 'instance' ) );
	}

endif;

if( ! function_exists( 'ptpb_extract_data_attr' ) ) :

	/**
	 * Returns elements with data attributes
	 * @param $string
	 *
	 * @return array
	 */
	function ptpb_extract_data_attr( $string ) {
		$string = str_replace( '"', "'", $string );
		$cnt    = preg_match_all( "/data-([^=]+)='([^']+)'/", $string, $matches );
		$data   = array();
		if ( $cnt === false || $cnt < 1 ) {
			return $data;
		}
		foreach ( $matches[1] as $ind => $key ) {
			$data[ $key ] = $matches[2][ $ind ];
		}

		return $data;
	}

endif;

if( ! function_exists( 'ptpb_generate_attr' ) ) :

	/**
	 * Generates HTML Attributes
	 *
	 * @param $array
	 * @param $attributes
	 *
	 * @return string $content
	 */
	function ptpb_generate_attr( $array, $attributes ) {
		$content = '';

		foreach ( $attributes as $attribute ) {
			if ( array_key_exists( $attribute, $array ) && $array[ $attribute ] !== '' ) {
				$value = esc_attr( $array[ $attribute ] );
				$content .= " $attribute='$value'";
			}
		}

		return $content;
	}

endif;

if( ! function_exists( 'ptpb_generate_data_attr' ) ) :

	/**
	 * Generates Data Attributes
	 *
	 * @param $values
	 * @param $properties
	 * @return string $content
	 */
	function ptpb_generate_data_attr( $values, $properties = array() ) {
		$content = '';
		if( empty( $properties ) ) {
			$properties = array_keys( $values );
		}
		foreach ( $properties as $prop ) {
			if ( array_key_exists( $prop, $values ) ) {
				$attr  = str_replace( '_', '-', $prop );
				$value = esc_attr( $values[ $prop ] );
				$content .= " data-$attr='$value'";
			}
		}

		return $content;
	}

endif;

if( ! function_exists( 'ptpb_generate_css' ) ) :

	/**
	 * Generates CSS Properties for a array
	 *
	 * @param $arr
	 * @return string
	 */
	function ptpb_generate_css( $arr ) {
		$css = array(
			'bg_image'   => 'background-image',
			'bg_attach'  => 'background-attachment',
			'bg_repeat'  => 'background-repeat',
			'bg_pos'	 	 => 'background-position',
			'bg_pos_x'   => 'background-position-x',
			'bg_pos_y'   => 'background-position-y',
			'bg_size'    => 'background-size',
			'bg_color'   => 'background-color',
			'text_color' => 'color',
			'pt'         => 'padding-top',
			'pb'         => 'padding-bottom',
			'pl'         => 'padding-left',
			'pr'         => 'padding-right',
			'margin_top' => 'margin-top',
			'mb'         => 'margin-bottom',
			'btw'        => 'border-top-width',
			'bbw'        => 'border-bottom-width',
			'btc'        => 'border-top-color',
			'bbc'        => 'border-bottom-color',
			'blw'        => 'border-left-width',
			'brw'        => 'border-right-width',
			'blc'        => 'border-left-color',
			'brc'        => 'border-right-color',
			'height'     => 'height',
			'text_size'  => 'font-size'
		);

		$properties = array();

		foreach ( $arr as $prop => $value ) {
			if ( ! array_key_exists( $prop, $css ) || trim( $value ) === '' ) {
				continue;
			}

			if ( $prop == 'bg_image' ) {
				$url          = esc_url( $value );
				$properties[] = "$css[$prop]:url($url)";
			} else {
				$properties[] = "$css[$prop]:$value";
			}
		}

		return esc_attr( implode( '; ', $properties ) );

	}

endif;

if( ! function_exists( 'ptpb_get_content' ) ) :

	/**
	 * escape HTML content in a module
	 *
	 * @param $module
	 * @return string
	 */
	function ptpb_get_content( $module ) {
		return isset( $module['content'] ) ? wpautop( stripslashes( $module['content'] ) ) : '';
	}

endif;

if( ! function_exists( 'ptpb_decode_data' ) ) :
	/**
	 * Decodes Page Builder Meta Data if it's encoded, uses `json_decode`
	 * @since  1.0.0
	 *
	 * @param $meta
	 * @return array
	 */
	function ptpb_decode_data( $meta ) {

		// Perform json decode on the meta
		$decoded = json_decode( $meta, true );

		// Convert quotes (single and double) entities back to quotes
		if ( is_array( $decoded ) ) {
			$decoded = ptpb_normalize_data( $decoded );
		}

		return $decoded;
	}
endif;

if( ! function_exists( 'ptpb_encode_data' ) ) :
	/**
	 * Encodes Pace Builder Meta Data to json format to handle PHP `serialize` issues with UTF8 characters
	 * WordPress `update_post_meta` serializes the data and in some cases (probably depends on hostng env.)
	 * the serialized data is not being unserialized
	 * Uses `json_encode`
	 *
	 * @since  1.0.0
	 *
	 * @param $meta
	 * @return string
	 */
	function ptpb_encode_data( $meta ) {

		if ( defined( 'JSON_UNESCAPED_UNICODE' ) ) {
			return json_encode( ptpb_sanitize_data( $meta ), JSON_UNESCAPED_UNICODE );
		}

		return preg_replace_callback( '/(?<!\\\\)\\\\u(\w{4})/', 'ptpb_unescape_utf8', json_encode( ptpb_sanitize_data( $meta ) ) );
	}
endif;

if( ! function_exists( 'ptpb_sanitize_data' ) ) :
	/**
	 * Sanitizes Pace Builder Meta Data
	 * Converts quotes and tags to html entities so that json_encode doesn't have issues
	 * @since  1.0.0
	 *
	 * @param $arr
	 * @return array
	 */
	function ptpb_sanitize_data( $arr ) {
		$result = array();
		foreach ( $arr as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = ptpb_sanitize_data( $value );
			} else {
				// try to unslash first incase the server already escaped quotes
				$value = htmlspecialchars( wp_unslash( $value ), ENT_QUOTES );
				$value = str_replace( '\&' , '&', $value );
				$value = str_replace( "\n", "\\\n", $value );
			}
			$result[ $key ] = $value;
		}

		return $result;
	}
endif;

if( ! function_exists( 'ptpb_normalize_data' ) ) :
	/**
	 * Normalizes Pace Builder Meta Data
	 * Converts quotes and tags html entities back to their original state
	 * @since  1.0.0
	 *
	 * @param $arr
	 * @return array
	 */
	function ptpb_normalize_data( $arr ) {
		$result = array();
		foreach ( $arr as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = ptpb_normalize_data( $value );
			} else {
				$value = htmlspecialchars_decode( $value, ENT_QUOTES );
			}
			$result[ $key ] = $value;
		}

		return $result;
	}
endif;

if( ! function_exists( 'ptpb_isset_and_array' ) ) :
	/**
	 * Check if param is set and is array
	 * @param $arr
	 *
	 * @return bool
	 */
	function ptpb_isset_and_array( $arr ) {
		return isset( $arr ) && is_array( $arr );
	}
endif;

if( ! function_exists( 'ptpb_request_param' ) ) :
	/**
	 * Check if REQUEST (GET or POST) param is set
	 * @param $param
	 *
	 * @return param | bool
	 */
	function ptpb_request_param( $param, $default = false ) {
		return isset( $_REQUEST[$param] ) ? $_REQUEST[$param] : $default ;
	}
endif;

if( ! function_exists( 'ptpb_unescape_utf8' ) ) :
	/**
	 * @param $matches
	 *
	 * @return string
	 */
	function ptpb_unescape_utf8( $matches ) {
		return html_entity_decode( '&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8' );
	}
endif;

if ( ! function_exists( 'ptpb_post_meta' ) ) :

	/**
	 * Prints HTML with meta information for the current post-date/time, author & comments.
	 */
	function ptpb_post_meta() {
		echo '<time class="post-date updated"><i class="fa fa-clock-o"></i>' . get_the_time( get_option( 'date_format' ) ) . '</time>';

		echo '<span class="separator">/</span>';

		echo comments_popup_link(
			__( '<i class="fa fa-comments"></i>No Comments', 'pace-builder' ),
			__( '<i class="fa fa-comments"></i>1 Comment', 'pace-builder' ),
			__( '<i class="fa fa-comments"></i>% Comments', 'pace-builder' ) );

	}
endif;

if( ! function_exists( 'ptpb_get_template_part' ) ) :
	/**
	 * Get template part (for templates like the blog posts).
	 *
	 *
	 * @access public
	 * @param mixed $slug
	 * @param string $name (default: '')
	 */
	function ptpb_get_template_part( $slug, $name = '' ) {
		$template = '';

		// Look in yourtheme/slug-name.php and yourtheme/pace-builder/slug-name.php
		if ( $name ) {
			$template = locate_template( array( "{$slug}-{$name}.php", PTPB()->template_path() . "{$slug}-{$name}.php" ) );
		}

		// Get default slug-name.php
		if ( ! $template && $name && file_exists( PTPB()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
			$template = PTPB()->plugin_path() . "/templates/{$slug}-{$name}.php";
		}

		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/pace-builder/slug.php
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", PTPB()->template_path() . "{$slug}.php" ) );
		}

		// Allow 3rd party plugins to filter template file from their plugin.
		$template = apply_filters( 'ptpb_get_template_part', $template, $slug, $name );

		if ( $template ) {
			include( $template );
		}
	}
endif;

if ( ! function_exists( 'ptpb_get_template' ) ) :
	/**
	 * Get pace builder template.
	 *
	 * @access public
	 * @param string $template_name
	 * @param array $args (default: array())
	 * @param string $template_path (default: '')
	 * @param string $default_path (default: '')
	 */
	function ptpb_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		$located = ptpb_locate_template( $template_name, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0' );
			return;
		}

		// Allow 3rd party plugin filter template file from their plugin.
		$located = apply_filters( 'ptpb_get_template', $located, $template_name, $args, $template_path, $default_path );

		do_action( 'ptpb_before_template_part', $template_name, $template_path, $located, $args );

		include( $located );

		do_action( 'ptpb_after_template_part', $template_name, $template_path, $located, $args );
	}
endif;

if ( ! function_exists( 'ptpb_get_template_html' ) ) :
	/**
	 * Like ptpb_get_template, but returns the HTML instead of outputting.
	 * @see ptpb_get_template
	 * @since 1.0.0
	 * @param string $template_name
	 */
	function ptpb_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		ob_start();
		ptpb_get_template( $template_name, $args, $template_path, $default_path );
		return ob_get_clean();
	}
endif;

if ( ! function_exists( 'ptpb_locate_template' ) ) :
	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 *		yourtheme		/	$template_path	/	$template_name
	 *		yourtheme		/	$template_name
	 *		$default_path	/	$template_name
	 *
	 * @access public
	 * @param string $template_name
	 * @param string $template_path (default: '')
	 * @param string $default_path (default: '')
	 * @return string
	 */
	function ptpb_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = PTPB()->template_path() . '/templates/';
		}

		if ( ! $default_path ) {
			$default_path = PTPB()->plugin_path() . '/templates/';
		}

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name
			)
		);

		// Get default template/
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found.
		return apply_filters( 'ptpb_locate_template', $template, $template_name, $template_path );
	}
endif;

if( ! function_exists( 'ptpb_get_taxonomies' ) ) :
	/**
	 * Get all taxomies registered
	 *
	 * @return array
	 */
	function ptpb_get_taxonomies(){
		global $ptpb_taxonomies;
		if( ! isset( $ptpb_taxonomies ) ) {
			$ptpb_taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		}
		return $ptpb_taxonomies;
	}
endif;

if ( ! function_exists( 'ptpb_wp_taxonomy_terms' ) ) :
	/**
	 * Return WordPress taxonomies
	 */
	function ptpb_wp_taxonomy_terms( $taxonomies = array() ) {
		$terms 		= get_terms( $taxonomies, array( 'hide_empty' => false ) );
		$all_tax 	= ptpb_get_taxonomies();
		$data 		= array();

		if( is_array( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$taxonomy = isset( $all_tax[ $term->taxonomy ] ) ? $all_tax[ $term->taxonomy ] : false;
				if( $taxonomy ) {
					$data[ $taxonomy->name ] = isset( $data[ $taxonomy->name ] ) ? $data[ $taxonomy->name ] : array( 'label' => $taxonomy->label, 'options' => array() );
					$data[ $taxonomy->name ]['options'][$term->term_id] = $term->name;
				}
			}
		}
		return $data;
	}
endif;

if ( ! function_exists( 'ptpb_get_term_item' ) ) :
	function ptpb_get_term_item( $term ){
		if( ! $term || ! is_object( $term ) )
			return array();

		$taxonomies = ptpb_get_taxonomies();
		return array(
					'label' 	=> $term->name,
					'value' 	=> $term->slug,
					'category'  => isset( $taxonomies[ $term->taxonomy ] ) ? $taxonomies[ $term->taxonomy ]->label : __( 'Taxonomies', 'pace-builder' )
				);
	}
endif;

if ( ! class_exists( 'PTPB_Menu' ) ) :

	/**
	 * PTPB_Menu extends from Walker_Nav_Menu
	 * Provides custom walker functions to add/edit additional markup for the PaceBuilder menu module
	 */
	class PTPB_Menu extends Walker_Nav_Menu {

		/**
		 * @param object $element
		 * @param array $children_elements
		 * @param int $max_depth
		 * @param int $depth
		 * @param array $args
		 * @param string $output
		 */
		function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
			$id_field = $this->db_fields['id'];

			if ( is_object( $args[0] ) ) {
				$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
			}

			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}

		/**
		 * @param string $output
		 * @param object $item
		 * @param int $depth
		 * @param array $args
		 * @param int $id
		 */
		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

			if ( $args->has_children ) {
				$item->classes[] = 'dropdown';
			}

			parent::start_el( $output, $item, $depth, $args, $id );
		}

		// add classes to ul sub-menus
		/**
		 * @param string $output
		 * @param int $depth
		 * @param array $args
		 */
		function start_lvl( &$output, $depth = 0, $args = array() ) {

			// depth dependent classes
			$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' );

			$output .= "\n" . $indent . '<ul class="dropdown-menu">' . "\n";
		}
	}

endif;
