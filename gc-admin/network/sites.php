<?php
/**
 * Multisite sites administration panel.
 *
 * @package GeChiUI
 * @subpackage Multisite
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_sites' ) ) {
	gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
}

$gc_list_table = _get_list_table( 'GC_MS_Sites_List_Table' );
$pagenum       = $gc_list_table->get_pagenum();

// Used in the HTML title tag.
$title       = __( '多系统' );
$parent_file = 'sites.php';

add_screen_option( 'per_page' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
			'<p>' . __( '“添加新系统”链接将带您到添加新系统的页面。在这里，您可以通过名称、ID或IP地址搜索某系统。在显示选项中，您可修改每页显示的系统数目。' ) . '</p>' .
			'<p>' . __( '这是本SaaS平台中所有系统的列表。您可通过点击列表上方的按钮，在“列表视图”和“摘要视图”模式间切换。' ) . '</p>' .
			'<p>' . __( '将鼠标移至系统上方，会出现7个选项（主系统则出现3个）：' ) . '</p>' .
			'<ul><li>' . __( '“编辑”链接，带您前往“编辑系统”页面。' ) . '</li>' .
			'<li>' . __( '点击“仪表盘”链接，则自动跳转至该系统的仪表盘。' ) . '</li>' .
			'<li>' . __( '点击“禁用”、“存档”或“垃圾系统”链接，则自动跳转至相应的确认页面。这些操作是可逆的。' ) . '</li>' .
			'<li>' . __( '“删除”是个永久性的操作，系统将在确认后删除。' ) . '</li>' .
			'<li>' . __( '点击“访问”可转到该系统的前端。' ) . '</li></ul>' .
			'<p>' . __( '系统ID是内部使用的，不会在系统前端显示给用户或访客。' ) . '</p>' .
			'<p>' . __( '点击粗体的标题可对列表进行重新排序。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/network-admin-sites-screen/">SaaS管理文档</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_pagination' => __( 'SaaS列表导航' ),
		'heading_list'       => __( 'SaaS列表' ),
	)
);

$id = isset( $_REQUEST['id'] ) ? (int) $_REQUEST['id'] : 0;

if ( isset( $_GET['action'] ) ) {
	/** This action is documented in gc-admin/network/edit.php */
	do_action( 'gcmuadminedit' );

	// A list of valid actions and their associated messaging for confirmation output.
	$manage_actions = array(
		/* translators: %s: Site URL. */
		'activateblog'   => __( '您将要激活系统%s。' ),
		/* translators: %s: Site URL. */
		'deactivateblog' => __( '您将要禁用系统%s。' ),
		/* translators: %s: Site URL. */
		'unarchiveblog'  => __( '您将要取消存档系统%s。' ),
		/* translators: %s: Site URL. */
		'archiveblog'    => __( '您将要存档系统%s。' ),
		/* translators: %s: Site URL. */
		'unspamblog'     => __( '您将要将系统%s标记为非垃圾。' ),
		/* translators: %s: Site URL. */
		'spamblog'       => __( '您将要将系统%s标记为垃圾。' ),
		/* translators: %s: Site URL. */
		'deleteblog'     => __( '您将要删除系统%s。' ),
		/* translators: %s: Site URL. */
		'unmatureblog'   => __( '您将要将系统%s标记为成人系统。' ),
		/* translators: %s: Site URL. */
		'matureblog'     => __( '您将要将系统%s标记为非成人系统。' ),
	);

	if ( 'confirm' === $_GET['action'] ) {
		// The action2 parameter contains the action being taken on the site.
		$site_action = $_GET['action2'];

		if ( ! array_key_exists( $site_action, $manage_actions ) ) {
			gc_die( __( '请求的操作无效。' ) );
		}

		// The mature/unmature UI exists only as external code. Check the "confirm" nonce for backward compatibility.
		if ( 'matureblog' === $site_action || 'unmatureblog' === $site_action ) {
			check_admin_referer( 'confirm' );
		} else {
			check_admin_referer( $site_action . '_' . $id );
		}

		if ( ! headers_sent() ) {
			nocache_headers();
			header( 'Content-Type: text/html; charset=utf-8' );
		}

		if ( is_main_site( $id ) ) {
			gc_die( __( '抱歉，您不能修改此系统。' ) );
		}

		$site_details = get_site( $id );
		$site_address = untrailingslashit( $site_details->domain . $site_details->path );

		require_once ABSPATH . 'gc-admin/admin-header.php';
		?>
			<div class="wrap">
				<div class="page-header"><h2 class="header-title"><?php _e( '确认您的操作' ); ?></h2></div>
				<form action="sites.php?action=<?php echo esc_attr( $site_action ); ?>" method="post">
					<input type="hidden" name="action" value="<?php echo esc_attr( $site_action ); ?>" />
					<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
					<input type="hidden" name="_gc_http_referer" value="<?php echo esc_attr( gc_get_referer() ); ?>" />
					<?php gc_nonce_field( $site_action . '_' . $id, '_gcnonce', false ); ?>
					<p><?php printf( $manage_actions[ $site_action ], $site_address ); ?></p>
					<?php submit_button( __( '确认' ), 'primary' ); ?>
				</form>
			</div>
		<?php
		require_once ABSPATH . 'gc-admin/admin-footer.php';
		exit;
	} elseif ( array_key_exists( $_GET['action'], $manage_actions ) ) {
		$action = $_GET['action'];
		check_admin_referer( $action . '_' . $id );
	} elseif ( 'allblogs' === $_GET['action'] ) {
		check_admin_referer( 'bulk-sites' );
	}

	$updated_action = '';

	switch ( $_GET['action'] ) {

		case 'deleteblog':
			if ( ! current_user_can( 'delete_sites' ) ) {
				gc_die( __( '抱歉，您不能访问此页面。' ), '', array( 'response' => 403 ) );
			}

			$updated_action = 'not_deleted';
			if ( 0 !== $id && ! is_main_site( $id ) && current_user_can( 'delete_site', $id ) ) {
				gcmu_delete_blog( $id, true );
				$updated_action = 'delete';
			}
			break;

		case 'delete_sites':
			check_admin_referer( 'ms-delete-sites' );

			foreach ( (array) $_POST['site_ids'] as $site_id ) {
				$site_id = (int) $site_id;

				if ( is_main_site( $site_id ) ) {
					continue;
				}

				if ( ! current_user_can( 'delete_site', $site_id ) ) {
					$site         = get_site( $site_id );
					$site_address = untrailingslashit( $site->domain . $site->path );

					gc_die(
						sprintf(
							/* translators: %s: Site URL. */
							__( '抱歉，您不能删除系统%s。' ),
							$site_address
						),
						403
					);
				}

				$updated_action = 'all_delete';
				gcmu_delete_blog( $site_id, true );
			}
			break;

		case 'allblogs':
			if ( isset( $_POST['action'] ) && isset( $_POST['allblogs'] ) ) {
				$doaction = $_POST['action'];

				foreach ( (array) $_POST['allblogs'] as $site_id ) {
					$site_id = (int) $site_id;

					if ( 0 !== $site_id && ! is_main_site( $site_id ) ) {
						switch ( $doaction ) {
							case 'delete':
								require_once ABSPATH . 'gc-admin/admin-header.php';
								?>
								<div class="wrap">
									<div class="page-header"><h2 class="header-title"><?php _e( '确认您的操作' ); ?></h2></div>
									<form action="sites.php?action=delete_sites" method="post">
										<input type="hidden" name="action" value="delete_sites" />
										<input type="hidden" name="_gc_http_referer" value="<?php echo esc_attr( gc_get_referer() ); ?>" />
										<?php gc_nonce_field( 'ms-delete-sites', '_gcnonce', false ); ?>
										<p><?php _e( '您将要删除以下系统：' ); ?></p>
										<ul class="ul-disc">
											<?php
											foreach ( $_POST['allblogs'] as $site_id ) :
												$site_id = (int) $site_id;

												$site         = get_site( $site_id );
												$site_address = untrailingslashit( $site->domain . $site->path );
												?>
												<li>
													<?php echo $site_address; ?>
													<input type="hidden" name="site_ids[]" value="<?php echo esc_attr( $site_id ); ?>" />
												</li>
											<?php endforeach; ?>
										</ul>
										<?php submit_button( __( '确认' ), 'primary' ); ?>
									</form>
								</div>
								<?php
								require_once ABSPATH . 'gc-admin/admin-footer.php';
								exit;
							break;

							case 'spam':
							case 'notspam':
								$updated_action = ( 'spam' === $doaction ) ? 'all_spam' : 'all_notspam';
								update_blog_status( $site_id, 'spam', ( 'spam' === $doaction ) ? '1' : '0' );
								break;
						}
					} else {
						gc_die( __( '抱歉，您不能修改此系统。' ) );
					}
				}

				if ( ! in_array( $doaction, array( 'delete', 'spam', 'notspam' ), true ) ) {
					$redirect_to = gc_get_referer();
					$blogs       = (array) $_POST['allblogs'];

					/** This action is documented in gc-admin/network/site-themes.php */
					$redirect_to = apply_filters( 'handle_network_bulk_actions-' . get_current_screen()->id, $redirect_to, $doaction, $blogs, $id ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

					gc_safe_redirect( $redirect_to );
					exit;
				}
			} else {
				// Process query defined by GC_MS_Site_List_Table::extra_table_nav().
				$location = remove_query_arg(
					array( '_gc_http_referer', '_gcnonce' ),
					add_query_arg( $_POST, network_admin_url( 'sites.php' ) )
				);

				gc_redirect( $location );
				exit;
			}

			break;

		case 'archiveblog':
		case 'unarchiveblog':
			update_blog_status( $id, 'archived', ( 'archiveblog' === $_GET['action'] ) ? '1' : '0' );
			break;

		case 'activateblog':
			update_blog_status( $id, 'deleted', '0' );

			/**
			 * Fires after a network site is activated.
			 *
			 * @since MU (3.0.0)
			 *
			 * @param int $id The ID of the activated site.
			 */
			do_action( 'activate_blog', $id );
			break;

		case 'deactivateblog':
			/**
			 * Fires before a network site is deactivated.
			 *
			 * @since MU (3.0.0)
			 *
			 * @param int $id The ID of the site being deactivated.
			 */
			do_action( 'deactivate_blog', $id );

			update_blog_status( $id, 'deleted', '1' );
			break;

		case 'unspamblog':
		case 'spamblog':
			update_blog_status( $id, 'spam', ( 'spamblog' === $_GET['action'] ) ? '1' : '0' );
			break;

		case 'unmatureblog':
		case 'matureblog':
			update_blog_status( $id, 'mature', ( 'matureblog' === $_GET['action'] ) ? '1' : '0' );
			break;
	}

	if ( empty( $updated_action ) && array_key_exists( $_GET['action'], $manage_actions ) ) {
		$updated_action = $_GET['action'];
	}

	if ( ! empty( $updated_action ) ) {
		gc_safe_redirect( add_query_arg( array( 'updated' => $updated_action ), gc_get_referer() ) );
		exit;
	}
}


if ( isset( $_GET['updated'] ) ) {
	$action = $_GET['updated'];
	$msg = '';
	switch ( $action ) {
		case 'all_notspam':
			$msg = __( '多个系统已被从垃圾系统列表中移除。' );
			break;
		case 'all_spam':
			$msg = __( '多个系统已被标记为垃圾系统。' );
			break;
		case 'all_delete':
			$msg = __( '多个系统已被删除。' );
			break;
		case 'delete':
			$msg = __( '系统已被删除。' );
			break;
		case 'not_deleted':
			$msg = __( '抱歉，您不能删除该系统。' );
			break;
		case 'archiveblog':
			$msg = __( '系统已被存档。' );
			break;
		case 'unarchiveblog':
			$msg = __( '系统未被存档。' );
			break;
		case 'activateblog':
			$msg = __( '系统已激活。' );
			break;
		case 'deactivateblog':
			$msg = __( '系统已禁用。' );
			break;
		case 'unspamblog':
			$msg = __( '系统已被从垃圾系统列表中移除。' );
			break;
		case 'spamblog':
			$msg = __( '系统已被标记为垃圾系统。' );
			break;
		default:
			/**
			 * Filters a specific, non-default, site-updated message in the Network admin.
			 *
			 * The dynamic portion of the hook name, `$action`, refers to the non-default
			 * site update action.
			 *
			 * @since 3.1.0
			 *
			 * @param string $msg The update message. Default 'Settings saved'.
			 */
			$msg = apply_filters( "network_sites_updated_message_{$action}", __( '设置已保存。' ) );
			break;
	}

	if ( ! empty( $msg ) ) {
		add_settings_error( 'general', 'message', $msg, 'success' );
	}
}

$gc_list_table->prepare_items();

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
	<div class="page-header">
		<h2 class="header-title"><?php esc_html_e( '多系统' ); ?></h2>
		<?php if ( current_user_can( 'create_sites' ) ) : ?>
			<a href="<?php echo esc_url( network_admin_url( 'site-new.php' ) ); ?>" class="btn btn-primary btn-tone btn-sm"><?php echo esc_html_x( '添加新系统', 'site' ); ?></a>
		<?php endif; ?>
	</div>

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

<?php $gc_list_table->views(); ?>

<form method="get" id="ms-search" class="gc-clearfix">
<?php $gc_list_table->search_box( __( '搜索系统' ), 'site' ); ?>
<input type="hidden" name="action" value="blogs" />
</form>

<form id="form-site-list" action="sites.php?action=allblogs" method="post">
	<?php $gc_list_table->display(); ?>
</form>
</div>
<?php

require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
