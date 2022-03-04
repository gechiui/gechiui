<?php
/**
 * Add Site Administration Screen
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

/** GeChiUI Translation Installation API */
require_once ABSPATH . 'gc-admin/includes/translation-install.php';

if ( ! current_user_can( 'create_sites' ) ) {
	gc_die( __( '抱歉，您无法在此站点网络中添加站点。' ) );
}

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
			'<p>' . __( '此页面供超级管理员向站点网络添加新站点使用。在这里添加站点不受站点注册策略的限制。' ) . '</p>' .
			'<p>' . __( '若新站点填写的管理员电子邮箱不存在于站点网络中，新用户也将一并被创建。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/network-admin-sites-screen/">站点管理文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/forum/issues/multisite/">支持论坛</a>' ) . '</p>'
);

if ( isset( $_REQUEST['action'] ) && 'add-site' === $_REQUEST['action'] ) {
	check_admin_referer( 'add-blog', '_gcnonce_add-blog' );

	if ( ! is_array( $_POST['blog'] ) ) {
		gc_die( __( '不能创建空站点。' ) );
	}

	$blog   = $_POST['blog'];
	$domain = '';

	$blog['domain'] = trim( $blog['domain'] );
	if ( preg_match( '|^([a-zA-Z0-9-])+$|', $blog['domain'] ) ) {
		$domain = strtolower( $blog['domain'] );
	}

	// If not a subdomain installation, make sure the domain isn't a reserved word.
	if ( ! is_subdomain_install() ) {
		$subdirectory_reserved_names = get_subdirectory_reserved_names();

		if ( in_array( $domain, $subdirectory_reserved_names, true ) ) {
			gc_die(
				sprintf(
					/* translators: %s: Reserved names list. */
					__( '以下保留字词仅供 GeChiUI 函数使用，无法用作站点名称：%s' ),
					'<code>' . implode( '</code>, <code>', $subdirectory_reserved_names ) . '</code>'
				)
			);
		}
	}

	$title = $blog['title'];

	$meta = array(
		'public' => 1,
	);

	// Handle translation installation for the new site.
	if ( isset( $_POST['GCLANG'] ) ) {
		if ( '' === $_POST['GCLANG'] ) {
			$meta['GCLANG'] = ''; // zh_CN
		} elseif ( in_array( $_POST['GCLANG'], get_available_languages(), true ) ) {
			$meta['GCLANG'] = $_POST['GCLANG'];
		} elseif ( current_user_can( 'install_languages' ) && gc_can_install_language_pack() ) {
			$language = gc_download_language_pack( gc_unslash( $_POST['GCLANG'] ) );
			if ( $language ) {
				$meta['GCLANG'] = $language;
			}
		}
	}

	if ( empty( $domain ) ) {
		gc_die( __( '站点地址缺少或无效。' ) );
	}

	if ( isset( $blog['email'] ) && '' === trim( $blog['email'] ) ) {
		gc_die( __( '电子邮箱缺失。' ) );
	}

	$email = sanitize_email( $blog['email'] );
	if ( ! is_email( $email ) ) {
		gc_die( __( '电子邮箱无效。' ) );
	}

	if ( is_subdomain_install() ) {
		$newdomain = $domain . '.' . preg_replace( '|^www\.|', '', get_network()->domain );
		$path      = get_network()->path;
	} else {
		$newdomain = get_network()->domain;
		$path      = get_network()->path . $domain . '/';
	}

	$password = 'N/A';
	$user_id  = email_exists( $email );
	if ( ! $user_id ) { // Create a new user with a random password.
		/**
		 * Fires immediately before a new user is created via the network site-new.php page.
		 *
		 *
		 * @param string $email Email of the non-existent user.
		 */
		do_action( 'pre_network_site_new_created_user', $email );

		$user_id = username_exists( $domain );
		if ( $user_id ) {
			gc_die( __( '输入的域名或路径与现有的用户名冲突。' ) );
		}
		$password = gc_generate_password( 12, false );
		$user_id  = gcmu_create_user( $domain, $password, $email );
		if ( false === $user_id ) {
			gc_die( __( '创建用户过程中出错。' ) );
		}

		/**
		 * Fires after a new user has been created via the network site-new.php page.
		 *
		 *
		 * @param int $user_id ID of the newly created user.
		 */
		do_action( 'network_site_new_created_user', $user_id );
	}

	$gcdb->hide_errors();
	$id = gcmu_create_blog( $newdomain, $path, $title, $user_id, $meta, get_current_network_id() );
	$gcdb->show_errors();

	if ( ! is_gc_error( $id ) ) {
		if ( ! is_super_admin( $user_id ) && ! get_user_option( 'primary_blog', $user_id ) ) {
			update_user_option( $user_id, 'primary_blog', $id, true );
		}

		gcmu_new_site_admin_notification( $id, $user_id );
		gcmu_welcome_notification( $id, $user_id, $password, $title, array( 'public' => 1 ) );
		gc_redirect(
			add_query_arg(
				array(
					'update' => 'added',
					'id'     => $id,
				),
				'site-new.php'
			)
		);
		exit;
	} else {
		gc_die( $id->get_error_message() );
	}
}

if ( isset( $_GET['update'] ) ) {
	$messages = array();
	if ( 'added' === $_GET['update'] ) {
		$messages[] = sprintf(
			/* translators: 1: Dashboard URL, 2: Network admin edit URL. */
			__( '站点已添加。<a href="%1$s">访问仪表盘</a>或<a href="%2$s">编辑站点</a>' ),
			esc_url( get_admin_url( absint( $_GET['id'] ) ) ),
			network_admin_url( 'site-info.php?id=' . absint( $_GET['id'] ) )
		);
	}
}

// Used in the HTML title tag.
$title       = __( '添加新站点' );
$parent_file = 'sites.php';

gc_enqueue_script( 'user-suggest' );

require_once ABSPATH . 'gc-admin/admin-header.php';

?>

<div class="wrap">
<h1 id="add-new-site"><?php _e( '添加新站点' ); ?></h1>
<?php
if ( ! empty( $messages ) ) {
	foreach ( $messages as $msg ) {
		echo '<div id="message" class="updated notice is-dismissible"><p>' . $msg . '</p></div>';
	}
}
?>
<p>
<?php
printf(
	/* translators: %s: Asterisk symbol (*). */
	__( '必填项已用%s标注' ),
	'<span class="required">*</span>'
);
?>
</p>
<form method="post" action="<?php echo esc_url( network_admin_url( 'site-new.php?action=add-site' ) ); ?>" novalidate="novalidate">
<?php gc_nonce_field( 'add-blog', '_gcnonce_add-blog' ); ?>
	<table class="form-table" role="presentation">
		<tr class="form-field form-required">
			<th scope="row"><label for="site-address"><?php _e( '站点地址（URL）' ); ?> <span class="required">*</span></label></th>
			<td>
			<?php if ( is_subdomain_install() ) { ?>
				<input name="blog[domain]" type="text" class="regular-text ltr" id="site-address" aria-describedby="site-address-desc" autocapitalize="none" autocorrect="off" required /><span class="no-break">.<?php echo preg_replace( '|^www\.|', '', get_network()->domain ); ?></span>
				<?php
			} else {
				echo get_network()->domain . get_network()->path
				?>
				<input name="blog[domain]" type="text" class="regular-text ltr" id="site-address" aria-describedby="site-address-desc" autocapitalize="none" autocorrect="off" required />
				<?php
			}
			echo '<p class="描述" id="site-address-desc">' . __( '只允许小写字母（a-z）、数字和连字符。' ) . '</p>';
			?>
			</td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><label for="site-title"><?php _e( '站点标题' ); ?> <span class="required">*</span></label></th>
			<td><input name="blog[title]" type="text" class="regular-text" id="site-title" required /></td>
		</tr>
		<?php
		$languages    = get_available_languages();
		$translations = gc_get_available_translations();
		if ( ! empty( $languages ) || ! empty( $translations ) ) :
			?>
			<tr class="form-field form-required">
				<th scope="row"><label for="site-language"><?php _e( '站点语言' ); ?></label></th>
				<td>
					<?php
					// Network default.
					$lang = get_site_option( 'GCLANG' );

					// Use English if the default isn't available.
					if ( ! in_array( $lang, $languages, true ) ) {
						$lang = '';
					}

					gc_dropdown_languages(
						array(
							'name'                        => 'GCLANG',
							'id'                          => 'site-language',
							'selected'                    => $lang,
							'languages'                   => $languages,
							'translations'                => $translations,
							'show_available_translations' => current_user_can( 'install_languages' ) && gc_can_install_language_pack(),
						)
					);
					?>
				</td>
			</tr>
		<?php endif; // Languages. ?>
		<tr class="form-field form-required">
			<th scope="row"><label for="admin-email"><?php _e( '管理员邮箱地址' ); ?> <span class="required">*</span></label></th>
			<td><input name="blog[email]" type="email" class="regular-text gc-suggest-user" id="admin-email" data-autocomplete-type="search" data-autocomplete-field="user_email" aria-describedby="site-admin-email" required /></td>
		</tr>
		<tr class="form-field">
			<td colspan="2" class="td-full"><p id="site-admin-email"><?php _e( '若邮箱地址在数据库中不存在，新用户将被创建。' ); ?><br /><?php _e( '用户名和密码设置链接会被发送到此电子邮箱。' ); ?></p></td>
		</tr>
	</table>

	<?php
	/**
	 * Fires at the end of the new site form in network admin.
	 *
	 */
	do_action( 'network_site_new_form' );

	submit_button( __( '添加站点' ), 'primary', 'add-site' );
	?>
	</form>
</div>
<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';
