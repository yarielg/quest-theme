<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="qt-main" class="qt-main">
	<div class="qt-container">

		<header class="qt-page-header">
			<h1 class="qt-page-title">
				<?php printf( esc_html__( 'Search results for: %s', 'quest' ), '<span>' . get_search_query() . '</span>' ); ?>
			</h1>
		</header>

		<?php if ( have_posts() ) : ?>
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
				<h2><?php esc_html_e( 'Nothing found', 'quest' ); ?></h2>
				<p><?php esc_html_e( 'Sorry, no results matched your search. Try different keywords.', 'quest' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>

	</div>
</main>

<?php
get_footer();
