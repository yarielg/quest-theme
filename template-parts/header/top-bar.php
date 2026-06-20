<?php
defined( 'ABSPATH' ) || exit;

$phone = quest_option_text( 'company_phone', '(800) 555-0199', false );
$email = quest_option_text( 'company_email', 'info@questtechnologyintl.com', false );
?>

<div class="qt-top-bar">
	<div class="qt-container qt-top-bar__inner">
		<div class="qt-top-bar__left">
			<?php
			$hours = function_exists( 'get_field' ) ? get_field( 'contact_hours', 'option' ) : '';
			if ( ! $hours ) $hours = 'Mon–Fri 8:00 AM – 5:00 PM EST';
			?>
			<span class="qt-top-bar__link">
				<?php echo quest_icon( 'grid', 14 ); ?>
				<span><?php echo esc_html( $hours ); ?></span>
			</span>
		</div>
		<div class="qt-top-bar__right">
			<?php if ( $phone ) : ?>
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>" class="qt-top-bar__link">
					<?php echo quest_icon( 'phone', 14 ); ?>
					<span><?php echo esc_html( $phone ); ?></span>
				</a>
			<?php endif; ?>
			<?php if ( $email ) : ?>
				<a href="mailto:<?php echo esc_attr( $email ); ?>" class="qt-top-bar__link">
					<?php echo quest_icon( 'mail', 14 ); ?>
					<span><?php echo esc_html( $email ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>
