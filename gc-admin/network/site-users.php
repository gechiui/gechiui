<?php
/**
 * Edit Site Users Administration Screen
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_sites' ) ) {
	gc_die( __( '抱歉，您不能编辑此系统。' ), 403 );
}

$gc_list_table = _get_list_table( 'GC_Users_List_Table' );
$gc_list_table->prepare_items();

get_current_screen()->add_help_tab( get_site_screen_help_tab_args() );
get_current_screen()->set_help_sidebar( get_site_screen_help_sidebar_content() );

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( '过滤系统用户列表' ),
		'heading_pagination' => __( '系统用户列表导航' ),
		'heading_list'       => __( '系统用户列表' ),
	)
);

$_SERVER['REQUEST_URI'] = remove_query_arg( 'update', $_SERVER['REQUEST_URI'] );
$referer                = remove_query_arg( 'update', gc_get_referer() );

if ( ! empty( $_REQUEST['paged'] ) ) {
	$referer = add_query_arg( 'paged', (int) $_REQUEST['paged'], $referer );
}

$id = isset( $_REQUEST['id'] ) ? (int) $_REQUEST['id'] : 0;

if ( ! $id ) {
	gc_die( __( '系统ID无效。' ) );
}

$details = get_site( $id );
if ( ! $details ) {
	gc_die( __( '请求的系统不存在。' ) );
}

if ( ! can_edit_network( $details->site_id ) ) {
	gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
}

$is_main_site = is_main_site( $id );

switch_to_blog( $id );

$action = $gc_list_table->current_action();

if ( $action ) {

	switch ( $action ) {
		case 'newuser':
			check_admin_referer( 'add-user', '_gcnonce_add-new-user' );
			$user = $_POST['user'];
			if ( ! is_array( $_POST['user'] ) || empty( $user['username'] ) || empty( $user['email'] ) ) {
				$update = 'err_new';
			} else {
				$password = gc_generate_password( 12, false );
				$user_id  = gcmu_create_user( esc_html( strtolower( $user['username'] ) ), $password, esc_html( $user['email'] ) );

				if ( false === $user_id ) {
					$update = 'err_new_dup';
				} else {
					$result = add_user_to_blog( $id, $user_id, $_POST['new_role'] );

					if ( is_gc_error( $result ) ) {
						$update = 'err_add_fail';
					} else {
						$update = 'newuser';

						/**
						 * Fires after a user has been created via the network site-users.php page.
						 *
					
						 *
						 * @param int $user_id ID of the newly created user.
						 */
						do_action( 'network_site_users_created_user', $user_id );
					}
				}
			}
			break;

		case 'adduser':
			check_admin_referer( 'add-user', '_gcnonce_add-user' );
			if ( ! empty( $_POST['newuser'] ) ) {
				$update  = 'adduser';
				$newuser = $_POST['newuser'];
				$user    = get_user_by( 'login', $newuser );
				if ( $user && $user->exists() ) {
					if ( ! is_user_member_of_blog( $user->ID, $id ) ) {
						$result = add_user_to_blog( $id, $user->ID, $_POST['new_role'] );

						if ( is_gc_error( $result ) ) {
							$update = 'err_add_fail';
						}
					} else {
						$update = 'err_add_member';
					}
				} else {
					$update = 'err_add_notfound';
				}
			} else {
				$update = 'err_add_notfound';
			}
			break;

		case 'remove':
			if ( ! current_user_can( 'remove_users' ) ) {
				gc_die( __( '抱歉，您不能移除用户。' ), 403 );
			}

			check_admin_referer( 'bulk-users' );

			$update = 'remove';
			if ( isset( $_REQUEST['users'] ) ) {
				$userids = $_REQUEST['users'];

				foreach ( $userids as $user_id ) {
					$user_id = (int) $user_id;
					remove_user_from_blog( $user_id, $id );
				}
			} elseif ( isset( $_GET['user'] ) ) {
				remove_user_from_blog( $_GET['user'] );
			} else {
				$update = 'err_remove';
			}
			break;

		case 'promote':
			check_admin_referer( 'bulk-users' );
			$editable_roles = get_editable_roles();
			$role           = $_REQUEST['new_role'];

			if ( empty( $editable_roles[ $role ] ) ) {
				gc_die( __( '抱歉，您不能将此角色给予用户。' ), 403 );
			}

			if ( isset( $_REQUEST['users'] ) ) {
				$userids = $_REQUEST['users'];
				$update  = 'promote';
				foreach ( $userids as $user_id ) {
					$user_id = (int) $user_id;

					// If the user doesn't already belong to the blog, bail.
					if ( ! is_user_member_of_blog( $user_id ) ) {
						gc_die(
							'<h1>' . __( '出现了问题。' ) . '</h1>' .
							'<p>' . __( '选择的用户之一不是该系统的成员。' ) . '</p>',
							403
						);
					}

					$user = get_userdata( $user_id );
					$user->set_role( $role );
				}
			} else {
				$update = 'err_promote';
			}
			break;
		default:
			if ( ! isset( $_REQUEST['users'] ) ) {
				break;
			}
			check_admin_referer( 'bulk-users' );
			$userids = $_REQUEST['users'];

			/** This action is documented in gc-admin/network/site-themes.php */
			$referer = apply_filters( 'handle_network_bulk_actions-' . get_current_screen()->id, $referer, $action, $userids, $id ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

			$update = $action;
			break;
	}

	gc_safe_redirect( add_query_arg( 'update', $update, $referer ) );
	exit;
}

restore_current_blog();

if ( isset( $_GET['action'] ) && 'update-site' === $_GET['action'] ) {
	gc_safe_redirect( $referer );
	exit;
}

add_screen_option( 'per_page' );

// Used in the HTML title tag.
/* translators: %s: Site title. */
$title = sprintf( __( '编辑系统：%s' ), esc_html( $details->blogname ) );

$parent_file  = 'sites.php';
$submenu_file = 'sites.php';

/**
 * Filters whether to show the Add Existing User form on the Multisite Users screen.
 *
 *
 * @param bool $bool Whether to show the Add Existing User form. Default true.
 */
if ( ! gc_is_large_network( 'users' ) && apply_filters( 'show_network_site_users_add_existing_form', true ) ) {
	gc_enqueue_script( 'user-suggest' );
}

network_edit_site_nav(
	array(
		'blog_id'  => $id,
		'selected' => 'site-users',
	)
);

if ( isset( $_GET['update'] ) ) {
	switch ( $_GET['update'] ) {
		case 'adduser':
			add_settings_error( 'general', 'settings_updated', __( '用户已添加。' ), 'success' );
			break;
		case 'err_add_member':
			add_settings_error( 'general', 'settings_updated', __( '用户已是此系统成员。' ), 'warning' );
			break;
		case 'err_add_fail':
			add_settings_error( 'general', 'settings_updated', __( '未能添加用户到此系统。' ), 'danger' );
			break;
		case 'err_add_notfound':
			add_settings_error( 'general', 'settings_updated', __( '输入现有用户的用户名。' ), 'danger' );
			break;
		case 'promote':
			add_settings_error( 'general', 'settings_updated', __( '角色已改变。' ), 'success' );
			break;
		case 'err_promote':
			add_settings_error( 'general', 'settings_updated', __( '选择要更改哪位用户的权限？' ), 'warning' );
			break;
		case 'remove':
			add_settings_error( 'general', 'settings_updated', __( '用户已从本系统移除。' ), 'success' );
			break;
		case 'err_remove':
			add_settings_error( 'general', 'settings_updated', __( '选择要移除的用户。' ), 'danger' );
			break;
		case 'newuser':
			add_settings_error( 'general', 'settings_updated', __( '用户已创建。' ), 'success' );
			break;
		case 'err_new':
			add_settings_error( 'general', 'settings_updated', __( '输入用户名和邮箱地址。' ), 'danger' );
			break;
		case 'err_new_dup':
			add_settings_error( 'general', 'settings_updated', __( '用户名或邮箱地址重复。' ), 'danger' );
			break;
	}
}

require_once ABSPATH . 'gc-admin/admin-header.php'; ?>

<script type="text/javascript">
var current_site_id = <?php echo absint( $id ); ?>;
</script>


<div class="wrap">
<div class="page-header"><h2 id="edit-site" class="header-title"><?php echo esc_html( $title ); ?></h2></div>
<p class="edit-site-actions"><a href="<?php echo esc_url( get_home_url( $id, '/' ) ); ?>"><?php _e( '访问' ); ?></a> | <a href="<?php echo esc_url( get_admin_url( $id ) ); ?>"><?php _e( '仪表盘' ); ?></a></p>

<form class="search-form" method="get">
<?php $gc_list_table->search_box( __( '搜索用户' ), 'user' ); ?>
<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
</form>

<?php $gc_list_table->views(); ?>

<form method="post" action="site-users.php?action=update-site">
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />

<?php $gc_list_table->display(); ?>

</form>

<?php
/**
 * Fires after the list table on the Users screen in the Multisite Network Admin.
 *
 */
do_action( 'network_site_users_after_list_table' );

/** This filter is documented in gc-admin/network/site-users.php */
if ( current_user_can( 'promote_users' ) && apply_filters( 'show_network_site_users_add_existing_form', true ) ) :
	?>
<h2 id="add-existing-user"><?php _e( '添加现有用户' ); ?></h2>
<form action="site-users.php?action=adduser" id="adduser" method="post">
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><label for="newuser"><?php _e( '用户名' ); ?></label></th>
			<td><input type="text" class="regular-text gc-suggest-user" name="newuser" id="newuser" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="new_role_adduser"><?php _e( '角色' ); ?></label></th>
			<td><select name="new_role" id="new_role_adduser">
			<?php
			switch_to_blog( $id );
			gc_dropdown_roles( get_option( 'default_role' ) );
			restore_current_blog();
			?>
			</select></td>
		</tr>
	</table>
	<?php gc_nonce_field( 'add-user', '_gcnonce_add-user' ); ?>
	<?php submit_button( __( '添加用户' ), 'primary', 'add-user', true, array( 'id' => 'submit-add-existing-user' ) ); ?>
</form>
<?php endif; ?>

<?php
/**
 * Filters whether to show the Add New User form on the Multisite Users screen.
 *
 *
 * @param bool $bool Whether to show the Add New User form. Default true.
 */
if ( current_user_can( 'create_users' ) && apply_filters( 'show_network_site_users_add_new_form', true ) ) :
	?>
<h2 id="add-new-user"><?php _e( '添加用户' ); ?></h2>
<form action="<?php echo esc_url( network_admin_url( 'site-users.php?action=newuser' ) ); ?>" id="newuser" method="post">
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><label for="user_username"><?php _e( '用户名' ); ?></label></th>
			<td><input type="text" class="regular-text" name="user[username]" id="user_username" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="user_email"><?php _e( '电子邮箱' ); ?></label></th>
			<td><input type="text" class="regular-text" name="user[email]" id="user_email" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="new_role_newuser"><?php _e( '角色' ); ?></label></th>
			<td><select name="new_role" id="new_role_newuser">
			<?php
			switch_to_blog( $id );
			gc_dropdown_roles( get_option( 'default_role' ) );
			restore_current_blog();
			?>
			</select></td>
		</tr>
		<tr class="form-field">
			<td colspan="2" class="td-full"><?php _e( '密码重设链接将通过邮件发给用户。' ); ?></td>
		</tr>
	</table>
	<?php gc_nonce_field( 'add-user', '_gcnonce_add-new-user' ); ?>
	<?php submit_button( __( '添加用户' ), 'primary', 'add-user', true, array( 'id' => 'submit-add-user' ) ); ?>
</form>
<?php endif; ?>
</div>
<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';
