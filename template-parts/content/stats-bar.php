<?php
defined( 'ABSPATH' ) || exit;

$stats = [];

if ( function_exists( 'get_field' ) && get_field( 'stats_items', 'option' ) ) {
	$stats = get_field( 'stats_items', 'option' );
}

if ( empty( $stats ) ) {
	$stats = [
		[ 'stat_number' => '40+',     'stat_label' => 'Years in Business' ],
		[ 'stat_number' => '10,000+', 'stat_label' => 'Products' ],
		[ 'stat_number' => '50+',     'stat_label' => 'States Served' ],
		[ 'stat_number' => '1,000+',  'stat_label' => 'Distributors' ],
	];
}
?>

<section class="qt-stats">
	<div class="qt-container qt-stats__grid">
		<?php foreach ( $stats as $item ) : ?>
			<div class="qt-stats__item">
				<span class="qt-stats__number"><?php echo esc_html( $item['stat_number'] ); ?></span>
				<span class="qt-stats__label"><?php echo esc_html( $item['stat_label'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
