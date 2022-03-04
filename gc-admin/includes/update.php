<?php
/**
 * GeChiUI Administration Update API
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Selects the first update version from the update_core option.
 *
 *
 *
 * @return object|array|false The response from the API on success, false on failure.
 */
function get_preferred_from_update_core() {
	$updates = get_core_updates();
	if ( ! is_array( $updates ) ) {
		return false;
	}
	if ( empty( $updates ) ) {
		return (object) array( 'response' => 'latest' );
	}
	return $updates[0];
}

/**
 * Gets available core updates.
 *
 *
 *
 * @param array $options Set $options['dismissed'] to true to show dismissed upgrades too,
 *                       set $options['available'] to false to skip not-dismissed updates.
 * @return array|false Array of the update objects on success, false on failure.
 */
function get_core_updates( $options = array() ) {
	$options   = array_merge(
		array(
			'available' => true,
			'dismissed' => false,
		),
		$options
	);
	$dismissed = get_site_option( 'dismissed_update_core' );

	if ( ! is_array( $dismissed ) ) {
		$dismissed = array();
	}

	$from_api = get_site_transient( 'update_core' );

	if ( ! isset( $from_api->updates ) || ! is_array( $from_api->updates ) ) {
		return false;
	}

	$updates = $from_api->updates;
	$result  = array();
	foreach ( $updates as $update ) {
		if ( 'autoupdate' === $update->response ) {
			continue;
		}

		if ( array_key_exists( $update->current . '|' . $update->locale, $dismissed ) ) {
			if ( $options['dismissed'] ) {
				$update->dismissed = true;
				$result[]          = $update;
			}
		} else {
			if ( $options['available'] ) {
				$update->dismissed = false;
				$result[]          = $update;
			}
		}
	}
	return $result;
}

/**
 * Gets the best available (and enabled) Auto-Update for GeChiUI core.
 *
 * If there's 1.2.3 and 1.3 on offer, it'll choose 1.3 if the installation allows it, else, 1.2.3.
 *
 *
 *
 * @return object|false The core update offering on success, false on failure.
 */
function find_core_auto_update() {
	$updates = get_site_transient( 'update_core' );
	if ( ! $updates || empty( $updates->updates ) ) {
		return false;
	}

	require_once ABSPATH . 'gc-admin/includes/class-gc-upgrader.php';

	$auto_update = false;
	$upgrader    = new GC_Automatic_Updater;
	foreach ( $updates->updates as $update ) {
		if ( 'autoupdate' !== $update->response ) {
			continue;
		}

		if ( ! $upgrader->should_update( 'core', $update, ABSPATH ) ) {
			continue;
		}

		if ( ! $auto_update || version_compare( $update->current, $auto_update->current, '>' ) ) {
			$auto_update = $update;
		}
	}
	return $auto_update;
}

/**
 * Gets and caches the checksums for the given version of GeChiUI.
 *
 *
 *
 * @param string $version Version string to query.
 * @param string $locale  Locale to query.
 * @return array|false An array of checksums on success, false on failure.
 */
function get_core_checksums( $version, $locale ) {
	$http_url = 'http://api.gechiui.com/core/checksums/1.0/?' . http_build_query( compact( 'version', 'locale' ), '', '&' );
	$url      = $http_url;

	$ssl = gc_http_supports( array( 'ssl' ) );
	if ( $ssl ) {
		$url = set_url_scheme( $url, 'https' );
	}

	$options = array(
		'timeout' => gc_doing_cron() ? 30 : 3,
	);

	$response = gc_remote_get( $url, $options );
	if ( $ssl && is_gc_error( $response ) ) {
		trigger_error(
			sprintf(
				/* translators: %s: Support forums URL. */
				__( '发生了预料之外的错误。www.GeChiUI.com或是此服务器的配置可能出了一些问题。如果您持续遇到困难，请试试<a href="%s">支持论坛</a>。' ),
				__( 'https://www.gechiui.com/support/forums/' )
			) . ' ' . __( '（GeChiUI无法建立到www.GeChiUI.com的安全连接，请联系您的服务器管理员。）' ),
			headers_sent() || GC_DEBUG ? E_USER_WARNING : E_USER_NOTICE
		);
		$response = gc_remote_get( $http_url, $options );
	}

	if ( is_gc_error( $response ) || 200 != gc_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	$body = trim( gc_remote_retrieve_body( $response ) );
	$body = json_decode( $body, true );

	if ( ! is_array( $body ) || ! isset( $body['checksums'] ) || ! is_array( $body['checksums'] ) ) {
		return false;
	}

	return $body['checksums'];
}

/**
 * Dismisses core update.
 *
 *
 *
 * @param object $update
 * @return bool
 */
function dismiss_core_update( $update ) {
	$dismissed = get_site_option( 'dismissed_update_core' );
	$dismissed[ $update->current . '|' . $update->locale ] = true;
	return update_site_option( 'dismissed_update_core', $dismissed );
}

/**
 * Undismisses core update.
 *
 *
 *
 * @param string $version
 * @param string $locale
 * @return bool
 */
function undismiss_core_update( $version, $locale ) {
	$dismissed = get_site_option( 'dismissed_update_core' );
	$key       = $version . '|' . $locale;

	if ( ! isset( $dismissed[ $key ] ) ) {
		return false;
	}

	unset( $dismissed[ $key ] );
	return update_site_option( 'dismissed_update_core', $dismissed );
}

/**
 * Finds the available update for GeChiUI core.
 *
 *
 *
 * @param string $version Version string to find the update for.
 * @param string $locale  Locale to find the update for.
 * @return object|false The core update offering on success, false on failure.
 */
function find_core_update( $version, $locale ) {
	$from_api = get_site_transient( 'update_core' );

	if ( ! isset( $from_api->updates ) || ! is_array( $from_api->updates ) ) {
		return false;
	}

	$updates = $from_api->updates;
	foreach ( $updates as $update ) {
		if ( $update->current == $version && $update->locale == $locale ) {
			return $update;
		}
	}
	return false;
}

/**
 *
 *
 * @param string $msg
 * @return string
 */
function core_update_footer( $msg = '' ) {
	if ( ! current_user_can( 'update_core' ) ) {
		/* translators: %s: GeChiUI version. */
		return sprintf( __( '%s版本' ), get_bloginfo( 'version', 'display' ) );
	}

	$cur = get_preferred_from_update_core();
	if ( ! is_object( $cur ) ) {
		$cur = new stdClass;
	}

	if ( ! isset( $cur->current ) ) {
		$cur->current = '';
	}

	if ( ! isset( $cur->response ) ) {
		$cur->response = '';
	}

	// Include an unmodified $gc_version.
	require ABSPATH . GCINC . '/version.php';

	$is_development_version = preg_match( '/alpha|beta|RC/', $gc_version );

	if ( $is_development_version ) {
		return sprintf(
			/* translators: 1: GeChiUI version number, 2: URL to GeChiUI Updates screen. */
			__( '您在使用开发版本（%1$s）。太棒了，请<a href="%2$s">保持更新</a>。' ),
			get_bloginfo( 'version', 'display' ),
			network_admin_url( 'update-core.php' )
		);
	}

	switch ( $cur->response ) {
		case 'upgrade':
			return sprintf(
				'<strong><a href="%s">%s</a></strong>',
				network_admin_url( 'update-core.php' ),
				/* translators: %s: GeChiUI version. */
				sprintf( __( '获取版本%s' ), $cur->current )
			);

		case 'latest':
		default:
			/* translators: %s: GeChiUI version. */
			return sprintf( __( '%s版本' ), get_bloginfo( 'version', 'display' ) );
	}
}

/**
 *
 *
 * @global string $pagenow
 * @return void|false
 */
function update_nag() {
	if ( is_multisite() && ! current_user_can( 'update_core' ) ) {
		return false;
	}

	global $pagenow;

	if ( 'update-core.php' === $pagenow ) {
		return;
	}

	$cur = get_preferred_from_update_core();

	if ( ! isset( $cur->response ) || 'upgrade' !== $cur->response ) {
		return false;
	}

	$version_url = sprintf(
		/* translators: %s: GeChiUI version. */
		esc_url( __( 'https://www.gechiui.com/support/gechiui-version/version-%s/' ) ),
		sanitize_title( $cur->current )
	);

	if ( current_user_can( 'update_core' ) ) {
		$msg = sprintf(
			/* translators: 1: URL to GeChiUI release notes, 2: New GeChiUI version, 3: URL to network admin, 4: Accessibility text. */
			__( '<a href="%1$s">GeChiUI %2$s</a>现已可用！<a href="%3$s" aria-label="%4$s">请立即更新</a>。' ),
			$version_url,
			$cur->current,
			network_admin_url( 'update-core.php' ),
			esc_attr__( '请立即更新GeChiUI' )
		);
	} else {
		$msg = sprintf(
			/* translators: 1: URL to GeChiUI release notes, 2: New GeChiUI version. */
			__( '<a href="%1$s">GeChiUI %2$s</a>现已可用！请通知站点管理员。' ),
			$version_url,
			$cur->current
		);
	}

	echo "<div class='update-nag notice notice-warning inline'>$msg</div>";
}

/**
 * Displays GeChiUI version and active theme in the '概览' dashboard widget.
 *
 *
 */
function update_right_now_message() {
	$theme_name = gc_get_theme();
	if ( current_user_can( 'switch_themes' ) ) {
		$theme_name = sprintf( '<a href="themes.php">%1$s</a>', $theme_name );
	}

	$msg = '';

	if ( current_user_can( 'update_core' ) ) {
		$cur = get_preferred_from_update_core();

		if ( isset( $cur->response ) && 'upgrade' === $cur->response ) {
			$msg .= sprintf(
				'<a href="%s" class="button" aria-describedby="gc-version">%s</a> ',
				network_admin_url( 'update-core.php' ),
				/* translators: %s: GeChiUI version number, or 'Latest' string. */
				sprintf( __( '更新到%s' ), $cur->current ? $cur->current : __( '最新版本' ) )
			);
		}
	}

	/* translators: 1: Version number, 2: Theme name. */
	$content = __( 'GeChiUI %1$s，使用%2$s主题。' );

	/**
	 * Filters the text displayed in the '概览' dashboard widget.
	 *
	 * Prior to 3.8.0, the widget was named '概况'.
	 *
	 *
	 * @param string $content Default text.
	 */
	$content = apply_filters( 'update_right_now_text', $content );

	$msg .= sprintf( '<span id="gc-version">' . $content . '</span>', get_bloginfo( 'version', 'display' ), $theme_name );

	echo "<p id='gc-version-message'>$msg</p>";
}

/**
 *
 *
 * @return array
 */
function get_plugin_updates() {
	$all_plugins     = get_plugins();
	$upgrade_plugins = array();
	$current         = get_site_transient( 'update_plugins' );
	foreach ( (array) $all_plugins as $plugin_file => $plugin_data ) {
		if ( isset( $current->response[ $plugin_file ] ) ) {
			$upgrade_plugins[ $plugin_file ]         = (object) $plugin_data;
			$upgrade_plugins[ $plugin_file ]->update = $current->response[ $plugin_file ];
		}
	}

	return $upgrade_plugins;
}

/**
 *
 */
function gc_plugin_update_rows() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$plugins = get_site_transient( 'update_plugins' );
	if ( isset( $plugins->response ) && is_array( $plugins->response ) ) {
		$plugins = array_keys( $plugins->response );
		foreach ( $plugins as $plugin_file ) {
			add_action( "after_plugin_row_{$plugin_file}", 'gc_plugin_update_row', 10, 2 );
		}
	}
}

/**
 * Displays update information for a plugin.
 *
 *
 *
 * @param string $file        Plugin basename.
 * @param array  $plugin_data Plugin information.
 * @return void|false
 */
function gc_plugin_update_row( $file, $plugin_data ) {
	$current = get_site_transient( 'update_plugins' );
	if ( ! isset( $current->response[ $file ] ) ) {
		return false;
	}

	$response = $current->response[ $file ];

	$plugins_allowedtags = array(
		'a'       => array(
			'href'  => array(),
			'title' => array(),
		),
		'abbr'    => array( 'title' => array() ),
		'acronym' => array( 'title' => array() ),
		'code'    => array(),
		'em'      => array(),
		'strong'  => array(),
	);

	$plugin_name = gc_kses( $plugin_data['Name'], $plugins_allowedtags );
	$plugin_slug = isset( $response->slug ) ? $response->slug : $response->id;

	if ( isset( $response->slug ) ) {
		$details_url = self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_slug . '&section=changelog' );
	} elseif ( isset( $response->url ) ) {
		$details_url = $response->url;
	} else {
		$details_url = $plugin_data['PluginURI'];
	}

	$details_url = add_query_arg(
		array(
			'TB_iframe' => 'true',
			'width'     => 600,
			'height'    => 800,
		),
		$details_url
	);

	/** @var GC_Plugins_List_Table $gc_list_table */
	$gc_list_table = _get_list_table(
		'GC_Plugins_List_Table',
		array(
			'screen' => get_current_screen(),
		)
	);

	if ( is_network_admin() || ! is_multisite() ) {
		if ( is_network_admin() ) {
			$active_class = is_plugin_active_for_network( $file ) ? ' active' : '';
		} else {
			$active_class = is_plugin_active( $file ) ? ' active' : '';
		}

		$requires_php   = isset( $response->requires_php ) ? $response->requires_php : null;
		$compatible_php = is_php_version_compatible( $requires_php );
		$notice_type    = $compatible_php ? 'notice-warning' : 'notice-error';

		printf(
			'<tr class="plugin-update-tr%s" id="%s" data-slug="%s" data-plugin="%s">' .
			'<td colspan="%s" class="plugin-update colspanchange">' .
			'<div class="update-message notice inline %s notice-alt"><p>',
			$active_class,
			esc_attr( $plugin_slug . '-update' ),
			esc_attr( $plugin_slug ),
			esc_attr( $file ),
			esc_attr( $gc_list_table->get_column_count() ),
			$notice_type
		);

		if ( ! current_user_can( 'update_plugins' ) ) {
			printf(
				/* translators: 1: Plugin name, 2: Details URL, 3: Additional link attributes, 4: Version number. */
				__( '%1$s有新版本可用。<a href="%2$s" %3$s>查看版本%4$s详情</a>。' ),
				$plugin_name,
				esc_url( $details_url ),
				sprintf(
					'class="thickbox open-plugin-details-modal" aria-label="%s"',
					/* translators: 1: Plugin name, 2: Version number. */
					esc_attr( sprintf( __( '查看%1$s版本%2$s详情' ), $plugin_name, $response->new_version ) )
				),
				esc_attr( $response->new_version )
			);
		} elseif ( empty( $response->package ) ) {
			printf(
				/* translators: 1: Plugin name, 2: Details URL, 3: Additional link attributes, 4: Version number. */
				__( '%1$s有新版本可用。<a href="%2$s" %3$s>查看版本%4$s详情</a>。<em>自动更新对此插件不可用。</em>' ),
				$plugin_name,
				esc_url( $details_url ),
				sprintf(
					'class="thickbox open-plugin-details-modal" aria-label="%s"',
					/* translators: 1: Plugin name, 2: Version number. */
					esc_attr( sprintf( __( '查看%1$s版本%2$s详情' ), $plugin_name, $response->new_version ) )
				),
				esc_attr( $response->new_version )
			);
		} else {
			if ( $compatible_php ) {
				printf(
					/* translators: 1: Plugin name, 2: Details URL, 3: Additional link attributes, 4: Version number, 5: Update URL, 6: Additional link attributes. */
					__( '%1$s有新版本可用。<a href="%2$s" %3$s>查看版本%4$s详情</a>或<a href="%5$s" %6$s>立即更新</a>。' ),
					$plugin_name,
					esc_url( $details_url ),
					sprintf(
						'class="thickbox open-plugin-details-modal" aria-label="%s"',
						/* translators: 1: Plugin name, 2: Version number. */
						esc_attr( sprintf( __( '查看%1$s版本%2$s详情' ), $plugin_name, $response->new_version ) )
					),
					esc_attr( $response->new_version ),
					gc_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file, 'upgrade-plugin_' . $file ),
					sprintf(
						'class="update-link" aria-label="%s"',
						/* translators: %s: Plugin name. */
						esc_attr( sprintf( _x( '立即更新%s', 'plugin' ), $plugin_name ) )
					)
				);
			} else {
				printf(
					/* translators: 1: Plugin name, 2: Details URL, 3: Additional link attributes, 4: Version number 5: URL to Update PHP page. */
					__( '现在有较新版本的%1$s可用，但它不能与您的PHP版本相兼容。<a href="%2$s" %3$s>查阅%4$s版本详情</a>或<a href="%5$s">查阅如何更新PHP</a>。' ),
					$plugin_name,
					esc_url( $details_url ),
					sprintf(
						'class="thickbox open-plugin-details-modal" aria-label="%s"',
						/* translators: 1: Plugin name, 2: Version number. */
						esc_attr( sprintf( __( '查看%1$s版本%2$s详情' ), $plugin_name, $response->new_version ) )
					),
					esc_attr( $response->new_version ),
					esc_url( gc_get_update_php_url() )
				);
				gc_update_php_annotation( '<br><em>', '</em>' );
			}
		}

		/**
		 * Fires at the end of the update message container in each
		 * row of the plugins list table.
		 *
		 * The dynamic portion of the hook name, `$file`, refers to the path
		 * of the plugin's primary file relative to the plugins directory.
		 *
		 *
		 * @param array  $plugin_data An array of plugin metadata. See get_plugin_data()
		 *                            and the {@see 'plugin_row_meta'} filter for the list
		 *                            of possible values.
		 * @param object $response {
		 *     An object of metadata about the available plugin update.
		 *
		 *     @type string   $id           Plugin ID, e.g. `w.org/plugins/[plugin-name]`.
		 *     @type string   $slug         Plugin slug.
		 *     @type string   $plugin       Plugin basename.
		 *     @type string   $new_version  New plugin version.
		 *     @type string   $url          Plugin URL.
		 *     @type string   $package      Plugin update package URL.
		 *     @type string[] $icons        An array of plugin icon URLs.
		 *     @type string[] $banners      An array of plugin banner URLs.
		 *     @type string[] $banners_rtl  An array of plugin RTL banner URLs.
		 *     @type string   $requires     The version of GeChiUI which the plugin requires.
		 *     @type string   $tested       The version of GeChiUI the plugin is tested against.
		 *     @type string   $requires_php The version of PHP which the plugin requires.
		 * }
		 */
		do_action( "in_plugin_update_message-{$file}", $plugin_data, $response ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

		echo '</p></div></td></tr>';
	}
}

/**
 *
 *
 * @return array
 */
function get_theme_updates() {
	$current = get_site_transient( 'update_themes' );

	if ( ! isset( $current->response ) ) {
		return array();
	}

	$update_themes = array();
	foreach ( $current->response as $stylesheet => $data ) {
		$update_themes[ $stylesheet ]         = gc_get_theme( $stylesheet );
		$update_themes[ $stylesheet ]->update = $data;
	}

	return $update_themes;
}

/**
 *
 */
function gc_theme_update_rows() {
	if ( ! current_user_can( 'update_themes' ) ) {
		return;
	}

	$themes = get_site_transient( 'update_themes' );
	if ( isset( $themes->response ) && is_array( $themes->response ) ) {
		$themes = array_keys( $themes->response );

		foreach ( $themes as $theme ) {
			add_action( "after_theme_row_{$theme}", 'gc_theme_update_row', 10, 2 );
		}
	}
}

/**
 * Displays update information for a theme.
 *
 *
 *
 * @param string   $theme_key Theme stylesheet.
 * @param GC_Theme $theme     Theme object.
 * @return void|false
 */
function gc_theme_update_row( $theme_key, $theme ) {
	$current = get_site_transient( 'update_themes' );

	if ( ! isset( $current->response[ $theme_key ] ) ) {
		return false;
	}

	$response = $current->response[ $theme_key ];

	$details_url = add_query_arg(
		array(
			'TB_iframe' => 'true',
			'width'     => 1024,
			'height'    => 800,
		),
		$current->response[ $theme_key ]['url']
	);

	/** @var GC_MS_Themes_List_Table $gc_list_table */
	$gc_list_table = _get_list_table( 'GC_MS_Themes_List_Table' );

	$active = $theme->is_allowed( 'network' ) ? ' active' : '';

	$requires_gc  = isset( $response['requires'] ) ? $response['requires'] : null;
	$requires_php = isset( $response['requires_php'] ) ? $response['requires_php'] : null;

	$compatible_gc  = is_gc_version_compatible( $requires_gc );
	$compatible_php = is_php_version_compatible( $requires_php );

	printf(
		'<tr class="plugin-update-tr%s" id="%s" data-slug="%s">' .
		'<td colspan="%s" class="plugin-update colspanchange">' .
		'<div class="update-message notice inline notice-warning notice-alt"><p>',
		$active,
		esc_attr( $theme->get_stylesheet() . '-update' ),
		esc_attr( $theme->get_stylesheet() ),
		$gc_list_table->get_column_count()
	);

	if ( $compatible_gc && $compatible_php ) {
		if ( ! current_user_can( 'update_themes' ) ) {
			printf(
				/* translators: 1: Theme name, 2: Details URL, 3: Additional link attributes, 4: Version number. */
				__( '%1$s有新版本可用。<a href="%2$s" %3$s>查看版本%4$s详情</a>。' ),
				$theme['Name'],
				esc_url( $details_url ),
				sprintf(
					'class="thickbox open-plugin-details-modal" aria-label="%s"',
					/* translators: 1: Theme name, 2: Version number. */
					esc_attr( sprintf( __( '查看%1$s版本%2$s详情' ), $theme['Name'], $response['new_version'] ) )
				),
				$response['new_version']
			);
		} elseif ( empty( $response['package'] ) ) {
			printf(
				/* translators: 1: Theme name, 2: Details URL, 3: Additional link attributes, 4: Version number. */
				__( '%1$s有新版本可用。<a href="%2$s" %3$s>查看版本%4$s详情</a>。<em>自动更新对此主题不可用。</em>' ),
				$theme['Name'],
				esc_url( $details_url ),
				sprintf(
					'class="thickbox open-plugin-details-modal" aria-label="%s"',
					/* translators: 1: Theme name, 2: Version number. */
					esc_attr( sprintf( __( '查看%1$s版本%2$s详情' ), $theme['Name'], $response['new_version'] ) )
				),
				$response['new_version']
			);
		} else {
			printf(
				/* translators: 1: Theme name, 2: Details URL, 3: Additional link attributes, 4: Version number, 5: Update URL, 6: Additional link attributes. */
				__( '%1$s有新版本可用。<a href="%2$s" %3$s>查看版本%4$s详情</a>或<a href="%5$s" %6$s>立即更新</a>。' ),
				$theme['Name'],
				esc_url( $details_url ),
				sprintf(
					'class="thickbox open-plugin-details-modal" aria-label="%s"',
					/* translators: 1: Theme name, 2: Version number. */
					esc_attr( sprintf( __( '查看%1$s版本%2$s详情' ), $theme['Name'], $response['new_version'] ) )
				),
				$response['new_version'],
				gc_nonce_url( self_admin_url( 'update.php?action=upgrade-theme&theme=' ) . $theme_key, 'upgrade-theme_' . $theme_key ),
				sprintf(
					'class="update-link" aria-label="%s"',
					/* translators: %s: Theme name. */
					esc_attr( sprintf( _x( '立即更新%s', 'theme' ), $theme['Name'] ) )
				)
			);
		}
	} else {
		if ( ! $compatible_gc && ! $compatible_php ) {
			printf(
				/* translators: %s: Theme name. */
				__( '%s的新版本可用，但无法在您安装版本的GeChiUI和PHP上工作。' ),
				$theme['Name']
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
		} elseif ( ! $compatible_gc ) {
			printf(
				/* translators: %s: Theme name. */
				__( '%s的新版本可用，但无法在您安装版本的GeChiUI和PHP上工作。' ),
				$theme['Name']
			);
			if ( current_user_can( 'update_core' ) ) {
				printf(
					/* translators: %s: URL to GeChiUI Updates screen. */
					' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
					self_admin_url( 'update-core.php' )
				);
			}
		} elseif ( ! $compatible_php ) {
			printf(
				/* translators: %s: Theme name. */
				__( '%s的新版本可用，但无法在您安装版本的GeChiUI和PHP上工作。' ),
				$theme['Name']
			);
			if ( current_user_can( 'update_php' ) ) {
				printf(
					/* translators: %s: URL to Update PHP page. */
					' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
					esc_url( gc_get_update_php_url() )
				);
				gc_update_php_annotation( '</p><p><em>', '</em>' );
			}
		}
	}

	/**
	 * Fires at the end of the update message container in each
	 * row of the themes list table.
	 *
	 * The dynamic portion of the hook name, `$theme_key`, refers to
	 * the theme slug as found in the www.GeChiUI.com themes repository.
	 *
	 *
	 * @param GC_Theme $theme    The GC_Theme object.
	 * @param array    $response {
	 *     An array of metadata about the available theme update.
	 *
	 *     @type string $new_version New theme version.
	 *     @type string $url         Theme URL.
	 *     @type string $package     Theme update package URL.
	 * }
	 */
	do_action( "in_theme_update_message-{$theme_key}", $theme, $response ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

	echo '</p></div></td></tr>';
}

/**
 *
 *
 * @global int $upgrading
 * @return void|false
 */
function maintenance_nag() {
	// Include an unmodified $gc_version.
	require ABSPATH . GCINC . '/version.php';
	global $upgrading;
	$nag = isset( $upgrading );
	if ( ! $nag ) {
		$failed = get_site_option( 'auto_core_update_failed' );
		/*
		 * If an update failed critically, we may have copied over version.php but not other files.
		 * In that case, if the installation claims we're running the version we attempted, nag.
		 * This is serious enough to err on the side of nagging.
		 *
		 * If we simply failed to update before we tried to copy any files, then assume things are
		 * OK if they are now running the latest.
		 *
		 * This flag is cleared whenever a successful update occurs using Core_Upgrader.
		 */
		$comparison = ! empty( $failed['critical'] ) ? '>=' : '>';
		if ( isset( $failed['attempted'] ) && version_compare( $failed['attempted'], $gc_version, $comparison ) ) {
			$nag = true;
		}
	}

	if ( ! $nag ) {
		return false;
	}

	if ( current_user_can( 'update_core' ) ) {
		$msg = sprintf(
			/* translators: %s: URL to GeChiUI Updates screen. */
			__( '自动升级GeChiUI失败 —— <a href="%s">请再试一次</a>。' ),
			'update-core.php'
		);
	} else {
		$msg = __( 'GeChiUI自动升级失败！请通知您的站点管理员。' );
	}

	echo "<div class='update-nag notice notice-warning inline'>$msg</div>";
}

/**
 * Prints the JavaScript templates for update admin notices.
 *
 * Template takes one argument with four values:
 *
 *     param {object} data {
 *         Arguments for admin notice.
 *
 *         @type string id        ID of the notice.
 *         @type string className Class names for the notice.
 *         @type string message   The notice's message.
 *         @type string type      The type of update the notice is for. Either 'plugin' or 'theme'.
 *     }
 *
 *
 */
function gc_print_admin_notice_templates() {
	?>
	<script id="tmpl-gc-updates-admin-notice" type="text/html">
		<div <# if ( data.id ) { #>id="{{ data.id }}"<# } #> class="notice {{ data.className }}"><p>{{{ data.message }}}</p></div>
	</script>
	<script id="tmpl-gc-bulk-updates-admin-notice" type="text/html">
		<div id="{{ data.id }}" class="{{ data.className }} notice <# if ( data.errors ) { #>notice-error<# } else { #>notice-success<# } #>">
			<p>
				<# if ( data.successes ) { #>
					<# if ( 1 === data.successes ) { #>
						<# if ( 'plugin' === data.type ) { #>
							<?php
							/* translators: %s: Number of plugins. */
							printf( __( '%s个插件成功更新。' ), '{{ data.successes }}' );
							?>
						<# } else { #>
							<?php
							/* translators: %s: Number of themes. */
							printf( __( '%s个主题成功更新。' ), '{{ data.successes }}' );
							?>
						<# } #>
					<# } else { #>
						<# if ( 'plugin' === data.type ) { #>
							<?php
							/* translators: %s: Number of plugins. */
							printf( __( '%s个插件成功更新。' ), '{{ data.successes }}' );
							?>
						<# } else { #>
							<?php
							/* translators: %s: Number of themes. */
							printf( __( '%s个主题成功更新。' ), '{{ data.successes }}' );
							?>
						<# } #>
					<# } #>
				<# } #>
				<# if ( data.errors ) { #>
					<button class="button-link bulk-action-errors-collapsed" aria-expanded="false">
						<# if ( 1 === data.errors ) { #>
							<?php
							/* translators: %s: Number of failed updates. */
							printf( __( '%s个更新失败。' ), '{{ data.errors }}' );
							?>
						<# } else { #>
							<?php
							/* translators: %s: Number of failed updates. */
							printf( __( '%s个更新失败。' ), '{{ data.errors }}' );
							?>
						<# } #>
						<span class="screen-reader-text"><?php _e( '显示详情' ); ?></span>
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
				<# } #>
			</p>
			<# if ( data.errors ) { #>
				<ul class="bulk-action-errors hidden">
					<# _.each( data.errorMessages, function( errorMessage ) { #>
						<li>{{ errorMessage }}</li>
					<# } ); #>
				</ul>
			<# } #>
		</div>
	</script>
	<?php
}

/**
 * Prints the JavaScript templates for update and deletion rows in list tables.
 *
 * The update template takes one argument with four values:
 *
 *     param {object} data {
 *         Arguments for the update row
 *
 *         @type string slug    Plugin slug.
 *         @type string plugin  Plugin base name.
 *         @type string colspan The number of table columns this row spans.
 *         @type string content The row content.
 *     }
 *
 * The delete template takes one argument with four values:
 *
 *     param {object} data {
 *         Arguments for the update row
 *
 *         @type string slug    Plugin slug.
 *         @type string plugin  Plugin base name.
 *         @type string name    Plugin name.
 *         @type string colspan The number of table columns this row spans.
 *     }
 *
 *
 */
function gc_print_update_row_templates() {
	?>
	<script id="tmpl-item-update-row" type="text/template">
		<tr class="plugin-update-tr update" id="{{ data.slug }}-update" data-slug="{{ data.slug }}" <# if ( data.plugin ) { #>data-plugin="{{ data.plugin }}"<# } #>>
			<td colspan="{{ data.colspan }}" class="plugin-update colspanchange">
				{{{ data.content }}}
			</td>
		</tr>
	</script>
	<script id="tmpl-item-deleted-row" type="text/template">
		<tr class="plugin-deleted-tr inactive deleted" id="{{ data.slug }}-deleted" data-slug="{{ data.slug }}" <# if ( data.plugin ) { #>data-plugin="{{ data.plugin }}"<# } #>>
			<td colspan="{{ data.colspan }}" class="plugin-update colspanchange">
				<# if ( data.plugin ) { #>
					<?php
					printf(
						/* translators: %s: Plugin name. */
						_x( '%s被成功删除。', 'plugin' ),
						'<strong>{{{ data.name }}}</strong>'
					);
					?>
				<# } else { #>
					<?php
					printf(
						/* translators: %s: Theme name. */
						_x( '%s被成功删除。', 'theme' ),
						'<strong>{{{ data.name }}}</strong>'
					);
					?>
				<# } #>
			</td>
		</tr>
	</script>
	<?php
}

/**
 * Displays a notice when the user is in recovery mode.
 *
 *
 */
function gc_recovery_mode_nag() {
	if ( ! gc_is_recovery_mode() ) {
		return;
	}

	$url = gc_login_url();
	$url = add_query_arg( 'action', GC_Recovery_Mode::EXIT_ACTION, $url );
	$url = gc_nonce_url( $url, GC_Recovery_Mode::EXIT_ACTION );

	?>
	<div class="notice notice-info">
		<p>
			<?php
			printf(
				/* translators: %s: Recovery Mode exit link. */
				__( '您正位于恢复模式。您的其中一个已启用的主题或插件可能发生了错误。要退出恢复模式，请注销登陆或使用退出按钮。<a href="%s">退出恢复模式</a>' ),
				esc_url( $url )
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Checks whether auto-updates are enabled.
 *
 *
 *
 * @param string $type The type of update being checked: 'theme' or 'plugin'.
 * @return bool True if auto-updates are enabled for `$type`, false otherwise.
 */
function gc_is_auto_update_enabled_for_type( $type ) {
	if ( ! class_exists( 'GC_Automatic_Updater' ) ) {
		require_once ABSPATH . 'gc-admin/includes/class-gc-automatic-updater.php';
	}

	$updater = new GC_Automatic_Updater();
	$enabled = ! $updater->is_disabled();

	switch ( $type ) {
		case 'plugin':
			/**
			 * Filters whether plugins auto-update is enabled.
			 *
		
			 *
			 * @param bool $enabled True if plugins auto-update is enabled, false otherwise.
			 */
			return apply_filters( 'plugins_auto_update_enabled', $enabled );
		case 'theme':
			/**
			 * Filters whether themes auto-update is enabled.
			 *
		
			 *
			 * @param bool $enabled True if themes auto-update is enabled, false otherwise.
			 */
			return apply_filters( 'themes_auto_update_enabled', $enabled );
	}

	return false;
}

/**
 * Checks whether auto-updates are forced for an item.
 *
 *
 *
 * @param string    $type   The type of update being checked: 'theme' or 'plugin'.
 * @param bool|null $update Whether to update. The value of null is internally used
 *                          to detect whether nothing has hooked into this filter.
 * @param object    $item   The update offer.
 * @return bool True if auto-updates are forced for `$item`, false otherwise.
 */
function gc_is_auto_update_forced_for_item( $type, $update, $item ) {
	/** This filter is documented in gc-admin/includes/class-gc-automatic-updater.php */
	return apply_filters( "auto_update_{$type}", $update, $item );
}

/**
 * Determines the appropriate auto-update message to be displayed.
 *
 *
 *
 * @return string The update message to be shown.
 */
function gc_get_auto_update_message() {
	$next_update_time = gc_next_scheduled( 'gc_version_check' );

	// Check if the event exists.
	if ( false === $next_update_time ) {
		$message = __( '未能安排自动更新。GC-Cron可能发生问题。' );
	} else {
		$time_to_next_update = human_time_diff( (int) $next_update_time );

		// See if cron is overdue.
		$overdue = ( time() - $next_update_time ) > 0;

		if ( $overdue ) {
			$message = sprintf(
				/* translators: %s: Duration that GC-Cron has been overdue. */
				__( '自动更新已延期 %s。GC-Cron 可能出现了问题。' ),
				$time_to_next_update
			);
		} else {
			$message = sprintf(
				/* translators: %s: Time until the next update. */
				__( '自动更新将于%s后执行。' ),
				$time_to_next_update
			);
		}
	}

	return $message;
}
