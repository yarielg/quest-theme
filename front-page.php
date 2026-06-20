<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="qt-main" class="qt-main qt-main--home">

	<?php get_template_part( 'template-parts/content/hero-banner' ); ?>
	<?php get_template_part( 'template-parts/content/category-grid' ); ?>
	<?php get_template_part( 'template-parts/content/product-tabs' ); ?>
	<?php get_template_part( 'template-parts/content/cta-section' ); ?>
	<?php get_template_part( 'template-parts/content/why-quest' ); ?>
	<?php get_template_part( 'template-parts/content/new-products' ); ?>
	<?php get_template_part( 'template-parts/content/partner-logos' ); ?>

</main>

<?php
get_footer();
