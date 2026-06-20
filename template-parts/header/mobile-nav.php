<?php
defined( 'ABSPATH' ) || exit;

$home_url    = home_url( '/' );
$account_url = quest_account_url();
$shop_url    = quest_shop_url();
$cart_url    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '';
?>

<div
	id="qt-mobile-menu"
	class="qt-mobile-menu"
	aria-hidden="true"
	role="dialog"
	aria-label="<?php esc_attr_e( 'Navigation menu', 'quest' ); ?>"
>
	<div class="qt-mobile-menu__head">
		<div class="qt-mobile-menu__logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<span><?php bloginfo( 'name' ); ?></span>
			<?php endif; ?>
		</div>
		<button
			type="button"
			class="qt-mobile-menu__close js-mobile-close"
			aria-label="<?php esc_attr_e( 'Close menu', 'quest' ); ?>"
		><?php echo quest_icon( 'close', 22 ); ?></button>
	</div>

	<div class="qt-mobile-menu__search">
		<form role="search" method="get" action="<?php echo esc_url( $shop_url ); ?>" class="qt-mobile-search-form" data-component="ajax-search">
			<label for="qt-search-mobile" class="screen-reader-text"><?php esc_html_e( 'Search products', 'quest' ); ?></label>
			<div class="qt-mobile-search-wrap">
				<input
					id="qt-search-mobile"
					type="search"
					name="s"
					placeholder="<?php esc_attr_e( 'Search products, SKUs...', 'quest' ); ?>"
					autocomplete="off"
				>
				<input type="hidden" name="post_type" value="product">
				<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'quest' ); ?>">
					<?php echo quest_icon( 'search', 18 ); ?>
				</button>
			</div>
			<div class="qt-search-results qt-search-results--mobile" aria-live="polite" hidden></div>
		</form>
	</div>

	<nav class="qt-mobile-menu__nav" aria-label="<?php esc_attr_e( 'Mobile navigation', 'quest' ); ?>">
		<?php
		wp_nav_menu( [
			'theme_location' => 'primary',
			'menu_class'     => 'qt-mobile-nav-list',
			'container'      => false,
			'depth'          => 2,
			'fallback_cb'    => false,
		] );
		?>
	</nav>

	<div class="qt-mobile-menu__footer">
		<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( $account_url ); ?>" class="qt-btn qt-btn--outline qt-btn--block">
				<?php echo quest_icon( 'account', 18 ); ?>
				<?php esc_html_e( 'My Account', 'quest' ); ?>
			</a>
		<?php else : ?>
			<a href="<?php echo esc_url( $account_url ); ?>" class="qt-btn qt-btn--outline qt-btn--block">
				<?php echo quest_icon( 'account', 18 ); ?>
				<?php esc_html_e( 'Sign In', 'quest' ); ?>
			</a>
			<a href="<?php echo esc_url( $account_url . '?action=register' ); ?>" class="qt-btn qt-btn--primary qt-btn--block">
				<?php esc_html_e( 'Request Access', 'quest' ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>

<div class="qt-mobile-menu__backdrop js-mobile-backdrop" aria-hidden="true"></div>

<nav class="qt-mobile-bottom-bar" aria-label="<?php esc_attr_e( 'Quick navigation', 'quest' ); ?>">
	<a href="<?php echo esc_url( $home_url ); ?>" class="qt-mobile-bottom-bar__item">
		<?php echo quest_icon( 'home', 20 ); ?>
		<span><?php esc_html_e( 'Home', 'quest' ); ?></span>
	</a>
	<a href="<?php echo esc_url( $shop_url ); ?>" class="qt-mobile-bottom-bar__item">
		<?php echo quest_icon( 'grid', 20 ); ?>
		<span><?php esc_html_e( 'Products', 'quest' ); ?></span>
	</a>
	<button type="button" class="qt-mobile-bottom-bar__item js-mobile-toggle">
		<?php echo quest_icon( 'search', 20 ); ?>
		<span><?php esc_html_e( 'Search', 'quest' ); ?></span>
	</button>
	<?php if ( $cart_url && ! quest_is_catalog_mode() ) : ?>
		<a href="<?php echo esc_url( $cart_url ); ?>" class="qt-mobile-bottom-bar__item">
			<?php echo quest_icon( 'cart', 20 ); ?>
			<span><?php esc_html_e( 'Cart', 'quest' ); ?></span>
		</a>
	<?php endif; ?>
	<a href="<?php echo esc_url( $account_url ); ?>" class="qt-mobile-bottom-bar__item">
		<?php echo quest_icon( 'account', 20 ); ?>
		<span><?php esc_html_e( 'Account', 'quest' ); ?></span>
	</a>
</nav>
