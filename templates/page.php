<?php
/**
 * The Template for displaying PaceBuilder Page when the Page Layout is set to "None"
 *
 * This template can be overridden by copying it to yourtheme/pace-builder/page.php.
 *
 * @since      1.1.0
 * @package    PTPB
 * @subpackage PTPB/templates
 * @author     Pace Themes <dev@pacethemes.com>
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

ptpb_get_template( 'header.php' );
if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<article <?php post_class( 'page-builder entry-content' ); ?> id="main">
		<?php the_content(); ?>
	</article>
	<?php 
	endwhile; 
endif; 
ptpb_get_template( 'footer.php' ); 

?>