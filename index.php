<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="qt-main" class="qt-main">
	<div class="qt-container">

		<?php if ( have_posts() ) : ?>

			<header class="qt-page-header">
				<?php if ( is_home() && ! is_front_page() ) : ?>
					<h1 class="qt-page-title"><?php single_post_title(); ?></h1>
				<?php elseif ( is_archive() ) : ?>
					<?php the_archive_title( '<h1 class="qt-page-title">', '</h1>' ); ?>
					<?php the_archive_description( '<div class="qt-page-description">', '</div>' ); ?>
				<?php elseif ( is_search() ) : ?>
					<h1 class="qt-page-title">
						<?php printf( esc_html__( 'Search results for: %s', 'quest' ), '<span>' . get_search_query() . '</span>' ); ?>
					</h1>
				<?php endif; ?>
			</header>

			<div class="qt-posts-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'qt-post-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="qt-post-card__thumb">
								<?php the_post_thumbnail( 'medium_large' ); ?>
							</a>
						<?php endif; ?>
						<div class="qt-post-card__content">
							<h2 class="qt-post-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<div class="qt-post-card__excerpt">
								<?php the_excerpt(); ?>
							</div>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<?php the_posts_pagination( [
				'mid_size'  => 2,
				'prev_text' => esc_html__( 'Previous', 'quest' ),
				'next_text' => esc_html__( 'Next', 'quest' ),
			] ); ?>

		<?php else : ?>
			<div class="qt-no-content">
				<h1><?php esc_html_e( 'Nothing found', 'quest' ); ?></h1>
				<p><?php esc_html_e( 'It seems we can\'t find what you\'re looking for.', 'quest' ); ?></p>
			</div>
		<?php endif; ?>

	</div>
</main>

<?php
get_footer();
