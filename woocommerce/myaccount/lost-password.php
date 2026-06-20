<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="qt-auth">
	<div class="qt-auth__card">

		<div class="qt-auth__panel qt-auth__panel--active">
			<div class="qt-auth__panel-header">
				<h2 class="qt-auth__title"><?php esc_html_e( 'Reset Your Password', 'quest' ); ?></h2>
				<p class="qt-auth__subtitle"><?php esc_html_e( 'Enter your email address and we\'ll send you a link to reset your password.', 'quest' ); ?></p>
			</div>

			<?php wc_print_notices(); ?>

			<form method="post" class="qt-auth__form woocommerce-ResetPassword lost_reset_password">

				<div class="qt-auth__field">
					<label for="user_login"><?php esc_html_e( 'Email Address', 'quest' ); ?></label>
					<input type="email" id="user_login" name="user_login" autocomplete="email" required>
				</div>

				<?php do_action( 'woocommerce_lostpassword_form' ); ?>

				<input type="hidden" name="wc_reset_password" value="true">
				<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

				<button type="submit" class="qt-btn qt-btn--primary qt-btn--block qt-btn--lg" value="<?php esc_attr_e( 'Reset password', 'quest' ); ?>">
					<?php esc_html_e( 'Send Reset Link', 'quest' ); ?>
					<?php echo quest_icon( 'arrow-right', 18 ); ?>
				</button>

			</form>

			<div class="qt-auth__switch">
				<p><?php esc_html_e( 'Remember your password?', 'quest' ); ?>
					<a href="<?php echo esc_url( quest_account_url() ); ?>" class="qt-auth__switch-link"><?php esc_html_e( 'Sign in', 'quest' ); ?></a>
				</p>
			</div>
		</div>

	</div>
</div>
