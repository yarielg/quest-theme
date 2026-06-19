<?php
defined( 'ABSPATH' ) || exit;
?>

<nav class="qt-cat-nav" aria-label="<?php esc_attr_e( 'Main navigation', 'quest' ); ?>">
	<div class="qt-container">
		<?php
		wp_nav_menu( [
			'theme_location' => 'primary',
			'menu_class'     => 'qt-mega-menu',
			'container'      => false,
			'depth'          => 0,
			'fallback_cb'    => false,
			'walker'         => new Quest_Mega_Menu_Walker(),
		] );
		?>
	</div>
</nav>
