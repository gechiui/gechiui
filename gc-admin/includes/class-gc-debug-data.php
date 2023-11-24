<?php
/**
 * Class for providing debug data based on a users GeChiUI environment.
 *
 * @package GeChiUI
 * @subpackage Site_Health
 * @since 5.2.0
 */

#[AllowDynamicProperties]
class GC_Debug_Data {
	/**
	 * Calls all core functions to check for updates.
	 *
	 * @since 5.2.0
	 */
	public static function check_for_updates() {
		gc_version_check();
		gc_update_plugins();
		gc_update_themes();
	}

	/**
	 * Static function for generating site debug data when required.
	 *
	 * @since 5.2.0
	 * @since 5.3.0 Added database charset, database collation,
	 *              and timezone information.
	 * @since 5.5.0 Added pretty permalinks support information.
	 *
	 * @throws ImagickException
	 * @global gcdb  $gcdb               GeChiUI database abstraction object.
	 * @global array $_gc_theme_features
	 *
	 * @return array The debug data for the site.
	 */
	public static function debug_data() {
		global $gcdb, $_gc_theme_features;

		// Save few function calls.
		$upload_dir             = gc_upload_dir();
		$permalink_structure    = get_option( 'permalink_structure' );
		$is_ssl                 = is_ssl();
		$is_multisite           = is_multisite();
		$users_can_register     = get_option( 'users_can_register' );
		$blog_public            = get_option( 'blog_public' );
		$default_comment_status = get_option( 'default_comment_status' );
		$environment_type       = gc_get_environment_type();
		$core_version           = get_bloginfo( 'version' );
		$core_updates           = get_core_updates();
		$core_update_needed     = '';

		if ( is_array( $core_updates ) ) {
			foreach ( $core_updates as $core => $update ) {
				if ( 'upgrade' === $update->response ) {
					/* translators: %s: Latest GeChiUI version number. */
					$core_update_needed = ' ' . sprintf( __( '（最新版本：%s）' ), $update->version );
				} else {
					$core_update_needed = '';
				}
			}
		}

		// Set up the array that holds all debug information.
		$info = array();

		$info['gc-core'] = array(
			'label'  => __( 'GeChiUI' ),
			'fields' => array(
				'version'                => array(
					'label' => __( '版本' ),
					'value' => $core_version . $core_update_needed,
					'debug' => $core_version,
				),
				'site_language'          => array(
					'label' => __( '系统语言' ),
					'value' => get_locale(),
				),
				'user_language'          => array(
					'label' => __( '用户语言' ),
					'value' => get_user_locale(),
				),
				'timezone'               => array(
					'label' => __( '时区' ),
					'value' => gc_timezone_string(),
				),
				'home_url'               => array(
					'label'   => __( '主页URL' ),
					'value'   => get_bloginfo( 'url' ),
					'private' => true,
				),
				'site_url'               => array(
					'label'   => __( '系统URL' ),
					'value'   => get_bloginfo( 'gcurl' ),
					'private' => true,
				),
				'permalink'              => array(
					'label' => __( '固定链接结构' ),
					'value' => $permalink_structure ? $permalink_structure : __( '未设置固定链接结构' ),
					'debug' => $permalink_structure,
				),
				'https_status'           => array(
					'label' => __( '此系统是否使用HTTPS？' ),
					'value' => $is_ssl ? __( '是' ) : __( '否' ),
					'debug' => $is_ssl,
				),
				'multisite'              => array(
					'label' => __( '这是多系统吗？' ),
					'value' => $is_multisite ? __( '是' ) : __( '否' ),
					'debug' => $is_multisite,
				),
				'user_registration'      => array(
					'label' => __( '此系统是否任何人都能注册？' ),
					'value' => $users_can_register ? __( '是' ) : __( '否' ),
					'debug' => $users_can_register,
				),
				'blog_public'            => array(
					'label' => __( '此系统是否不建议搜索引擎进行索引？' ),
					'value' => $blog_public ? __( '否' ) : __( '是' ),
					'debug' => $blog_public,
				),
				'default_comment_status' => array(
					'label' => __( '默认评论状态' ),
					'value' => 'open' === $default_comment_status ? _x( '开放', 'comment status' ) : _x( '关闭', 'comment status' ),
					'debug' => $default_comment_status,
				),
				'environment_type'       => array(
					'label' => __( '环境类型' ),
					'value' => $environment_type,
					'debug' => $environment_type,
				),
			),
		);

		if ( ! $is_multisite ) {
			$info['gc-paths-sizes'] = array(
				'label'  => __( '目录和大小' ),
				'fields' => array(),
			);
		}

		$info['gc-dropins'] = array(
			'label'       => __( '强化扩展' ),
			'show_count'  => true,
			'description' => sprintf(
				/* translators: %s: gc-content directory name. */
				__( '强化扩展是能够替代或增强GeChiUI无法通过传统插件实现的功能的在%s目录中的单个文件。' ),
				'<code>' . str_replace( ABSPATH, '', GC_CONTENT_DIR ) . '</code>'
			),
			'fields'      => array(),
		);

		$info['gc-active-theme'] = array(
			'label'  => __( '已启用的主题' ),
			'fields' => array(),
		);

		$info['gc-parent-theme'] = array(
			'label'  => __( '父主题' ),
			'fields' => array(),
		);

		$info['gc-themes-inactive'] = array(
			'label'      => __( '未启用的主题' ),
			'show_count' => true,
			'fields'     => array(),
		);

		$info['gc-mu-plugins'] = array(
			'label'      => __( '强制使用的插件' ),
			'show_count' => true,
			'fields'     => array(),
		);

		$info['gc-plugins-active'] = array(
			'label'      => __( '已启用的插件' ),
			'show_count' => true,
			'fields'     => array(),
		);

		$info['gc-plugins-inactive'] = array(
			'label'      => __( '未启用的插件' ),
			'show_count' => true,
			'fields'     => array(),
		);

		$info['gc-media'] = array(
			'label'  => __( '媒体处理' ),
			'fields' => array(),
		);

		$info['gc-server'] = array(
			'label'       => __( '服务器' ),
			'description' => __( '这些选项与您的服务器设置相关。如果您需要修改，您可能需要获取您的主机提供商的帮助。' ),
			'fields'      => array(),
		);

		$info['gc-database'] = array(
			'label'  => __( '数据库' ),
			'fields' => array(),
		);

		// Check if GC_DEBUG_LOG is set.
		$gc_debug_log_value = __( '禁用' );

		if ( is_string( GC_DEBUG_LOG ) ) {
			$gc_debug_log_value = GC_DEBUG_LOG;
		} elseif ( GC_DEBUG_LOG ) {
			$gc_debug_log_value = __( '启用' );
		}

		// Check CONCATENATE_SCRIPTS.
		if ( defined( 'CONCATENATE_SCRIPTS' ) ) {
			$concatenate_scripts       = CONCATENATE_SCRIPTS ? __( '启用' ) : __( '禁用' );
			$concatenate_scripts_debug = CONCATENATE_SCRIPTS ? 'true' : 'false';
		} else {
			$concatenate_scripts       = __( '未定义' );
			$concatenate_scripts_debug = 'undefined';
		}

		// Check COMPRESS_SCRIPTS.
		if ( defined( 'COMPRESS_SCRIPTS' ) ) {
			$compress_scripts       = COMPRESS_SCRIPTS ? __( '启用' ) : __( '禁用' );
			$compress_scripts_debug = COMPRESS_SCRIPTS ? 'true' : 'false';
		} else {
			$compress_scripts       = __( '未定义' );
			$compress_scripts_debug = 'undefined';
		}

		// Check COMPRESS_CSS.
		if ( defined( 'COMPRESS_CSS' ) ) {
			$compress_css       = COMPRESS_CSS ? __( '启用' ) : __( '禁用' );
			$compress_css_debug = COMPRESS_CSS ? 'true' : 'false';
		} else {
			$compress_css       = __( '未定义' );
			$compress_css_debug = 'undefined';
		}

		// Check GC_ENVIRONMENT_TYPE.
		if ( defined( 'GC_ENVIRONMENT_TYPE' ) && GC_ENVIRONMENT_TYPE ) {
			$gc_environment_type = GC_ENVIRONMENT_TYPE;
		} else {
			$gc_environment_type = __( '未定义' );
		}

		$info['gc-constants'] = array(
			'label'       => __( 'GeChiUI常量' ),
			'description' => __( '这些设置修改GeChiUI的每个部分将在何处如何加载。' ),
			'fields'      => array(
				'ABSPATH'             => array(
					'label'   => 'ABSPATH',
					'value'   => ABSPATH,
					'private' => true,
				),
				'GC_HOME'             => array(
					'label' => 'GC_HOME',
					'value' => ( defined( 'GC_HOME' ) ? GC_HOME : __( '未定义' ) ),
					'debug' => ( defined( 'GC_HOME' ) ? GC_HOME : 'undefined' ),
				),
				'GC_SITEURL'          => array(
					'label' => 'GC_SITEURL',
					'value' => ( defined( 'GC_SITEURL' ) ? GC_SITEURL : __( '未定义' ) ),
					'debug' => ( defined( 'GC_SITEURL' ) ? GC_SITEURL : 'undefined' ),
				),
				'GC_CONTENT_DIR'      => array(
					'label' => 'GC_CONTENT_DIR',
					'value' => GC_CONTENT_DIR,
				),
				'GC_PLUGIN_DIR'       => array(
					'label' => 'GC_PLUGIN_DIR',
					'value' => GC_PLUGIN_DIR,
				),
				'GC_MEMORY_LIMIT'     => array(
					'label' => 'GC_MEMORY_LIMIT',
					'value' => GC_MEMORY_LIMIT,
				),
				'GC_MAX_MEMORY_LIMIT' => array(
					'label' => 'GC_MAX_MEMORY_LIMIT',
					'value' => GC_MAX_MEMORY_LIMIT,
				),
				'GC_DEBUG'            => array(
					'label' => 'GC_DEBUG',
					'value' => GC_DEBUG ? __( '启用' ) : __( '禁用' ),
					'debug' => GC_DEBUG,
				),
				'GC_DEBUG_DISPLAY'    => array(
					'label' => 'GC_DEBUG_DISPLAY',
					'value' => GC_DEBUG_DISPLAY ? __( '启用' ) : __( '禁用' ),
					'debug' => GC_DEBUG_DISPLAY,
				),
				'GC_DEBUG_LOG'        => array(
					'label' => 'GC_DEBUG_LOG',
					'value' => $gc_debug_log_value,
					'debug' => GC_DEBUG_LOG,
				),
				'SCRIPT_DEBUG'        => array(
					'label' => 'SCRIPT_DEBUG',
					'value' => SCRIPT_DEBUG ? __( '启用' ) : __( '禁用' ),
					'debug' => SCRIPT_DEBUG,
				),
				'GC_CACHE'            => array(
					'label' => 'GC_CACHE',
					'value' => GC_CACHE ? __( '启用' ) : __( '禁用' ),
					'debug' => GC_CACHE,
				),
				'CONCATENATE_SCRIPTS' => array(
					'label' => 'CONCATENATE_SCRIPTS',
					'value' => $concatenate_scripts,
					'debug' => $concatenate_scripts_debug,
				),
				'COMPRESS_SCRIPTS'    => array(
					'label' => 'COMPRESS_SCRIPTS',
					'value' => $compress_scripts,
					'debug' => $compress_scripts_debug,
				),
				'COMPRESS_CSS'        => array(
					'label' => 'COMPRESS_CSS',
					'value' => $compress_css,
					'debug' => $compress_css_debug,
				),
				'GC_ENVIRONMENT_TYPE' => array(
					'label' => 'GC_ENVIRONMENT_TYPE',
					'value' => $gc_environment_type,
					'debug' => $gc_environment_type,
				),
				'GC_DEVELOPMENT_MODE' => array(
					'label' => 'GC_DEVELOPMENT_MODE',
					'value' => GC_DEVELOPMENT_MODE ? GC_DEVELOPMENT_MODE : __( '禁用' ),
					'debug' => GC_DEVELOPMENT_MODE,
				),
				'DB_CHARSET'          => array(
					'label' => 'DB_CHARSET',
					'value' => ( defined( 'DB_CHARSET' ) ? DB_CHARSET : __( '未定义' ) ),
					'debug' => ( defined( 'DB_CHARSET' ) ? DB_CHARSET : 'undefined' ),
				),
				'DB_COLLATE'          => array(
					'label' => 'DB_COLLATE',
					'value' => ( defined( 'DB_COLLATE' ) ? DB_COLLATE : __( '未定义' ) ),
					'debug' => ( defined( 'DB_COLLATE' ) ? DB_COLLATE : 'undefined' ),
				),
			),
		);

		$is_writable_abspath            = gc_is_writable( ABSPATH );
		$is_writable_gc_content_dir     = gc_is_writable( GC_CONTENT_DIR );
		$is_writable_upload_dir         = gc_is_writable( $upload_dir['basedir'] );
		$is_writable_gc_plugin_dir      = gc_is_writable( GC_PLUGIN_DIR );
		$is_writable_template_directory = gc_is_writable( get_theme_root( get_template() ) );

		$info['gc-filesystem'] = array(
			'label'       => __( '文件系统权限' ),
			'description' => __( '显示GeChiUI是否能够写入必需的目录。' ),
			'fields'      => array(
				'gechiui'  => array(
					'label' => __( '主GeChiUI目录' ),
					'value' => ( $is_writable_abspath ? __( '可写' ) : __( '不可写' ) ),
					'debug' => ( $is_writable_abspath ? 'writable' : 'not writable' ),
				),
				'gc-content' => array(
					'label' => __( 'gc-content目录' ),
					'value' => ( $is_writable_gc_content_dir ? __( '可写' ) : __( '不可写' ) ),
					'debug' => ( $is_writable_gc_content_dir ? 'writable' : 'not writable' ),
				),
				'uploads'    => array(
					'label' => __( 'uploads目录' ),
					'value' => ( $is_writable_upload_dir ? __( '可写' ) : __( '不可写' ) ),
					'debug' => ( $is_writable_upload_dir ? 'writable' : 'not writable' ),
				),
				'plugins'    => array(
					'label' => __( 'plugins目录' ),
					'value' => ( $is_writable_gc_plugin_dir ? __( '可写' ) : __( '不可写' ) ),
					'debug' => ( $is_writable_gc_plugin_dir ? 'writable' : 'not writable' ),
				),
				'themes'     => array(
					'label' => __( 'themes目录' ),
					'value' => ( $is_writable_template_directory ? __( '可写' ) : __( '不可写' ) ),
					'debug' => ( $is_writable_template_directory ? 'writable' : 'not writable' ),
				),
			),
		);

		// Conditionally add debug information for multisite setups.
		if ( is_multisite() ) {
			$network_query = new GC_Network_Query();
			$network_ids   = $network_query->query(
				array(
					'fields'        => 'ids',
					'number'        => 100,
					'no_found_rows' => false,
				)
			);

			$site_count = 0;
			foreach ( $network_ids as $network_id ) {
				$site_count += get_blog_count( $network_id );
			}

			$info['gc-core']['fields']['site_count'] = array(
				'label' => __( '系统数量' ),
				'value' => $site_count,
			);

			$info['gc-core']['fields']['network_count'] = array(
				'label' => __( 'SaaS平台数量' ),
				'value' => $network_query->found_networks,
			);
		}

		$info['gc-core']['fields']['user_count'] = array(
			'label' => __( '用户数量' ),
			'value' => get_user_count(),
		);

		// GeChiUI features requiring processing.
		$gc_dotorg = gc_remote_get( 'https://www.gechiui.com', array( 'timeout' => 10 ) );

		if ( ! is_gc_error( $gc_dotorg ) ) {
			$info['gc-core']['fields']['dotorg_communication'] = array(
				'label' => __( '与www.GeChiUI.com通讯' ),
				'value' => __( 'www.GeChiUI.com可达' ),
				'debug' => 'true',
			);
		} else {
			$info['gc-core']['fields']['dotorg_communication'] = array(
				'label' => __( '与www.GeChiUI.com通讯' ),
				'value' => sprintf(
					/* translators: 1: The IP address www.GeChiUI.com resolves to. 2: The error returned by the lookup. */
					__( '无法访问www.GeChiUI.com（%1$s）：%2$s' ),
					gethostbyname( 'www.gechiui.com' ),
					$gc_dotorg->get_error_message()
				),
				'debug' => $gc_dotorg->get_error_message(),
			);
		}

		// Remove accordion for Directories and Sizes if in Multisite.
		if ( ! $is_multisite ) {
			$loading = __( '载入中&hellip;' );

			$info['gc-paths-sizes']['fields'] = array(
				'gechiui_path' => array(
					'label' => __( 'GeChiUI目录位置' ),
					'value' => untrailingslashit( ABSPATH ),
				),
				'gechiui_size' => array(
					'label' => __( 'GeChiUI目录大小' ),
					'value' => $loading,
					'debug' => 'loading...',
				),
				'uploads_path'   => array(
					'label' => __( '上传目录位置' ),
					'value' => $upload_dir['basedir'],
				),
				'uploads_size'   => array(
					'label' => __( '上传目录大小' ),
					'value' => $loading,
					'debug' => 'loading...',
				),
				'themes_path'    => array(
					'label' => __( '主题目录位置' ),
					'value' => get_theme_root(),
				),
				'themes_size'    => array(
					'label' => __( '主题目录大小' ),
					'value' => $loading,
					'debug' => 'loading...',
				),
				'plugins_path'   => array(
					'label' => __( '插件目录位置' ),
					'value' => GC_PLUGIN_DIR,
				),
				'plugins_size'   => array(
					'label' => __( '插件目录大小' ),
					'value' => $loading,
					'debug' => 'loading...',
				),
				'database_size'  => array(
					'label' => __( '数据库大小' ),
					'value' => $loading,
					'debug' => 'loading...',
				),
				'total_size'     => array(
					'label' => __( '总安装大小' ),
					'value' => $loading,
					'debug' => 'loading...',
				),
			);
		}

		// Get a list of all drop-in replacements.
		$dropins = get_dropins();

		// Get dropins descriptions.
		$dropin_descriptions = _get_dropins();

		// Spare few function calls.
		$not_available = __( '不可用' );

		foreach ( $dropins as $dropin_key => $dropin ) {
			$info['gc-dropins']['fields'][ sanitize_text_field( $dropin_key ) ] = array(
				'label' => $dropin_key,
				'value' => $dropin_descriptions[ $dropin_key ][0],
				'debug' => 'true',
			);
		}

		// Populate the media fields.
		$info['gc-media']['fields']['image_editor'] = array(
			'label' => __( '已启用的编辑器' ),
			'value' => _gc_image_editor_choose(),
		);

		// Get ImageMagic information, if available.
		if ( class_exists( 'Imagick' ) ) {
			// Save the Imagick instance for later use.
			$imagick             = new Imagick();
			$imagemagick_version = $imagick->getVersion();
		} else {
			$imagemagick_version = __( '不可用' );
		}

		$info['gc-media']['fields']['imagick_module_version'] = array(
			'label' => __( 'ImageMagick版本号' ),
			'value' => ( is_array( $imagemagick_version ) ? $imagemagick_version['versionNumber'] : $imagemagick_version ),
		);

		$info['gc-media']['fields']['imagemagick_version'] = array(
			'label' => __( 'ImageMagick版本字串' ),
			'value' => ( is_array( $imagemagick_version ) ? $imagemagick_version['versionString'] : $imagemagick_version ),
		);

		$imagick_version = phpversion( 'imagick' );

		$info['gc-media']['fields']['imagick_version'] = array(
			'label' => __( 'Imagick 版本' ),
			'value' => ( $imagick_version ) ? $imagick_version : __( '不可用' ),
		);

		if ( ! function_exists( 'ini_get' ) ) {
			$info['gc-media']['fields']['ini_get'] = array(
				'label' => __( '文件上传设置' ),
				'value' => sprintf(
					/* translators: %s: ini_get() */
					__( '无法确认部分设置，因%s函数已被禁用。' ),
					'ini_get()'
				),
				'debug' => 'ini_get() is disabled',
			);
		} else {
			// Get the PHP ini directive values.
			$post_max_size       = ini_get( 'post_max_size' );
			$upload_max_filesize = ini_get( 'upload_max_filesize' );
			$max_file_uploads    = ini_get( 'max_file_uploads' );
			$effective           = min( gc_convert_hr_to_bytes( $post_max_size ), gc_convert_hr_to_bytes( $upload_max_filesize ) );

			// Add info in Media section.
			$info['gc-media']['fields']['file_uploads']        = array(
				'label' => __( '文件上传' ),
				'value' => empty( ini_get( 'file_uploads' ) ) ? __( '禁用' ) : __( '启用' ),
				'debug' => 'File uploads is turned off',
			);
			$info['gc-media']['fields']['post_max_size']       = array(
				'label' => __( '最大允许POST提交数据大小' ),
				'value' => $post_max_size,
			);
			$info['gc-media']['fields']['upload_max_filesize'] = array(
				'label' => __( '最大单一上传文件大小' ),
				'value' => $upload_max_filesize,
			);
			$info['gc-media']['fields']['max_effective_size']  = array(
				'label' => __( '最大有效文件大小' ),
				'value' => size_format( $effective ),
			);
			$info['gc-media']['fields']['max_file_uploads']    = array(
				'label' => __( '最大允许上传文件数' ),
				'value' => number_format( $max_file_uploads ),
			);
		}

		// If Imagick is used as our editor, provide some more information about its limitations.
		if ( 'GC_Image_Editor_Imagick' === _gc_image_editor_choose() && isset( $imagick ) && $imagick instanceof Imagick ) {
			$limits = array(
				'area'   => ( defined( 'imagick::RESOURCETYPE_AREA' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_AREA ) ) : $not_available ),
				'disk'   => ( defined( 'imagick::RESOURCETYPE_DISK' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_DISK ) : $not_available ),
				'file'   => ( defined( 'imagick::RESOURCETYPE_FILE' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_FILE ) : $not_available ),
				'map'    => ( defined( 'imagick::RESOURCETYPE_MAP' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_MAP ) ) : $not_available ),
				'memory' => ( defined( 'imagick::RESOURCETYPE_MEMORY' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_MEMORY ) ) : $not_available ),
				'thread' => ( defined( 'imagick::RESOURCETYPE_THREAD' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_THREAD ) : $not_available ),
				'time'   => ( defined( 'imagick::RESOURCETYPE_TIME' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_TIME ) : $not_available ),
			);

			$limits_debug = array(
				'imagick::RESOURCETYPE_AREA'   => ( defined( 'imagick::RESOURCETYPE_AREA' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_AREA ) ) : 'not available' ),
				'imagick::RESOURCETYPE_DISK'   => ( defined( 'imagick::RESOURCETYPE_DISK' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_DISK ) : 'not available' ),
				'imagick::RESOURCETYPE_FILE'   => ( defined( 'imagick::RESOURCETYPE_FILE' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_FILE ) : 'not available' ),
				'imagick::RESOURCETYPE_MAP'    => ( defined( 'imagick::RESOURCETYPE_MAP' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_MAP ) ) : 'not available' ),
				'imagick::RESOURCETYPE_MEMORY' => ( defined( 'imagick::RESOURCETYPE_MEMORY' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_MEMORY ) ) : 'not available' ),
				'imagick::RESOURCETYPE_THREAD' => ( defined( 'imagick::RESOURCETYPE_THREAD' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_THREAD ) : 'not available' ),
				'imagick::RESOURCETYPE_TIME'   => ( defined( 'imagick::RESOURCETYPE_TIME' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_TIME ) : 'not available' ),
			);

			$info['gc-media']['fields']['imagick_limits'] = array(
				'label' => __( 'Imagick资源限制' ),
				'value' => $limits,
				'debug' => $limits_debug,
			);

			try {
				$formats = Imagick::queryFormats( '*' );
			} catch ( Exception $e ) {
				$formats = array();
			}

			$info['gc-media']['fields']['imagemagick_file_formats'] = array(
				'label' => __( 'ImageMagick 支持的文件格式' ),
				'value' => ( empty( $formats ) ) ? __( '无法确定' ) : implode( ', ', $formats ),
				'debug' => ( empty( $formats ) ) ? '无法确定' : implode( ', ', $formats ),
			);
		}

		// Get GD information, if available.
		if ( function_exists( 'gd_info' ) ) {
			$gd = gd_info();
		} else {
			$gd = false;
		}

		$info['gc-media']['fields']['gd_version'] = array(
			'label' => __( 'GD版本' ),
			'value' => ( is_array( $gd ) ? $gd['GD Version'] : $not_available ),
			'debug' => ( is_array( $gd ) ? $gd['GD Version'] : 'not available' ),
		);

		$gd_image_formats     = array();
		$gd_supported_formats = array(
			'GIF Create' => 'GIF',
			'JPEG'       => 'JPEG',
			'PNG'        => 'PNG',
			'WebP'       => 'WebP',
			'BMP'        => 'BMP',
			'AVIF'       => 'AVIF',
			'HEIF'       => 'HEIF',
			'TIFF'       => 'TIFF',
			'XPM'        => 'XPM',
		);

		foreach ( $gd_supported_formats as $format_key => $format ) {
			$index = $format_key . ' Support';
			if ( isset( $gd[ $index ] ) && $gd[ $index ] ) {
				array_push( $gd_image_formats, $format );
			}
		}

		if ( ! empty( $gd_image_formats ) ) {
			$info['gc-media']['fields']['gd_formats'] = array(
				'label' => __( 'GD 支持的文件格式' ),
				'value' => implode( ', ', $gd_image_formats ),
			);
		}

		// Get Ghostscript information, if available.
		if ( function_exists( 'exec' ) ) {
			$gs = exec( 'gs --version' );

			if ( empty( $gs ) ) {
				$gs       = $not_available;
				$gs_debug = 'not available';
			} else {
				$gs_debug = $gs;
			}
		} else {
			$gs       = __( '无法确认Ghostscript是否安装' );
			$gs_debug = 'unknown';
		}

		$info['gc-media']['fields']['ghostscript_version'] = array(
			'label' => __( 'Ghostscript版本' ),
			'value' => $gs,
			'debug' => $gs_debug,
		);

		// Populate the server debug fields.
		if ( function_exists( 'php_uname' ) ) {
			$server_architecture = sprintf( '%s %s %s', php_uname( 's' ), php_uname( 'r' ), php_uname( 'm' ) );
		} else {
			$server_architecture = 'unknown';
		}

		$php_version_debug = PHP_VERSION;
		// Whether PHP supports 64-bit.
		$php64bit = ( PHP_INT_SIZE * 8 === 64 );

		$php_version = sprintf(
			'%s %s',
			$php_version_debug,
			( $php64bit ? __( '（支持64位数值）' ) : __( '（不支持64位数值）' ) )
		);

		if ( $php64bit ) {
			$php_version_debug .= ' 64bit';
		}

		if ( function_exists( 'php_sapi_name' ) ) {
			$php_sapi = php_sapi_name();
		} else {
			$php_sapi = 'unknown';
		}

		$info['gc-server']['fields']['server_architecture'] = array(
			'label' => __( '服务器架构' ),
			'value' => ( 'unknown' !== $server_architecture ? $server_architecture : __( '无法确认服务器架构' ) ),
			'debug' => $server_architecture,
		);
		$info['gc-server']['fields']['httpd_software']      = array(
			'label' => __( 'Web服务器' ),
			'value' => ( isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : __( '无法确认web服务器软件' ) ),
			'debug' => ( isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : 'unknown' ),
		);
		$info['gc-server']['fields']['php_version']         = array(
			'label' => __( 'PHP版本' ),
			'value' => $php_version,
			'debug' => $php_version_debug,
		);
		$info['gc-server']['fields']['php_sapi']            = array(
			'label' => __( 'PHP SAPI' ),
			'value' => ( 'unknown' !== $php_sapi ? $php_sapi : __( '无法确认PHP SAPI' ) ),
			'debug' => $php_sapi,
		);

		// Some servers disable `ini_set()` and `ini_get()`, we check this before trying to get configuration values.
		if ( ! function_exists( 'ini_get' ) ) {
			$info['gc-server']['fields']['ini_get'] = array(
				'label' => __( '服务器设置' ),
				'value' => sprintf(
					/* translators: %s: ini_get() */
					__( '无法确认部分设置，因%s函数已被禁用。' ),
					'ini_get()'
				),
				'debug' => 'ini_get() is disabled',
			);
		} else {
			$info['gc-server']['fields']['max_input_variables'] = array(
				'label' => __( 'PHP最大输入变量' ),
				'value' => ini_get( 'max_input_vars' ),
			);
			$info['gc-server']['fields']['time_limit']          = array(
				'label' => __( 'PHP时间限制' ),
				'value' => ini_get( 'max_execution_time' ),
			);

			if ( GC_Site_Health::get_instance()->php_memory_limit !== ini_get( 'memory_limit' ) ) {
				$info['gc-server']['fields']['memory_limit']       = array(
					'label' => __( 'PHP内存限制' ),
					'value' => GC_Site_Health::get_instance()->php_memory_limit,
				);
				$info['gc-server']['fields']['admin_memory_limit'] = array(
					'label' => __( 'PHP内存限制（仅限管理界面）' ),
					'value' => ini_get( 'memory_limit' ),
				);
			} else {
				$info['gc-server']['fields']['memory_limit'] = array(
					'label' => __( 'PHP内存限制' ),
					'value' => ini_get( 'memory_limit' ),
				);
			}

			$info['gc-server']['fields']['max_input_time']      = array(
				'label' => __( '最大输入时间' ),
				'value' => ini_get( 'max_input_time' ),
			);
			$info['gc-server']['fields']['upload_max_filesize'] = array(
				'label' => __( '上传最大文件大小' ),
				'value' => ini_get( 'upload_max_filesize' ),
			);
			$info['gc-server']['fields']['php_post_max_size']   = array(
				'label' => __( 'PHP最大post大小' ),
				'value' => ini_get( 'post_max_size' ),
			);
		}

		if ( function_exists( 'curl_version' ) ) {
			$curl = curl_version();

			$info['gc-server']['fields']['curl_version'] = array(
				'label' => __( 'cURL版本' ),
				'value' => sprintf( '%s %s', $curl['version'], $curl['ssl_version'] ),
			);
		} else {
			$info['gc-server']['fields']['curl_version'] = array(
				'label' => __( 'cURL版本' ),
				'value' => $not_available,
				'debug' => 'not available',
			);
		}

		// SUHOSIN.
		$suhosin_loaded = ( extension_loaded( 'suhosin' ) || ( defined( 'SUHOSIN_PATCH' ) && constant( 'SUHOSIN_PATCH' ) ) );

		$info['gc-server']['fields']['suhosin'] = array(
			'label' => __( '是否安装了SUHOSIN？' ),
			'value' => ( $suhosin_loaded ? __( '是' ) : __( '否' ) ),
			'debug' => $suhosin_loaded,
		);

		// Imagick.
		$imagick_loaded = extension_loaded( 'imagick' );

		$info['gc-server']['fields']['imagick_availability'] = array(
			'label' => __( 'Imagick库是否可用？' ),
			'value' => ( $imagick_loaded ? __( '是' ) : __( '否' ) ),
			'debug' => $imagick_loaded,
		);

		// Pretty permalinks.
		$pretty_permalinks_supported = got_url_rewrite();

		$info['gc-server']['fields']['pretty_permalinks'] = array(
			'label' => __( '是否支持易于识别的固定链接？' ),
			'value' => ( $pretty_permalinks_supported ? __( '是' ) : __( '否' ) ),
			'debug' => $pretty_permalinks_supported,
		);

		// Check if a .htaccess file exists.
		if ( is_file( ABSPATH . '.htaccess' ) ) {
			// If the file exists, grab the content of it.
			$htaccess_content = file_get_contents( ABSPATH . '.htaccess' );

			// Filter away the core GeChiUI rules.
			$filtered_htaccess_content = trim( preg_replace( '/\# BEGIN GeChiUI[\s\S]+?# END GeChiUI/si', '', $htaccess_content ) );
			$filtered_htaccess_content = ! empty( $filtered_htaccess_content );

			if ( $filtered_htaccess_content ) {
				/* translators: %s: .htaccess */
				$htaccess_rules_string = sprintf( __( '自定义规则已被加入您的%s文件。' ), '.htaccess' );
			} else {
				/* translators: %s: .htaccess */
				$htaccess_rules_string = sprintf( __( '您的%s文件仅包含核心GeChiUI功能。' ), '.htaccess' );
			}

			$info['gc-server']['fields']['htaccess_extra_rules'] = array(
				'label' => __( '.htaccess规则' ),
				'value' => $htaccess_rules_string,
				'debug' => $filtered_htaccess_content,
			);
		}

		// Server time.
		$date = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
		$info['gc-server']['fields']['current'] = array(
			'label' => __( '当前时间' ),
			'value' => $date->format( DateTime::ATOM ),
		);
		$info['gc-server']['fields']['utc-time'] = array(
			'label' => __( '当前 UTC 时间' ),
			'value' => $date->format( DateTime::RFC850 ),
		);
		$info['gc-server']['fields']['server-time'] = array(
			'label' => __( '当前服务器时间' ),
			'value' => gc_date( 'c', $_SERVER['REQUEST_TIME'] ),
		);

		// Populate the database debug fields.
		if ( is_resource( $gcdb->dbh ) ) {
			// Old mysql extension.
			$extension = 'mysql';
		} elseif ( is_object( $gcdb->dbh ) ) {
			// mysqli or PDO.
			$extension = get_class( $gcdb->dbh );
		} else {
			// Unknown sql extension.
			$extension = null;
		}

		$server = $gcdb->get_var( 'SELECT VERSION()' );

		if ( isset( $gcdb->use_mysqli ) && $gcdb->use_mysqli ) {
			$client_version = $gcdb->dbh->client_info;
		} else {
			// phpcs:ignore GeChiUI.DB.RestrictedFunctions.mysql_mysql_get_client_info,PHPCompatibility.Extensions.RemovedExtensions.mysql_DeprecatedRemoved
			if ( preg_match( '|[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}|', mysql_get_client_info(), $matches ) ) {
				$client_version = $matches[0];
			} else {
				$client_version = null;
			}
		}

		$info['gc-database']['fields']['extension'] = array(
			'label' => __( '扩展' ),
			'value' => $extension,
		);

		$info['gc-database']['fields']['server_version'] = array(
			'label' => __( '服务器版本' ),
			'value' => $server,
		);

		$info['gc-database']['fields']['client_version'] = array(
			'label' => __( '用户端版本' ),
			'value' => $client_version,
		);

		$info['gc-database']['fields']['database_user'] = array(
			'label'   => __( '数据库用户名' ),
			'value'   => $gcdb->dbuser,
			'private' => true,
		);

		$info['gc-database']['fields']['database_host'] = array(
			'label'   => __( '数据库主机' ),
			'value'   => $gcdb->dbhost,
			'private' => true,
		);

		$info['gc-database']['fields']['database_name'] = array(
			'label'   => __( '数据库名' ),
			'value'   => $gcdb->dbname,
			'private' => true,
		);

		$info['gc-database']['fields']['database_prefix'] = array(
			'label'   => __( '数据表前缀' ),
			'value'   => $gcdb->prefix,
			'private' => true,
		);

		$info['gc-database']['fields']['database_charset'] = array(
			'label'   => __( '数据库字符集' ),
			'value'   => $gcdb->charset,
			'private' => true,
		);

		$info['gc-database']['fields']['database_collate'] = array(
			'label'   => __( '数据库排序规则' ),
			'value'   => $gcdb->collate,
			'private' => true,
		);

		$info['gc-database']['fields']['max_allowed_packet'] = array(
			'label' => __( '最大允许数据包大小' ),
			'value' => self::get_mysql_var( 'max_allowed_packet' ),
		);

		$info['gc-database']['fields']['max_connections'] = array(
			'label' => __( '最大连接数' ),
			'value' => self::get_mysql_var( 'max_connections' ),
		);

		// List must use plugins if there are any.
		$mu_plugins = get_mu_plugins();

		foreach ( $mu_plugins as $plugin_path => $plugin ) {
			$plugin_version = $plugin['Version'];
			$plugin_author  = $plugin['Author'];

			$plugin_version_string       = __( '没有可用的版本或作者信息。' );
			$plugin_version_string_debug = 'author: (undefined), version: (undefined)';

			if ( ! empty( $plugin_version ) && ! empty( $plugin_author ) ) {
				/* translators: 1: Plugin version number. 2: Plugin author name. */
				$plugin_version_string       = sprintf( __( '%1$s版，开发者为%2$s' ), $plugin_version, $plugin_author );
				$plugin_version_string_debug = sprintf( 'version: %s, author: %s', $plugin_version, $plugin_author );
			} else {
				if ( ! empty( $plugin_author ) ) {
					/* translators: %s: Plugin author name. */
					$plugin_version_string       = sprintf( __( '作者：%s' ), $plugin_author );
					$plugin_version_string_debug = sprintf( 'author: %s, version: (undefined)', $plugin_author );
				}

				if ( ! empty( $plugin_version ) ) {
					/* translators: %s: Plugin version number. */
					$plugin_version_string       = sprintf( __( '%s版本' ), $plugin_version );
					$plugin_version_string_debug = sprintf( 'author: (undefined), version: %s', $plugin_version );
				}
			}

			$info['gc-mu-plugins']['fields'][ sanitize_text_field( $plugin['Name'] ) ] = array(
				'label' => $plugin['Name'],
				'value' => $plugin_version_string,
				'debug' => $plugin_version_string_debug,
			);
		}

		// List all available plugins.
		$plugins        = get_plugins();
		$plugin_updates = get_plugin_updates();
		$transient      = get_site_transient( 'update_plugins' );

		$auto_updates = array();

		$auto_updates_enabled = gc_is_auto_update_enabled_for_type( 'plugin' );

		if ( $auto_updates_enabled ) {
			$auto_updates = (array) get_site_option( 'auto_update_plugins', array() );
		}

		foreach ( $plugins as $plugin_path => $plugin ) {
			$plugin_part = ( is_plugin_active( $plugin_path ) ) ? 'gc-plugins-active' : 'gc-plugins-inactive';

			$plugin_version = $plugin['Version'];
			$plugin_author  = $plugin['Author'];

			$plugin_version_string       = __( '没有可用的版本或作者信息。' );
			$plugin_version_string_debug = 'author: (undefined), version: (undefined)';

			if ( ! empty( $plugin_version ) && ! empty( $plugin_author ) ) {
				/* translators: 1: Plugin version number. 2: Plugin author name. */
				$plugin_version_string       = sprintf( __( '%1$s版，开发者为%2$s' ), $plugin_version, $plugin_author );
				$plugin_version_string_debug = sprintf( 'version: %s, author: %s', $plugin_version, $plugin_author );
			} else {
				if ( ! empty( $plugin_author ) ) {
					/* translators: %s: Plugin author name. */
					$plugin_version_string       = sprintf( __( '作者：%s' ), $plugin_author );
					$plugin_version_string_debug = sprintf( 'author: %s, version: (undefined)', $plugin_author );
				}

				if ( ! empty( $plugin_version ) ) {
					/* translators: %s: Plugin version number. */
					$plugin_version_string       = sprintf( __( '%s版本' ), $plugin_version );
					$plugin_version_string_debug = sprintf( 'author: (undefined), version: %s', $plugin_version );
				}
			}

			if ( array_key_exists( $plugin_path, $plugin_updates ) ) {
				/* translators: %s: Latest plugin version number. */
				$plugin_version_string       .= ' ' . sprintf( __( '（最新版本：%s）' ), $plugin_updates[ $plugin_path ]->update->new_version );
				$plugin_version_string_debug .= sprintf( ' (latest version: %s)', $plugin_updates[ $plugin_path ]->update->new_version );
			}

			if ( $auto_updates_enabled ) {
				if ( isset( $transient->response[ $plugin_path ] ) ) {
					$item = $transient->response[ $plugin_path ];
				} elseif ( isset( $transient->no_update[ $plugin_path ] ) ) {
					$item = $transient->no_update[ $plugin_path ];
				} else {
					$item = array(
						'id'            => $plugin_path,
						'slug'          => '',
						'plugin'        => $plugin_path,
						'new_version'   => '',
						'url'           => '',
						'package'       => '',
						'icons'         => array(),
						'banners'       => array(),
						'banners_rtl'   => array(),
						'tested'        => '',
						'requires_php'  => '',
						'compatibility' => new stdClass(),
					);
					$item = gc_parse_args( $plugin, $item );
				}

				$auto_update_forced = gc_is_auto_update_forced_for_item( 'plugin', null, (object) $item );

				if ( ! is_null( $auto_update_forced ) ) {
					$enabled = $auto_update_forced;
				} else {
					$enabled = in_array( $plugin_path, $auto_updates, true );
				}

				if ( $enabled ) {
					$auto_updates_string = __( '自动更新已启用' );
				} else {
					$auto_updates_string = __( '自动更新已禁用' );
				}

				/**
				 * Filters the text string of the auto-updates setting for each plugin in the Site Health debug data.
				 *
				 * @since 5.5.0
				 *
				 * @param string $auto_updates_string The string output for the auto-updates column.
				 * @param string $plugin_path         The path to the plugin file.
				 * @param array  $plugin              An array of plugin data.
				 * @param bool   $enabled             Whether auto-updates are enabled for this item.
				 */
				$auto_updates_string = apply_filters( 'plugin_auto_update_debug_string', $auto_updates_string, $plugin_path, $plugin, $enabled );

				$plugin_version_string       .= ' | ' . $auto_updates_string;
				$plugin_version_string_debug .= ', ' . $auto_updates_string;
			}

			$info[ $plugin_part ]['fields'][ sanitize_text_field( $plugin['Name'] ) ] = array(
				'label' => $plugin['Name'],
				'value' => $plugin_version_string,
				'debug' => $plugin_version_string_debug,
			);
		}

		// Populate the section for the currently active theme.
		$theme_features = array();

		if ( ! empty( $_gc_theme_features ) ) {
			foreach ( $_gc_theme_features as $feature => $options ) {
				$theme_features[] = $feature;
			}
		}

		$active_theme  = gc_get_theme();
		$theme_updates = get_theme_updates();
		$transient     = get_site_transient( 'update_themes' );

		$active_theme_version       = $active_theme->version;
		$active_theme_version_debug = $active_theme_version;

		$auto_updates         = array();
		$auto_updates_enabled = gc_is_auto_update_enabled_for_type( 'theme' );
		if ( $auto_updates_enabled ) {
			$auto_updates = (array) get_site_option( 'auto_update_themes', array() );
		}

		if ( array_key_exists( $active_theme->stylesheet, $theme_updates ) ) {
			$theme_update_new_version = $theme_updates[ $active_theme->stylesheet ]->update['new_version'];

			/* translators: %s: Latest theme version number. */
			$active_theme_version       .= ' ' . sprintf( __( '（最新版本：%s）' ), $theme_update_new_version );
			$active_theme_version_debug .= sprintf( ' (latest version: %s)', $theme_update_new_version );
		}

		$active_theme_author_uri = $active_theme->display( 'AuthorURI' );

		if ( $active_theme->parent_theme ) {
			$active_theme_parent_theme = sprintf(
				/* translators: 1: Theme name. 2: Theme slug. */
				__( '%1$s（%2$s）' ),
				$active_theme->parent_theme,
				$active_theme->template
			);
			$active_theme_parent_theme_debug = sprintf(
				'%s (%s)',
				$active_theme->parent_theme,
				$active_theme->template
			);
		} else {
			$active_theme_parent_theme       = __( '无' );
			$active_theme_parent_theme_debug = 'none';
		}

		$info['gc-active-theme']['fields'] = array(
			'name'           => array(
				'label' => __( '名称' ),
				'value' => sprintf(
					/* translators: 1: Theme name. 2: Theme slug. */
					__( '%1$s（%2$s）' ),
					$active_theme->name,
					$active_theme->stylesheet
				),
			),
			'version'        => array(
				'label' => __( '版本' ),
				'value' => $active_theme_version,
				'debug' => $active_theme_version_debug,
			),
			'author'         => array(
				'label' => __( '作者' ),
				'value' => gc_kses( $active_theme->author, array() ),
			),
			'author_website' => array(
				'label' => __( '作者网站' ),
				'value' => ( $active_theme_author_uri ? $active_theme_author_uri : __( '未定义' ) ),
				'debug' => ( $active_theme_author_uri ? $active_theme_author_uri : '(undefined)' ),
			),
			'parent_theme'   => array(
				'label' => __( '父主题' ),
				'value' => $active_theme_parent_theme,
				'debug' => $active_theme_parent_theme_debug,
			),
			'theme_features' => array(
				'label' => __( '主题功能' ),
				'value' => implode( ', ', $theme_features ),
			),
			'theme_path'     => array(
				'label' => __( '主题目录位置' ),
				'value' => get_stylesheet_directory(),
			),
		);

		if ( $auto_updates_enabled ) {
			if ( isset( $transient->response[ $active_theme->stylesheet ] ) ) {
				$item = $transient->response[ $active_theme->stylesheet ];
			} elseif ( isset( $transient->no_update[ $active_theme->stylesheet ] ) ) {
				$item = $transient->no_update[ $active_theme->stylesheet ];
			} else {
				$item = array(
					'theme'        => $active_theme->stylesheet,
					'new_version'  => $active_theme->version,
					'url'          => '',
					'package'      => '',
					'requires'     => '',
					'requires_php' => '',
				);
			}

			$auto_update_forced = gc_is_auto_update_forced_for_item( 'theme', null, (object) $item );

			if ( ! is_null( $auto_update_forced ) ) {
				$enabled = $auto_update_forced;
			} else {
				$enabled = in_array( $active_theme->stylesheet, $auto_updates, true );
			}

			if ( $enabled ) {
				$auto_updates_string = __( '启用' );
			} else {
				$auto_updates_string = __( '禁用' );
			}

			/** This filter is documented in gc-admin/includes/class-gc-debug-data.php */
			$auto_updates_string = apply_filters( 'theme_auto_update_debug_string', $auto_updates_string, $active_theme, $enabled );

			$info['gc-active-theme']['fields']['auto_update'] = array(
				'label' => __( '自动更新' ),
				'value' => $auto_updates_string,
				'debug' => $auto_updates_string,
			);
		}

		$parent_theme = $active_theme->parent();

		if ( $parent_theme ) {
			$parent_theme_version       = $parent_theme->version;
			$parent_theme_version_debug = $parent_theme_version;

			if ( array_key_exists( $parent_theme->stylesheet, $theme_updates ) ) {
				$parent_theme_update_new_version = $theme_updates[ $parent_theme->stylesheet ]->update['new_version'];

				/* translators: %s: Latest theme version number. */
				$parent_theme_version       .= ' ' . sprintf( __( '（最新版本：%s）' ), $parent_theme_update_new_version );
				$parent_theme_version_debug .= sprintf( ' (latest version: %s)', $parent_theme_update_new_version );
			}

			$parent_theme_author_uri = $parent_theme->display( 'AuthorURI' );

			$info['gc-parent-theme']['fields'] = array(
				'name'           => array(
					'label' => __( '名称' ),
					'value' => sprintf(
						/* translators: 1: Theme name. 2: Theme slug. */
						__( '%1$s（%2$s）' ),
						$parent_theme->name,
						$parent_theme->stylesheet
					),
				),
				'version'        => array(
					'label' => __( '版本' ),
					'value' => $parent_theme_version,
					'debug' => $parent_theme_version_debug,
				),
				'author'         => array(
					'label' => __( '作者' ),
					'value' => gc_kses( $parent_theme->author, array() ),
				),
				'author_website' => array(
					'label' => __( '作者网站' ),
					'value' => ( $parent_theme_author_uri ? $parent_theme_author_uri : __( '未定义' ) ),
					'debug' => ( $parent_theme_author_uri ? $parent_theme_author_uri : '(undefined)' ),
				),
				'theme_path'     => array(
					'label' => __( '主题目录位置' ),
					'value' => get_template_directory(),
				),
			);

			if ( $auto_updates_enabled ) {
				if ( isset( $transient->response[ $parent_theme->stylesheet ] ) ) {
					$item = $transient->response[ $parent_theme->stylesheet ];
				} elseif ( isset( $transient->no_update[ $parent_theme->stylesheet ] ) ) {
					$item = $transient->no_update[ $parent_theme->stylesheet ];
				} else {
					$item = array(
						'theme'        => $parent_theme->stylesheet,
						'new_version'  => $parent_theme->version,
						'url'          => '',
						'package'      => '',
						'requires'     => '',
						'requires_php' => '',
					);
				}

				$auto_update_forced = gc_is_auto_update_forced_for_item( 'theme', null, (object) $item );

				if ( ! is_null( $auto_update_forced ) ) {
					$enabled = $auto_update_forced;
				} else {
					$enabled = in_array( $parent_theme->stylesheet, $auto_updates, true );
				}

				if ( $enabled ) {
					$parent_theme_auto_update_string = __( '启用' );
				} else {
					$parent_theme_auto_update_string = __( '禁用' );
				}

				/** This filter is documented in gc-admin/includes/class-gc-debug-data.php */
				$parent_theme_auto_update_string = apply_filters( 'theme_auto_update_debug_string', $auto_updates_string, $parent_theme, $enabled );

				$info['gc-parent-theme']['fields']['auto_update'] = array(
					'label' => __( '自动更新' ),
					'value' => $parent_theme_auto_update_string,
					'debug' => $parent_theme_auto_update_string,
				);
			}
		}

		// Populate a list of all themes available in the install.
		$all_themes = gc_get_themes();

		foreach ( $all_themes as $theme_slug => $theme ) {
			// Exclude the currently active theme from the list of all themes.
			if ( $active_theme->stylesheet === $theme_slug ) {
				continue;
			}

			// Exclude the currently active parent theme from the list of all themes.
			if ( ! empty( $parent_theme ) && $parent_theme->stylesheet === $theme_slug ) {
				continue;
			}

			$theme_version = $theme->version;
			$theme_author  = $theme->author;

			// Sanitize.
			$theme_author = gc_kses( $theme_author, array() );

			$theme_version_string       = __( '没有可用的版本或作者信息。' );
			$theme_version_string_debug = 'undefined';

			if ( ! empty( $theme_version ) && ! empty( $theme_author ) ) {
				/* translators: 1: Theme version number. 2: Theme author name. */
				$theme_version_string       = sprintf( __( '%1$s版，开发者为%2$s' ), $theme_version, $theme_author );
				$theme_version_string_debug = sprintf( 'version: %s, author: %s', $theme_version, $theme_author );
			} else {
				if ( ! empty( $theme_author ) ) {
					/* translators: %s: Theme author name. */
					$theme_version_string       = sprintf( __( '作者：%s' ), $theme_author );
					$theme_version_string_debug = sprintf( 'author: %s, version: (undefined)', $theme_author );
				}

				if ( ! empty( $theme_version ) ) {
					/* translators: %s: Theme version number. */
					$theme_version_string       = sprintf( __( '%s版本' ), $theme_version );
					$theme_version_string_debug = sprintf( 'author: (undefined), version: %s', $theme_version );
				}
			}

			if ( array_key_exists( $theme_slug, $theme_updates ) ) {
				/* translators: %s: Latest theme version number. */
				$theme_version_string       .= ' ' . sprintf( __( '（最新版本：%s）' ), $theme_updates[ $theme_slug ]->update['new_version'] );
				$theme_version_string_debug .= sprintf( ' (latest version: %s)', $theme_updates[ $theme_slug ]->update['new_version'] );
			}

			if ( $auto_updates_enabled ) {
				if ( isset( $transient->response[ $theme_slug ] ) ) {
					$item = $transient->response[ $theme_slug ];
				} elseif ( isset( $transient->no_update[ $theme_slug ] ) ) {
					$item = $transient->no_update[ $theme_slug ];
				} else {
					$item = array(
						'theme'        => $theme_slug,
						'new_version'  => $theme->version,
						'url'          => '',
						'package'      => '',
						'requires'     => '',
						'requires_php' => '',
					);
				}

				$auto_update_forced = gc_is_auto_update_forced_for_item( 'theme', null, (object) $item );

				if ( ! is_null( $auto_update_forced ) ) {
					$enabled = $auto_update_forced;
				} else {
					$enabled = in_array( $theme_slug, $auto_updates, true );
				}

				if ( $enabled ) {
					$auto_updates_string = __( '自动更新已启用' );
				} else {
					$auto_updates_string = __( '自动更新已禁用' );
				}

				/**
				 * Filters the text string of the auto-updates setting for each theme in the Site Health debug data.
				 *
				 * @since 5.5.0
				 *
				 * @param string   $auto_updates_string The string output for the auto-updates column.
				 * @param GC_Theme $theme               An object of theme data.
				 * @param bool     $enabled             Whether auto-updates are enabled for this item.
				 */
				$auto_updates_string = apply_filters( 'theme_auto_update_debug_string', $auto_updates_string, $theme, $enabled );

				$theme_version_string       .= ' | ' . $auto_updates_string;
				$theme_version_string_debug .= ', ' . $auto_updates_string;
			}

			$info['gc-themes-inactive']['fields'][ sanitize_text_field( $theme->name ) ] = array(
				'label' => sprintf(
					/* translators: 1: Theme name. 2: Theme slug. */
					__( '%1$s（%2$s）' ),
					$theme->name,
					$theme_slug
				),
				'value' => $theme_version_string,
				'debug' => $theme_version_string_debug,
			);
		}

		// Add more filesystem checks.
		if ( defined( 'GCMU_PLUGIN_DIR' ) && is_dir( GCMU_PLUGIN_DIR ) ) {
			$is_writable_gcmu_plugin_dir = gc_is_writable( GCMU_PLUGIN_DIR );

			$info['gc-filesystem']['fields']['mu-plugins'] = array(
				'label' => __( '强制使用的插件目录' ),
				'value' => ( $is_writable_gcmu_plugin_dir ? __( '可写' ) : __( '不可写' ) ),
				'debug' => ( $is_writable_gcmu_plugin_dir ? 'writable' : 'not writable' ),
			);
		}

		/**
		 * Filters the debug information shown on the Tools -> Site Health -> Info screen.
		 *
		 * Plugin or themes may wish to introduce their own debug information without creating
		 * additional admin pages. They can utilize this filter to introduce their own sections
		 * or add more data to existing sections.
		 *
		 * Array keys for sections added by core are all prefixed with `gc-`. Plugins and themes
		 * should use their own slug as a prefix, both for consistency as well as avoiding
		 * key collisions. Note that the array keys are used as labels for the copied data.
		 *
		 * All strings are expected to be plain text except `$description` that can contain
		 * inline HTML tags (see below).
		 *
		 * @since 5.2.0
		 *
		 * @param array $args {
		 *     The debug information to be added to the core information page.
		 *
		 *     This is an associative multi-dimensional array, up to three levels deep.
		 *     The topmost array holds the sections, keyed by section ID.
		 *
		 *     @type array ...$0 {
		 *         Each section has a `$fields` associative array (see below), and each `$value` in `$fields`
		 *         can be another associative array of name/value pairs when there is more structured data
		 *         to display.
		 *
		 *         @type string $label       Required. The title for this section of the debug output.
		 *         @type string $description Optional. A description for your information section which
		 *                                   may contain basic HTML markup, inline tags only as it is
		 *                                   outputted in a paragraph.
		 *         @type bool   $show_count  Optional. If set to `true`, the amount of fields will be included
		 *                                   in the title for this section. Default false.
		 *         @type bool   $private     Optional. If set to `true`, the section and all associated fields
		 *                                   will be excluded from the copied data. Default false.
		 *         @type array  $fields {
		 *             Required. An associative array containing the fields to be displayed in the section,
		 *             keyed by field ID.
		 *
		 *             @type array ...$0 {
		 *                 An associative array containing the data to be displayed for the field.
		 *
		 *                 @type string $label    Required. The label for this piece of information.
		 *                 @type mixed  $value    Required. The output that is displayed for this field.
		 *                                        Text should be translated. Can be an associative array
		 *                                        that is displayed as name/value pairs.
		 *                                        Accepted types: `string|int|float|(string|int|float)[]`.
		 *                 @type string $debug    Optional. The output that is used for this field when
		 *                                        the user copies the data. It should be more concise and
		 *                                        not translated. If not set, the content of `$value`
		 *                                        is used. Note that the array keys are used as labels
		 *                                        for the copied data.
		 *                 @type bool   $private  Optional. If set to `true`, the field will be excluded
		 *                                        from the copied data, allowing you to show, for example,
		 *                                        API keys here. Default false.
		 *             }
		 *         }
		 *     }
		 * }
		 */
		$info = apply_filters( 'debug_information', $info );

		return $info;
	}

	/**
	 * Returns the value of a MySQL system variable.
	 *
	 * @since 5.9.0
	 *
	 * @global gcdb $gcdb GeChiUI database abstraction object.
	 *
	 * @param string $mysql_var Name of the MySQL system variable.
	 * @return string|null The variable value on success. Null if the variable does not exist.
	 */
	public static function get_mysql_var( $mysql_var ) {
		global $gcdb;

		$result = $gcdb->get_row(
			$gcdb->prepare( 'SHOW VARIABLES LIKE %s', $mysql_var ),
			ARRAY_A
		);

		if ( ! empty( $result ) && array_key_exists( 'Value', $result ) ) {
			return $result['Value'];
		}

		return null;
	}

	/**
	 * Formats the information gathered for debugging, in a manner suitable for copying to a forum or support ticket.
	 *
	 * @since 5.2.0
	 *
	 * @param array  $info_array Information gathered from the `GC_Debug_Data::debug_data()` function.
	 * @param string $data_type  The data type to return, either 'info' or 'debug'.
	 * @return string The formatted data.
	 */
	public static function format( $info_array, $data_type ) {
		$return = "`\n";

		foreach ( $info_array as $section => $details ) {
			// Skip this section if there are no fields, or the section has been declared as private.
			if ( empty( $details['fields'] ) || ( isset( $details['private'] ) && $details['private'] ) ) {
				continue;
			}

			$section_label = 'debug' === $data_type ? $section : $details['label'];

			$return .= sprintf(
				"### %s%s ###\n\n",
				$section_label,
				( isset( $details['show_count'] ) && $details['show_count'] ? sprintf( ' (%d)', count( $details['fields'] ) ) : '' )
			);

			foreach ( $details['fields'] as $field_name => $field ) {
				if ( isset( $field['private'] ) && true === $field['private'] ) {
					continue;
				}

				if ( 'debug' === $data_type && isset( $field['debug'] ) ) {
					$debug_data = $field['debug'];
				} else {
					$debug_data = $field['value'];
				}

				// Can be array, one level deep only.
				if ( is_array( $debug_data ) ) {
					$value = '';

					foreach ( $debug_data as $sub_field_name => $sub_field_value ) {
						$value .= sprintf( "\n\t%s: %s", $sub_field_name, $sub_field_value );
					}
				} elseif ( is_bool( $debug_data ) ) {
					$value = $debug_data ? 'true' : 'false';
				} elseif ( empty( $debug_data ) && '0' !== $debug_data ) {
					$value = 'undefined';
				} else {
					$value = $debug_data;
				}

				if ( 'debug' === $data_type ) {
					$label = $field_name;
				} else {
					$label = $field['label'];
				}

				$return .= sprintf( "%s: %s\n", $label, $value );
			}

			$return .= "\n";
		}

		$return .= '`';

		return $return;
	}

	/**
	 * Fetches the total size of all the database tables for the active database user.
	 *
	 * @since 5.2.0
	 *
	 * @global gcdb $gcdb GeChiUI database abstraction object.
	 *
	 * @return int The size of the database, in bytes.
	 */
	public static function get_database_size() {
		global $gcdb;
		$size = 0;
		$rows = $gcdb->get_results( 'SHOW TABLE STATUS', ARRAY_A );

		if ( $gcdb->num_rows > 0 ) {
			foreach ( $rows as $row ) {
				$size += $row['Data_length'] + $row['Index_length'];
			}
		}

		return (int) $size;
	}

	/**
	 * Fetches the sizes of the GeChiUI directories: `gechiui` (ABSPATH), `plugins`, `themes`, and `uploads`.
	 * Intended to supplement the array returned by `GC_Debug_Data::debug_data()`.
	 *
	 * @since 5.2.0
	 *
	 * @return array The sizes of the directories, also the database size and total installation size.
	 */
	public static function get_sizes() {
		$size_db    = self::get_database_size();
		$upload_dir = gc_get_upload_dir();

		/*
		 * We will be using the PHP max execution time to prevent the size calculations
		 * from causing a timeout. The default value is 30 seconds, and some
		 * hosts do not allow you to read configuration values.
		 */
		if ( function_exists( 'ini_get' ) ) {
			$max_execution_time = ini_get( 'max_execution_time' );
		}

		/*
		 * The max_execution_time defaults to 0 when PHP runs from cli.
		 * We still want to limit it below.
		 */
		if ( empty( $max_execution_time ) ) {
			$max_execution_time = 30; // 30 seconds.
		}

		if ( $max_execution_time > 20 ) {
			/*
			 * If the max_execution_time is set to lower than 20 seconds, reduce it a bit to prevent
			 * edge-case timeouts that may happen after the size loop has finished running.
			 */
			$max_execution_time -= 2;
		}

		/*
		 * Go through the various installation directories and calculate their sizes.
		 * No trailing slashes.
		 */
		$paths = array(
			'gechiui_size' => untrailingslashit( ABSPATH ),
			'themes_size'    => get_theme_root(),
			'plugins_size'   => GC_PLUGIN_DIR,
			'uploads_size'   => $upload_dir['basedir'],
		);

		$exclude = $paths;
		unset( $exclude['gechiui_size'] );
		$exclude = array_values( $exclude );

		$size_total = 0;
		$all_sizes  = array();

		// Loop over all the directories we want to gather the sizes for.
		foreach ( $paths as $name => $path ) {
			$dir_size = null; // Default to timeout.
			$results  = array(
				'path' => $path,
				'raw'  => 0,
			);

			if ( microtime( true ) - GC_START_TIMESTAMP < $max_execution_time ) {
				if ( 'gechiui_size' === $name ) {
					$dir_size = recurse_dirsize( $path, $exclude, $max_execution_time );
				} else {
					$dir_size = recurse_dirsize( $path, null, $max_execution_time );
				}
			}

			if ( false === $dir_size ) {
				// Error reading.
				$results['size']  = __( '未能计算目录大小。目录不能被访问，这通常是因为目录有无效的权限。' );
				$results['debug'] = 'not accessible';

				// Stop total size calculation.
				$size_total = null;
			} elseif ( null === $dir_size ) {
				// Timeout.
				$results['size']  = __( '目录大小计算已超时。这通常是因为目录中有特别大量的子目录及文件。' );
				$results['debug'] = 'timeout while calculating size';

				// Stop total size calculation.
				$size_total = null;
			} else {
				if ( null !== $size_total ) {
					$size_total += $dir_size;
				}

				$results['raw']   = $dir_size;
				$results['size']  = size_format( $dir_size, 2 );
				$results['debug'] = $results['size'] . " ({$dir_size} bytes)";
			}

			$all_sizes[ $name ] = $results;
		}

		if ( $size_db > 0 ) {
			$database_size = size_format( $size_db, 2 );

			$all_sizes['database_size'] = array(
				'raw'   => $size_db,
				'size'  => $database_size,
				'debug' => $database_size . " ({$size_db} bytes)",
			);
		} else {
			$all_sizes['database_size'] = array(
				'size'  => __( '不可用' ),
				'debug' => 'not available',
			);
		}

		if ( null !== $size_total && $size_db > 0 ) {
			$total_size    = $size_total + $size_db;
			$total_size_mb = size_format( $total_size, 2 );

			$all_sizes['total_size'] = array(
				'raw'   => $total_size,
				'size'  => $total_size_mb,
				'debug' => $total_size_mb . " ({$total_size} bytes)",
			);
		} else {
			$all_sizes['total_size'] = array(
				'size'  => __( '总大小不可用。在计算您的安装大小时遇到了一些错误。' ),
				'debug' => 'not available',
			);
		}

		return $all_sizes;
	}
}
