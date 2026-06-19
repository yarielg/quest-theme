<?php
defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// ACF Options pages
// ---------------------------------------------------------------------------
function quest_register_acf_options(): void {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page( [
		'page_title'  => 'Quest Theme Settings',
		'menu_title'  => 'Theme Settings',
		'menu_slug'   => 'quest-theme-settings',
		'capability'  => 'manage_options',
		'redirect'    => false,
		'icon_url'    => 'dashicons-admin-customizer',
		'position'    => 61,
	] );

	$sub_pages = [
		'Brand & Colors' => 'quest-brand-colors',
		'Contact Info'   => 'quest-contact',
		'Homepage'       => 'quest-homepage',
		'Footer'         => 'quest-footer',
	];

	foreach ( $sub_pages as $title => $slug ) {
		acf_add_options_sub_page( [
			'page_title'  => $title,
			'menu_title'  => $title,
			'menu_slug'   => $slug,
			'parent_slug' => 'quest-theme-settings',
			'capability'  => 'manage_options',
		] );
	}
}
add_action( 'acf/init', 'quest_register_acf_options' );

// ---------------------------------------------------------------------------
// ACF field groups — registered in PHP for version control
// ---------------------------------------------------------------------------
function quest_register_acf_fields(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// -----------------------------------------------------------------------
	// Global Settings (main Theme Settings page)
	// -----------------------------------------------------------------------
	acf_add_local_field_group( [
		'key'      => 'group_quest_global',
		'title'    => 'Global Settings',
		'fields'   => [
			[
				'key'           => 'field_catalog_mode',
				'label'         => 'Catalog Mode',
				'name'          => 'catalog_mode',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => 'Enabled',
				'ui_off_text'   => 'Disabled',
				'instructions'  => 'When enabled: hides prices, removes add-to-cart buttons, and disables the cart/checkout. The site acts as a product catalog only.',
			],
			[
				'key'          => 'field_notification_email',
				'label'        => 'Notification Email',
				'name'         => 'notification_email',
				'type'         => 'email',
				'instructions' => 'Email address that receives new dealer application notifications. Defaults to the WordPress admin email if left empty.',
			],
		],
		'location' => [ [ [
			'param'    => 'options_page',
			'operator' => '==',
			'value'    => 'quest-theme-settings',
		] ] ],
	] );

	// -----------------------------------------------------------------------
	// Product Category — card image
	// -----------------------------------------------------------------------
	acf_add_local_field_group( [
		'key'      => 'group_quest_product_cat',
		'title'    => 'Category Card Image',
		'fields'   => [
			[
				'key'           => 'field_cat_card_image',
				'label'         => 'Card Image',
				'name'          => 'cat_card_image',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Image used for this category wherever a category card is displayed (homepage, navigation, etc.). Recommended size: 480x360px.',
			],
		],
		'location' => [ [ [
			'param'    => 'taxonomy',
			'operator' => '==',
			'value'    => 'product_cat',
		] ] ],
	] );

	// -----------------------------------------------------------------------
	// Contact Info (used in top bar + footer)
	// -----------------------------------------------------------------------
	acf_add_local_field_group( [
		'key'      => 'group_quest_contact',
		'title'    => 'Contact Information',
		'fields'   => [
			[
				'key'   => 'field_company_phone',
				'label' => 'Phone Number',
				'name'  => 'company_phone',
				'type'  => 'text',
				'default_value' => '(800) 555-0199',
			],
			[
				'key'   => 'field_company_email',
				'label' => 'Email Address',
				'name'  => 'company_email',
				'type'  => 'email',
				'default_value' => 'info@questtechnologyintl.com',
			],
			[
				'key'   => 'field_company_address',
				'label' => 'Address',
				'name'  => 'company_address',
				'type'  => 'textarea',
				'rows'  => 3,
				'instructions' => 'Company address displayed in the footer.',
			],
			[
				'key'        => 'field_social_links',
				'label'      => 'Social Media Links',
				'name'       => 'social_links',
				'type'       => 'group',
				'layout'     => 'block',
				'sub_fields' => [
					[
						'key'     => 'field_social_facebook',
						'label'   => 'Facebook URL',
						'name'    => 'facebook',
						'type'    => 'url',
						'wrapper' => [ 'width' => '50' ],
					],
					[
						'key'     => 'field_social_linkedin',
						'label'   => 'LinkedIn URL',
						'name'    => 'linkedin',
						'type'    => 'url',
						'wrapper' => [ 'width' => '50' ],
					],
					[
						'key'     => 'field_social_youtube',
						'label'   => 'YouTube URL',
						'name'    => 'youtube',
						'type'    => 'url',
						'wrapper' => [ 'width' => '50' ],
					],
					[
						'key'     => 'field_social_instagram',
						'label'   => 'Instagram URL',
						'name'    => 'instagram',
						'type'    => 'url',
						'wrapper' => [ 'width' => '50' ],
					],
				],
			],
		],
		'location' => [ [ [
			'param'    => 'options_page',
			'operator' => '==',
			'value'    => 'quest-contact',
		] ] ],
	] );

	// -----------------------------------------------------------------------
	// Brand & Colors
	// -----------------------------------------------------------------------
	acf_add_local_field_group( [
		'key'      => 'group_quest_brand',
		'title'    => 'Brand Colors',
		'fields'   => [
			[
				'key'   => 'field_brand_color_primary',
				'label' => 'Primary Color',
				'name'  => 'brand_color_primary',
				'type'  => 'color_picker',
				'default_value' => '#CC0000',
			],
			[
				'key'   => 'field_brand_color_secondary',
				'label' => 'Secondary Color',
				'name'  => 'brand_color_secondary',
				'type'  => 'color_picker',
				'default_value' => '#1A1A2E',
			],
			[
				'key'   => 'field_brand_color_dark',
				'label' => 'Dark Color',
				'name'  => 'brand_color_dark',
				'type'  => 'color_picker',
				'default_value' => '#111111',
			],
		],
		'location' => [ [ [
			'param'    => 'options_page',
			'operator' => '==',
			'value'    => 'quest-brand-colors',
		] ] ],
	] );

	// -----------------------------------------------------------------------
	// Homepage — Hero Slides
	// -----------------------------------------------------------------------
	acf_add_local_field_group( [
		'key'      => 'group_quest_homepage',
		'title'    => 'Homepage Settings',
		'fields'   => [
			// Hero
			[
				'key'          => 'field_hero_slides',
				'label'        => 'Hero Slides',
				'name'         => 'hero_slides',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add Slide',
				'max'          => 3,
				'instructions' => 'Up to 3 slides. Each slide has a full background image with text overlay on the left.',
				'sub_fields'   => [
					[
						'key'           => 'field_hero_bg_image',
						'label'         => 'Background Image',
						'name'          => 'bg_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'required'      => 1,
						'instructions'  => 'Full-width background image. Recommended: 1920x800px minimum.',
					],
					[
						'key'   => 'field_hero_bg_color',
						'label' => 'Overlay Color',
						'name'  => 'bg_color',
						'type'  => 'color_picker',
						'instructions' => 'Optional tint over the background image. Leave empty for default dark overlay.',
						'wrapper' => [ 'width' => '50' ],
					],
					[
						'key'   => 'field_hero_overlay_opacity',
						'label' => 'Overlay Opacity',
						'name'  => 'overlay_opacity',
						'type'  => 'range',
						'min'   => 0,
						'max'   => 100,
						'step'  => 5,
						'default_value' => 60,
						'append' => '%',
						'wrapper' => [ 'width' => '25' ],
					],
					[
						'key'     => 'field_hero_bg_position',
						'label'   => 'Image Focus',
						'name'    => 'bg_position',
						'type'    => 'select',
						'choices' => [
							'center center' => 'Center (default)',
							'center top'    => 'Top',
							'center bottom' => 'Bottom',
							'left center'   => 'Left',
							'right center'  => 'Right',
							'left top'      => 'Top Left',
							'right top'     => 'Top Right',
							'left bottom'   => 'Bottom Left',
							'right bottom'  => 'Bottom Right',
						],
						'default_value' => 'center center',
						'instructions'  => 'Which part of the image to keep visible when cropped.',
						'wrapper' => [ 'width' => '25' ],
					],
					[
						'key'   => 'field_hero_label',
						'label' => 'Eyebrow Text',
						'name'  => 'label',
						'type'  => 'text',
						'instructions' => 'Small text above headline (e.g. "HIGH PERFORMANCE")',
					],
					[
						'key'      => 'field_hero_headline',
						'label'    => 'Headline',
						'name'     => 'headline',
						'type'     => 'text',
						'required' => 1,
					],
					[
						'key'   => 'field_hero_body',
						'label' => 'Paragraph',
						'name'  => 'body',
						'type'  => 'textarea',
						'rows'  => 3,
					],
					[
						'key'   => 'field_hero_cta_text',
						'label' => 'CTA Button Text',
						'name'  => 'cta_text',
						'type'  => 'text',
						'wrapper' => [ 'width' => '33' ],
					],
					[
						'key'   => 'field_hero_cta_url',
						'label' => 'CTA Button URL',
						'name'  => 'cta_url',
						'type'  => 'url',
						'wrapper' => [ 'width' => '33' ],
					],
					[
						'key'     => 'field_hero_cta_style',
						'label'   => 'CTA Style',
						'name'    => 'cta_style',
						'type'    => 'select',
						'choices' => [
							'primary' => 'Primary (Red)',
							'outline' => 'Outline (White)',
						],
						'default_value' => 'primary',
						'wrapper' => [ 'width' => '33' ],
					],
				],
			],

			// Categories section
			[
				'key'   => 'field_categories_title',
				'label' => 'Categories Section Title',
				'name'  => 'categories_title',
				'type'  => 'text',
				'default_value' => 'Popular Categories',
			],
			[
				'key'   => 'field_categories_subtitle',
				'label' => 'Categories Section Subtitle',
				'name'  => 'categories_subtitle',
				'type'  => 'text',
				'default_value' => 'Explore our full range of cabling, connectivity, and infrastructure products',
			],
			[
				'key'          => 'field_categories_selected',
				'label'        => 'Select Categories',
				'name'         => 'categories_selected',
				'type'         => 'taxonomy',
				'taxonomy'     => 'product_cat',
				'field_type'   => 'multi_select',
				'return_format' => 'id',
				'add_term'     => 0,
				'instructions' => 'Choose which categories to display. Leave empty to auto-select the top categories by product count.',
			],

			// Product Tabs section (replaces Featured Products)
			[
				'key'   => 'field_product_tabs_title',
				'label' => 'Product Tabs Title',
				'name'  => 'product_tabs_title',
				'type'  => 'text',
				'default_value' => 'Shop by Category',
			],
			[
				'key'   => 'field_product_tabs_subtitle',
				'label' => 'Product Tabs Subtitle',
				'name'  => 'product_tabs_subtitle',
				'type'  => 'text',
				'default_value' => 'Browse our most popular product lines',
			],
			[
				'key'          => 'field_product_tabs_categories',
				'label'        => 'Tab Categories',
				'name'         => 'product_tabs_categories',
				'type'         => 'taxonomy',
				'taxonomy'     => 'product_cat',
				'field_type'   => 'multi_select',
				'return_format' => 'id',
				'add_term'     => 0,
				'instructions' => 'Select which categories appear as tabs. Each tab shows a carousel of 3 products from that category.',
			],

			// Why Quest section
			[
				'key'   => 'field_why_quest_headline',
				'label' => 'Why Quest Headline',
				'name'  => 'why_quest_headline',
				'type'  => 'text',
				'default_value' => 'Why Choose Quest Technology',
			],
			[
				'key'          => 'field_why_quest_features',
				'label'        => 'Why Quest Features',
				'name'         => 'why_quest_features',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add Feature',
				'max'          => 6,
				'sub_fields'   => [
					[
						'key'   => 'field_wq_icon',
						'label' => 'Icon',
						'name'  => 'icon',
						'type'  => 'select',
						'choices' => [
							'expertise' => 'Expertise (layers)',
							'certified' => 'Certified (shield)',
							'pricing'   => 'Pricing (dollar)',
							'shipping'  => 'Shipping (truck)',
						],
						'wrapper' => [ 'width' => '30' ],
					],
					[
						'key'      => 'field_wq_title',
						'label'    => 'Title',
						'name'     => 'title',
						'type'     => 'text',
						'required' => 1,
						'wrapper'  => [ 'width' => '70' ],
					],
					[
						'key'   => 'field_wq_description',
						'label' => 'Description',
						'name'  => 'description',
						'type'  => 'textarea',
						'rows'  => 2,
					],
				],
			],

			// Stats bar
			[
				'key'          => 'field_stats_items',
				'label'        => 'Stats Bar',
				'name'         => 'stats_items',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Add Stat',
				'max'          => 6,
				'sub_fields'   => [
					[
						'key'      => 'field_stat_number',
						'label'    => 'Number',
						'name'     => 'stat_number',
						'type'     => 'text',
						'required' => 1,
					],
					[
						'key'      => 'field_stat_label',
						'label'    => 'Label',
						'name'     => 'stat_label',
						'type'     => 'text',
						'required' => 1,
					],
				],
			],

			// CTA section
			[
				'key'   => 'field_cta_headline',
				'label' => 'CTA Headline',
				'name'  => 'cta_headline',
				'type'  => 'text',
				'default_value' => 'Become a Quest Distributor',
			],
			[
				'key'   => 'field_cta_body',
				'label' => 'CTA Body',
				'name'  => 'cta_body',
				'type'  => 'textarea',
				'rows'  => 3,
				'default_value' => 'Get access to competitive pricing, dedicated support, and our full product catalog. Join our growing network of distributors and dealers.',
			],
			[
				'key'   => 'field_cta_button_text',
				'label' => 'CTA Button Text',
				'name'  => 'cta_button_text',
				'type'  => 'text',
				'default_value' => 'Request Access',
				'wrapper' => [ 'width' => '50' ],
			],
			[
				'key'   => 'field_cta_button_url',
				'label' => 'CTA Button URL',
				'name'  => 'cta_button_url',
				'type'  => 'url',
				'wrapper' => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_cta_image',
				'label'         => 'CTA Image',
				'name'          => 'cta_image',
				'type'          => 'image',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'instructions'  => 'Image displayed on the right side of the distributor CTA section.',
			],

			// Newsletter
			[
				'key'   => 'field_newsletter_headline',
				'label' => 'Newsletter Headline',
				'name'  => 'newsletter_headline',
				'type'  => 'text',
				'default_value' => 'Stay Connected',
			],
			[
				'key'   => 'field_newsletter_description',
				'label' => 'Newsletter Description',
				'name'  => 'newsletter_description',
				'type'  => 'textarea',
				'rows'  => 2,
				'default_value' => 'Get the latest product updates, promotions, and industry news delivered to your inbox.',
			],
			[
				'key'   => 'field_newsletter_shortcode',
				'label' => 'Newsletter Form Shortcode',
				'name'  => 'newsletter_shortcode',
				'type'  => 'text',
				'instructions' => 'Paste a Constant Contact or other form shortcode here.',
			],
		],
		'location' => [ [ [
			'param'    => 'options_page',
			'operator' => '==',
			'value'    => 'quest-homepage',
		] ] ],
		'menu_order' => 0,
	] );
	// -----------------------------------------------------------------------
	// Resources & Brochures page template
	// -----------------------------------------------------------------------
	acf_add_local_field_group( [
		'key'      => 'group_quest_resources',
		'title'    => 'Resources & Brochures',
		'fields'   => [
			[
				'key'   => 'field_resources_subtitle',
				'label' => 'Page Subtitle',
				'name'  => 'resources_subtitle',
				'type'  => 'text',
				'instructions' => 'Short description below the page title.',
			],
			[
				'key'          => 'field_resources_files',
				'label'        => 'Resources',
				'name'         => 'resources_files',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Add Resource',
				'sub_fields'   => [
					[
						'key'      => 'field_resource_name',
						'label'    => 'Name',
						'name'     => 'name',
						'type'     => 'text',
						'required' => 1,
						'wrapper'  => [ 'width' => '50' ],
					],
					[
						'key'     => 'field_resource_category',
						'label'   => 'Category',
						'name'    => 'category',
						'type'    => 'select',
						'choices' => [
							''            => '— None —',
							'Brochures'   => 'Brochures',
							'Catalogs'    => 'Catalogs',
							'Data Sheets' => 'Data Sheets',
							'Guides'      => 'Guides',
							'Certifications' => 'Certifications',
						],
						'wrapper' => [ 'width' => '50' ],
					],
					[
						'key'           => 'field_resource_file',
						'label'         => 'File',
						'name'          => 'file',
						'type'          => 'file',
						'return_format' => 'array',
						'required'      => 1,
						'mime_types'    => 'pdf,doc,docx,xls,xlsx,zip,jpg,jpeg,png',
						'instructions'  => 'Upload a PDF, document, or image file.',
					],
					[
						'key'           => 'field_resource_thumbnail',
						'label'         => 'Cover Image',
						'name'          => 'thumbnail',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
						'instructions'  => 'Optional cover/preview image. If empty, a file type icon is shown.',
					],
				],
			],
		],
		'location' => [ [ [
			'param'    => 'page_template',
			'operator' => '==',
			'value'    => 'page-resources.php',
		] ] ],
	] );

	// -----------------------------------------------------------------------
	// Footer Settings
	// -----------------------------------------------------------------------
	acf_add_local_field_group( [
		'key'      => 'group_quest_footer',
		'title'    => 'Footer Settings',
		'fields'   => [
			[
				'key'   => 'field_footer_tagline',
				'label' => 'Footer Tagline',
				'name'  => 'footer_tagline',
				'type'  => 'text',
				'instructions' => 'Short description below the logo. Leave empty to use the site tagline.',
			],
			[
				'key'   => 'field_footer_col1_heading',
				'label' => 'Column 1 Heading',
				'name'  => 'footer_col1_heading',
				'type'  => 'text',
				'default_value' => 'Products',
				'wrapper' => [ 'width' => '33' ],
			],
			[
				'key'   => 'field_footer_col2_heading',
				'label' => 'Column 2 Heading',
				'name'  => 'footer_col2_heading',
				'type'  => 'text',
				'default_value' => 'Company',
				'wrapper' => [ 'width' => '33' ],
			],
			[
				'key'   => 'field_footer_col3_heading',
				'label' => 'Column 3 Heading',
				'name'  => 'footer_col3_heading',
				'type'  => 'text',
				'default_value' => 'Support',
				'wrapper' => [ 'width' => '33' ],
			],
			[
				'key'   => 'field_footer_copyright',
				'label' => 'Copyright Text',
				'name'  => 'footer_copyright',
				'type'  => 'text',
				'instructions' => 'Leave empty for default. Use {year} for current year, {name} for site name.',
			],
		],
		'location' => [ [ [
			'param'    => 'options_page',
			'operator' => '==',
			'value'    => 'quest-footer',
		] ] ],
	] );
}
add_action( 'acf/init', 'quest_register_acf_fields', 20 );
