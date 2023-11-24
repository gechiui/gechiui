<?php
/**
 * Filesystem API: Top-level functionality
 *
 * Functions for reading, writing, modifying, and deleting files on the file system.
 * Includes functionality for theme-specific files as well as operations for uploading,
 * archiving, and rendering output when necessary.
 *
 * @package GeChiUI
 * @subpackage Filesystem
 */

/** The descriptions for theme files. */
$gc_file_descriptions = array(
	'functions.php'         => __( '模板函数' ),
	'header.php'            => __( '主题页眉' ),
	'footer.php'            => __( '主题页脚' ),
	'sidebar.php'           => __( '边栏' ),
	'comments.php'          => __( '评论' ),
	'searchform.php'        => __( '搜索框' ),
	'404.php'               => __( '404 模板' ),
	'link.php'              => __( '链接模板' ),
	'theme.json'            => __( '主题样式和区块设置' ),
	// Archives.
	'index.php'             => __( '首页模板' ),
	'archive.php'           => __( '归档' ),
	'author.php'            => __( '作者模板' ),
	'taxonomy.php'          => __( '分类法模板' ),
	'category.php'          => __( '分类模板' ),
	'tag.php'               => __( '标签模板' ),
	'home.php'              => __( '文章页' ),
	'search.php'            => __( '搜索结果' ),
	'date.php'              => __( '日期模板' ),
	// Content.
	'singular.php'          => __( '单数模板' ),
	'single.php'            => __( '文章页面' ),
	'page.php'              => __( '单独页面' ),
	'front-page.php'        => __( '主页' ),
	'privacy-policy.php'    => __( '隐私政策页' ),
	// Attachments.
	'attachment.php'        => __( '附件模板' ),
	'image.php'             => __( '图片附件模板' ),
	'video.php'             => __( '视频附件模板' ),
	'audio.php'             => __( '音频附件模板' ),
	'application.php'       => __( '应用程序附件模板' ),
	// Embeds.
	'embed.php'             => __( '嵌入模板' ),
	'embed-404.php'         => __( '嵌入404模板' ),
	'embed-content.php'     => __( '嵌入内容模板' ),
	'header-embed.php'      => __( '嵌入头部模板' ),
	'footer-embed.php'      => __( '嵌入页脚模板' ),
	// Stylesheets.
	'style.css'             => __( '样式表' ),
	'editor-style.css'      => __( '可视化编辑器样式表' ),
	'editor-style-rtl.css'  => __( '用于可视化编辑器的右至左书写顺序样式表' ),
	'rtl.css'               => __( 'RTL 样式表' ),
	// Other.
	'my-hacks.php'          => __( 'my-hacks.php（老式hack支持）' ),
	'.htaccess'             => __( '.htaccess（重写规则）' ),
	// Deprecated files.
	'gc-layout.css'         => __( '样式表' ),
	'gc-comments.php'       => __( '评论模板' ),
	'gc-comments-popup.php' => __( '评论弹出窗口模板' ),
	'comments-popup.php'    => __( '评论弹出窗口' ),
);

/**
 * Gets the description for standard GeChiUI theme files.
 *
 * @global array $gc_file_descriptions Theme file descriptions.
 * @global array $allowed_files        List of allowed files.
 *
 * @param string $file Filesystem path or filename.
 * @return string Description of file from $gc_file_descriptions or basename of $file if description doesn't exist.
 *                Appends 'Page Template' to basename of $file if the file is a page template.
 */
function get_file_description( $file ) {
	global $gc_file_descriptions, $allowed_files;

	$dirname   = pathinfo( $file, PATHINFO_DIRNAME );
	$file_path = $allowed_files[ $file ];

	if ( isset( $gc_file_descriptions[ basename( $file ) ] ) && '.' === $dirname ) {
		return $gc_file_descriptions[ basename( $file ) ];
	} elseif ( file_exists( $file_path ) && is_file( $file_path ) ) {
		$template_data = implode( '', file( $file_path ) );

		if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) ) {
			/* translators: %s: Template name. */
			return sprintf( __( '%s页面模板' ), _cleanup_header_comment( $name[1] ) );
		}
	}

	return trim( basename( $file ) );
}

/**
 * Gets the absolute filesystem path to the root of the GeChiUI installation.
 *
 * @return string Full filesystem path to the root of the GeChiUI installation.
 */
function get_home_path() {
	$home    = set_url_scheme( get_option( 'home' ), 'http' );
	$siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );

	if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
		$gc_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
		$pos                 = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $gc_path_rel_to_home ) );
		$home_path           = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
		$home_path           = trailingslashit( $home_path );
	} else {
		$home_path = ABSPATH;
	}

	return str_replace( '\\', '/', $home_path );
}

/**
 * Returns a listing of all files in the specified folder and all subdirectories up to 100 levels deep.
 *
 * The depth of the recursiveness can be controlled by the $levels param.
 * Added the `$exclusions` parameter.
 * @since 6.3.0 Added the `$include_hidden` parameter.
 *
 * @param string   $folder         Optional. Full path to folder. Default empty.
 * @param int      $levels         Optional. Levels of folders to follow, Default 100 (PHP Loop limit).
 * @param string[] $exclusions     Optional. List of folders and files to skip.
 * @param bool     $include_hidden Optional. Whether to include details of hidden ("." prefixed) files.
 *                                 Default false.
 * @return string[]|false Array of files on success, false on failure.
 */
function list_files( $folder = '', $levels = 100, $exclusions = array(), $include_hidden = false ) {
	if ( empty( $folder ) ) {
		return false;
	}

	$folder = trailingslashit( $folder );

	if ( ! $levels ) {
		return false;
	}

	$files = array();

	$dir = @opendir( $folder );

	if ( $dir ) {
		while ( ( $file = readdir( $dir ) ) !== false ) {
			// Skip current and parent folder links.
			if ( in_array( $file, array( '.', '..' ), true ) ) {
				continue;
			}

			// Skip hidden and excluded files.
			if ( ( ! $include_hidden && '.' === $file[0] ) || in_array( $file, $exclusions, true ) ) {
				continue;
			}

			if ( is_dir( $folder . $file ) ) {
				$files2 = list_files( $folder . $file, $levels - 1, array(), $include_hidden );
				if ( $files2 ) {
					$files = array_merge( $files, $files2 );
				} else {
					$files[] = $folder . $file . '/';
				}
			} else {
				$files[] = $folder . $file;
			}
		}

		closedir( $dir );
	}

	return $files;
}

/**
 * Gets the list of file extensions that are editable in plugins.
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 * @return string[] Array of editable file extensions.
 */
function gc_get_plugin_file_editable_extensions( $plugin ) {

	$default_types = array(
		'bash',
		'conf',
		'css',
		'diff',
		'htm',
		'html',
		'http',
		'inc',
		'include',
		'js',
		'json',
		'jsx',
		'less',
		'md',
		'patch',
		'php',
		'php3',
		'php4',
		'php5',
		'php7',
		'phps',
		'phtml',
		'sass',
		'scss',
		'sh',
		'sql',
		'svg',
		'text',
		'txt',
		'xml',
		'yaml',
		'yml',
	);

	/**
	 * Filters the list of file types allowed for editing in the plugin file editor.
	 *
	 * @since 4.9.0 Added the `$plugin` parameter.
	 *
	 * @param string[] $default_types An array of editable plugin file extensions.
	 * @param string   $plugin        Path to the plugin file relative to the plugins directory.
	 */
	$file_types = (array) apply_filters( 'editable_extensions', $default_types, $plugin );

	return $file_types;
}

/**
 * Gets the list of file extensions that are editable for a given theme.
 *
 * @param GC_Theme $theme Theme object.
 * @return string[] Array of editable file extensions.
 */
function gc_get_theme_file_editable_extensions( $theme ) {

	$default_types = array(
		'bash',
		'conf',
		'css',
		'diff',
		'htm',
		'html',
		'http',
		'inc',
		'include',
		'js',
		'json',
		'jsx',
		'less',
		'md',
		'patch',
		'php',
		'php3',
		'php4',
		'php5',
		'php7',
		'phps',
		'phtml',
		'sass',
		'scss',
		'sh',
		'sql',
		'svg',
		'text',
		'txt',
		'xml',
		'yaml',
		'yml',
	);

	/**
	 * Filters the list of file types allowed for editing in the theme file editor.
	 *
	 * @since 4.4.0
	 *
	 * @param string[] $default_types An array of editable theme file extensions.
	 * @param GC_Theme $theme         The active theme object.
	 */
	$file_types = apply_filters( 'gc_theme_editor_filetypes', $default_types, $theme );

	// Ensure that default types are still there.
	return array_unique( array_merge( $file_types, $default_types ) );
}

/**
 * Prints file editor templates (for plugins and themes).
 *
 */
function gc_print_file_editor_templates() {
	?>
	<script type="text/html" id="tmpl-gc-file-editor-notice">
		<div class="notice inline notice-{{ data.type || 'info' }} {{ data.alt ? 'notice-alt' : '' }} {{ data.dismissible ? 'is-dismissible' : '' }} {{ data.classes || '' }}">
			<# if ( 'php_error' === data.code ) { #>
				<p>
					<?php
					printf(
						/* translators: 1: Line number, 2: File path. */
						__( '因在%2$s文件的%1$s行有错误，您对PHP代码的修改已被回滚。请修复并重试。' ),
						'{{ data.line }}',
						'{{ data.file }}'
					);
					?>
				</p>
				<pre>{{ data.message }}</pre>
			<# } else if ( 'file_not_writable' === data.code ) { #>
				<p>
					<?php
					printf(
						/* translators: %s: Documentation URL. */
						__( '在您保存修改前，您需要将此文件设置为可写。请参见<a href="%s">更改文件权限文档</a>。' ),
						__( 'https://www.gechiui.com/support/changing-file-permissions/' )
					);
					?>
				</p>
			<# } else { #>
				<p>{{ data.message || data.code }}</p>

				<# if ( 'lint_errors' === data.code ) { #>
					<p>
						<# var elementId = 'el-' + String( Math.random() ); #>
						<input id="{{ elementId }}"  type="checkbox">
						<label for="{{ elementId }}"><?php _e( '仍然更新，即使这可能损坏您的系统？' ); ?></label>
					</p>
				<# } #>
			<# } #>
			<# if ( data.dismissible ) { #>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '不再显示' );
					?>
				</span></button>
			<# } #>
		</div>
	</script>
	<?php
}

/**
 * Attempts to edit a file for a theme or plugin.
 *
 * When editing a PHP file, loopback requests will be made to the admin and the homepage
 * to attempt to see if there is a fatal error introduced. If so, the PHP change will be
 * reverted.
 *
 * @param string[] $args {
 *     Args. Note that all of the arg values are already unslashed. They are, however,
 *     coming straight from `$_POST` and are not validated or sanitized in any way.
 *
 *     @type string $file       Relative path to file.
 *     @type string $plugin     Path to the plugin file relative to the plugins directory.
 *     @type string $theme      Theme being edited.
 *     @type string $newcontent New content for the file.
 *     @type string $nonce      Nonce.
 * }
 * @return true|GC_Error True on success or `GC_Error` on failure.
 */
function gc_edit_theme_plugin_file( $args ) {
	if ( empty( $args['file'] ) ) {
		return new GC_Error( 'missing_file' );
	}

	if ( 0 !== validate_file( $args['file'] ) ) {
		return new GC_Error( 'bad_file' );
	}

	if ( ! isset( $args['newcontent'] ) ) {
		return new GC_Error( 'missing_content' );
	}

	if ( ! isset( $args['nonce'] ) ) {
		return new GC_Error( 'missing_nonce' );
	}

	$file    = $args['file'];
	$content = $args['newcontent'];

	$plugin    = null;
	$theme     = null;
	$real_file = null;

	if ( ! empty( $args['plugin'] ) ) {
		$plugin = $args['plugin'];

		if ( ! current_user_can( 'edit_plugins' ) ) {
			return new GC_Error( 'unauthorized', __( '抱歉，您不能编辑此系统的插件。' ) );
		}

		if ( ! gc_verify_nonce( $args['nonce'], 'edit-plugin_' . $file ) ) {
			return new GC_Error( 'nonce_failure' );
		}

		if ( ! array_key_exists( $plugin, get_plugins() ) ) {
			return new GC_Error( 'invalid_plugin' );
		}

		if ( 0 !== validate_file( $file, get_plugin_files( $plugin ) ) ) {
			return new GC_Error( 'bad_plugin_file_path', __( '抱歉，不能编辑那个文件。' ) );
		}

		$editable_extensions = gc_get_plugin_file_editable_extensions( $plugin );

		$real_file = GC_PLUGIN_DIR . '/' . $file;

		$is_active = in_array(
			$plugin,
			(array) get_option( 'active_plugins', array() ),
			true
		);

	} elseif ( ! empty( $args['theme'] ) ) {
		$stylesheet = $args['theme'];

		if ( 0 !== validate_file( $stylesheet ) ) {
			return new GC_Error( 'bad_theme_path' );
		}

		if ( ! current_user_can( 'edit_themes' ) ) {
			return new GC_Error( 'unauthorized', __( '抱歉，您不能在此系统上编辑模板。' ) );
		}

		$theme = gc_get_theme( $stylesheet );
		if ( ! $theme->exists() ) {
			return new GC_Error( 'non_existent_theme', __( '请求的主题不存在。' ) );
		}

		if ( ! gc_verify_nonce( $args['nonce'], 'edit-theme_' . $stylesheet . '_' . $file ) ) {
			return new GC_Error( 'nonce_failure' );
		}

		if ( $theme->errors() && 'theme_no_stylesheet' === $theme->errors()->get_error_code() ) {
			return new GC_Error(
				'theme_no_stylesheet',
				__( '请求的主题不存在。' ) . ' ' . $theme->errors()->get_error_message()
			);
		}

		$editable_extensions = gc_get_theme_file_editable_extensions( $theme );

		$allowed_files = array();
		foreach ( $editable_extensions as $type ) {
			switch ( $type ) {
				case 'php':
					$allowed_files = array_merge( $allowed_files, $theme->get_files( 'php', -1 ) );
					break;
				case 'css':
					$style_files                = $theme->get_files( 'css', -1 );
					$allowed_files['style.css'] = $style_files['style.css'];
					$allowed_files              = array_merge( $allowed_files, $style_files );
					break;
				default:
					$allowed_files = array_merge( $allowed_files, $theme->get_files( $type, -1 ) );
					break;
			}
		}

		// Compare based on relative paths.
		if ( 0 !== validate_file( $file, array_keys( $allowed_files ) ) ) {
			return new GC_Error( 'disallowed_theme_file', __( '抱歉，不能编辑那个文件。' ) );
		}

		$real_file = $theme->get_stylesheet_directory() . '/' . $file;

		$is_active = ( get_stylesheet() === $stylesheet || get_template() === $stylesheet );

	} else {
		return new GC_Error( 'missing_theme_or_plugin' );
	}

	// Ensure file is real.
	if ( ! is_file( $real_file ) ) {
		return new GC_Error( 'file_does_not_exist', __( '文件不存在。请检查文件名，然后再试。' ) );
	}

	// Ensure file extension is allowed.
	$extension = null;
	if ( preg_match( '/\.([^.]+)$/', $real_file, $matches ) ) {
		$extension = strtolower( $matches[1] );
		if ( ! in_array( $extension, $editable_extensions, true ) ) {
			return new GC_Error( 'illegal_file_type', __( '无法编辑该类型的文件。' ) );
		}
	}

	$previous_content = file_get_contents( $real_file );

	if ( ! is_writable( $real_file ) ) {
		return new GC_Error( 'file_not_writable' );
	}

	$f = fopen( $real_file, 'w+' );

	if ( false === $f ) {
		return new GC_Error( 'file_not_writable' );
	}

	$written = fwrite( $f, $content );
	fclose( $f );

	if ( false === $written ) {
		return new GC_Error( 'unable_to_write', __( '无法写入文件。' ) );
	}

	gc_opcache_invalidate( $real_file, true );

	if ( $is_active && 'php' === $extension ) {

		$scrape_key   = md5( rand() );
		$transient    = 'scrape_key_' . $scrape_key;
		$scrape_nonce = (string) rand();
		// It shouldn't take more than 60 seconds to make the two loopback requests.
		set_transient( $transient, $scrape_nonce, 60 );

		$cookies       = gc_unslash( $_COOKIE );
		$scrape_params = array(
			'gc_scrape_key'   => $scrape_key,
			'gc_scrape_nonce' => $scrape_nonce,
		);
		$headers       = array(
			'Cache-Control' => 'no-cache',
		);

		/** This filter is documented in gc-includes/class-gc-http-streams.php */
		$sslverify = apply_filters( 'https_local_ssl_verify', false );

		// Include Basic auth in loopback requests.
		if ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) {
			$headers['Authorization'] = 'Basic ' . base64_encode( gc_unslash( $_SERVER['PHP_AUTH_USER'] ) . ':' . gc_unslash( $_SERVER['PHP_AUTH_PW'] ) );
		}

		// Make sure PHP process doesn't die before loopback requests complete.
		if ( function_exists( 'set_time_limit' ) ) {
			set_time_limit( 5 * MINUTE_IN_SECONDS );
		}

		// Time to wait for loopback requests to finish.
		$timeout = 100; // 100 seconds.

		$needle_start = "###### gc_scraping_result_start:$scrape_key ######";
		$needle_end   = "###### gc_scraping_result_end:$scrape_key ######";

		// Attempt loopback request to editor to see if user just whitescreened themselves.
		if ( $plugin ) {
			$url = add_query_arg( compact( 'plugin', 'file' ), admin_url( 'plugin-editor.php' ) );
		} elseif ( isset( $stylesheet ) ) {
			$url = add_query_arg(
				array(
					'theme' => $stylesheet,
					'file'  => $file,
				),
				admin_url( 'theme-editor.php' )
			);
		} else {
			$url = admin_url();
		}

		if ( function_exists( 'session_status' ) && PHP_SESSION_ACTIVE === session_status() ) {
			/*
			 * Close any active session to prevent HTTP requests from timing out
			 * when attempting to connect back to the site.
			 */
			session_write_close();
		}

		$url                    = add_query_arg( $scrape_params, $url );
		$r                      = gc_remote_get( $url, compact( 'cookies', 'headers', 'timeout', 'sslverify' ) );
		$body                   = gc_remote_retrieve_body( $r );
		$scrape_result_position = strpos( $body, $needle_start );

		$loopback_request_failure = array(
			'code'    => 'loopback_request_failed',
			'message' => __( '无法与系统通信来检查致命错误，因此PHP修改已被回滚。您需要采用其他方式（如SFTP）上传您修改的PHP文件。' ),
		);
		$json_parse_failure       = array(
			'code' => 'json_parse_error',
		);

		$result = null;

		if ( false === $scrape_result_position ) {
			$result = $loopback_request_failure;
		} else {
			$error_output = substr( $body, $scrape_result_position + strlen( $needle_start ) );
			$error_output = substr( $error_output, 0, strpos( $error_output, $needle_end ) );
			$result       = json_decode( trim( $error_output ), true );
			if ( empty( $result ) ) {
				$result = $json_parse_failure;
			}
		}

		// Try making request to homepage as well to see if visitors have been whitescreened.
		if ( true === $result ) {
			$url                    = home_url( '/' );
			$url                    = add_query_arg( $scrape_params, $url );
			$r                      = gc_remote_get( $url, compact( 'cookies', 'headers', 'timeout', 'sslverify' ) );
			$body                   = gc_remote_retrieve_body( $r );
			$scrape_result_position = strpos( $body, $needle_start );

			if ( false === $scrape_result_position ) {
				$result = $loopback_request_failure;
			} else {
				$error_output = substr( $body, $scrape_result_position + strlen( $needle_start ) );
				$error_output = substr( $error_output, 0, strpos( $error_output, $needle_end ) );
				$result       = json_decode( trim( $error_output ), true );
				if ( empty( $result ) ) {
					$result = $json_parse_failure;
				}
			}
		}

		delete_transient( $transient );

		if ( true !== $result ) {
			// Roll-back file change.
			file_put_contents( $real_file, $previous_content );
			gc_opcache_invalidate( $real_file, true );

			if ( ! isset( $result['message'] ) ) {
				$message = __( '出现了问题。' );
			} else {
				$message = $result['message'];
				unset( $result['message'] );
			}

			return new GC_Error( 'php_error', $message, $result );
		}
	}

	if ( $theme instanceof GC_Theme ) {
		$theme->cache_delete();
	}

	return true;
}


/**
 * Returns a filename of a temporary unique file.
 *
 * Please note that the calling function must unlink() this itself.
 *
 * The filename is based off the passed parameter or defaults to the current unix timestamp,
 * while the directory can either be passed as well, or by leaving it blank, default to a writable
 * temporary directory.
 *
 * @param string $filename Optional. Filename to base the Unique file off. Default empty.
 * @param string $dir      Optional. Directory to store the file in. Default empty.
 * @return string A writable filename.
 */
function gc_tempnam( $filename = '', $dir = '' ) {
	if ( empty( $dir ) ) {
		$dir = get_temp_dir();
	}

	if ( empty( $filename ) || in_array( $filename, array( '.', '/', '\\' ), true ) ) {
		$filename = uniqid();
	}

	// Use the basename of the given file without the extension as the name for the temporary directory.
	$temp_filename = basename( $filename );
	$temp_filename = preg_replace( '|\.[^.]*$|', '', $temp_filename );

	// If the folder is falsey, use its parent directory name instead.
	if ( ! $temp_filename ) {
		return gc_tempnam( dirname( $filename ), $dir );
	}

	// Suffix some random data to avoid filename conflicts.
	$temp_filename .= '-' . gc_generate_password( 6, false );
	$temp_filename .= '.tmp';
	$temp_filename  = gc_unique_filename( $dir, $temp_filename );

	/*
	 * Filesystems typically have a limit of 255 characters for a filename.
	 *
	 * If the generated unique filename exceeds this, truncate the initial
	 * filename and try again.
	 *
	 * As it's possible that the truncated filename may exist, producing a
	 * suffix of "-1" or "-10" which could exceed the limit again, truncate
	 * it to 252 instead.
	 */
	$characters_over_limit = strlen( $temp_filename ) - 252;
	if ( $characters_over_limit > 0 ) {
		$filename = substr( $filename, 0, -$characters_over_limit );
		return gc_tempnam( $filename, $dir );
	}

	$temp_filename = $dir . $temp_filename;

	$fp = @fopen( $temp_filename, 'x' );

	if ( ! $fp && is_writable( $dir ) && file_exists( $temp_filename ) ) {
		return gc_tempnam( $filename, $dir );
	}

	if ( $fp ) {
		fclose( $fp );
	}

	return $temp_filename;
}

/**
 * Makes sure that the file that was requested to be edited is allowed to be edited.
 *
 * Function will die if you are not allowed to edit the file.
 *
 * @param string   $file          File the user is attempting to edit.
 * @param string[] $allowed_files Optional. Array of allowed files to edit.
 *                                `$file` must match an entry exactly.
 * @return string|void Returns the file name on success, dies on failure.
 */
function validate_file_to_edit( $file, $allowed_files = array() ) {
	$code = validate_file( $file, $allowed_files );

	if ( ! $code ) {
		return $file;
	}

	switch ( $code ) {
		case 1:
			gc_die( __( '抱歉，不能编辑那个文件。' ) );

			// case 2 :
			// gc_die( __('对不起，无法使用文件的真实路径调用文件。' ));

		case 3:
			gc_die( __( '抱歉，不能编辑那个文件。' ) );
	}
}

/**
 * Handles PHP uploads in GeChiUI.
 *
 * Sanitizes file names, checks extensions for mime type, and moves the file
 * to the appropriate directory within the uploads directory.
 *
 * @access private
 * @since 4.0.0
 *
 * @see gc_handle_upload_error
 *
 * @param array       $file      {
 *     Reference to a single element from `$_FILES`. Call the function once for each uploaded file.
 *
 *     @type string $name     The original name of the file on the client machine.
 *     @type string $type     The mime type of the file, if the browser provided this information.
 *     @type string $tmp_name The temporary filename of the file in which the uploaded file was stored on the server.
 *     @type int    $size     The size, in bytes, of the uploaded file.
 *     @type int    $error    The error code associated with this file upload.
 * }
 * @param array|false $overrides {
 *     An array of override parameters for this file, or boolean false if none are provided.
 *
 *     @type callable $upload_error_handler     Function to call when there is an error during the upload process.
 *                                              @see gc_handle_upload_error().
 *     @type callable $unique_filename_callback Function to call when determining a unique file name for the file.
 *                                              @see gc_unique_filename().
 *     @type string[] $upload_error_strings     The strings that describe the error indicated in
 *                                              `$_FILES[{form field}]['error']`.
 *     @type bool     $test_form                Whether to test that the `$_POST['action']` parameter is as expected.
 *     @type bool     $test_size                Whether to test that the file size is greater than zero bytes.
 *     @type bool     $test_type                Whether to test that the mime type of the file is as expected.
 *     @type string[] $mimes                    Array of allowed mime types keyed by their file extension regex.
 * }
 * @param string      $time      Time formatted in 'yyyy/mm'.
 * @param string      $action    Expected value for `$_POST['action']`.
 * @return array {
 *     On success, returns an associative array of file attributes.
 *     On failure, returns `$overrides['upload_error_handler']( &$file, $message )`
 *     or `array( 'error' => $message )`.
 *
 *     @type string $file Filename of the newly-uploaded file.
 *     @type string $url  URL of the newly-uploaded file.
 *     @type string $type Mime type of the newly-uploaded file.
 * }
 */
function _gc_handle_upload( &$file, $overrides, $time, $action ) {
	// The default error handler.
	if ( ! function_exists( 'gc_handle_upload_error' ) ) {
		function gc_handle_upload_error( &$file, $message ) {
			return array( 'error' => $message );
		}
	}

	/**
	 * Filters the data for a file before it is uploaded to GeChiUI.
	 *
	 * The dynamic portion of the hook name, `$action`, refers to the post action.
	 *
	 * Possible hook names include:
	 *
	 *  - `gc_handle_sideload_prefilter`
	 *  - `gc_handle_upload_prefilter`
	 *
	 * @since 2.9.0 as 'gc_handle_upload_prefilter'.
	 * @since 4.0.0 Converted to a dynamic hook with `$action`.
	 *
	 * @param array $file {
	 *     Reference to a single element from `$_FILES`.
	 *
	 *     @type string $name     The original name of the file on the client machine.
	 *     @type string $type     The mime type of the file, if the browser provided this information.
	 *     @type string $tmp_name The temporary filename of the file in which the uploaded file was stored on the server.
	 *     @type int    $size     The size, in bytes, of the uploaded file.
	 *     @type int    $error    The error code associated with this file upload.
	 * }
	 */
	$file = apply_filters( "{$action}_prefilter", $file );

	/**
	 * Filters the override parameters for a file before it is uploaded to GeChiUI.
	 *
	 * The dynamic portion of the hook name, `$action`, refers to the post action.
	 *
	 * Possible hook names include:
	 *
	 *  - `gc_handle_sideload_overrides`
	 *  - `gc_handle_upload_overrides`
	 *
	 * @since 5.7.0
	 *
	 * @param array|false $overrides An array of override parameters for this file. Boolean false if none are
	 *                               provided. @see _gc_handle_upload().
	 * @param array       $file      {
	 *     Reference to a single element from `$_FILES`.
	 *
	 *     @type string $name     The original name of the file on the client machine.
	 *     @type string $type     The mime type of the file, if the browser provided this information.
	 *     @type string $tmp_name The temporary filename of the file in which the uploaded file was stored on the server.
	 *     @type int    $size     The size, in bytes, of the uploaded file.
	 *     @type int    $error    The error code associated with this file upload.
	 * }
	 */
	$overrides = apply_filters( "{$action}_overrides", $overrides, $file );

	// You may define your own function and pass the name in $overrides['upload_error_handler'].
	$upload_error_handler = 'gc_handle_upload_error';
	if ( isset( $overrides['upload_error_handler'] ) ) {
		$upload_error_handler = $overrides['upload_error_handler'];
	}

	// You may have had one or more 'gc_handle_upload_prefilter' functions error out the file. Handle that gracefully.
	if ( isset( $file['error'] ) && ! is_numeric( $file['error'] ) && $file['error'] ) {
		return call_user_func_array( $upload_error_handler, array( &$file, $file['error'] ) );
	}

	// Install user overrides. Did we mention that this voids your warranty?

	// You may define your own function and pass the name in $overrides['unique_filename_callback'].
	$unique_filename_callback = null;
	if ( isset( $overrides['unique_filename_callback'] ) ) {
		$unique_filename_callback = $overrides['unique_filename_callback'];
	}

	/*
	 * This may not have originally been intended to be overridable,
	 * but historically has been.
	 */
	if ( isset( $overrides['upload_error_strings'] ) ) {
		$upload_error_strings = $overrides['upload_error_strings'];
	} else {
		// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
		$upload_error_strings = array(
			false,
			sprintf(
				/* translators: 1: upload_max_filesize, 2: php.ini */
				__( '上传的文件大小超过%2$s文件中定义的%1$s值。' ),
				'upload_max_filesize',
				'php.ini'
			),
			sprintf(
				/* translators: %s: MAX_FILE_SIZE */
				__( '上传的文件大小超过HTML表单所定义的%s值。' ),
				'MAX_FILE_SIZE'
			),
			__( '上传的文件不完整。' ),
			__( '没有被上传的文件。' ),
			'',
			__( '缺少临时文件夹。' ),
			__( '写文件到磁盘失败。' ),
			__( '扩展中止了文件的上传。' ),
		);
	}

	// All tests are on by default. Most can be turned off by $overrides[{test_name}] = false;
	$test_form = isset( $overrides['test_form'] ) ? $overrides['test_form'] : true;
	$test_size = isset( $overrides['test_size'] ) ? $overrides['test_size'] : true;

	// If you override this, you must provide $ext and $type!!
	$test_type = isset( $overrides['test_type'] ) ? $overrides['test_type'] : true;
	$mimes     = isset( $overrides['mimes'] ) ? $overrides['mimes'] : null;

	// A correct form post will pass this test.
	if ( $test_form && ( ! isset( $_POST['action'] ) || $_POST['action'] !== $action ) ) {
		return call_user_func_array( $upload_error_handler, array( &$file, __( '表单提交无效。' ) ) );
	}

	// A successful upload will pass this test. It makes no sense to override this one.
	if ( isset( $file['error'] ) && $file['error'] > 0 ) {
		return call_user_func_array( $upload_error_handler, array( &$file, $upload_error_strings[ $file['error'] ] ) );
	}

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	$test_uploaded_file = 'gc_handle_upload' === $action ? is_uploaded_file( $file['tmp_name'] ) : @is_readable( $file['tmp_name'] );
	if ( ! $test_uploaded_file ) {
		return call_user_func_array( $upload_error_handler, array( &$file, __( '指定的文件没有通过上传测试。' ) ) );
	}

	$test_file_size = 'gc_handle_upload' === $action ? $file['size'] : filesize( $file['tmp_name'] );
	// A non-empty file will pass this test.
	if ( $test_size && ! ( $test_file_size > 0 ) ) {
		if ( is_multisite() ) {
			$error_msg = __( '文件为空。请上传有内容的文件。' );
		} else {
			$error_msg = sprintf(
				/* translators: 1: php.ini, 2: post_max_size, 3: upload_max_filesize */
				__( '文件是空的。请上传有内容的文件。这个错误也有可能是因为您的%1$s文件禁止了上传，或%1$s中%2$s的值小于%3$s的值。' ),
				'php.ini',
				'post_max_size',
				'upload_max_filesize'
			);
		}

		return call_user_func_array( $upload_error_handler, array( &$file, $error_msg ) );
	}

	// A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
	if ( $test_type ) {
		$gc_filetype     = gc_check_filetype_and_ext( $file['tmp_name'], $file['name'], $mimes );
		$ext             = empty( $gc_filetype['ext'] ) ? '' : $gc_filetype['ext'];
		$type            = empty( $gc_filetype['type'] ) ? '' : $gc_filetype['type'];
		$proper_filename = empty( $gc_filetype['proper_filename'] ) ? '' : $gc_filetype['proper_filename'];

		// Check to see if gc_check_filetype_and_ext() determined the filename was incorrect.
		if ( $proper_filename ) {
			$file['name'] = $proper_filename;
		}

		if ( ( ! $type || ! $ext ) && ! current_user_can( 'unfiltered_upload' ) ) {
			return call_user_func_array( $upload_error_handler, array( &$file, __( '抱歉，您无权上传此文件类型。' ) ) );
		}

		if ( ! $type ) {
			$type = $file['type'];
		}
	} else {
		$type = '';
	}

	/*
	 * A writable uploads dir will pass this test. Again, there's no point
	 * overriding this one.
	 */
	$uploads = gc_upload_dir( $time );
	if ( ! ( $uploads && false === $uploads['error'] ) ) {
		return call_user_func_array( $upload_error_handler, array( &$file, $uploads['error'] ) );
	}

	$filename = gc_unique_filename( $uploads['path'], $file['name'], $unique_filename_callback );

	// Move the file to the uploads dir.
	$new_file = $uploads['path'] . "/$filename";

	/**
	 * Filters whether to short-circuit moving the uploaded file after passing all checks.
	 *
	 * If a non-null value is returned from the filter, moving the file and any related
	 * error reporting will be completely skipped.
	 *
	 * @since 4.9.0
	 *
	 * @param mixed    $move_new_file If null (default) move the file after the upload.
	 * @param array    $file          {
	 *     Reference to a single element from `$_FILES`.
	 *
	 *     @type string $name     The original name of the file on the client machine.
	 *     @type string $type     The mime type of the file, if the browser provided this information.
	 *     @type string $tmp_name The temporary filename of the file in which the uploaded file was stored on the server.
	 *     @type int    $size     The size, in bytes, of the uploaded file.
	 *     @type int    $error    The error code associated with this file upload.
	 * }
	 * @param string   $new_file      Filename of the newly-uploaded file.
	 * @param string   $type          Mime type of the newly-uploaded file.
	 */
	$move_new_file = apply_filters( 'pre_move_uploaded_file', null, $file, $new_file, $type );

	if ( null === $move_new_file ) {
		if ( 'gc_handle_upload' === $action ) {
			$move_new_file = @move_uploaded_file( $file['tmp_name'], $new_file );
		} else {
			// Use copy and unlink because rename breaks streams.
			// phpcs:ignore GeChiUI.PHP.NoSilencedErrors.Discouraged
			$move_new_file = @copy( $file['tmp_name'], $new_file );
			unlink( $file['tmp_name'] );
		}

		if ( false === $move_new_file ) {
			if ( str_starts_with( $uploads['basedir'], ABSPATH ) ) {
				$error_path = str_replace( ABSPATH, '', $uploads['basedir'] ) . $uploads['subdir'];
			} else {
				$error_path = basename( $uploads['basedir'] ) . $uploads['subdir'];
			}

			return $upload_error_handler(
				$file,
				sprintf(
					/* translators: %s: Destination file path. */
					__( '无法将上传的文件移动至 %s。' ),
					$error_path
				)
			);
		}
	}

	// Set correct file permissions.
	$stat  = stat( dirname( $new_file ) );
	$perms = $stat['mode'] & 0000666;
	chmod( $new_file, $perms );

	// Compute the URL.
	$url = $uploads['url'] . "/$filename";

	if ( is_multisite() ) {
		clean_dirsize_cache( $new_file );
	}

	/**
	 * Filters the data array for the uploaded file.
	 *
	 *
	 * @param array  $upload {
	 *     Array of upload data.
	 *
	 *     @type string $file Filename of the newly-uploaded file.
	 *     @type string $url  URL of the newly-uploaded file.
	 *     @type string $type Mime type of the newly-uploaded file.
	 * }
	 * @param string $context The type of upload action. Values include 'upload' or 'sideload'.
	 */
	return apply_filters(
		'gc_handle_upload',
		array(
			'file' => $new_file,
			'url'  => $url,
			'type' => $type,
		),
		'gc_handle_sideload' === $action ? 'sideload' : 'upload'
	);
}

/**
 * Wrapper for _gc_handle_upload().
 *
 * Passes the {@see 'gc_handle_upload'} action.
 *
 * @see _gc_handle_upload()
 *
 * @param array       $file      Reference to a single element of `$_FILES`.
 *                               Call the function once for each uploaded file.
 *                               See _gc_handle_upload() for accepted values.
 * @param array|false $overrides Optional. An associative array of names => values
 *                               to override default variables. Default false.
 *                               See _gc_handle_upload() for accepted values.
 * @param string      $time      Optional. Time formatted in 'yyyy/mm'. Default null.
 * @return array See _gc_handle_upload() for return value.
 */
function gc_handle_upload( &$file, $overrides = false, $time = null ) {
	/*
	 *  $_POST['action'] must be set and its value must equal $overrides['action']
	 *  or this:
	 */
	$action = 'gc_handle_upload';
	if ( isset( $overrides['action'] ) ) {
		$action = $overrides['action'];
	}

	return _gc_handle_upload( $file, $overrides, $time, $action );
}

/**
 * Wrapper for _gc_handle_upload().
 *
 * Passes the {@see 'gc_handle_sideload'} action.
 *
 * @see _gc_handle_upload()
 *
 * @param array       $file      Reference to a single element of `$_FILES`.
 *                               Call the function once for each uploaded file.
 *                               See _gc_handle_upload() for accepted values.
 * @param array|false $overrides Optional. An associative array of names => values
 *                               to override default variables. Default false.
 *                               See _gc_handle_upload() for accepted values.
 * @param string      $time      Optional. Time formatted in 'yyyy/mm'. Default null.
 * @return array See _gc_handle_upload() for return value.
 */
function gc_handle_sideload( &$file, $overrides = false, $time = null ) {
	/*
	 *  $_POST['action'] must be set and its value must equal $overrides['action']
	 *  or this:
	 */
	$action = 'gc_handle_sideload';
	if ( isset( $overrides['action'] ) ) {
		$action = $overrides['action'];
	}

	return _gc_handle_upload( $file, $overrides, $time, $action );
}

/**
 * Downloads a URL to a local temporary file using the GeChiUI HTTP API.
 *
 * Please note that the calling function must unlink() the file.
 *
 * @since 5.2.0 Signature Verification with SoftFail was added.
 * @since 5.9.0 Support for Content-Disposition filename was added.
 *
 * @param string $url                    The URL of the file to download.
 * @param int    $timeout                The timeout for the request to download the file.
 *                                       Default 300 seconds.
 * @param bool   $signature_verification Whether to perform Signature Verification.
 *                                       Default false.
 * @return string|GC_Error Filename on success, GC_Error on failure.
 */
function download_url( $url, $timeout = 300, $signature_verification = false ) {
	// WARNING: The file is not automatically deleted, the script must unlink() the file.
	if ( ! $url ) {
		return new GC_Error( 'http_no_url', __( '提供的 URL 无效。' ) );
	}

	$url_path     = parse_url( $url, PHP_URL_PATH );
	$url_filename = '';
	if ( is_string( $url_path ) && '' !== $url_path ) {
		$url_filename = basename( $url_path );
	}

	$tmpfname = gc_tempnam( $url_filename );
	if ( ! $tmpfname ) {
		return new GC_Error( 'http_no_file', __( '无法创建临时文件。' ) );
	}

	$response = gc_safe_remote_get(
		$url,
		array(
			'timeout'  => $timeout,
			'stream'   => true,
			'filename' => $tmpfname,
		)
	);

	if ( is_gc_error( $response ) ) {
		unlink( $tmpfname );
		return $response;
	}

	$response_code = gc_remote_retrieve_response_code( $response );

	if ( 200 !== $response_code ) {
		$data = array(
			'code' => $response_code,
		);

		// Retrieve a sample of the response body for debugging purposes.
		$tmpf = fopen( $tmpfname, 'rb' );

		if ( $tmpf ) {
			/**
			 * Filters the maximum error response body size in `download_url()`.
			 *
			 * @since 5.1.0
			 *
			 * @see download_url()
			 *
			 * @param int $size The maximum error response body size. Default 1 KB.
			 */
			$response_size = apply_filters( 'download_url_error_max_body_size', KB_IN_BYTES );

			$data['body'] = fread( $tmpf, $response_size );
			fclose( $tmpf );
		}

		unlink( $tmpfname );

		return new GC_Error( 'http_404', trim( gc_remote_retrieve_response_message( $response ) ), $data );
	}

	$content_disposition = gc_remote_retrieve_header( $response, 'Content-Disposition' );

	if ( $content_disposition ) {
		$content_disposition = strtolower( $content_disposition );

		if ( str_starts_with( $content_disposition, 'attachment; filename=' ) ) {
			$tmpfname_disposition = sanitize_file_name( substr( $content_disposition, 21 ) );
		} else {
			$tmpfname_disposition = '';
		}

		// Potential file name must be valid string.
		if ( $tmpfname_disposition && is_string( $tmpfname_disposition )
			&& ( 0 === validate_file( $tmpfname_disposition ) )
		) {
			$tmpfname_disposition = dirname( $tmpfname ) . '/' . $tmpfname_disposition;

			if ( rename( $tmpfname, $tmpfname_disposition ) ) {
				$tmpfname = $tmpfname_disposition;
			}

			if ( ( $tmpfname !== $tmpfname_disposition ) && file_exists( $tmpfname_disposition ) ) {
				unlink( $tmpfname_disposition );
			}
		}
	}

	$content_md5 = gc_remote_retrieve_header( $response, 'Content-MD5' );

	if ( $content_md5 ) {
		$md5_check = verify_file_md5( $tmpfname, $content_md5 );

		if ( is_gc_error( $md5_check ) ) {
			unlink( $tmpfname );
			return $md5_check;
		}
	}

	// If the caller expects signature verification to occur, check to see if this URL supports it.
	if ( $signature_verification ) {
		/**
		 * Filters the list of hosts which should have Signature Verification attempted on.
		 *
		 * @since 5.2.0
		 *
		 * @param string[] $hostnames List of hostnames.
		 */
		$signed_hostnames = apply_filters( 'gc_signature_hosts', array( 'www.gechiui.com', 'downloads.gechiui.com', 's.w.org' ) );

		$signature_verification = in_array( parse_url( $url, PHP_URL_HOST ), $signed_hostnames, true );
	}

	// Perform signature valiation if supported.
	if ( $signature_verification ) {
		$signature = gc_remote_retrieve_header( $response, 'X-Content-Signature' );

		if ( ! $signature ) {
			/*
			 * Retrieve signatures from a file if the header wasn't included.
			 * www.GeChiUI.com stores signatures at $package_url.sig.
			 */

			$signature_url = false;

			if ( is_string( $url_path ) && ( str_ends_with( $url_path, '.zip' ) || str_ends_with( $url_path, '.tar.gz' ) ) ) {
				$signature_url = str_replace( $url_path, $url_path . '.sig', $url );
			}

			/**
			 * Filters the URL where the signature for a file is located.
			 *
			 * @since 5.2.0
			 *
			 * @param false|string $signature_url The URL where signatures can be found for a file, or false if none are known.
			 * @param string $url                 The URL being verified.
			 */
			$signature_url = apply_filters( 'gc_signature_url', $signature_url, $url );

			if ( $signature_url ) {
				$signature_request = gc_safe_remote_get(
					$signature_url,
					array(
						'limit_response_size' => 10 * KB_IN_BYTES, // 10KB should be large enough for quite a few signatures.
					)
				);

				if ( ! is_gc_error( $signature_request ) && 200 === gc_remote_retrieve_response_code( $signature_request ) ) {
					$signature = explode( "\n", gc_remote_retrieve_body( $signature_request ) );
				}
			}
		}

		// Perform the checks.
		$signature_verification = verify_file_signature( $tmpfname, $signature, $url_filename );
	}

	if ( is_gc_error( $signature_verification ) ) {
		if (
			/**
			 * Filters whether Signature Verification failures should be allowed to soft fail.
			 *
			 * WARNING: This may be removed from a future release.
			 *
			 * @since 5.2.0
			 *
			 * @param bool   $signature_softfail If a softfail is allowed.
			 * @param string $url                The url being accessed.
			 */
			apply_filters( 'gc_signature_softfail', true, $url )
		) {
			$signature_verification->add_data( $tmpfname, 'softfail-filename' );
		} else {
			// Hard-fail.
			unlink( $tmpfname );
		}

		return $signature_verification;
	}

	return $tmpfname;
}

/**
 * Calculates and compares the MD5 of a file to its expected value.
 *
 * @param string $filename     The filename to check the MD5 of.
 * @param string $expected_md5 The expected MD5 of the file, either a base64-encoded raw md5,
 *                             or a hex-encoded md5.
 * @return bool|GC_Error True on success, false when the MD5 format is unknown/unexpected,
 *                       GC_Error on failure.
 */
function verify_file_md5( $filename, $expected_md5 ) {
	if ( 32 === strlen( $expected_md5 ) ) {
		$expected_raw_md5 = pack( 'H*', $expected_md5 );
	} elseif ( 24 === strlen( $expected_md5 ) ) {
		$expected_raw_md5 = base64_decode( $expected_md5 );
	} else {
		return false; // Unknown format.
	}

	$file_md5 = md5_file( $filename, true );

	if ( $file_md5 === $expected_raw_md5 ) {
		return true;
	}

	return new GC_Error(
		'md5_mismatch',
		sprintf(
			/* translators: 1: File checksum, 2: Expected checksum value. */
			__( '文件的检验和（%1$s）与期待的值（%2$s）不符。' ),
			bin2hex( $file_md5 ),
			bin2hex( $expected_raw_md5 )
		)
	);
}

/**
 * Verifies the contents of a file against its ED25519 signature.
 *
 * @since 5.2.0
 *
 * @param string       $filename            The file to validate.
 * @param string|array $signatures          A Signature provided for the file.
 * @param string|false $filename_for_errors Optional. A friendly filename for errors.
 * @return bool|GC_Error True on success, false if verification not attempted,
 *                       or GC_Error describing an error condition.
 */
function verify_file_signature( $filename, $signatures, $filename_for_errors = false ) {
	if ( ! $filename_for_errors ) {
		$filename_for_errors = gc_basename( $filename );
	}

	// Check we can process signatures.
	if ( ! function_exists( 'sodium_crypto_sign_verify_detached' ) || ! in_array( 'sha384', array_map( 'strtolower', hash_algos() ), true ) ) {
		return new GC_Error(
			'signature_verification_unsupported',
			sprintf(
				/* translators: %s: The filename of the package. */
				__( '无法验证%s的真实性，因为签名验证于此系统不可用。' ),
				'<span class="code">' . esc_html( $filename_for_errors ) . '</span>'
			),
			( ! function_exists( 'sodium_crypto_sign_verify_detached' ) ? 'sodium_crypto_sign_verify_detached' : 'sha384' )
		);
	}

	// Check for an edge-case affecting PHP Maths abilities.
	if (
		! extension_loaded( 'sodium' ) &&
		in_array( PHP_VERSION_ID, array( 70200, 70201, 70202 ), true ) &&
		extension_loaded( 'opcache' )
	) {
		/*
		 * Sodium_Compat isn't compatible with PHP 7.2.0~7.2.2 due to a bug in the PHP Opcache extension, bail early as it'll fail.
		 * https://bugs.php.net/bug.php?id=75938
		 */
		return new GC_Error(
			'signature_verification_unsupported',
			sprintf(
				/* translators: %s: The filename of the package. */
				__( '无法验证%s的真实性，因为签名验证于此系统不可用。' ),
				'<span class="code">' . esc_html( $filename_for_errors ) . '</span>'
			),
			array(
				'php'    => PHP_VERSION,
				'sodium' => defined( 'SODIUM_LIBRARY_VERSION' ) ? SODIUM_LIBRARY_VERSION : ( defined( 'ParagonIE_Sodium_Compat::VERSION_STRING' ) ? ParagonIE_Sodium_Compat::VERSION_STRING : false ),
			)
		);
	}

	// Verify runtime speed of Sodium_Compat is acceptable.
	if ( ! extension_loaded( 'sodium' ) && ! ParagonIE_Sodium_Compat::polyfill_is_fast() ) {
		$sodium_compat_is_fast = false;

		// Allow for an old version of Sodium_Compat being loaded before the bundled GeChiUI one.
		if ( method_exists( 'ParagonIE_Sodium_Compat', 'runtime_speed_test' ) ) {
			/*
			 * Run `ParagonIE_Sodium_Compat::runtime_speed_test()` in optimized integer mode,
			 * as that's what GeChiUI utilizes during signing verifications.
			 */
			// phpcs:disable GeChiUI.NamingConventions.ValidVariableName
			$old_fastMult                      = ParagonIE_Sodium_Compat::$fastMult;
			ParagonIE_Sodium_Compat::$fastMult = true;
			$sodium_compat_is_fast             = ParagonIE_Sodium_Compat::runtime_speed_test( 100, 10 );
			ParagonIE_Sodium_Compat::$fastMult = $old_fastMult;
			// phpcs:enable
		}

		/*
		 * This cannot be performed in a reasonable amount of time.
		 * https://github.com/paragonie/sodium_compat#help-sodium_compat-is-slow-how-can-i-make-it-fast
		 */
		if ( ! $sodium_compat_is_fast ) {
			return new GC_Error(
				'signature_verification_unsupported',
				sprintf(
					/* translators: %s: The filename of the package. */
					__( '无法验证%s的真实性，因为签名验证于此系统不可用。' ),
					'<span class="code">' . esc_html( $filename_for_errors ) . '</span>'
				),
				array(
					'php'                => PHP_VERSION,
					'sodium'             => defined( 'SODIUM_LIBRARY_VERSION' ) ? SODIUM_LIBRARY_VERSION : ( defined( 'ParagonIE_Sodium_Compat::VERSION_STRING' ) ? ParagonIE_Sodium_Compat::VERSION_STRING : false ),
					'polyfill_is_fast'   => false,
					'max_execution_time' => ini_get( 'max_execution_time' ),
				)
			);
		}
	}

	if ( ! $signatures ) {
		return new GC_Error(
			'signature_verification_no_signature',
			sprintf(
				/* translators: %s: The filename of the package. */
				__( '无法验证%s的真实性，因为没有找到签名。' ),
				'<span class="code">' . esc_html( $filename_for_errors ) . '</span>'
			),
			array(
				'filename' => $filename_for_errors,
			)
		);
	}

	$trusted_keys = gc_trusted_keys();
	$file_hash    = hash_file( 'sha384', $filename, true );

	mbstring_binary_safe_encoding();

	$skipped_key       = 0;
	$skipped_signature = 0;

	foreach ( (array) $signatures as $signature ) {
		$signature_raw = base64_decode( $signature );

		// Ensure only valid-length signatures are considered.
		if ( SODIUM_CRYPTO_SIGN_BYTES !== strlen( $signature_raw ) ) {
			$skipped_signature++;
			continue;
		}

		foreach ( (array) $trusted_keys as $key ) {
			$key_raw = base64_decode( $key );

			// Only pass valid public keys through.
			if ( SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES !== strlen( $key_raw ) ) {
				$skipped_key++;
				continue;
			}

			if ( sodium_crypto_sign_verify_detached( $signature_raw, $file_hash, $key_raw ) ) {
				reset_mbstring_encoding();
				return true;
			}
		}
	}

	reset_mbstring_encoding();

	return new GC_Error(
		'signature_verification_failed',
		sprintf(
			/* translators: %s: The filename of the package. */
			__( '无法验证%s的真实性。' ),
			'<span class="code">' . esc_html( $filename_for_errors ) . '</span>'
		),
		// Error data helpful for debugging:
		array(
			'filename'    => $filename_for_errors,
			'keys'        => $trusted_keys,
			'signatures'  => $signatures,
			'hash'        => bin2hex( $file_hash ),
			'skipped_key' => $skipped_key,
			'skipped_sig' => $skipped_signature,
			'php'         => PHP_VERSION,
			'sodium'      => defined( 'SODIUM_LIBRARY_VERSION' ) ? SODIUM_LIBRARY_VERSION : ( defined( 'ParagonIE_Sodium_Compat::VERSION_STRING' ) ? ParagonIE_Sodium_Compat::VERSION_STRING : false ),
		)
	);
}

/**
 * Retrieves the list of signing keys trusted by GeChiUI.
 *
 * @since 5.2.0
 *
 * @return string[] Array of base64-encoded signing keys.
 */
function gc_trusted_keys() {
	$trusted_keys = array();

	if ( time() < 1617235200 ) {
		// www.GeChiUI.com Key #1 - This key is only valid before April 1st, 2021.
		$trusted_keys[] = 'fRPyrxb/MvVLbdsYi+OOEv4xc+Eqpsj+kkAS6gNOkI0=';
	}

	// TODO: Add key #2 with longer expiration.

	/**
	 * Filters the valid signing keys used to verify the contents of files.
	 *
	 * @since 5.2.0
	 *
	 * @param string[] $trusted_keys The trusted keys that may sign packages.
	 */
	return apply_filters( 'gc_trusted_keys', $trusted_keys );
}

/**
 * Unzips a specified ZIP file to a location on the filesystem via the GeChiUI
 * Filesystem Abstraction.
 *
 * Assumes that GC_Filesystem() has already been called and set up. Does not extract
 * a root-level __MACOSX directory, if present.
 *
 * Attempts to increase the PHP memory limit to 256M before uncompressing. However,
 * the most memory required shouldn't be much larger than the archive itself.
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 *
 * @param string $file Full path and filename of ZIP archive.
 * @param string $to   Full path on the filesystem to extract archive to.
 * @return true|GC_Error True on success, GC_Error on failure.
 */
function unzip_file( $file, $to ) {
	global $gc_filesystem;

	if ( ! $gc_filesystem || ! is_object( $gc_filesystem ) ) {
		return new GC_Error( 'fs_unavailable', __( '无法访问文件系统。' ) );
	}

	// Unzip can use a lot of memory, but not this much hopefully.
	gc_raise_memory_limit( 'admin' );

	$needed_dirs = array();
	$to          = trailingslashit( $to );

	// Determine any parent directories needed (of the upgrade directory).
	if ( ! $gc_filesystem->is_dir( $to ) ) { // Only do parents if no children exist.
		$path = preg_split( '![/\\\]!', untrailingslashit( $to ) );
		for ( $i = count( $path ); $i >= 0; $i-- ) {
			if ( empty( $path[ $i ] ) ) {
				continue;
			}

			$dir = implode( '/', array_slice( $path, 0, $i + 1 ) );
			if ( preg_match( '!^[a-z]:$!i', $dir ) ) { // Skip it if it looks like a Windows Drive letter.
				continue;
			}

			if ( ! $gc_filesystem->is_dir( $dir ) ) {
				$needed_dirs[] = $dir;
			} else {
				break; // A folder exists, therefore we don't need to check the levels below this.
			}
		}
	}

	/**
	 * Filters whether to use ZipArchive to unzip archives.
	 *
	 *
	 * @param bool $ziparchive Whether to use ZipArchive. Default true.
	 */
	if ( class_exists( 'ZipArchive', false ) && apply_filters( 'unzip_file_use_ziparchive', true ) ) {
		$result = _unzip_file_ziparchive( $file, $to, $needed_dirs );
		if ( true === $result ) {
			return $result;
		} elseif ( is_gc_error( $result ) ) {
			if ( 'incompatible_archive' !== $result->get_error_code() ) {
				return $result;
			}
		}
	}
	// Fall through to PclZip if ZipArchive is not available, or encountered an error opening the file.
	return _unzip_file_pclzip( $file, $to, $needed_dirs );
}

/**
 * Attempts to unzip an archive using the ZipArchive class.
 *
 * This function should not be called directly, use `unzip_file()` instead.
 *
 * Assumes that GC_Filesystem() has already been called and set up.
 *
 * @access private
 *
 * @see unzip_file()
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 *
 * @param string   $file        Full path and filename of ZIP archive.
 * @param string   $to          Full path on the filesystem to extract archive to.
 * @param string[] $needed_dirs A partial list of required folders needed to be created.
 * @return true|GC_Error True on success, GC_Error on failure.
 */
function _unzip_file_ziparchive( $file, $to, $needed_dirs = array() ) {
	global $gc_filesystem;

	$z = new ZipArchive();

	$zopen = $z->open( $file, ZIPARCHIVE::CHECKCONS );

	if ( true !== $zopen ) {
		return new GC_Error( 'incompatible_archive', __( '存档不兼容。' ), array( 'ziparchive_error' => $zopen ) );
	}

	$uncompressed_size = 0;

	for ( $i = 0; $i < $z->numFiles; $i++ ) {
		$info = $z->statIndex( $i );

		if ( ! $info ) {
			return new GC_Error( 'stat_failed_ziparchive', __( '无法从压缩文件中获取文件。' ) );
		}

		if ( str_starts_with( $info['name'], '__MACOSX/' ) ) { // Skip the OS X-created __MACOSX directory.
			continue;
		}

		// Don't extract invalid files:
		if ( 0 !== validate_file( $info['name'] ) ) {
			continue;
		}

		$uncompressed_size += $info['size'];

		$dirname = dirname( $info['name'] );

		if ( str_ends_with( $info['name'], '/' ) ) {
			// Directory.
			$needed_dirs[] = $to . untrailingslashit( $info['name'] );
		} elseif ( '.' !== $dirname ) {
			// Path to a file.
			$needed_dirs[] = $to . untrailingslashit( $dirname );
		}
	}

	/*
	 * disk_free_space() could return false. Assume that any falsey value is an error.
	 * A disk that has zero free bytes has bigger problems.
	 * Require we have enough space to unzip the file and copy its contents, with a 10% buffer.
	 */
	if ( gc_doing_cron() ) {
		$available_space = function_exists( 'disk_free_space' ) ? @disk_free_space( GC_CONTENT_DIR ) : false;

		if ( $available_space && ( $uncompressed_size * 2.1 ) > $available_space ) {
			return new GC_Error(
				'disk_full_unzip_file',
				__( '无法复制文件，您可能用完了磁盘空间。' ),
				compact( 'uncompressed_size', 'available_space' )
			);
		}
	}

	$needed_dirs = array_unique( $needed_dirs );

	foreach ( $needed_dirs as $dir ) {
		// Check the parent folders of the folders all exist within the creation array.
		if ( untrailingslashit( $to ) === $dir ) { // Skip over the working directory, we know this exists (or will exist).
			continue;
		}

		if ( ! str_contains( $dir, $to ) ) { // If the directory is not within the working directory, skip it.
			continue;
		}

		$parent_folder = dirname( $dir );

		while ( ! empty( $parent_folder )
			&& untrailingslashit( $to ) !== $parent_folder
			&& ! in_array( $parent_folder, $needed_dirs, true )
		) {
			$needed_dirs[] = $parent_folder;
			$parent_folder = dirname( $parent_folder );
		}
	}

	asort( $needed_dirs );

	// Create those directories if need be:
	foreach ( $needed_dirs as $_dir ) {
		// Only check to see if the Dir exists upon creation failure. Less I/O this way.
		if ( ! $gc_filesystem->mkdir( $_dir, FS_CHMOD_DIR ) && ! $gc_filesystem->is_dir( $_dir ) ) {
			return new GC_Error( 'mkdir_failed_ziparchive', __( '无法创建目录。' ), $_dir );
		}
	}
	unset( $needed_dirs );

	for ( $i = 0; $i < $z->numFiles; $i++ ) {
		$info = $z->statIndex( $i );

		if ( ! $info ) {
			return new GC_Error( 'stat_failed_ziparchive', __( '无法从压缩文件中获取文件。' ) );
		}

		if ( str_ends_with( $info['name'], '/' ) ) { // Directory.
			continue;
		}

		if ( str_starts_with( $info['name'], '__MACOSX/' ) ) { // Don't extract the OS X-created __MACOSX directory files.
			continue;
		}

		// Don't extract invalid files:
		if ( 0 !== validate_file( $info['name'] ) ) {
			continue;
		}

		$contents = $z->getFromIndex( $i );

		if ( false === $contents ) {
			return new GC_Error( 'extract_failed_ziparchive', __( '无法从压缩文件解压缩。' ), $info['name'] );
		}

		if ( ! $gc_filesystem->put_contents( $to . $info['name'], $contents, FS_CHMOD_FILE ) ) {
			return new GC_Error( 'copy_failed_ziparchive', __( '无法复制文件。' ), $info['name'] );
		}
	}

	$z->close();

	return true;
}

/**
 * Attempts to unzip an archive using the PclZip library.
 *
 * This function should not be called directly, use `unzip_file()` instead.
 *
 * Assumes that GC_Filesystem() has already been called and set up.
 *
 * @access private
 *
 * @see unzip_file()
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 *
 * @param string   $file        Full path and filename of ZIP archive.
 * @param string   $to          Full path on the filesystem to extract archive to.
 * @param string[] $needed_dirs A partial list of required folders needed to be created.
 * @return true|GC_Error True on success, GC_Error on failure.
 */
function _unzip_file_pclzip( $file, $to, $needed_dirs = array() ) {
	global $gc_filesystem;

	mbstring_binary_safe_encoding();

	require_once ABSPATH . 'gc-admin/includes/class-pclzip.php';

	$archive = new PclZip( $file );

	$archive_files = $archive->extract( PCLZIP_OPT_EXTRACT_AS_STRING );

	reset_mbstring_encoding();

	// Is the archive valid?
	if ( ! is_array( $archive_files ) ) {
		return new GC_Error( 'incompatible_archive', __( '存档不兼容。' ), $archive->errorInfo( true ) );
	}

	if ( 0 === count( $archive_files ) ) {
		return new GC_Error( 'empty_archive_pclzip', __( '压缩文件为空。' ) );
	}

	$uncompressed_size = 0;

	// Determine any children directories needed (From within the archive).
	foreach ( $archive_files as $file ) {
		if ( str_starts_with( $file['filename'], '__MACOSX/' ) ) { // Skip the OS X-created __MACOSX directory.
			continue;
		}

		$uncompressed_size += $file['size'];

		$needed_dirs[] = $to . untrailingslashit( $file['folder'] ? $file['filename'] : dirname( $file['filename'] ) );
	}

	/*
	 * disk_free_space() could return false. Assume that any falsey value is an error.
	 * A disk that has zero free bytes has bigger problems.
	 * Require we have enough space to unzip the file and copy its contents, with a 10% buffer.
	 */
	if ( gc_doing_cron() ) {
		$available_space = function_exists( 'disk_free_space' ) ? @disk_free_space( GC_CONTENT_DIR ) : false;

		if ( $available_space && ( $uncompressed_size * 2.1 ) > $available_space ) {
			return new GC_Error(
				'disk_full_unzip_file',
				__( '无法复制文件，您可能用完了磁盘空间。' ),
				compact( 'uncompressed_size', 'available_space' )
			);
		}
	}

	$needed_dirs = array_unique( $needed_dirs );

	foreach ( $needed_dirs as $dir ) {
		// Check the parent folders of the folders all exist within the creation array.
		if ( untrailingslashit( $to ) === $dir ) { // Skip over the working directory, we know this exists (or will exist).
			continue;
		}

		if ( ! str_contains( $dir, $to ) ) { // If the directory is not within the working directory, skip it.
			continue;
		}

		$parent_folder = dirname( $dir );

		while ( ! empty( $parent_folder )
			&& untrailingslashit( $to ) !== $parent_folder
			&& ! in_array( $parent_folder, $needed_dirs, true )
		) {
			$needed_dirs[] = $parent_folder;
			$parent_folder = dirname( $parent_folder );
		}
	}

	asort( $needed_dirs );

	// Create those directories if need be:
	foreach ( $needed_dirs as $_dir ) {
		// Only check to see if the dir exists upon creation failure. Less I/O this way.
		if ( ! $gc_filesystem->mkdir( $_dir, FS_CHMOD_DIR ) && ! $gc_filesystem->is_dir( $_dir ) ) {
			return new GC_Error( 'mkdir_failed_pclzip', __( '无法创建目录。' ), $_dir );
		}
	}
	unset( $needed_dirs );

	// Extract the files from the zip.
	foreach ( $archive_files as $file ) {
		if ( $file['folder'] ) {
			continue;
		}

		if ( str_starts_with( $file['filename'], '__MACOSX/' ) ) { // Don't extract the OS X-created __MACOSX directory files.
			continue;
		}

		// Don't extract invalid files:
		if ( 0 !== validate_file( $file['filename'] ) ) {
			continue;
		}

		if ( ! $gc_filesystem->put_contents( $to . $file['filename'], $file['content'], FS_CHMOD_FILE ) ) {
			return new GC_Error( 'copy_failed_pclzip', __( '无法复制文件。' ), $file['filename'] );
		}
	}

	return true;
}

/**
 * Copies a directory from one location to another via the GeChiUI Filesystem
 * Abstraction.
 *
 * Assumes that GC_Filesystem() has already been called and setup.
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 *
 * @param string   $from      Source directory.
 * @param string   $to        Destination directory.
 * @param string[] $skip_list An array of files/folders to skip copying.
 * @return true|GC_Error True on success, GC_Error on failure.
 */
function copy_dir( $from, $to, $skip_list = array() ) {
	global $gc_filesystem;

	$dirlist = $gc_filesystem->dirlist( $from );

	if ( false === $dirlist ) {
		return new GC_Error( 'dirlist_failed_copy_dir', __( '无法显示目录列表。' ), basename( $from ) );
	}

	$from = trailingslashit( $from );
	$to   = trailingslashit( $to );

	if ( ! $gc_filesystem->exists( $to ) && ! $gc_filesystem->mkdir( $to ) ) {
		return new GC_Error(
			'mkdir_destination_failed_copy_dir',
			__( '无法创建目标目录。' ),
			basename( $to )
		);
	}

	foreach ( (array) $dirlist as $filename => $fileinfo ) {
		if ( in_array( $filename, $skip_list, true ) ) {
			continue;
		}

		if ( 'f' === $fileinfo['type'] ) {
			if ( ! $gc_filesystem->copy( $from . $filename, $to . $filename, true, FS_CHMOD_FILE ) ) {
				// If copy failed, chmod file to 0644 and try again.
				$gc_filesystem->chmod( $to . $filename, FS_CHMOD_FILE );

				if ( ! $gc_filesystem->copy( $from . $filename, $to . $filename, true, FS_CHMOD_FILE ) ) {
					return new GC_Error( 'copy_failed_copy_dir', __( '无法复制文件。' ), $to . $filename );
				}
			}

			gc_opcache_invalidate( $to . $filename );
		} elseif ( 'd' === $fileinfo['type'] ) {
			if ( ! $gc_filesystem->is_dir( $to . $filename ) ) {
				if ( ! $gc_filesystem->mkdir( $to . $filename, FS_CHMOD_DIR ) ) {
					return new GC_Error( 'mkdir_failed_copy_dir', __( '无法创建目录。' ), $to . $filename );
				}
			}

			// Generate the $sub_skip_list for the subdirectory as a sub-set of the existing $skip_list.
			$sub_skip_list = array();

			foreach ( $skip_list as $skip_item ) {
				if ( str_starts_with( $skip_item, $filename . '/' ) ) {
					$sub_skip_list[] = preg_replace( '!^' . preg_quote( $filename, '!' ) . '/!i', '', $skip_item );
				}
			}

			$result = copy_dir( $from . $filename, $to . $filename, $sub_skip_list );

			if ( is_gc_error( $result ) ) {
				return $result;
			}
		}
	}

	return true;
}

/**
 * Moves a directory from one location to another.
 *
 * Recursively invalidates OPcache on success.
 *
 * If the renaming failed, falls back to copy_dir().
 *
 * Assumes that GC_Filesystem() has already been called and setup.
 *
 * This function is not designed to merge directories, copy_dir() should be used instead.
 *
 * @since 6.2.0
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 *
 * @param string $from      Source directory.
 * @param string $to        Destination directory.
 * @param bool   $overwrite Optional. Whether to overwrite the destination directory if it exists.
 *                          Default false.
 * @return true|GC_Error True on success, GC_Error on failure.
 */
function move_dir( $from, $to, $overwrite = false ) {
	global $gc_filesystem;

	if ( trailingslashit( strtolower( $from ) ) === trailingslashit( strtolower( $to ) ) ) {
		return new GC_Error( 'source_destination_same_move_dir', __( '来源和目标相同。' ) );
	}

	if ( $gc_filesystem->exists( $to ) ) {
		if ( ! $overwrite ) {
			return new GC_Error( 'destination_already_exists_move_dir', __( '目标目录已存在。' ), $to );
		} elseif ( ! $gc_filesystem->delete( $to, true ) ) {
			// Can't overwrite if the destination couldn't be deleted.
			return new GC_Error( 'destination_not_deleted_move_dir', __( '目标目录已经存在且无法移除。' ) );
		}
	}

	if ( $gc_filesystem->move( $from, $to ) ) {
		/*
		 * When using an environment with shared folders,
		 * there is a delay in updating the filesystem's cache.
		 *
		 * This is a known issue in environments with a VirtualBox provider.
		 *
		 * A 200ms delay gives time for the filesystem to update its cache,
		 * prevents "Operation not permitted", and "No such file or directory" warnings.
		 *
		 * This delay is used in other projects, including Composer.
		 * @link https://github.com/composer/composer/blob/2.5.1/src/Composer/Util/Platform.php#L228-L233
		 */
		usleep( 200000 );
		gc_opcache_invalidate_directory( $to );

		return true;
	}

	// Fall back to a recursive copy.
	if ( ! $gc_filesystem->is_dir( $to ) ) {
		if ( ! $gc_filesystem->mkdir( $to, FS_CHMOD_DIR ) ) {
			return new GC_Error( 'mkdir_failed_move_dir', __( '无法创建目录。' ), $to );
		}
	}

	$result = copy_dir( $from, $to, array( basename( $to ) ) );

	// Clear the source directory.
	if ( true === $result ) {
		$gc_filesystem->delete( $from, true );
	}

	return $result;
}

/**
 * Initializes and connects the GeChiUI Filesystem Abstraction classes.
 *
 * This function will include the chosen transport and attempt connecting.
 *
 * Plugins may add extra transports, And force GeChiUI to use them by returning
 * the filename via the {@see 'filesystem_method_file'} filter.
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 *
 * @param array|false  $args                         Optional. Connection args, These are passed
 *                                                   directly to the `GC_Filesystem_*()` classes.
 *                                                   Default false.
 * @param string|false $context                      Optional. Context for get_filesystem_method().
 *                                                   Default false.
 * @param bool         $allow_relaxed_file_ownership Optional. Whether to allow Group/World writable.
 *                                                   Default false.
 * @return bool|null True on success, false on failure,
 *                   null if the filesystem method class file does not exist.
 */
function GC_Filesystem( $args = false, $context = false, $allow_relaxed_file_ownership = false ) { // phpcs:ignore GeChiUI.NamingConventions.ValidFunctionName.FunctionNameInvalid
	global $gc_filesystem;

	require_once ABSPATH . 'gc-admin/includes/class-gc-filesystem-base.php';

	$method = get_filesystem_method( $args, $context, $allow_relaxed_file_ownership );

	if ( ! $method ) {
		return false;
	}

	if ( ! class_exists( "GC_Filesystem_$method" ) ) {

		/**
		 * Filters the path for a specific filesystem method class file.
		 *
		 * @since 2.6.0
		 *
		 * @see get_filesystem_method()
		 *
		 * @param string $path   Path to the specific filesystem method class file.
		 * @param string $method The filesystem method to use.
		 */
		$abstraction_file = apply_filters( 'filesystem_method_file', ABSPATH . 'gc-admin/includes/class-gc-filesystem-' . $method . '.php', $method );

		if ( ! file_exists( $abstraction_file ) ) {
			return;
		}

		require_once $abstraction_file;
	}
	$method = "GC_Filesystem_$method";

	$gc_filesystem = new $method( $args );

	/*
	 * Define the timeouts for the connections. Only available after the constructor is called
	 * to allow for per-transport overriding of the default.
	 */
	if ( ! defined( 'FS_CONNECT_TIMEOUT' ) ) {
		define( 'FS_CONNECT_TIMEOUT', 30 ); // 30 seconds.
	}
	if ( ! defined( 'FS_TIMEOUT' ) ) {
		define( 'FS_TIMEOUT', 30 ); // 30 seconds.
	}

	if ( is_gc_error( $gc_filesystem->errors ) && $gc_filesystem->errors->has_errors() ) {
		return false;
	}

	if ( ! $gc_filesystem->connect() ) {
		return false; // There was an error connecting to the server.
	}

	// Set the permission constants if not already set.
	if ( ! defined( 'FS_CHMOD_DIR' ) ) {
		define( 'FS_CHMOD_DIR', ( fileperms( ABSPATH ) & 0777 | 0755 ) );
	}
	if ( ! defined( 'FS_CHMOD_FILE' ) ) {
		define( 'FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
	}

	return true;
}

/**
 * Determines which method to use for reading, writing, modifying, or deleting
 * files on the filesystem.
 *
 * The priority of the transports are: Direct, SSH2, FTP PHP Extension, FTP Sockets
 * (Via Sockets class, or `fsockopen()`). Valid values for these are: 'direct', 'ssh2',
 * 'ftpext' or 'ftpsockets'.
 *
 * The return value can be overridden by defining the `FS_METHOD` constant in `gc-config.php`,
 * or filtering via {@see 'filesystem_method'}.
 *
 * @link https://www.gechiui.com/support/editing-gc-config-php/#gechiui-upgrade-constants
 *
 * Plugins may define a custom transport handler, See GC_Filesystem().
 *
 * @global callable $_gc_filesystem_direct_method
 *
 * @param array  $args                         Optional. Connection details. Default empty array.
 * @param string $context                      Optional. Full path to the directory that is tested
 *                                             for being writable. Default empty.
 * @param bool   $allow_relaxed_file_ownership Optional. Whether to allow Group/World writable.
 *                                             Default false.
 * @return string The transport to use, see description for valid return values.
 */
function get_filesystem_method( $args = array(), $context = '', $allow_relaxed_file_ownership = false ) {
	// Please ensure that this is either 'direct', 'ssh2', 'ftpext', or 'ftpsockets'.
	$method = defined( 'FS_METHOD' ) ? FS_METHOD : false;

	if ( ! $context ) {
		$context = GC_CONTENT_DIR;
	}

	// If the directory doesn't exist (gc-content/languages) then use the parent directory as we'll create it.
	if ( GC_LANG_DIR === $context && ! is_dir( $context ) ) {
		$context = dirname( $context );
	}

	$context = trailingslashit( $context );

	if ( ! $method ) {

		$temp_file_name = $context . 'temp-write-test-' . str_replace( '.', '-', uniqid( '', true ) );
		$temp_handle    = @fopen( $temp_file_name, 'w' );
		if ( $temp_handle ) {

			// Attempt to determine the file owner of the GeChiUI files, and that of newly created files.
			$gc_file_owner   = false;
			$temp_file_owner = false;
			if ( function_exists( 'fileowner' ) ) {
				$gc_file_owner   = @fileowner( __FILE__ );
				$temp_file_owner = @fileowner( $temp_file_name );
			}

			if ( false !== $gc_file_owner && $gc_file_owner === $temp_file_owner ) {
				/*
				 * GeChiUI is creating files as the same owner as the GeChiUI files,
				 * this means it's safe to modify & create new files via PHP.
				 */
				$method                                  = 'direct';
				$GLOBALS['_gc_filesystem_direct_method'] = 'file_owner';
			} elseif ( $allow_relaxed_file_ownership ) {
				/*
				 * The $context directory is writable, and $allow_relaxed_file_ownership is set,
				 * this means we can modify files safely in this directory.
				 * This mode doesn't create new files, only alter existing ones.
				 */
				$method                                  = 'direct';
				$GLOBALS['_gc_filesystem_direct_method'] = 'relaxed_ownership';
			}

			fclose( $temp_handle );
			@unlink( $temp_file_name );
		}
	}

	if ( ! $method && isset( $args['connection_type'] ) && 'ssh' === $args['connection_type'] && extension_loaded( 'ssh2' ) ) {
		$method = 'ssh2';
	}
	if ( ! $method && extension_loaded( 'ftp' ) ) {
		$method = 'ftpext';
	}
	if ( ! $method && ( extension_loaded( 'sockets' ) || function_exists( 'fsockopen' ) ) ) {
		$method = 'ftpsockets'; // Sockets: Socket extension; PHP Mode: FSockopen / fwrite / fread.
	}

	/**
	 * Filters the filesystem method to use.
	 *
	 * @since 2.6.0
	 *
	 * @param string $method                       Filesystem method to return.
	 * @param array  $args                         An array of connection details for the method.
	 * @param string $context                      Full path to the directory that is tested for being writable.
	 * @param bool   $allow_relaxed_file_ownership Whether to allow Group/World writable.
	 */
	return apply_filters( 'filesystem_method', $method, $args, $context, $allow_relaxed_file_ownership );
}

/**
 * Displays a form to the user to request for their FTP/SSH details in order
 * to connect to the filesystem.
 *
 * All chosen/entered details are saved, excluding the password.
 *
 * Hostnames may be in the form of hostname:portnumber (eg: www.gechiui.com:2467)
 * to specify an alternate FTP/SSH port.
 *
 * Plugins may override this form by returning true|false via the {@see 'request_filesystem_credentials'} filter.
 * The `$context` parameter default changed from `false` to an empty string.
 *
 * @global string $pagenow The filename of the current screen.
 *
 * @param string        $form_post                    The URL to post the form to.
 * @param string        $type                         Optional. Chosen type of filesystem. Default empty.
 * @param bool|GC_Error $error                        Optional. Whether the current request has failed
 *                                                    to connect, or an error object. Default false.
 * @param string        $context                      Optional. Full path to the directory that is tested
 *                                                    for being writable. Default empty.
 * @param array         $extra_fields                 Optional. Extra `POST` fields to be checked
 *                                                    for inclusion in the post. Default null.
 * @param bool          $allow_relaxed_file_ownership Optional. Whether to allow Group/World writable.
 *                                                    Default false.
 * @return bool|array True if no filesystem credentials are required,
 *                    false if they are required but have not been provided,
 *                    array of credentials if they are required and have been provided.
 */
function request_filesystem_credentials( $form_post, $type = '', $error = false, $context = '', $extra_fields = null, $allow_relaxed_file_ownership = false ) {
	global $pagenow;

	/**
	 * Filters the filesystem credentials.
	 *
	 * Returning anything other than an empty string will effectively short-circuit
	 * output of the filesystem credentials form, returning that value instead.
	 *
	 * A filter should return true if no filesystem credentials are required, false if they are required but have not been
	 * provided, or an array of credentials if they are required and have been provided.
	 *
	 * @since 4.6.0 The `$context` parameter default changed from `false` to an empty string.
	 *
	 * @param mixed         $credentials                  Credentials to return instead. Default empty string.
	 * @param string        $form_post                    The URL to post the form to.
	 * @param string        $type                         Chosen type of filesystem.
	 * @param bool|GC_Error $error                        Whether the current request has failed to connect,
	 *                                                    or an error object.
	 * @param string        $context                      Full path to the directory that is tested for
	 *                                                    being writable.
	 * @param array         $extra_fields                 Extra POST fields.
	 * @param bool          $allow_relaxed_file_ownership Whether to allow Group/World writable.
	 */
	$req_cred = apply_filters( 'request_filesystem_credentials', '', $form_post, $type, $error, $context, $extra_fields, $allow_relaxed_file_ownership );

	if ( '' !== $req_cred ) {
		return $req_cred;
	}

	if ( empty( $type ) ) {
		$type = get_filesystem_method( array(), $context, $allow_relaxed_file_ownership );
	}

	if ( 'direct' === $type ) {
		return true;
	}

	if ( is_null( $extra_fields ) ) {
		$extra_fields = array( 'version', 'locale' );
	}

	$credentials = get_option(
		'ftp_credentials',
		array(
			'hostname' => '',
			'username' => '',
		)
	);

	$submitted_form = gc_unslash( $_POST );

	// Verify nonce, or unset submitted form field values on failure.
	if ( ! isset( $_POST['_fs_nonce'] ) || ! gc_verify_nonce( $_POST['_fs_nonce'], 'filesystem-credentials' ) ) {
		unset(
			$submitted_form['hostname'],
			$submitted_form['username'],
			$submitted_form['password'],
			$submitted_form['public_key'],
			$submitted_form['private_key'],
			$submitted_form['connection_type']
		);
	}

	$ftp_constants = array(
		'hostname'    => 'FTP_HOST',
		'username'    => 'FTP_USER',
		'password'    => 'FTP_PASS',
		'public_key'  => 'FTP_PUBKEY',
		'private_key' => 'FTP_PRIKEY',
	);

	/*
	 * If defined, set it to that. Else, if POST'd, set it to that. If not, set it to an empty string.
	 * Otherwise, keep it as it previously was (saved details in option).
	 */
	foreach ( $ftp_constants as $key => $constant ) {
		if ( defined( $constant ) ) {
			$credentials[ $key ] = constant( $constant );
		} elseif ( ! empty( $submitted_form[ $key ] ) ) {
			$credentials[ $key ] = $submitted_form[ $key ];
		} elseif ( ! isset( $credentials[ $key ] ) ) {
			$credentials[ $key ] = '';
		}
	}

	// Sanitize the hostname, some people might pass in odd data.
	$credentials['hostname'] = preg_replace( '|\w+://|', '', $credentials['hostname'] ); // Strip any schemes off.

	if ( strpos( $credentials['hostname'], ':' ) ) {
		list( $credentials['hostname'], $credentials['port'] ) = explode( ':', $credentials['hostname'], 2 );
		if ( ! is_numeric( $credentials['port'] ) ) {
			unset( $credentials['port'] );
		}
	} else {
		unset( $credentials['port'] );
	}

	if ( ( defined( 'FTP_SSH' ) && FTP_SSH ) || ( defined( 'FS_METHOD' ) && 'ssh2' === FS_METHOD ) ) {
		$credentials['connection_type'] = 'ssh';
	} elseif ( ( defined( 'FTP_SSL' ) && FTP_SSL ) && 'ftpext' === $type ) { // Only the FTP Extension understands SSL.
		$credentials['connection_type'] = 'ftps';
	} elseif ( ! empty( $submitted_form['connection_type'] ) ) {
		$credentials['connection_type'] = $submitted_form['connection_type'];
	} elseif ( ! isset( $credentials['connection_type'] ) ) { // All else fails (and it's not defaulted to something else saved), default to FTP.
		$credentials['connection_type'] = 'ftp';
	}

	if ( ! $error
		&& ( ! empty( $credentials['hostname'] ) && ! empty( $credentials['username'] ) && ! empty( $credentials['password'] )
			|| 'ssh' === $credentials['connection_type'] && ! empty( $credentials['public_key'] ) && ! empty( $credentials['private_key'] )
		)
	) {
		$stored_credentials = $credentials;

		if ( ! empty( $stored_credentials['port'] ) ) { // Save port as part of hostname to simplify above code.
			$stored_credentials['hostname'] .= ':' . $stored_credentials['port'];
		}

		unset(
			$stored_credentials['password'],
			$stored_credentials['port'],
			$stored_credentials['private_key'],
			$stored_credentials['public_key']
		);

		if ( ! gc_installing() ) {
			update_option( 'ftp_credentials', $stored_credentials );
		}

		return $credentials;
	}

	$hostname        = isset( $credentials['hostname'] ) ? $credentials['hostname'] : '';
	$username        = isset( $credentials['username'] ) ? $credentials['username'] : '';
	$public_key      = isset( $credentials['public_key'] ) ? $credentials['public_key'] : '';
	$private_key     = isset( $credentials['private_key'] ) ? $credentials['private_key'] : '';
	$port            = isset( $credentials['port'] ) ? $credentials['port'] : '';
	$connection_type = isset( $credentials['connection_type'] ) ? $credentials['connection_type'] : '';

	if ( $error ) {
		$error_string = __( '<strong>错误：</strong>无法连接到服务器。请确认设置是否正确。' );
		if ( is_gc_error( $error ) ) {
			$error_string = esc_html( $error->get_error_message() );
		}
		echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
	}

	$types = array();
	if ( extension_loaded( 'ftp' ) || extension_loaded( 'sockets' ) || function_exists( 'fsockopen' ) ) {
		$types['ftp'] = __( 'FTP' );
	}
	if ( extension_loaded( 'ftp' ) ) { // Only this supports FTPS.
		$types['ftps'] = __( 'FTPS（SSL）' );
	}
	if ( extension_loaded( 'ssh2' ) ) {
		$types['ssh'] = __( 'SSH2' );
	}

	/**
	 * Filters the connection types to output to the filesystem credentials form.
	 *
	 * @since 2.9.0
	 * @since 4.6.0 The `$context` parameter default changed from `false` to an empty string.
	 *
	 * @param string[]      $types       Types of connections.
	 * @param array         $credentials Credentials to connect with.
	 * @param string        $type        Chosen filesystem method.
	 * @param bool|GC_Error $error       Whether the current request has failed to connect,
	 *                                   or an error object.
	 * @param string        $context     Full path to the directory that is tested for being writable.
	 */
	$types = apply_filters( 'fs_ftp_connection_types', $types, $credentials, $type, $error, $context );
	?>
<form action="<?php echo esc_url( $form_post ); ?>" method="post">
<div id="request-filesystem-credentials-form" class="request-filesystem-credentials-form">
	<?php
	// Print a H1 heading in the FTP credentials modal dialog, default is a H2.
	$heading_tag = 'h2';
	if ( 'plugins.php' === $pagenow || 'plugin-install.php' === $pagenow ) {
		$heading_tag = 'h1';
	}
	echo "<$heading_tag id='request-filesystem-credentials-title'>" . __( '连接信息' ) . "</$heading_tag>";
	?>
<p id="request-filesystem-credentials-desc">
	<?php
	$label_user = __( '用户名' );
	$label_pass = __( '密码' );
	_e( '要执行请求的操作，GeChiUI需要访问您网页服务器的权限。' );
	echo ' ';
	if ( ( isset( $types['ftp'] ) || isset( $types['ftps'] ) ) ) {
		if ( isset( $types['ssh'] ) ) {
			_e( '请输入您的FTP或SSH登录凭据以继续。' );
			$label_user = __( 'FTP或SSH用户名' );
			$label_pass = __( 'FTP或SSH密码' );
		} else {
			_e( '请输入您的FTP登录凭据以继续。' );
			$label_user = __( 'FTP用户名' );
			$label_pass = __( 'FTP密码' );
		}
		echo ' ';
	}
	_e( '如果您忘记了您的登录凭据（如用户名、密码），请联系您的主机提供商。' );

	$hostname_value = esc_attr( $hostname );
	if ( ! empty( $port ) ) {
		$hostname_value .= ":$port";
	}

	$password_value = '';
	if ( defined( 'FTP_PASS' ) ) {
		$password_value = '*****';
	}
	?>
</p>
<label for="hostname">
	<span class="field-title"><?php _e( '主机名' ); ?></span>
	<input name="hostname" type="text" id="hostname" aria-describedby="request-filesystem-credentials-desc" class="code" placeholder="<?php esc_attr_e( '例子：www.gechiui.com' ); ?>" value="<?php echo $hostname_value; ?>"<?php disabled( defined( 'FTP_HOST' ) ); ?> />
</label>
<div class="ftp-username">
	<label for="username">
		<span class="field-title"><?php echo $label_user; ?></span>
		<input name="username" type="text" id="username" value="<?php echo esc_attr( $username ); ?>"<?php disabled( defined( 'FTP_USER' ) ); ?> />
	</label>
</div>
<div class="ftp-password">
	<label for="password">
		<span class="field-title"><?php echo $label_pass; ?></span>
		<input name="password" type="password" id="password" value="<?php echo $password_value; ?>"<?php disabled( defined( 'FTP_PASS' ) ); ?> spellcheck="false" />
		<?php
		if ( ! defined( 'FTP_PASS' ) ) {
			_e( '密码不会被保存在服务器上。' );
		}
		?>
	</label>
</div>
<fieldset>
<legend><?php _e( '连接类型' ); ?></legend>
	<?php
	$disabled = disabled( ( defined( 'FTP_SSL' ) && FTP_SSL ) || ( defined( 'FTP_SSH' ) && FTP_SSH ), true, false );
	foreach ( $types as $name => $text ) :
		?>
	<label for="<?php echo esc_attr( $name ); ?>">
		<input type="radio" name="connection_type" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $name ); ?>" <?php checked( $name, $connection_type ); ?> <?php echo $disabled; ?> />
		<?php echo $text; ?>
	</label>
		<?php
	endforeach;
	?>
</fieldset>
	<?php
	if ( isset( $types['ssh'] ) ) {
		$hidden_class = '';
		if ( 'ssh' !== $connection_type || empty( $connection_type ) ) {
			$hidden_class = ' class="hidden"';
		}
		?>
<fieldset id="ssh-keys"<?php echo $hidden_class; ?>>
<legend><?php _e( '验证密钥对' ); ?></legend>
<label for="public_key">
	<span class="field-title"><?php _e( '公钥：' ); ?></span>
	<input name="public_key" type="text" id="public_key" aria-describedby="auth-keys-desc" value="<?php echo esc_attr( $public_key ); ?>"<?php disabled( defined( 'FTP_PUBKEY' ) ); ?> />
</label>
<label for="private_key">
	<span class="field-title"><?php _e( '私钥：' ); ?></span>
	<input name="private_key" type="text" id="private_key" value="<?php echo esc_attr( $private_key ); ?>"<?php disabled( defined( 'FTP_PRIKEY' ) ); ?> />
</label>
<p id="auth-keys-desc"><?php _e( '输入服务器上公钥和私钥的位置。如需要密码，请在上方的密码框输入。' ); ?></p>
</fieldset>
		<?php
	}

	foreach ( (array) $extra_fields as $field ) {
		if ( isset( $submitted_form[ $field ] ) ) {
			echo '<input type="hidden" name="' . esc_attr( $field ) . '" value="' . esc_attr( $submitted_form[ $field ] ) . '" />';
		}
	}

	/*
	 * Make sure the `submit_button()` function is available during the REST API call
	 * from GC_Site_Health_Auto_Updates::test_check_gc_filesystem_method().
	 */
	if ( ! function_exists( 'submit_button' ) ) {
		require_once ABSPATH . 'gc-admin/includes/template.php';
	}
	?>
	<p class="request-filesystem-credentials-action-buttons">
		<?php gc_nonce_field( 'filesystem-credentials', '_fs_nonce', false, true ); ?>
		<button class="btn btn-primary btn-tone btn-sm cancel-button" data-js-action="close" type="button"><?php _e( '取消' ); ?></button>
		<?php submit_button( __( '继续' ), '', 'upgrade', false ); ?>
	</p>
</div>
</form>
	<?php
	return false;
}

/**
 * Prints the filesystem credentials modal when needed.
 *
 */
function gc_print_request_filesystem_credentials_modal() {
	$filesystem_method = get_filesystem_method();

	ob_start();
	$filesystem_credentials_are_stored = request_filesystem_credentials( self_admin_url() );
	ob_end_clean();

	$request_filesystem_credentials = ( 'direct' !== $filesystem_method && ! $filesystem_credentials_are_stored );
	if ( ! $request_filesystem_credentials ) {
		return;
	}
	?>
	<div id="request-filesystem-credentials-dialog" class="notification-dialog-wrap request-filesystem-credentials-dialog">
		<div class="notification-dialog-background"></div>
		<div class="notification-dialog" role="dialog" aria-labelledby="request-filesystem-credentials-title" tabindex="0">
			<div class="request-filesystem-credentials-dialog-content">
				<?php request_filesystem_credentials( site_url() ); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Attempts to clear the opcode cache for an individual PHP file.
 *
 * This function can be called safely without having to check the file extension
 * or availability of the OPcache extension.
 *
 * Whether or not invalidation is possible is cached to improve performance.
 *
 * @since 5.5.0
 *
 * @link https://www.php.net/manual/en/function.opcache-invalidate.php
 *
 * @param string $filepath Path to the file, including extension, for which the opcode cache is to be cleared.
 * @param bool   $force    Invalidate even if the modification time is not newer than the file in cache.
 *                         Default false.
 * @return bool True if opcache was invalidated for `$filepath`, or there was nothing to invalidate.
 *              False if opcache invalidation is not available, or is disabled via filter.
 */
function gc_opcache_invalidate( $filepath, $force = false ) {
	static $can_invalidate = null;

	/*
	 * Check to see if GeChiUI is able to run `opcache_invalidate()` or not, and cache the value.
	 *
	 * First, check to see if the function is available to call, then if the host has restricted
	 * the ability to run the function to avoid a PHP warning.
	 *
	 * `opcache.restrict_api` can specify the path for files allowed to call `opcache_invalidate()`.
	 *
	 * If the host has this set, check whether the path in `opcache.restrict_api` matches
	 * the beginning of the path of the origin file.
	 *
	 * `$_SERVER['SCRIPT_FILENAME']` approximates the origin file's path, but `realpath()`
	 * is necessary because `SCRIPT_FILENAME` can be a relative path when run from CLI.
	 *
	 * For more details, see:
	 * - https://www.php.net/manual/en/opcache.configuration.php
	 * - https://www.php.net/manual/en/reserved.variables.server.php
	 * - https://core.trac.gechiui.com/ticket/36455
	 */
	if ( null === $can_invalidate
		&& function_exists( 'opcache_invalidate' )
		&& ( ! ini_get( 'opcache.restrict_api' )
			|| stripos( realpath( $_SERVER['SCRIPT_FILENAME'] ), ini_get( 'opcache.restrict_api' ) ) === 0 )
	) {
		$can_invalidate = true;
	}

	// If invalidation is not available, return early.
	if ( ! $can_invalidate ) {
		return false;
	}

	// Verify that file to be invalidated has a PHP extension.
	if ( '.php' !== strtolower( substr( $filepath, -4 ) ) ) {
		return false;
	}

	/**
	 * Filters whether to invalidate a file from the opcode cache.
	 *
	 * @since 5.5.0
	 *
	 * @param bool   $will_invalidate Whether GeChiUI will invalidate `$filepath`. Default true.
	 * @param string $filepath        The path to the PHP file to invalidate.
	 */
	if ( apply_filters( 'gc_opcache_invalidate_file', true, $filepath ) ) {
		return opcache_invalidate( $filepath, $force );
	}

	return false;
}

/**
 * Attempts to clear the opcode cache for a directory of files.
 *
 * @since 6.2.0
 *
 * @see gc_opcache_invalidate()
 * @link https://www.php.net/manual/en/function.opcache-invalidate.php
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 *
 * @param string $dir The path to the directory for which the opcode cache is to be cleared.
 */
function gc_opcache_invalidate_directory( $dir ) {
	global $gc_filesystem;

	if ( ! is_string( $dir ) || '' === trim( $dir ) ) {
		if ( GC_DEBUG ) {
			$error_message = sprintf(
				/* translators: %s: The function name. */
				__( '%s 必须是一个非空的字符串。' ),
				'<code>gc_opcache_invalidate_directory()</code>'
			);
			// phpcs:ignore GeChiUI.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( $error_message );
		}
		return;
	}

	$dirlist = $gc_filesystem->dirlist( $dir, false, true );

	if ( empty( $dirlist ) ) {
		return;
	}

	/*
	 * Recursively invalidate opcache of files in a directory.
	 *
	 * GC_Filesystem_*::dirlist() returns an array of file and directory information.
	 *
	 * This does not include a path to the file or directory.
	 * To invalidate files within sub-directories, recursion is needed
	 * to prepend an absolute path containing the sub-directory's name.
	 *
	 * @param array  $dirlist Array of file/directory information from GC_Filesystem_Base::dirlist(),
	 *                        with sub-directories represented as nested arrays.
	 * @param string $path    Absolute path to the directory.
	 */
	$invalidate_directory = static function( $dirlist, $path ) use ( &$invalidate_directory ) {
		$path = trailingslashit( $path );

		foreach ( $dirlist as $name => $details ) {
			if ( 'f' === $details['type'] ) {
				gc_opcache_invalidate( $path . $name, true );
			} elseif ( is_array( $details['files'] ) && ! empty( $details['files'] ) ) {
				$invalidate_directory( $details['files'], $path . $name );
			}
		}
	};

	$invalidate_directory( $dirlist, $dir );
}
