<?php
defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// Add B2B fields to user profile in admin
// ---------------------------------------------------------------------------
function quest_admin_user_fields( WP_User $user ): void {
	$status  = get_user_meta( $user->ID, 'quest_account_status', true );
	$company = get_user_meta( $user->ID, 'quest_company', true );

	if ( ! $company && $status !== 'pending' ) return;
	?>
	<h2><?php esc_html_e( 'Dealer Account Information', 'quest' ); ?></h2>
	<table class="form-table">
		<tr>
			<th><label><?php esc_html_e( 'Account Status', 'quest' ); ?></label></th>
			<td>
				<select name="quest_account_status">
					<option value="pending" <?php selected( $status, 'pending' ); ?>><?php esc_html_e( 'Pending Review', 'quest' ); ?></option>
					<option value="approved" <?php selected( $status, 'approved' ); ?>><?php esc_html_e( 'Approved', 'quest' ); ?></option>
					<option value="rejected" <?php selected( $status, 'rejected' ); ?>><?php esc_html_e( 'Rejected', 'quest' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Company', 'quest' ); ?></label></th>
			<td><input type="text" name="quest_company" value="<?php echo esc_attr( $company ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Phone', 'quest' ); ?></label></th>
			<td><input type="text" name="quest_phone" value="<?php echo esc_attr( get_user_meta( $user->ID, 'quest_phone', true ) ); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Address', 'quest' ); ?></label></th>
			<td>
				<?php
				$addr  = get_user_meta( $user->ID, 'quest_address', true );
				$city  = get_user_meta( $user->ID, 'quest_city', true );
				$state = get_user_meta( $user->ID, 'quest_state', true );
				$zip   = get_user_meta( $user->ID, 'quest_zip', true );
				$parts = array_filter( [ $addr, $city, $state . ' ' . $zip ] );
				echo esc_html( $parts ? implode( ', ', $parts ) : '—' );
				?>
			</td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Registered', 'quest' ); ?></label></th>
			<td><?php echo esc_html( get_user_meta( $user->ID, 'quest_registered_date', true ) ?: '—' ); ?></td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'quest_admin_user_fields' );
add_action( 'edit_user_profile', 'quest_admin_user_fields' );

// ---------------------------------------------------------------------------
// Save admin user fields + handle approval
// ---------------------------------------------------------------------------
function quest_save_admin_user_fields( int $user_id ): void {
	if ( ! current_user_can( 'edit_users' ) ) return;
	if ( ! isset( $_POST['quest_account_status'] ) ) return;

	$new_status = sanitize_text_field( $_POST['quest_account_status'] );
	$old_status = get_user_meta( $user_id, 'quest_account_status', true );

	update_user_meta( $user_id, 'quest_account_status', $new_status );

	if ( isset( $_POST['quest_company'] ) ) {
		update_user_meta( $user_id, 'quest_company', sanitize_text_field( $_POST['quest_company'] ) );
	}
	if ( isset( $_POST['quest_phone'] ) ) {
		update_user_meta( $user_id, 'quest_phone', sanitize_text_field( $_POST['quest_phone'] ) );
	}

	// Handle status change
	if ( $old_status !== $new_status ) {
		$user = get_userdata( $user_id );

		if ( $new_status === 'approved' ) {
			$user->set_role( 'quest_dealer' );
			quest_send_approval_email( $user );
		} elseif ( $new_status === 'rejected' ) {
			quest_send_rejection_email( $user );
		} elseif ( $new_status === 'pending' ) {
			$user->set_role( 'quest_pending' );
		}
	}
}
add_action( 'personal_options_update', 'quest_save_admin_user_fields' );
add_action( 'edit_user_profile_update', 'quest_save_admin_user_fields' );

// ---------------------------------------------------------------------------
// Approval email
// ---------------------------------------------------------------------------
function quest_send_approval_email( WP_User $user ): void {
	$site_name = get_bloginfo( 'name' );
	$login_url = quest_account_url();

	$subject = sprintf( '[%s] Your Dealer Account Has Been Approved!', $site_name );

	$message  = "Hi {$user->first_name},\n\n";
	$message .= "Great news! Your dealer account with {$site_name} has been approved.\n\n";
	$message .= "You can now log in to access your dealer pricing, product catalog, and exclusive resources.\n\n";
	$message .= "Log in here: {$login_url}\n\n";
	$message .= "If you have any questions, please don't hesitate to reach out to our team.\n\n";
	$message .= "Welcome aboard!\n";
	$message .= "The {$site_name} Team\n";

	wp_mail( $user->user_email, $subject, $message );
}

// ---------------------------------------------------------------------------
// Rejection email
// ---------------------------------------------------------------------------
function quest_send_rejection_email( WP_User $user ): void {
	$site_name = get_bloginfo( 'name' );

	$subject = sprintf( '[%s] Account Application Update', $site_name );

	$message  = "Hi {$user->first_name},\n\n";
	$message .= "Thank you for your interest in becoming a dealer with {$site_name}.\n\n";
	$message .= "After reviewing your application, we are unable to approve your dealer account at this time. ";
	$message .= "If you believe this is an error or would like more information, please contact us.\n\n";
	$message .= "Best regards,\n";
	$message .= "The {$site_name} Team\n";

	wp_mail( $user->user_email, $subject, $message );
}

// ---------------------------------------------------------------------------
// Add company name column to users list
// ---------------------------------------------------------------------------
function quest_users_columns( array $columns ): array {
	$new = [];
	foreach ( $columns as $key => $val ) {
		$new[ $key ] = $val;
		if ( $key === 'username' ) {
			$new['quest_company'] = __( 'Company', 'quest' );
		}
	}
	return $new;
}
add_filter( 'manage_users_columns', 'quest_users_columns' );

function quest_users_column_content( string $val, string $column, int $user_id ): string {
	if ( $column === 'quest_company' ) {
		return esc_html( get_user_meta( $user_id, 'quest_company', true ) ?: '—' );
	}
	return $val;
}
add_filter( 'manage_users_custom_column', 'quest_users_column_content', 10, 3 );

// ---------------------------------------------------------------------------
// Admin notice for pending users count
// ---------------------------------------------------------------------------
function quest_pending_users_notice(): void {
	$screen = get_current_screen();
	if ( ! $screen || $screen->id !== 'users' ) return;

	$pending = get_users( [ 'role' => 'quest_pending', 'count_total' => true, 'number' => 0 ] );
	$count   = count( $pending );

	if ( $count === 0 ) return;

	$url = admin_url( 'users.php?role=quest_pending' );
	printf(
		'<div class="notice notice-warning"><p><strong>%s</strong> <a href="%s">%s</a></p></div>',
		sprintf( _n( '%d dealer application pending review.', '%d dealer applications pending review.', $count, 'quest' ), $count ),
		esc_url( $url ),
		esc_html__( 'Review now', 'quest' )
	);
}
add_action( 'admin_notices', 'quest_pending_users_notice' );
