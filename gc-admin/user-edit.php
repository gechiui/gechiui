<?php
/**
 * Edit user administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

/** GeChiUI Translation Installation API */
require_once ABSPATH . 'gc-admin/includes/translation-install.php';

gc_reset_vars( array( 'action', 'user_id', 'gc_http_referer' ) );

$user_id      = (int) $user_id;
$current_user = gc_get_current_user();

if ( ! defined( 'IS_PROFILE_PAGE' ) ) {
	define( 'IS_PROFILE_PAGE', ( $user_id === $current_user->ID ) );
}

if ( ! $user_id && IS_PROFILE_PAGE ) {
	$user_id = $current_user->ID;
} elseif ( ! $user_id && ! IS_PROFILE_PAGE ) {
	gc_die( __( '用户ID无效。' ) );
} elseif ( ! get_userdata( $user_id ) ) {
	gc_die( __( '用户ID无效。' ) );
}

gc_enqueue_script( 'user-profile' );

if ( gc_is_appkeys_available_for_user( $user_id ) ) {
	gc_enqueue_script( 'appkeys' );
}

if ( IS_PROFILE_PAGE ) {
	// Used in the HTML title tag.
	$title = __( '个人资料' );
} else {
	// Used in the HTML title tag.
	/* translators: %s: User's display name. */
	$title = __( '编辑用户%s' );
}

if ( current_user_can( 'edit_users' ) && ! IS_PROFILE_PAGE ) {
	$submenu_file = 'users.php';
} else {
	$submenu_file = 'profile.php';
}

if ( current_user_can( 'edit_users' ) && ! is_user_admin() ) {
	$parent_file = 'users.php';
} else {
	$parent_file = 'profile.php';
}

$profile_help = '<p>' . __( '您的个人资料包含您的个人信息和账户信息，以及使用GeChiUI的偏好设置。' ) . '</p>' .
	'<p>' . __( '您可以修改密码、启用键盘快捷键、更换GeChiUI管理界面配色、关闭可视化编辑器等。您也可以选择在系统前端隐藏工具栏（前称“管理工具栏”），但不能在管理界面隐藏。' ) . '</p>' .
	'<p>' . __( '您可以选择GeChiUI管理界面的语言，这不会影响到系统访客所看到的语言。' ) . '</p>' .
	'<p>' . __( '您的用户名不能更改，但是您可以在其他字段中输入真实姓名或昵称，即可决定在文章中显示哪个名字。' ) . '</p>' .
	'<p>' . __( '您可以通过点击 “注销除此之外的所有会话”按钮来注销其他设备的登录状态，例如您的手机或公共计算机。' ) . '</p>' .
	'<p>' . __( '必填项目有特殊标记，其余是选填项目。个人资料信息仅在主题需要时才可能被显示在系统前台。' ) . '</p>' .
	'<p>' . __( '在完成后不要忘记点击“更新个人资料”。' ) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => $profile_help,
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/users-your-profile-screen/">用户资料文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/forums/">支持论坛</a>' ) . '</p>'
);

$gc_http_referer = remove_query_arg( array( 'update', 'delete_count', 'user_id' ), $gc_http_referer );

$user_can_edit = current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' );

/**
 * Filters whether to allow administrators on Multisite to edit every user.
 *
 * Enabling the user editing form via this filter also hinges on the user holding
 * the 'manage_network_users' cap, and the logged-in user not matching the user
 * profile open for editing.
 *
 * The filter was introduced to replace the EDIT_ANY_USER constant.
 *
 * @param bool $allow Whether to allow editing of any user. Default true.
 */
if ( is_multisite()
	&& ! current_user_can( 'manage_network_users' )
	&& $user_id !== $current_user->ID
	&& ! apply_filters( 'enable_edit_any_user_configuration', true )
) {
	gc_die( __( '抱歉，您不能编辑此用户。' ) );
}

// Execute confirmed email change. See send_confirmation_on_profile_email().
if ( IS_PROFILE_PAGE && isset( $_GET['newuseremail'] ) && $current_user->ID ) {
	$new_email = get_user_meta( $current_user->ID, '_new_email', true );
	if ( $new_email && hash_equals( $new_email['hash'], $_GET['newuseremail'] ) ) {
		$user             = new stdClass();
		$user->ID         = $current_user->ID;
		$user->user_email = esc_html( trim( $new_email['newemail'] ) );
		if ( is_multisite() && $gcdb->get_var( $gcdb->prepare( "SELECT user_login FROM {$gcdb->signups} WHERE user_login = %s", $current_user->user_login ) ) ) {
			$gcdb->query( $gcdb->prepare( "UPDATE {$gcdb->signups} SET user_email = %s WHERE user_login = %s", $user->user_email, $current_user->user_login ) );
		}
		gc_update_user( $user );
		delete_user_meta( $current_user->ID, '_new_email' );
		gc_redirect( add_query_arg( array( 'updated' => 'true' ), self_admin_url( 'profile.php' ) ) );
		die();
	} else {
		gc_redirect( add_query_arg( array( 'error' => 'new-email' ), self_admin_url( 'profile.php' ) ) );
	}
} elseif ( IS_PROFILE_PAGE && ! empty( $_GET['dismiss'] ) && $current_user->ID . '_new_email' === $_GET['dismiss'] ) {
	check_admin_referer( 'dismiss-' . $current_user->ID . '_new_email' );
	delete_user_meta( $current_user->ID, '_new_email' );
	gc_redirect( add_query_arg( array( 'updated' => 'true' ), self_admin_url( 'profile.php' ) ) );
	die();
}

switch ( $action ) {
	case 'update':
		check_admin_referer( 'update-user_' . $user_id );

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			gc_die( __( '抱歉，您不能编辑此用户。' ) );
		}

		if ( IS_PROFILE_PAGE ) {
			/**
			 * Fires before the page loads on the 'Profile' editing screen.
			 *
			 * The action only fires if the current user is editing their own profile.
			 *
			 * @since 2.0.0
			 *
			 * @param int $user_id The user ID.
			 */
			do_action( 'personal_options_update', $user_id );
		} else {
			/**
			 * Fires before the page loads on the '编辑用户' screen.
			 *
			 * @since 2.7.0
			 *
			 * @param int $user_id The user ID.
			 */
			do_action( 'edit_user_profile_update', $user_id );
		}

		// Update the email address in signups, if present.
		if ( is_multisite() ) {
			$user = get_userdata( $user_id );

			if ( $user->user_login && isset( $_POST['email'] ) && is_email( $_POST['email'] ) && $gcdb->get_var( $gcdb->prepare( "SELECT user_login FROM {$gcdb->signups} WHERE user_login = %s", $user->user_login ) ) ) {
				$gcdb->query( $gcdb->prepare( "UPDATE {$gcdb->signups} SET user_email = %s WHERE user_login = %s", $_POST['email'], $user_login ) );
			}
		}

		// Update the user.
		$errors = edit_user( $user_id );

		// Grant or revoke super admin status if requested.
		if ( is_multisite() && is_network_admin()
			&& ! IS_PROFILE_PAGE && current_user_can( 'manage_network_options' )
			&& ! isset( $super_admins ) && empty( $_POST['super_admin'] ) === is_super_admin( $user_id )
		) {
			empty( $_POST['super_admin'] ) ? revoke_super_admin( $user_id ) : grant_super_admin( $user_id );
		}

		if ( ! is_gc_error( $errors ) ) {
			$redirect = add_query_arg( 'updated', true, get_edit_user_link( $user_id ) );
			if ( $gc_http_referer ) {
				$redirect = add_query_arg( 'gc_http_referer', urlencode( $gc_http_referer ), $redirect );
			}
			gc_redirect( $redirect );
			exit;
		}

		// Intentional fall-through to display $errors.
	default:
		$profile_user = get_user_to_edit( $user_id );

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			gc_die( __( '抱歉，您不能编辑此用户。' ) );
		}

		$title    = sprintf( $title, $profile_user->display_name );
		$sessions = GC_Session_Tokens::get_instance( $profile_user->ID );


		 if ( ! IS_PROFILE_PAGE && is_super_admin( $profile_user->ID ) && current_user_can( 'manage_network_options' ) ) {
			$message = '<strong>'. __( '重要：' ) .'</strong>' . __( '此用户拥有超级管理员权限。' );
			add_settings_error( 'general', 'settings_updated', $message, 'warning' );
		 }

		if ( isset( $_GET['updated'] ) ) {
			if ( IS_PROFILE_PAGE ) {
				$message = __( '个人资料已更新。' );
			} else {
				$message = __( '已更新用户。' );
			}
			if ( $gc_http_referer && ! str_contains( $gc_http_referer, 'user-new.php' ) && ! IS_PROFILE_PAGE ) {
				$message = '<a href="'. esc_url( gc_validate_redirect( sanitize_url( $gc_http_referer ), self_admin_url( 'users.php' ) ) ) .'">'. __( '&larr; 转到“用户”页面' ) .'</a>';
			}
			add_settings_error( 'general', 'settings_updated', $message, 'success' );
		}

		if ( isset( $_GET['error'] ) ) {
				 if ( 'new-email' === $_GET['error'] ) {
				 	add_settings_error( 'general', 'settings_updated', __( '保存新的电子邮箱时发生错误，请重试。' ), 'danger' );
				}
		}

		if ( isset( $errors ) && is_gc_error( $errors ) ) {
			add_settings_error( 'general', 'settings_updated', $errors->get_error_messages(), 'danger' );
		}

		require_once ABSPATH . 'gc-admin/admin-header.php';
		?>

		<div class="wrap" id="profile-page">
			<div class="page-header">
				<h2 class="header-title"><?php echo esc_html( $title ); ?></h2>
				<?php if ( ! IS_PROFILE_PAGE ) : ?>
					<?php if ( current_user_can( 'create_users' ) ) : ?>
						<a href="user-new.php" class="btn btn-primary btn-tone btn-sm"><?php echo esc_html_x( '添加用户', 'user' ); ?></a>
					<?php elseif ( is_multisite() && current_user_can( 'promote_users' ) ) : ?>
						<a href="user-new.php" class="btn btn-primary btn-tone btn-sm"><?php echo esc_html_x( '添加现有用户', 'user' ); ?></a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<form id="your-profile" action="<?php echo esc_url( self_admin_url( IS_PROFILE_PAGE ? 'profile.php' : 'user-edit.php' ) ); ?>" method="post" novalidate="novalidate"
			<?php
			/**
			 * Fires inside the your-profile form tag on the user editing screen.
			 *
			 * @since 3.0.0
			 */
			do_action( 'user_edit_form_tag' );
			?>
			>
			<?php gc_nonce_field( 'update-user_' . $user_id ); ?>
			<?php if ( $gc_http_referer ) : ?>
				<input type="hidden" name="gc_http_referer" value="<?php echo esc_url( $gc_http_referer ); ?>" />
			<?php endif; ?>
			<p>
				<input type="hidden" name="from" value="profile" />
				<input type="hidden" name="checkuser_id" value="<?php echo get_current_user_id(); ?>" />
			</p>
			<div class="card">
				<div class="card-header">
					<h4 class="card-title"><?php _e( '个人设置' ); ?></h4>
				</div>
				<div class="card-body">
				<table class="form-table" role="presentation">
					<?php if ( ! ( IS_PROFILE_PAGE && ! $user_can_edit ) ) : ?>
						<tr class="user-rich-editing-wrap">
							<th scope="row"><?php _e( '可视化编辑器' ); ?></th>
							<td>
								<label for="rich_editing"><input name="rich_editing" type="checkbox" id="rich_editing" value="false" <?php checked( 'false', $profile_user->rich_editing ); ?> />
									<?php _e( '撰写文章时不使用可视化编辑器' ); ?>
								</label>
							</td>
						</tr>
					<?php endif; ?>

					<?php
					$show_syntax_highlighting_preference = (
					// For Custom HTML widget and Additional CSS in Customizer.
					user_can( $profile_user, 'edit_theme_options' )
					||
					// Edit plugins.
					user_can( $profile_user, 'edit_plugins' )
					||
					// Edit themes.
					user_can( $profile_user, 'edit_themes' )
					);
					?>

					<?php if ( $show_syntax_highlighting_preference ) : ?>
					<tr class="user-syntax-highlighting-wrap">
						<th scope="row"><?php _e( '语法高亮' ); ?></th>
						<td>
							<label for="syntax_highlighting"><input name="syntax_highlighting" type="checkbox" id="syntax_highlighting" value="false" <?php checked( 'false', $profile_user->syntax_highlighting ); ?> />
								<?php _e( '在编辑代码时禁用语法高亮' ); ?>
							</label>
						</td>
					</tr>
					<?php endif; ?>

					<?php if ( ! ( IS_PROFILE_PAGE && ! $user_can_edit ) ) : ?>
					<tr class="user-comment-shortcuts-wrap">
						<th scope="row"><?php _e( '键盘快捷键' ); ?></th>
						<td>
							<label for="comment_shortcuts">
								<input type="checkbox" name="comment_shortcuts" id="comment_shortcuts" value="true" <?php checked( 'true', $profile_user->comment_shortcuts ); ?> />
								<?php _e( '管理评论时启用键盘快捷键。' ); ?>
							</label>
							<?php _e( '<a href="https://www.gechiui.com/support/keyboard-shortcuts/">键盘快捷键文档</a>' ); ?>
						</td>
					</tr>
					<?php endif; ?>

					<tr class="show-admin-bar user-admin-bar-front-wrap">
						<th scope="row"><?php _e( '工具栏' ); ?></th>
						<td>
							<label for="admin_bar_front">
								<input name="admin_bar_front" type="checkbox" id="admin_bar_front" value="1"<?php checked( _get_admin_bar_pref( 'front', $profile_user->ID ) ); ?> />
								<?php _e( '在浏览前端系统时显示工具栏' ); ?>
							</label><br />
						</td>
					</tr>

					<?php
					$languages                = get_available_languages();
					$can_install_translations = current_user_can( 'install_languages' ) && gc_can_install_language_pack();
					?>
					<?php if ( $languages || $can_install_translations ) : ?>
					<tr class="user-language-wrap">
						<th scope="row">
							<?php /* translators: The user language selection field label. */ ?>
							<label for="locale"><?php _e( '语言' ); ?><span class="dashicons dashicons-translation" aria-hidden="true"></span></label>
						</th>
						<td>
							<?php
								$user_locale = $profile_user->locale;

							if ( 'zh_CN' === $user_locale ) {
								$user_locale = '';
							} elseif ( '' === $user_locale || ! in_array( $user_locale, $languages, true ) ) {
								$user_locale = 'site-default';
							}

							gc_dropdown_languages(
								array(
									'name'      => 'locale',
									'id'        => 'locale',
									'selected'  => $user_locale,
									'languages' => $languages,
									'show_available_translations' => $can_install_translations,
									'show_option_site_default' => true,
								)
							);
							?>
						</td>
					</tr>
					<?php endif; ?>

					<?php
					/**
					 * Fires at the end of the '个人设置' settings table on the user editing screen.
					 *
					 * @since 2.7.0
					 *
					 * @param GC_User $profile_user The current GC_User object.
					 */
					do_action( 'personal_options', $profile_user );
					?>

				</table>
				<?php
				if ( IS_PROFILE_PAGE ) {
					/**
					 * Fires after the '个人设置' settings table on the 'Profile' editing screen.
					 *
					 * The action only fires if the current user is editing their own profile.
					 *
					 * @since 2.0.0
					 *
					 * @param GC_User $profile_user The current GC_User object.
					 */
					do_action( 'profile_personal_options', $profile_user );
				}
				?>

				</div>
			</div>
			<div class="card">
				<div class="card-header">
					<h4 class="card-title"><?php _e( '名称' ); ?></h4>
				</div>
				<div class="card-body">

				<table class="form-table" role="presentation">
					<tr class="user-user-login-wrap">
						<th><label for="user_login"><?php _e( '用户名' ); ?></label></th>
						<td><input type="text" name="user_login" id="user_login" value="<?php echo esc_attr( $profile_user->user_login ); ?>" disabled="disabled" class="regular-text" /> <span class="description"><?php _e( '用户名不可更改。' ); ?></span></td>
					</tr>

					<?php if ( ! IS_PROFILE_PAGE && ! is_network_admin() && current_user_can( 'promote_user', $profile_user->ID ) ) : ?>
						<tr class="user-role-wrap">
							<th><label for="role"><?php _e( '角色' ); ?></label></th>
							<td>
								<select name="role" id="role">
									<?php
									// Compare user role against currently editable roles.
									$user_roles = array_intersect( array_values( $profile_user->roles ), array_keys( get_editable_roles() ) );
									$user_role  = reset( $user_roles );

									// Print the full list of roles with the primary one selected.
									gc_dropdown_roles( $user_role );

									// Print the 'no role' option. Make it selected if the user has no role yet.
									if ( $user_role ) {
										echo '<option value="">' . __( '—这个系统没有任何用户角色—' ) . '</option>';
									} else {
										echo '<option value="" selected="selected">' . __( '—这个系统没有任何用户角色—' ) . '</option>';
									}
									?>
							</select>
							</td>
						</tr>
					<?php endif; // End if ! IS_PROFILE_PAGE. ?>

					<?php if ( is_multisite() && is_network_admin() && ! IS_PROFILE_PAGE && current_user_can( 'manage_network_options' ) && ! isset( $super_admins ) ) : ?>
						<tr class="user-super-admin-wrap">
							<th><?php _e( '超级管理员' ); ?></th>
							<td>
								<?php if ( 0 !== strcasecmp( $profile_user->user_email, get_site_option( 'admin_email' ) ) || ! is_super_admin( $profile_user->ID ) ) : ?>
									<p><label><input type="checkbox" id="super_admin" name="super_admin"<?php checked( is_super_admin( $profile_user->ID ) ); ?> /> <?php _e( '为此用户授予SaaS平台超级管理员权限。' ); ?></label></p>
								<?php else : ?>
									<p><?php _e( '超级管理员权限无法移除，因为该用户使用的是平台管理员电子邮箱。' ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endif; ?>

					<tr class="user-first-name-wrap">
						<th><label for="first_name"><?php _e( '名字' ); ?></label></th>
						<td><input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $profile_user->first_name ); ?>" class="regular-text" /></td>
					</tr>

					<tr class="user-last-name-wrap">
						<th><label for="last_name"><?php _e( '姓氏' ); ?></label></th>
						<td><input type="text" name="last_name" id="last_name" value="<?php echo esc_attr( $profile_user->last_name ); ?>" class="regular-text" /></td>
					</tr>

					<tr class="user-nickname-wrap">
						<th><label for="nickname"><?php _e( '昵称' ); ?> <span class="description"><?php _e( '（必填）' ); ?></span></label></th>
						<td><input type="text" name="nickname" id="nickname" value="<?php echo esc_attr( $profile_user->nickname ); ?>" class="regular-text" /></td>
					</tr>

					<tr class="user-display-name-wrap">
						<th>
							<label for="display_name"><?php _e( '公开显示为' ); ?></label>
						</th>
						<td>
							<select name="display_name" id="display_name">
								<?php
									$public_display                     = array();
									$public_display['display_nickname'] = $profile_user->nickname;
									$public_display['display_username'] = $profile_user->user_login;

								if ( ! empty( $profile_user->first_name ) ) {
									$public_display['display_firstname'] = $profile_user->first_name;
								}

								if ( ! empty( $profile_user->last_name ) ) {
									$public_display['display_lastname'] = $profile_user->last_name;
								}

								if ( ! empty( $profile_user->first_name ) && ! empty( $profile_user->last_name ) ) {
									$public_display['display_firstlast'] = $profile_user->first_name . ' ' . $profile_user->last_name;
									$public_display['display_lastfirst'] = $profile_user->last_name . ' ' . $profile_user->first_name;
									$public_display['display_lastfirst2'] = $profile_user->last_name . $profile_user->first_name;
								}

								if ( ! in_array( $profile_user->display_name, $public_display, true ) ) { // Only add this if it isn't duplicated elsewhere.
									$public_display = array( 'display_displayname' => $profile_user->display_name ) + $public_display;
								}

								$public_display = array_map( 'trim', $public_display );
								$public_display = array_unique( $public_display );

								?>
								<?php foreach ( $public_display as $id => $item ) : ?>
									<option <?php selected( $profile_user->display_name, $item ); ?>><?php echo $item; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				</div>
			</div>
			<div class="card">
				<div class="card-header">
					<h4 class="card-title"><?php _e( '联系信息' ); ?></h4>
				</div>
				<div class="card-body">

				<table class="form-table" role="presentation">
					<tr class="user-email-wrap">
						<th><label for="email"><?php _e( '电子邮箱' ); ?> <span class="description"><?php _e( '（必填）' ); ?></span></label></th>
						<td>
							<input type="email" name="email" id="email" aria-describedby="email-description" value="<?php echo esc_attr( $profile_user->user_email ); ?>" class="regular-text ltr" />
							<?php if ( $profile_user->ID === $current_user->ID ) : ?>
								<p class="description" id="email-description">
									<?php _e( '如果您更改此设置，系统将向您的新电子邮箱发送一封电子邮件进行确认。 <strong>新电子邮箱在确认之前不会生效</strong>。' ); ?>
								</p>
							<?php endif; ?>

							<?php $new_email = get_user_meta( $current_user->ID, '_new_email', true ); ?>
							<?php if ( $new_email && $new_email['newemail'] !== $current_user->user_email && $profile_user->ID === $current_user->ID ) : ?>
							<div class="updated inline">
								<p>
									<?php
									printf(
										/* translators: %s: New email. */
										__( '您即将修改您的电子邮箱为%s。' ),
										'<code>' . esc_html( $new_email['newemail'] ) . '</code>'
									);
									printf(
										' <a href="%1$s">%2$s</a>',
										esc_url( gc_nonce_url( self_admin_url( 'profile.php?dismiss=' . $current_user->ID . '_new_email' ), 'dismiss-' . $current_user->ID . '_new_email' ) ),
										__( '取消' )
									);
									?>
								</p>
							</div>
							<?php endif; ?>
						</td>
					</tr>

					<tr class="user-url-wrap">
						<th><label for="url"><?php _e( '网站地址' ); ?></label></th>
						<td><input type="url" name="url" id="url" value="<?php echo esc_attr( $profile_user->user_url ); ?>" class="regular-text code" /></td>
					</tr>

					<?php foreach ( gc_get_user_contact_methods( $profile_user ) as $name => $desc ) : ?>
					<tr class="user-<?php echo $name; ?>-wrap">
						<th>
							<label for="<?php echo $name; ?>">
							<?php
							/**
							 * Filters a user contactmethod label.
							 *
							 * The dynamic portion of the hook name, `$name`, refers to
							 * each of the keys in the contact methods array.
							 *
							 * @since 2.9.0
							 *
							 * @param string $desc The translatable label for the contact method.
							 */
							echo apply_filters( "user_{$name}_label", $desc );
							?>
							</label>
						</th>
						<td>
							<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr( $profile_user->$name ); ?>" class="regular-text" />
						</td>
					</tr>
					<?php endforeach; ?>
				</table>
				</div>
			</div>
			<div class="card">
				<div class="card-header">
					<h4 class="card-title"><?php IS_PROFILE_PAGE ? _e( '关于您自己' ) : _e( '关于该用户' ); ?></h4>
				</div>
				<div class="card-body">

				<table class="form-table" role="presentation">
					<tr class="user-description-wrap">
						<th><label for="description"><?php _e( '个人说明' ); ?></label></th>
						<td><textarea name="description" id="description" rows="5" cols="30"><?php echo $profile_user->description; // textarea_escaped ?></textarea>
						<p class="description"><?php _e( '分享关于您的一些信息。可能会被公开。' ); ?></p></td>
					</tr>

					<?php if ( get_option( 'show_avatars' ) ) : ?>
						<tr class="user-profile-picture">
							<th><?php _e( '头像' ); ?></th>
							<td>
								<table>
									<tr>
										<td><?php echo get_avatar( $user_id ); ?></td>
										<td>
								            <?php
								                $options = get_option('simple_local_avatars_caps');
								        
								                if ( empty($options['simple_local_avatars_caps']) || current_user_can('upload_files') ) {
								                    do_action( 'simple_local_avatar_notices' );
								                    gc_nonce_field( 'simple_local_avatar_nonce', '_simple_local_avatar_nonce', false );
								            ?>
								                    <input type="file" name="simple-local-avatar" id="simple-local-avatar" /><br />
								            <?php
								                    if ( empty( $profile_user->simple_local_avatar ) )
								                        echo '<span class="description">' . __('尚未设置本地头像，请点击“浏览”按钮上传本地头像。','simple-local-avatars') . '</span>';
								                    else
								                        echo '
								                            <input type="checkbox" name="simple-local-avatar-erase" value="1" /> ' . __('移除本地头像','simple-local-avatars') . '<br />
								                            <span class="description">' . __('如需要修改本地头像，请重新上传新头像。如需要移除本地头像，请选中上方的“移除本地头像”复选框并更新个人资料即可。','simple-local-avatars') . '</span>
								                        ';     
								                } else {
								                    echo '<span class="description">' . __('你没有头像上传权限，如需要修改本地头像，请联系系统管理员。','simple-local-avatars') . '</span>';
								                }
								            ?>
								            <script type="text/javascript">var form = document.getElementById('your-profile');form.encoding = 'multipart/form-data';form.setAttribute('enctype', 'multipart/form-data');</script>
										</td>
									</tr>
								</table>
					        </td>
						</tr>
					<?php endif; ?>
					<?php
					/**
					 * Filters the display of the password fields.
					 *
					 * @since 1.5.1
					 * @since 2.8.0 Added the `$profile_user` parameter.
					 * @since 4.4.0 Now evaluated only in user-edit.php.
					 *
					 * @param bool    $show         Whether to show the password fields. Default true.
					 * @param GC_User $profile_user User object for the current user to edit.
					 */
					$show_password_fields = apply_filters( 'show_password_fields', true, $profile_user );
					?>
					<?php if ( $show_password_fields ) : ?>
						</table>

					</div>
				</div>
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><?php _e( '账户管理' ); ?></h4>
					</div>
					<div class="card-body">

						<table class="form-table" role="presentation">
							<tr id="password" class="user-pass1-wrap">
								<th><label for="pass1"><?php _e( '新密码' ); ?></label></th>
								<td>
									<input type="hidden" value=" " /><!-- #24364 workaround -->
									<button type="button" class="btn btn-primary btn-tone btn-sm gc-generate-pw hide-if-no-js" aria-expanded="false"><?php _e( '设置新密码' ); ?></button>
									<div class="gc-pwd hide-if-js">
										<div class="password-input-wrapper">
											<input type="password" name="pass1" id="pass1" class="regular-text" value="" autocomplete="new-password" spellcheck="false" data-pw="<?php echo esc_attr( gc_generate_password( 24 ) ); ?>" aria-describedby="pass-strength-result" />
											
										</div>
										<button type="button" class="btn btn-primary btn-tone btn-sm gc-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( '隐藏密码' ); ?>">
											<span class="dashicons dashicons-hidden" aria-hidden="true"></span>
											<span class="text"><?php _e( '隐藏' ); ?></span>
										</button>
										<button type="button" class="btn btn-primary btn-tone btn-sm gc-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( '取消密码修改' ); ?>">
											<span class="dashicons dashicons-no" aria-hidden="true"></span>
											<span class="text"><?php _e( '取消' ); ?></span>
										</button>
										<div style="display:none" id="pass-strength-result" aria-live="polite"></div>
									</div>
								</td>
							</tr>
							<tr class="user-pass2-wrap hide-if-js">
								<th scope="row"><label for="pass2"><?php _e( '重复新密码' ); ?></label></th>
								<td>
								<input type="password" name="pass2" id="pass2" class="regular-text" value="" autocomplete="new-password" spellcheck="false" aria-describedby="pass2-desc" />
									<?php if ( IS_PROFILE_PAGE ) : ?>
										<p class="description" id="pass2-desc"><?php _e( '再输入一遍新密码。' ); ?></p>
									<?php else : ?>
										<p class="description" id="pass2-desc"><?php _e( '再输入新密码。' ); ?></p>
									<?php endif; ?>
								</td>
							</tr>
							<tr class="pw-weak">
								<th><?php _e( '确认密码' ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="pw_weak" class="pw-checkbox" />
										<span id="pw-weak-text-label"><?php _e( '确认使用弱密码' ); ?></span>
									</label>
								</td>
							</tr>
							<?php endif; // End Show Password Fields. ?>

							<?php // Allow admins to send reset password link. ?>
							<?php if ( ! IS_PROFILE_PAGE && true === gc_is_password_reset_allowed_for_user( $profile_user ) ) : ?>
								<tr class="user-generate-reset-link-wrap hide-if-no-js">
									<th><?php _e( '密码重置' ); ?></th>
									<td>
										<div class="generate-reset-link">
											<button type="button" class="btn btn-primary btn-tone btn-sm" id="generate-reset-link">
												<?php _e( '发送重置链接' ); ?>
											</button>
										</div>
										<p class="description">
											<?php
											printf(
												/* translators: %s: User's display name. */
												__( '将链接发送给%s以重置其密码。本操作将不会修改其密码，也不会强制其修改密码。' ),
												esc_html( $profile_user->display_name )
											);
											?>
										</p>
									</td>
								</tr>
							<?php endif; ?>

							<?php if ( IS_PROFILE_PAGE && count( $sessions->get_all() ) === 1 ) : ?>
								<tr class="user-sessions-wrap hide-if-no-js">
									<th><?php _e( '会话' ); ?></th>
									<td aria-live="assertive">
										<div class="destroy-sessions"><button type="button" disabled class="btn btn-primary btn-tone btn-sm"><?php _e( '注销除此之外的所有会话' ); ?></button></div>
										<p class="description">
											<?php _e( '您目前只有当前会话保持登录状态。' ); ?>
										</p>
									</td>
								</tr>
							<?php elseif ( IS_PROFILE_PAGE && count( $sessions->get_all() ) > 1 ) : ?>
								<tr class="user-sessions-wrap hide-if-no-js">
									<th><?php _e( '会话' ); ?></th>
									<td aria-live="assertive">
										<div class="destroy-sessions"><button type="button" class="btn btn-primary btn-tone btn-sm" id="destroy-sessions"><?php _e( '注销除此之外的所有会话' ); ?></button></div>
										<p class="description">
											<?php _e( '如果您丢失了您的手机或是在公共电脑上登录了您的账户，您可以立即注销其他设备的登陆状态，并只保留此处的登录状态。' ); ?>
										</p>
									</td>
								</tr>
							<?php elseif ( ! IS_PROFILE_PAGE && $sessions->get_all() ) : ?>
								<tr class="user-sessions-wrap hide-if-no-js">
									<th><?php _e( '会话' ); ?></th>
									<td>
										<p><button type="button" class="btn btn-primary btn-tone btn-sm" id="destroy-sessions"><?php _e( '注销所有会话' ); ?></button></p>
										<p class="description">
											<?php
											/* translators: %s: User's display name. */
											printf( __( '注销%s的所有会话。' ), $profile_user->display_name );
											?>
										</p>
									</td>
								</tr>
							<?php endif; ?>
						</table>
					</div>
				</div>

				<?php if ( gc_is_appkeys_available_for_user( $user_id ) || ! gc_is_appkeys_supported() ) : ?>
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><?php _e( 'REST API 管理' ); ?></h4>
					</div>
						<div class="card-body appkeys hide-if-no-js" id="appkeys-section">
							<p><?php _e( 'Appkey允许通过非交互式系统（例如XML-RPC或REST API）进行身份验证，而无需提供您的实际密码。应用密码可以随时撤销。它们不能用于通过传统方式登录您的系统。' ); ?></p>
							<?php if ( gc_is_appkeys_available_for_user( $user_id ) ) : ?>
								<?php
								if ( is_multisite() ) :
									$blogs       = get_blogs_of_user( $user_id, true );
									$blogs_count = count( $blogs );

									if ( $blogs_count > 1 ) :
										?>
										<p>
											<?php
											/* translators: 1: URL to my-sites.php, 2: Number of sites the user has. */
											$message = _n(
												'Appkey让您能够访问<a href="%1$s">此SaaS平台中您拥有权限的 %2$s 个系统</a>。',
												'Appkey让您能够访问<a href="%1$s">此SaaS平台中您拥有权限的 %2$s 个系统</a>。',
												$blogs_count
											);

											if ( is_super_admin( $user_id ) ) {
												/* translators: 1: URL to my-sites.php, 2: Number of sites the user has. */
												$message = _n(
													'Appkey授予您访问 <a href="%1$s">网络上%2$s系统的权限，因为您拥有超级管理员权限</a>.',
													'Appkey授予您访问 <a href="%1$s">网络上%2$s系统的权限，因为您拥有超级管理员权限</a>.',
													$blogs_count
												);
											}

											printf(
												$message,
												admin_url( 'my-sites.php' ),
												number_format_i18n( $blogs_count )
											);
											?>
										</p>
										<?php
									endif;
								endif;
								?>

								<?php if ( ! gc_is_site_protected_by_basic_auth( 'front' ) ) : ?>
									<div class="create-appkey form-wrap">
										<div class="form-field">
											<label for="new_appname"><?php _e( '新的应用名称' ); ?></label>
											<input type="text" size="30" id="new_appname" name="new_appname" class="input" aria-required="true" aria-describedby="new_appname_desc" spellcheck="false" />
											<p class="description" id="new_appname_desc"><?php _e( '此功能需要创建Appkey，但不需要更新用户信息。' ); ?></p>
										</div>

										<?php
										/**
										 * Fires in the create Application Passwords form.
										 *
										 * @since 5.6.0
										 *
										 * @param GC_User $profile_user The current GC_User object.
										 */
										do_action( 'gc_create_appkey_form', $profile_user );
										?>

										<button type="button" name="do_new_appkey" id="do_new_appkey" class="btn btn-primary"><?php _e( '添加新的Appkey' ); ?></button>
									</div>
								<?php else : 
										echo setting_error( __( '您的系统似乎正在使用HTTP基本认证功能，该功能目前与Appkey功能不兼容。' ), 'danger inline' );
								endif; ?>

								<div class="appkeys-list-table-wrapper">
									<?php
									$appkeys_list_table = _get_list_table( 'GC_AppKeys_List_Table', array( 'screen' => 'appkeys-user' ) );
									$appkeys_list_table->prepare_items();
									$appkeys_list_table->display();
									?>
								</div>
							<?php elseif ( ! gc_is_appkeys_supported() ) : ?>
								<p><?php _e( 'Appkey功能需要 HTTPS ，此系统未启用 https。' ); ?></p>
								<p>
									<?php
									printf(
										/* translators: %s: Documentation URL. */
										__( '如果这是一个开发系统，您可以<a href="%s" target="_blank">设置相应地环境类型</a>以启用Appkey功能。' ),
										__( 'https://www.gechiui.com/support/editing-gc-config-php/#gc_environment_type' )
									);
									?>
								</p>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php endif; // End Application Passwords. ?>

				<?php
				if ( IS_PROFILE_PAGE ) {
					/**
					 * Fires after the '关于您自己' settings table on the 'Profile' editing screen.
					 *
					 * The action only fires if the current user is editing their own profile.
					 *
					 * @since 2.0.0
					 *
					 * @param GC_User $profile_user The current GC_User object.
					 */
					do_action( 'show_user_profile', $profile_user );
				} else {
					/**
					 * Fires after the 'About the User' settings table on the '编辑用户' screen.
					 *
					 * @since 2.0.0
					 *
					 * @param GC_User $profile_user The current GC_User object.
					 */
					do_action( 'edit_user_profile', $profile_user );
				}
				?>

				<?php
				/**
				 * Filters whether to display additional capabilities for the user.
				 *
				 * The '额外权限' section will only be enabled if
				 * the number of the user's capabilities exceeds their number of
				 * roles.
				 *
				 * @since 2.8.0
				 *
				 * @param bool    $enable      Whether to display the capabilities. Default true.
				 * @param GC_User $profile_user The current GC_User object.
				 */
				$display_additional_caps = apply_filters( 'additional_capabilities_display', true, $profile_user );
				?>

			<?php if ( count( $profile_user->caps ) > count( $profile_user->roles ) && ( true === $display_additional_caps ) ) : ?>
			<div class="card">
				<div class="card-header">
					<h4 class="card-title"><?php _e( '额外权限' ); ?></h4>
				</div>
				<div class="card-body">
					<table class="form-table" role="presentation">
						<tr class="user-capabilities-wrap">
							<th scope="row"><?php _e( '机能' ); ?></th>
							<td>
								<?php
								$output = '';
								foreach ( $profile_user->caps as $cap => $value ) {
									if ( ! $gc_roles->is_role( $cap ) ) {
										if ( '' !== $output ) {
											$output .= ', ';
										}

										if ( $value ) {
											$output .= $cap;
										} else {
											/* translators: %s: Capability name. */
											$output .= sprintf( __( '拒绝：%s' ), $cap );
										}
									}
								}
								echo $output;
								?>
							</td>
						</tr>
					</table>
					<?php endif; // End Display Additional Capabilities. ?>
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr( $user_id ); ?>" />
					</div>
				</div>
				<?php submit_button( IS_PROFILE_PAGE ? __( '更新个人资料' ) : __( '更新用户' ) ); ?>
			</form>
		</div>
		<?php
		break;
}
?>
<script type="text/javascript">
	if (window.location.hash == '#password') {
		document.getElementById('pass1').focus();
	}
</script>

<script type="text/javascript">
	jQuery( function( $ ) {
		var languageSelect = $( '#locale' );
		$( 'form' ).on( 'submit', function() {
			/*
			 * Don't show a spinner for English and installed languages,
			 * as there is nothing to download.
			 */
			if ( ! languageSelect.find( 'option:selected' ).data( 'installed' ) ) {
				$( '#submit', this ).after( '<span class="spinner language-install-spinner is-active" />' );
			}
		});
	} );
</script>

<?php if ( isset( $appkeys_list_table ) ) : ?>
	<script type="text/html" id="tmpl-new-appkey">
		<div class="alert alert-success is-dismissible new-appkey-notice" role="alert" tabindex="-1">
			<p class="appkey-display">
				<label for="new-appkey-value">
					<?php
					printf(
						/* translators: %s: Application name. */
						__( '%s的新Appkey为：' ),
						'<strong>{{ data.name }}</strong>'
					);
					?>
				</label>
				<input id="new-appkey-value" type="text" class="code" readonly="readonly" value="{{ data.password }}" />
			</p>
			<p><?php _e( '确保将其保存在安全的位置。 您将无法检索它。' ); ?></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '忽略此通知。' );
					?>
				</span>
			</button>
		</div>
	</script>

	<script type="text/html" id="tmpl-appkey-row">
		<?php $appkeys_list_table->print_js_template_row(); ?>
	</script>
<?php endif; ?>
<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';
