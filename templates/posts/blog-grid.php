<?php
/**
 * Blog Posts Grid template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package PaceBuilder/templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$show_title	= isset( $show_title ) ? $show_title : true;
$show_image	= isset( $show_image ) ? $show_image : true;
$show_meta	= isset( $show_meta )  ? $show_meta  : false;
$columns	= isset( $columns )    ? $columns    : 4;

/* Start the Loop */
while ( have_posts() ) : 
	the_post();
	echo isset( $wrap ) ? "<$wrap>" : "";
	ptpb_get_template( 'posts/blog-grid-single.php', array(
		'show_title'	=> $show_title,
		'show_image'	=> $show_image,
		'show_meta'		=> $show_meta,
		'columns'		=> $columns
	) );
	echo isset( $wrap ) ? "</$wrap>" : "";
endwhile;