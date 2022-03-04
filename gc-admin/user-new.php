<?php
/**
 * New User Administration Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( is_multisite() ) {
	if ( ! current_user_can( 'create_users' ) && ! current_user_can( 'promote_users' ) ) {
		gc_die(
			'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
			'<p>' . __( '抱歉，您不能将用户添加到此站点网络。' ) . '</p>',
			403
		);
	}
} elseif ( ! current_user_can( 'create_users' ) ) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能新建用户。' ) . '</p>',
		403
	);
}

if ( is_multisite() ) {
	add_filter( 'gcmu_signup_user_notification_email', 'admin_created_user_email' );
}

if ( isset( $_REQUEST['action'] ) && 'adduser' === $_REQUEST['action'] ) {
	check_admin_referer( 'add-user', '_gcnonce_add-user' );

	$user_details = null;
	$user_email   = gc_unslash( $_REQUEST['email'] );
	if ( false !== strpos( $user_email, '@' ) ) {
		$user_details = get_user_by( 'email', $user_email );
	} else {
		if ( current_user_can( 'manage_network_users' ) ) {
			$user_details = get_user_by( 'login', $user_email );
		} else {
			gc_redirect( add_query_arg( array( 'update' => 'enter_email' ), 'user-new.php' ) );
			die();
		}
	}

	if ( ! $user_details ) {
		gc_redirect( add_query_arg( array( 'update' => 'does_not_exist' ), 'user-new.php' ) );
		die();
	}

	if ( ! current_user_can( 'promote_user', $user_details->ID ) ) {
		gc_die(
			'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
			'<p>' . __( '抱歉，您不能将用户添加到此站点网络。' ) . '</p>',
			403
		);
	}

	// Adding an existing user to this blog.
	$new_user_email = array();
	$redirect       = 'user-new.php';
	$username       = $user_details->user_login;
	$user_id        = $user_details->ID;
	if ( null != $username && array_key_exists( $blog_id, get_blogs_of_user( $user_id ) ) ) {
		$redirect = add_query_arg( array( 'update' => 'addexisting' ), 'user-new.php' );
	} else {
		if ( isset( $_POST['noconfirmation'] ) && current_user_can( 'manage_network_users' ) ) {
			$result = add_existing_user_to_blog(
				array(
					'user_id' => $user_id,
					'role'    => $_REQUEST['role'],
				)
			);

			if ( ! is_gc_error( $result ) ) {
				$redirect = add_query_arg(
					array(
						'update'  => 'addnoconfirmation',
						'user_id' => $user_id,
					),
					'user-new.php'
				);
			} else {
				$redirect = add_query_arg( array( 'update' => 'could_not_add' ), 'user-new.php' );
			}
		} else {
			$newuser_key = gc_generate_password( 20, false );
			add_option(
				'new_user_' . $newuser_key,
				array(
					'user_id' => $user_id,
					'email'   => $user_details->user_email,
					'role'    => $_REQUEST['role'],
				)
			);

			$roles = get_editable_roles();
			$role  = $roles[ $_REQUEST['role'] ];

			/**
			 * Fires immediately after an existing user is invited to join the site, but before the notification is sent.
			 *
		
			 *
			 * @param int    $user_id     The invited user's ID.
			 * @param array  $role        Array containing role information for the invited user.
			 * @param string $newuser_key The key of the invitation.
			 */
			do_action( 'invite_user', $user_id, $role, $newuser_key );

			$switched_locale = switch_to_locale( get_user_locale( $user_details ) );

			/* translators: 1: Site title, 2: Site URL, 3: User role, 4: Activation URL. */
			$message = __(
				'您好，

我们邀您加入“%1$s”并成为%3$s。站点地址为：%2$s
请点击以下链接确认加入：%4$s'
			);

			$new_user_email['to']      = $user_details->user_email;
			$new_user_email['subject'] = sprintf(
				/* translators: Joining confirmation notification email subject. %s: Site title. */
				__( '[%s] 加入确认' ),
				gc_specialchars_decode( get_option( 'blogname' ) )
			);
			$new_user_email['message'] = sprintf(
				$message,
				get_option( 'blogname' ),
				home_url(),
				gc_specialchars_decode( translate_user_role( $role['name'] ) ),
				home_url( "/newbloguser/$newuser_key/" )
			);
			$new_user_email['headers'] = '';

			/**
			 * Filters the contents of the email sent when an existing user is invited to join the site.
			 *
		
			 *
			 * @param array $new_user_email {
			 *     Used to build gc_mail().
			 *
			 *     @type string $to      The email address of the invited user.
			 *     @type string $subject The subject of the email.
			 *     @type string $message The content of the email.
			 *     @type string $headers Headers.
			 * }
			 * @param int    $user_id     The invited user's ID.
			 * @param array  $role        Array containing role information for the invited user.
			 * @param string $newuser_key The key of the invitation.
			 *
			 */
			$new_user_email = apply_filters( 'invited_user_email', $new_user_email, $user_id, $role, $newuser_key );

			gc_mail(
				$new_user_email['to'],
				$new_user_email['subject'],
				$new_user_email['message'],
				$new_user_email['headers']
			);

			if ( $switched_locale ) {
				restore_previous_locale();
			}

			$redirect = add_query_arg( array( 'update' => 'add' ), 'user-new.php' );
		}
	}
	gc_redirect( $redirect );
	die();
} elseif ( isset( $_REQUEST['action'] ) && 'createuser' === $_REQUEST['action'] ) {
	check_admin_referer( 'create-user', '_gcnonce_create-user' );

	if ( ! current_user_can( 'create_users' ) ) {
		gc_die(
			'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
			'<p>' . __( '抱歉，您不能新建用户。' ) . '</p>',
			403
		);
	}

	if ( ! is_multisite() ) {
		$user_id = edit_user();

		if ( is_gc_error( $user_id ) ) {
			$add_user_errors = $user_id;
		} else {
			if ( current_user_can( 'list_users' ) ) {
				$redirect = 'users.php?update=add&id=' . $user_id;
			} else {
				$redirect = add_query_arg( 'update', 'add', 'user-new.php' );
			}
			gc_redirect( $redirect );
			die();
		}
	} else {
		// Adding a new user to this site.
		$new_user_email = gc_unslash( $_REQUEST['email'] );
		$user_details   = gcmu_validate_user_signup( $_REQUEST['user_login'], $new_user_email );
		if ( is_gc_error( $user_details['errors'] ) && $user_details['errors']->has_errors() ) {
			$add_user_errors = $user_details['errors'];
		} else {
			/** This filter is documented in gc-includes/user.php */
			$new_user_login = apply_filters( 'pre_user_login', sanitize_user( gc_unslash( $_REQUEST['user_login'] ), true ) );
			if ( isset( $_POST['noconfirmation'] ) && current_user_can( 'manage_network_users' ) ) {
				add_filter( 'gcmu_signup_user_notification', '__return_false' );  // Disable confirmation email.
				add_filter( 'gcmu_welcome_user_notification', '__return_false' ); // Disable welcome email.
			}
			gcmu_signup_user(
				$new_user_login,
				$new_user_email,
				array(
					'add_to_blog' => get_current_blog_id(),
					'new_role'    => $_REQUEST['role'],
				)
			);
			if ( isset( $_POST['noconfirmation'] ) && current_user_can( 'manage_network_users' ) ) {
				$key      = $gcdb->get_var( $gcdb->prepare( "SELECT activation_key FROM {$gcdb->signups} WHERE user_login = %s AND user_email = %s", $new_user_login, $new_user_email ) );
				$new_user = gcmu_activate_signup( $key );
				if ( is_gc_error( $new_user ) ) {
					$redirect = add_query_arg( array( 'update' => 'addnoconfirmation' ), 'user-new.php' );
				} elseif ( ! is_user_member_of_blog( $new_user['user_id'] ) ) {
					$redirect = add_query_arg( array( 'update' => 'created_could_not_add' ), 'user-new.php' );
				} else {
					$redirect = add_query_arg(
						array(
							'update'  => 'addnoconfirmation',
							'user_id' => $new_user['user_id'],
						),
						'user-new.php'
					);
				}
			} else {
				$redirect = add_query_arg( array( 'update' => 'newuserconfirmation' ), 'user-new.php' );
			}
			gc_redirect( $redirect );
			die();
		}
	}
}

// Used in the HTML title tag.
$title       = __( '添加用户' );
$parent_file = 'users.php';

$do_both = false;
if ( is_multisite() && current_user_can( 'promote_users' ) && current_user_can( 'create_users' ) ) {
	$do_both = true;
}

$help = '<p>' . __( '要向您的站点添加新用户，填写本页上的表单，并点击下方的“添加新用户”按钮。' ) . '</p>';

if ( is_multisite() ) {
	$help .= '<p>' . __( '由于当前多站点功能已经开启，您可以直接通过输入用户名或电子邮箱的方式将站点网络中的现有用户添加到站点中，然后为其指定用户角色。如需修改密码等其他信息，您需要拥有网络管理员的权限，访问“管理网络”>“所有用户”，然后将鼠标放置在用户名上方，点击出现的“编辑”链接。' ) . '</p>' .
	'<p>' . __( '新用户将收到一封邮件，告知其已被添加至您的站点。这封邮件包含他们的密码。若您不希望发送“欢迎邮件”，请选择下面的复选框。' ) . '</p>';
} else {
	$help .= '<p>' . __( '新用户将被自动指派一个密码，他们可以在登录后修改。您可以通过点击“显示密码”按钮来查看或编辑这个密码。用户一旦被添加，用户名就不可再被修改。' ) . '</p>' .

	'<p>' . __( '新用户默认会收到一封邮件，以便用户了解他们已被添加至您的站点。这封邮件也包含了他们的密码重置链接，若您不希望欢迎邮件中包含密码，请不要选择下面的复选框。' ) . '</p>';
}

$help .= '<p>' . __( '请不要忘记在完成表单后点击页面下方的“添加新用户”按钮。' ) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => $help,
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => 'user-roles',
		'title'   => __( '用户角色' ),
		'content' => '<p>' . __( '如下是各种用户角色和它们所拥有的权限：' ) . '</p>' .
							'<ul>' .
							'<li>' . __( '订阅者可以阅读评论、发表评论、接收电子报等，但不能发布诸如文章、页面等常规站点内容。' ) . '</li>' .
							'<li>' . __( '贡献者可以编写、管理他们的文章，但是无法发布文章、无法上传多媒体文件。' ) . '</li>' .
							'<li>' . __( '作者可以发布和管理自己的文章，可以上传文件。' ) . '</li>' .
							'<li>' . __( '编辑可以发布文章、管理文章，亦可编辑他人发布的文章等。' ) . '</li>' .
							'<li>' . __( '管理员可以访问所有管理功能。' ) . '</li>' .
							'</ul>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/users-add-new-screen/">添加新用户文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

gc_enqueue_script( 'gc-ajax-response' );
gc_enqueue_script( 'user-profile' );

/**
 * Filters whether to enable user auto-complete for non-super admins in Multisite.
 *
 *
 *
 * @param bool $enable Whether to enable auto-complete for non-super admins. Default false.
 */
if ( is_multisite() && current_user_can( 'promote_users' ) && ! gc_is_large_network( 'users' )
	&& ( current_user_can( 'manage_network_users' ) || apply_filters( 'autocomplete_users_for_site_admins', false ) )
) {
	gc_enqueue_script( 'user-suggest' );
}

require_once ABSPATH . 'gc-admin/admin-header.php';

if ( isset( $_GET['update'] ) ) {
	$messages = array();
	if ( is_multisite() ) {
		$edit_link = '';
		if ( ( isset( $_GET['user_id'] ) ) ) {
			$user_id_new = absint( $_GET['user_id'] );
			if ( $user_id_new ) {
				$edit_link = esc_url( add_query_arg( 'gc_http_referer', urlencode( gc_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $user_id_new ) ) );
			}
		}

		switch ( $_GET['update'] ) {
			case 'newuserconfirmation':
				$messages[] = __( '已向新用户发送邀请函。受邀者须先点击邮件中的确认链接，才能完成新账户的创建。' );
				break;
			case 'add':
				$messages[] = __( '已向用户发送邀请邮件。他们必须点击其中的确认链接才可加入到您的站点。' );
				break;
			case 'addnoconfirmation':
				$message = __( '用户已被添加到您的站点。' );

				if ( $edit_link ) {
					$message .= sprintf( ' <a href="%s">%s</a>', $edit_link, __( '编辑用户' ) );
				}

				$messages[] = $message;
				break;
			case 'addexisting':
				$messages[] = __( '那位用户已经是本站点的成员了。' );
				break;
			case 'could_not_add':
				$add_user_errors = new GC_Error( 'could_not_add', __( '此用户不能被添加到此站点。' ) );
				break;
			case 'created_could_not_add':
				$add_user_errors = new GC_Error( 'created_could_not_add', __( '用户已被创建，但不能被添加到此站点。' ) );
				break;
			case 'does_not_exist':
				$add_user_errors = new GC_Error( 'does_not_exist', __( '该用户不存在。' ) );
				break;
			case 'enter_email':
				$add_user_errors = new GC_Error( 'enter_email', __( '请输入有效的电子邮箱。' ) );
				break;
		}
	} else {
		if ( 'add' === $_GET['update'] ) {
			$messages[] = __( '用户已添加。' );
		}
	}
}
?>
<div class="wrap">
<h1 id="add-new-user">
<?php
if ( current_user_can( 'create_users' ) ) {
	_e( '添加用户' );
} elseif ( current_user_can( 'promote_users' ) ) {
	_e( '添加现有用户' );
}
?>
</h1>

<?php if ( isset( $errors ) && is_gc_error( $errors ) ) : ?>
	<div class="error">
		<ul>
		<?php
		foreach ( $errors->get_error_messages() as $err ) {
			echo "<li>$err</li>\n";
		}
		?>
		</ul>
	</div>
	<?php
endif;

if ( ! empty( $messages ) ) {
	foreach ( $messages as $msg ) {
		echo '<div id="message" class="updated notice is-dismissible"><p>' . $msg . '</p></div>';
	}
}
?>

<?php if ( isset( $add_user_errors ) && is_gc_error( $add_user_errors ) ) : ?>
	<div class="error">
		<?php
		foreach ( $add_user_errors->get_error_messages() as $message ) {
			echo "<p>$message</p>";
		}
		?>
	</div>
<?php endif; ?>
<div id="ajax-response"></div>

<?php
if ( is_multisite() && current_user_can( 'promote_users' ) ) {
	if ( $do_both ) {
		echo '<h2 id="add-existing-user">' . __( '添加现有用户' ) . '</h2>';
	}
	if ( ! current_user_can( 'manage_network_users' ) ) {
		echo '<p>' . __( '输入此站点网络中已存在用户的电子邮箱以邀请他们加入此站点。该用户将收到一封电子邮件，并需要他们确认该邀请。' ) . '</p>';
		$label = __( '电子邮箱' );
		$type  = 'email';
	} else {
		echo '<p>' . __( '输入此站点网络中已存在用户的电子邮箱或用户名以邀请他们加入此站点。该用户将收到一封电子邮件，并需要他们确认该邀请。' ) . '</p>';
		$label = __( '电子邮箱或用户名' );
		$type  = 'text';
	}
	?>
<form method="post" name="adduser" id="adduser" class="validate" novalidate="novalidate"
	<?php
	/**
	 * Fires inside the adduser form tag.
	 *
	 */
	do_action( 'user_new_form_tag' );
	?>
>
<input name="action" type="hidden" value="adduser" />
	<?php gc_nonce_field( 'add-user', '_gcnonce_add-user' ); ?>

<table class="form-table" role="presentation">
	<tr class="form-field form-required">
		<th scope="row"><label for="adduser-email"><?php echo $label; ?></label></th>
		<td><input name="email" type="<?php echo $type; ?>" id="adduser-email" class="gc-suggest-user" value="" /></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="adduser-role"><?php _e( '角色' ); ?></label></th>
		<td><select name="role" id="adduser-role">
			<?php gc_dropdown_roles( get_option( 'default_role' ) ); ?>
			</select>
		</td>
	</tr>
	<?php if ( current_user_can( 'manage_network_users' ) ) { ?>
	<tr>
		<th scope="row"><?php _e( '跳过邮件确认' ); ?></th>
		<td>
			<input type="checkbox" name="noconfirmation" id="adduser-noconfirmation" value="1" />
			<label for="adduser-noconfirmation"><?php _e( '不发送确认邮件，直接添加用户。' ); ?></label>
		</td>
	</tr>
	<?php } ?>
</table>
	<?php
	/**
	 * Fires at the end of the new user form.
	 *
	 * Passes a contextual string to make both types of new user forms
	 * uniquely targetable. Contexts are 'add-existing-user' (Multisite),
	 * and 'add-new-user' (single site and network admin).
	 *
	 *
	 * @param string $type A contextual string specifying which type of new user form the hook follows.
	 */
	do_action( 'user_new_form', 'add-existing-user' );
	?>
	<?php submit_button( __( '添加现有用户' ), 'primary', 'adduser', true, array( 'id' => 'addusersub' ) ); ?>
</form>
	<?php
} // End if is_multisite().

if ( current_user_can( 'create_users' ) ) {
	if ( $do_both ) {
		echo '<h2 id="create-new-user">' . __( '添加用户' ) . '</h2>';
	}
	?>
<p><?php _e( '新建用户，并将用户加入此站点。' ); ?></p>
<form method="post" name="createuser" id="createuser" class="validate" novalidate="novalidate"
	<?php
	/** This action is documented in gc-admin/user-new.php */
	do_action( 'user_new_form_tag' );
	?>
>
<input name="action" type="hidden" value="createuser" />
	<?php gc_nonce_field( 'create-user', '_gcnonce_create-user' ); ?>
	<?php
	// Load up the passed data, else set to a default.
	$creating = isset( $_POST['createuser'] );

	$new_user_login             = $creating && isset( $_POST['user_login'] ) ? gc_unslash( $_POST['user_login'] ) : '';
	$new_user_firstname         = $creating && isset( $_POST['first_name'] ) ? gc_unslash( $_POST['first_name'] ) : '';
	$new_user_lastname          = $creating && isset( $_POST['last_name'] ) ? gc_unslash( $_POST['last_name'] ) : '';
	$new_user_email             = $creating && isset( $_POST['email'] ) ? gc_unslash( $_POST['email'] ) : '';
	$new_user_uri               = $creating && isset( $_POST['url'] ) ? gc_unslash( $_POST['url'] ) : '';
	$new_user_role              = $creating && isset( $_POST['role'] ) ? gc_unslash( $_POST['role'] ) : '';
	$new_user_send_notification = $creating && ! isset( $_POST['send_user_notification'] ) ? false : true;
	$new_user_ignore_pass       = $creating && isset( $_POST['noconfirmation'] ) ? gc_unslash( $_POST['noconfirmation'] ) : '';

	?>
<table class="form-table" role="presentation">
	<tr class="form-field form-required">
		<th scope="row"><label for="user_login"><?php _e( '用户名' ); ?> <span class="描述"><?php _e( '（必填）' ); ?></span></label></th>
		<td><input name="user_login" type="text" id="user_login" value="<?php echo esc_attr( $new_user_login ); ?>" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="60" /></td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="email"><?php _e( '电子邮箱' ); ?> <span class="描述"><?php _e( '（必填）' ); ?></span></label></th>
		<td><input name="email" type="email" id="email" value="<?php echo esc_attr( $new_user_email ); ?>" /></td>
	</tr>
	<?php if ( ! is_multisite() ) { ?>
	<tr class="form-field">
		<th scope="row"><label for="first_name"><?php _e( '名字' ); ?> </label></th>
		<td><input name="first_name" type="text" id="first_name" value="<?php echo esc_attr( $new_user_firstname ); ?>" /></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="last_name"><?php _e( '姓氏' ); ?> </label></th>
		<td><input name="last_name" type="text" id="last_name" value="<?php echo esc_attr( $new_user_lastname ); ?>" /></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="url"><?php _e( '网站地址' ); ?></label></th>
		<td><input name="url" type="url" id="url" class="code" value="<?php echo esc_attr( $new_user_uri ); ?>" /></td>
	</tr>
		<?php
		$languages = get_available_languages();
		if ( $languages ) :
			?>
		<tr class="form-field user-language-wrap">
			<th scope="row">
				<label for="locale">
					<?php /* translators: The user language selection field label. */ ?>
					<?php _e( '语言' ); ?><span class="dashicons dashicons-translation" aria-hidden="true"></span>
				</label>
			</th>
			<td>
				<?php
				gc_dropdown_languages(
					array(
						'name'                        => 'locale',
						'id'                          => 'locale',
						'selected'                    => 'site-default',
						'languages'                   => $languages,
						'show_available_translations' => false,
						'show_option_site_default'    => true,
					)
				);
				?>
			</td>
		</tr>
		<?php endif; ?>
	<tr class="form-field form-required user-pass1-wrap">
		<th scope="row">
			<label for="pass1">
				<?php _e( '密码' ); ?>
				<span class="description hide-if-js"><?php _e( '（必填）' ); ?></span>
			</label>
		</th>
		<td>
			<input class="hidden" value=" " /><!-- #24364 workaround -->
			<button type="button" class="button gc-generate-pw hide-if-no-js"><?php _e( '生成密码' ); ?></button>
			<div class="gc-pwd">
				<?php $initial_password = gc_generate_password( 24 ); ?>
				<span class="password-input-wrapper">
					<input type="password" name="pass1" id="pass1" class="regular-text" autocomplete="off" data-reveal="1" data-pw="<?php echo esc_attr( $initial_password ); ?>" aria-describedby="pass-strength-result" />
				</span>
				<button type="button" class="button gc-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( '隐藏密码' ); ?>">
					<span class="dashicons dashicons-hidden" aria-hidden="true"></span>
					<span class="text"><?php _e( '隐藏' ); ?></span>
				</button>
				<div style="display:none" id="pass-strength-result" aria-live="polite"></div>
			</div>
		</td>
	</tr>
	<tr class="form-field form-required user-pass2-wrap hide-if-js">
		<th scope="row"><label for="pass2"><?php _e( '重复密码' ); ?> <span class="描述"><?php _e( '（必填）' ); ?></span></label></th>
		<td>
		<input name="pass2" type="password" id="pass2" autocomplete="off" aria-describedby="pass2-desc" />
		<p class="描述" id="pass2-desc"><?php _e( '再次输入密码。' ); ?></p>
		</td>
	</tr>
	<tr class="pw-weak">
		<th><?php _e( '确认密码' ); ?></th>
		<td>
			<label>
				<input type="checkbox" name="pw_weak" class="pw-checkbox" />
				<?php _e( '确认使用弱密码' ); ?>
			</label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php _e( '发送用户通知' ); ?></th>
		<td>
			<input type="checkbox" name="send_user_notification" id="send_user_notification" value="1" <?php checked( $new_user_send_notification ); ?> />
			<label for="send_user_notification"><?php _e( '向新用户发送有关其账户详细信息的邮件。' ); ?></label>
		</td>
	</tr>
	<?php } // End if ! is_multisite(). ?>
	<?php if ( current_user_can( 'promote_users' ) ) { ?>
	<tr class="form-field">
		<th scope="row"><label for="role"><?php _e( '角色' ); ?></label></th>
		<td><select name="role" id="role">
			<?php
			if ( ! $new_user_role ) {
				$new_user_role = get_option( 'default_role' );
			}
			gc_dropdown_roles( $new_user_role );
			?>
			</select>
		</td>
	</tr>
	<?php } ?>
	<?php if ( is_multisite() && current_user_can( 'manage_network_users' ) ) { ?>
	<tr>
		<th scope="row"><?php _e( '跳过邮件确认' ); ?></th>
		<td>
			<input type="checkbox" name="noconfirmation" id="noconfirmation" value="1" <?php checked( $new_user_ignore_pass ); ?> />
			<label for="noconfirmation"><?php _e( '不发送确认邮件，直接添加用户。' ); ?></label>
		</td>
	</tr>
	<?php } ?>
</table>

	<?php
	/** This action is documented in gc-admin/user-new.php */
	do_action( 'user_new_form', 'add-new-user' );
	?>

	<?php submit_button( __( '添加用户' ), 'primary', 'createuser', true, array( 'id' => 'createusersub' ) ); ?>

</form>
<?php } // End if current_user_can( 'create_users' ). ?>
</div>
<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';
