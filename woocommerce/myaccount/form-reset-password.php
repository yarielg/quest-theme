<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="qt-auth">
	<div class="qt-auth__card">

		<div class="qt-auth__panel qt-auth__panel--active">
			<div class="qt-auth__panel-header">
				<h2 class="qt-auth__title"><?php esc_html_e( 'Set New Password', 'quest' ); ?></h2>
				<p class="qt-auth__subtitle"><?php esc_html_e( 'Enter your new password below.', 'quest' ); ?></p>
			</div>

			<?php wc_print_notices(); ?>

			<form method="post" class="qt-auth__form woocommerce-ResetPassword lost_reset_password">

				<div class="qt-auth__field">
					<label for="password_1"><?php esc_html_e( 'New Password', 'quest' ); ?> <span class="qt-required">*</span></label>
					<input type="password" id="password_1" name="password_1" autocomplete="new-password" required minlength="8">
				</div>

				<div class="qt-auth__field">
					<label for="password_2"><?php esc_html_e( 'Confirm New Password', 'quest' ); ?> <span class="qt-required">*</span></label>
					<input type="password" id="password_2" name="password_2" autocomplete="new-password" required minlength="8">
				</div>

				<?php do_action( 'woocommerce_resetpassword_form' ); ?>

				<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>">
				<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>">
				<input type="hidden" name="wc_reset_password" value="true">
				<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>

				<button type="submit" class="qt-btn qt-btn--primary qt-btn--block qt-btn--lg">
					<?php esc_html_e( 'Save Password', 'quest' ); ?>
					<?php echo quest_icon( 'arrow-right', 18 ); ?>
				</button>

			</form>
		</div>

	</div>
</div>
