<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="qt-main" class="qt-main">
	<div class="qt-container">

		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'qt-single-post' ); ?>>
				<header class="qt-post-header">
					<h1 class="qt-post-title"><?php the_title(); ?></h1>
					<div class="qt-post-meta">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					</div>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="qt-post-thumbnail">
						<?php the_post_thumbnail( 'large' ); ?>
					</div>
				<?php endif; ?>

				<div class="qt-entry-content">
					<?php the_content(); ?>
				</div>
			</article>

			<?php
			the_post_navigation( [
				'prev_text' => '<span class="qt-nav-label">' . esc_html__( 'Previous', 'quest' ) . '</span> %title',
				'next_text' => '<span class="qt-nav-label">' . esc_html__( 'Next', 'quest' ) . '</span> %title',
			] );
			?>

		<?php endwhile; ?>

	</div>
</main>

<?php
get_footer();
