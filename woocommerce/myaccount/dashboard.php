<?php
defined( 'ABSPATH' ) || exit;

$user       = wp_get_current_user();
$is_pending = in_array( 'quest_pending', (array) $user->roles, true );
$is_dealer  = in_array( 'quest_dealer', (array) $user->roles, true );
$company    = get_user_meta( $user->ID, 'quest_company', true );
$status     = get_user_meta( $user->ID, 'quest_account_status', true );
$price_list = get_user_meta( $user->ID, 'price_list', true );
?>

<div class="qt-dashboard">

	<?php if ( $is_pending ) : ?>
		<!-- Pending State -->
		<div class="qt-dashboard__pending">
			<div class="qt-dashboard__pending-icon">
				<?php echo quest_icon( 'certified', 48 ); ?>
			</div>
			<h2><?php esc_html_e( 'Account Under Review', 'quest' ); ?></h2>
			<p><?php esc_html_e( 'Your dealer application is being reviewed by our team. You will receive an email notification once your account has been approved.', 'quest' ); ?></p>
			<div class="qt-dashboard__pending-details">
				<div class="qt-dashboard__pending-row">
					<span><?php esc_html_e( 'Company', 'quest' ); ?></span>
					<strong><?php echo esc_html( $company ); ?></strong>
				</div>
				<div class="qt-dashboard__pending-row">
					<span><?php esc_html_e( 'Email', 'quest' ); ?></span>
					<strong><?php echo esc_html( $user->user_email ); ?></strong>
				</div>
				<div class="qt-dashboard__pending-row">
					<span><?php esc_html_e( 'Status', 'quest' ); ?></span>
					<span class="qt-badge qt-badge--pending"><?php esc_html_e( 'Pending Review', 'quest' ); ?></span>
				</div>
			</div>
		</div>

	<?php else : ?>
		<!-- Approved Dashboard -->
		<div class="qt-dashboard__welcome">
			<div class="qt-dashboard__welcome-text">
				<h2><?php printf( esc_html__( 'Welcome back, %s', 'quest' ), esc_html( $user->first_name ) ); ?></h2>
				<?php if ( $company ) : ?>
					<p class="qt-dashboard__company"><?php echo esc_html( $company ); ?></p>
				<?php endif; ?>
			</div>
			<div class="qt-dashboard__welcome-status">
				<span class="qt-badge qt-badge--active"><?php esc_html_e( 'Active Dealer', 'quest' ); ?></span>
			</div>
		</div>

		<div class="qt-dashboard__grid">

			<a href="<?php echo esc_url( quest_shop_url() ); ?>" class="qt-dashboard__card">
				<div class="qt-dashboard__card-icon"><?php echo quest_icon( 'grid', 28 ); ?></div>
				<h3><?php esc_html_e( 'Browse Products', 'quest' ); ?></h3>
				<p><?php esc_html_e( 'Explore our full catalog with your dealer pricing.', 'quest' ); ?></p>
			</a>

			<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'company-details' ) ); ?>" class="qt-dashboard__card">
				<div class="qt-dashboard__card-icon"><?php echo quest_icon( 'account', 28 ); ?></div>
				<h3><?php esc_html_e( 'Company Details', 'quest' ); ?></h3>
				<p><?php esc_html_e( 'View and update your company information.', 'quest' ); ?></p>
			</a>

			<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'resources' ) ); ?>" class="qt-dashboard__card">
				<div class="qt-dashboard__card-icon"><?php echo quest_icon( 'catalog', 28 ); ?></div>
				<h3><?php esc_html_e( 'Resources', 'quest' ); ?></h3>
				<p><?php esc_html_e( 'Access brochures, catalogs, and marketing materials.', 'quest' ); ?></p>
			</a>

			<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>" class="qt-dashboard__card">
				<div class="qt-dashboard__card-icon"><?php echo quest_icon( 'mail', 28 ); ?></div>
				<h3><?php esc_html_e( 'Account Settings', 'quest' ); ?></h3>
				<p><?php esc_html_e( 'Update your password and account details.', 'quest' ); ?></p>
			</a>

		</div>
	<?php endif; ?>

</div>
