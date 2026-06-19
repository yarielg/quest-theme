<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="qt-skip-link screen-reader-text" href="#qt-main">
	<?php esc_html_e( 'Skip to content', 'quest' ); ?>
</a>

<div id="qt-page" class="qt-page">

	<?php get_template_part( 'template-parts/header/top-bar' ); ?>
	<?php get_template_part( 'template-parts/header/site-header' ); ?>
	<?php get_template_part( 'template-parts/header/category-nav' ); ?>
	<?php get_template_part( 'template-parts/header/mobile-nav' ); ?>
