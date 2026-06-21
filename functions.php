<?php
defined( 'ABSPATH' ) || exit;

define( 'QUEST_VERSION', '1.5.0' );
define( 'QUEST_DIR', get_template_directory() );
define( 'QUEST_URL', get_template_directory_uri() );

require_once QUEST_DIR . '/inc/theme-setup.php';
require_once QUEST_DIR . '/inc/enqueue.php';
require_once QUEST_DIR . '/inc/template-tags.php';
require_once QUEST_DIR . '/inc/woocommerce.php';
require_once QUEST_DIR . '/inc/acf-fields.php';
require_once QUEST_DIR . '/inc/class-mega-menu-walker.php';
require_once QUEST_DIR . '/inc/ajax-search.php';
require_once QUEST_DIR . '/inc/contact-form.php';
require_once QUEST_DIR . '/inc/email-template.php';
require_once QUEST_DIR . '/inc/account.php';
require_once QUEST_DIR . '/inc/admin-approval.php';
