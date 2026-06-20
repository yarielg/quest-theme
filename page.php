<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="qt-main" class="qt-main">
	<?php get_template_part( 'template-parts/content/breadcrumb' ); ?>
	<div class="qt-container">

		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'qt-page-content' ); ?>>
				<header class="qt-page-header">
					<h1 class="qt-page-title"><?php the_title(); ?></h1>
				</header>
				<div class="qt-entry-content">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>

	</div>
</main>

<?php
get_footer();
