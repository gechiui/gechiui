<?php
/**
 * GeChiUI Network Administration API.
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

/**
 * Check for an existing network.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @return string|false Base domain if network exists, otherwise false.
 */
function network_domain_check() {
	global $gcdb;

	$sql = $gcdb->prepare( 'SHOW TABLES LIKE %s', $gcdb->esc_like( $gcdb->site ) );
	if ( $gcdb->get_var( $sql ) ) {
		return $gcdb->get_var( "SELECT domain FROM $gcdb->site ORDER BY id ASC LIMIT 1" );
	}
	return false;
}

/**
 * Allow subdomain installation
 *
 *
 * @return bool Whether subdomain installation is allowed
 */
function allow_subdomain_install() {
	$domain = preg_replace( '|https?://([^/]+)|', '$1', get_option( 'home' ) );
	if ( parse_url( get_option( 'home' ), PHP_URL_PATH ) || 'localhost' === $domain || preg_match( '|^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$|', $domain ) ) {
		return false;
	}

	return true;
}

/**
 * Allow subdirectory installation.
 *
 *
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @return bool Whether subdirectory installation is allowed
 */
function allow_subdirectory_install() {
	global $gcdb;

	/**
	 * Filters whether to enable the subdirectory installation feature in Multisite.
	 *
	 *
	 * @param bool $allow Whether to enable the subdirectory installation feature in Multisite.
	 *                    Default false.
	 */
	if ( apply_filters( 'allow_subdirectory_install', false ) ) {
		return true;
	}

	if ( defined( 'ALLOW_SUBDIRECTORY_INSTALL' ) && ALLOW_SUBDIRECTORY_INSTALL ) {
		return true;
	}

	$post = $gcdb->get_row( "SELECT ID FROM $gcdb->posts WHERE post_date < DATE_SUB(NOW(), INTERVAL 1 MONTH) AND post_status = 'publish'" );
	if ( empty( $post ) ) {
		return true;
	}

	return false;
}

/**
 * Get base domain of network.
 *
 *
 * @return string Base domain.
 */
function get_clean_basedomain() {
	$existing_domain = network_domain_check();
	if ( $existing_domain ) {
		return $existing_domain;
	}
	$domain = preg_replace( '|https?://|', '', get_option( 'siteurl' ) );
	$slash  = strpos( $domain, '/' );
	if ( $slash ) {
		$domain = substr( $domain, 0, $slash );
	}
	return $domain;
}

/**
 * Prints step 1 for Network installation process.
 *
 * @todo Realistically, step 1 should be a welcome screen explaining what a Network is and such.
 *       Navigating to Tools > Network should not be a sudden "Welcome to a new install process!
 *       Fill this out and click here." See also contextual help todo.
 *
 *
 *
 * @global bool $is_apache
 *
 * @param false|GC_Error $errors Optional. Error object. Default false.
 */
function network_step1( $errors = false ) {
	global $is_apache;

	if ( defined( 'DO_NOT_UPGRADE_GLOBAL_TABLES' ) ) {
		echo '<div class="error"><p><strong>' . __( '错误：' ) . '</strong> ' . sprintf(
			/* translators: %s: DO_NOT_UPGRADE_GLOBAL_TABLES */
			__( '创建站点网络时无法定义常数 %s。' ),
			'<code>DO_NOT_UPGRADE_GLOBAL_TABLES</code>'
		) . '</p></div>';
		echo '</div>';
		require_once ABSPATH . 'gc-admin/admin-footer.php';
		die();
	}

	$active_plugins = get_option( 'active_plugins' );
	if ( ! empty( $active_plugins ) ) {
		echo '<div class="notice notice-warning"><p><strong>' . __( '警告：' ) . '</strong> ' . sprintf(
			/* translators: %s: URL to Plugins screen. */
			__( '请在启用站点网络功能前<a href="%s">禁用您的插件</a>。' ),
			admin_url( 'plugins.php?plugin_status=active' )
		) . '</p></div>';
		echo '<p>' . __( '站点网络创建成功后，您可以重新启用插件。' ) . '</p>';
		echo '</div>';
		require_once ABSPATH . 'gc-admin/admin-footer.php';
		die();
	}

	$hostname  = get_clean_basedomain();
	$has_ports = strstr( $hostname, ':' );
	if ( ( false !== $has_ports && ! in_array( $has_ports, array( ':80', ':443' ), true ) ) ) {
		echo '<div class="error"><p><strong>' . __( '错误：' ) . '</strong> ' . __( '您不能使用您的服务器地址来安装站点网络。' ) . '</p></div>';
		echo '<p>' . sprintf(
			/* translators: %s: Port number. */
			__( '您不能使用形如%s的端口号。' ),
			'<code>' . $has_ports . '</code>'
		) . '</p>';
		echo '<a href="' . esc_url( admin_url() ) . '">' . __( '转到“仪表盘”页面' ) . '</a>';
		echo '</div>';
		require_once ABSPATH . 'gc-admin/admin-footer.php';
		die();
	}

	echo '<form method="post">';

	gc_nonce_field( 'install-network-1' );

	$error_codes = array();
	if ( is_gc_error( $errors ) ) {
		echo '<div class="error"><p><strong>' . __( '错误：无法创建站点网络。' ) . '</strong></p>';
		foreach ( $errors->get_error_messages() as $error ) {
			echo "<p>$error</p>";
		}
		echo '</div>';
		$error_codes = $errors->get_error_codes();
	}

	if ( ! empty( $_POST['sitename'] ) && ! in_array( 'empty_sitename', $error_codes, true ) ) {
		$site_name = $_POST['sitename'];
	} else {
		/* translators: %s: Default network title. */
		$site_name = sprintf( __( '%s站点' ), get_option( 'blogname' ) );
	}

	if ( ! empty( $_POST['email'] ) && ! in_array( 'invalid_email', $error_codes, true ) ) {
		$admin_email = $_POST['email'];
	} else {
		$admin_email = get_option( 'admin_email' );
	}
	?>
	<p><?php _e( '欢迎来到站点网络安装向导！' ); ?></p>
	<p><?php _e( '填写以下信息创建 GeChiUI 站点网络。 配置文件将在下一步骤中创建。' ); ?></p>
	<?php

	if ( isset( $_POST['subdomain_install'] ) ) {
		$subdomain_install = (bool) $_POST['subdomain_install'];
	} elseif ( apache_mod_loaded( 'mod_rewrite' ) ) { // Assume nothing.
		$subdomain_install = true;
	} elseif ( ! allow_subdirectory_install() ) {
		$subdomain_install = true;
	} else {
		$subdomain_install = false;
		$got_mod_rewrite   = got_mod_rewrite();
		if ( $got_mod_rewrite ) { // Dangerous assumptions.
			echo '<div class="updated inline"><p><strong>' . __( '注：' ) . '</strong> ';
			printf(
				/* translators: %s: mod_rewrite */
				__( '请确保Apache的%s模块已被安装，在安装过程最后会用到该模块。' ),
				'<code>mod_rewrite</code>'
			);
			echo '</p>';
		} elseif ( $is_apache ) {
			echo '<div class="error inline"><p><strong>' . __( '警告：' ) . '</strong> ';
			printf(
				/* translators: %s: mod_rewrite */
				__( '似乎 Apache 的 %s 模块未被安装。' ),
				'<code>mod_rewrite</code>'
			);
			echo '</p>';
		}

		if ( $got_mod_rewrite || $is_apache ) { // Protect against mod_rewrite mimicry (but ! Apache).
			echo '<p>';
			printf(
				/* translators: 1: mod_rewrite, 2: mod_rewrite documentation URL, 3: Google search for mod_rewrite. */
				__( '如果 %1$s 未启用，请让您的管理员启用该模块，或者查看 <a href="%2$s">Apache 文档</a>或<a href="%3$s">其他地方</a>以获得设置帮助。' ),
				'<code>mod_rewrite</code>',
				'https://httpd.apache.org/docs/mod/mod_rewrite.html',
				'https://www.google.com/search?q=apache+mod_rewrite'
			);
			echo '</p></div>';
		}
	}

	if ( allow_subdomain_install() && allow_subdirectory_install() ) :
		?>
		<h3><?php esc_html_e( '您在站点网络中的站点地址' ); ?></h3>
		<p><?php _e( '请选择您希望您GeChiUI网络中的站点使用子域名还是子目录。' ); ?>
			<strong><?php _e( '您在此后将不能修改此值。' ); ?></strong></p>
		<p><?php _e( '如果您希望使用虚拟主机（子域名）功能，您将需要一个通配DNS记录。' ); ?></p>
		<?php // @todo Link to an MS readme? ?>
		<table class="form-table" role="presentation">
			<tr>
				<th><label><input type="radio" name="subdomain_install" value="1"<?php checked( $subdomain_install ); ?> /> <?php _e( '子域名' ); ?></label></th>
				<td>
				<?php
				printf(
					/* translators: 1: Host name. */
					_x( '如<code>site1.%1$s</code>和<code>site2.%1$s</code>', 'subdomain examples' ),
					$hostname
				);
				?>
				</td>
			</tr>
			<tr>
				<th><label><input type="radio" name="subdomain_install" value="0"<?php checked( ! $subdomain_install ); ?> /> <?php _e( '子目录' ); ?></label></th>
				<td>
				<?php
				printf(
					/* translators: 1: Host name. */
					_x( '如<code>%1$s/site1</code>和<code>%1$s/site2</code>', 'subdirectory examples' ),
					$hostname
				);
				?>
				</td>
			</tr>
		</table>

		<?php
	endif;

	if ( GC_CONTENT_DIR !== ABSPATH . 'gc-content' && ( allow_subdirectory_install() || ! allow_subdomain_install() ) ) {
		echo '<div class="error inline"><p><strong>' . __( '警告：' ) . '</strong> ' . __( '已子目录形式创建的站点网络可能无法与自定义的gc-content目录完全兼容。' ) . '</p></div>';
	}

	$is_www = ( 0 === strpos( $hostname, 'www.' ) );
	if ( $is_www ) :
		?>
		<h3><?php esc_html_e( '服务器地址' ); ?></h3>
		<p>
		<?php
		printf(
			/* translators: 1: Site URL, 2: Host name, 3: www. */
			__( '在启用站点网络功能前，我们建议您将站点域名修改为%1$s。您将来仍可使用带有%3$s前缀的地址（如%2$s）来访问您的站点，但任何链接将不会带有%3$s前缀。' ),
			'<code>' . substr( $hostname, 4 ) . '</code>',
			'<code>' . $hostname . '</code>',
			'<code>www</code>'
		);
		?>
		</p>
		<table class="form-table" role="presentation">
			<tr>
			<th scope='row'><?php esc_html_e( '服务器地址' ); ?></th>
			<td>
				<?php
					printf(
						/* translators: %s: Host name. */
						__( '您网络的互联网地址将会是%s。' ),
						'<code>' . $hostname . '</code>'
					);
				?>
				</td>
			</tr>
		</table>
		<?php endif; ?>

		<h3><?php esc_html_e( '站点网络详情' ); ?></h3>
		<table class="form-table" role="presentation">
		<?php if ( 'localhost' === $hostname ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( '子目录安装' ); ?></th>
				<td>
				<?php
					printf(
						/* translators: 1: localhost, 2: localhost.localdomain */
						__( '因为您在使用 %1$s，您 GeChiUI 网络中的站点必须使用子目录。如果您想使用子域名，请考虑使用 %2$s。' ),
						'<code>localhost</code>',
						'<code>localhost.localdomain</code>'
					);
					// Uh oh:
				if ( ! allow_subdirectory_install() ) {
					echo ' <strong>' . __( '警告：' ) . ' ' . __( '子目录安装中的主站点将需要使用修改过的固定链接结构，这可能会损坏已有链接。' ) . '</strong>';
				}
				?>
				</td>
			</tr>
		<?php elseif ( ! allow_subdomain_install() ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( '子目录安装' ); ?></th>
				<td>
				<?php
					_e( '因为您的 GeChiUI 安装位于子目录中，所以您 GeChiUI 网络中的站点必须使用子目录。' );
					// Uh oh:
				if ( ! allow_subdirectory_install() ) {
					echo ' <strong>' . __( '警告：' ) . ' ' . __( '子目录安装中的主站点将需要使用修改过的固定链接结构，这可能会损坏已有链接。' ) . '</strong>';
				}
				?>
				</td>
			</tr>
		<?php elseif ( ! allow_subdirectory_install() ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( '子域名安装' ); ?></th>
				<td>
				<?php
				_e( '因为您的站点网络并非全新安装，您GeChiUI网络中的站点必须使用子域名。' );
					echo ' <strong>' . __( '子目录安装中的主站点将需要使用修改过的固定链接结构，这可能会损坏已有链接。' ) . '</strong>';
				?>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ( ! $is_www ) : ?>
			<tr>
				<th scope='row'><?php esc_html_e( '服务器地址' ); ?></th>
				<td>
					<?php
					printf(
						/* translators: %s: Host name. */
						__( '您网络的互联网地址将会是%s。' ),
						'<code>' . $hostname . '</code>'
					);
					?>
				</td>
			</tr>
		<?php endif; ?>
			<tr>
				<th scope='row'><label for="sitename"><?php esc_html_e( '网络标题' ); ?></label></th>
				<td>
					<input name='sitename' id='sitename' type='text' size='45' value='<?php echo esc_attr( $site_name ); ?>' />
					<p class="description">
						<?php _e( '您想怎么称呼您的站点网络？' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope='row'><label for="email"><?php esc_html_e( '网络管理员邮箱' ); ?></label></th>
				<td>
					<input name='email' id='email' type='text' size='45' value='<?php echo esc_attr( $admin_email ); ?>' />
					<p class="description">
						<?php _e( '您的邮箱地址。' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php submit_button( __( '安装' ), 'primary', 'submit' ); ?>
	</form>
	<?php
}

/**
 * Prints step 2 for Network installation process.
 *
 *
 *
 * @global gcdb $gcdb     GeChiUI database abstraction object.
 * @global bool $is_nginx Whether the server software is Nginx or something else.
 *
 * @param false|GC_Error $errors Optional. Error object. Default false.
 */
function network_step2( $errors = false ) {
	global $gcdb, $is_nginx;

	$hostname          = get_clean_basedomain();
	$slashed_home      = trailingslashit( get_option( 'home' ) );
	$base              = parse_url( $slashed_home, PHP_URL_PATH );
	$document_root_fix = str_replace( '\\', '/', realpath( $_SERVER['DOCUMENT_ROOT'] ) );
	$abspath_fix       = str_replace( '\\', '/', ABSPATH );
	$home_path         = 0 === strpos( $abspath_fix, $document_root_fix ) ? $document_root_fix . $base : get_home_path();
	$gc_siteurl_subdir = preg_replace( '#^' . preg_quote( $home_path, '#' ) . '#', '', $abspath_fix );
	$rewrite_base      = ! empty( $gc_siteurl_subdir ) ? ltrim( trailingslashit( $gc_siteurl_subdir ), '/' ) : '';

	$location_of_gc_config = $abspath_fix;
	if ( ! file_exists( ABSPATH . 'gc-config.php' ) && file_exists( dirname( ABSPATH ) . '/gc-config.php' ) ) {
		$location_of_gc_config = dirname( $abspath_fix );
	}
	$location_of_gc_config = trailingslashit( $location_of_gc_config );

	// Wildcard DNS message.
	if ( is_gc_error( $errors ) ) {
		echo '<div class="error">' . $errors->get_error_message() . '</div>';
	}

	if ( $_POST ) {
		if ( allow_subdomain_install() ) {
			$subdomain_install = allow_subdirectory_install() ? ! empty( $_POST['subdomain_install'] ) : true;
		} else {
			$subdomain_install = false;
		}
	} else {
		if ( is_multisite() ) {
			$subdomain_install = is_subdomain_install();
			?>
	<p><?php _e( '原始配置步骤作为参考如下所示。' ); ?></p>
			<?php
		} else {
			$subdomain_install = (bool) $gcdb->get_var( "SELECT meta_value FROM $gcdb->sitemeta WHERE site_id = 1 AND meta_key = 'subdomain_install'" );
			?>
	<div class="error"><p><strong><?php _e( '警告：' ); ?></strong> <?php _e( '检测到已存在的 GeChiUI 站点网络。' ); ?></p></div>
	<p><?php _e( '请完成配置步骤。如需创建新的站点网络，您需要清空或删除站点网络的数据库表。' ); ?></p>
			<?php
		}
	}

	$subdir_match          = $subdomain_install ? '' : '([_0-9a-zA-Z-]+/)?';
	$subdir_replacement_01 = $subdomain_install ? '' : '$1';
	$subdir_replacement_12 = $subdomain_install ? '$1' : '$2';

	if ( $_POST || ! is_multisite() ) {
		?>
		<h3><?php esc_html_e( '正在启用站点网络' ); ?></h3>
		<p><?php _e( '完成以下步骤来启用创建站点网络的功能。' ); ?></p>
		<div class="notice notice-warning inline"><p>
		<?php
		if ( file_exists( $home_path . '.htaccess' ) ) {
			echo '<strong>' . __( '注意：' ) . '</strong> ';
			printf(
				/* translators: 1: gc-config.php, 2: .htaccess */
				__( '您应备份现有的 %1$s 和 %2$s 文件。' ),
				'<code>gc-config.php</code>',
				'<code>.htaccess</code>'
			);
		} elseif ( file_exists( $home_path . 'web.config' ) ) {
			echo '<strong>' . __( '注意：' ) . '</strong> ';
			printf(
				/* translators: 1: gc-config.php, 2: web.config */
				__( '您应备份现有的 %1$s 和 %2$s 文件。' ),
				'<code>gc-config.php</code>',
				'<code>web.config</code>'
			);
		} else {
			echo '<strong>' . __( '注意：' ) . '</strong> ';
			printf(
				/* translators: %s: gc-config.php */
				__( '您应备份现有的 %s 文件。' ),
				'<code>gc-config.php</code>'
			);
		}
		?>
		</p></div>
		<?php
	}
	?>
	<ol>
		<li><p>
		<?php
		printf(
			/* translators: 1: gc-config.php, 2: Location of gc-config file, 3: Translated version of "停止编辑，到这里截止自定义值。" */
			__( '将以下内容加入位于%2$s的%1$s文件，加在%3$s这行<strong>上方</strong>：' ),
			'<code>gc-config.php</code>',
			'<code>' . $location_of_gc_config . '</code>',
			/*
			 * translators: This string should only be translated if gc-config-sample.php is localized.
			 * You can check the localized release package or
			 * https://i18n.svn.gechiui.com/<locale code>/branches/<gc version>/dist/gc-config-sample.php
			 */
			'<code>/* ' . __( '停止编辑，到这里截止自定义值。' ) . ' */</code>'
		);
		?>
		</p>
		<textarea class="code" readonly="readonly" cols="100" rows="7">
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', <?php echo $subdomain_install ? 'true' : 'false'; ?> );
define( 'DOMAIN_CURRENT_SITE', '<?php echo $hostname; ?>' );
define( 'PATH_CURRENT_SITE', '<?php echo $base; ?>' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );
</textarea>
		<?php
		$keys_salts = array(
			'AUTH_KEY'         => '',
			'SECURE_AUTH_KEY'  => '',
			'LOGGED_IN_KEY'    => '',
			'NONCE_KEY'        => '',
			'AUTH_SALT'        => '',
			'SECURE_AUTH_SALT' => '',
			'LOGGED_IN_SALT'   => '',
			'NONCE_SALT'       => '',
		);
		foreach ( $keys_salts as $c => $v ) {
			if ( defined( $c ) ) {
				unset( $keys_salts[ $c ] );
			}
		}

		if ( ! empty( $keys_salts ) ) {
			$keys_salts_str = '';
			$from_api       = gc_remote_get( 'https://api.gechiui.com/secret-key/1.1/salt/' );
			if ( is_gc_error( $from_api ) ) {
				foreach ( $keys_salts as $c => $v ) {
					$keys_salts_str .= "\ndefine( '$c', '" . gc_generate_password( 64, true, true ) . "' );";
				}
			} else {
				$from_api = explode( "\n", gc_remote_retrieve_body( $from_api ) );
				foreach ( $keys_salts as $c => $v ) {
					$keys_salts_str .= "\ndefine( '$c', '" . substr( array_shift( $from_api ), 28, 64 ) . "' );";
				}
			}
			$num_keys_salts = count( $keys_salts );
			?>
		<p>
			<?php
			if ( 1 === $num_keys_salts ) {
				printf(
					/* translators: %s: gc-config.php */
					__( '您的 %s 文件中也缺少此唯一身份验证密钥。' ),
					'<code>gc-config.php</code>'
				);
			} else {
				printf(
					/* translators: %s: gc-config.php */
					__( '您的 %s 文件中也缺少这些唯一身份验证密钥。' ),
					'<code>gc-config.php</code>'
				);
			}
			?>
			<?php _e( '为使您的 GeChiUI 安装更加安全，请添加以下行：' ); ?>
		</p>
		<textarea class="code" readonly="readonly" cols="100" rows="<?php echo $num_keys_salts; ?>"><?php echo esc_textarea( $keys_salts_str ); ?></textarea>
			<?php
		}
		?>
		</li>
	<?php
	if ( iis7_supports_permalinks() ) :
		// IIS doesn't support RewriteBase, all your RewriteBase are belong to us.
		$iis_subdir_match       = ltrim( $base, '/' ) . $subdir_match;
		$iis_rewrite_base       = ltrim( $base, '/' ) . $rewrite_base;
		$iis_subdir_replacement = $subdomain_install ? '' : '{R:1}';

		$web_config_file = '<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="GeChiUI Rule 1" stopProcessing="true">
                    <match url="^index\.php$" ignoreCase="false" />
                    <action type="None" />
                </rule>';
		if ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) {
			$web_config_file .= '
                <rule name="GeChiUI Rule for Files" stopProcessing="true">
                    <match url="^' . $iis_subdir_match . 'files/(.+)" ignoreCase="false" />
                    <action type="Rewrite" url="' . $iis_rewrite_base . GCINC . '/ms-files.php?file={R:1}" appendQueryString="false" />
                </rule>';
		}
			$web_config_file .= '
                <rule name="GeChiUI Rule 2" stopProcessing="true">
                    <match url="^' . $iis_subdir_match . 'gc-admin$" ignoreCase="false" />
                    <action type="Redirect" url="' . $iis_subdir_replacement . 'gc-admin/" redirectType="Permanent" />
                </rule>
                <rule name="GeChiUI Rule 3" stopProcessing="true">
                    <match url="^" ignoreCase="false" />
                    <conditions logicalGrouping="MatchAny">
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" />
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" />
                    </conditions>
                    <action type="None" />
                </rule>
                <rule name="GeChiUI Rule 4" stopProcessing="true">
                    <match url="^' . $iis_subdir_match . '(gc-(content|admin|includes).*)" ignoreCase="false" />
                    <action type="Rewrite" url="' . $iis_rewrite_base . '{R:1}" />
                </rule>
                <rule name="GeChiUI Rule 5" stopProcessing="true">
                    <match url="^' . $iis_subdir_match . '([_0-9a-zA-Z-]+/)?(.*\.php)$" ignoreCase="false" />
                    <action type="Rewrite" url="' . $iis_rewrite_base . '{R:2}" />
                </rule>
                <rule name="GeChiUI Rule 6" stopProcessing="true">
                    <match url="." ignoreCase="false" />
                    <action type="Rewrite" url="index.php" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
';

			echo '<li><p>';
			printf(
				/* translators: 1: File name (.htaccess or web.config), 2: File path. */
				__( '将这些加入您位于%2$s的%1$s文件，<strong>替换</strong>其他GeChiUI规则：' ),
				'<code>web.config</code>',
				'<code>' . $home_path . '</code>'
			);
		echo '</p>';
		if ( ! $subdomain_install && GC_CONTENT_DIR !== ABSPATH . 'gc-content' ) {
			echo '<p><strong>' . __( '警告：' ) . ' ' . __( '已子目录形式创建的站点网络可能无法与自定义的gc-content目录完全兼容。' ) . '</strong></p>';
		}
		?>
		<textarea class="code" readonly="readonly" cols="100" rows="20"><?php echo esc_textarea( $web_config_file ); ?></textarea>
		</li>
	</ol>

		<?php
	elseif ( $is_nginx ) : // End iis7_supports_permalinks(). Link to Nginx documentation instead:

		echo '<li><p>';
		printf(
			/* translators: %s: Documentation URL. */
			__( '您的站点网络似乎正在使用Nginx web服务器运行。<a href="%s">进一步了解更多配置信息</a>。' ),
			__( 'https://www.gechiui.com/support/nginx/' )
		);
		echo '</p></li>';

	else : // End $is_nginx. Construct an .htaccess file instead:

		$ms_files_rewriting = '';
		if ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) {
			$ms_files_rewriting  = "\n# uploaded files\nRewriteRule ^";
			$ms_files_rewriting .= $subdir_match . "files/(.+) {$rewrite_base}" . GCINC . "/ms-files.php?file={$subdir_replacement_12} [L]" . "\n";
		}

		$htaccess_file = <<<EOF
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase {$base}
RewriteRule ^index\.php$ - [L]
{$ms_files_rewriting}
# add a trailing slash to /gc-admin
RewriteRule ^{$subdir_match}gc-admin$ {$subdir_replacement_01}gc-admin/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^{$subdir_match}(gc-(content|admin|includes).*) {$rewrite_base}{$subdir_replacement_12} [L]
RewriteRule ^{$subdir_match}(.*\.php)$ {$rewrite_base}$subdir_replacement_12 [L]
RewriteRule . index.php [L]

EOF;

		echo '<li><p>';
		printf(
			/* translators: 1: File name (.htaccess or web.config), 2: File path. */
			__( '将这些加入您位于%2$s的%1$s文件，<strong>替换</strong>其他GeChiUI规则：' ),
			'<code>.htaccess</code>',
			'<code>' . $home_path . '</code>'
		);
		echo '</p>';
		if ( ! $subdomain_install && GC_CONTENT_DIR !== ABSPATH . 'gc-content' ) {
			echo '<p><strong>' . __( '警告：' ) . ' ' . __( '已子目录形式创建的站点网络可能无法与自定义的gc-content目录完全兼容。' ) . '</strong></p>';
		}
		?>
		<textarea class="code" readonly="readonly" cols="100" rows="<?php echo substr_count( $htaccess_file, "\n" ) + 1; ?>"><?php echo esc_textarea( $htaccess_file ); ?></textarea>
		</li>
	</ol>

		<?php
	endif; // End IIS/Nginx/Apache code branches.

	if ( ! is_multisite() ) {
		?>
		<p><?php _e( '完成这些步骤后，您的站点网络即已启用并配置完成。您将需要重新登录。' ); ?> <a href="<?php echo esc_url( gc_login_url() ); ?>"><?php _e( '登录' ); ?></a></p>
		<?php
	}
}
