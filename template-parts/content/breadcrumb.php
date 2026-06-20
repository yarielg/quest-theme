<?php
defined( 'ABSPATH' ) || exit;

if ( is_front_page() ) return;
?>

<div class="qt-page-breadcrumb">
	<div class="qt-container">
		<nav class="qt-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'quest' ); ?>">
			<ol class="qt-breadcrumb__list">
				<li class="qt-breadcrumb__item">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'quest' ); ?></a>
				</li>
				<?php if ( is_page() ) : ?>
					<?php
					$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
					foreach ( $ancestors as $ancestor_id ) :
					?>
						<li class="qt-breadcrumb__item">
							<a href="<?php echo esc_url( get_permalink( $ancestor_id ) ); ?>"><?php echo esc_html( get_the_title( $ancestor_id ) ); ?></a>
						</li>
					<?php endforeach; ?>
					<li class="qt-breadcrumb__item"><?php the_title(); ?></li>
				<?php elseif ( is_single() ) : ?>
					<?php
					$cats = get_the_category();
					if ( ! empty( $cats ) ) :
					?>
						<li class="qt-breadcrumb__item">
							<a href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>"><?php echo esc_html( $cats[0]->name ); ?></a>
						</li>
					<?php endif; ?>
					<li class="qt-breadcrumb__item"><?php the_title(); ?></li>
				<?php elseif ( is_archive() ) : ?>
					<li class="qt-breadcrumb__item"><?php the_archive_title(); ?></li>
				<?php elseif ( is_search() ) : ?>
					<li class="qt-breadcrumb__item"><?php printf( esc_html__( 'Search: %s', 'quest' ), get_search_query() ); ?></li>
				<?php elseif ( is_404() ) : ?>
					<li class="qt-breadcrumb__item"><?php esc_html_e( '404', 'quest' ); ?></li>
				<?php endif; ?>
			</ol>
		</nav>
	</div>
</div>
