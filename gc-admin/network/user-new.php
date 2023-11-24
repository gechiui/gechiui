<?php
/**
 * Add New User network administration panel.
 *
 * @package GeChiUI
 * @subpackage Multisite
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'create_users' ) ) {
	gc_die( __( '抱歉，您不能将用户添加到此SaaS平台。' ) );
}

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
			'<p>' . __( '点击“添加用户”链接，将会在SaaS平台中创建用户帐户，并自动向该用户发送包含用户名和密码的邮件。' ) . '</p>' .
			'<p>' . __( '已在SaaS平台中注册，且不拥有系统的用户将以订阅者的身份加入主仪表盘系统，允许他们在其中修改资料、管理自己的账户。在他们创建自己的系统之前，只能在导航栏中看到“仪表盘”和“我的系统”菜单。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.gechiui.com/Network_Admin_Users_Screen">SaaS平台成员文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/forum/issues/multisite/">支持论坛</a>' ) . '</p>'
);

if ( isset( $_REQUEST['action'] ) && 'add-user' === $_REQUEST['action'] ) {
	check_admin_referer( 'add-user', '_gcnonce_add-user' );

	if ( ! current_user_can( 'manage_network_users' ) ) {
		gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
	}

	if ( ! is_array( $_POST['user'] ) ) {
		gc_die( __( '不能创建空用户。' ) );
	}

	$user = gc_unslash( $_POST['user'] );

	$user_details = gcmu_validate_user_signup( $user['username'], $user['email'] );

	if ( is_gc_error( $user_details['errors'] ) && $user_details['errors']->has_errors() ) {
		$add_user_errors = $user_details['errors'];
	} else {
		$password = gc_generate_password( 12, false );
		$user_id  = gcmu_create_user( esc_html( strtolower( $user['username'] ) ), $password, sanitize_email( $user['email'] ) );

		if ( ! $user_id ) {
			$add_user_errors = new GC_Error( 'add_user_fail', __( '无法添加用户。' ) );
		} else {
			/**
			 * Fires after a new user has been created via the network user-new.php page.
			 *
			 * @since 4.4.0
			 *
			 * @param int $user_id ID of the newly created user.
			 */
			do_action( 'network_user_new_created_user', $user_id );

			gc_redirect(
				add_query_arg(
					array(
						'update'  => 'added',
						'user_id' => $user_id,
					),
					'user-new.php'
				)
			);
			exit;
		}
	}
}

$message = '';
if ( isset( $_GET['update'] ) ) {
	if ( 'added' === $_GET['update'] ) {
		$edit_link = '';
		if ( isset( $_GET['user_id'] ) ) {
			$user_id_new = absint( $_GET['user_id'] );
			if ( $user_id_new ) {
				$edit_link = esc_url( add_query_arg( 'gc_http_referer', urlencode( gc_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user_id_new ) ) );
			}
		}

		$message = __( '用户已添加。' );

		if ( $edit_link ) {
			$message .= sprintf( ' <a href="%s">%s</a>', $edit_link, __( '编辑用户' ) );
		}
	}
}

// Used in the HTML title tag.
$title       = __( '添加用户' );
$parent_file = 'users.php';

if ( ! empty( $message ) ) {
	add_settings_error( 'general', 'message', $message, 'success' );
}

if ( isset( $add_user_errors ) && is_gc_error( $add_user_errors ) ) {
	foreach ( $add_user_errors->get_error_messages() as $error ) {
		add_settings_error( 'general', 'message', $error, 'danger' );
	}
}

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
	<div class="page-header"><h2 id="add-new-user" class="header-title"><?php _e( '添加用户' ); ?></h2></div>
	<form action="<?php echo esc_url( network_admin_url( 'user-new.php?action=add-user' ) ); ?>" id="adduser" method="post" novalidate="novalidate">
		<div class="card">
			<div class="card-body">
			<p><?php echo gc_required_field_message(); ?></p>
			<table class="form-table" role="presentation">
				<tr class="form-field form-required">
					<th scope="row"><label for="username"><?php _e( '用户名' ); ?> <?php echo gc_required_field_indicator(); ?></label></th>
					<td><input type="text" class="regular-text" name="user[username]" id="username" autocapitalize="none" autocorrect="off" maxlength="60" required="required" /></td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="email"><?php _e( '电子邮箱' ); ?> <?php echo gc_required_field_indicator(); ?></label></th>
					<td><input type="email" class="regular-text" name="user[email]" id="email" required="required" /></td>
				</tr>
				<tr class="form-field">
					<td colspan="2" class="td-full"><?php _e( '密码重设链接将通过邮件发给用户。' ); ?></td>
				</tr>
			</table>
			<?php
			/**
			 * Fires at the end of the new user form in network admin.
			 *
			 * @since 4.5.0
			 */
			do_action( 'network_user_new_form' );

			gc_nonce_field( 'add-user', '_gcnonce_add-user' );
			submit_button( __( '添加用户' ), 'primary', 'add-user' );
			?>
			</div>
		</div>
	</form>
</div>
<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';
