<?php
/**
 * GeChiUI user administration API.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Creates a new user from the "Users" form using $_POST information.
 *
 *
 *
 * @return int|GC_Error GC_Error or User ID.
 */
function add_user() {
	return edit_user();
}

/**
 * Edit user settings based on contents of $_POST
 *
 * Used on user-edit.php and profile.php to manage and process user options, passwords etc.
 *
 *
 *
 * @param int $user_id Optional. User ID.
 * @return int|GC_Error User ID of the updated user or GC_Error on failure.
 */
function edit_user( $user_id = 0 ) {
	$gc_roles = gc_roles();
	$user     = new stdClass;
	$user_id  = (int) $user_id;
	if ( $user_id ) {
		$update           = true;
		$user->ID         = $user_id;
		$userdata         = get_userdata( $user_id );
		$user->user_login = gc_slash( $userdata->user_login );
	} else {
		$update = false;
	}

	if ( ! $update && isset( $_POST['user_login'] ) ) {
		$user->user_login = sanitize_user( gc_unslash( $_POST['user_login'] ), true );
	}

	$pass1 = '';
	$pass2 = '';
	if ( isset( $_POST['pass1'] ) ) {
		$pass1 = trim( $_POST['pass1'] );
	}
	if ( isset( $_POST['pass2'] ) ) {
		$pass2 = trim( $_POST['pass2'] );
	}

	if ( isset( $_POST['role'] ) && current_user_can( 'promote_users' ) && ( ! $user_id || current_user_can( 'promote_user', $user_id ) ) ) {
		$new_role = sanitize_text_field( $_POST['role'] );

		// If the new role isn't editable by the logged-in user die with error.
		$editable_roles = get_editable_roles();
		if ( ! empty( $new_role ) && empty( $editable_roles[ $new_role ] ) ) {
			gc_die( __( '抱歉，您不能将此角色给予用户。' ), 403 );
		}

		$potential_role = isset( $gc_roles->role_objects[ $new_role ] ) ? $gc_roles->role_objects[ $new_role ] : false;

		/*
		 * Don't let anyone with 'promote_users' edit their own role to something without it.
		 * Multisite super admins can freely edit their roles, they possess all caps.
		 */
		if (
			( is_multisite() && current_user_can( 'manage_network_users' ) ) ||
			get_current_user_id() !== $user_id ||
			( $potential_role && $potential_role->has_cap( 'promote_users' ) )
		) {
			$user->role = $new_role;
		}
	}

	if ( isset( $_POST['email'] ) ) {
		$user->user_email = sanitize_text_field( gc_unslash( $_POST['email'] ) );
	}
	if ( isset( $_POST['url'] ) ) {
		if ( empty( $_POST['url'] ) || 'http://' === $_POST['url'] ) {
			$user->user_url = '';
		} else {
			$user->user_url = esc_url_raw( $_POST['url'] );
			$protocols      = implode( '|', array_map( 'preg_quote', gc_allowed_protocols() ) );
			$user->user_url = preg_match( '/^(' . $protocols . '):/is', $user->user_url ) ? $user->user_url : 'http://' . $user->user_url;
		}
	}
	if ( isset( $_POST['first_name'] ) ) {
		$user->first_name = sanitize_text_field( $_POST['first_name'] );
	}
	if ( isset( $_POST['last_name'] ) ) {
		$user->last_name = sanitize_text_field( $_POST['last_name'] );
	}
	if ( isset( $_POST['nickname'] ) ) {
		$user->nickname = sanitize_text_field( $_POST['nickname'] );
	}
	if ( isset( $_POST['display_name'] ) ) {
		$user->display_name = sanitize_text_field( $_POST['display_name'] );
	}

	if ( isset( $_POST['description'] ) ) {
		$user->description = trim( $_POST['description'] );
	}

	foreach ( gc_get_user_contact_methods( $user ) as $method => $name ) {
		if ( isset( $_POST[ $method ] ) ) {
			$user->$method = sanitize_text_field( $_POST[ $method ] );
		}
	}

	if ( isset( $_POST['locale'] ) ) {
		$locale = sanitize_text_field( $_POST['locale'] );
		if ( 'site-default' === $locale ) {
			$locale = '';
		} elseif ( '' === $locale ) {
			$locale = 'zh_CN';
		} elseif ( ! in_array( $locale, get_available_languages(), true ) ) {
			$locale = '';
		}

		$user->locale = $locale;
	}

	if ( $update ) {
		$user->rich_editing         = isset( $_POST['rich_editing'] ) && 'false' === $_POST['rich_editing'] ? 'false' : 'true';
		$user->syntax_highlighting  = isset( $_POST['syntax_highlighting'] ) && 'false' === $_POST['syntax_highlighting'] ? 'false' : 'true';
		$user->admin_color          = isset( $_POST['admin_color'] ) ? sanitize_text_field( $_POST['admin_color'] ) : 'fresh';
		$user->show_admin_bar_front = isset( $_POST['admin_bar_front'] ) ? 'true' : 'false';
	}

	$user->comment_shortcuts = isset( $_POST['comment_shortcuts'] ) && 'true' === $_POST['comment_shortcuts'] ? 'true' : '';

	$user->use_ssl = 0;
	if ( ! empty( $_POST['use_ssl'] ) ) {
		$user->use_ssl = 1;
	}

	$errors = new GC_Error();

	/* checking that username has been typed */
	if ( '' === $user->user_login ) {
		$errors->add( 'user_login', __( '<strong>错误</strong>：请填写用户名。' ) );
	}

	/* checking that nickname has been typed */
	if ( $update && empty( $user->nickname ) ) {
		$errors->add( 'nickname', __( '<strong>错误</strong>：请输入昵称。' ) );
	}

	/**
	 * Fires before the password and confirm password fields are checked for congruity.
	 *
	 *
	 * @param string $user_login The username.
	 * @param string $pass1     The password (passed by reference).
	 * @param string $pass2     The confirmed password (passed by reference).
	 */
	do_action_ref_array( 'check_passwords', array( $user->user_login, &$pass1, &$pass2 ) );

	// Check for blank password when adding a user.
	if ( ! $update && empty( $pass1 ) ) {
		$errors->add( 'pass', __( '<strong>错误</strong>：请输入密码。' ), array( 'form-field' => 'pass1' ) );
	}

	// Check for "\" in password.
	if ( false !== strpos( gc_unslash( $pass1 ), '\\' ) ) {
		$errors->add( 'pass', __( '<strong>错误</strong>：密码中不能有“\\”字符。' ), array( 'form-field' => 'pass1' ) );
	}

	// Checking the password has been typed twice the same.
	if ( ( $update || ! empty( $pass1 ) ) && $pass1 != $pass2 ) {
		$errors->add( 'pass', __( '<strong>错误</strong>：两次输入的密码不相符，请在两个密码栏中输入相同密码。' ), array( 'form-field' => 'pass1' ) );
	}

	if ( ! empty( $pass1 ) ) {
		$user->user_pass = $pass1;
	}

	if ( ! $update && isset( $_POST['user_login'] ) && ! validate_username( $_POST['user_login'] ) ) {
		$errors->add( 'user_login', __( '<strong>错误</strong>：此用户名包含无效字符，请输入有效的用户名。' ) );
	}

	if ( ! $update && username_exists( $user->user_login ) ) {
		$errors->add( 'user_login', __( '<strong>错误</strong>：该用户名已被注册，请再选择一个。' ) );
	}

	/** This filter is documented in gc-includes/user.php */
	$illegal_logins = (array) apply_filters( 'illegal_user_logins', array() );

	if ( in_array( strtolower( $user->user_login ), array_map( 'strtolower', $illegal_logins ), true ) ) {
		$errors->add( 'invalid_username', __( '<strong>错误</strong>：此用户名不被允许。' ) );
	}

	/* checking email address */
	if ( empty( $user->user_email ) ) {
		$errors->add( 'empty_email', __( '<strong>错误</strong>：请输入电子邮箱。' ), array( 'form-field' => 'email' ) );
	} elseif ( ! is_email( $user->user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>错误</strong>：电子邮箱不正确。' ), array( 'form-field' => 'email' ) );
	} else {
		$owner_id = email_exists( $user->user_email );
		if ( $owner_id && ( ! $update || ( $owner_id != $user->ID ) ) ) {
			$errors->add( 'email_exists', __( '<strong>错误</strong>：此电子邮箱已经被注册，请换一个。' ), array( 'form-field' => 'email' ) );
		}
	}

	/**
	 * Fires before user profile update errors are returned.
	 *
	 *
	 * @param GC_Error $errors GC_Error object (passed by reference).
	 * @param bool     $update Whether this is a user update.
	 * @param stdClass $user   User object (passed by reference).
	 */
	do_action_ref_array( 'user_profile_update_errors', array( &$errors, $update, &$user ) );

	if ( $errors->has_errors() ) {
		return $errors;
	}

	if ( $update ) {
		$user_id = gc_update_user( $user );
	} else {
		$user_id = gc_insert_user( $user );
		$notify  = isset( $_POST['send_user_notification'] ) ? 'both' : 'admin';

		/**
		 * Fires after a new user has been created.
		 *
		 *
		 * @param int|GC_Error $user_id ID of the newly created user or GC_Error on failure.
		 * @param string       $notify  Type of notification that should happen. See
		 *                              gc_send_new_user_notifications() for more information.
		 */
		do_action( 'edit_user_created_user', $user_id, $notify );
	}
	return $user_id;
}

/**
 * Fetch a filtered list of user roles that the current user is
 * allowed to edit.
 *
 * Simple function whose main purpose is to allow filtering of the
 * list of roles in the $gc_roles object so that plugins can remove
 * inappropriate ones depending on the situation or user making edits.
 * Specifically because without filtering anyone with the edit_users
 * capability can edit others to be administrators, even if they are
 * only editors or authors. This filter allows admins to delegate
 * user management.
 *
 *
 *
 * @return array[] Array of arrays containing role information.
 */
function get_editable_roles() {
	$all_roles = gc_roles()->roles;

	/**
	 * Filters the list of editable roles.
	 *
	 *
	 * @param array[] $all_roles Array of arrays containing role information.
	 */
	$editable_roles = apply_filters( 'editable_roles', $all_roles );

	return $editable_roles;
}

/**
 * Retrieve user data and filter it.
 *
 *
 *
 * @param int $user_id User ID.
 * @return GC_User|false GC_User object on success, false on failure.
 */
function get_user_to_edit( $user_id ) {
	$user = get_userdata( $user_id );

	if ( $user ) {
		$user->filter = 'edit';
	}

	return $user;
}

/**
 * Retrieve the user's drafts.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int $user_id User ID.
 * @return array
 */
function get_users_drafts( $user_id ) {
	global $gcdb;
	$query = $gcdb->prepare( "SELECT ID, post_title FROM $gcdb->posts WHERE post_type = 'post' AND post_status = 'draft' AND post_author = %d ORDER BY post_modified DESC", $user_id );

	/**
	 * Filters the user's drafts query string.
	 *
	 *
	 * @param string $query The user's drafts query string.
	 */
	$query = apply_filters( 'get_users_drafts', $query );
	return $gcdb->get_results( $query );
}

/**
 * Remove user and optionally reassign posts and links to another user.
 *
 * If the $reassign parameter is not assigned to a User ID, then all posts will
 * be deleted of that user. The action {@see 'delete_user'} that is passed the User ID
 * being deleted will be run after the posts are either reassigned or deleted.
 * The user meta will also be deleted that are for that User ID.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param int $id User ID.
 * @param int $reassign Optional. Reassign posts and links to new User ID.
 * @return bool True when finished.
 */
function gc_delete_user( $id, $reassign = null ) {
	global $gcdb;

	if ( ! is_numeric( $id ) ) {
		return false;
	}

	$id   = (int) $id;
	$user = new GC_User( $id );

	if ( ! $user->exists() ) {
		return false;
	}

	// Normalize $reassign to null or a user ID. 'novalue' was an older default.
	if ( 'novalue' === $reassign ) {
		$reassign = null;
	} elseif ( null !== $reassign ) {
		$reassign = (int) $reassign;
	}

	/**
	 * Fires immediately before a user is deleted from the database.
	 *
	 *
	 * @param int      $id       ID of the user to delete.
	 * @param int|null $reassign ID of the user to reassign posts and links to.
	 *                           Default null, for no reassignment.
	 * @param GC_User  $user     GC_User object of the user to delete.
	 */
	do_action( 'delete_user', $id, $reassign, $user );

	if ( null === $reassign ) {
		$post_types_to_delete = array();
		foreach ( get_post_types( array(), 'objects' ) as $post_type ) {
			if ( $post_type->delete_with_user ) {
				$post_types_to_delete[] = $post_type->name;
			} elseif ( null === $post_type->delete_with_user && post_type_supports( $post_type->name, 'author' ) ) {
				$post_types_to_delete[] = $post_type->name;
			}
		}

		/**
		 * Filters the list of post types to delete with a user.
		 *
		 *
		 * @param string[] $post_types_to_delete Array of post types to delete.
		 * @param int      $id                   User ID.
		 */
		$post_types_to_delete = apply_filters( 'post_types_to_delete_with_user', $post_types_to_delete, $id );
		$post_types_to_delete = implode( "', '", $post_types_to_delete );
		$post_ids             = $gcdb->get_col( $gcdb->prepare( "SELECT ID FROM $gcdb->posts WHERE post_author = %d AND post_type IN ('$post_types_to_delete')", $id ) );
		if ( $post_ids ) {
			foreach ( $post_ids as $post_id ) {
				gc_delete_post( $post_id );
			}
		}

		// Clean links.
		$link_ids = $gcdb->get_col( $gcdb->prepare( "SELECT link_id FROM $gcdb->links WHERE link_owner = %d", $id ) );

		if ( $link_ids ) {
			foreach ( $link_ids as $link_id ) {
				gc_delete_link( $link_id );
			}
		}
	} else {
		$post_ids = $gcdb->get_col( $gcdb->prepare( "SELECT ID FROM $gcdb->posts WHERE post_author = %d", $id ) );
		$gcdb->update( $gcdb->posts, array( 'post_author' => $reassign ), array( 'post_author' => $id ) );
		if ( ! empty( $post_ids ) ) {
			foreach ( $post_ids as $post_id ) {
				clean_post_cache( $post_id );
			}
		}
		$link_ids = $gcdb->get_col( $gcdb->prepare( "SELECT link_id FROM $gcdb->links WHERE link_owner = %d", $id ) );
		$gcdb->update( $gcdb->links, array( 'link_owner' => $reassign ), array( 'link_owner' => $id ) );
		if ( ! empty( $link_ids ) ) {
			foreach ( $link_ids as $link_id ) {
				clean_bookmark_cache( $link_id );
			}
		}
	}

	// FINALLY, delete user.
	if ( is_multisite() ) {
		remove_user_from_blog( $id, get_current_blog_id() );
	} else {
		$meta = $gcdb->get_col( $gcdb->prepare( "SELECT umeta_id FROM $gcdb->usermeta WHERE user_id = %d", $id ) );
		foreach ( $meta as $mid ) {
			delete_metadata_by_mid( 'user', $mid );
		}

		$gcdb->delete( $gcdb->users, array( 'ID' => $id ) );
	}

	clean_user_cache( $user );

	/**
	 * Fires immediately after a user is deleted from the database.
	 *
	 *
	 * @param int      $id       ID of the deleted user.
	 * @param int|null $reassign ID of the user to reassign posts and links to.
	 *                           Default null, for no reassignment.
	 * @param GC_User  $user     GC_User object of the deleted user.
	 */
	do_action( 'deleted_user', $id, $reassign, $user );

	return true;
}

/**
 * Remove all capabilities from user.
 *
 *
 *
 * @param int $id User ID.
 */
function gc_revoke_user( $id ) {
	$id = (int) $id;

	$user = new GC_User( $id );
	$user->remove_all_caps();
}

/**
 *
 *
 * @global int $user_ID
 *
 * @param false $errors Deprecated.
 */
function default_password_nag_handler( $errors = false ) {
	global $user_ID;
	// Short-circuit it.
	if ( ! get_user_option( 'default_password_nag' ) ) {
		return;
	}

	// get_user_setting() = JS-saved UI setting. Else no-js-fallback code.
	if ( 'hide' === get_user_setting( 'default_password_nag' )
		|| isset( $_GET['default_password_nag'] ) && '0' == $_GET['default_password_nag']
	) {
		delete_user_setting( 'default_password_nag' );
		update_user_meta( $user_ID, 'default_password_nag', false );
	}
}

/**
 *
 *
 * @param int     $user_ID
 * @param GC_User $old_data
 */
function default_password_nag_edit_user( $user_ID, $old_data ) {
	// Short-circuit it.
	if ( ! get_user_option( 'default_password_nag', $user_ID ) ) {
		return;
	}

	$new_data = get_userdata( $user_ID );

	// Remove the nag if the password has been changed.
	if ( $new_data->user_pass != $old_data->user_pass ) {
		delete_user_setting( 'default_password_nag' );
		update_user_meta( $user_ID, 'default_password_nag', false );
	}
}

/**
 *
 *
 * @global string $pagenow
 */
function default_password_nag() {
	global $pagenow;
	// Short-circuit it.
	if ( 'profile.php' === $pagenow || ! get_user_option( 'default_password_nag' ) ) {
		return;
	}

	echo '<div class="error default-password-nag">';
	echo '<p>';
	echo '<strong>' . __( '注意：' ) . '</strong> ';
	_e( '您的账户正在使用自动生成的密码。您希望修改它吗？' );
	echo '</p><p>';
	printf( '<a href="%s">' . __( '希望，请带我到个人资料编辑页面' ) . '</a> | ', get_edit_profile_url() . '#password' );
	printf( '<a href="%s" id="default-password-nag-no">' . __( '不要，不用再提示我了' ) . '</a>', '?default_password_nag=0' );
	echo '</p></div>';
}

/**
 *
 * @access private
 */
function delete_users_add_js() {
	?>
<script>
jQuery( function($) {
	var submit = $('#submit').prop('disabled', true);
	$('input[name="delete_option"]').one('change', function() {
		submit.prop('disabled', false);
	});
	$('#reassign_user').focus( function() {
		$('#delete_option1').prop('checked', true).trigger('change');
	});
} );
</script>
	<?php
}

/**
 * Optional SSL preference that can be turned on by hooking to the 'personal_options' action.
 *
 * See the {@see 'personal_options'} action.
 *
 *
 *
 * @param GC_User $user User data object.
 */
function use_ssl_preference( $user ) {
	?>
	<tr class="user-use-ssl-wrap">
		<th scope="row"><?php _e( '使用https' ); ?></th>
		<td><label for="use_ssl"><input name="use_ssl" type="checkbox" id="use_ssl" value="1" <?php checked( '1', $user->use_ssl ); ?> /> <?php _e( '始终在访问管理后台时使用https' ); ?></label></td>
	</tr>
	<?php
}

/**
 * @since MU
 *
 * @param string $text
 * @return string
 */
function admin_created_user_email( $text ) {
	$roles = get_editable_roles();
	$role  = $roles[ $_REQUEST['role'] ];

	return sprintf(
		/* translators: 1: Site title, 2: Site URL, 3: User role. */
		__(
			'您好，

我们邀您加入“%1$s”并成为%3$s。站点地址为：
%2$s
如果您不想加入，您只需忽略本邮件。
本邀请函仅在几天内有效。

如果您愿意，请点击以下链接来激活您的用户账户：
%%s'
		),
		gc_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		home_url(),
		gc_specialchars_decode( translate_user_role( $role['name'] ) )
	);
}

/**
 * Checks if the Authorize AppKey request is valid.
 *
 *
 *
 * @param array   $request {
 *     The array of request data. All arguments are optional and may be empty.
 *
 *     @type string $app_name    The suggested name of the application.
 *     @type string $app_id      A UUID provided by the application to uniquely identify it.
 *     @type string $success_url The URL the user will be redirected to after approving the application.
 *     @type string $reject_url  The URL the user will be redirected to after rejecting the application.
 * }
 * @param GC_User $user The user authorizing the application.
 * @return true|GC_Error True if the request is valid, a GC_Error object contains errors if not.
 */
function gc_is_authorize_appkey_request_valid( $request, $user ) {
	$error = new GC_Error();

	if ( ! empty( $request['success_url'] ) ) {
		$scheme = gc_parse_url( $request['success_url'], PHP_URL_SCHEME );

		if ( 'http' === $scheme ) {
			$error->add(
				'invalid_redirect_scheme',
				__( '成功授权网址必须以安全连接提供。' )
			);
		}
	}

	if ( ! empty( $request['reject_url'] ) ) {
		$scheme = gc_parse_url( $request['reject_url'], PHP_URL_SCHEME );

		if ( 'http' === $scheme ) {
			$error->add(
				'invalid_redirect_scheme',
				__( '驳回授权网址必须以安全连接提供。' )
			);
		}
	}

	if ( ! empty( $request['app_id'] ) && ! gc_is_uuid( $request['app_id'] ) ) {
		$error->add(
			'invalid_app_id',
			__( '应用程序 ID 必须为 UUID。' )
		);
	}

	/**
	 * Fires before appkey errors are returned.
	 *
	 *
	 * @param GC_Error $error   The error object.
	 * @param array    $request The array of request data.
	 * @param GC_User  $user    The user authorizing the application.
	 */
	do_action( 'gc_authorize_appkey_request_errors', $error, $request, $user );

	if ( $error->has_errors() ) {
		return $error;
	}

	return true;
}
