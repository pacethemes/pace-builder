<?php

/**
 * Blog Module
 *
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes/modules
 * @author     Pace Themes <dev@pacethemes.com>
 */

if ( ! class_exists( 'PTPB_Module_Blog' ) ) :
	/**
	 * Class to handle HTML generation for Alert Module
	 *
	 */
	class PTPB_Module_Blog extends PTPB_Module {

		/**
		 * PTPB_Module_Blog Constructor
		 */
		public function __construct() {
			parent::__construct();
			$this->icon        = 'dashicons-admin-post';
			$this->label       = __( 'Blog Posts Grid', 'pace-builder' );
			$this->description = __( 'Simple grid with WordPress blog posts', 'pace-builder' );
		}

		/**
		 * All Fields for this Module
		 * @return array
		 */
		public function fields() {
			return array(
				'tax_terms' => array(
					'type'     => 'chosen',
					'label'    => __( 'Taxonomies', 'pace-builder' ),
					'desc'     => __( 'Enter categories, tags or custom taxonomies to narrow down posts', 'pace-builder' ),
					'options'  => ptpb_wp_taxonomy_terms( array( 'category', 'post_tag' ) ),
					'multiple' => true
				),
				'limit'            => array(
					'type'    => 'slider',
					'default' => 10,
					'label'   => __( 'Post Count', 'pace-builder' ),
					'desc'    => __( 'How many posts do you want to show', 'pace-builder' ),
					'max'     => 50,
					'min'     => 0,
					'step'    => 1,
					'unit'    => ''
				),
				'orderby'           => array(
					'type'    => 'select',
					'default' => 'date',
					'label'   => __( 'Sort By', 'pace-builder' ),
					'desc'    => __( 'For Meta Value and Numeric Meta Value enter a Meta Key below.', 'pace-builder' ),
					'options' => array(
						'date'   			=> __( 'Date', 'pace-builder' ),
						'ID' 				=> __( 'Post ID', 'pace-builder' ),
						'author'  			=> __( 'Post Author', 'pace-builder' ),
						'title'    			=> __( 'Post Title', 'pace-builder' ),
						'name'    			=> __( 'Post Name', 'pace-builder' ),
						'modified'  		=> __( 'Post Modified Date', 'pace-builder' ),
						'parent' 			=> __( 'Post Parent ID', 'pace-builder' ),
						'rand'  			=> __( 'Random', 'pace-builder' ),
						'comment_count'    	=> __( 'Comment Count', 'pace-builder' ),
						'menu_order'    	=> __( 'Menu/Page Order', 'pace-builder' ),
						'meta_value'    	=> __( 'Meta Value', 'pace-builder' ),
						'meta_value_num'    => __( 'Numeric Meta Value', 'pace-builder' )
					)
				),
				'order'           => array(
					'type'    => 'select',
					'default' => 'DESC',
					'label'   => __( 'Sort Direction', 'pace-builder' ),
					'options' => array(
						'DESC'   			=> __( 'Descending', 'pace-builder' ),
						'ASC' 				=> __( 'Ascending', 'pace-builder' )
					)
				),
				'meta_key'           => array(
					'type'    	 => 'text',
					'label'   	 => __( 'Meta Key', 'pace-builder' ),
					'dependency' => 'orderby',
					'condition'  => "['meta_value','meta_value_num'].indexOf(orderby) > -1"
				),
				'columns'           => array(
					'type'    => 'select',
					'default' => 'three',
					'label'   => __( 'Columns', 'pace-builder' ),
					'options' => array(
						'one'   	=> __( 'One', 'pace-builder' ),
						'two' 		=> __( 'Two', 'pace-builder' ),
						'three'  	=> __( 'Three', 'pace-builder' ),
						'four'    	=> __( 'Four', 'pace-builder' ),
						'five'    	=> __( 'Five', 'pace-builder' ),
					)
				),
				'image'           => array(
					'type'    => 'select',
					'default' => 'yes',
					'label'   => __( 'Show Featured Image', 'pace-builder' ),
					'options' => $this->yes_no_option
				),
				'title'           => array(
					'type'    => 'select',
					'default' => 'no',
					'label'   => __( 'Show Post Title', 'pace-builder' ),
					'options' => $this->yes_no_option
				),
				'meta'           => array(
					'type'    => 'select',
					'default' => 'no',
					'label'   => __( 'Show Post Meta', 'pace-builder' ),
					'options' => $this->yes_no_option
				),
				'padding'           => array(
					'type'    => 'select',
					'default' => 'no',
					'label'   => __( 'Show Padding (Spacing between posts)', 'pace-builder' ),
					'options' => $this->yes_no_option
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

			$terms = empty( $module['tax_terms'] ) ? '' : implode( ' ', $module['tax_terms'] ) ;

			return sprintf( "<div class='ptpb-blog-list clearfix' %s></div>",
						ptpb_generate_data_attr( 
							array( 
								'pb-process' => 'true', 
								'type'    	=> 'blog', 
								'limit'   	=> $module['limit'], 
								'image'   	=> $module['image'],
								'title'   	=> $module['title'],
								'meta'    	=> $module['meta'],
								'padding' 	=> $module['padding'],
								'columns' 	=> $module['columns'],
								'orderby' 	=> $module['orderby'],
								'order'   	=> $module['order'],
								'meta_key'	=> $module['meta_key'],
								'terms'   	=> $terms
							)
						)
					);
		}

		/**
		 * Filter post_content to add/insert the Blog Posts HTML
		 * @param $content
		 * @param $col
		 *
		 * @return mixed
		 */
		public function filter_content( $content, $col ) { 
			global $wp_query, $wp_the_query;
			
			$data   = ptpb_extract_data_attr( $col );

			$params = array(
				'post_type'    => 'post',
				'showposts'    => $data ['limit'],
				'orderby'      => empty( $data['orderby'] ) ? 'post_date' : $data['orderby'],
				'order'        => empty( $data['order'] ) ? 'DESC' : $data['order'],
				'post__not_in' => get_option( 'sticky_posts' )
			);

			if ( in_array( $params['orderby'], array( 'meta_value', 'meta_value_num' ) ) ) {
				$params['meta_key'] = empty( $data['meta_key'] ) ? '' : $data['meta_key'];
			}

			// required for older page builder versions
			if ( isset( $data['cats'] ) ) {
				$taxonomy = array(
					array(
						'taxonomy' => 'category',
						'field'    => 'slug',
						'terms'    => explode( ' ', $data['cats'] )
					)
				);
				$params['tax_query'] = $taxonomy;
			}

			if ( ! empty( $data['terms'] ) ) {
				$terms = get_terms( array( 'include' => $data['terms'], 'hide_empty' => false ) );
				$taxonomies = array();
				if( ! empty( $terms ) && is_array( $terms ) ) {
					foreach ( $terms as $term ) {
						$taxonomies[$term->taxonomy] = isset( $taxonomies[$term->taxonomy] ) ? $taxonomies[$term->taxonomy] : array( 'taxonomy' => $term->taxonomy, 'field' => 'term_id', 'terms' => array() );
						$taxonomies[$term->taxonomy]['terms'][] = $term->term_id;
					}
					$params['tax_query'] = array_values( $taxonomies );
					$params['tax_query']['relation'] = 'OR';
				}
			}

			$wp_query       = new WP_Query( $params );
			$padding_cls = $data['padding'] === 'yes' ? '' : 'no-pad';
			$columns     = $data['columns'] . '-col';

			$posts = $col . "<div class='masonry-grid-container clearfix $padding_cls $columns'>";

			$posts .=  ptpb_get_template_html( 'posts/blog-grid.php', array(
							'show_title'	=> $data['title'] === 'yes',
							'show_image'	=> $data['image'] === 'yes',
							'show_meta'		=> $data['meta'] === 'yes',
							'columns'		=> $data['columns']
						) );

			//back to the initial WP query stored in $wp_the_query
    		$wp_query = $wp_the_query;
			wp_reset_postdata();

			$posts .= '</div>';

			return str_replace( $col, $posts, $content );

		}

	}
endif;