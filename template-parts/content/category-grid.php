<?php
defined( 'ABSPATH' ) || exit;

$section_title    = quest_option_text( 'categories_title', 'Popular Categories', false );
$section_subtitle = quest_option_text( 'categories_subtitle', 'Explore our full range of cabling, connectivity, and infrastructure products', false );

$selected_ids = function_exists( 'get_field' ) ? get_field( 'categories_selected', 'option' ) : null;

if ( ! empty( $selected_ids ) && is_array( $selected_ids ) ) {
	$categories = get_terms( [
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'include'    => $selected_ids,
		'orderby'    => 'include',
	] );
} else {
	$categories = get_terms( [
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'exclude'    => get_option( 'default_product_cat' ),
		'number'     => 8,
		'orderby'    => 'count',
		'order'      => 'DESC',
	] );
}

if ( is_wp_error( $categories ) || empty( $categories ) ) {
	return;
}
?>

<section class="qt-section qt-categories">
	<div class="qt-container">
		<div class="qt-section__header">
			<h2 class="qt-section__title"><?php echo esc_html( $section_title ); ?></h2>
			<?php if ( $section_subtitle ) : ?>
				<p class="qt-section__subtitle"><?php echo esc_html( $section_subtitle ); ?></p>
			<?php endif; ?>
		</div>
		<div class="qt-categories__grid">
			<?php foreach ( $categories as $category ) :
				$thumb_url = quest_get_cat_image_url( $category->term_id );
				$link      = get_term_link( $category );
				if ( is_wp_error( $link ) ) continue;

				$children = get_terms( [
					'taxonomy'   => 'product_cat',
					'hide_empty' => true,
					'parent'     => $category->term_id,
					'number'     => 3,
					'orderby'    => 'count',
					'order'      => 'DESC',
				] );
				$child_names = [];
				if ( ! is_wp_error( $children ) ) {
					foreach ( $children as $child ) {
						$child_names[] = $child->name;
					}
				}
			?>
				<a href="<?php echo esc_url( $link ); ?>" class="qt-category-card">
					<div class="qt-category-card__visual">
						<?php if ( $thumb_url ) : ?>
							<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $category->name ); ?>" class="qt-category-card__img" loading="lazy">
						<?php else : ?>
							<div class="qt-category-card__placeholder">
								<?php echo quest_icon( 'grid', 32 ); ?>
							</div>
						<?php endif; ?>
						<span class="qt-category-card__count-badge"><?php echo esc_html( $category->count ); ?> <?php esc_html_e( 'products', 'quest' ); ?></span>
					</div>
					<div class="qt-category-card__info">
						<h3 class="qt-category-card__name"><?php echo esc_html( $category->name ); ?></h3>
						<?php if ( ! empty( $child_names ) ) : ?>
							<p class="qt-category-card__subs"><?php echo esc_html( implode( ', ', $child_names ) ); ?></p>
						<?php endif; ?>
						<span class="qt-category-card__cta">
							<?php esc_html_e( 'Shop Now', 'quest' ); ?>
							<?php echo quest_icon( 'arrow-right', 14 ); ?>
						</span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
		<div class="qt-section__footer">
			<a href="<?php echo esc_url( quest_shop_url() ); ?>" class="qt-btn qt-btn--outline">
				<?php esc_html_e( 'View All Categories', 'quest' ); ?>
				<?php echo quest_icon( 'arrow-right', 16 ); ?>
			</a>
		</div>
	</div>
</section>
