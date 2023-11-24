<?php
/**
 * Edit Site Themes Administration Screen
 *
 * @package GeChiUI
 * @subpackage Multisite
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_sites' ) ) {
	gc_die( __( '抱歉，您不能管理此系统的主题。' ) );
}

get_current_screen()->add_help_tab( get_site_screen_help_tab_args() );
get_current_screen()->set_help_sidebar( get_site_screen_help_sidebar_content() );

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( '过滤系统主题列表' ),
		'heading_pagination' => __( '系统主题列表导航' ),
		'heading_list'       => __( '系统主题列表' ),
	)
);

$gc_list_table = _get_list_table( 'GC_MS_Themes_List_Table' );

$action = $gc_list_table->current_action();

$s = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';

// Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$temp_args              = array( 'enabled', 'disabled', 'error' );
$_SERVER['REQUEST_URI'] = remove_query_arg( $temp_args, $_SERVER['REQUEST_URI'] );
$referer                = remove_query_arg( $temp_args, gc_get_referer() );

if ( ! empty( $_REQUEST['paged'] ) ) {
	$referer = add_query_arg( 'paged', (int) $_REQUEST['paged'], $referer );
}

$id = isset( $_REQUEST['id'] ) ? (int) $_REQUEST['id'] : 0;

if ( ! $id ) {
	gc_die( __( '系统ID无效。' ) );
}

$gc_list_table->prepare_items();

$details = get_site( $id );
if ( ! $details ) {
	gc_die( __( '请求的系统不存在。' ) );
}

if ( ! can_edit_network( $details->site_id ) ) {
	gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
}

$is_main_site = is_main_site( $id );

if ( $action ) {
	switch_to_blog( $id );
	$allowed_themes = get_option( 'allowedthemes' );

	switch ( $action ) {
		case 'enable':
			check_admin_referer( 'enable-theme_' . $_GET['theme'] );
			$theme  = $_GET['theme'];
			$action = 'enabled';
			$n      = 1;
			if ( ! $allowed_themes ) {
				$allowed_themes = array( $theme => true );
			} else {
				$allowed_themes[ $theme ] = true;
			}
			break;
		case 'disable':
			check_admin_referer( 'disable-theme_' . $_GET['theme'] );
			$theme  = $_GET['theme'];
			$action = 'disabled';
			$n      = 1;
			if ( ! $allowed_themes ) {
				$allowed_themes = array();
			} else {
				unset( $allowed_themes[ $theme ] );
			}
			break;
		case 'enable-selected':
			check_admin_referer( 'bulk-themes' );
			if ( isset( $_POST['checked'] ) ) {
				$themes = (array) $_POST['checked'];
				$action = 'enabled';
				$n      = count( $themes );
				foreach ( (array) $themes as $theme ) {
					$allowed_themes[ $theme ] = true;
				}
			} else {
				$action = 'error';
				$n      = 'none';
			}
			break;
		case 'disable-selected':
			check_admin_referer( 'bulk-themes' );
			if ( isset( $_POST['checked'] ) ) {
				$themes = (array) $_POST['checked'];
				$action = 'disabled';
				$n      = count( $themes );
				foreach ( (array) $themes as $theme ) {
					unset( $allowed_themes[ $theme ] );
				}
			} else {
				$action = 'error';
				$n      = 'none';
			}
			break;
		default:
			if ( isset( $_POST['checked'] ) ) {
				check_admin_referer( 'bulk-themes' );
				$themes = (array) $_POST['checked'];
				$n      = count( $themes );
				$screen = get_current_screen()->id;

				/**
				 * Fires when a custom bulk action should be handled.
				 *
				 * The redirect link should be modified with success or failure feedback
				 * from the action to be used to display feedback to the user.
				 *
				 * The dynamic portion of the hook name, `$screen`, refers to the current screen ID.
				 *
				 * @since 4.7.0
				 *
				 * @param string $redirect_url The redirect URL.
				 * @param string $action       The action being taken.
				 * @param array  $items        The items to take the action on.
				 * @param int    $site_id      The site ID.
				 */
				$referer = apply_filters( "handle_network_bulk_actions-{$screen}", $referer, $action, $themes, $id ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
			} else {
				$action = 'error';
				$n      = 'none';
			}
	}

	update_option( 'allowedthemes', $allowed_themes );
	restore_current_blog();

	gc_safe_redirect(
		add_query_arg(
			array(
				'id'    => $id,
				$action => $n,
			),
			$referer
		)
	);
	exit;
}

if ( isset( $_GET['action'] ) && 'update-site' === $_GET['action'] ) {
	gc_safe_redirect( $referer );
	exit;
}

add_thickbox();
add_screen_option( 'per_page' );

// Used in the HTML title tag.
/* translators: %s: Site title. */
$title = sprintf( __( '编辑系统：%s' ), esc_html( $details->blogname ) );

$parent_file  = 'sites.php';
$submenu_file = 'sites.php';

network_edit_site_nav(
	array(
		'blog_id'  => $id,
		'selected' => 'site-themes',
	)
);

if ( isset( $_GET['enabled'] ) ) {
	$enabled = absint( $_GET['enabled'] );
	if ( 1 === $enabled ) {
		$message = __( '主题已启用。' );
	} else {
		/* translators: %s: Number of themes. */
		$message = _n( '已启用%s个主题。', '已启用%s个主题。', $enabled );
	}
	add_settings_error( 'general', 'message', sprintf( $message, number_format_i18n( $enabled ) ), 'success' );
} elseif ( isset( $_GET['disabled'] ) ) {
	$disabled = absint( $_GET['disabled'] );
	if ( 1 === $disabled ) {
		$message = __( '主题已禁用。' );
	} else {
		/* translators: %s: Number of themes. */
		$message = _n( '已禁用%s个主题。', '已禁用%s个主题。', $disabled );
	}
	add_settings_error( 'general', 'message', sprintf( $message, number_format_i18n( $disabled ) ), 'success' );
} elseif ( isset( $_GET['error'] ) && 'none' === $_GET['error'] ) {
	add_settings_error( 'general', 'message', __( '未选择主题。' ), 'danger' );
}

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
<div class="page-header"><h2 id="edit-site" class="header-title"><?php echo esc_html( $title ); ?></h2></div>
<p class="edit-site-actions"><a href="<?php echo esc_url( get_home_url( $id, '/' ) ); ?>"><?php _e( '访问' ); ?></a> | <a href="<?php echo esc_url( get_admin_url( $id ) ); ?>"><?php _e( '仪表盘' ); ?></a></p>

<p><?php _e( '在SaaS平台中启用的主题不会显示在本页面。' ); ?></p>

<form method="get">
<?php $gc_list_table->search_box( __( '搜索已安装的主题' ), 'theme' ); ?>
<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
</form>

<?php $gc_list_table->views(); ?>

<form method="post" action="site-themes.php?action=update-site">
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />

<?php $gc_list_table->display(); ?>

</form>

</div>
<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
