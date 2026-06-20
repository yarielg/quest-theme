<?php
defined( 'ABSPATH' ) || exit;

class Quest_Mega_Menu_Walker extends Walker_Nav_Menu {

	private $item_has_children = false;
	private $is_products       = false;

	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		$this->item_has_children = ! empty( $children_elements[ $element->ID ] );

		$url      = $element->url ?? '';
		$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		$archive_url = get_post_type_archive_link( 'product' );
		$this->is_products = (
			untrailingslashit( $url ) === untrailingslashit( $shop_url )
			|| ( $archive_url && untrailingslashit( $url ) === untrailingslashit( $archive_url ) )
		);

		// For Products: remove children so WP doesn't render them
		if ( $this->is_products && $depth === 0 ) {
			unset( $children_elements[ $element->ID ] );
			$this->item_has_children = true;
		}

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes   = empty( $item->classes ) ? [] : (array) $item->classes;
		$classes[] = 'qt-mega-menu__item';

		if ( $depth === 0 ) {
			if ( $this->item_has_children || $this->is_products ) {
				$classes[] = 'qt-mega-menu__item--has-mega';
			}
			if ( $this->is_products ) {
				$classes[] = 'qt-mega-menu__item--products';
			}
		}

		$output .= '<li class="' . esc_attr( implode( ' ', array_filter( $classes ) ) ) . '">';

		$link_class = $depth === 0 ? 'qt-mega-menu__link' : 'qt-mega-menu__sub-link';

		$atts = [
			'href'  => ! empty( $item->url ) ? $item->url : '',
			'class' => $link_class,
		];

		if ( $depth === 0 && ( $this->item_has_children || $this->is_products ) ) {
			$atts['aria-haspopup'] = 'true';
			$atts['aria-expanded'] = 'false';
		}

		$attrs = '';
		foreach ( $atts as $attr => $val ) {
			$attrs .= ' ' . $attr . '="' . esc_attr( $val ) . '"';
		}

		$output .= '<a' . $attrs . '>';
		$output .= esc_html( $item->title );

		if ( $depth === 0 && ( $this->item_has_children || $this->is_products ) ) {
			$output .= ' ' . quest_icon( 'chevron-down', 12 );
		}

		$output .= '</a>';

		if ( $depth === 0 && $this->is_products ) {
			$output .= $this->render_product_categories();
		}
	}

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( $depth === 0 ) {
			$output .= '<div class="qt-mega-menu__panel"><div class="qt-container"><div class="qt-mega-menu__panel-inner">';
			$output .= '<div class="qt-mega-menu__col"><ul class="qt-mega-menu__sub-list">';
		} else {
			$output .= '<ul class="qt-mega-menu__sub-list qt-mega-menu__sub-list--nested">';
		}
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( $depth === 0 ) {
			$output .= '</ul></div></div></div></div>';
		} else {
			$output .= '</ul>';
		}
	}

	private function render_product_categories(): string {
		$parents = get_terms( [
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => 0,
			'exclude'    => get_option( 'default_product_cat' ),
			'orderby'    => 'menu_order',
			'order'      => 'ASC',
			'number'     => 12,
		] );

		if ( is_wp_error( $parents ) || empty( $parents ) ) {
			return '';
		}

		$html  = '<div class="qt-mega-menu__panel"><div class="qt-container"><div class="qt-mega-menu__panel-inner">';
		$html .= '<div class="qt-mega-menu__cat-columns">';

		foreach ( $parents as $cat ) {
			$link = get_term_link( $cat );
			if ( is_wp_error( $link ) ) continue;

			$children = get_terms( [
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
				'parent'     => $cat->term_id,
				'orderby'    => 'menu_order',
				'order'      => 'ASC',
				'number'     => 8,
			] );
			$has_children = ! is_wp_error( $children ) && ! empty( $children );

			$html .= '<div class="qt-mega-menu__cat-col">';

			$html .= '<a href="' . esc_url( $link ) . '" class="qt-mega-menu__cat-header">';
			$html .= '<span class="qt-mega-menu__cat-title">' . esc_html( $cat->name ) . '</span>';
			$html .= '</a>';

			if ( $has_children ) {
				$html .= '<ul class="qt-mega-menu__cat-subs">';
				foreach ( $children as $child ) {
					$child_link = get_term_link( $child );
					if ( is_wp_error( $child_link ) ) continue;
					$html .= '<li><a href="' . esc_url( $child_link ) . '">' . esc_html( $child->name ) . '</a></li>';
				}
				$html .= '<li class="qt-mega-menu__cat-view-all"><a href="' . esc_url( $link ) . '">';
				$html .= esc_html__( 'View All', 'quest' ) . ' ' . quest_icon( 'arrow-right', 12 );
				$html .= '</a></li>';
				$html .= '</ul>';
			} else {
				$products = function_exists( 'wc_get_products' ) ? wc_get_products( [
					'category' => [ $cat->slug ],
					'status'   => 'publish',
					'limit'    => 5,
					'orderby'  => 'date',
					'order'    => 'DESC',
				] ) : [];

				if ( ! empty( $products ) ) {
					$html .= '<ul class="qt-mega-menu__cat-subs">';
					foreach ( $products as $product ) {
						$html .= '<li><a href="' . esc_url( $product->get_permalink() ) . '">' . esc_html( $product->get_name() ) . '</a></li>';
					}
					$html .= '<li class="qt-mega-menu__cat-view-all"><a href="' . esc_url( $link ) . '">';
					$html .= esc_html__( 'View All', 'quest' ) . ' ' . quest_icon( 'arrow-right', 12 );
					$html .= '</a></li>';
					$html .= '</ul>';
				}
			}

			$html .= '</div>';
		}

		$html .= '</div>';
		$html .= '</div></div></div>';

		return $html;
	}
}
