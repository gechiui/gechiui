<?php
/**
 * Multisite users administration panel.
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_network_users' ) ) {
	gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
}

if ( isset( $_GET['action'] ) ) {
	/** This action is documented in gc-admin/network/edit.php */
	do_action( 'gcmuadminedit' );

	switch ( $_GET['action'] ) {
		case 'deleteuser':
			if ( ! current_user_can( 'manage_network_users' ) ) {
				gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
			}

			check_admin_referer( 'deleteuser' );

			$id = (int) $_GET['id'];
			if ( $id > 1 ) {
				$_POST['allusers'] = array( $id ); // confirm_delete_users() can only handle arrays.

				// Used in the HTML title tag.
				$title       = __( '用户' );
				$parent_file = 'users.php';

				require_once ABSPATH . 'gc-admin/admin-header.php';

				echo '<div class="wrap">';
				confirm_delete_users( $_POST['allusers'] );
				echo '</div>';

				require_once ABSPATH . 'gc-admin/admin-footer.php';
			} else {
				gc_redirect( network_admin_url( 'users.php' ) );
			}
			exit;

		case 'allusers':
			if ( ! current_user_can( 'manage_network_users' ) ) {
				gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
			}

			if ( isset( $_POST['action'] ) && isset( $_POST['allusers'] ) ) {
				check_admin_referer( 'bulk-users-network' );

				$doaction     = $_POST['action'];
				$userfunction = '';

				foreach ( (array) $_POST['allusers'] as $user_id ) {
					if ( ! empty( $user_id ) ) {
						switch ( $doaction ) {
							case 'delete':
								if ( ! current_user_can( 'delete_users' ) ) {
									gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
								}

								// Used in the HTML title tag.
								$title       = __( '用户' );
								$parent_file = 'users.php';

								require_once ABSPATH . 'gc-admin/admin-header.php';

								echo '<div class="wrap">';
								confirm_delete_users( $_POST['allusers'] );
								echo '</div>';

								require_once ABSPATH . 'gc-admin/admin-footer.php';
								exit;

							case 'spam':
								$user = get_userdata( $user_id );
								if ( is_super_admin( $user->ID ) ) {
									gc_die(
										sprintf(
											/* translators: %s: User login. */
											__( '警告！无法修改%s，该用户是网络管理员。' ),
											esc_html( $user->user_login )
										)
									);
								}

								$userfunction = 'all_spam';
								$blogs        = get_blogs_of_user( $user_id, true );

								foreach ( (array) $blogs as $details ) {
									if ( get_network()->site_id != $details->userblog_id ) { // Main blog is not a spam!
										update_blog_status( $details->userblog_id, 'spam', '1' );
									}
								}

								$user_data         = $user->to_array();
								$user_data['spam'] = '1';

								gc_update_user( $user_data );
								break;

							case 'notspam':
								$user = get_userdata( $user_id );

								$userfunction = 'all_notspam';
								$blogs        = get_blogs_of_user( $user_id, true );

								foreach ( (array) $blogs as $details ) {
									update_blog_status( $details->userblog_id, 'spam', '0' );
								}

								$user_data         = $user->to_array();
								$user_data['spam'] = '0';

								gc_update_user( $user_data );
								break;
						}
					}
				}

				if ( ! in_array( $doaction, array( 'delete', 'spam', 'notspam' ), true ) ) {
					$sendback = gc_get_referer();
					$user_ids = (array) $_POST['allusers'];

					/** This action is documented in gc-admin/network/site-themes.php */
					$sendback = apply_filters( 'handle_network_bulk_actions-' . get_current_screen()->id, $sendback, $doaction, $user_ids ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

					gc_safe_redirect( $sendback );
					exit;
				}

				gc_safe_redirect(
					add_query_arg(
						array(
							'updated' => 'true',
							'action'  => $userfunction,
						),
						gc_get_referer()
					)
				);
			} else {
				$location = network_admin_url( 'users.php' );

				if ( ! empty( $_REQUEST['paged'] ) ) {
					$location = add_query_arg( 'paged', (int) $_REQUEST['paged'], $location );
				}
				gc_redirect( $location );
			}
			exit;

		case 'dodelete':
			check_admin_referer( 'ms-users-delete' );
			if ( ! ( current_user_can( 'manage_network_users' ) && current_user_can( 'delete_users' ) ) ) {
				gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
			}

			if ( ! empty( $_POST['blog'] ) && is_array( $_POST['blog'] ) ) {
				foreach ( $_POST['blog'] as $id => $users ) {
					foreach ( $users as $blogid => $user_id ) {
						if ( ! current_user_can( 'delete_user', $id ) ) {
							continue;
						}

						if ( ! empty( $_POST['delete'] ) && 'reassign' === $_POST['delete'][ $blogid ][ $id ] ) {
							remove_user_from_blog( $id, $blogid, (int) $user_id );
						} else {
							remove_user_from_blog( $id, $blogid );
						}
					}
				}
			}

			$i = 0;

			if ( is_array( $_POST['user'] ) && ! empty( $_POST['user'] ) ) {
				foreach ( $_POST['user'] as $id ) {
					if ( ! current_user_can( 'delete_user', $id ) ) {
						continue;
					}
					gcmu_delete_user( $id );
					$i++;
				}
			}

			if ( 1 === $i ) {
				$deletefunction = 'delete';
			} else {
				$deletefunction = 'all_delete';
			}

			gc_redirect(
				add_query_arg(
					array(
						'updated' => 'true',
						'action'  => $deletefunction,
					),
					network_admin_url( 'users.php' )
				)
			);
			exit;
	}
}

$gc_list_table = _get_list_table( 'GC_MS_Users_List_Table' );
$pagenum       = $gc_list_table->get_pagenum();
$gc_list_table->prepare_items();
$total_pages = $gc_list_table->get_pagination_arg( 'total_pages' );

if ( $pagenum > $total_pages && $total_pages > 0 ) {
	gc_redirect( add_query_arg( 'paged', $total_pages ) );
	exit;
}

// Used in the HTML title tag.
$title       = __( '用户' );
$parent_file = 'users.php';

add_screen_option( 'per_page' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
			'<p>' . __( '本表格列出了站点网络中的所有用户，以及它们所在的站点。' ) . '</p>' .
			'<p>' . __( '将鼠标移至用户的上方，将出现编辑链接。左侧的编辑链接是编辑用户信息的；而右侧的编辑链接用于编辑其所属站点的信息。' ) . '</p>' .
			'<p>' . __( '您也可以通过点击用户名转到用户的个人资料页面。' ) . '</p>' .
			'<p>' . __( '您可以点击表头来排序，也可以使用用户列表上方的图标来切换列表和摘要视图。' ) . '</p>' .
			'<p>' . __( '批量操作将永久删除选中的用户，或标记/取消标记选择的用户为垃圾用户。垃圾用户发布的文章将被移除，并无法再使用相同的电子邮箱注册。' ) . '</p>' .
			'<p>' . __( '您可以让一个现有的用户成为额外的超级管理员，方法是进入编辑用户个人资料的页面，勾选方框以授予该权限。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.gechiui.com/Network_Admin_Users_Screen">站点网络用户文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/forum/issues/multisite/">支持论坛</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( '筛选用户列表' ),
		'heading_pagination' => __( '用户列表导航' ),
		'heading_list'       => __( '用户列表' ),
	)
);

require_once ABSPATH . 'gc-admin/admin-header.php';

if ( isset( $_REQUEST['updated'] ) && 'true' == $_REQUEST['updated'] && ! empty( $_REQUEST['action'] ) ) {
	?>
	<div id="message" class="updated notice is-dismissible"><p>
		<?php
		switch ( $_REQUEST['action'] ) {
			case 'delete':
				_e( '用户已删除。' );
				break;
			case 'all_spam':
				_e( '用户已被标记为垃圾用户。' );
				break;
			case 'all_notspam':
				_e( '多个用户已被从垃圾用户列表中移除。' );
				break;
			case 'all_delete':
				_e( '用户已被删除。' );
				break;
			case 'add':
				_e( '用户已添加。' );
				break;
		}
		?>
	</p></div>
	<?php
}
?>
<div class="wrap">
	<h1 class="gc-heading-inline"><?php esc_html_e( '用户' ); ?></h1>

	<?php
	if ( current_user_can( 'create_users' ) ) :
		?>
		<a href="<?php echo esc_url( network_admin_url( 'user-new.php' ) ); ?>" class="page-title-action"><?php echo esc_html_x( '添加用户', 'user' ); ?></a>
		<?php
	endif;

	if ( strlen( $usersearch ) ) {
		echo '<span class="subtitle">';
		printf(
			/* translators: %s: Search query. */
			__( '搜索结果：%s' ),
			'<strong>' . esc_html( $usersearch ) . '</strong>'
		);
		echo '</span>';
	}
	?>

	<hr class="gc-header-end">

	<?php $gc_list_table->views(); ?>

	<form method="get" class="search-form">
		<?php $gc_list_table->search_box( __( '搜索用户' ), 'all-user' ); ?>
	</form>

	<form id="form-user-list" action="users.php?action=allusers" method="post">
		<?php $gc_list_table->display(); ?>
	</form>
</div>

<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
