<?php
/**
 * The Template for displaying PaceBuilder Page Header when the Page Layout is set to "None"
 *
 * This template can be overridden by copying it to yourtheme/pace-builder/header.php.
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

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>
</head>

<body <?php body_class( 'ptpb-page pace-builder-page' ); ?>>

<div id="ptpb-page" class="hfeed site">
