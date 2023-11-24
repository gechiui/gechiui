<?php
/**
 * Build Administration Menu.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Constructs the admin menu.
 *
 * The elements in the array are:
 *     0: Menu item name.
 *     1: Minimum level or capability required.
 *     2: The URL of the item's file.
 *     3: Page title.
 *     4: Classes.
 *     5: ID.
 *     6: Icon for top level menu.
 *
 * @global array $menu
 */

$menu[2] = array( __( '仪表盘' ), 'read', 'index.php', '', 'menu-top menu-top-first menu-icon-dashboard', 'menu-dashboard', 'anticon anticon-dashboard' );

$submenu['index.php'][0] = array( __( '首页' ), 'read', 'index.php' );

if ( is_multisite() ) {
	$submenu['index.php'][5] = array( __( '我的系统' ), 'read', 'my-sites.php' );
}

if ( ! is_multisite() || current_user_can( 'update_core' ) ) {
	$update_data = gc_get_update_data();
}

if ( ! is_multisite() ) {
	if ( current_user_can( 'update_core' ) ) {
		$cap = 'update_core';
	} elseif ( current_user_can( 'update_plugins' ) ) {
		$cap = 'update_plugins';
	} elseif ( current_user_can( 'update_themes' ) ) {
		$cap = 'update_themes';
	} else {
		$cap = 'update_languages';
	}
	$submenu['index.php'][10] = array(
		sprintf(
			/* translators: %s: Number of pending updates. */
			__( '更新%s' ),
			sprintf(
				'<span class="update-plugins count-%s"><div class="avatar avatar-text bg-danger avatar-xs m-l-5"><span class="update-count">%s</span></div></span>',
				$update_data['counts']['total'],
				number_format_i18n( $update_data['counts']['total'] )
			)
		),
		$cap,
		'update-core.php',
	);
	unset( $cap );
}

$menu[4] = array( '', 'read', 'separator1', '', 'gc-menu-separator' );

// $menu[5] = Posts.

$menu[10]                     = array( __( '媒体' ), 'upload_files', 'upload.php', '', 'menu-top menu-icon-media', 'menu-media', 'anticon anticon-folder' );
	$submenu['upload.php'][5] = array( __( '媒体库' ), 'upload_files', 'upload.php' );
	/* translators: Add new file. */
	$submenu['upload.php'][10] = array( _x( '新增文件', 'file' ), 'upload_files', 'media-new.php' );
	$i                         = 15;
foreach ( get_taxonomies_for_attachments( 'objects' ) as $tax ) {
	if ( ! $tax->show_ui || ! $tax->show_in_menu ) {
		continue;
	}

	$submenu['upload.php'][ $i++ ] = array( esc_attr( $tax->labels->menu_name ), $tax->cap->manage_terms, 'edit-tags.php?taxonomy=' . $tax->name . '&amp;post_type=attachment' );
}
	unset( $tax, $i );

$menu[15]                           = array( __( '链接' ), 'manage_links', 'link-manager.php', '', 'menu-top menu-icon-links', 'menu-links', 'anticon anticon-link' );
	$submenu['link-manager.php'][5] = array( _x( '全部链接', 'admin menu' ), 'manage_links', 'link-manager.php' );
	/* translators: Add new links. */
	$submenu['link-manager.php'][10] = array( _x( '添加新链接', 'link' ), 'manage_links', 'link-add.php' );
	$submenu['link-manager.php'][15] = array( __( '链接分类' ), 'manage_categories', 'edit-tags.php?taxonomy=link_category' );

// $menu[20] = Pages.

// Avoid the comment count query for users who cannot edit_posts.
if ( current_user_can( 'edit_posts' ) ) {
	$awaiting_mod      = gc_count_comments();
	$awaiting_mod      = $awaiting_mod->moderated;
	$awaiting_mod_i18n = number_format_i18n( $awaiting_mod );
	/* translators: %s: Number of comments. */
	$awaiting_mod_text = sprintf( _n( '%s条评论待审', '%s条评论待审', $awaiting_mod ), $awaiting_mod_i18n );
	$awaiting_count_html =  $awaiting_mod_i18n>0 ? '<div class="avatar avatar-text bg-danger avatar-xs m-l-5">' . $awaiting_mod_i18n . '</div>' : '';

	$menu[25] = array(
		/* translators: %s: Number of comments. */
		sprintf( __( '评论%s' ), $awaiting_count_html ),
		'edit_posts',
		'edit-comments.php',
		'',
		'menu-top menu-icon-comments',
		'menu-comments',
		'anticon anticon-message',
	);
	unset( $awaiting_mod );
}

$submenu['edit-comments.php'][0] = array( __( '所有评论' ), 'edit_posts', 'edit-comments.php' );

$_gc_last_object_menu = 25; // The index of the last top-level menu in the object menu group.

$types   = (array) get_post_types(
	array(
		'show_ui'      => true,
		'_builtin'     => false,
		'show_in_menu' => true,
	)
);
$builtin = array( 'post', 'page' );
foreach ( array_merge( $builtin, $types ) as $ptype ) {
	$ptype_obj = get_post_type_object( $ptype );
	// Check if it should be a submenu.
	if ( true !== $ptype_obj->show_in_menu ) {
		continue;
	}
	$ptype_menu_position = is_int( $ptype_obj->menu_position ) ? $ptype_obj->menu_position : ++$_gc_last_object_menu; // If we're to use $_gc_last_object_menu, increment it first.
	$ptype_for_id        = sanitize_html_class( $ptype );

	$menu_icon = 'anticon anticon-file'; //这里发放一个默认值
	if ( is_string( $ptype_obj->menu_icon ) ) {
		// Special handling for data:image/svg+xml and Dashicons.
		if ( 0 === strpos( $ptype_obj->menu_icon, 'data:image/svg+xml;base64,' ) || 0 === strpos( $ptype_obj->menu_icon, 'anticon' ) || 0 === strpos( $ptype_obj->menu_icon, 'dashicons-' ) ) {
			$menu_icon = $ptype_obj->menu_icon;
		} else {
			$menu_icon = esc_url( $ptype_obj->menu_icon );
		}
	} elseif ( in_array( $ptype, $builtin, true ) ) {
		$menu_icon = 'dashicons-admin-' . $ptype;
	}

	$menu_class = 'menu-top menu-icon-' . $ptype_for_id;
	// 'post' special case.
	if ( 'post' === $ptype ) {
		$menu_class    .= ' open-if-no-js';
		$ptype_file     = 'edit.php';
		$post_new_file  = 'post-new.php';
		$edit_tags_file = 'edit-tags.php?taxonomy=%s';
	} else {
		$ptype_file     = "edit.php?post_type=$ptype";
		$post_new_file  = "post-new.php?post_type=$ptype";
		$edit_tags_file = "edit-tags.php?taxonomy=%s&amp;post_type=$ptype";
	}

	if ( in_array( $ptype, $builtin, true ) ) {
		$ptype_menu_id = 'menu-' . $ptype_for_id . 's';
	} else {
		$ptype_menu_id = 'menu-posts-' . $ptype_for_id;
	}
	/*
	 * If $ptype_menu_position is already populated or will be populated
	 * by a hard-coded value below, increment the position.
	 */
	$core_menu_positions = array( 59, 60, 65, 70, 75, 80, 85, 99 );
	while ( isset( $menu[ $ptype_menu_position ] ) || in_array( $ptype_menu_position, $core_menu_positions, true ) ) {
		$ptype_menu_position++;
	}

	$menu[ $ptype_menu_position ] = array( esc_attr( $ptype_obj->labels->menu_name ), $ptype_obj->cap->edit_posts, $ptype_file, '', $menu_class, $ptype_menu_id, $menu_icon );
	$submenu[ $ptype_file ][5]    = array( $ptype_obj->labels->all_items, $ptype_obj->cap->edit_posts, $ptype_file );
	$submenu[ $ptype_file ][10]   = array( $ptype_obj->labels->add_new, $ptype_obj->cap->create_posts, $post_new_file );

	$i = 15;
	foreach ( get_taxonomies( array(), 'objects' ) as $tax ) {
		if ( ! $tax->show_ui || ! $tax->show_in_menu || ! in_array( $ptype, (array) $tax->object_type, true ) ) {
			continue;
		}

		$submenu[ $ptype_file ][ $i++ ] = array( esc_attr( $tax->labels->menu_name ), $tax->cap->manage_terms, sprintf( $edit_tags_file, $tax->name ) );
	}
}
unset( $ptype, $ptype_obj, $ptype_for_id, $ptype_menu_position, $menu_icon, $i, $tax, $post_new_file );

$menu[59] = array( '', 'read', 'separator2', '', 'gc-menu-separator' );

$appearance_cap = current_user_can( 'switch_themes' ) ? 'switch_themes' : 'edit_theme_options';

$menu[60] = array( __( '外观' ), $appearance_cap, 'themes.php', '', 'menu-top menu-icon-appearance', 'menu-appearance', 'anticon anticon-skin' );

$count = '';
if ( ! is_multisite() && current_user_can( 'update_themes' ) ) {
	if ( ! isset( $update_data ) ) {
		$update_data = gc_get_update_data();
	}
	$count = sprintf(
		'<span class="update-plugins count-%s"><div class="avatar avatar-text bg-danger avatar-xs m-l-5">%s</div></span>',
		$update_data['counts']['themes'],
		number_format_i18n( $update_data['counts']['themes'] )
	);
}

	/* translators: %s: Number of available theme updates. */
	$submenu['themes.php'][5] = array( sprintf( __( '主题%s' ), $count ), $appearance_cap, 'themes.php' );

if ( gc_is_block_theme() ) {
	$submenu['themes.php'][6] = array( _x( '编辑器', '系统编辑器菜单项' ), 'edit_theme_options', 'site-editor.php' ); 
}

// 自定义
$customize_url = add_query_arg( 'return', urlencode( remove_query_arg( gc_removable_query_args(), gc_unslash( $_SERVER['REQUEST_URI'] ) ) ), 'customize.php' );

if ( ! gc_is_block_theme() || has_action( 'customize_register' ) ) {
	$position = ( gc_is_block_theme() || current_theme_supports( 'block-template-parts' ) ) ? 7 : 6;

	$submenu['themes.php'][ $position ] = array( __( '自定义' ), 'customize', esc_url( $customize_url ), '', 'hide-if-no-customize' );
}

if ( current_theme_supports( 'menus' ) || current_theme_supports( 'widgets' ) ) {
	$submenu['themes.php'][10] = array( __( '菜单' ), 'edit_theme_options', 'nav-menus.php' );
}

if ( current_theme_supports( 'custom-header' ) && current_user_can( 'customize' ) ) {
	$customize_header_url      = add_query_arg( array( 'autofocus' => array( 'control' => 'header_image' ) ), $customize_url );
	$submenu['themes.php'][15] = array( __( '页眉' ), $appearance_cap, esc_url( $customize_header_url ), '', 'hide-if-no-customize' );
}

if ( current_theme_supports( 'custom-background' ) && current_user_can( 'customize' ) ) {
	$customize_background_url  = add_query_arg( array( 'autofocus' => array( 'control' => 'background_image' ) ), $customize_url );
	$submenu['themes.php'][20] = array( __( '背景' ), $appearance_cap, esc_url( $customize_background_url ), '', 'hide-if-no-customize' );
}

unset( $customize_url );

unset( $appearance_cap );

// Add '主题文件编辑器' to the bottom of the Appearance (non-block themes) or Tools (block themes) menu.
if ( ! is_multisite() ) {
	// Must use API on the admin_menu hook, direct modification is only possible on/before the _admin_menu hook.
	add_action( 'admin_menu', '_add_themes_utility_last', 101 );
}
/**
 * Adds the '主题文件编辑器' menu item to the bottom of the Appearance (non-block themes)
 * or Tools (block themes) menu.
 *
 * @access private
 *
 *              Relocates to Tools for block themes.
 */
function _add_themes_utility_last() {
	add_submenu_page(
		gc_is_block_theme() ? 'tools.php' : 'themes.php',
		__( '主题文件编辑器' ),
		__( '主题文件编辑器' ),
		'edit_themes',
		'theme-editor.php'
	);
}

/**
 * Adds the '插件文件编辑器' menu item after the 'Themes File Editor' in Tools
 * for block themes.
 *
 * @access private
 *
 */
function _add_plugin_file_editor_to_tools() {
	if ( ! gc_is_block_theme() ) {
		return;
	}
	add_submenu_page(
		'tools.php',
		__( '插件文件编辑器' ),
		__( '插件文件编辑器' ),
		'edit_plugins',
		'plugin-editor.php'
	);
}

$count = '';
if ( ! is_multisite() && current_user_can( 'update_plugins' ) ) {
	if ( ! isset( $update_data ) ) {
		$update_data = gc_get_update_data();
	}
	$count = sprintf(
		'<span class="update-plugins count-%s"><div class="avatar avatar-text bg-danger avatar-xs m-l-5">%s</div></span>',
		$update_data['counts']['plugins'],
		number_format_i18n( $update_data['counts']['plugins'] )
	);
}

/* translators: %s: Number of available plugin updates. */
$menu[65] = array( sprintf( __( '插件%s' ), $count ), 'activate_plugins', 'plugins.php', '', 'menu-top menu-icon-plugins', 'menu-plugins', 'anticon anticon-appstore' );

$submenu['plugins.php'][5] = array( __( '已安装的插件' ), 'activate_plugins', 'plugins.php' );

if ( ! is_multisite() ) {
	/* translators: Add new plugin. */
	$submenu['plugins.php'][10] = array( _x( '安装插件', 'plugin' ), 'install_plugins', 'plugin-install.php' );
	if ( gc_is_block_theme() ) {
		// Place the menu item below the Theme File Editor menu item.
		add_action( 'admin_menu', '_add_plugin_file_editor_to_tools', 101 );
	} else {
		$submenu['plugins.php'][15] = array( __( '插件文件编辑器' ), 'edit_plugins', 'plugin-editor.php' );
	}
}

unset( $update_data );

if ( current_user_can( 'list_users' ) ) {
	$menu[70] = array( __( '用户' ), 'list_users', 'users.php', '', 'menu-top menu-icon-users', 'menu-users', 'anticon anticon-team' );
} else {
	$menu[70] = array( __( '个人资料' ), 'read', 'profile.php', '', 'menu-top menu-icon-users', 'menu-users', 'anticon anticon-user' );
}

if ( current_user_can( 'list_users' ) ) {
	$_gc_real_parent_file['profile.php'] = 'users.php'; // Back-compat for plugins adding submenus to profile.php.
	$submenu['users.php'][5]             = array( __( '所有用户' ), 'list_users', 'users.php' );
	if ( current_user_can( 'create_users' ) ) {
		$submenu['users.php'][10] = array( _x( '添加用户', 'user' ), 'create_users', 'user-new.php' );
	} elseif ( is_multisite() ) {
		$submenu['users.php'][10] = array( _x( '添加用户', 'user' ), 'promote_users', 'user-new.php' );
	}

	$submenu['users.php'][15] = array( __( '个人资料' ), 'read', 'profile.php' );
} else {
	$_gc_real_parent_file['users.php'] = 'profile.php';
	$submenu['profile.php'][5]         = array( __( '个人资料' ), 'read', 'profile.php' );
	if ( current_user_can( 'create_users' ) ) {
		$submenu['profile.php'][10] = array( __( '添加用户' ), 'create_users', 'user-new.php' );
	} elseif ( is_multisite() ) {
		$submenu['profile.php'][10] = array( __( '添加用户' ), 'promote_users', 'user-new.php' );
	}
}

$menu[75]                     = array( __( '工具' ), 'edit_posts', 'tools.php', '', 'menu-top menu-icon-tools', 'menu-tools', 'anticon anticon-tool' );
	$submenu['tools.php'][5]  = array( __( '可用工具' ), 'edit_posts', 'tools.php' );
	$submenu['tools.php'][10] = array( __( '导入' ), 'import', 'import.php' );
	$submenu['tools.php'][15] = array( __( '导出' ), 'export', 'export.php' );
	$submenu['tools.php'][20] = array( __( '系统健康' ), 'view_site_health_checks', 'site-health.php' );
	$submenu['tools.php'][25] = array( __( '导出个人数据' ), 'export_others_personal_data', 'export-personal-data.php' );
	$submenu['tools.php'][30] = array( __( '抹除个人数据' ), 'erase_others_personal_data', 'erase-personal-data.php' );
if ( is_multisite() && ! is_main_site() ) {
	$submenu['tools.php'][35] = array( __( '删除系统' ), 'delete_site', 'ms-delete-site.php' );
}
if ( ! is_multisite() && defined( 'GC_ALLOW_MULTISITE' ) && GC_ALLOW_MULTISITE ) {
	$submenu['tools.php'][50] = array( __( 'SaaS平台配置' ), 'setup_network', 'network.php' );
}

$menu[80]                               = array( __( '设置' ), 'manage_options', 'options-general.php', '', 'menu-top menu-icon-settings', 'menu-settings', 'anticon anticon-setting' );
	$submenu['options-general.php'][10] = array( _x( '常规', 'settings screen' ), 'manage_options', 'options-general.php' );
	$submenu['options-general.php'][15] = array( __( '撰写' ), 'manage_options', 'options-writing.php' );
	$submenu['options-general.php'][20] = array( __( '阅读' ), 'manage_options', 'options-reading.php' );
	$submenu['options-general.php'][25] = array( __( '讨论' ), 'manage_options', 'options-discussion.php' );
	$submenu['options-general.php'][30] = array( __( '媒体' ), 'manage_options', 'options-media.php' );
	$submenu['options-general.php'][40] = array( __( '固定链接' ), 'manage_options', 'options-permalink.php' );
	$submenu['options-general.php'][45] = array( __( '隐私' ), 'manage_privacy_options', 'options-privacy.php' );

$_gc_last_utility_menu = 80; // The index of the last top-level menu in the utility menu group.

$menu[99] = array( '', 'read', 'separator-last', '', 'gc-menu-separator' );

// Back-compat for old top-levels.
$_gc_real_parent_file['post.php']       = 'edit.php';
$_gc_real_parent_file['post-new.php']   = 'edit.php';
$_gc_real_parent_file['edit-pages.php'] = 'edit.php?post_type=page';
$_gc_real_parent_file['page-new.php']   = 'edit.php?post_type=page';
$_gc_real_parent_file['gcmu-admin.php'] = 'tools.php';
$_gc_real_parent_file['ms-admin.php']   = 'tools.php';

// Ensure backward compatibility.
$compat = array(
	'index'           => 'dashboard',
	'edit'            => 'posts',
	'post'            => 'posts',
	'upload'          => 'media',
	'link-manager'    => 'links',
	'edit-pages'      => 'pages',
	'page'            => 'pages',
	'edit-comments'   => 'comments',
	'options-general' => 'settings',
	'themes'          => 'appearance',
);

require_once ABSPATH . 'gc-admin/includes/menu.php';
