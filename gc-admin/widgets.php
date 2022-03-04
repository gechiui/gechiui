<?php
/**
 * Widget administration screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

/** GeChiUI Administration Widgets API */
require_once ABSPATH . 'gc-admin/includes/widgets.php';

if ( ! current_user_can( 'edit_theme_options' ) ) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能在此站点上编辑主题选项。' ) . '</p>',
		403
	);
}

if ( ! current_theme_supports( 'widgets' ) ) {
	gc_die( __( '您当前使用的主题并不支持小工具功能，这意味着它没有可供修改的侧边栏。要查看如何才能使您的主题支持小工具，请<a href="https://developer.gechiui.com/themes/functionality/widgets/">遵循这些说明</a>。' ) );
}

// Used in the HTML title tag.
$title       = __( '小工具' );
$parent_file = 'themes.php';

if ( gc_use_widgets_block_editor() ) {
	require ABSPATH . 'gc-admin/widgets-form-blocks.php';
} else {
	require ABSPATH . 'gc-admin/widgets-form.php';
}
