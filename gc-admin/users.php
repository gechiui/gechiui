<?php
/**
 * User administration panel
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'list_users' ) ) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能列出用户。' ) . '</p>',
		403
	);
}

$gc_list_table = _get_list_table( 'GC_Users_List_Table' );
$pagenum       = $gc_list_table->get_pagenum();

// Used in the HTML title tag.
$title       = __( '用户' );
$parent_file = 'users.php';

add_screen_option( 'per_page' );

// Contextual help - choose Help on the top right of admin panel to preview this.
get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => '<p>' . __( '本页面列出了您系统当前的所有用户。根据系统管理员的意愿，每位用户都有下列五种用户角色中的其中一种：系统管理员、编辑、作者、贡献者或订阅者。在用户登录到仪表盘后，权限低于管理员角色的用户，只能基于其权限看到部分选项。' ) . '</p>' .
						'<p>' . __( '要添加一位新用户到系统中，点击屏幕上方的“添加用户”按钮，或选择左侧菜单中的“用户”→“添加用户”。' ) . '</p>',
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => 'screen-content',
		'title'   => __( '页面内容' ),
		'content' => '<p>' . __( '您可以通过以下方法来自定义本页面：' ) . '</p>' .
						'<ul>' .
						'<li>' . __( '您可在“显示选项”中按需隐藏或显示各个栏目，并决定每页显示的用户数。' ) . '</li>' .
						'<li>' . __( '您可以使用用户列表上方的文字链接按用户角色筛选用户列表，可显示所有、管理员、编辑、作者、贡献者或订阅者。默认视图为显示所有用户。未使用的用户角色不会被列出。' ) . '</li>' .
						'<li>' . __( 'GeChiUI可列出一个用户所发布的所有文章——点击列表中相应用户“文章”一栏的数字来查看该人所写的文章。' ) . '</li>' .
						'</ul>',
	)
);

$help = '<p>' . __( '将鼠标光标悬停在用户列表中的某一行，操作链接将会显示出来，您可以通过它们快速管理用户。您可进行下列操作：' ) . '</p>' .
	'<ul>' .
	'<li>' . __( '点击“<strong>编辑</strong>”可在个人资料编辑器中编辑该用户。当然，直接点击用户名也是可以的。' ) . '</li>';

if ( is_multisite() ) {
	$help .= '<li>' . __( '点击“<strong>移除</strong>”可以将用户从您的系统中移除。该操作不会删除用户之前所发布的内容。您也可以通过批量操作功能一次移除多个用户。' ) . '</li>';
} else {
	$help .= '<li>' . __( '“<strong>删除</strong>”链接会带您到“删除用户”确认页面。在确认页面，您可以永久从系统中删除用户，并决定是否保留他们的内容。您也可以通过批量操作功能一次删除多个用户。' ) . '</li>';
}

$help .= '</ul>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'action-links',
		'title'   => __( '可进行的操作' ),
		'content' => $help,
	)
);
unset( $help );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/users-screen/">管理用户文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/roles-and-capabilities/">角色和能力文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( '筛选用户列表' ),
		'heading_pagination' => __( '用户列表导航' ),
		'heading_list'       => __( '用户列表' ),
	)
);

if ( empty( $_REQUEST ) ) {
	$referer = '<input type="hidden" name="gc_http_referer" value="' . esc_attr( gc_unslash( $_SERVER['REQUEST_URI'] ) ) . '" />';
} elseif ( isset( $_REQUEST['gc_http_referer'] ) ) {
	$redirect = remove_query_arg( array( 'gc_http_referer', 'updated', 'delete_count' ), gc_unslash( $_REQUEST['gc_http_referer'] ) );
	$referer  = '<input type="hidden" name="gc_http_referer" value="' . esc_attr( $redirect ) . '" />';
} else {
	$redirect = 'users.php';
	$referer  = '';
}

$update = '';

switch ( $gc_list_table->current_action() ) {

	/* Bulk Dropdown menu Role changes */
	case 'promote':
		check_admin_referer( 'bulk-users' );

		if ( ! current_user_can( 'promote_users' ) ) {
			gc_die( __( '抱歉，您不能编辑此用户。' ), 403 );
		}

		if ( empty( $_REQUEST['users'] ) ) {
			gc_redirect( $redirect );
			exit;
		}

		$editable_roles = get_editable_roles();
		$role           = $_REQUEST['new_role'];

		// Mocking the `none` role so we are able to save it to the database
		$editable_roles['none'] = array(
			'name' => __( '—这个系统没有任何用户角色—' ),
		);

		if ( ! $role || empty( $editable_roles[ $role ] ) ) {
			gc_die( __( '抱歉，您不能将此角色给予用户。' ), 403 );
		}

		if ( 'none' === $role ) {
			$role = '';
		}

		$userids = $_REQUEST['users'];
		$update  = 'promote';
		foreach ( $userids as $id ) {
			$id = (int) $id;

			if ( ! current_user_can( 'promote_user', $id ) ) {
				gc_die( __( '抱歉，您不能编辑此用户。' ), 403 );
			}

			// The new role of the current user must also have the promote_users cap or be a multisite super admin.
			if ( $id == $current_user->ID && ! $gc_roles->role_objects[ $role ]->has_cap( 'promote_users' )
			&& ! ( is_multisite() && current_user_can( 'manage_network_users' ) ) ) {
					$update = 'err_admin_role';
					continue;
			}

			// If the user doesn't already belong to the blog, bail.
			if ( is_multisite() && ! is_user_member_of_blog( $id ) ) {
				gc_die(
					'<h1>' . __( '出现了问题。' ) . '</h1>' .
					'<p>' . __( '选择的用户之一不是该系统的成员。' ) . '</p>',
					403
				);
			}

			$user = get_userdata( $id );
			$user->set_role( $role );
		}

		gc_redirect( add_query_arg( 'update', $update, $redirect ) );
		exit;

	case 'dodelete':
		if ( is_multisite() ) {
			gc_die( __( '不能在本页面删除用户。' ), 400 );
		}

		check_admin_referer( 'delete-users' );

		if ( empty( $_REQUEST['users'] ) ) {
			gc_redirect( $redirect );
			exit;
		}

		$userids = array_map( 'intval', (array) $_REQUEST['users'] );

		if ( empty( $_REQUEST['delete_option'] ) ) {
			$url = self_admin_url( 'users.php?action=delete&users[]=' . implode( '&users[]=', $userids ) . '&error=true' );
			$url = str_replace( '&amp;', '&', gc_nonce_url( $url, 'bulk-users' ) );
			gc_redirect( $url );
			exit;
		}

		if ( ! current_user_can( 'delete_users' ) ) {
			gc_die( __( '抱歉，您不能删除用户。' ), 403 );
		}

		$update       = 'del';
		$delete_count = 0;

		foreach ( $userids as $id ) {
			if ( ! current_user_can( 'delete_user', $id ) ) {
				gc_die( __( '抱歉，您不能删除那些用户。' ), 403 );
			}

			if ( $id == $current_user->ID ) {
				$update = 'err_admin_del';
				continue;
			}
			switch ( $_REQUEST['delete_option'] ) {
				case 'delete':
					gc_delete_user( $id );
					break;
				case 'reassign':
					gc_delete_user( $id, $_REQUEST['reassign_user'] );
					break;
			}
			++$delete_count;
		}

		$redirect = add_query_arg(
			array(
				'delete_count' => $delete_count,
				'update'       => $update,
			),
			$redirect
		);
		gc_redirect( $redirect );
		exit;

	case 'resetpassword':
		check_admin_referer( 'bulk-users' );
		if ( ! current_user_can( 'edit_users' ) ) {
			$errors = new GC_Error( 'edit_users', __( '抱歉，您不能编辑该评论。' ) );
		}
		if ( empty( $_REQUEST['users'] ) ) {
			gc_redirect( $redirect );
			exit();
		}
		$userids = array_map( 'intval', (array) $_REQUEST['users'] );

		$reset_count = 0;

		foreach ( $userids as $id ) {
			if ( ! current_user_can( 'edit_user', $id ) ) {
				gc_die( __( '抱歉，您不能编辑此用户。' ) );
			}

			if ( $id === $current_user->ID ) {
				$update = 'err_admin_reset';
				continue;
			}

			// Send the password reset link.
			$user = get_userdata( $id );
			if ( retrieve_password( $user->user_login ) ) {
				++$reset_count;
			}
		}

		$redirect = add_query_arg(
			array(
				'reset_count' => $reset_count,
				'update'      => 'resetpassword',
			),
			$redirect
		);
		gc_redirect( $redirect );
		exit;

	case 'delete':
		if ( is_multisite() ) {
			gc_die( __( '不能在本页面删除用户。' ), 400 );
		}

		check_admin_referer( 'bulk-users' );

		if ( empty( $_REQUEST['users'] ) && empty( $_REQUEST['user'] ) ) {
			gc_redirect( $redirect );
			exit;
		}

		if ( ! current_user_can( 'delete_users' ) ) {
			$errors = new GC_Error( 'edit_users', __( '抱歉，您不能删除用户。' ) );
		}

		if ( empty( $_REQUEST['users'] ) ) {
			$userids = array( (int) $_REQUEST['user'] );
		} else {
			$userids = array_map( 'intval', (array) $_REQUEST['users'] );
		}

		$all_userids = $userids;

		if ( in_array( $current_user->ID, $userids, true ) ) {
			$userids = array_diff( $userids, array( $current_user->ID ) );
		}

		/**
		 * Filters whether the users being deleted have additional content
		 * associated with them outside of the `post_author` and `link_owner` relationships.
		 *
		 * @param bool  $users_have_additional_content Whether the users have additional content. Default false.
		 * @param int[] $userids                       Array of IDs for users being deleted.
		 */
		$users_have_content = (bool) apply_filters( 'users_have_additional_content', false, $userids );

		if ( $userids && ! $users_have_content ) {
			if ( $gcdb->get_var( "SELECT ID FROM {$gcdb->posts} WHERE post_author IN( " . implode( ',', $userids ) . ' ) LIMIT 1' ) ) {
				$users_have_content = true;
			} elseif ( $gcdb->get_var( "SELECT link_id FROM {$gcdb->links} WHERE link_owner IN( " . implode( ',', $userids ) . ' ) LIMIT 1' ) ) {
				$users_have_content = true;
			}
		}

		if ( $users_have_content ) {
			add_action( 'admin_head', 'delete_users_add_js' );
		}

		if ( isset( $_REQUEST['error'] ) ) {
			$message = '<strong>' . _e( '错误：' ) . '</strong>' . _e( '请选择一个选项。' );
			add_settings_error( 'general', 'settings_updated', $message, 'danger' );
		}

		require_once ABSPATH . 'gc-admin/admin-header.php';
		?>
	<form method="post" name="updateusers" id="updateusers">
		<?php gc_nonce_field( 'delete-users' ); ?>
		<?php echo $referer; ?>

<div class="wrap">
<div class="page-header"><h2 class="header-title"><?php _e( '删除用户' ); ?></h2></div>

		<?php if ( 1 === count( $all_userids ) ) : ?>
	<p><?php _e( '您已指定删除此用户：' ); ?></p>
		<?php else : ?>
	<p><?php _e( '您已指定删除下列用户：' ); ?></p>
		<?php endif; ?>

<ul>
		<?php
		$go_delete = 0;
		foreach ( $all_userids as $id ) {
			$user = get_userdata( $id );
			if ( $id == $current_user->ID ) {
				/* translators: 1: User ID, 2: User login. */
				echo '<li>' . sprintf( __( 'ID #%1$s：%2$s <strong>当前用户不会被删除。</strong>' ), $id, $user->user_login ) . "</li>\n";
			} else {
				/* translators: 1: User ID, 2: User login. */
				echo '<li><input type="hidden" name="users[]" value="' . esc_attr( $id ) . '" />' . sprintf( __( 'ID #%1$s：%2$s' ), $id, $user->user_login ) . "</li>\n";
				$go_delete++;
			}
		}
		?>
	</ul>
		<?php
		if ( $go_delete ) :

			if ( ! $users_have_content ) :
				?>
			<input type="hidden" name="delete_option" value="delete" />
			<?php else : ?>
				<?php if ( 1 == $go_delete ) : ?>
			<fieldset><p><legend><?php _e( '如何处理该用户名下的内容？' ); ?></legend></p>
		<?php else : ?>
			<fieldset><p><legend><?php _e( '如何处理这些用户名下的内容？' ); ?></legend></p>
		<?php endif; ?>
		<ul style="list-style:none;">
			<li><label><input type="radio" id="delete_option0" name="delete_option" value="delete" />
				<?php _e( '删除所有内容。' ); ?></label></li>
			<li><input type="radio" id="delete_option1" name="delete_option" value="reassign" />
				<?php
				echo '<label for="delete_option1">' . __( '将这些内容的作者修改为：' ) . '</label> ';
				gc_dropdown_users(
					array(
						'name'    => 'reassign_user',
						'exclude' => $userids,
						'show'    => 'display_name_with_login',
					)
				);
				?>
			</li>
		</ul></fieldset>
				<?php
	endif;
			/**
			 * Fires at the end of the delete users form prior to the confirm button.
			 *
		
		
			 *
			 * @param GC_User $current_user GC_User object for the current user.
			 * @param int[]   $userids      Array of IDs for users being deleted.
			 */
			do_action( 'delete_user_form', $current_user, $userids );
			?>
	<input type="hidden" name="action" value="dodelete" />
			<?php submit_button( __( '确认删除' ), 'primary' ); ?>
	<?php else : ?>
	<p><?php _e( '没有可供删除的有效用户。' ); ?></p>
	<?php endif; ?>
	</div>
	</form>
		<?php

		break;

	case 'doremove':
		check_admin_referer( 'remove-users' );

		if ( ! is_multisite() ) {
			gc_die( __( '您无法移除用户。' ), 400 );
		}

		if ( empty( $_REQUEST['users'] ) ) {
			gc_redirect( $redirect );
			exit;
		}

		if ( ! current_user_can( 'remove_users' ) ) {
			gc_die( __( '抱歉，您不能移除用户。' ), 403 );
		}

		$userids = $_REQUEST['users'];

		$update = 'remove';
		foreach ( $userids as $id ) {
			$id = (int) $id;
			if ( ! current_user_can( 'remove_user', $id ) ) {
				$update = 'err_admin_remove';
				continue;
			}
			remove_user_from_blog( $id, $blog_id );
		}

		$redirect = add_query_arg( array( 'update' => $update ), $redirect );
		gc_redirect( $redirect );
		exit;

	case 'remove':
		check_admin_referer( 'bulk-users' );

		if ( ! is_multisite() ) {
			gc_die( __( '您无法移除用户。' ), 400 );
		}

		if ( empty( $_REQUEST['users'] ) && empty( $_REQUEST['user'] ) ) {
			gc_redirect( $redirect );
			exit;
		}

		if ( ! current_user_can( 'remove_users' ) ) {
			$error = new GC_Error( 'edit_users', __( '抱歉，您不能移除用户。' ) );
		}

		if ( empty( $_REQUEST['users'] ) ) {
			$userids = array( (int) $_REQUEST['user'] );
		} else {
			$userids = $_REQUEST['users'];
		}

		require_once ABSPATH . 'gc-admin/admin-header.php';
		?>
	<form method="post" name="updateusers" id="updateusers">
		<?php gc_nonce_field( 'remove-users' ); ?>
		<?php echo $referer; ?>

<div class="wrap">
<div class="page-header"><h2 class="header-title"><?php _e( '从系统移除用户' ); ?></h2></div>

		<?php if ( 1 === count( $userids ) ) : ?>
	<p><?php _e( '您将要删除这名用户：' ); ?></p>
		<?php else : ?>
	<p><?php _e( '您要删除的用户：' ); ?></p>
		<?php endif; ?>

<ul>
		<?php
		$go_remove = false;
		foreach ( $userids as $id ) {
			$id   = (int) $id;
			$user = get_userdata( $id );
			if ( ! current_user_can( 'remove_user', $id ) ) {
				/* translators: 1: User ID, 2: User login. */
				echo '<li>' . sprintf( __( 'ID #%1$s：%2$s <strong>抱歉，您不能移除此用户。</strong>' ), $id, $user->user_login ) . "</li>\n";
			} else {
				/* translators: 1: User ID, 2: User login. */
				echo "<li><input type=\"hidden\" name=\"users[]\" value=\"{$id}\" />" . sprintf( __( 'ID #%1$s：%2$s' ), $id, $user->user_login ) . "</li>\n";
				$go_remove = true;
			}
		}
		?>
	</ul>
		<?php if ( $go_remove ) : ?>
		<input type="hidden" name="action" value="doremove" />
			<?php submit_button( __( '确认删除' ), 'primary' ); ?>
	<?php else : ?>
	<p><?php _e( '选取时没有选取可删除的用户。' ); ?></p>
	<?php endif; ?>
	</div>
	</form>
		<?php

		break;

	default:
		if ( ! empty( $_GET['_gc_http_referer'] ) ) {
			gc_redirect( remove_query_arg( array( '_gc_http_referer', '_gcnonce' ), gc_unslash( $_SERVER['REQUEST_URI'] ) ) );
			exit;
		}

		if ( $gc_list_table->current_action() && ! empty( $_REQUEST['users'] ) ) {
			$screen   = get_current_screen()->id;
			$sendback = gc_get_referer();
			$userids  = $_REQUEST['users'];

			/** This action is documented in gc-admin/edit.php */
			$sendback = apply_filters( "handle_bulk_actions-{$screen}", $sendback, $gc_list_table->current_action(), $userids ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

			gc_safe_redirect( $sendback );
			exit;
		}

		$gc_list_table->prepare_items();
		$total_pages = $gc_list_table->get_pagination_arg( 'total_pages' );
		if ( $pagenum > $total_pages && $total_pages > 0 ) {
			gc_redirect( add_query_arg( 'paged', $total_pages ) );
			exit;
		}

		if ( isset( $_GET['update'] ) ) {
			switch ( $_GET['update'] ) {
				case 'del':
				case 'del_many':
					$delete_count = isset( $_GET['delete_count'] ) ? (int) $_GET['delete_count'] : 0;
					if ( 1 == $delete_count ) {
						$message = __( '用户已删除。' );
					} else {
						/* translators: %s: Number of users. */
						$message = _n( '已删除 %s 个用户。', '已删除 %s 个用户。', $delete_count );
					}
					add_settings_error( 'general', 'settings_updated', sprintf( $message, number_format_i18n( $delete_count ) ), 'success' );
					break;
				case 'add':
					$message = __( '新用户已创建。' );

					$user_id = isset( $_GET['id'] ) ? $_GET['id'] : false;
					if ( $user_id && current_user_can( 'edit_user', $user_id ) ) {
						$message .= sprintf(
							' <a href="%s">%s</a>',
							esc_url(
								add_query_arg(
									'gc_http_referer',
									urlencode( gc_unslash( $_SERVER['REQUEST_URI'] ) ),
									self_admin_url( 'user-edit.php?user_id=' . $user_id )
								)
							),
							__( '编辑用户' )
						);
					}
					add_settings_error( 'general', 'settings_updated', $message, 'success' );
					break;
				case 'resetpassword':
					$reset_count = isset( $_GET['reset_count'] ) ? (int) $_GET['reset_count'] : 0;
					if ( 1 === $reset_count ) {
						$message = __( '密码重置链接已发送。' );
					} else {
						/* translators: %s: Number of users. */
						$message = _n( '密码重置链接已发送给%s位用户。', '密码重置链接已发送给%s位用户。', $reset_count );
					}
					add_settings_error( 'general', 'settings_updated', sprintf( $message, number_format_i18n( $reset_count ) ), 'success' );
					break;
				case 'promote':
					add_settings_error( 'general', 'settings_updated', __( '角色已改变。' ), 'success' );
					break;
				case 'err_admin_role':
					add_settings_error( 'general', 'settings_updated', __( '当前用户角色必须有用户编辑权。' ), 'danger' );
					add_settings_error( 'general', 'settings_updated', __( '其他用户的角色已更改。' ), 'success' );
					break;
				case 'err_admin_del':
					add_settings_error( 'general', 'settings_updated', __( '您无法删除当前用户。' ), 'danger' );
					add_settings_error( 'general', 'settings_updated', __( '其他用户已被删除。' ), 'success' );
					break;
				case 'remove':
					add_settings_error( 'general', 'settings_updated', __( '用户已从本系统移除。' ), 'success' );
					break;
				case 'err_admin_remove':
					add_settings_error( 'general', 'settings_updated', __( '您无法移除当前用户。' ), 'danger' );
					add_settings_error( 'general', 'settings_updated', __( '其他用户已被移除。' ), 'success' );
					break;
			}
		}

		if ( isset( $errors ) && is_gc_error( $errors ) ) {

			foreach ( $errors->get_error_messages() as $err ) {
				add_settings_error( 'general', 'settings_updated', $err, 'danger' );
			}

		}

		require_once ABSPATH . 'gc-admin/admin-header.php';

		?>

	<div class="wrap">
	<div class="page-header">
		<h2 class="header-title"><?php echo esc_html( $title ); ?></h2>

		<?php
		if ( current_user_can( 'create_users' ) ) {
			?>
		<a href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>" class="btn btn-primary btn-tone btn-sm"><?php echo esc_html_x( '添加用户', 'user' ); ?></a>
		<?php } elseif ( is_multisite() && current_user_can( 'promote_users' ) ) { ?>
		<a href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>" class="btn btn-primary btn-tone btn-sm"><?php echo esc_html_x( '添加现有用户', 'user' ); ?></a>
				<?php
		}

		if ( strlen( $usersearch ) ) {
			echo '<span class="subtitle">';
			printf(
				/* translators: %s: Search query. */
				__( '搜索词：%s' ),
				'<strong>' . esc_html( $usersearch ) . '</strong>'
			);
			echo '</span>';
		}
		?>

	</div>

		<?php $gc_list_table->views(); ?>

<form method="get">

		<?php $gc_list_table->search_box( __( '搜索用户' ), 'user' ); ?>

		<?php if ( ! empty( $_REQUEST['role'] ) ) { ?>
<input type="hidden" name="role" value="<?php echo esc_attr( $_REQUEST['role'] ); ?>" />
<?php } ?>

		<?php $gc_list_table->display(); ?>
</form>

<div class="clear"></div>
</div>
		<?php
		break;

} // End of the $doaction switch.

require_once ABSPATH . 'gc-admin/admin-footer.php';
