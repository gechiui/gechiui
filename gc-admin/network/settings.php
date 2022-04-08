<?php
/**
 * Multisite network settings administration panel.
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

/** GeChiUI Translation Installation API */
require_once ABSPATH . 'gc-admin/includes/translation-install.php';

if ( ! current_user_can( 'manage_network_options' ) ) {
	gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
}

// Used in the HTML title tag.
$title       = __( '网络设置' );
$parent_file = 'settings.php';

// Handle network admin email change requests.
if ( ! empty( $_GET['network_admin_hash'] ) ) {
	$new_admin_details = get_site_option( 'network_admin_hash' );
	$redirect          = 'settings.php?updated=false';
	if ( is_array( $new_admin_details ) && hash_equals( $new_admin_details['hash'], $_GET['network_admin_hash'] ) && ! empty( $new_admin_details['newemail'] ) ) {
		update_site_option( 'admin_email', $new_admin_details['newemail'] );
		delete_site_option( 'network_admin_hash' );
		delete_site_option( 'new_admin_email' );
		$redirect = 'settings.php?updated=true';
	}
	gc_redirect( network_admin_url( $redirect ) );
	exit;
} elseif ( ! empty( $_GET['dismiss'] ) && 'new_network_admin_email' === $_GET['dismiss'] ) {
	check_admin_referer( 'dismiss_new_network_admin_email' );
	delete_site_option( 'network_admin_hash' );
	delete_site_option( 'new_admin_email' );
	gc_redirect( network_admin_url( 'settings.php?updated=true' ) );
	exit;
}

add_action( 'admin_head', 'network_settings_add_js' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
			'<p>' . __( '在本页面可对整个站点网络的设置进行修改。第一个站点是网络中的主站点，站点网络的设置从原始站点的设置中继承。' ) . '</p>' .
			'<p>' . __( '运营设置包括站点网络的名称及管理员电子邮箱字段。' ) . '</p>' .
			'<p>' . __( '注册选项可以启用/禁用公开注册。如果您允许其他人注册站点，建议您安装防垃圾内容（spam）的插件。您可以指定一些不允许作为站点名称的词语，用空格隔开（请注意不是逗号）。' ) . '</p>' .
			'<p>' . __( '新站点设置是对于未来注册的站点的默认值。包含“欢迎”邮件、首篇文章、首篇评论、首个页面的内容。' ) . '</p>' .
			'<p>' . __( '上传设置控制每个站点所能上传的文件数目、大小和文件类型（用空格隔开）。您也可以对每个站点做出不同的限制。' ) . '</p>' .
			'<p>' . __( '您可以设置语言，翻译文件将被自动下载并安装（如果您的文件系统可写）。' ) . '</p>' .
			'<p>' . __( '菜单设置允许您选择一般用户是否有权自行控制插件。' ) . '</p>' .
			'<p>' . __( '现在已经不能在设置页面添加超级管理员了。您需前往“管理网络” &rarr; “用户”页面，然后点击相应的用户名，或其下的编辑链接。之后您可在用户编辑页面为用户授予超级管理员权限。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/network-admin-settings-screen/">站点网络设置文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

if ( $_POST ) {
	/** This action is documented in gc-admin/network/edit.php */
	do_action( 'gcmuadminedit' );

	check_admin_referer( 'siteoptions' );

	$checked_options = array(
		'menu_items'                  => array(),
		'registrationnotification'    => 'no',
		'upload_space_check_disabled' => 1,
		'add_new_users'               => 0,
	);
	foreach ( $checked_options as $option_name => $option_unchecked_value ) {
		if ( ! isset( $_POST[ $option_name ] ) ) {
			$_POST[ $option_name ] = $option_unchecked_value;
		}
	}

	$options = array(
		'registrationnotification',
		'registration',
		'add_new_users',
		'menu_items',
		'upload_space_check_disabled',
		'blog_upload_space',
		'upload_filetypes',
		'site_name',
		'first_post',
		'first_page',
		'first_comment',
		'first_comment_url',
		'first_comment_author',
		'welcome_email',
		'welcome_user_email',
		'fileupload_maxk',
		'global_terms_enabled',
		'illegal_names',
		'limited_email_domains',
		'banned_email_domains',
		'GCLANG',
		'new_admin_email',
		'first_comment_email',
	);

	// Handle translation installation.
	if ( ! empty( $_POST['GCLANG'] ) && current_user_can( 'install_languages' ) && gc_can_install_language_pack() ) {
		$language = gc_download_language_pack( $_POST['GCLANG'] );
		if ( $language ) {
			$_POST['GCLANG'] = $language;
		}
	}

	foreach ( $options as $option_name ) {
		if ( ! isset( $_POST[ $option_name ] ) ) {
			continue;
		}
		$value = gc_unslash( $_POST[ $option_name ] );
		update_site_option( $option_name, $value );
	}

	/**
	 * Fires after the network options are updated.
	 *
	 * @since MU
	 */
	do_action( 'update_gcmu_options' );

	gc_redirect( add_query_arg( 'updated', 'true', network_admin_url( 'settings.php' ) ) );
	exit;
}

require_once ABSPATH . 'gc-admin/admin-header.php';

if ( isset( $_GET['updated'] ) ) {
	?><div id="message" class="updated notice is-dismissible"><p><?php _e( '设置已保存。' ); ?></p></div>
	<?php
}
?>

<div class="wrap">
	<h1><?php echo esc_html( $title ); ?></h1>
	<form method="post" action="settings.php" novalidate="novalidate">
		<?php gc_nonce_field( 'siteoptions' ); ?>
		<h2><?php _e( '运营设置' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="site_name"><?php _e( '网络标题' ); ?></label></th>
				<td>
					<input name="site_name" type="text" id="site_name" class="regular-text" value="<?php echo esc_attr( get_network()->site_name ); ?>" />
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="admin_email"><?php _e( '网络管理员邮箱' ); ?></label></th>
				<td>
					<input name="new_admin_email" type="email" id="admin_email" aria-describedby="admin-email-desc" class="regular-text" value="<?php echo esc_attr( get_site_option( 'admin_email' ) ); ?>" />
					<p class="description" id="admin-email-desc">
						<?php _e( '这个地址将被用于管理目的。如果您修改这个地址，我们将会向新地址发送一封邮件来确认。<strong>新的电子邮箱直到获得确认才会生效。</strong>' ); ?>
					</p>
					<?php
					$new_admin_email = get_site_option( 'new_admin_email' );
					if ( $new_admin_email && get_site_option( 'admin_email' ) !== $new_admin_email ) :
						?>
						<div class="updated inline">
						<p>
						<?php
							printf(
								/* translators: %s: New network admin email. */
								__( '网络管理员电子邮箱即将被修改为%s。' ),
								'<code>' . esc_html( $new_admin_email ) . '</code>'
							);
							printf(
								' <a href="%1$s">%2$s</a>',
								esc_url( gc_nonce_url( network_admin_url( 'settings.php?dismiss=new_network_admin_email' ), 'dismiss_new_network_admin_email' ) ),
								__( '取消' )
							);
						?>
						</p>
						</div>
					<?php endif; ?>
				</td>
			</tr>
		</table>
		<h2><?php _e( '注册设置' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php _e( '允许新站点注册' ); ?></th>
				<?php
				if ( ! get_site_option( 'registration' ) ) {
					update_site_option( 'registration', 'none' );
				}
				$reg = get_site_option( 'registration' );
				?>
				<td>
					<fieldset>
					<legend class="screen-reader-text"><?php _e( '新注册设置' ); ?></legend>
					<label><input name="registration" type="radio" id="registration1" value="none"<?php checked( $reg, 'none' ); ?> /> <?php _e( '注册已禁用' ); ?></label><br />
					<label><input name="registration" type="radio" id="registration2" value="user"<?php checked( $reg, 'user' ); ?> /> <?php _e( '用户可以注册账户' ); ?></label><br />
					<label><input name="registration" type="radio" id="registration3" value="blog"<?php checked( $reg, 'blog' ); ?> /> <?php _e( '已登录用户可以注册新站点' ); ?></label><br />
					<label><input name="registration" type="radio" id="registration4" value="all"<?php checked( $reg, 'all' ); ?> /> <?php _e( '可以注册站点和用户账户' ); ?></label>
					<?php
					if ( is_subdomain_install() ) {
						echo '<p class="description">';
						printf(
							/* translators: 1: NOBLOGREDIRECT, 2: gc-config.php */
							__( '如果注册未启用，请在%2$s中设置%1$s为您希望重定向不存在站点的访问者到的URL。' ),
							'<code>NOBLOGREDIRECT</code>',
							'<code>gc-config.php</code>'
						);
						echo '</p>';
					}
					?>
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php _e( '注册提醒' ); ?></th>
				<?php
				if ( ! get_site_option( 'registrationnotification' ) ) {
					update_site_option( 'registrationnotification', 'yes' );
				}
				?>
				<td>
					<label><input name="registrationnotification" type="checkbox" id="registrationnotification" value="yes"<?php checked( get_site_option( 'registrationnotification' ), 'yes' ); ?> /> <?php _e( '在有人注册站点或用户账户时向网络管理员发送邮件通知' ); ?></label>
				</td>
			</tr>

			<tr id="addnewusers">
				<th scope="row"><?php _e( '添加新用户' ); ?></th>
				<td>
					<label><input name="add_new_users" type="checkbox" id="add_new_users" value="1"<?php checked( get_site_option( 'add_new_users' ) ); ?> /> <?php _e( '允许站点管理员通过“用户 &rarr; 添加”页面添加新用户' ); ?></label>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="illegal_names"><?php _e( '不允许使用的名称' ); ?></label></th>
				<td>
					<?php
					$illegal_names = get_site_option( 'illegal_names' );

					if ( empty( $illegal_names ) ) {
						$illegal_names = '';
					} elseif ( is_array( $illegal_names ) ) {
						$illegal_names = implode( ' ', $illegal_names );
					}
					?>
					<input name="illegal_names" type="text" id="illegal_names" aria-describedby="illegal-names-desc" class="large-text" value="<?php echo esc_attr( $illegal_names ); ?>" size="45" />
					<p class="description" id="illegal-names-desc">
						<?php _e( '用户不可注册这些站点。名称间使用空格隔开。' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="limited_email_domains"><?php _e( '电子邮箱域名注册限制' ); ?></label></th>
				<td>
					<?php
					$limited_email_domains = get_site_option( 'limited_email_domains' );

					if ( empty( $limited_email_domains ) ) {
						$limited_email_domains = '';
					} else {
						// Convert from an input field. Back-compat for GCMU < 1.0.
						$limited_email_domains = str_replace( ' ', "\n", $limited_email_domains );

						if ( is_array( $limited_email_domains ) ) {
							$limited_email_domains = implode( "\n", $limited_email_domains );
						}
					}
					?>
					<textarea name="limited_email_domains" id="limited_email_domains" aria-describedby="limited-email-domains-desc" cols="45" rows="5">
<?php echo esc_textarea( $limited_email_domains ); ?></textarea>
					<p class="description" id="limited-email-domains-desc">
						<?php _e( '若您想把站点的注册限制于某些域名。每行一个域名。' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="banned_email_domains"><?php _e( '禁止使用的电子邮箱域名' ); ?></label></th>
				<td>
					<?php
					$banned_email_domains = get_site_option( 'banned_email_domains' );

					if ( empty( $banned_email_domains ) ) {
						$banned_email_domains = '';
					} elseif ( is_array( $banned_email_domains ) ) {
						$banned_email_domains = implode( "\n", $banned_email_domains );
					}
					?>
					<textarea name="banned_email_domains" id="banned_email_domains" aria-describedby="banned-email-domains-desc" cols="45" rows="5">
<?php echo esc_textarea( $banned_email_domains ); ?></textarea>
					<p class="description" id="banned-email-domains-desc">
						<?php _e( '如果您想禁止使用下列电子邮箱域名的用户注册站点。每行一个域。' ); ?>
					</p>
				</td>
			</tr>

		</table>
		<h2><?php _e( '新站点设置' ); ?></h2>
		<table class="form-table" role="presentation">

			<tr>
				<th scope="row"><label for="welcome_email"><?php _e( '“欢迎”邮件' ); ?></label></th>
				<td>
					<textarea name="welcome_email" id="welcome_email" aria-describedby="welcome-email-desc" rows="5" cols="45" class="large-text">
<?php echo esc_textarea( get_site_option( 'welcome_email' ) ); ?></textarea>
					<p class="description" id="welcome-email-desc">
						<?php _e( '用以欢迎新站点所有者的邮件内容。' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="welcome_user_email"><?php _e( '“欢迎”用户邮件' ); ?></label></th>
				<td>
					<textarea name="welcome_user_email" id="welcome_user_email" aria-describedby="welcome-user-email-desc" rows="5" cols="45" class="large-text">
<?php echo esc_textarea( get_site_option( 'welcome_user_email' ) ); ?></textarea>
					<p class="description" id="welcome-user-email-desc">
						<?php _e( '要发送给新用户的欢迎邮件内容。' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="first_post"><?php _e( '首篇文章' ); ?></label></th>
				<td>
					<textarea name="first_post" id="first_post" aria-describedby="first-post-desc" rows="5" cols="45" class="large-text">
<?php echo esc_textarea( get_site_option( 'first_post' ) ); ?></textarea>
					<p class="description" id="first-post-desc">
						<?php _e( '新站点的首篇文章。' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="first_page"><?php _e( '首个页面' ); ?></label></th>
				<td>
					<textarea name="first_page" id="first_page" aria-describedby="first-page-desc" rows="5" cols="45" class="large-text">
<?php echo esc_textarea( get_site_option( 'first_page' ) ); ?></textarea>
					<p class="description" id="first-page-desc">
						<?php _e( '新站点的首个页面。' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="first_comment"><?php _e( '首条评论' ); ?></label></th>
				<td>
					<textarea name="first_comment" id="first_comment" aria-describedby="first-comment-desc" rows="5" cols="45" class="large-text">
<?php echo esc_textarea( get_site_option( 'first_comment' ) ); ?></textarea>
					<p class="description" id="first-comment-desc">
						<?php _e( '新站点的首条评论。' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="first_comment_author"><?php _e( '首条评论的评论者名称' ); ?></label></th>
				<td>
					<input type="text" size="40" name="first_comment_author" id="first_comment_author" aria-describedby="first-comment-author-desc" value="<?php echo esc_attr( get_site_option( 'first_comment_author' ) ); ?>" />
					<p class="description" id="first-comment-author-desc">
						<?php _e( '新站点首条评论的评论者名称。' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="first_comment_email"><?php _e( '首条评论者的邮箱地址' ); ?></label></th>
				<td>
					<input type="text" size="40" name="first_comment_email" id="first_comment_email" aria-describedby="first-comment-email-desc" value="<?php echo esc_attr( get_site_option( 'first_comment_email' ) ); ?>" />
					<p class="description" id="first-comment-email-desc">
						<?php _e( '新站点上首个评论者的邮箱地址。' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="first_comment_url"><?php _e( '首条评论者的URL' ); ?></label></th>
				<td>
					<input type="text" size="40" name="first_comment_url" id="first_comment_url" aria-describedby="first-comment-url-desc" value="<?php echo esc_attr( get_site_option( 'first_comment_url' ) ); ?>" />
					<p class="description" id="first-comment-url-desc">
						<?php _e( '新站点首条评论者的网址。' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<h2><?php _e( '上传设置' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php _e( '站点上传大小配额' ); ?></th>
				<td>
					<label><input type="checkbox" id="upload_space_check_disabled" name="upload_space_check_disabled" value="0"<?php checked( (bool) get_site_option( 'upload_space_check_disabled' ), false ); ?>/>
						<?php
						printf(
							/* translators: %s: Number of megabytes to limit uploads to. */
							__( '上传文件的总大小不能超过%s MB' ),
							'</label><label><input name="blog_upload_space" type="number" min="0" style="width: 100px" id="blog_upload_space" aria-describedby="blog-upload-space-desc" value="' . esc_attr( get_site_option( 'blog_upload_space', 100 ) ) . '" />'
						);
						?>
					</label><br />
					<p class="screen-reader-text" id="blog-upload-space-desc">
						<?php _e( '大小，兆字节' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="upload_filetypes"><?php _e( '上传文件类型' ); ?></label></th>
				<td>
					<input name="upload_filetypes" type="text" id="upload_filetypes" aria-describedby="upload-filetypes-desc" class="large-text" value="<?php echo esc_attr( get_site_option( 'upload_filetypes', 'jpg jpeg png gif' ) ); ?>" size="45" />
					<p class="description" id="upload-filetypes-desc">
						<?php _e( '允许的文件类型，以空格分隔。' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="fileupload_maxk"><?php _e( '最大上传文件的大小' ); ?></label></th>
				<td>
					<?php
						printf(
							/* translators: %s: File size in kilobytes. */
							__( '%s KB' ),
							'<input name="fileupload_maxk" type="number" min="0" style="width: 100px" id="fileupload_maxk" aria-describedby="fileupload-maxk-desc" value="' . esc_attr( get_site_option( 'fileupload_maxk', 300 ) ) . '" />'
						);
						?>
					<p class="screen-reader-text" id="fileupload-maxk-desc">
						<?php _e( '大小，千字节' ); ?>
					</p>
				</td>
			</tr>
		</table>

		<?php
		$languages    = get_available_languages();
		$translations = gc_get_available_translations();
		if ( ! empty( $languages ) || ! empty( $translations ) ) {
			?>
			<h2><?php _e( '语言设置' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th><label for="GCLANG"><?php _e( '默认语言' ); ?><span class="dashicons dashicons-translation" aria-hidden="true"></span></label></th>
					<td>
						<?php
						$lang = get_site_option( 'GCLANG' );
						if ( ! in_array( $lang, $languages, true ) ) {
							$lang = '';
						}

						gc_dropdown_languages(
							array(
								'name'         => 'GCLANG',
								'id'           => 'GCLANG',
								'selected'     => $lang,
								'languages'    => $languages,
								'translations' => $translations,
								'show_available_translations' => current_user_can( 'install_languages' ) && gc_can_install_language_pack(),
							)
						);
						?>
					</td>
				</tr>
			</table>
			<?php
		}
		?>

		<?php
		$menu_perms = get_site_option( 'menu_items' );
		/**
		 * Filters available network-wide administration menu options.
		 *
		 * Options returned to this filter are output as individual checkboxes that, when selected,
		 * enable site administrator access to the specified administration menu in certain contexts.
		 *
		 * Adding options for specific menus here hinges on the appropriate checks and capabilities
		 * being in place in the site dashboard on the other side. For instance, when the single
		 * default option, 'plugins' is enabled, site administrators are granted access to the Plugins
		 * screen in their individual sites' dashboards.
		 *
		 * @since MU
		 *
		 * @param string[] $admin_menus Associative array of the menu items available.
		 */
		$menu_items = apply_filters( 'mu_menu_items', array( 'plugins' => __( '插件' ) ) );

		if ( $menu_items ) :
			?>
			<h2><?php _e( '菜单设置' ); ?></h2>
			<table id="menu" class="form-table">
				<tr>
					<th scope="row"><?php _e( '启用管理菜单' ); ?></th>
					<td>
						<?php
						echo '<fieldset><legend class="screen-reader-text">' . __( '启用菜单' ) . '</legend>';

						foreach ( (array) $menu_items as $key => $val ) {
							echo "<label><input type='checkbox' name='menu_items[" . $key . "]' value='1'" . ( isset( $menu_perms[ $key ] ) ? checked( $menu_perms[ $key ], '1', false ) : '' ) . ' /> ' . esc_html( $val ) . '</label><br/>';
						}

						echo '</fieldset>';
						?>
					</td>
				</tr>
			</table>
			<?php
		endif;
		?>

		<?php
		/**
		 * Fires at the end of the Network Settings form, before the submit button.
		 *
		 * @since MU
		 */
		do_action( 'gcmu_options' );
		?>
		<?php submit_button(); ?>
	</form>
</div>

<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
