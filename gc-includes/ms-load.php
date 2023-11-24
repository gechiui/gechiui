<?php
/**
 * These functions are needed to load Multisite.
 *
 * @package GeChiUI
 * @subpackage Multisite
 */

/**
 * Whether a subdomain configuration is enabled.
 *
 * @return bool True if subdomain configuration is enabled, false otherwise.
 */
function is_subdomain_install() {
	if ( defined( 'SUBDOMAIN_INSTALL' ) ) {
		return SUBDOMAIN_INSTALL;
	}

	return ( defined( 'VHOST' ) && 'yes' === VHOST );
}

/**
 * Returns array of network plugin files to be included in global scope.
 *
 * The default directory is gc-content/plugins. To change the default directory
 * manually, define `GC_PLUGIN_DIR` and `GC_PLUGIN_URL` in `gc-config.php`.
 *
 * @access private
 *
 * @return string[] Array of absolute paths to files to include.
 */
function gc_get_active_network_plugins() {
	$active_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
	if ( empty( $active_plugins ) ) {
		return array();
	}

	$plugins        = array();
	$active_plugins = array_keys( $active_plugins );
	sort( $active_plugins );

	foreach ( $active_plugins as $plugin ) {
		if ( ! validate_file( $plugin )                     // $plugin must validate as file.
			&& str_ends_with( $plugin, '.php' )             // $plugin must end with '.php'.
			&& file_exists( GC_PLUGIN_DIR . '/' . $plugin ) // $plugin must exist.
			) {
			$plugins[] = GC_PLUGIN_DIR . '/' . $plugin;
		}
	}

	return $plugins;
}

/**
 * Checks status of current blog.
 *
 * Checks if the blog is deleted, inactive, archived, or spammed.
 *
 * Dies with a default message if the blog does not pass the check.
 *
 * To change the default message when a blog does not pass the check,
 * use the gc-content/blog-deleted.php, blog-inactive.php and
 * blog-suspended.php drop-ins.
 *
 * @return true|string Returns true on success, or drop-in file to include.
 */
function ms_site_check() {

	/**
	 * Filters checking the status of the current blog.
	 *
	 *
	 * @param bool|null $check Whether to skip the blog status check. Default null.
	 */
	$check = apply_filters( 'ms_site_check', null );
	if ( null !== $check ) {
		return true;
	}

	// Allow super admins to see blocked sites.
	if ( is_super_admin() ) {
		return true;
	}

	$blog = get_site();

	if ( '1' == $blog->deleted ) {
		if ( file_exists( GC_CONTENT_DIR . '/blog-deleted.php' ) ) {
			return GC_CONTENT_DIR . '/blog-deleted.php';
		} else {
			gc_die( __( '该系统已不再可用。' ), '', array( 'response' => 410 ) );
		}
	}

	if ( '2' == $blog->deleted ) {
		if ( file_exists( GC_CONTENT_DIR . '/blog-inactive.php' ) ) {
			return GC_CONTENT_DIR . '/blog-inactive.php';
		} else {
			$admin_email = str_replace( '@', ' AT ', get_site_option( 'admin_email', 'support@' . get_network()->domain ) );
			gc_die(
				sprintf(
					/* translators: %s: Admin email link. */
					__( '此系统还未被激活。如果您在激活系统时遇到困难，请联系%s。' ),
					sprintf( '<a href="mailto:%1$s">%1$s</a>', $admin_email )
				)
			);
		}
	}

	if ( '1' == $blog->archived || '1' == $blog->spam ) {
		if ( file_exists( GC_CONTENT_DIR . '/blog-suspended.php' ) ) {
			return GC_CONTENT_DIR . '/blog-suspended.php';
		} else {
			gc_die( __( '该系统已被归档或挂起。' ), '', array( 'response' => 410 ) );
		}
	}

	return true;
}

/**
 * Retrieves the closest matching network for a domain and path.
 *
 * @since 3.9.0
 *
 * @internal In 4.4.0, converted to a wrapper for GC_Network::get_by_path()
 *
 * @param string   $domain   Domain to check.
 * @param string   $path     Path to check.
 * @param int|null $segments Path segments to use. Defaults to null, or the full path.
 * @return GC_Network|false Network object if successful. False when no network is found.
 */
function get_network_by_path( $domain, $path, $segments = null ) {
	return GC_Network::get_by_path( $domain, $path, $segments );
}

/**
 * Retrieves the closest matching site object by its domain and path.
 *
 * This will not necessarily return an exact match for a domain and path. Instead, it
 * breaks the domain and path into pieces that are then used to match the closest
 * possibility from a query.
 *
 * The intent of this method is to match a site object during bootstrap for a
 * requested site address
 *
 * @since 3.9.0 Updated to always return a `GC_Site` object.
 *
 * @param string   $domain   Domain to check.
 * @param string   $path     Path to check.
 * @param int|null $segments Path segments to use. Defaults to null, or the full path.
 * @return GC_Site|false Site object if successful. False when no site is found.
 */
function get_site_by_path( $domain, $path, $segments = null ) {
	$path_segments = array_filter( explode( '/', trim( $path, '/' ) ) );

	/**
	 * Filters the number of path segments to consider when searching for a site.
	 *
	 *
	 * @param int|null $segments The number of path segments to consider. GeChiUI by default looks at
	 *                           one path segment following the network path. The function default of
	 *                           null only makes sense when you know the requested path should match a site.
	 * @param string   $domain   The requested domain.
	 * @param string   $path     The requested path, in full.
	 */
	$segments = apply_filters( 'site_by_path_segments_count', $segments, $domain, $path );

	if ( null !== $segments && count( $path_segments ) > $segments ) {
		$path_segments = array_slice( $path_segments, 0, $segments );
	}

	$paths = array();

	while ( count( $path_segments ) ) {
		$paths[] = '/' . implode( '/', $path_segments ) . '/';
		array_pop( $path_segments );
	}

	$paths[] = '/';

	/**
	 * Determines a site by its domain and path.
	 *
	 * This allows one to short-circuit the default logic, perhaps by
	 * replacing it with a routine that is more optimal for your setup.
	 *
	 * Return null to avoid the short-circuit. Return false if no site
	 * can be found at the requested domain and path. Otherwise, return
	 * a site object.
	 *
	 *
	 * @param null|false|GC_Site $site     Site value to return by path. Default null
	 *                                     to continue retrieving the site.
	 * @param string             $domain   The requested domain.
	 * @param string             $path     The requested path, in full.
	 * @param int|null           $segments The suggested number of paths to consult.
	 *                                     Default null, meaning the entire path was to be consulted.
	 * @param string[]           $paths    The paths to search for, based on $path and $segments.
	 */
	$pre = apply_filters( 'pre_get_site_by_path', null, $domain, $path, $segments, $paths );
	if ( null !== $pre ) {
		if ( false !== $pre && ! $pre instanceof GC_Site ) {
			$pre = new GC_Site( $pre );
		}
		return $pre;
	}

	/*
	 * @todo
	 * Caching, etc. Consider alternative optimization routes,
	 * perhaps as an opt-in for plugins, rather than using the pre_* filter.
	 * For example: The segments filter can expand or ignore paths.
	 * If persistent caching is enabled, we could query the DB for a path <> '/'
	 * then cache whether we can just always ignore paths.
	 */

	/*
	 * Either www or non-www is supported, not both. If a www domain is requested,
	 * query for both to provide the proper redirect.
	 */
	$domains = array( $domain );
	if ( str_starts_with( $domain, 'www.' ) ) {
		$domains[] = substr( $domain, 4 );
	}

	$args = array(
		'number'                 => 1,
		'update_site_meta_cache' => false,
	);

	if ( count( $domains ) > 1 ) {
		$args['domain__in']               = $domains;
		$args['orderby']['domain_length'] = 'DESC';
	} else {
		$args['domain'] = array_shift( $domains );
	}

	if ( count( $paths ) > 1 ) {
		$args['path__in']               = $paths;
		$args['orderby']['path_length'] = 'DESC';
	} else {
		$args['path'] = array_shift( $paths );
	}

	$result = get_sites( $args );
	$site   = array_shift( $result );

	if ( $site ) {
		return $site;
	}

	return false;
}

/**
 * Identifies the network and site of a requested domain and path and populates the
 * corresponding network and site global objects as part of the multisite bootstrap process.
 *
 * Prior to 4.6.0, this was a procedural block in `ms-settings.php`. It was wrapped into
 * a function to facilitate unit tests. It should not be used outside of core.
 *
 * Usually, it's easier to query the site first, which then declares its network.
 * In limited situations, we either can or must find the network first.
 *
 * If a network and site are found, a `true` response will be returned so that the
 * request can continue.
 *
 * If neither a network or site is found, `false` or a URL string will be returned
 * so that either an error can be shown or a redirect can occur.
 *
 * @access private
 *
 * @global GC_Network $current_site The current network.
 * @global GC_Site    $current_blog The current site.
 *
 * @param string $domain    The requested domain.
 * @param string $path      The requested path.
 * @param bool   $subdomain Optional. Whether a subdomain (true) or subdirectory (false) configuration.
 *                          Default false.
 * @return bool|string True if bootstrap successfully populated `$current_blog` and `$current_site`.
 *                     False if bootstrap could not be properly completed.
 *                     Redirect URL if parts exist, but the request as a whole can not be fulfilled.
 */
function ms_load_current_site_and_network( $domain, $path, $subdomain = false ) {
	global $current_site, $current_blog;

	// If the network is defined in gc-config.php, we can simply use that.
	if ( defined( 'DOMAIN_CURRENT_SITE' ) && defined( 'PATH_CURRENT_SITE' ) ) {
		$current_site         = new stdClass();
		$current_site->id     = defined( 'SITE_ID_CURRENT_SITE' ) ? SITE_ID_CURRENT_SITE : 1;
		$current_site->domain = DOMAIN_CURRENT_SITE;
		$current_site->path   = PATH_CURRENT_SITE;
		if ( defined( 'BLOG_ID_CURRENT_SITE' ) ) {
			$current_site->blog_id = BLOG_ID_CURRENT_SITE;
		} elseif ( defined( 'BLOGID_CURRENT_SITE' ) ) { // Deprecated.
			$current_site->blog_id = BLOGID_CURRENT_SITE;
		}

		if ( 0 === strcasecmp( $current_site->domain, $domain ) && 0 === strcasecmp( $current_site->path, $path ) ) {
			$current_blog = get_site_by_path( $domain, $path );
		} elseif ( '/' !== $current_site->path && 0 === strcasecmp( $current_site->domain, $domain ) && 0 === stripos( $path, $current_site->path ) ) {
			/*
			 * If the current network has a path and also matches the domain and path of the request,
			 * we need to look for a site using the first path segment following the network's path.
			 */
			$current_blog = get_site_by_path( $domain, $path, 1 + count( explode( '/', trim( $current_site->path, '/' ) ) ) );
		} else {
			// Otherwise, use the first path segment (as usual).
			$current_blog = get_site_by_path( $domain, $path, 1 );
		}
	} elseif ( ! $subdomain ) {
		/*
		 * A "subdomain" installation can be re-interpreted to mean "can support any domain".
		 * If we're not dealing with one of these installations, then the important part is determining
		 * the network first, because we need the network's path to identify any sites.
		 */
		$current_site = gc_cache_get( 'current_network', 'site-options' );
		if ( ! $current_site ) {
			// Are there even two networks installed?
			$networks = get_networks( array( 'number' => 2 ) );
			if ( count( $networks ) === 1 ) {
				$current_site = array_shift( $networks );
				gc_cache_add( 'current_network', $current_site, 'site-options' );
			} elseif ( empty( $networks ) ) {
				// A network not found hook should fire here.
				return false;
			}
		}

		if ( empty( $current_site ) ) {
			$current_site = GC_Network::get_by_path( $domain, $path, 1 );
		}

		if ( empty( $current_site ) ) {
			/**
			 * Fires when a network cannot be found based on the requested domain and path.
			 *
			 * At the time of this action, the only recourse is to redirect somewhere
			 * and exit. If you want to declare a particular network, do so earlier.
			 *
			 * @since 4.4.0
			 *
			 * @param string $domain       The domain used to search for a network.
			 * @param string $path         The path used to search for a path.
			 */
			do_action( 'ms_network_not_found', $domain, $path );

			return false;
		} elseif ( $path === $current_site->path ) {
			$current_blog = get_site_by_path( $domain, $path );
		} else {
			// Search the network path + one more path segment (on top of the network path).
			$current_blog = get_site_by_path( $domain, $path, substr_count( $current_site->path, '/' ) );
		}
	} else {
		// Find the site by the domain and at most the first path segment.
		$current_blog = get_site_by_path( $domain, $path, 1 );
		if ( $current_blog ) {
			$current_site = GC_Network::get_instance( $current_blog->site_id ? $current_blog->site_id : 1 );
		} else {
			// If you don't have a site with the same domain/path as a network, you're pretty screwed, but:
			$current_site = GC_Network::get_by_path( $domain, $path, 1 );
		}
	}

	// The network declared by the site trumps any constants.
	if ( $current_blog && $current_blog->site_id != $current_site->id ) {
		$current_site = GC_Network::get_instance( $current_blog->site_id );
	}

	// No network has been found, bail.
	if ( empty( $current_site ) ) {
		/** This action is documented in gc-includes/ms-settings.php */
		do_action( 'ms_network_not_found', $domain, $path );

		return false;
	}

	// During activation of a new subdomain, the requested site does not yet exist.
	if ( empty( $current_blog ) && gc_installing() ) {
		$current_blog          = new stdClass();
		$current_blog->blog_id = 1;
		$blog_id               = 1;
		$current_blog->public  = 1;
	}

	// No site has been found, bail.
	if ( empty( $current_blog ) ) {
		// We're going to redirect to the network URL, with some possible modifications.
		$scheme      = is_ssl() ? 'https' : 'http';
		$destination = "$scheme://{$current_site->domain}{$current_site->path}";

		/**
		 * Fires when a network can be determined but a site cannot.
		 *
		 * At the time of this action, the only recourse is to redirect somewhere
		 * and exit. If you want to declare a particular site, do so earlier.
		 *
		 * @since 3.9.0
		 *
		 * @param GC_Network $current_site The network that had been determined.
		 * @param string     $domain       The domain used to search for a site.
		 * @param string     $path         The path used to search for a site.
		 */
		do_action( 'ms_site_not_found', $current_site, $domain, $path );

		if ( $subdomain && ! defined( 'NOBLOGREDIRECT' ) ) {
			// For a "subdomain" installation, redirect to the signup form specifically.
			$destination .= 'gc-signup.php?new=' . str_replace( '.' . $current_site->domain, '', $domain );
		} elseif ( $subdomain ) {
			/*
			 * For a "subdomain" installation, the NOBLOGREDIRECT constant
			 * can be used to avoid a redirect to the signup form.
			 * Using the ms_site_not_found action is preferred to the constant.
			 */
			if ( '%siteurl%' !== NOBLOGREDIRECT ) {
				$destination = NOBLOGREDIRECT;
			}
		} elseif ( 0 === strcasecmp( $current_site->domain, $domain ) ) {
			/*
			 * If the domain we were searching for matches the network's domain,
			 * it's no use redirecting back to ourselves -- it'll cause a loop.
			 * As we couldn't find a site, we're simply not installed.
			 */
			return false;
		}

		return $destination;
	}

	// Figure out the current network's main site.
	if ( empty( $current_site->blog_id ) ) {
		$current_site->blog_id = get_main_site_id( $current_site->id );
	}

	return true;
}

/**
 * Displays a failure message.
 *
 * Used when a blog's tables do not exist. Checks for a missing $gcdb->site table as well.
 *
 * @access private The `$domain` and `$path` parameters were added.
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param string $domain The requested domain for the error to reference.
 * @param string $path   The requested path for the error to reference.
 */
function ms_not_installed( $domain, $path ) {
	global $gcdb;

	if ( ! is_admin() ) {
		dead_db();
	}

	gc_load_translations_early();

	$title = __( '建立数据库连接时出错' );

	$msg   = '<h1>' . $title . '</h1>';
	$msg  .= '<p>' . __( '如果这是您的系统，请联系系统平台管理员。' ) . '';
	$msg  .= ' ' . __( '如果您是此系统网络的管理员，请检查您主机上的数据库服务器是否正常运行，以及数据表中是否包含错误。' ) . '</p>';
	$query = $gcdb->prepare( 'SHOW TABLES LIKE %s', $gcdb->esc_like( $gcdb->site ) );
	if ( ! $gcdb->get_var( $query ) ) {
		$msg .= '<p>' . sprintf(
			/* translators: %s: Table name. */
			__( '<strong>数据库表缺失。</strong>这意味着您主机上的数据库服务器未在运行、GeChiUI未被正确安装，或有人删除了 %s 表。您需要立即检查您的数据库。' ),
			'<code>' . $gcdb->site . '</code>'
		) . '</p>';
	} else {
		$msg .= '<p>' . sprintf(
			/* translators: 1: Site URL, 2: Table name, 3: Database name. */
			__( '<strong>无法找到系统%1$s。</strong>在数据库%3$s中搜索了数据表%2$s，这对吗？' ),
			'<code>' . rtrim( $domain . $path, '/' ) . '</code>',
			'<code>' . $gcdb->blogs . '</code>',
			'<code>' . DB_NAME . '</code>'
		) . '</p>';
	}
	$msg .= '<p><strong>' . __( '怎么办？' ) . '</strong> ';
	$msg .= sprintf(
		/* translators: %s: Documentation URL. */
		__( '阅读<a href="%s" target="_blank">调试 GeChiUI 系统网络</a>的文章。其中的一些建议可能会帮助您找出故障的原因。' ),
		__( 'https://www.gechiui.com/support/debugging-a-gechiui-network/' )
	);
	$msg .= ' ' . __( '如果您仍然看到此信息，请检查您系统的数据库中是否包含以下表：' ) . '</p><ul>';
	foreach ( $gcdb->tables( 'global' ) as $t => $table ) {
		if ( 'sitecategories' === $t ) {
			continue;
		}
		$msg .= '<li>' . $table . '</li>';
	}
	$msg .= '</ul>';

	gc_die( $msg, $title, array( 'response' => 500 ) );
}

/**
 * This deprecated function formerly set the site_name property of the $current_site object.
 *
 * This function simply returns the object, as before.
 * The bootstrap takes care of setting site_name.
 *
 * @access private
 * @deprecated 3.9.0 Use get_current_site() instead.
 *
 * @param GC_Network $current_site
 * @return GC_Network
 */
function get_current_site_name( $current_site ) {
	_deprecated_function( __FUNCTION__, '3.9.0', 'get_current_site()' );
	return $current_site;
}

/**
 * This deprecated function managed much of the site and network loading in multisite.
 *
 * The current bootstrap code is now responsible for parsing the site and network load as
 * well as setting the global $current_site object.
 *
 * @access private
 * @deprecated 3.9.0
 *
 * @global GC_Network $current_site
 *
 * @return GC_Network
 */
function gcmu_current_site() {
	global $current_site;
	_deprecated_function( __FUNCTION__, '3.9.0' );
	return $current_site;
}

/**
 * Retrieves an object containing information about the requested network.
 *
 * @since 3.9.0
 * @deprecated 4.7.0 Use get_network()
 * @see get_network()
 *
 * @internal In 4.6.0, converted to use get_network()
 *
 * @param object|int $network The network's database row or ID.
 * @return GC_Network|false Object containing network information if found, false if not.
 */
function gc_get_network( $network ) {
	_deprecated_function( __FUNCTION__, '4.7.0', 'get_network()' );

	$network = get_network( $network );
	if ( null === $network ) {
		return false;
	}

	return $network;
}
