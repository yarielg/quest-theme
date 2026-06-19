<?php
defined( 'ABSPATH' ) || exit;

$user    = wp_get_current_user();
$company = get_user_meta( $user->ID, 'quest_company', true );
$phone   = get_user_meta( $user->ID, 'quest_phone', true );
$address = get_user_meta( $user->ID, 'quest_address', true );
$city    = get_user_meta( $user->ID, 'quest_city', true );
$state   = get_user_meta( $user->ID, 'quest_state', true );
$zip     = get_user_meta( $user->ID, 'quest_zip', true );
?>

<div class="qt-account-section">
	<h2 class="qt-account-section__title"><?php esc_html_e( 'Company Details', 'quest' ); ?></h2>

	<div class="qt-account-details">
		<div class="qt-account-details__grid">
			<div class="qt-account-details__item">
				<span class="qt-account-details__label"><?php esc_html_e( 'Company Name', 'quest' ); ?></span>
				<span class="qt-account-details__value"><?php echo esc_html( $company ?: '—' ); ?></span>
			</div>
			<div class="qt-account-details__item">
				<span class="qt-account-details__label"><?php esc_html_e( 'Contact', 'quest' ); ?></span>
				<span class="qt-account-details__value"><?php echo esc_html( $user->first_name . ' ' . $user->last_name ); ?></span>
			</div>
			<div class="qt-account-details__item">
				<span class="qt-account-details__label"><?php esc_html_e( 'Email', 'quest' ); ?></span>
				<span class="qt-account-details__value"><?php echo esc_html( $user->user_email ); ?></span>
			</div>
			<div class="qt-account-details__item">
				<span class="qt-account-details__label"><?php esc_html_e( 'Phone', 'quest' ); ?></span>
				<span class="qt-account-details__value"><?php echo esc_html( $phone ?: '—' ); ?></span>
			</div>
			<div class="qt-account-details__item qt-account-details__item--full">
				<span class="qt-account-details__label"><?php esc_html_e( 'Address', 'quest' ); ?></span>
				<span class="qt-account-details__value">
					<?php
					$parts = array_filter( [ $address, $city, $state . ' ' . $zip ] );
					echo esc_html( $parts ? implode( ', ', $parts ) : '—' );
					?>
				</span>
			</div>
		</div>
	</div>

	<p class="qt-account-section__note"><?php esc_html_e( 'To update your company information, please contact our sales team.', 'quest' ); ?></p>
</div>
