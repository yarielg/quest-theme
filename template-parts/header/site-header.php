<?php
defined( 'ABSPATH' ) || exit;

$home_url    = home_url( '/' );
$shop_url    = quest_shop_url();
$account_url = quest_account_url();
$cart_url    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '';
$cart_count  = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>

<header id="qt-header" class="qt-header" role="banner">
	<div class="qt-container qt-header__inner">

		<button
			type="button"
			class="qt-header__icon-btn qt-header__hamburger js-mobile-toggle"
			aria-label="<?php esc_attr_e( 'Open menu', 'quest' ); ?>"
			aria-expanded="false"
			aria-controls="qt-mobile-menu"
		><?php echo quest_icon( 'menu', 24 ); ?></button>

		<div class="qt-header__logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( $home_url ); ?>" rel="home" class="qt-header__site-name">
					<?php bloginfo( 'name' ); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="qt-header__search">
			<form role="search" method="get" action="<?php echo esc_url( $shop_url ); ?>" class="qt-header-search" data-component="ajax-search">
				<label for="qt-header-search-input" class="screen-reader-text"><?php esc_html_e( 'Search products', 'quest' ); ?></label>
				<div class="qt-header-search__wrap">
					<input
						id="qt-header-search-input"
						type="search"
						name="s"
						placeholder="<?php esc_attr_e( 'Search products, SKUs, categories...', 'quest' ); ?>"
						autocomplete="off"
						value="<?php echo get_search_query(); ?>"
					>
					<input type="hidden" name="post_type" value="product">
					<button type="submit" class="qt-header-search__btn" aria-label="<?php esc_attr_e( 'Search', 'quest' ); ?>">
						<?php echo quest_icon( 'search', 20 ); ?>
					</button>
				</div>
				<div class="qt-search-results" aria-live="polite" hidden></div>
			</form>
		</div>

		<div class="qt-header__actions">
			<a
				href="<?php echo esc_url( $shop_url . '?s=' ); ?>"
				class="qt-header__icon-btn qt-header__search-toggle"
				aria-label="<?php esc_attr_e( 'Search products', 'quest' ); ?>"
			><?php echo quest_icon( 'search', 22 ); ?></a>

			<div class="qt-header__account-wrap">
				<button type="button" class="qt-header__account-link js-account-toggle" aria-expanded="false" aria-controls="qt-account-dropdown">
					<?php echo quest_icon( 'account', 22 ); ?>
					<span><?php echo is_user_logged_in() ? esc_html__( 'My Account', 'quest' ) : esc_html__( 'Account', 'quest' ); ?></span>
					<?php echo quest_icon( 'chevron-down', 12 ); ?>
				</button>
				<div id="qt-account-dropdown" class="qt-header__account-dropdown" hidden>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( $account_url ); ?>" class="qt-header__account-item">
							<?php echo quest_icon( 'account', 16 ); ?>
							<?php esc_html_e( 'Dashboard', 'quest' ); ?>
						</a>
						<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>" class="qt-header__account-item">
							<?php echo quest_icon( 'mail', 16 ); ?>
							<?php esc_html_e( 'Account Settings', 'quest' ); ?>
						</a>
						<div class="qt-header__account-sep"></div>
						<a href="<?php echo esc_url( wc_logout_url() ); ?>" class="qt-header__account-item qt-header__account-item--logout">
							<?php echo quest_icon( 'close', 16 ); ?>
							<?php esc_html_e( 'Log Out', 'quest' ); ?>
						</a>
					<?php else : ?>
						<a href="<?php echo esc_url( $account_url ); ?>" class="qt-header__account-item">
							<?php echo quest_icon( 'account', 16 ); ?>
							<?php esc_html_e( 'Sign In', 'quest' ); ?>
						</a>
						<a href="<?php echo esc_url( $account_url . '?action=register' ); ?>" class="qt-header__account-item">
							<?php echo quest_icon( 'arrow-right', 16 ); ?>
							<?php esc_html_e( 'Become a Dealer', 'quest' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $cart_url && ! quest_is_catalog_mode() ) : ?>
				<a
					href="<?php echo esc_url( $cart_url ); ?>"
					class="qt-header__icon-btn qt-header__cart"
					aria-label="<?php esc_attr_e( 'Shopping cart', 'quest' ); ?>"
				>
					<?php echo quest_icon( 'cart', 22 ); ?>
					<?php if ( $cart_count > 0 ) : ?>
						<span class="qt-header__cart-count"><?php echo esc_html( $cart_count ); ?></span>
					<?php endif; ?>
				</a>
			<?php endif; ?>
		</div>

	</div>
</header>
