<?php
/**
 * GeChiUI Plugin Install Administration API
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Retrieves plugin installer pages from the www.GeChiUI.com Plugins API.
 *
 * It is possible for a plugin to override the Plugin API result with three
 * filters. Assume this is for plugins, which can extend on the Plugin Info to
 * offer more choices. This is very powerful and must be used with care when
 * overriding the filters.
 *
 * The first filter, {@see 'plugins_api_args'}, is for the args and gives the action
 * as the second parameter. The hook for {@see 'plugins_api_args'} must ensure that
 * an object is returned.
 *
 * The second filter, {@see 'plugins_api'}, allows a plugin to override the www.GeChiUI.com
 * Plugin Installation API entirely. If `$action` is 'query_plugins' or 'plugin_information',
 * an object MUST be passed. If `$action` is 'hot_tags' or 'hot_categories', an array MUST
 * be passed.
 *
 * Finally, the third filter, {@see 'plugins_api_result'}, makes it possible to filter the
 * response object or array, depending on the `$action` type.
 *
 * Supported arguments per action:
 *
 * | Argument Name        | query_plugins | plugin_information | hot_tags | hot_categories |
 * | -------------------- | :-----------: | :----------------: | :------: | :------------: |
 * | `$slug`              | No            |  Yes               | No       | No             |
 * | `$per_page`          | Yes           |  No                | No       | No             |
 * | `$page`              | Yes           |  No                | No       | No             |
 * | `$number`            | No            |  No                | Yes      | Yes            |
 * | `$search`            | Yes           |  No                | No       | No             |
 * | `$tag`               | Yes           |  No                | No       | No             |
 * | `$author`            | Yes           |  No                | No       | No             |
 * | `$user`              | Yes           |  No                | No       | No             |
 * | `$browse`            | Yes           |  No                | No       | No             |
 * | `$locale`            | Yes           |  Yes               | No       | No             |
 * | `$installed_plugins` | Yes           |  No                | No       | No             |
 * | `$is_ssl`            | Yes           |  Yes               | No       | No             |
 * | `$fields`            | Yes           |  Yes               | No       | No             |
 *
 *
 *
 * @param string       $action API action to perform: 'query_plugins', 'plugin_information',
 *                             'hot_tags' or 'hot_categories'.
 * @param array|object $args   {
 *     Optional. Array or object of arguments to serialize for the Plugin Info API.
 *
 *     @type string  $slug              The plugin slug. Default empty.
 *     @type int     $per_page          Number of plugins per page. Default 24.
 *     @type int     $page              Number of current page. Default 1.
 *     @type int     $number            Number of tags or categories to be queried.
 *     @type string  $search            A search term. Default empty.
 *     @type string  $tag               Tag to filter plugins. Default empty.
 *     @type string  $author            Username of an plugin author to filter plugins. Default empty.
 *     @type string  $user              Username to query for their favorites. Default empty.
 *     @type string  $browse            Browse view: 'popular', 'new', 'beta', 'recommended'.
 *     @type string  $locale            Locale to provide context-sensitive results. Default is the value
 *                                      of get_locale().
 *     @type string  $installed_plugins Installed plugins to provide context-sensitive results.
 *     @type bool    $is_ssl            Whether links should be returned with https or not. Default false.
 *     @type array   $fields            {
 *         Array of fields which should or should not be returned.
 *
 *         @type bool $short_description Whether to return the plugin short description. Default true.
 *         @type bool $description       Whether to return the plugin full description. Default false.
 *         @type bool $sections          Whether to return the plugin readme sections: description, installation,
 *                                       FAQ, screenshots, other notes, and changelog. Default false.
 *         @type bool $tested            Whether to return the 'Compatible up to' value. Default true.
 *         @type bool $requires          Whether to return the required GeChiUI version. Default true.
 *         @type bool $requires_php      Whether to return the required PHP version. Default true.
 *         @type bool $rating            Whether to return the rating in percent and total number of ratings.
 *                                       Default true.
 *         @type bool $ratings           Whether to return the number of rating for each star (1-5). Default true.
 *         @type bool $downloaded        Whether to return the download count. Default true.
 *         @type bool $downloadlink      Whether to return the download link for the package. Default true.
 *         @type bool $last_updated      Whether to return the date of the last update. Default true.
 *         @type bool $added             Whether to return the date when the plugin was added to the www.gechiui.com
 *                                       repository. Default true.
 *         @type bool $tags              Whether to return the assigned tags. Default true.
 *         @type bool $compatibility     Whether to return the GeChiUI compatibility list. Default true.
 *         @type bool $homepage          Whether to return the plugin homepage link. Default true.
 *         @type bool $versions          Whether to return the list of all available versions. Default false.
 *         @type bool $donate_link       Whether to return the donation link. Default true.
 *         @type bool $reviews           Whether to return the plugin reviews. Default false.
 *         @type bool $banners           Whether to return the banner images links. Default false.
 *         @type bool $icons             Whether to return the icon links. Default false.
 *         @type bool $active_installs   Whether to return the number of active installations. Default false.
 *         @type bool $group             Whether to return the assigned group. Default false.
 *         @type bool $contributors      Whether to return the list of contributors. Default false.
 *     }
 * }
 * @return object|array|GC_Error Response object or array on success, GC_Error on failure. See the
 *         {@link https://developer.gechiui.com/reference/functions/plugins_api/ function reference article}
 *         for more information on the make-up of possible return values depending on the value of `$action`.
 */
function plugins_api( $action, $args = array() ) {
	// Include an unmodified $gc_version.
	require ABSPATH . GCINC . '/version.php';

	if ( is_array( $args ) ) {
		$args = (object) $args;
	}

	if ( 'query_plugins' === $action ) {
		if ( ! isset( $args->per_page ) ) {
			$args->per_page = 24;
		}
	}

	//添加appkey身份识别
    $gcorg_professionals = get_user_option( 'gcorg_professionals' );
   if ( $gcorg_professionals ) {
		$args->username = $gcorg_professionals['username'];
        $args->appkey = $gcorg_professionals['appkey'];
	}

	if ( ! isset( $args->locale ) ) {
		$args->locale = get_user_locale();
	}

	if ( ! isset( $args->gc_version ) ) {
		$args->gc_version = substr( $gc_version, 0, 3 ); // x.y
	}

	/**
	 * Filters the www.GeChiUI.com Plugin Installation API arguments.
	 *
	 * Important: An object MUST be returned to this filter.
	 *
	 *
	 * @param object $args   Plugin API arguments.
	 * @param string $action The type of information being requested from the Plugin Installation API.
	 */
	$args = apply_filters( 'plugins_api_args', $args, $action );

	/**
	 * Filters the response for the current www.GeChiUI.com Plugin Installation API request.
	 *
	 * Returning a non-false value will effectively short-circuit the www.GeChiUI.com API request.
	 *
	 * If `$action` is 'query_plugins' or 'plugin_information', an object MUST be passed.
	 * If `$action` is 'hot_tags' or 'hot_categories', an array should be passed.
	 *
	 *
	 * @param false|object|array $result The result object or array. Default false.
	 * @param string             $action The type of information being requested from the Plugin Installation API.
	 * @param object             $args   Plugin API arguments.
	 */
	$res = apply_filters( 'plugins_api', false, $action, $args );

	if ( false === $res ) {

		$url = 'http://api.gechiui.com/plugins/info/1.2/';
		$url = add_query_arg(
			array(
				'action'  => $action,
				'request' => $args,
			),
			$url
		);

		$http_url = $url;
		$ssl      = gc_http_supports( array( 'ssl' ) );
		if ( $ssl ) {
			$url = set_url_scheme( $url, 'https' );
		}

		$http_args = array(
			'timeout'    => 15,
			'user-agent' => 'GeChiUI/' . $gc_version . '; ' . home_url( '/' ),
		);
		$request   = gc_remote_get( $url, $http_args );

		if ( $ssl && is_gc_error( $request ) ) {
			if ( ! gc_is_json_request() ) {
				trigger_error(
					sprintf(
						/* translators: %s: Support forums URL. */
						__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
						__( 'https://www.gechiui.com/support/forums/' )
					) . ' ' . __( '（GeChiUI无法建立到www.GeChiUI.com的安全连接，请联系您的服务器管理员。）' ),
					headers_sent() || GC_DEBUG ? E_USER_WARNING : E_USER_NOTICE
				);
			}

			$request = gc_remote_get( $http_url, $http_args );
		}

		if ( is_gc_error( $request ) ) {
			$res = new GC_Error(
				'plugins_api_failed',
				sprintf(
					/* translators: %s: Support forums URL. */
					__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
					__( 'https://www.gechiui.com/support/forums/' )
				),
				$request->get_error_message()
			);
		} else {
			$res = json_decode( gc_remote_retrieve_body( $request ), true );
			if ( is_array( $res ) ) {
				// Object casting is required in order to match the info/1.0 format.
				$res = (object) $res;
			} elseif ( null === $res ) {
				$res = new GC_Error(
					'plugins_api_failed',
					sprintf(
						/* translators: %s: Support forums URL. */
						__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
						__( 'https://www.gechiui.com/support/forums/' )
					),
					gc_remote_retrieve_body( $request )
				);
			}

			if ( isset( $res->error ) ) {
				$res = new GC_Error( 'plugins_api_failed', $res->error );
			}
		}
	} elseif ( ! is_gc_error( $res ) ) {
		$res->external = true;
	}

	/**
	 * Filters the Plugin Installation API response results.
	 *
	 *
	 * @param object|GC_Error $res    Response object or GC_Error.
	 * @param string          $action The type of information being requested from the Plugin Installation API.
	 * @param object          $args   Plugin API arguments.
	 */
	return apply_filters( 'plugins_api_result', $res, $action, $args );
}

/**
 * Retrieve popular GeChiUI plugin tags.
 *
 *
 *
 * @param array $args
 * @return array|GC_Error
 */
function install_popular_tags( $args = array() ) {
	$key  = md5( serialize( $args ) );
	$tags = get_site_transient( 'poptags_' . $key );
	if ( false !== $tags ) {
		return $tags;
	}

	$tags = plugins_api( 'hot_tags', $args );

	if ( is_gc_error( $tags ) ) {
		return $tags;
	}

	set_site_transient( 'poptags_' . $key, $tags, 3 * HOUR_IN_SECONDS );

	return $tags;
}

/**
 *
 */
function install_dashboard() {
	?>
	<p>
		<?php
		printf(
			/* translators: %s: https://www.gechiui.com/plugins/ */
			__( '插件可以扩展和增强GeChiUI的功能。您可以从<a href="%s">GeChiUI插件目录</a>中自动安装插件，或者通过点击本页顶部的按钮上传.zip格式的插件文件。' ),
			__( 'https://www.gechiui.com/plugins/' )
		);
		?>
	</p>

	<?php display_plugins_table(); ?>

	<div class="plugins-popular-tags-wrapper">
	<h2><?php _e( '热门标签' ); ?></h2>
	<p><?php _e( '您也可以浏览插件目录中最流行的标签：' ); ?></p>
	<?php

	$api_tags = install_popular_tags();

	echo '<p class="popular-tags">';
	if ( is_gc_error( $api_tags ) ) {
		echo $api_tags->get_error_message();
	} else {
		// Set up the tags in a way which can be interpreted by gc_generate_tag_cloud().
		$tags = array();
		foreach ( (array) $api_tags as $tag ) {
			$url                  = self_admin_url( 'plugin-install.php?tab=search&type=tag&s=' . urlencode( $tag['name'] ) );
			$data                 = array(
				'link'  => esc_url( $url ),
				'name'  => $tag['name'],
				'slug'  => $tag['slug'],
				'id'    => sanitize_title_with_dashes( $tag['name'] ),
				'count' => $tag['count'],
			);
			$tags[ $tag['name'] ] = (object) $data;
		}
		echo gc_generate_tag_cloud(
			$tags,
			array(
				/* translators: %s: Number of plugins. */
				'single_text'   => __( '%d个插件' ),
				/* translators: %s: Number of plugins. */
				'multiple_text' => __( '%d个插件' ),
			)
		);
	}
	echo '</p><br class="clear" /></div>';
}

/**
 * Displays a search form for searching plugins.
 *
 *
 *
 *
 * @param bool $deprecated Not used.
 */
function install_search_form( $deprecated = true ) {
	$type = isset( $_REQUEST['type'] ) ? gc_unslash( $_REQUEST['type'] ) : 'term';
	$term = isset( $_REQUEST['s'] ) ? gc_unslash( $_REQUEST['s'] ) : '';
	?>
	<form class="search-form search-plugins" method="get">
		<input type="hidden" name="tab" value="search" />
		<label class="screen-reader-text" for="typeselector"><?php _e( '搜索插件：' ); ?></label>
		<select name="type" id="typeselector">
			<option value="term"<?php selected( 'term', $type ); ?>><?php _e( '关键字' ); ?></option>
			<option value="author"<?php selected( 'author', $type ); ?>><?php _e( '作者' ); ?></option>
			<option value="tag"<?php selected( 'tag', $type ); ?>><?php _ex( '标签', 'Plugin Installer' ); ?></option>
		</select>
		<label class="screen-reader-text" for="search-plugins"><?php _e( '搜索插件' ); ?></label>
		<input type="search" name="s" id="search-plugins" value="<?php echo esc_attr( $term ); ?>" class="gc-filter-search" placeholder="<?php esc_attr_e( '搜索插件…' ); ?>" />
		<?php submit_button( __( '搜索插件' ), 'hide-if-js', false, false, array( 'id' => 'search-submit' ) ); ?>
	</form>
	<?php
}

/**
 * Upload from zip
 *
 *
 */
function install_plugins_upload() {
	?>
<div class="upload-plugin">
	<p class="install-help"><?php _e( '如果您有.zip格式的插件文件，可以在这里通过上传安装它。' ); ?></p>
	<form method="post" enctype="multipart/form-data" class="gc-upload-form" action="<?php echo self_admin_url( 'update.php?action=upload-plugin' ); ?>">
		<?php gc_nonce_field( 'plugin-upload' ); ?>
		<label class="screen-reader-text" for="pluginzip"><?php _e( '插件zip文件' ); ?></label>
		<input type="file" id="pluginzip" name="pluginzip" accept=".zip" />
		<?php submit_button( __( '立即安装' ), '', 'install-plugin-submit', false ); ?>
	</form>
</div>
	<?php
}

/**
 * 显示专业版AppKey表单
 *
 */
function install_plugins_professionals_form() {
	$user   = get_user_option( 'gcorg_professionals' );
	$action = 'save_gcorg_username_' . get_current_user_id();
	?>
	<p><?php _e( '您可在此浏览在www.GeChiUI.com上购买的专业版插件。' ); ?></p>
	<form method="get">
		<input type="hidden" name="tab" value="professionals" />
		<p>
			<label for="username"><?php _e( '您的www.GeChiUI.com用户名：' ); ?></label>
			<input type="search" id="username" name="username" value="<?php echo esc_attr( $user['username'] ); ?>" />
            <label for="appkey"><?php _e( 'AppKey：' ); ?></label>
            <input type="search" id="appkey" name="appkey" value="<?php echo esc_attr( $user['appkey'] ); ?>" />
			<input type="submit" class="button" value="<?php esc_attr_e( '获取专业版列表' ); ?>" />
			<input type="hidden" id="gcorg-username-nonce" name="_gcnonce" value="<?php echo esc_attr( gc_create_nonce( $action ) ); ?>" />
		</p>
	</form>
	<?php
}

/**
 * Display plugin content based on plugin list.
 *
 *
 *
 * @global GC_List_Table $gc_list_table
 */
function display_plugins_table() {
	global $gc_list_table;

	switch ( current_filter() ) {
		case 'install_plugins_favorites':
			if ( empty( $_GET['user'] ) && ! get_user_option( 'gcorg_favorites' ) ) {
				return;
			}
			break;
		case 'install_plugins_all':
			echo '<p>' . __( '包含了所有的插件，包括子插件。' ) . '</p>';
			break;
		case 'install_plugins_beta':
			printf(
				/* translators: %s: URL to "Features as Plugins" page. */
				'<p>' . __( '您正在使用开发版本的GeChiUI。这些功能插件同样正被开发。<a href=\"%s\">了解更多</a>。' ) . '</p>',
				'https://make.gechiui.com/core/handbook/about/release-cycle/features-as-plugins/'
			);
			break;
	}

	?>
	<form id="plugin-filter" method="post">
		<?php $gc_list_table->display(); ?>
	</form>
	<?php
}

/**
 * Determine the status we can perform on a plugin.
 *
 *
 *
 * @param array|object $api  Data about the plugin retrieved from the API.
 * @param bool         $loop Optional. Disable further loops. Default false.
 * @return array {
 *     Plugin installation status data.
 *
 *     @type string $status  Status of a plugin. Could be one of 'install', 'update_available', 'latest_installed' or 'newer_installed'.
 *     @type string $url     Plugin installation URL.
 *     @type string $version The most recent version of the plugin.
 *     @type string $file    Plugin filename relative to the plugins directory.
 * }
 */
function install_plugin_install_status( $api, $loop = false ) {
	// This function is called recursively, $loop prevents further loops.
	if ( is_array( $api ) ) {
		$api = (object) $api;
	}

	// Default to a "new" plugin.
	$status      = 'install';
	$url         = false;
	$update_file = false;
	$version     = '';

	/*
	 * Check to see if this plugin is known to be installed,
	 * and has an update awaiting it.
	 */
	$update_plugins = get_site_transient( 'update_plugins' );
	if ( isset( $update_plugins->response ) ) {
		foreach ( (array) $update_plugins->response as $file => $plugin ) {
			if ( $plugin->slug === $api->slug ) {
				$status      = 'update_available';
				$update_file = $file;
				$version     = $plugin->new_version;
				if ( current_user_can( 'update_plugins' ) ) {
					$url = gc_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . $update_file ), 'upgrade-plugin_' . $update_file );
				}
				break;
			}
		}
	}

	if ( 'install' === $status ) {
		if ( is_dir( GC_PLUGIN_DIR . '/' . $api->slug ) ) {
			$installed_plugin = get_plugins( '/' . $api->slug );
			if ( empty( $installed_plugin ) ) {
				if ( current_user_can( 'install_plugins' ) ) {
					$url = gc_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $api->slug ), 'install-plugin_' . $api->slug );
				}
			} else {
				$key = array_keys( $installed_plugin );
				// Use the first plugin regardless of the name.
				// Could have issues for multiple plugins in one directory if they share different version numbers.
				$key = reset( $key );

				$update_file = $api->slug . '/' . $key;
				if ( version_compare( $api->version, $installed_plugin[ $key ]['Version'], '=' ) ) {
					$status = 'latest_installed';
				} elseif ( version_compare( $api->version, $installed_plugin[ $key ]['Version'], '<' ) ) {
					$status  = 'newer_installed';
					$version = $installed_plugin[ $key ]['Version'];
				} else {
					// If the above update check failed, then that probably means that the update checker has out-of-date information, force a refresh.
					if ( ! $loop ) {
						delete_site_transient( 'update_plugins' );
						gc_update_plugins();
						return install_plugin_install_status( $api, true );
					}
				}
			}
		} else {
			// "install" & no directory with that slug.
			if ( current_user_can( 'install_plugins' ) ) {
				$url = gc_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $api->slug ), 'install-plugin_' . $api->slug );
			}
		}
	}
	if ( isset( $_GET['from'] ) ) {
		$url .= '&amp;from=' . urlencode( gc_unslash( $_GET['from'] ) );
	}

	$file = $update_file;
	return compact( 'status', 'url', 'version', 'file' );
}

/**
 * Display plugin information in dialog box form.
 *
 *
 *
 * @global string $tab
 */
function install_plugin_information() {
	global $tab;

	if ( empty( $_REQUEST['plugin'] ) ) {
		return;
	}

	$api = plugins_api(
		'plugin_information',
		array(
			'slug' => gc_unslash( $_REQUEST['plugin'] ),
		)
	);

	if ( is_gc_error( $api ) ) {
		gc_die( $api );
	}

	$plugins_allowedtags = array(
		'a'          => array(
			'href'   => array(),
			'title'  => array(),
			'target' => array(),
		),
		'abbr'       => array( 'title' => array() ),
		'acronym'    => array( 'title' => array() ),
		'code'       => array(),
		'pre'        => array(),
		'em'         => array(),
		'strong'     => array(),
		'div'        => array( 'class' => array() ),
		'span'       => array( 'class' => array() ),
		'p'          => array(),
		'br'         => array(),
		'ul'         => array(),
		'ol'         => array(),
		'li'         => array(),
		'h1'         => array(),
		'h2'         => array(),
		'h3'         => array(),
		'h4'         => array(),
		'h5'         => array(),
		'h6'         => array(),
		'img'        => array(
			'src'   => array(),
			'class' => array(),
			'alt'   => array(),
		),
		'段落引用' => array( 'cite' => true ),
	);

	$plugins_section_titles = array(
		'description'  => _x( '描述', 'Plugin installer section title' ),
		'installation' => _x( '安装', 'Plugin installer section title' ),
		'faq'          => _x( '常见问题', 'Plugin installer section title' ),
		'screenshots'  => _x( '截图', 'Plugin installer section title' ),
		'changelog'    => _x( '修订历史', 'Plugin installer section title' ),
		'reviews'      => _x( '评价', 'Plugin installer section title' ),
		'other_notes'  => _x( '其他备注', 'Plugin installer section title' ),
	);

	// Sanitize HTML.
	foreach ( (array) $api->sections as $section_name => $content ) {
		$api->sections[ $section_name ] = gc_kses( $content, $plugins_allowedtags );
	}

	foreach ( array( 'version', 'author', 'requires', 'tested', 'homepage', 'downloaded', 'slug' ) as $key ) {
		if ( isset( $api->$key ) ) {
			$api->$key = gc_kses( $api->$key, $plugins_allowedtags );
		}
	}

	$_tab = esc_attr( $tab );

	// Default to the Description tab, Do not translate, API returns English.
	$section = isset( $_REQUEST['section'] ) ? gc_unslash( $_REQUEST['section'] ) : 'description';
	if ( empty( $section ) || ! isset( $api->sections[ $section ] ) ) {
		$section_titles = array_keys( (array) $api->sections );
		$section        = reset( $section_titles );
	}

	iframe_header( __( '安装插件' ) );

	$_with_banner = '';

	if ( ! empty( $api->banners ) && ( ! empty( $api->banners['low'] ) || ! empty( $api->banners['high'] ) ) ) {
		$_with_banner = 'with-banner';
		$low          = empty( $api->banners['low'] ) ? $api->banners['high'] : $api->banners['low'];
		$high         = empty( $api->banners['high'] ) ? $api->banners['low'] : $api->banners['high'];
		?>
		<style type="text/css">
			#plugin-information-title.with-banner {
				background-image: url( <?php echo esc_url( $low ); ?> );
			}
			@media only screen and ( -webkit-min-device-pixel-ratio: 1.5 ) {
				#plugin-information-title.with-banner {
					background-image: url( <?php echo esc_url( $high ); ?> );
				}
			}
		</style>
		<?php
	}

	echo '<div id="plugin-information-scrollable">';
	echo "<div id='{$_tab}-title' class='{$_with_banner}'><div class='vignette'></div><h2>{$api->name}</h2></div>";
	echo "<div id='{$_tab}-tabs' class='{$_with_banner}'>\n";

	foreach ( (array) $api->sections as $section_name => $content ) {
		if ( 'reviews' === $section_name && ( empty( $api->ratings ) || 0 === array_sum( (array) $api->ratings ) ) ) {
			continue;
		}

		if ( isset( $plugins_section_titles[ $section_name ] ) ) {
			$title = $plugins_section_titles[ $section_name ];
		} else {
			$title = ucwords( str_replace( '_', ' ', $section_name ) );
		}

		$class       = ( $section_name === $section ) ? ' class="current"' : '';
		$href        = add_query_arg(
			array(
				'tab'     => $tab,
				'section' => $section_name,
			)
		);
		$href        = esc_url( $href );
		$san_section = esc_attr( $section_name );
		echo "\t<a name='$san_section' href='$href' $class>$title</a>\n";
	}

	echo "</div>\n";

	?>
<div id="<?php echo $_tab; ?>-content" class='<?php echo $_with_banner; ?>'>
	<div class="fyi">
		<ul>
			<?php if ( ! empty( $api->version ) ) { ?>
				<li><strong><?php _e( '版本：' ); ?></strong> <?php echo $api->version; ?></li>
			<?php } if ( ! empty( $api->author ) ) { ?>
				<li><strong><?php _e( '作者：' ); ?></strong> <?php echo links_add_target( $api->author, '_blank' ); ?></li>
			<?php } if ( ! empty( $api->last_updated ) ) { ?>
				<li><strong><?php _e( '最近更新：' ); ?></strong>
					<?php
					/* translators: %s: Human-readable time difference. */
					printf( __( '%s前' ), human_time_diff( strtotime( $api->last_updated ) ) );
					?>
				</li>
			<?php } if ( ! empty( $api->requires ) ) { ?>
				<li>
					<strong><?php _e( '需要GeChiUI版本：' ); ?></strong>
					<?php
					/* translators: %s: Version number. */
					printf( __( '%s或更高' ), $api->requires );
					?>
				</li>
			<?php } if ( ! empty( $api->tested ) ) { ?>
				<li><strong><?php _e( '兼容至：' ); ?></strong> <?php echo $api->tested; ?></li>
			<?php } if ( ! empty( $api->requires_php ) ) { ?>
				<li>
					<strong><?php _e( '要求PHP版本：' ); ?></strong>
					<?php
					/* translators: %s: Version number. */
					printf( __( '%s或更高' ), $api->requires_php );
					?>
				</li>
			<?php } if ( isset( $api->active_installs ) ) { ?>
				<li><strong><?php _e( '已启用安装数：' ); ?></strong>
				<?php
				if ( $api->active_installs >= 1000000 ) {
					$active_installs_millions = floor( $api->active_installs / 1000000 );
					printf(
						/* translators: %s: Number of millions. */
						_nx( '超%s百万', '超%s百万', $active_installs_millions, 'Active plugin installations' ),
						number_format_i18n( $active_installs_millions )
					);
				} elseif ( $api->active_installs < 10 ) {
					_ex( '小于10', 'Active plugin installations' );
				} else {
					echo number_format_i18n( $api->active_installs ) . '+';
				}
				?>
				</li>
			<?php } if ( ! empty( $api->slug ) && empty( $api->external ) ) { ?>
				<li><a target="_blank" href="<?php echo esc_url( __( 'https://www.gechiui.com/plugins/' ) . $api->slug ); ?>/"><?php _e( 'www.GeChiUI.com插件页面 &#187;' ); ?></a></li>
			<?php } if ( ! empty( $api->homepage ) ) { ?>
				<li><a target="_blank" href="<?php echo esc_url( $api->homepage ); ?>"><?php _e( '插件主页 &#187;' ); ?></a></li>
			<?php } if ( ! empty( $api->donate_link ) && empty( $api->contributors ) ) { ?>
				<li><a target="_blank" href="<?php echo esc_url( $api->donate_link ); ?>"><?php _e( '捐助该插件 &#187;' ); ?></a></li>
			<?php } ?>
		</ul>
		<?php if ( ! empty( $api->rating ) ) { ?>
			<h3><?php _e( '平均评级' ); ?></h3>
			<?php
			gc_star_rating(
				array(
					'rating' => $api->rating,
					'type'   => 'percent',
					'number' => $api->num_ratings,
				)
			);
			?>
			<p aria-hidden="true" class="fyi-description">
				<?php
				printf(
					/* translators: %s: Number of ratings. */
					_n( '（基于%s次评级）', '（基于%s次评级）', $api->num_ratings ),
					number_format_i18n( $api->num_ratings )
				);
				?>
			</p>
			<?php
		}

		if ( ! empty( $api->ratings ) && array_sum( (array) $api->ratings ) > 0 ) {
			?>
			<h3><?php _e( '评价' ); ?></h3>
			<p class="fyi-description"><?php _e( '在www.GeChiUI.com阅读所有评价或撰写您的评价！' ); ?></p>
			<?php
			foreach ( $api->ratings as $key => $ratecount ) {
				// Avoid div-by-zero.
				$_rating    = $api->num_ratings ? ( $ratecount / $api->num_ratings ) : 0;
				$aria_label = esc_attr(
					sprintf(
						/* translators: 1: Number of stars (used to determine singular/plural), 2: Number of reviews. */
						_n(
							'%1$d星的评价：%2$s。在新标签页中打开。',
							'%1$d星的评价：%2$s。在新标签页中打开。',
							$key
						),
						$key,
						number_format_i18n( $ratecount )
					)
				);
				?>
				<div class="counter-container">
						<span class="counter-label">
							<?php
							printf(
								'<a href="%s" target="_blank" aria-label="%s">%s</a>',
								"https://www.gechiui.com/support/plugin/{$api->slug}/reviews/?filter={$key}",
								$aria_label,
								/* translators: %s: Number of stars. */
								sprintf( _n( '%d星', '%d星', $key ), $key )
							);
							?>
						</span>
						<span class="counter-back">
							<span class="counter-bar" style="width: <?php echo 92 * $_rating; ?>px;"></span>
						</span>
					<span class="counter-count" aria-hidden="true"><?php echo number_format_i18n( $ratecount ); ?></span>
				</div>
				<?php
			}
		}
		if ( ! empty( $api->contributors ) ) {
			?>
			<h3><?php _e( '贡献者' ); ?></h3>
			<ul class="contributors">
				<?php
				foreach ( (array) $api->contributors as $contrib_username => $contrib_details ) {
					$contrib_name = $contrib_details['display_name'];
					if ( ! $contrib_name ) {
						$contrib_name = $contrib_username;
					}
					$contrib_name = esc_html( $contrib_name );

					$contrib_profile = esc_url( $contrib_details['profile'] );
					$contrib_avatar  = esc_url( add_query_arg( 's', '36', $contrib_details['avatar'] ) );

					echo "<li><a href='{$contrib_profile}' target='_blank'><img src='{$contrib_avatar}' width='18' height='18' alt='' />{$contrib_name}</a></li>";
				}
				?>
			</ul>
					<?php if ( ! empty( $api->donate_link ) ) { ?>
				<a target="_blank" href="<?php echo esc_url( $api->donate_link ); ?>"><?php _e( '捐助该插件 &#187;' ); ?></a>
			<?php } ?>
				<?php } ?>
	</div>
	<div id="section-holder">
	<?php
	$requires_php = isset( $api->requires_php ) ? $api->requires_php : null;
	$requires_gc  = isset( $api->requires ) ? $api->requires : null;

	$compatible_php = is_php_version_compatible( $requires_php );
	$compatible_gc  = is_gc_version_compatible( $requires_gc );
	$tested_gc      = ( empty( $api->tested ) || version_compare( get_bloginfo( 'version' ), $api->tested, '<=' ) );

	if ( ! $compatible_php ) {
		echo '<div class="notice notice-error notice-alt"><p>';
		_e( '<strong>错误：</strong>此插件<strong>需要新版本的PHP</strong>。' );
		if ( current_user_can( 'update_php' ) ) {
			printf(
				/* translators: %s: URL to Update PHP page. */
				' ' . __( '<a href="%s" target="_blank">点击此处查阅如何更新PHP</a>。' ),
				esc_url( gc_get_update_php_url() )
			);

			gc_update_php_annotation( '</p><p><em>', '</em>' );
		} else {
			echo '</p>';
		}
		echo '</div>';
	}

	if ( ! $tested_gc ) {
		echo '<div class="notice notice-warning notice-alt"><p>';
		_e( '<strong>警告：</strong>此插件没有与您当前版本的GeChiUI进行<strong>兼容性测试</strong>。' );
		echo '</p></div>';
	} elseif ( ! $compatible_gc ) {
		echo '<div class="notice notice-error notice-alt"><p>';
		_e( '<strong>错误：</strong>此插件<strong>需要新版本的GeChiUI</strong>。' );
		if ( current_user_can( 'update_core' ) ) {
			printf(
				/* translators: %s: URL to GeChiUI Updates screen. */
				' ' . __( '<a href="%s" target="_parent">点击此处更新GeChiUI</a>。' ),
				self_admin_url( 'update-core.php' )
			);
		}
		echo '</p></div>';
	}

	foreach ( (array) $api->sections as $section_name => $content ) {
		$content = links_add_base_url( $content, 'https://www.gechiui.com/plugins/' . $api->slug . '/' );
		$content = links_add_target( $content, '_blank' );

		$san_section = esc_attr( $section_name );

		$display = ( $section_name === $section ) ? 'block' : 'none';

		echo "\t<div id='section-{$san_section}' class='section' style='display: {$display};'>\n";
		echo $content;
		echo "\t</div>\n";
	}
	echo "</div>\n";
	echo "</div>\n";
	echo "</div>\n"; // #plugin-information-scrollable
	echo "<div id='$tab-footer'>\n";
	if ( ! empty( $api->download_link ) && ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) ) {
		$status = install_plugin_install_status( $api );
		switch ( $status['status'] ) {
			case 'install':
				if ( $status['url'] ) {
					if ( $compatible_php && $compatible_gc ) {
						echo '<a data-slug="' . esc_attr( $api->slug ) . '" id="plugin_install_from_iframe" class="button button-primary right" href="' . $status['url'] . '" target="_parent">' . __( '立即安装' ) . '</a>';
					} else {
						printf(
							'<button type="button" class="button button-primary button-disabled right" disabled="disabled">%s</button>',
							_x( '无法安装', 'plugin' )
						);
					}
				}
				break;
			case 'update_available':
				if ( $status['url'] ) {
					if ( $compatible_php ) {
						echo '<a data-slug="' . esc_attr( $api->slug ) . '" data-plugin="' . esc_attr( $status['file'] ) . '" id="plugin_update_from_iframe" class="button button-primary right" href="' . $status['url'] . '" target="_parent">' . __( '立即安装更新' ) . '</a>';
					} else {
						printf(
							'<button type="button" class="button button-primary button-disabled right" disabled="disabled">%s</button>',
							_x( '未能更新', 'plugin' )
						);
					}
				}
				break;
			case 'newer_installed':
				/* translators: %s: Plugin version. */
				echo '<a class="button button-primary right disabled">' . sprintf( __( '新版本（%s）已安装' ), esc_html( $status['version'] ) ) . '</a>';
				break;
			case 'latest_installed':
				echo '<a class="button button-primary right disabled">' . __( '已安装最新版本' ) . '</a>';
				break;
		}
	}
	echo "</div>\n";

	iframe_footer();
	exit;
}
