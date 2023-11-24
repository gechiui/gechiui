<?php
/**
 * Import GeChiUI Administration Screen
 *
 * @package GeChiUI
 * @subpackage Administration
 */

define( 'GC_LOAD_IMPORTERS', true );

/** Load GeChiUI Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'import' ) ) {
	gc_die( __( '抱歉，您不能向此系统导入内容。' ) );
}

// Used in the HTML title tag.
$title = __( '导入' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => '<p>' . __( '本页面列出了一些用于从其他GC平台或CMS（内容管理系统）导入数据的插件。选择您原来使用的平台，当新窗口弹出时，请点击“立即安装”。若您在列表中没有找到您原来使用的平台，点击链接以在整个插件目录搜索适合您原平台的导入工具。' ) . '</p>' .
			'<p>' . __( '在先前的GeChiUI版本中，导入工具都是内建的。最近我们将导入工具全部移植成了插件，因为大部分用户不会经常使用它们。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/tools-import-screen/">导入文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

if ( current_user_can( 'install_plugins' ) ) {
	// List of popular importer plugins from the www.GeChiUI.com API.
	$popular_importers = gc_get_popular_importers();
} else {
	$popular_importers = array();
}

// Detect and redirect invalid importers like 'movabletype', which is registered as 'mt'.
if ( ! empty( $_GET['invalid'] ) && isset( $popular_importers[ $_GET['invalid'] ] ) ) {
	$importer_id = $popular_importers[ $_GET['invalid'] ]['importer-id'];
	if ( $importer_id !== $_GET['invalid'] ) { // Prevent redirect loops.
		gc_redirect( admin_url( 'admin.php?import=' . $importer_id ) );
		exit;
	}
	unset( $importer_id );
}

add_thickbox();
gc_enqueue_script( 'plugin-install' );
gc_enqueue_script( 'updates' );

if ( ! empty( $_GET['invalid'] ) ) {
	$message ='<strong>'. _e( '错误：' ) .'</strong>' . printf( __( '%s导入器不可用或未被安装。' ), '<strong>' . esc_html( $_GET['invalid'] ) . '</strong>' );
	add_settings_error( 'general', 'settings_updated', $message, 'danger' );
}

require_once ABSPATH . 'gc-admin/admin-header.php';
$parent_file = 'tools.php';
?>

<div class="wrap">
<div class="page-header">
	<h2 class="header-title"><?php echo esc_html( $title ); ?></h2>
	<p><?php _e( '若有需要，您可以把其他系统的文章和评论内容导入到这个GeChiUI系统。请从以下系统中选择一个导入源，开始导入：' ); ?></p>
</div>

<?php
// Registered (already installed) importers. They're stored in the global $gc_importers.
$importers = get_importers();

// If a popular importer is not registered, create a dummy registration that links to the plugin installer.
foreach ( $popular_importers as $pop_importer => $pop_data ) {
	if ( isset( $importers[ $pop_importer ] ) ) {
		continue;
	}
	if ( isset( $importers[ $pop_data['importer-id'] ] ) ) {
		continue;
	}

	// Fill the array of registered (already installed) importers with data of the popular importers from the www.GeChiUI.com API.
	$importers[ $pop_data['importer-id'] ] = array(
		$pop_data['name'],
		$pop_data['description'],
		'install' => $pop_data['plugin-slug'],
	);
}

if ( empty( $importers ) ) {
	echo '<p>' . __( '当前没有可用的导入工具。' ) . '</p>'; // TODO: Make more helpful.
} else {
	uasort( $importers, '_usort_by_first_member' );
	?>
<table class="widefat importers striped">

	<?php
	foreach ( $importers as $importer_id => $data ) {
		$plugin_slug         = '';
		$action              = '';
		$is_plugin_installed = false;

		if ( isset( $data['install'] ) ) {
			$plugin_slug = $data['install'];

			if ( file_exists( GC_PLUGIN_DIR . '/' . $plugin_slug ) ) {
				// Looks like an importer is installed, but not active.
				$plugins = get_plugins( '/' . $plugin_slug );
				if ( ! empty( $plugins ) ) {
					$keys        = array_keys( $plugins );
					$plugin_file = $plugin_slug . '/' . $keys[0];
					$url         = gc_nonce_url(
						add_query_arg(
							array(
								'action' => 'activate',
								'plugin' => $plugin_file,
								'from'   => 'import',
							),
							admin_url( 'plugins.php' )
						),
						'activate-plugin_' . $plugin_file
					);
					$action      = sprintf(
						'<a href="%s" aria-label="%s">%s</a>',
						esc_url( $url ),
						/* translators: %s: Importer name. */
						esc_attr( sprintf( __( '运行%s' ), $data[0] ) ),
						__( '运行导入器' )
					);

					$is_plugin_installed = true;
				}
			}

			if ( empty( $action ) ) {
				if ( is_main_site() ) {
					$url    = gc_nonce_url(
						add_query_arg(
							array(
								'action' => 'install-plugin',
								'plugin' => $plugin_slug,
								'from'   => 'import',
							),
							self_admin_url( 'update.php' )
						),
						'install-plugin_' . $plugin_slug
					);
					$action = sprintf(
						'<a href="%1$s" class="install-now" data-slug="%2$s" data-name="%3$s" aria-label="%4$s">%5$s</a>',
						esc_url( $url ),
						esc_attr( $plugin_slug ),
						esc_attr( $data[0] ),
						/* translators: %s: Importer name. */
						esc_attr( sprintf( _x( '立即安装%s', 'plugin' ), $data[0] ) ),
						__( '立即安装' )
					);
				} else {
					$action = sprintf(
						/* translators: %s: URL to Import screen on the main site. */
						__( '尚未安装该导入工具。请从<a href="%s">主系统</a>安装导入工具。' ),
						get_admin_url( get_current_network_id(), 'import.php' )
					);
				}
			}
		} else {
			$url    = add_query_arg(
				array(
					'import' => $importer_id,
				),
				self_admin_url( 'admin.php' )
			);
			$action = sprintf(
				'<a href="%1$s" aria-label="%2$s">%3$s</a>',
				esc_url( $url ),
				/* translators: %s: Importer name. */
				esc_attr( sprintf( __( '运行%s' ), $data[0] ) ),
				__( '运行导入器' )
			);

			$is_plugin_installed = true;
		}

		if ( ! $is_plugin_installed && is_main_site() ) {
			$url     = add_query_arg(
				array(
					'tab'       => 'plugin-information',
					'plugin'    => $plugin_slug,
					'from'      => 'import',
					'TB_iframe' => 'true',
					'width'     => 600,
					'height'    => 550,
				),
				network_admin_url( 'plugin-install.php' )
			);
			$action .= sprintf(
				' | <a href="%1$s" class="thickbox open-plugin-details-modal" aria-label="%2$s">%3$s</a>',
				esc_url( $url ),
				/* translators: %s: Importer name. */
				esc_attr( sprintf( __( '关于%s的更多信息' ), $data[0] ) ),
				__( '详情' )
			);
		}

		echo "
			<tr class='importer-item'>
				<td class='import-system'>
					<span class='importer-title'>{$data[0]}</span>
					<span class='importer-action'>{$action}</span>
				</td>
				<td class='desc'>
					<span class='importer-desc'>{$data[1]}</span>
				</td>
			</tr>";
	}
	?>
</table>
	<?php
}

if ( current_user_can( 'install_plugins' ) ) {
	echo '<p>' . sprintf(
		/* translators: %s: URL to Add Plugins screen. */
		__( '若上面没有您希望使用的导入工具，请尝试<a href="%s">搜索插件目录</a>以寻找适用的导入工具。' ),
		esc_url( network_admin_url( 'plugin-install.php?tab=search&type=tag&s=importer' ) )
	) . '</p>';
}
?>

</div>

<?php
gc_print_request_filesystem_credentials_modal();
gc_print_admin_notice_templates();

require_once ABSPATH . 'gc-admin/admin-footer.php';
