<?php
/**
 * Multisite themes administration panel.
 *
 * @package GeChiUI
 * @subpackage Multisite
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_network_themes' ) ) {
	gc_die( __( '抱歉，您无法管理SaaS平台主题。' ) );
}

$gc_list_table = _get_list_table( 'GC_MS_Themes_List_Table' );
$pagenum       = $gc_list_table->get_pagenum();

$action = $gc_list_table->current_action();

$s = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';

// Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$temp_args = array(
	'enabled',
	'disabled',
	'deleted',
	'error',
	'enabled-auto-update',
	'disabled-auto-update',
);

$_SERVER['REQUEST_URI'] = remove_query_arg( $temp_args, $_SERVER['REQUEST_URI'] );
$referer                = remove_query_arg( $temp_args, gc_get_referer() );

if ( $action ) {
	switch ( $action ) {
		case 'enable':
			check_admin_referer( 'enable-theme_' . $_GET['theme'] );
			GC_Theme::network_enable_theme( $_GET['theme'] );
			if ( ! str_contains( $referer, '/network/themes.php' ) ) {
				gc_redirect( network_admin_url( 'themes.php?enabled=1' ) );
			} else {
				gc_safe_redirect( add_query_arg( 'enabled', 1, $referer ) );
			}
			exit;
		case 'disable':
			check_admin_referer( 'disable-theme_' . $_GET['theme'] );
			GC_Theme::network_disable_theme( $_GET['theme'] );
			gc_safe_redirect( add_query_arg( 'disabled', '1', $referer ) );
			exit;
		case 'enable-selected':
			check_admin_referer( 'bulk-themes' );
			$themes = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			if ( empty( $themes ) ) {
				gc_safe_redirect( add_query_arg( 'error', 'none', $referer ) );
				exit;
			}
			GC_Theme::network_enable_theme( (array) $themes );
			gc_safe_redirect( add_query_arg( 'enabled', count( $themes ), $referer ) );
			exit;
		case 'disable-selected':
			check_admin_referer( 'bulk-themes' );
			$themes = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			if ( empty( $themes ) ) {
				gc_safe_redirect( add_query_arg( 'error', 'none', $referer ) );
				exit;
			}
			GC_Theme::network_disable_theme( (array) $themes );
			gc_safe_redirect( add_query_arg( 'disabled', count( $themes ), $referer ) );
			exit;
		case 'update-selected':
			check_admin_referer( 'bulk-themes' );

			if ( isset( $_GET['themes'] ) ) {
				$themes = explode( ',', $_GET['themes'] );
			} elseif ( isset( $_POST['checked'] ) ) {
				$themes = (array) $_POST['checked'];
			} else {
				$themes = array();
			}

			// Used in the HTML title tag.
			$title       = __( '升级主题' );
			$parent_file = 'themes.php';

			require_once ABSPATH . 'gc-admin/admin-header.php';

			echo '<div class="wrap">';
			echo '<div class="page-header"><h2 class="header-title">' . esc_html( $title ) . '</h2></div>';

			$url = self_admin_url( 'update.php?action=update-selected-themes&amp;themes=' . urlencode( implode( ',', $themes ) ) );
			$url = gc_nonce_url( $url, 'bulk-update-themes' );

			echo "<iframe src='$url' style='width: 100%; height:100%; min-height:850px;'></iframe>";
			echo '</div>';
			require_once ABSPATH . 'gc-admin/admin-footer.php';
			exit;
		case 'delete-selected':
			if ( ! current_user_can( 'delete_themes' ) ) {
				gc_die( __( '抱歉，您不能删除此系统的主题。' ) );
			}

			check_admin_referer( 'bulk-themes' );

			$themes = isset( $_REQUEST['checked'] ) ? (array) $_REQUEST['checked'] : array();

			if ( empty( $themes ) ) {
				gc_safe_redirect( add_query_arg( 'error', 'none', $referer ) );
				exit;
			}

			$themes = array_diff( $themes, array( get_option( 'stylesheet' ), get_option( 'template' ) ) );

			if ( empty( $themes ) ) {
				gc_safe_redirect( add_query_arg( 'error', 'main', $referer ) );
				exit;
			}

			$theme_info = array();
			foreach ( $themes as $key => $theme ) {
				$theme_info[ $theme ] = gc_get_theme( $theme );
			}

			require ABSPATH . 'gc-admin/update.php';

			$parent_file = 'themes.php';

			if ( ! isset( $_REQUEST['verify-delete'] ) ) {
				gc_enqueue_script( 'jquery' );
				require_once ABSPATH . 'gc-admin/admin-header.php';
				$themes_to_delete = count( $themes );
				?>
				<div class="wrap">
				<?php if ( 1 === $themes_to_delete ) : ?>
					<div class="page-header"><h2 class="header-title"><?php _e( '删除主题' ); ?></h2></div>
					<div class="error"><p><strong><?php _e( '注意：' ); ?></strong> <?php _e( '这个主题可能已被SaaS平台中的其他系统启用。' ); ?></p></div>
					<p><?php _e( '您将要移除以下主题：' ); ?></p>
				<?php else : ?>
					<div class="page-header"><h2 class="header-title"><?php _e( '删除主题' ); ?></h2></div>
					<div class="error"><p><strong><?php _e( '注意：' ); ?></strong> <?php _e( '这些主题可能已被SaaS平台中的其他系统启用。' ); ?></p></div>
					<p><?php _e( '您将要移除以下主题：' ); ?></p>
				<?php endif; ?>
					<ul class="ul-disc">
					<?php
					foreach ( $theme_info as $theme ) {
						echo '<li>' . sprintf(
							/* translators: 1: Theme name, 2: Theme author. */
							_x( '由%2$s制作的%1$s', 'theme' ),
							'<strong>' . $theme->display( 'Name' ) . '</strong>',
							'<em>' . $theme->display( 'Author' ) . '</em>'
						) . '</li>';
					}
					?>
					</ul>
				<?php if ( 1 === $themes_to_delete ) : ?>
					<p><?php _e( '您确定要删除该主题吗？' ); ?></p>
				<?php else : ?>
					<p><?php _e( '您确定要删除这些主题吗？' ); ?></p>
				<?php endif; ?>
				<form method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" style="display:inline;">
					<input type="hidden" name="verify-delete" value="1" />
					<input type="hidden" name="action" value="delete-selected" />
					<?php

					foreach ( (array) $themes as $theme ) {
						echo '<input type="hidden" name="checked[]" value="' . esc_attr( $theme ) . '" />';
					}

					gc_nonce_field( 'bulk-themes' );

					if ( 1 === $themes_to_delete ) {
						submit_button( __( '是，删除这个主题' ), '', 'submit', false );
					} else {
						submit_button( __( '是，删除这些主题' ), '', 'submit', false );
					}

					?>
				</form>
				<?php $referer = gc_get_referer(); ?>
				<form method="post" action="<?php echo $referer ? esc_url( $referer ) : ''; ?>" style="display:inline;">
					<?php submit_button( __( '不，返回主题列表' ), '', 'submit', false ); ?>
				</form>
				</div>
				<?php

				require_once ABSPATH . 'gc-admin/admin-footer.php';
				exit;
			} // End if verify-delete.

			foreach ( $themes as $theme ) {
				$delete_result = delete_theme(
					$theme,
					esc_url(
						add_query_arg(
							array(
								'verify-delete' => 1,
								'action'        => 'delete-selected',
								'checked'       => $_REQUEST['checked'],
								'_gcnonce'      => $_REQUEST['_gcnonce'],
							),
							network_admin_url( 'themes.php' )
						)
					)
				);
			}

			$paged = ( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : 1;
			gc_redirect(
				add_query_arg(
					array(
						'deleted' => count( $themes ),
						'paged'   => $paged,
						's'       => $s,
					),
					network_admin_url( 'themes.php' )
				)
			);
			exit;
		case 'enable-auto-update':
		case 'disable-auto-update':
		case 'enable-auto-update-selected':
		case 'disable-auto-update-selected':
			if ( ! ( current_user_can( 'update_themes' ) && gc_is_auto_update_enabled_for_type( 'theme' ) ) ) {
				gc_die( __( '抱歉，您不能更改主题自动更新设置。' ) );
			}

			if ( 'enable-auto-update' === $action || 'disable-auto-update' === $action ) {
				check_admin_referer( 'updates' );
			} else {
				if ( empty( $_POST['checked'] ) ) {
					// Nothing to do.
					gc_safe_redirect( add_query_arg( 'error', 'none', $referer ) );
					exit;
				}

				check_admin_referer( 'bulk-themes' );
			}

			$auto_updates = (array) get_site_option( 'auto_update_themes', array() );

			if ( 'enable-auto-update' === $action ) {
				$auto_updates[] = $_GET['theme'];
				$auto_updates   = array_unique( $auto_updates );
				$referer        = add_query_arg( 'enabled-auto-update', 1, $referer );
			} elseif ( 'disable-auto-update' === $action ) {
				$auto_updates = array_diff( $auto_updates, array( $_GET['theme'] ) );
				$referer      = add_query_arg( 'disabled-auto-update', 1, $referer );
			} else {
				// Bulk enable/disable.
				$themes = (array) gc_unslash( $_POST['checked'] );

				if ( 'enable-auto-update-selected' === $action ) {
					$auto_updates = array_merge( $auto_updates, $themes );
					$auto_updates = array_unique( $auto_updates );
					$referer      = add_query_arg( 'enabled-auto-update', count( $themes ), $referer );
				} else {
					$auto_updates = array_diff( $auto_updates, $themes );
					$referer      = add_query_arg( 'disabled-auto-update', count( $themes ), $referer );
				}
			}

			$all_items = gc_get_themes();

			// Remove themes that don't exist or have been deleted since the option was last updated.
			$auto_updates = array_intersect( $auto_updates, array_keys( $all_items ) );

			update_site_option( 'auto_update_themes', $auto_updates );

			gc_safe_redirect( $referer );
			exit;
		default:
			$themes = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			if ( empty( $themes ) ) {
				gc_safe_redirect( add_query_arg( 'error', 'none', $referer ) );
				exit;
			}
			check_admin_referer( 'bulk-themes' );

			/** This action is documented in gc-admin/network/site-themes.php */
			$referer = apply_filters( 'handle_network_bulk_actions-' . get_current_screen()->id, $referer, $action, $themes ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

			gc_safe_redirect( $referer );
			exit;
	}
}

$gc_list_table->prepare_items();

add_thickbox();

add_screen_option( 'per_page' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
			'<p>' . __( '本页面设置每个系统的“外观”菜单中，可供用户选择的主题。不能禁用系统正在使用的主题。' ) . '</p>' .
			'<p>' . __( '若平台管理员禁用了正在使用的主题，在该系统上，这个主题将依然可用。一旦这位用户选择了其他主题，那么用户就无法再选择回来了。' ) . '</p>' .
			'<p>' . __( '在“编辑系统”的“主题”选项卡，您可以为每个系统设置不同的主题。通过“所有系统”页面上相应系统的“编辑”链接可以找到这个选项卡。只有平台管理员有权安装和编辑主题。' ) . '</p>',
	)
);

$help_sidebar_autoupdates = '';

if ( current_user_can( 'update_themes' ) && gc_is_auto_update_enabled_for_type( 'theme' ) ) {
	get_current_screen()->add_help_tab(
		array(
			'id'      => 'plugins-themes-auto-updates',
			'title'   => __( '自动更新' ),
			'content' =>
				'<p>' . __( '每个主题均可单独启用或禁用自动更新功能。对于已启用自动更新的主题，系统将显示下一次自动更新的预计日期。自动更新功能运行正常与否，取决于GC-Cron任务计划系统。' ) . '</p>' .
				'<p>' . __( '请注意：第三方主题、插件或自定义代码，都有可能覆盖GeChiUI的计划任务。' ) . '</p>',
		)
	);

	$help_sidebar_autoupdates = '<p>' . __( '<a href="https://www.gechiui.com/support/plugins-themes-auto-updates/">关于自动更新的文档</a>' ) . '</p>';
}

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.gechiui.com/Network_Admin_Themes_Screen">平台模板文档</a>' ) . '</p>' .
	$help_sidebar_autoupdates .
	'<p>' . __( '<a href="https://www.gechiui.com/support/forums/">支持论坛</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( '筛选主题列表' ),
		'heading_pagination' => __( '主题列表导航' ),
		'heading_list'       => __( '主题列表' ),
	)
);

// Used in the HTML title tag.
$title       = __( '主题' );
$parent_file = 'themes.php';

gc_enqueue_script( 'updates' );
gc_enqueue_script( 'theme-preview' );

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
	add_settings_error( 'general', 'message', sprintf( $message, number_format_i18n( $disabled ) ) , 'success' );
} elseif ( isset( $_GET['deleted'] ) ) {
	$deleted = absint( $_GET['deleted'] );
	if ( 1 === $deleted ) {
		$message = __( '主题已删除。' );
	} else {
		/* translators: %s: Number of themes. */
		$message = _n( '已删除%s个主题。', '已删除%s个主题。', $deleted );
	}
	add_settings_error( 'general', 'message', sprintf( $message, number_format_i18n( $deleted ) ) , 'success' );
} elseif ( isset( $_GET['enabled-auto-update'] ) ) {
	$enabled = absint( $_GET['enabled-auto-update'] );
	if ( 1 === $enabled ) {
		$message = __( '主题将自动更新。' );
	} else {
		/* translators: %s: Number of themes. */
		$message = _n( '%s个主题将自动更新。', '%s个主题将自动更新。', $enabled );
	}
	add_settings_error( 'general', 'message', sprintf( $message, number_format_i18n( $enabled ) ), 'success' );
} elseif ( isset( $_GET['disabled-auto-update'] ) ) {
	$disabled = absint( $_GET['disabled-auto-update'] );
	if ( 1 === $disabled ) {
		$message = __( '主题将不再自动更新。' );
	} else {
		/* translators: %s: Number of themes. */
		$message = _n( '%s个主题将不再自动更新。', '%s个主题将不再自动更新。', $disabled );
	}
	add_settings_error( 'general', 'message', sprintf( $message, number_format_i18n( $disabled ) ), 'success' );
} elseif ( isset( $_GET['error'] ) && 'none' === $_GET['error'] ) {
	add_settings_error( 'general', 'message', __( '未选择主题。' ), 'danger' );
} elseif ( isset( $_GET['error'] ) && 'main' === $_GET['error'] ) {
	add_settings_error( 'general', 'message', __( '您不能删除主系统正在使用的主题。' ), 'danger' );
}

require_once ABSPATH . 'gc-admin/admin-header.php';

?>

<div class="wrap">
<div class="page-header">
	<h2 class="header-title"><?php echo esc_html( $title ); ?></h2>
	<?php if ( current_user_can( 'install_themes' ) ) : ?>
		<a href="theme-install.php" class="btn btn-primary btn-tone btn-sm"><?php echo esc_html_x( '安装主题', 'theme' ); ?></a>
	<?php endif; ?>

	<?php
	if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
		echo '<span class="subtitle">';
		printf(
			/* translators: %s: Search query. */
			__( '搜索词：%s' ),
			'<strong>' . esc_html( $s ) . '</strong>'
		);
		echo '</span>';
	}
	?>

	<form method="get">
	<?php $gc_list_table->search_box( __( '搜索已安装的主题' ), 'theme' ); ?>
	</form>
</div>
<?php
$gc_list_table->views();

if ( 'broken' === $status ) {
	echo '<p class="clear">' . __( '下列主题已安装但不完整。' ) . '</p>';
}
?>

<form id="bulk-action-form" method="post">
<input type="hidden" name="theme_status" value="<?php echo esc_attr( $status ); ?>" />
<input type="hidden" name="paged" value="<?php echo esc_attr( $page ); ?>" />

<?php $gc_list_table->display(); ?>
</form>

</div>

<?php
gc_print_request_filesystem_credentials_modal();
gc_print_admin_notice_templates();
gc_print_update_row_templates();

require_once ABSPATH . 'gc-admin/admin-footer.php';
