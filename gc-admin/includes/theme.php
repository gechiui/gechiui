<?php
/**
 * GeChiUI Theme Administration API
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Removes a theme.
 *
 * @global GC_Filesystem_Base $gc_filesystem GeChiUI filesystem subclass.
 *
 * @param string $stylesheet Stylesheet of the theme to delete.
 * @param string $redirect   Redirect to page when complete.
 * @return bool|null|GC_Error True on success, false if `$stylesheet` is empty, GC_Error on failure.
 *                            Null if filesystem credentials are required to proceed.
 */
function delete_theme( $stylesheet, $redirect = '' ) {
	global $gc_filesystem;

	if ( empty( $stylesheet ) ) {
		return false;
	}

	if ( empty( $redirect ) ) {
		$redirect = gc_nonce_url( 'themes.php?action=delete&stylesheet=' . urlencode( $stylesheet ), 'delete-theme_' . $stylesheet );
	}

	ob_start();
	$credentials = request_filesystem_credentials( $redirect );
	$data        = ob_get_clean();

	if ( false === $credentials ) {
		if ( ! empty( $data ) ) {
			require_once ABSPATH . 'gc-admin/admin-header.php';
			echo $data;
			require_once ABSPATH . 'gc-admin/admin-footer.php';
			exit;
		}
		return;
	}

	if ( ! GC_Filesystem( $credentials ) ) {
		ob_start();
		// Failed to connect. Error and request again.
		request_filesystem_credentials( $redirect, '', true );
		$data = ob_get_clean();

		if ( ! empty( $data ) ) {
			require_once ABSPATH . 'gc-admin/admin-header.php';
			echo $data;
			require_once ABSPATH . 'gc-admin/admin-footer.php';
			exit;
		}
		return;
	}

	if ( ! is_object( $gc_filesystem ) ) {
		return new GC_Error( 'fs_unavailable', __( '无法访问文件系统。' ) );
	}

	if ( is_gc_error( $gc_filesystem->errors ) && $gc_filesystem->errors->has_errors() ) {
		return new GC_Error( 'fs_error', __( '文件系统错误。' ), $gc_filesystem->errors );
	}

	// Get the base plugin folder.
	$themes_dir = $gc_filesystem->gc_themes_dir();
	if ( empty( $themes_dir ) ) {
		return new GC_Error( 'fs_no_themes_dir', __( '无法找到GeChiUI主题目录。' ) );
	}

	/**
	 * Fires immediately before a theme deletion attempt.
	 *
	 * @since 5.8.0
	 *
	 * @param string $stylesheet Stylesheet of the theme to delete.
	 */
	do_action( 'delete_theme', $stylesheet );

	$themes_dir = trailingslashit( $themes_dir );
	$theme_dir  = trailingslashit( $themes_dir . $stylesheet );
	$deleted    = $gc_filesystem->delete( $theme_dir, true );

	/**
	 * Fires immediately after a theme deletion attempt.
	 *
	 * @since 5.8.0
	 *
	 * @param string $stylesheet Stylesheet of the theme to delete.
	 * @param bool   $deleted    Whether the theme deletion was successful.
	 */
	do_action( 'deleted_theme', $stylesheet, $deleted );

	if ( ! $deleted ) {
		return new GC_Error(
			'could_not_remove_theme',
			/* translators: %s: Theme name. */
			sprintf( __( '无法完全移除主题%s。' ), $stylesheet )
		);
	}

	$theme_translations = gc_get_installed_translations( 'themes' );

	// Remove language files, silently.
	if ( ! empty( $theme_translations[ $stylesheet ] ) ) {
		$translations = $theme_translations[ $stylesheet ];

		foreach ( $translations as $translation => $data ) {
			$gc_filesystem->delete( GC_LANG_DIR . '/themes/' . $stylesheet . '-' . $translation . '.po' );
			$gc_filesystem->delete( GC_LANG_DIR . '/themes/' . $stylesheet . '-' . $translation . '.mo' );

			$json_translation_files = glob( GC_LANG_DIR . '/themes/' . $stylesheet . '-' . $translation . '-*.json' );
			if ( $json_translation_files ) {
				array_map( array( $gc_filesystem, 'delete' ), $json_translation_files );
			}
		}
	}

	// Remove the theme from allowed themes on the network.
	if ( is_multisite() ) {
		GC_Theme::network_disable_theme( $stylesheet );
	}

	// Force refresh of theme update information.
	delete_site_transient( 'update_themes' );

	return true;
}

/**
 * Gets the page templates available in this theme.
 * Added the `$post_type` parameter.
 *
 * @param GC_Post|null $post      Optional. The post being edited, provided for context.
 * @param string       $post_type Optional. Post type to get the templates for. Default 'page'.
 * @return string[] Array of template file names keyed by the template header name.
 */
function get_page_templates( $post = null, $post_type = 'page' ) {
	return array_flip( gc_get_theme()->get_page_templates( $post, $post_type ) );
}

/**
 * Tidies a filename for url display by the theme file editor.
 *
 * @access private
 *
 * @param string $fullpath Full path to the theme file
 * @param string $containingfolder Path of the theme parent folder
 * @return string
 */
function _get_template_edit_filename( $fullpath, $containingfolder ) {
	return str_replace( dirname( dirname( $containingfolder ) ), '', $fullpath );
}

/**
 * Check if there is an update for a theme available.
 *
 * Will display link, if there is an update available.
 *
 * @since 2.7.0
 *
 * @see get_theme_update_available()
 *
 * @param GC_Theme $theme Theme data object.
 */
function theme_update_available( $theme ) {
	echo get_theme_update_available( $theme );
}

/**
 * Retrieves the update link if there is a theme update available.
 *
 * Will return a link if there is an update available.
 *
 * @since 3.8.0
 *
 * @param GC_Theme $theme GC_Theme object.
 * @return string|false HTML for the update link, or false if invalid info was passed.
 */
function get_theme_update_available( $theme ) {
	static $themes_update = null;

	if ( ! current_user_can( 'update_themes' ) ) {
		return false;
	}

	if ( ! isset( $themes_update ) ) {
		$themes_update = get_site_transient( 'update_themes' );
	}

	if ( ! ( $theme instanceof GC_Theme ) ) {
		return false;
	}

	$stylesheet = $theme->get_stylesheet();

	$html = '';

	if ( isset( $themes_update->response[ $stylesheet ] ) ) {
		$update      = $themes_update->response[ $stylesheet ];
		$theme_name  = $theme->display( 'Name' );
		$details_url = add_query_arg(
			array(
				'TB_iframe' => 'true',
				'width'     => 1024,
				'height'    => 800,
			),
			$update['url']
		); // Theme browser inside GC? Replace this. Also, theme preview JS will override this on the available list.
		$update_url  = gc_nonce_url( admin_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode( $stylesheet ) ), 'upgrade-theme_' . $stylesheet );

		if ( ! is_multisite() ) {
			if ( ! current_user_can( 'update_themes' ) ) {
				$html = sprintf(
					/* translators: 1: Theme name, 2: Theme details URL, 3: Additional link attributes, 4: Version number. */
					'<p><strong>' . __( '%1$s有新版本可用。<a href="%2$s" %3$s>查看版本%4$s详情</a>。' ) . '</strong></p>',
					$theme_name,
					esc_url( $details_url ),
					sprintf(
						'class="thickbox open-plugin-details-modal" aria-label="%s"',
						/* translators: 1: Theme name, 2: Version number. */
						esc_attr( sprintf( __( '查看%1$s版本%2$s详情' ), $theme_name, $update['new_version'] ) )
					),
					$update['new_version']
				);
			} elseif ( empty( $update['package'] ) ) {
				$html = sprintf(
					/* translators: 1: Theme name, 2: Theme details URL, 3: Additional link attributes, 4: Version number. */
					'<p><strong>' . __( '%1$s有新版本可用。<a href="%2$s" %3$s>查看版本%4$s详情</a>。<em>自动更新对此主题不可用。</em>' ) . '</strong></p>',
					$theme_name,
					esc_url( $details_url ),
					sprintf(
						'class="thickbox open-plugin-details-modal" aria-label="%s"',
						/* translators: 1: Theme name, 2: Version number. */
						esc_attr( sprintf( __( '查看%1$s版本%2$s详情' ), $theme_name, $update['new_version'] ) )
					),
					$update['new_version']
				);
			} else {
				$html = sprintf(
					/* translators: 1: Theme name, 2: Theme details URL, 3: Additional link attributes, 4: Version number, 5: Update URL, 6: Additional link attributes. */
					'<p><strong>' . __( '%1$s有新版本可用。<a href="%2$s" %3$s>查看版本%4$s详情</a>或<a href="%5$s" %6$s>立即更新</a>。' ) . '</strong></p>',
					$theme_name,
					esc_url( $details_url ),
					sprintf(
						'class="thickbox open-plugin-details-modal" aria-label="%s"',
						/* translators: 1: Theme name, 2: Version number. */
						esc_attr( sprintf( __( '查看%1$s版本%2$s详情' ), $theme_name, $update['new_version'] ) )
					),
					$update['new_version'],
					$update_url,
					sprintf(
						'aria-label="%s" id="update-theme" data-slug="%s"',
						/* translators: %s: Theme name. */
						esc_attr( sprintf( _x( '立即更新%s', 'theme' ), $theme_name ) ),
						$stylesheet
					)
				);
			}
		}
	}

	return $html;
}

/**
 * Retrieves list of GeChiUI theme features (aka theme tags).
 * Added 'Gray' color and '页眉特色图片', '特色图片',
 *              '全宽模板', and '文章形式' features. Added 'Flexible Header' feature.
 * @since 3.8.0 Renamed 'Width' filter to 'Layout'.
 * @since 3.8.0 Renamed 'Fixed Width' and 'Flexible Width' options
 *              to '固定布局' and '流动布局'.
 * @since 3.8.0 Added '无障碍友好' feature and '响应式布局' option.
 * @since 3.9.0 Combined 'Layout' and 'Columns' filters. Removed 'Colors' filter. Added '网格布局' option.
 *              Removed '固定布局', '流动布局', and '响应式布局' options. Added '自定义 logo' and '页脚小工具' features.
 *              Removed 'Blavatar' feature. Added 'Blog', 'E-Commerce', 'Education', 'Entertainment', '食品和饮料',
 *              'Holiday', 'News', 'Photography', and 'Portfolio' subjects.
 *              Removed 'Photoblogging' and 'Seasonal' subjects. Reordered the filters from 'Layout', 'Features', 'Subject'
 *              to 'Subject', 'Features', 'Layout'. Removed 'BuddyPress', 'Custom Menu', 'Flexible Header',
 *              'Front Page Posting', 'Microformats', 'RTL Language Support',
 *              'Threaded Comments', and 'Translation Ready' features.
 * @since 5.5.0 Added '区块编辑器样板', '区块编辑器样式',
 *              and 'Full Site Editing' features.
 * @since 5.5.0 Added '宽幅区块' layout option.
 * @since 5.8.1 Added '模板编辑' feature.
 * @since 6.1.1 Replaced 'Full Site Editing' feature name with '系统编辑器'.
 * @since 6.2.0 Added '样式变体' feature.
 *
 * @param bool $api Optional. Whether try to fetch tags from the www.GeChiUI.com API. Defaults to true.
 * @return array Array of features keyed by category with translations keyed by slug.
 */
function get_theme_feature_list( $api = true ) {
	// Hard-coded list is used if API is not accessible.
	$features = array(

		__( '主题' )  => array(
			'blog'           => __( '博客' ),
			'e-commerce'     => __( '电子商务' ),
			'education'      => __( '教育' ),
			'entertainment'  => __( '娱乐' ),
			'food-and-drink' => __( '食品和饮料' ),
			'holiday'        => __( '节日' ),
			'news'           => __( '新闻' ),
			'photography'    => __( '摄影' ),
			'portfolio'      => __( '作品集' ),
		),

		__( '特色' ) => array(
			'accessibility-ready'   => __( '无障碍友好' ),
			'block-patterns'        => __( '区块编辑器样板' ),
			'block-styles'          => __( '区块编辑器样式' ),
			'custom-background'     => __( '自定义背景' ),
			'custom-colors'         => __( '自定义颜色' ),
			'custom-header'         => __( '自定义页眉' ),
			'custom-logo'           => __( '自定义 logo' ),
			'editor-style'          => __( '编辑器样式支持' ),
			'featured-image-header' => __( '页眉特色图片' ),
			'featured-images'       => __( '特色图片' ),
			'footer-widgets'        => __( '页脚小工具' ),
			'full-site-editing'     => __( '系统编辑器' ),
			'full-width-template'   => __( '全宽模板' ),
			'post-formats'          => __( '文章形式' ),
			'sticky-post'           => __( '文章置顶' ),
			'style-variations'      => __( '样式变体' ),
			'template-editing'      => __( '模板编辑' ),
			'theme-options'         => __( '主题选项' ),
		),

		__( '布局' )   => array(
			'grid-layout'   => __( '网格布局' ),
			'one-column'    => __( '单栏' ),
			'two-columns'   => __( '双栏' ),
			'three-columns' => __( '三栏' ),
			'four-columns'  => __( '四栏' ),
			'left-sidebar'  => __( '边栏在左侧' ),
			'right-sidebar' => __( '边栏在右侧' ),
			'wide-blocks'   => __( '宽幅区块' ),
		),

	);

	if ( ! $api || ! current_user_can( 'install_themes' ) ) {
		return $features;
	}

	$feature_list = get_site_transient( 'gcorg_theme_feature_list' );
	if ( ! $feature_list ) {
		set_site_transient( 'gcorg_theme_feature_list', array(), 3 * HOUR_IN_SECONDS );
	}

	if ( ! $feature_list ) {
		$feature_list = themes_api( 'feature_list', array() );
		if ( is_gc_error( $feature_list ) ) {
			return $features;
		}
	}

	if ( ! $feature_list ) {
		return $features;
	}

	set_site_transient( 'gcorg_theme_feature_list', $feature_list, 3 * HOUR_IN_SECONDS );

	$category_translations = array(
		'Layout'   => __( '布局' ),
		'Features' => __( '特色' ),
		'Subject'  => __( '主题' ),
	);

	$gcorg_features = array();

	// Loop over the gc.org canonical list and apply translations.
	foreach ( (array) $feature_list as $feature_category => $feature_items ) {
		if ( isset( $category_translations[ $feature_category ] ) ) {
			$feature_category = $category_translations[ $feature_category ];
		}

		$gcorg_features[ $feature_category ] = array();

		foreach ( $feature_items as $feature ) {
			if ( isset( $features[ $feature_category ][ $feature ] ) ) {
				$gcorg_features[ $feature_category ][ $feature ] = $features[ $feature_category ][ $feature ];
			} else {
				$gcorg_features[ $feature_category ][ $feature ] = $feature;
			}
		}
	}

	return $gcorg_features;
}

/**
 * Retrieves theme installer pages from the www.GeChiUI.com Themes API.
 *
 * It is possible for a theme to override the Themes API result with three
 * filters. Assume this is for themes, which can extend on the Theme Info to
 * offer more choices. This is very powerful and must be used with care, when
 * overriding the filters.
 *
 * The first filter, {@see 'themes_api_args'}, is for the args and gives the action
 * as the second parameter. The hook for {@see 'themes_api_args'} must ensure that
 * an object is returned.
 *
 * The second filter, {@see 'themes_api'}, allows a plugin to override the www.GeChiUI.com
 * Theme API entirely. If `$action` is 'query_themes', 'theme_information', or 'feature_list',
 * an object MUST be passed. If `$action` is 'hot_tags', an array should be passed.
 *
 * Finally, the third filter, {@see 'themes_api_result'}, makes it possible to filter the
 * response object or array, depending on the `$action` type.
 *
 * Supported arguments per action:
 *
 * | Argument Name      | 'query_themes' | 'theme_information' | 'hot_tags' | 'feature_list'   |
 * | -------------------| :------------: | :-----------------: | :--------: | :--------------: |
 * | `$slug`            | No             |  Yes                | No         | No               |
 * | `$per_page`        | Yes            |  No                 | No         | No               |
 * | `$page`            | Yes            |  No                 | No         | No               |
 * | `$number`          | No             |  No                 | Yes        | No               |
 * | `$search`          | Yes            |  No                 | No         | No               |
 * | `$tag`             | Yes            |  No                 | No         | No               |
 * | `$author`          | Yes            |  No                 | No         | No               |
 * | `$user`            | Yes            |  No                 | No         | No               |
 * | `$browse`          | Yes            |  No                 | No         | No               |
 * | `$locale`          | Yes            |  Yes                | No         | No               |
 * | `$fields`          | Yes            |  Yes                | No         | No               |
 *
 * @param string       $action API action to perform: 'query_themes', 'theme_information',
 *                             'hot_tags' or 'feature_list'.
 * @param array|object $args   {
 *     Optional. Array or object of arguments to serialize for the Themes API. Default empty array.
 *
 *     @type string  $slug     The theme slug. Default empty.
 *     @type int     $per_page Number of themes per page. Default 24.
 *     @type int     $page     Number of current page. Default 1.
 *     @type int     $number   Number of tags to be queried.
 *     @type string  $search   A search term. Default empty.
 *     @type string  $tag      Tag to filter themes. Default empty.
 *     @type string  $author   Username of an author to filter themes. Default empty.
 *     @type string  $user     Username to query for their favorites. Default empty.
 *     @type string  $browse   Browse view: 'featured', 'popular', 'updated', 'favorites'.
 *     @type string  $locale   Locale to provide context-sensitive results. Default is the value of get_locale().
 *     @type array   $fields   {
 *         Array of fields which should or should not be returned.
 *
 *         @type bool $description        Whether to return the theme full description. Default false.
 *         @type bool $sections           Whether to return the theme readme sections: description, installation,
 *                                        FAQ, screenshots, other notes, and changelog. Default false.
 *         @type bool $rating             Whether to return the rating in percent and total number of ratings.
 *                                        Default false.
 *         @type bool $ratings            Whether to return the number of rating for each star (1-5). Default false.
 *         @type bool $downloaded         Whether to return the download count. Default false.
 *         @type bool $downloadlink       Whether to return the download link for the package. Default false.
 *         @type bool $last_updated       Whether to return the date of the last update. Default false.
 *         @type bool $tags               Whether to return the assigned tags. Default false.
 *         @type bool $homepage           Whether to return the theme homepage link. Default false.
 *         @type bool $screenshots        Whether to return the screenshots. Default false.
 *         @type int  $screenshot_count   Number of screenshots to return. Default 1.
 *         @type bool $screenshot_url     Whether to return the URL of the first screenshot. Default false.
 *         @type bool $photon_screenshots Whether to return the screenshots via Photon. Default false.
 *         @type bool $template           Whether to return the slug of the parent theme. Default false.
 *         @type bool $parent             Whether to return the slug, name and homepage of the parent theme. Default false.
 *         @type bool $versions           Whether to return the list of all available versions. Default false.
 *         @type bool $theme_url          Whether to return theme's URL. Default false.
 *         @type bool $extended_author    Whether to return nicename or nicename and display name. Default false.
 *     }
 * }
 * @return object|array|GC_Error Response object or array on success, GC_Error on failure. See the
 *         {@link https://developer.gechiui.com/reference/functions/themes_api/ function reference article}
 *         for more information on the make-up of possible return objects depending on the value of `$action`.
 */
function themes_api( $action, $args = array() ) {
	// Include an unmodified $gc_version.
	require ABSPATH . GCINC . '/version.php';

	if ( is_array( $args ) ) {
		$args = (object) $args;
	}

	if ( 'query_themes' === $action ) {
		if ( ! isset( $args->per_page ) ) {
			$args->per_page = 24;
		}
	}

	if ( ! isset( $args->locale ) ) {
		$args->locale = get_user_locale();
	}

	if ( ! isset( $args->gc_version ) ) {
		$args->gc_version = substr( $gc_version, 0, 3 ); // x.y
	}

	/**
	 * Filters arguments used to query for installer pages from the www.GeChiUI.com Themes API.
	 *
	 * Important: An object MUST be returned to this filter.
	 *
	 *
	 * @param object $args   Arguments used to query for installer pages from the www.GeChiUI.com Themes API.
	 * @param string $action Requested action. Likely values are 'theme_information',
	 *                       'feature_list', or 'query_themes'.
	 */
	$args = apply_filters( 'themes_api_args', $args, $action );

	/**
	 * Filters whether to override the www.GeChiUI.com Themes API.
	 *
	 * Returning a non-false value will effectively short-circuit the www.GeChiUI.com API request.
	 *
	 * If `$action` is 'query_themes', 'theme_information', or 'feature_list', an object MUST
	 * be passed. If `$action` is 'hot_tags', an array should be passed.
	 *
	 *
	 * @param false|object|array $override Whether to override the www.GeChiUI.com Themes API. Default false.
	 * @param string             $action   Requested action. Likely values are 'theme_information',
	 *                                    'feature_list', or 'query_themes'.
	 * @param object             $args     Arguments used to query for installer pages from the Themes API.
	 */
	$res = apply_filters( 'themes_api', false, $action, $args );

	if ( ! $res ) {
		$url = 'http://api.gechiui.com/themes/info/1.2/';
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
			if ( ! gc_doing_ajax() ) {
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
				'themes_api_failed',
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
					'themes_api_failed',
					sprintf(
						/* translators: %s: Support forums URL. */
						__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
						__( 'https://www.gechiui.com/support/forums/' )
					),
					gc_remote_retrieve_body( $request )
				);
			}

			if ( isset( $res->error ) ) {
				$res = new GC_Error( 'themes_api_failed', $res->error );
			}
		}

		if ( ! is_gc_error( $res ) ) {
			// Back-compat for info/1.2 API, upgrade the theme objects in query_themes to objects.
			if ( 'query_themes' === $action ) {
				foreach ( $res->themes as $i => $theme ) {
					$res->themes[ $i ] = (object) $theme;
				}
			}

			// Back-compat for info/1.2 API, downgrade the feature_list result back to an array.
			if ( 'feature_list' === $action ) {
				$res = (array) $res;
			}
		}
	}

	/**
	 * Filters the returned www.GeChiUI.com Themes API response.
	 *
	 *
	 * @param array|stdClass|GC_Error $res    www.GeChiUI.com Themes API response.
	 * @param string                  $action Requested action. Likely values are 'theme_information',
	 *                                        'feature_list', or 'query_themes'.
	 * @param stdClass                $args   Arguments used to query for installer pages from the www.GeChiUI.com Themes API.
	 */
	return apply_filters( 'themes_api_result', $res, $action, $args );
}

/**
 * Prepares themes for JavaScript.
 *
 * @since 3.8.0
 *
 * @param GC_Theme[] $themes Optional. Array of theme objects to prepare.
 *                           Defaults to all allowed themes.
 *
 * @return array An associative array of theme data, sorted by name.
 */
function gc_prepare_themes_for_js( $themes = null ) {
	$current_theme = get_stylesheet();

	/**
	 * Filters theme data before it is prepared for JavaScript.
	 *
	 * Passing a non-empty array will result in gc_prepare_themes_for_js() returning
	 * early with that value instead.
	 *
	 * @since 4.2.0
	 *
	 * @param array           $prepared_themes An associative array of theme data. Default empty array.
	 * @param GC_Theme[]|null $themes          An array of theme objects to prepare, if any.
	 * @param string          $current_theme   The active theme slug.
	 */
	$prepared_themes = (array) apply_filters( 'pre_prepare_themes_for_js', array(), $themes, $current_theme );

	if ( ! empty( $prepared_themes ) ) {
		return $prepared_themes;
	}

	// Make sure the active theme is listed first.
	$prepared_themes[ $current_theme ] = array();

	if ( null === $themes ) {
		$themes = gc_get_themes( array( 'allowed' => true ) );
		if ( ! isset( $themes[ $current_theme ] ) ) {
			$themes[ $current_theme ] = gc_get_theme();
		}
	}

	$updates    = array();
	$no_updates = array();
	if ( ! is_multisite() && current_user_can( 'update_themes' ) ) {
		$updates_transient = get_site_transient( 'update_themes' );
		if ( isset( $updates_transient->response ) ) {
			$updates = $updates_transient->response;
		}
		if ( isset( $updates_transient->no_update ) ) {
			$no_updates = $updates_transient->no_update;
		}
	}

	GC_Theme::sort_by_name( $themes );

	$parents = array();

	$auto_updates = (array) get_site_option( 'auto_update_themes', array() );

	foreach ( $themes as $theme ) {
		$slug         = $theme->get_stylesheet();
		$encoded_slug = urlencode( $slug );

		$parent = false;
		if ( $theme->parent() ) {
			$parent           = $theme->parent();
			$parents[ $slug ] = $parent->get_stylesheet();
			$parent           = $parent->display( 'Name' );
		}

		$customize_action = null;

		$can_edit_theme_options = current_user_can( 'edit_theme_options' );
		$can_customize          = current_user_can( 'customize' );
		$is_block_theme         = $theme->is_block_theme();

		if ( $is_block_theme && $can_edit_theme_options ) {
			$customize_action = admin_url( 'site-editor.php' );
			if ( $current_theme !== $slug ) {
				$customize_action = add_query_arg( 'gc_theme_preview', $slug, $customize_action );
			}
		} elseif ( ! $is_block_theme && $can_customize && $can_edit_theme_options ) {
			$customize_action = gc_customize_url( $slug );
		}
		if ( null !== $customize_action ) {
			$customize_action = add_query_arg(
				array(
					'return' => urlencode( sanitize_url( remove_query_arg( gc_removable_query_args(), gc_unslash( $_SERVER['REQUEST_URI'] ) ) ) ),
				),
				$customize_action
			);
			$customize_action = esc_url( $customize_action );
		}

		$update_requires_gc  = isset( $updates[ $slug ]['requires'] ) ? $updates[ $slug ]['requires'] : null;
		$update_requires_php = isset( $updates[ $slug ]['requires_php'] ) ? $updates[ $slug ]['requires_php'] : null;

		$auto_update        = in_array( $slug, $auto_updates, true );
		$auto_update_action = $auto_update ? 'disable-auto-update' : 'enable-auto-update';

		if ( isset( $updates[ $slug ] ) ) {
			$auto_update_supported      = true;
			$auto_update_filter_payload = (object) $updates[ $slug ];
		} elseif ( isset( $no_updates[ $slug ] ) ) {
			$auto_update_supported      = true;
			$auto_update_filter_payload = (object) $no_updates[ $slug ];
		} else {
			$auto_update_supported = false;
			/*
			 * Create the expected payload for the auto_update_theme filter, this is the same data
			 * as contained within $updates or $no_updates but used when the Theme is not known.
			 */
			$auto_update_filter_payload = (object) array(
				'theme'        => $slug,
				'new_version'  => $theme->get( 'Version' ),
				'url'          => '',
				'package'      => '',
				'requires'     => $theme->get( 'RequiresGC' ),
				'requires_php' => $theme->get( 'RequiresPHP' ),
			);
		}

		$auto_update_forced = gc_is_auto_update_forced_for_item( 'theme', null, $auto_update_filter_payload );

		$prepared_themes[ $slug ] = array(
			'id'             => $slug,
			'name'           => $theme->display( 'Name' ),
			'screenshot'     => array( $theme->get_screenshot() ), // @todo Multiple screenshots.
			'description'    => $theme->display( 'Description' ),
			'author'         => $theme->display( 'Author', false, true ),
			'authorAndUri'   => $theme->display( 'Author' ),
			'tags'           => $theme->display( 'Tags' ),
			'version'        => $theme->get( 'Version' ),
			'compatibleGC'   => is_gc_version_compatible( $theme->get( 'RequiresGC' ) ),
			'compatiblePHP'  => is_php_version_compatible( $theme->get( 'RequiresPHP' ) ),
			'updateResponse' => array(
				'compatibleGC'  => is_gc_version_compatible( $update_requires_gc ),
				'compatiblePHP' => is_php_version_compatible( $update_requires_php ),
			),
			'parent'         => $parent,
			'active'         => $slug === $current_theme,
			'hasUpdate'      => isset( $updates[ $slug ] ),
			'hasPackage'     => isset( $updates[ $slug ] ) && ! empty( $updates[ $slug ]['package'] ),
			'update'         => get_theme_update_available( $theme ),
			'autoupdate'     => array(
				'enabled'   => $auto_update || $auto_update_forced,
				'supported' => $auto_update_supported,
				'forced'    => $auto_update_forced,
			),
			'actions'        => array(
				'activate'   => current_user_can( 'switch_themes' ) ? gc_nonce_url( admin_url( 'themes.php?action=activate&amp;stylesheet=' . $encoded_slug ), 'switch-theme_' . $slug ) : null,
				'customize'  => $customize_action,
				'delete'     => ( ! is_multisite() && current_user_can( 'delete_themes' ) ) ? gc_nonce_url( admin_url( 'themes.php?action=delete&amp;stylesheet=' . $encoded_slug ), 'delete-theme_' . $slug ) : null,
				'autoupdate' => gc_is_auto_update_enabled_for_type( 'theme' ) && ! is_multisite() && current_user_can( 'update_themes' )
					? gc_nonce_url( admin_url( 'themes.php?action=' . $auto_update_action . '&amp;stylesheet=' . $encoded_slug ), 'updates' )
					: null,
			),
			'blockTheme'     => $theme->is_block_theme(),
		);
	}

	// Remove 'delete' action if theme has an active child.
	if ( ! empty( $parents ) && array_key_exists( $current_theme, $parents ) ) {
		unset( $prepared_themes[ $parents[ $current_theme ] ]['actions']['delete'] );
	}

	/**
	 * Filters the themes prepared for JavaScript, for themes.php.
	 *
	 * Could be useful for changing the order, which is by name by default.
	 *
	 * @since 3.8.0
	 *
	 * @param array $prepared_themes Array of theme data.
	 */
	$prepared_themes = apply_filters( 'gc_prepare_themes_for_js', $prepared_themes );
	$prepared_themes = array_values( $prepared_themes );
	return array_filter( $prepared_themes );
}

/**
 * Prints JS templates for the theme-browsing UI in the Customizer.
 *
 */
function customize_themes_print_templates() {
	?>
	<script type="text/html" id="tmpl-customize-themes-details-view">
		<div class="theme-backdrop"></div>
		<div class="theme-wrap gc-clearfix" role="document">
			<div class="theme-header">
				<button type="button" class="left dashicons dashicons-no"><span class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '显示上一个主题' );
					?>
				</span></button>
				<button type="button" class="right dashicons dashicons-no"><span class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '显示下一个主题' );
					?>
				</span></button>
				<button type="button" class="close dashicons dashicons-no"><span class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					_e( '关闭详情对话框' );
					?>
				</span></button>
			</div>
			<div class="theme-about gc-clearfix">
				<div class="theme-screenshots">
				<# if ( data.screenshot && data.screenshot[0] ) { #>
					<div class="screenshot"><img src="{{ data.screenshot[0] }}?ver={{ data.version }}" alt="" /></div>
				<# } else { #>
					<div class="screenshot blank"></div>
				<# } #>
				</div>

				<div class="theme-info">
					<# if ( data.active ) { #>
						<span class="current-label"><?php _e( '已启用的主题' ); ?></span>
					<# } #>
					<h2 class="theme-name">{{{ data.name }}}<span class="theme-version">
						<?php
						/* translators: %s: Theme version. */
						printf( __( '版本：%s' ), '{{ data.version }}' );
						?>
					</span></h2>
					<h3 class="theme-author">
						<?php
						/* translators: %s: Theme author link. */
						printf( __( '作者：%s' ), '{{{ data.authorAndUri }}}' );
						?>
					</h3>

					<# if ( data.stars && 0 != data.num_ratings ) { #>
						<div class="theme-rating">
							{{{ data.stars }}}
							<a class="num-ratings" target="_blank" href="{{ data.reviews_url }}">
								<?php
								printf(
									'%1$s <span class="screen-reader-text">%2$s</span>',
									/* translators: %s: Number of ratings. */
									sprintf( __( '（%s个评级）' ), '{{ data.num_ratings }}' ),
									/* translators: Hidden accessibility text. */
									__( '（在新窗口中打开）' )
								);
								?>
							</a>
						</div>
					<# } #>

					<# if ( data.hasUpdate ) { #>
						<# if ( data.updateResponse.compatibleGC && data.updateResponse.compatiblePHP ) { #>
							<div class="alert alert-warning notice-alt notice-large" data-slug="{{ data.id }}">
								<h3 class="notice-title"><?php _e( '更新可用' ); ?></h3>
								{{{ data.update }}}
							</div>
						<# } else { #>
							<div class="alert alert-danger notice-alt notice-large" data-slug="{{ data.id }}">
								<h3 class="notice-title"><?php _e( '更新不兼容' ); ?></h3>
								<p>
									<# if ( ! data.updateResponse.compatibleGC && ! data.updateResponse.compatiblePHP ) { #>
										<?php
										printf(
											/* translators: %s: Theme name. */
											__( '%s 有新版本可用，但与您当前使用的 GeChiUI 和 PHP 版本不兼容。' ),
											'{{{ data.name }}}'
										);
										if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
											printf(
												/* translators: 1: URL to GeChiUI Updates screen, 2: URL to Update PHP page. */
												' ' . __( '<a href="%1$s">请更新GeChiUI</a>，并<a href="%2$s">查阅如何更新PHP</a>。' ),
												self_admin_url( 'update-core.php' ),
												esc_url( gc_get_update_php_url() )
											);
											gc_update_php_annotation( '</p><p><em>', '</em>' );
										} elseif ( current_user_can( 'update_core' ) ) {
											printf(
												/* translators: %s: URL to GeChiUI Updates screen. */
												' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
												self_admin_url( 'update-core.php' )
											);
										} elseif ( current_user_can( 'update_php' ) ) {
											printf(
												/* translators: %s: URL to Update PHP page. */
												' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
												esc_url( gc_get_update_php_url() )
											);
											gc_update_php_annotation( '</p><p><em>', '</em>' );
										}
										?>
									<# } else if ( ! data.updateResponse.compatibleGC ) { #>
										<?php
										printf(
											/* translators: %s: Theme name. */
											__( '%s 有新版本可用，但与您当前使用的 GeChiUI 版本不兼容。' ),
											'{{{ data.name }}}'
										);
										if ( current_user_can( 'update_core' ) ) {
											printf(
												/* translators: %s: URL to GeChiUI Updates screen. */
												' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
												self_admin_url( 'update-core.php' )
											);
										}
										?>
									<# } else if ( ! data.updateResponse.compatiblePHP ) { #>
										<?php
										printf(
											/* translators: %s: Theme name. */
											__( '%s 有新版本可用，但与您当前使用的 PHP 版本不兼容。' ),
											'{{{ data.name }}}'
										);
										if ( current_user_can( 'update_php' ) ) {
											printf(
												/* translators: %s: URL to Update PHP page. */
												' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
												esc_url( gc_get_update_php_url() )
											);
											gc_update_php_annotation( '</p><p><em>', '</em>' );
										}
										?>
									<# } #>
								</p>
							</div>
						<# } #>
					<# } #>

					<# if ( data.parent ) { #>
						<p class="parent-theme">
							<?php
							printf(
								/* translators: %s: Theme name. */
								__( '这是%s的子主题。' ),
								'<strong>{{{ data.parent }}}</strong>'
							);
							?>
						</p>
					<# } #>

					<# if ( ! data.compatibleGC || ! data.compatiblePHP ) { #>
						<div class="alert alert-danger notice-alt notice-large"><p>
							<# if ( ! data.compatibleGC && ! data.compatiblePHP ) { #>
								<?php
								_e( '此主题未适配您当前的 GeChiUI 和 PHP 版本。' );
								if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
									printf(
										/* translators: 1: URL to GeChiUI Updates screen, 2: URL to Update PHP page. */
										' ' . __( '<a href="%1$s">请更新GeChiUI</a>，并<a href="%2$s">查阅如何更新PHP</a>。' ),
										self_admin_url( 'update-core.php' ),
										esc_url( gc_get_update_php_url() )
									);
									gc_update_php_annotation( '</p><p><em>', '</em>' );
								} elseif ( current_user_can( 'update_core' ) ) {
									printf(
										/* translators: %s: URL to GeChiUI Updates screen. */
										' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
										self_admin_url( 'update-core.php' )
									);
								} elseif ( current_user_can( 'update_php' ) ) {
									printf(
										/* translators: %s: URL to Update PHP page. */
										' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
										esc_url( gc_get_update_php_url() )
									);
									gc_update_php_annotation( '</p><p><em>', '</em>' );
								}
								?>
							<# } else if ( ! data.compatibleGC ) { #>
								<?php
								_e( '此主题未适配您当前的 GeChiUI 版本。' );
								if ( current_user_can( 'update_core' ) ) {
									printf(
										/* translators: %s: URL to GeChiUI Updates screen. */
										' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
										self_admin_url( 'update-core.php' )
									);
								}
								?>
							<# } else if ( ! data.compatiblePHP ) { #>
								<?php
								_e( '此主题未适配您当前的 PHP 版本。' );
								if ( current_user_can( 'update_php' ) ) {
									printf(
										/* translators: %s: URL to Update PHP page. */
										' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
										esc_url( gc_get_update_php_url() )
									);
									gc_update_php_annotation( '</p><p><em>', '</em>' );
								}
								?>
							<# } #>
						</p></div>
					<# } else if ( ! data.active && data.blockTheme ) { #>
						<div class="alert alert-danger notice-alt notice-large"><p>
						<?php
							_e( '此主题不支持自定义。' );
						?>
						<# if ( data.actions.activate ) { #>
							<?php
							printf(
								/* translators: %s: URL to the themes page (also it activates the theme). */
								' ' . __( '但您也可以<a href="%s">启用此主题</a>，并使用系统编辑器对其进行自定义。' ),
								'{{{ data.actions.activate }}}'
							);
							?>
						<# } #>
						</p></div>
					<# } #>

					<p class="theme-description">{{{ data.description }}}</p>

					<# if ( data.tags ) { #>
						<p class="theme-tags"><span><?php _e( '标签：' ); ?></span> {{{ data.tags }}}</p>
					<# } #>
				</div>
			</div>

			<div class="theme-actions">
				<# if ( data.active ) { #>
					<button type="button" class="btn btn-primary customize-theme"><?php _e( '自定义' ); ?></button>
				<# } else if ( 'installed' === data.type ) { #>
					<?php if ( current_user_can( 'delete_themes' ) ) { ?>
						<# if ( data.actions && data.actions['delete'] ) { #>
							<a href="{{{ data.actions['delete'] }}}" data-slug="{{ data.id }}" class="button button-secondary delete-theme"><?php _e( '删除' ); ?></a>
						<# } #>
					<?php } ?>

					<# if ( data.blockTheme ) { #>
						<?php
							/* translators: %s: Theme name. */
							$aria_label = sprintf( _x( '启用 %s', 'theme' ), '{{ data.name }}' );
						?>
						<# if ( data.compatibleGC && data.compatiblePHP && data.actions.activate ) { #>
							<a href="{{{ data.actions.activate }}}" class="btn btn-primary activate" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php _e( '启用' ); ?></a>
						<# } #>
					<# } else { #>
						<# if ( data.compatibleGC && data.compatiblePHP ) { #>
							<button type="button" class="btn btn-primary preview-theme" data-slug="{{ data.id }}"><?php _e( '实时预览' ); ?></button>
						<# } else { #>
							<button class="btn btn-primary disabled"><?php _e( '实时预览' ); ?></button>
						<# } #>
					<# } #>
				<# } else { #>
					<# if ( data.compatibleGC && data.compatiblePHP ) { #>
						<button type="button" class="button theme-install" data-slug="{{ data.id }}"><?php _e( '安装' ); ?></button>
						<button type="button" class="btn btn-primary theme-install preview" data-slug="{{ data.id }}"><?php _e( '安装并预览' ); ?></button>
					<# } else { #>
						<button type="button" class="button disabled"><?php _ex( '无法安装', 'theme' ); ?></button>
						<button type="button" class="btn btn-primary disabled"><?php _e( '安装并预览' ); ?></button>
					<# } #>
				<# } #>
			</div>
		</div>
	</script>
	<?php
}

/**
 * Determines whether a theme is technically active but was paused while
 * loading.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 5.2.0
 *
 * @param string $theme Path to the theme directory relative to the themes directory.
 * @return bool True, if in the list of paused themes. False, not in the list.
 */
function is_theme_paused( $theme ) {
	if ( ! isset( $GLOBALS['_paused_themes'] ) ) {
		return false;
	}

	if ( get_stylesheet() !== $theme && get_template() !== $theme ) {
		return false;
	}

	return array_key_exists( $theme, $GLOBALS['_paused_themes'] );
}

/**
 * Gets the error that was recorded for a paused theme.
 *
 * @since 5.2.0
 *
 * @param string $theme Path to the theme directory relative to the themes
 *                      directory.
 * @return array|false Array of error information as it was returned by
 *                     `error_get_last()`, or false if none was recorded.
 */
function gc_get_theme_error( $theme ) {
	if ( ! isset( $GLOBALS['_paused_themes'] ) ) {
		return false;
	}

	if ( ! array_key_exists( $theme, $GLOBALS['_paused_themes'] ) ) {
		return false;
	}

	return $GLOBALS['_paused_themes'][ $theme ];
}

/**
 * Tries to resume a single theme.
 *
 * If a redirect was provided and a functions.php file was found, we first ensure that
 * functions.php file does not throw fatal errors anymore.
 *
 * The way it works is by setting the redirection to the error before trying to
 * include the file. If the theme fails, then the redirection will not be overwritten
 * with the success message and the theme will not be resumed.
 *
 * @since 5.2.0
 *
 * @param string $theme    Single theme to resume.
 * @param string $redirect Optional. URL to redirect to. Default empty string.
 * @return bool|GC_Error True on success, false if `$theme` was not paused,
 *                       `GC_Error` on failure.
 */
function resume_theme( $theme, $redirect = '' ) {
	list( $extension ) = explode( '/', $theme );

	/*
	 * We'll override this later if the theme could be resumed without
	 * creating a fatal error.
	 */
	if ( ! empty( $redirect ) ) {
		$functions_path = '';
		if ( str_contains( STYLESHEETPATH, $extension ) ) {
			$functions_path = STYLESHEETPATH . '/functions.php';
		} elseif ( str_contains( TEMPLATEPATH, $extension ) ) {
			$functions_path = TEMPLATEPATH . '/functions.php';
		}

		if ( ! empty( $functions_path ) ) {
			gc_redirect(
				add_query_arg(
					'_error_nonce',
					gc_create_nonce( 'theme-resume-error_' . $theme ),
					$redirect
				)
			);

			// Load the theme's functions.php to test whether it throws a fatal error.
			ob_start();
			if ( ! defined( 'GC_SANDBOX_SCRAPING' ) ) {
				define( 'GC_SANDBOX_SCRAPING', true );
			}
			include $functions_path;
			ob_clean();
		}
	}

	$result = gc_paused_themes()->delete( $extension );

	if ( ! $result ) {
		return new GC_Error(
			'could_not_resume_theme',
			__( '未能恢复此主题。' )
		);
	}

	return true;
}

/**
 * Renders an admin notice in case some themes have been paused due to errors.
 *
 * @since 5.2.0
 *
 * @global string $pagenow The filename of the current screen.
 */
function paused_themes_notice() {
	if ( 'themes.php' === $GLOBALS['pagenow'] ) {
		return;
	}

	if ( ! current_user_can( 'resume_themes' ) ) {
		return;
	}

	if ( ! isset( $GLOBALS['_paused_themes'] ) || empty( $GLOBALS['_paused_themes'] ) ) {
		return;
	}

	printf(
		'<div class="alert alert-danger"><p><strong>%s</strong><br>%s</p><p><a href="%s">%s</a></p></div>',
		__( '一个或多个主题未能成功加载。' ),
		__( '您可以在主题页面获取更多信息及做出修改。' ),
		esc_url( admin_url( 'themes.php' ) ),
		__( '转到“主题”页面' )
	);
}
