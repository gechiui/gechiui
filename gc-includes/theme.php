<?php
/**
 * Theme, template, and stylesheet functions.
 *
 * @package GeChiUI
 * @subpackage Theme
 */

/**
 * Returns an array of GC_Theme objects based on the arguments.
 *
 * Despite advances over get_themes(), this function is quite expensive, and grows
 * linearly with additional themes. Stick to gc_get_theme() if possible.
 *
 *
 *
 * @global array $gc_theme_directories
 *
 * @param array $args {
 *     Optional. The search arguments.
 *
 *     @type mixed $errors  True to return themes with errors, false to return
 *                          themes without errors, null to return all themes.
 *                          Default false.
 *     @type mixed $allowed (Multisite) True to return only allowed themes for a site.
 *                          False to return only disallowed themes for a site.
 *                          'site' to return only site-allowed themes.
 *                          'network' to return only network-allowed themes.
 *                          Null to return all themes. Default null.
 *     @type int   $blog_id (Multisite) The blog ID used to calculate which themes
 *                          are allowed. Default 0, synonymous for the current blog.
 * }
 * @return GC_Theme[] Array of GC_Theme objects.
 */
function gc_get_themes( $args = array() ) {
	global $gc_theme_directories;

	$defaults = array(
		'errors'  => false,
		'allowed' => null,
		'blog_id' => 0,
	);
	$args     = gc_parse_args( $args, $defaults );

	$theme_directories = search_theme_directories();

	if ( is_array( $gc_theme_directories ) && count( $gc_theme_directories ) > 1 ) {
		// Make sure the active theme wins out, in case search_theme_directories() picks the wrong
		// one in the case of a conflict. (Normally, last registered theme root wins.)
		$current_theme = get_stylesheet();
		if ( isset( $theme_directories[ $current_theme ] ) ) {
			$root_of_current_theme = get_raw_theme_root( $current_theme );
			if ( ! in_array( $root_of_current_theme, $gc_theme_directories, true ) ) {
				$root_of_current_theme = GC_CONTENT_DIR . $root_of_current_theme;
			}
			$theme_directories[ $current_theme ]['theme_root'] = $root_of_current_theme;
		}
	}

	if ( empty( $theme_directories ) ) {
		return array();
	}

	if ( is_multisite() && null !== $args['allowed'] ) {
		$allowed = $args['allowed'];
		if ( 'network' === $allowed ) {
			$theme_directories = array_intersect_key( $theme_directories, GC_Theme::get_allowed_on_network() );
		} elseif ( 'site' === $allowed ) {
			$theme_directories = array_intersect_key( $theme_directories, GC_Theme::get_allowed_on_site( $args['blog_id'] ) );
		} elseif ( $allowed ) {
			$theme_directories = array_intersect_key( $theme_directories, GC_Theme::get_allowed( $args['blog_id'] ) );
		} else {
			$theme_directories = array_diff_key( $theme_directories, GC_Theme::get_allowed( $args['blog_id'] ) );
		}
	}

	$themes         = array();
	static $_themes = array();

	foreach ( $theme_directories as $theme => $theme_root ) {
		if ( isset( $_themes[ $theme_root['theme_root'] . '/' . $theme ] ) ) {
			$themes[ $theme ] = $_themes[ $theme_root['theme_root'] . '/' . $theme ];
		} else {
			$themes[ $theme ] = new GC_Theme( $theme, $theme_root['theme_root'] );

			$_themes[ $theme_root['theme_root'] . '/' . $theme ] = $themes[ $theme ];
		}
	}

	if ( null !== $args['errors'] ) {
		foreach ( $themes as $theme => $gc_theme ) {
			if ( $gc_theme->errors() != $args['errors'] ) {
				unset( $themes[ $theme ] );
			}
		}
	}

	return $themes;
}

/**
 * Gets a GC_Theme object for a theme.
 *
 *
 *
 * @global array $gc_theme_directories
 *
 * @param string $stylesheet Optional. Directory name for the theme. Defaults to active theme.
 * @param string $theme_root Optional. Absolute path of the theme root to look in.
 *                           If not specified, get_raw_theme_root() is used to calculate
 *                           the theme root for the $stylesheet provided (or active theme).
 * @return GC_Theme Theme object. Be sure to check the object's exists() method
 *                  if you need to confirm the theme's existence.
 */
function gc_get_theme( $stylesheet = '', $theme_root = '' ) {
	global $gc_theme_directories;

	if ( empty( $stylesheet ) ) {
		$stylesheet = get_stylesheet();
	}

	if ( empty( $theme_root ) ) {
		$theme_root = get_raw_theme_root( $stylesheet );
		if ( false === $theme_root ) {
			$theme_root = GC_CONTENT_DIR . '/themes';
		} elseif ( ! in_array( $theme_root, (array) $gc_theme_directories, true ) ) {
			$theme_root = GC_CONTENT_DIR . $theme_root;
		}
	}

	return new GC_Theme( $stylesheet, $theme_root );
}

/**
 * Clears the cache held by get_theme_roots() and GC_Theme.
 *
 *
 * @param bool $clear_update_cache Whether to clear the theme updates cache.
 */
function gc_clean_themes_cache( $clear_update_cache = true ) {
	if ( $clear_update_cache ) {
		delete_site_transient( 'update_themes' );
	}
	search_theme_directories( true );
	foreach ( gc_get_themes( array( 'errors' => null ) ) as $theme ) {
		$theme->cache_delete();
	}
}

/**
 * Whether a child theme is in use.
 *
 *
 *
 * @return bool True if a child theme is in use, false otherwise.
 */
function is_child_theme() {
	return ( TEMPLATEPATH !== STYLESHEETPATH );
}

/**
 * Retrieves name of the current stylesheet.
 *
 * The theme name that is currently set as the front end theme.
 *
 * For all intents and purposes, the template name and the stylesheet name
 * are going to be the same for most cases.
 *
 *
 *
 * @return string Stylesheet name.
 */
function get_stylesheet() {
	/**
	 * Filters the name of current stylesheet.
	 *
	 *
	 * @param string $stylesheet Name of the current stylesheet.
	 */
	return apply_filters( 'stylesheet', get_option( 'stylesheet' ) );
}

/**
 * Retrieves stylesheet directory path for the active theme.
 *
 *
 *
 * @return string Path to active theme's stylesheet directory.
 */
function get_stylesheet_directory() {
	$stylesheet     = get_stylesheet();
	$theme_root     = get_theme_root( $stylesheet );
	$stylesheet_dir = "$theme_root/$stylesheet";

	/**
	 * Filters the stylesheet directory path for the active theme.
	 *
	 *
	 * @param string $stylesheet_dir Absolute path to the active theme.
	 * @param string $stylesheet     Directory name of the active theme.
	 * @param string $theme_root     Absolute path to themes directory.
	 */
	return apply_filters( 'stylesheet_directory', $stylesheet_dir, $stylesheet, $theme_root );
}

/**
 * Retrieves stylesheet directory URI for the active theme.
 *
 *
 *
 * @return string URI to active theme's stylesheet directory.
 */
function get_stylesheet_directory_uri() {
	$stylesheet         = str_replace( '%2F', '/', rawurlencode( get_stylesheet() ) );
	$theme_root_uri     = get_theme_root_uri( $stylesheet );
	$stylesheet_dir_uri = "$theme_root_uri/$stylesheet";

	/**
	 * Filters the stylesheet directory URI.
	 *
	 *
	 * @param string $stylesheet_dir_uri Stylesheet directory URI.
	 * @param string $stylesheet         Name of the activated theme's directory.
	 * @param string $theme_root_uri     Themes root URI.
	 */
	return apply_filters( 'stylesheet_directory_uri', $stylesheet_dir_uri, $stylesheet, $theme_root_uri );
}

/**
 * Retrieves stylesheet URI for the active theme.
 *
 * The stylesheet file name is 'style.css' which is appended to the stylesheet directory URI path.
 * See get_stylesheet_directory_uri().
 *
 *
 *
 * @return string URI to active theme's stylesheet.
 */
function get_stylesheet_uri() {
	$stylesheet_dir_uri = get_stylesheet_directory_uri();
	$stylesheet_uri     = $stylesheet_dir_uri . '/style.css';
	/**
	 * Filters the URI of the active theme stylesheet.
	 *
	 *
	 * @param string $stylesheet_uri     Stylesheet URI for the active theme/child theme.
	 * @param string $stylesheet_dir_uri Stylesheet directory URI for the active theme/child theme.
	 */
	return apply_filters( 'stylesheet_uri', $stylesheet_uri, $stylesheet_dir_uri );
}

/**
 * Retrieves the localized stylesheet URI.
 *
 * The stylesheet directory for the localized stylesheet files are located, by
 * default, in the base theme directory. The name of the locale file will be the
 * locale followed by '.css'. If that does not exist, then the text direction
 * stylesheet will be checked for existence, for example 'ltr.css'.
 *
 * The theme may change the location of the stylesheet directory by either using
 * the {@see 'stylesheet_directory_uri'} or {@see 'locale_stylesheet_uri'} filters.
 *
 * If you want to change the location of the stylesheet files for the entire
 * GeChiUI workflow, then change the former. If you just have the locale in a
 * separate folder, then change the latter.
 *
 *
 *
 * @global GC_Locale $gc_locale GeChiUI date and time locale object.
 *
 * @return string URI to active theme's localized stylesheet.
 */
function get_locale_stylesheet_uri() {
	global $gc_locale;
	$stylesheet_dir_uri = get_stylesheet_directory_uri();
	$dir                = get_stylesheet_directory();
	$locale             = get_locale();
	if ( file_exists( "$dir/$locale.css" ) ) {
		$stylesheet_uri = "$stylesheet_dir_uri/$locale.css";
	} elseif ( ! empty( $gc_locale->text_direction ) && file_exists( "$dir/{$gc_locale->text_direction}.css" ) ) {
		$stylesheet_uri = "$stylesheet_dir_uri/{$gc_locale->text_direction}.css";
	} else {
		$stylesheet_uri = '';
	}
	/**
	 * Filters the localized stylesheet URI.
	 *
	 *
	 * @param string $stylesheet_uri     Localized stylesheet URI.
	 * @param string $stylesheet_dir_uri Stylesheet directory URI.
	 */
	return apply_filters( 'locale_stylesheet_uri', $stylesheet_uri, $stylesheet_dir_uri );
}

/**
 * Retrieves name of the active theme.
 *
 *
 *
 * @return string Template name.
 */
function get_template() {
	/**
	 * Filters the name of the active theme.
	 *
	 *
	 * @param string $template active theme's directory name.
	 */
	return apply_filters( 'template', get_option( 'template' ) );
}

/**
 * Retrieves template directory path for the active theme.
 *
 *
 *
 * @return string Path to active theme's template directory.
 */
function get_template_directory() {
	$template     = get_template();
	$theme_root   = get_theme_root( $template );
	$template_dir = "$theme_root/$template";

	/**
	 * Filters the active theme directory path.
	 *
	 *
	 * @param string $template_dir The path of the active theme directory.
	 * @param string $template     Directory name of the active theme.
	 * @param string $theme_root   Absolute path to the themes directory.
	 */
	return apply_filters( 'template_directory', $template_dir, $template, $theme_root );
}

/**
 * Retrieves template directory URI for the active theme.
 *
 *
 *
 * @return string URI to active theme's template directory.
 */
function get_template_directory_uri() {
	$template         = str_replace( '%2F', '/', rawurlencode( get_template() ) );
	$theme_root_uri   = get_theme_root_uri( $template );
	$template_dir_uri = "$theme_root_uri/$template";

	/**
	 * Filters the active theme directory URI.
	 *
	 *
	 * @param string $template_dir_uri The URI of the active theme directory.
	 * @param string $template         Directory name of the active theme.
	 * @param string $theme_root_uri   The themes root URI.
	 */
	return apply_filters( 'template_directory_uri', $template_dir_uri, $template, $theme_root_uri );
}

/**
 * Retrieves theme roots.
 *
 *
 *
 * @global array $gc_theme_directories
 *
 * @return array|string An array of theme roots keyed by template/stylesheet
 *                      or a single theme root if all themes have the same root.
 */
function get_theme_roots() {
	global $gc_theme_directories;

	if ( ! is_array( $gc_theme_directories ) || count( $gc_theme_directories ) <= 1 ) {
		return '/themes';
	}

	$theme_roots = get_site_transient( 'theme_roots' );
	if ( false === $theme_roots ) {
		search_theme_directories( true ); // Regenerate the transient.
		$theme_roots = get_site_transient( 'theme_roots' );
	}
	return $theme_roots;
}

/**
 * Registers a directory that contains themes.
 *
 *
 *
 * @global array $gc_theme_directories
 *
 * @param string $directory Either the full filesystem path to a theme folder
 *                          or a folder within GC_CONTENT_DIR.
 * @return bool True if successfully registered a directory that contains themes,
 *              false if the directory does not exist.
 */
function register_theme_directory( $directory ) {
	global $gc_theme_directories;

	if ( ! file_exists( $directory ) ) {
		// Try prepending as the theme directory could be relative to the content directory.
		$directory = GC_CONTENT_DIR . '/' . $directory;
		// If this directory does not exist, return and do not register.
		if ( ! file_exists( $directory ) ) {
			return false;
		}
	}

	if ( ! is_array( $gc_theme_directories ) ) {
		$gc_theme_directories = array();
	}

	$untrailed = untrailingslashit( $directory );
	if ( ! empty( $untrailed ) && ! in_array( $untrailed, $gc_theme_directories, true ) ) {
		$gc_theme_directories[] = $untrailed;
	}

	return true;
}

/**
 * Searches all registered theme directories for complete and valid themes.
 *
 *
 *
 * @global array $gc_theme_directories
 *
 * @param bool $force Optional. Whether to force a new directory scan. Default false.
 * @return array|false Valid themes found on success, false on failure.
 */
function search_theme_directories( $force = false ) {
	global $gc_theme_directories;
	static $found_themes = null;

	if ( empty( $gc_theme_directories ) ) {
		return false;
	}

	if ( ! $force && isset( $found_themes ) ) {
		return $found_themes;
	}

	$found_themes = array();

	$gc_theme_directories = (array) $gc_theme_directories;
	$relative_theme_roots = array();

	/*
	 * Set up maybe-relative, maybe-absolute array of theme directories.
	 * We always want to return absolute, but we need to cache relative
	 * to use in get_theme_root().
	 */
	foreach ( $gc_theme_directories as $theme_root ) {
		if ( 0 === strpos( $theme_root, GC_CONTENT_DIR ) ) {
			$relative_theme_roots[ str_replace( GC_CONTENT_DIR, '', $theme_root ) ] = $theme_root;
		} else {
			$relative_theme_roots[ $theme_root ] = $theme_root;
		}
	}

	/**
	 * Filters whether to get the cache of the registered theme directories.
	 *
	 *
	 * @param bool   $cache_expiration Whether to get the cache of the theme directories. Default false.
	 * @param string $context          The class or function name calling the filter.
	 */
	$cache_expiration = apply_filters( 'gc_cache_themes_persistently', false, 'search_theme_directories' );

	if ( $cache_expiration ) {
		$cached_roots = get_site_transient( 'theme_roots' );
		if ( is_array( $cached_roots ) ) {
			foreach ( $cached_roots as $theme_dir => $theme_root ) {
				// A cached theme root is no longer around, so skip it.
				if ( ! isset( $relative_theme_roots[ $theme_root ] ) ) {
					continue;
				}
				$found_themes[ $theme_dir ] = array(
					'theme_file' => $theme_dir . '/style.css',
					'theme_root' => $relative_theme_roots[ $theme_root ], // Convert relative to absolute.
				);
			}
			return $found_themes;
		}
		if ( ! is_int( $cache_expiration ) ) {
			$cache_expiration = 30 * MINUTE_IN_SECONDS;
		}
	} else {
		$cache_expiration = 30 * MINUTE_IN_SECONDS;
	}

	/* Loop the registered theme directories and extract all themes */
	foreach ( $gc_theme_directories as $theme_root ) {

		// Start with directories in the root of the active theme directory.
		$dirs = @ scandir( $theme_root );
		if ( ! $dirs ) {
			trigger_error( "$theme_root is not readable", E_USER_NOTICE );
			continue;
		}
		foreach ( $dirs as $dir ) {
			if ( ! is_dir( $theme_root . '/' . $dir ) || '.' === $dir[0] || 'CVS' === $dir ) {
				continue;
			}
			if ( file_exists( $theme_root . '/' . $dir . '/style.css' ) ) {
				// gc-content/themes/a-single-theme
				// gc-content/themes is $theme_root, a-single-theme is $dir.
				$found_themes[ $dir ] = array(
					'theme_file' => $dir . '/style.css',
					'theme_root' => $theme_root,
				);
			} else {
				$found_theme = false;
				// gc-content/themes/a-folder-of-themes/*
				// gc-content/themes is $theme_root, a-folder-of-themes is $dir, then themes are $sub_dirs.
				$sub_dirs = @ scandir( $theme_root . '/' . $dir );
				if ( ! $sub_dirs ) {
					trigger_error( "$theme_root/$dir is not readable", E_USER_NOTICE );
					continue;
				}
				foreach ( $sub_dirs as $sub_dir ) {
					if ( ! is_dir( $theme_root . '/' . $dir . '/' . $sub_dir ) || '.' === $dir[0] || 'CVS' === $dir ) {
						continue;
					}
					if ( ! file_exists( $theme_root . '/' . $dir . '/' . $sub_dir . '/style.css' ) ) {
						continue;
					}
					$found_themes[ $dir . '/' . $sub_dir ] = array(
						'theme_file' => $dir . '/' . $sub_dir . '/style.css',
						'theme_root' => $theme_root,
					);
					$found_theme                           = true;
				}
				// Never mind the above, it's just a theme missing a style.css.
				// Return it; GC_Theme will catch the error.
				if ( ! $found_theme ) {
					$found_themes[ $dir ] = array(
						'theme_file' => $dir . '/style.css',
						'theme_root' => $theme_root,
					);
				}
			}
		}
	}

	asort( $found_themes );

	$theme_roots          = array();
	$relative_theme_roots = array_flip( $relative_theme_roots );

	foreach ( $found_themes as $theme_dir => $theme_data ) {
		$theme_roots[ $theme_dir ] = $relative_theme_roots[ $theme_data['theme_root'] ]; // Convert absolute to relative.
	}

	if ( get_site_transient( 'theme_roots' ) != $theme_roots ) {
		set_site_transient( 'theme_roots', $theme_roots, $cache_expiration );
	}

	return $found_themes;
}

/**
 * Retrieves path to themes directory.
 *
 * Does not have trailing slash.
 *
 *
 *
 * @global array $gc_theme_directories
 *
 * @param string $stylesheet_or_template Optional. The stylesheet or template name of the theme.
 *                                       Default is to leverage the main theme root.
 * @return string Themes directory path.
 */
function get_theme_root( $stylesheet_or_template = '' ) {
	global $gc_theme_directories;

	$theme_root = '';

	if ( $stylesheet_or_template ) {
		$theme_root = get_raw_theme_root( $stylesheet_or_template );
		if ( $theme_root ) {
			// Always prepend GC_CONTENT_DIR unless the root currently registered as a theme directory.
			// This gives relative theme roots the benefit of the doubt when things go haywire.
			if ( ! in_array( $theme_root, (array) $gc_theme_directories, true ) ) {
				$theme_root = GC_CONTENT_DIR . $theme_root;
			}
		}
	}

	if ( ! $theme_root ) {
		$theme_root = GC_CONTENT_DIR . '/themes';
	}

	/**
	 * Filters the absolute path to the themes directory.
	 *
	 *
	 * @param string $theme_root Absolute path to themes directory.
	 */
	return apply_filters( 'theme_root', $theme_root );
}

/**
 * Retrieves URI for themes directory.
 *
 * Does not have trailing slash.
 *
 *
 *
 * @global array $gc_theme_directories
 *
 * @param string $stylesheet_or_template Optional. The stylesheet or template name of the theme.
 *                                       Default is to leverage the main theme root.
 * @param string $theme_root             Optional. The theme root for which calculations will be based,
 *                                       preventing the need for a get_raw_theme_root() call. Default empty.
 * @return string Themes directory URI.
 */
function get_theme_root_uri( $stylesheet_or_template = '', $theme_root = '' ) {
	global $gc_theme_directories;

	if ( $stylesheet_or_template && ! $theme_root ) {
		$theme_root = get_raw_theme_root( $stylesheet_or_template );
	}

	if ( $stylesheet_or_template && $theme_root ) {
		if ( in_array( $theme_root, (array) $gc_theme_directories, true ) ) {
			// Absolute path. Make an educated guess. YMMV -- but note the filter below.
			if ( 0 === strpos( $theme_root, GC_CONTENT_DIR ) ) {
				$theme_root_uri = content_url( str_replace( GC_CONTENT_DIR, '', $theme_root ) );
			} elseif ( 0 === strpos( $theme_root, ABSPATH ) ) {
				$theme_root_uri = site_url( str_replace( ABSPATH, '', $theme_root ) );
			} elseif ( 0 === strpos( $theme_root, GC_PLUGIN_DIR ) || 0 === strpos( $theme_root, GCMU_PLUGIN_DIR ) ) {
				$theme_root_uri = plugins_url( basename( $theme_root ), $theme_root );
			} else {
				$theme_root_uri = $theme_root;
			}
		} else {
			$theme_root_uri = content_url( $theme_root );
		}
	} else {
		$theme_root_uri = content_url( 'themes' );
	}

	/**
	 * Filters the URI for themes directory.
	 *
	 *
	 * @param string $theme_root_uri         The URI for themes directory.
	 * @param string $siteurl                GeChiUI web address which is set in General Options.
	 * @param string $stylesheet_or_template The stylesheet or template name of the theme.
	 */
	return apply_filters( 'theme_root_uri', $theme_root_uri, get_option( 'siteurl' ), $stylesheet_or_template );
}

/**
 * Gets the raw theme root relative to the content directory with no filters applied.
 *
 *
 *
 * @global array $gc_theme_directories
 *
 * @param string $stylesheet_or_template The stylesheet or template name of the theme.
 * @param bool   $skip_cache             Optional. Whether to skip the cache.
 *                                       Defaults to false, meaning the cache is used.
 * @return string Theme root.
 */
function get_raw_theme_root( $stylesheet_or_template, $skip_cache = false ) {
	global $gc_theme_directories;

	if ( ! is_array( $gc_theme_directories ) || count( $gc_theme_directories ) <= 1 ) {
		return '/themes';
	}

	$theme_root = false;

	// If requesting the root for the active theme, consult options to avoid calling get_theme_roots().
	if ( ! $skip_cache ) {
		if ( get_option( 'stylesheet' ) == $stylesheet_or_template ) {
			$theme_root = get_option( 'stylesheet_root' );
		} elseif ( get_option( 'template' ) == $stylesheet_or_template ) {
			$theme_root = get_option( 'template_root' );
		}
	}

	if ( empty( $theme_root ) ) {
		$theme_roots = get_theme_roots();
		if ( ! empty( $theme_roots[ $stylesheet_or_template ] ) ) {
			$theme_root = $theme_roots[ $stylesheet_or_template ];
		}
	}

	return $theme_root;
}

/**
 * Displays localized stylesheet link element.
 *
 *
 */
function locale_stylesheet() {
	$stylesheet = get_locale_stylesheet_uri();
	if ( empty( $stylesheet ) ) {
		return;
	}

	$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

	printf(
		'<link rel="stylesheet" href="%s"%s media="screen" />',
		$stylesheet,
		$type_attr
	);
}

/**
 * Switches the theme.
 *
 * Accepts one argument: $stylesheet of the theme. It also accepts an additional function signature
 * of two arguments: $template then $stylesheet. This is for backward compatibility.
 *
 *
 *
 * @global array                $gc_theme_directories
 * @global GC_Customize_Manager $gc_customize
 * @global array                $sidebars_widgets
 *
 * @param string $stylesheet Stylesheet name.
 */
function switch_theme( $stylesheet ) {
	global $gc_theme_directories, $gc_customize, $sidebars_widgets;

	$requirements = validate_theme_requirements( $stylesheet );
	if ( is_gc_error( $requirements ) ) {
		gc_die( $requirements );
	}

	$_sidebars_widgets = null;
	if ( 'gc_ajax_customize_save' === current_action() ) {
		$old_sidebars_widgets_data_setting = $gc_customize->get_setting( 'old_sidebars_widgets_data' );
		if ( $old_sidebars_widgets_data_setting ) {
			$_sidebars_widgets = $gc_customize->post_value( $old_sidebars_widgets_data_setting );
		}
	} elseif ( is_array( $sidebars_widgets ) ) {
		$_sidebars_widgets = $sidebars_widgets;
	}

	if ( is_array( $_sidebars_widgets ) ) {
		set_theme_mod(
			'sidebars_widgets',
			array(
				'time' => time(),
				'data' => $_sidebars_widgets,
			)
		);
	}

	$nav_menu_locations = get_theme_mod( 'nav_menu_locations' );
	update_option( 'theme_switch_menu_locations', $nav_menu_locations );

	if ( func_num_args() > 1 ) {
		$stylesheet = func_get_arg( 1 );
	}

	$old_theme = gc_get_theme();
	$new_theme = gc_get_theme( $stylesheet );
	$template  = $new_theme->get_template();

	if ( gc_is_recovery_mode() ) {
		$paused_themes = gc_paused_themes();
		$paused_themes->delete( $old_theme->get_stylesheet() );
		$paused_themes->delete( $old_theme->get_template() );
	}

	update_option( 'template', $template );
	update_option( 'stylesheet', $stylesheet );

	if ( count( $gc_theme_directories ) > 1 ) {
		update_option( 'template_root', get_raw_theme_root( $template, true ) );
		update_option( 'stylesheet_root', get_raw_theme_root( $stylesheet, true ) );
	} else {
		delete_option( 'template_root' );
		delete_option( 'stylesheet_root' );
	}

	$new_name = $new_theme->get( 'Name' );

	update_option( 'current_theme', $new_name );

	// Migrate from the old mods_{name} option to theme_mods_{slug}.
	if ( is_admin() && false === get_option( 'theme_mods_' . $stylesheet ) ) {
		$default_theme_mods = (array) get_option( 'mods_' . $new_name );
		if ( ! empty( $nav_menu_locations ) && empty( $default_theme_mods['nav_menu_locations'] ) ) {
			$default_theme_mods['nav_menu_locations'] = $nav_menu_locations;
		}
		add_option( "theme_mods_$stylesheet", $default_theme_mods );
	} else {
		/*
		 * Since retrieve_widgets() is called when initializing a theme in the Customizer,
		 * we need to remove the theme mods to avoid overwriting changes made via
		 * the Customizer when accessing gc-admin/widgets.php.
		 */
		if ( 'gc_ajax_customize_save' === current_action() ) {
			remove_theme_mod( 'sidebars_widgets' );
		}
	}

	update_option( 'theme_switched', $old_theme->get_stylesheet() );

	/**
	 * Fires after the theme is switched.
	 *
	 *
	 * @param string   $new_name  Name of the new theme.
	 * @param GC_Theme $new_theme GC_Theme instance of the new theme.
	 * @param GC_Theme $old_theme GC_Theme instance of the old theme.
	 */
	do_action( 'switch_theme', $new_name, $new_theme, $old_theme );
}

/**
 * Checks that the active theme has 'index.php' and 'style.css' files.
 *
 * Does not initially check the default theme, which is the fallback and should always exist.
 * But if it doesn't exist, it'll fall back to the latest core default theme that does exist.
 * Will switch theme to the fallback theme if active theme does not validate.
 *
 * You can use the {@see 'validate_current_theme'} filter to return false to disable
 * this functionality.
 *
 *
 *
 * @see GC_DEFAULT_THEME
 *
 * @return bool
 */
function validate_current_theme() {
	/**
	 * Filters whether to validate the active theme.
	 *
	 *
	 * @param bool $validate Whether to validate the active theme. Default true.
	 */
	if ( gc_installing() || ! apply_filters( 'validate_current_theme', true ) ) {
		return true;
	}

	if ( ! file_exists( get_template_directory() . '/index.php' ) ) {
		// Invalid.
	} elseif ( ! file_exists( get_template_directory() . '/style.css' ) ) {
		// Invalid.
	} elseif ( is_child_theme() && ! file_exists( get_stylesheet_directory() . '/style.css' ) ) {
		// Invalid.
	} else {
		// Valid.
		return true;
	}

	$default = gc_get_theme( GC_DEFAULT_THEME );
	if ( $default->exists() ) {
		switch_theme( GC_DEFAULT_THEME );
		return false;
	}

	/**
	 * If we're in an invalid state but GC_DEFAULT_THEME doesn't exist,
	 * switch to the latest core default theme that's installed.
	 *
	 * If it turns out that this latest core default theme is our current
	 * theme, then there's nothing we can do about that, so we have to bail,
	 * rather than going into an infinite loop. (This is why there are
	 * checks against GC_DEFAULT_THEME above, also.) We also can't do anything
	 * if it turns out there is no default theme installed. (That's `false`.)
	 */
	$default = GC_Theme::get_core_default_theme();
	if ( false === $default || get_stylesheet() == $default->get_stylesheet() ) {
		return true;
	}

	switch_theme( $default->get_stylesheet() );
	return false;
}

/**
 * Validates the theme requirements for GeChiUI version and PHP version.
 *
 * Uses the information from `Requires at least` and `Requires PHP` headers
 * defined in the theme's `style.css` file.
 *
 *
 *
 *
 * @param string $stylesheet Directory name for the theme.
 * @return true|GC_Error True if requirements are met, GC_Error on failure.
 */
function validate_theme_requirements( $stylesheet ) {
	$theme = gc_get_theme( $stylesheet );

	$requirements = array(
		'requires'     => ! empty( $theme->get( 'RequiresGC' ) ) ? $theme->get( 'RequiresGC' ) : '',
		'requires_php' => ! empty( $theme->get( 'RequiresPHP' ) ) ? $theme->get( 'RequiresPHP' ) : '',
	);

	$compatible_gc  = is_gc_version_compatible( $requirements['requires'] );
	$compatible_php = is_php_version_compatible( $requirements['requires_php'] );

	if ( ! $compatible_gc && ! $compatible_php ) {
		return new GC_Error(
			'theme_gc_php_incompatible',
			sprintf(
				/* translators: %s: Theme name. */
				_x( '<strong>错误：</strong>当前的GeChiUI和PHP版本不符合%s的最低要求。', 'theme' ),
				$theme->display( 'Name' )
			)
		);
	} elseif ( ! $compatible_php ) {
		return new GC_Error(
			'theme_php_incompatible',
			sprintf(
				/* translators: %s: Theme name. */
				_x( '<strong>错误：</strong>当前的PHP版本不符合%s的最低要求。', 'theme' ),
				$theme->display( 'Name' )
			)
		);
	} elseif ( ! $compatible_gc ) {
		return new GC_Error(
			'theme_gc_incompatible',
			sprintf(
				/* translators: %s: Theme name. */
				_x( '<strong>错误：</strong>当前的GeChiUI版本不符合%s的最低要求。', 'theme' ),
				$theme->display( 'Name' )
			)
		);
	}

	return true;
}

/**
 * Retrieves all theme modifications.
 *
 *
 *
 *
 * @return array Theme modifications.
 */
function get_theme_mods() {
	$theme_slug = get_option( 'stylesheet' );
	$mods       = get_option( "theme_mods_$theme_slug" );

	if ( false === $mods ) {
		$theme_name = get_option( 'current_theme' );
		if ( false === $theme_name ) {
			$theme_name = gc_get_theme()->get( 'Name' );
		}

		$mods = get_option( "mods_$theme_name" ); // Deprecated location.
		if ( is_admin() && false !== $mods ) {
			update_option( "theme_mods_$theme_slug", $mods );
			delete_option( "mods_$theme_name" );
		}
	}

	if ( ! is_array( $mods ) ) {
		$mods = array();
	}

	return $mods;
}

/**
 * Retrieves theme modification value for the active theme.
 *
 * If the modification name does not exist and `$default` is a string, then the
 * default will be passed through the {@link https://www.php.net/sprintf sprintf()}
 * PHP function with the template directory URI as the first value and the
 * stylesheet directory URI as the second value.
 *
 *
 *
 * @param string $name    Theme modification name.
 * @param mixed  $default Optional. Theme modification default value. Default false.
 * @return mixed Theme modification value.
 */
function get_theme_mod( $name, $default = false ) {
	$mods = get_theme_mods();

	if ( isset( $mods[ $name ] ) ) {
		/**
		 * Filters the theme modification, or 'theme_mod', value.
		 *
		 * The dynamic portion of the hook name, `$name`, refers to the key name
		 * of the modification array. For example, 'header_textcolor', 'header_image',
		 * and so on depending on the theme options.
		 *
		 *
		 * @param mixed $current_mod The value of the active theme modification.
		 */
		return apply_filters( "theme_mod_{$name}", $mods[ $name ] );
	}

	if ( is_string( $default ) ) {
		// Only run the replacement if an sprintf() string format pattern was found.
		if ( preg_match( '#(?<!%)%(?:\d+\$?)?s#', $default ) ) {
			// Remove a single trailing percent sign.
			$default = preg_replace( '#(?<!%)%$#', '', $default );
			$default = sprintf( $default, get_template_directory_uri(), get_stylesheet_directory_uri() );
		}
	}

	/** This filter is documented in gc-includes/theme.php */
	return apply_filters( "theme_mod_{$name}", $default );
}

/**
 * Updates theme modification value for the active theme.
 *
 *
 *
 *
 * @param string $name  Theme modification name.
 * @param mixed  $value Theme modification value.
 * @return bool True if the value was updated, false otherwise.
 */
function set_theme_mod( $name, $value ) {
	$mods      = get_theme_mods();
	$old_value = isset( $mods[ $name ] ) ? $mods[ $name ] : false;

	/**
	 * Filters the theme modification, or 'theme_mod', value on save.
	 *
	 * The dynamic portion of the hook name, `$name`, refers to the key name
	 * of the modification array. For example, 'header_textcolor', 'header_image',
	 * and so on depending on the theme options.
	 *
	 *
	 * @param mixed $value     The new value of the theme modification.
	 * @param mixed $old_value The current value of the theme modification.
	 */
	$mods[ $name ] = apply_filters( "pre_set_theme_mod_{$name}", $value, $old_value );

	$theme = get_option( 'stylesheet' );

	return update_option( "theme_mods_$theme", $mods );
}

/**
 * Removes theme modification name from active theme list.
 *
 * If removing the name also removes all elements, then the entire option
 * will be removed.
 *
 *
 *
 * @param string $name Theme modification name.
 */
function remove_theme_mod( $name ) {
	$mods = get_theme_mods();

	if ( ! isset( $mods[ $name ] ) ) {
		return;
	}

	unset( $mods[ $name ] );

	if ( empty( $mods ) ) {
		remove_theme_mods();
		return;
	}

	$theme = get_option( 'stylesheet' );

	update_option( "theme_mods_$theme", $mods );
}

/**
 * Removes theme modifications option for the active theme.
 *
 *
 */
function remove_theme_mods() {
	delete_option( 'theme_mods_' . get_option( 'stylesheet' ) );

	// Old style.
	$theme_name = get_option( 'current_theme' );
	if ( false === $theme_name ) {
		$theme_name = gc_get_theme()->get( 'Name' );
	}

	delete_option( 'mods_' . $theme_name );
}

/**
 * Retrieves the custom header text color in 3- or 6-digit hexadecimal form.
 *
 *
 *
 * @return string Header text color in 3- or 6-digit hexadecimal form (minus the hash symbol).
 */
function get_header_textcolor() {
	return get_theme_mod( 'header_textcolor', get_theme_support( 'custom-header', 'default-text-color' ) );
}

/**
 * Displays the custom header text color in 3- or 6-digit hexadecimal form (minus the hash symbol).
 *
 *
 */
function header_textcolor() {
	echo get_header_textcolor();
}

/**
 * Whether to display the header text.
 *
 *
 *
 * @return bool
 */
function display_header_text() {
	if ( ! current_theme_supports( 'custom-header', 'header-text' ) ) {
		return false;
	}

	$text_color = get_theme_mod( 'header_textcolor', get_theme_support( 'custom-header', 'default-text-color' ) );
	return 'blank' !== $text_color;
}

/**
 * Checks whether a header image is set or not.
 *
 *
 *
 * @see get_header_image()
 *
 * @return bool Whether a header image is set or not.
 */
function has_header_image() {
	return (bool) get_header_image();
}

/**
 * Retrieves header image for custom header.
 *
 *
 *
 * @return string|false
 */
function get_header_image() {
	$url = get_theme_mod( 'header_image', get_theme_support( 'custom-header', 'default-image' ) );

	if ( 'remove-header' === $url ) {
		return false;
	}

	if ( is_random_header_image() ) {
		$url = get_random_header_image();
	}

	return esc_url_raw( set_url_scheme( $url ) );
}

/**
 * Creates image tag markup for a custom header image.
 *
 *
 *
 * @param array $attr Optional. Additional attributes for the image tag. Can be used
 *                              to override the default attributes. Default empty.
 * @return string HTML image element markup or empty string on failure.
 */
function get_header_image_tag( $attr = array() ) {
	$header      = get_custom_header();
	$header->url = get_header_image();

	if ( ! $header->url ) {
		return '';
	}

	$width  = absint( $header->width );
	$height = absint( $header->height );
	$alt    = '';

	// Use alternative text assigned to the image, if available. Otherwise, leave it empty.
	if ( ! empty( $header->attachment_id ) ) {
		$image_alt = get_post_meta( $header->attachment_id, '_gc_attachment_image_alt', true );

		if ( is_string( $image_alt ) ) {
			$alt = $image_alt;
		}
	}

	$attr = gc_parse_args(
		$attr,
		array(
			'src'    => $header->url,
			'width'  => $width,
			'height' => $height,
			'alt'    => $alt,
		)
	);

	// Generate 'srcset' and 'sizes' if not already present.
	if ( empty( $attr['srcset'] ) && ! empty( $header->attachment_id ) ) {
		$image_meta = get_post_meta( $header->attachment_id, '_gc_attachment_metadata', true );
		$size_array = array( $width, $height );

		if ( is_array( $image_meta ) ) {
			$srcset = gc_calculate_image_srcset( $size_array, $header->url, $image_meta, $header->attachment_id );

			if ( ! empty( $attr['sizes'] ) ) {
				$sizes = $attr['sizes'];
			} else {
				$sizes = gc_calculate_image_sizes( $size_array, $header->url, $image_meta, $header->attachment_id );
			}

			if ( $srcset && $sizes ) {
				$attr['srcset'] = $srcset;
				$attr['sizes']  = $sizes;
			}
		}
	}

	/**
	 * Filters the list of header image attributes.
	 *
	 *
	 * @param array  $attr   Array of the attributes for the image tag.
	 * @param object $header The custom header object returned by 'get_custom_header()'.
	 */
	$attr = apply_filters( 'get_header_image_tag_attributes', $attr, $header );

	$attr = array_map( 'esc_attr', $attr );
	$html = '<img';

	foreach ( $attr as $name => $value ) {
		$html .= ' ' . $name . '="' . $value . '"';
	}

	$html .= ' />';

	/**
	 * Filters the markup of header images.
	 *
	 *
	 * @param string $html   The HTML image tag markup being filtered.
	 * @param object $header The custom header object returned by 'get_custom_header()'.
	 * @param array  $attr   Array of the attributes for the image tag.
	 */
	return apply_filters( 'get_header_image_tag', $html, $header, $attr );
}

/**
 * Displays the image markup for a custom header image.
 *
 *
 *
 * @param array $attr Optional. Attributes for the image markup. Default empty.
 */
function the_header_image_tag( $attr = array() ) {
	echo get_header_image_tag( $attr );
}

/**
 * Gets random header image data from registered images in theme.
 *
 *
 *
 * @access private
 *
 * @global array $_gc_default_headers
 *
 * @return object
 */
function _get_random_header_data() {
	global $_gc_default_headers;
	static $_gc_random_header = null;

	if ( empty( $_gc_random_header ) ) {
		$header_image_mod = get_theme_mod( 'header_image', '' );
		$headers          = array();

		if ( 'random-uploaded-image' === $header_image_mod ) {
			$headers = get_uploaded_header_images();
		} elseif ( ! empty( $_gc_default_headers ) ) {
			if ( 'random-default-image' === $header_image_mod ) {
				$headers = $_gc_default_headers;
			} else {
				if ( current_theme_supports( 'custom-header', 'random-default' ) ) {
					$headers = $_gc_default_headers;
				}
			}
		}

		if ( empty( $headers ) ) {
			return new stdClass;
		}

		$_gc_random_header = (object) $headers[ array_rand( $headers ) ];

		$_gc_random_header->url = sprintf(
			$_gc_random_header->url,
			get_template_directory_uri(),
			get_stylesheet_directory_uri()
		);

		$_gc_random_header->thumbnail_url = sprintf(
			$_gc_random_header->thumbnail_url,
			get_template_directory_uri(),
			get_stylesheet_directory_uri()
		);
	}

	return $_gc_random_header;
}

/**
 * Gets random header image URL from registered images in theme.
 *
 *
 *
 * @return string Path to header image.
 */
function get_random_header_image() {
	$random_image = _get_random_header_data();

	if ( empty( $random_image->url ) ) {
		return '';
	}

	return $random_image->url;
}

/**
 * Checks if random header image is in use.
 *
 * Always true if user expressly chooses the option in Appearance > Header.
 * Also true if theme has multiple header images registered, no specific header image
 * is chosen, and theme turns on random headers with add_theme_support().
 *
 *
 *
 * @param string $type The random pool to use. Possible values include 'any',
 *                     'default', 'uploaded'. Default 'any'.
 * @return bool
 */
function is_random_header_image( $type = 'any' ) {
	$header_image_mod = get_theme_mod( 'header_image', get_theme_support( 'custom-header', 'default-image' ) );

	if ( 'any' === $type ) {
		if ( 'random-default-image' === $header_image_mod
			|| 'random-uploaded-image' === $header_image_mod
			|| ( '' !== get_random_header_image() && empty( $header_image_mod ) )
		) {
			return true;
		}
	} else {
		if ( "random-$type-image" === $header_image_mod ) {
			return true;
		} elseif ( 'default' === $type && empty( $header_image_mod ) && '' !== get_random_header_image() ) {
			return true;
		}
	}

	return false;
}

/**
 * Displays header image URL.
 *
 *
 */
function header_image() {
	$image = get_header_image();

	if ( $image ) {
		echo esc_url( $image );
	}
}

/**
 * Gets the header images uploaded for the active theme.
 *
 *
 *
 * @return array
 */
function get_uploaded_header_images() {
	$header_images = array();

	// @todo Caching.
	$headers = get_posts(
		array(
			'post_type'  => 'attachment',
			'meta_key'   => '_gc_attachment_is_custom_header',
			'meta_value' => get_option( 'stylesheet' ),
			'orderby'    => 'none',
			'nopaging'   => true,
		)
	);

	if ( empty( $headers ) ) {
		return array();
	}

	foreach ( (array) $headers as $header ) {
		$url          = esc_url_raw( gc_get_attachment_url( $header->ID ) );
		$header_data  = gc_get_attachment_metadata( $header->ID );
		$header_index = $header->ID;

		$header_images[ $header_index ]                  = array();
		$header_images[ $header_index ]['attachment_id'] = $header->ID;
		$header_images[ $header_index ]['url']           = $url;
		$header_images[ $header_index ]['thumbnail_url'] = $url;
		$header_images[ $header_index ]['alt_text']      = get_post_meta( $header->ID, '_gc_attachment_image_alt', true );

		if ( isset( $header_data['attachment_parent'] ) ) {
			$header_images[ $header_index ]['attachment_parent'] = $header_data['attachment_parent'];
		} else {
			$header_images[ $header_index ]['attachment_parent'] = '';
		}

		if ( isset( $header_data['width'] ) ) {
			$header_images[ $header_index ]['width'] = $header_data['width'];
		}
		if ( isset( $header_data['height'] ) ) {
			$header_images[ $header_index ]['height'] = $header_data['height'];
		}
	}

	return $header_images;
}

/**
 * Gets the header image data.
 *
 *
 *
 * @global array $_gc_default_headers
 *
 * @return object
 */
function get_custom_header() {
	global $_gc_default_headers;

	if ( is_random_header_image() ) {
		$data = _get_random_header_data();
	} else {
		$data = get_theme_mod( 'header_image_data' );
		if ( ! $data && current_theme_supports( 'custom-header', 'default-image' ) ) {
			$directory_args        = array( get_template_directory_uri(), get_stylesheet_directory_uri() );
			$data                  = array();
			$data['url']           = vsprintf( get_theme_support( 'custom-header', 'default-image' ), $directory_args );
			$data['thumbnail_url'] = $data['url'];
			if ( ! empty( $_gc_default_headers ) ) {
				foreach ( (array) $_gc_default_headers as $default_header ) {
					$url = vsprintf( $default_header['url'], $directory_args );
					if ( $data['url'] == $url ) {
						$data                  = $default_header;
						$data['url']           = $url;
						$data['thumbnail_url'] = vsprintf( $data['thumbnail_url'], $directory_args );
						break;
					}
				}
			}
		}
	}

	$default = array(
		'url'           => '',
		'thumbnail_url' => '',
		'width'         => get_theme_support( 'custom-header', 'width' ),
		'height'        => get_theme_support( 'custom-header', 'height' ),
		'video'         => get_theme_support( 'custom-header', 'video' ),
	);
	return (object) gc_parse_args( $data, $default );
}

/**
 * Registers a selection of default headers to be displayed by the custom header admin UI.
 *
 *
 *
 * @global array $_gc_default_headers
 *
 * @param array $headers Array of headers keyed by a string ID. The IDs point to arrays
 *                       containing 'url', 'thumbnail_url', and 'description' keys.
 */
function register_default_headers( $headers ) {
	global $_gc_default_headers;

	$_gc_default_headers = array_merge( (array) $_gc_default_headers, (array) $headers );
}

/**
 * Unregisters default headers.
 *
 * This function must be called after register_default_headers() has already added the
 * header you want to remove.
 *
 * @see register_default_headers()
 *
 *
 * @global array $_gc_default_headers
 *
 * @param string|array $header The header string id (key of array) to remove, or an array thereof.
 * @return bool|void A single header returns true on success, false on failure.
 *                   There is currently no return value for multiple headers.
 */
function unregister_default_headers( $header ) {
	global $_gc_default_headers;

	if ( is_array( $header ) ) {
		array_map( 'unregister_default_headers', $header );
	} elseif ( isset( $_gc_default_headers[ $header ] ) ) {
		unset( $_gc_default_headers[ $header ] );
		return true;
	} else {
		return false;
	}
}

/**
 * Checks whether a header video is set or not.
 *
 *
 *
 * @see get_header_video_url()
 *
 * @return bool Whether a header video is set or not.
 */
function has_header_video() {
	return (bool) get_header_video_url();
}

/**
 * Retrieves header video URL for custom header.
 *
 * Uses a local video if present, or falls back to an external video.
 *
 *
 *
 * @return string|false Header video URL or false if there is no video.
 */
function get_header_video_url() {
	$id = absint( get_theme_mod( 'header_video' ) );

	if ( $id ) {
		// Get the file URL from the attachment ID.
		$url = gc_get_attachment_url( $id );
	} else {
		$url = get_theme_mod( 'external_header_video' );
	}

	/**
	 * Filters the header video URL.
	 *
	 *
	 * @param string $url Header video URL, if available.
	 */
	$url = apply_filters( 'get_header_video_url', $url );

	if ( ! $id && ! $url ) {
		return false;
	}

	return esc_url_raw( set_url_scheme( $url ) );
}

/**
 * Displays header video URL.
 *
 *
 */
function the_header_video_url() {
	$video = get_header_video_url();

	if ( $video ) {
		echo esc_url( $video );
	}
}

/**
 * Retrieves header video settings.
 *
 *
 *
 * @return array
 */
function get_header_video_settings() {
	$header     = get_custom_header();
	$video_url  = get_header_video_url();
	$video_type = gc_check_filetype( $video_url, gc_get_mime_types() );

	$settings = array(
		'mimeType'  => '',
		'posterUrl' => get_header_image(),
		'videoUrl'  => $video_url,
		'width'     => absint( $header->width ),
		'height'    => absint( $header->height ),
		'minWidth'  => 900,
		'minHeight' => 500,
		'l10n'      => array(
			'pause'      => __( '暂停' ),
			'play'       => __( '播放' ),
			'pauseSpeak' => __( '视频已暂停。' ),
			'playSpeak'  => __( '视频正在播放。' ),
		),
	);

	if ( preg_match( '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#', $video_url ) ) {
		$settings['mimeType'] = 'video/x-youtube';
	} elseif ( ! empty( $video_type['type'] ) ) {
		$settings['mimeType'] = $video_type['type'];
	}

	/**
	 * Filters header video settings.
	 *
	 *
	 * @param array $settings An array of header video settings.
	 */
	return apply_filters( 'header_video_settings', $settings );
}

/**
 * Checks whether a custom header is set or not.
 *
 *
 *
 * @return bool True if a custom header is set. False if not.
 */
function has_custom_header() {
	if ( has_header_image() || ( has_header_video() && is_header_video_active() ) ) {
		return true;
	}

	return false;
}

/**
 * Checks whether the custom header video is eligible to show on the current page.
 *
 *
 *
 * @return bool True if the custom header video should be shown. False if not.
 */
function is_header_video_active() {
	if ( ! get_theme_support( 'custom-header', 'video' ) ) {
		return false;
	}

	$video_active_cb = get_theme_support( 'custom-header', 'video-active-callback' );

	if ( empty( $video_active_cb ) || ! is_callable( $video_active_cb ) ) {
		$show_video = true;
	} else {
		$show_video = call_user_func( $video_active_cb );
	}

	/**
	 * Filters whether the custom header video is eligible to show on the current page.
	 *
	 *
	 * @param bool $show_video Whether the custom header video should be shown. Returns the value
	 *                         of the theme setting for the `custom-header`'s `video-active-callback`.
	 *                         If no callback is set, the default value is that of `is_front_page()`.
	 */
	return apply_filters( 'is_header_video_active', $show_video );
}

/**
 * Retrieves the markup for a custom header.
 *
 * The container div will always be returned in the Customizer preview.
 *
 *
 *
 * @return string The markup for a custom header on success.
 */
function get_custom_header_markup() {
	if ( ! has_custom_header() && ! is_customize_preview() ) {
		return '';
	}

	return sprintf(
		'<div id="gc-custom-header" class="gc-custom-header">%s</div>',
		get_header_image_tag()
	);
}

/**
 * Prints the markup for a custom header.
 *
 * A container div will always be printed in the Customizer preview.
 *
 *
 */
function the_custom_header_markup() {
	$custom_header = get_custom_header_markup();
	if ( empty( $custom_header ) ) {
		return;
	}

	echo $custom_header;

	if ( is_header_video_active() && ( has_header_video() || is_customize_preview() ) ) {
		gc_enqueue_script( 'gc-custom-header' );
		gc_localize_script( 'gc-custom-header', '_gcCustomHeaderSettings', get_header_video_settings() );
	}
}

/**
 * Retrieves background image for custom background.
 *
 *
 *
 * @return string
 */
function get_background_image() {
	return get_theme_mod( 'background_image', get_theme_support( 'custom-background', 'default-image' ) );
}

/**
 * Displays background image path.
 *
 *
 */
function background_image() {
	echo get_background_image();
}

/**
 * Retrieves value for custom background color.
 *
 *
 *
 * @return string
 */
function get_background_color() {
	return get_theme_mod( 'background_color', get_theme_support( 'custom-background', 'default-color' ) );
}

/**
 * Displays background color value.
 *
 *
 */
function background_color() {
	echo get_background_color();
}

/**
 * Default custom background callback.
 *
 *
 */
function _custom_background_cb() {
	// $background is the saved custom image, or the default image.
	$background = set_url_scheme( get_background_image() );

	// $color is the saved custom color.
	// A default has to be specified in style.css. It will not be printed here.
	$color = get_background_color();

	if ( get_theme_support( 'custom-background', 'default-color' ) === $color ) {
		$color = false;
	}

	$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

	if ( ! $background && ! $color ) {
		if ( is_customize_preview() ) {
			printf( '<style%s id="custom-background-css"></style>', $type_attr );
		}
		return;
	}

	$style = $color ? "background-color: #$color;" : '';

	if ( $background ) {
		$image = ' background-image: url("' . esc_url_raw( $background ) . '");';

		// Background Position.
		$position_x = get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );
		$position_y = get_theme_mod( 'background_position_y', get_theme_support( 'custom-background', 'default-position-y' ) );

		if ( ! in_array( $position_x, array( 'left', 'center', 'right' ), true ) ) {
			$position_x = 'left';
		}

		if ( ! in_array( $position_y, array( 'top', 'center', 'bottom' ), true ) ) {
			$position_y = 'top';
		}

		$position = " background-position: $position_x $position_y;";

		// Background Size.
		$size = get_theme_mod( 'background_size', get_theme_support( 'custom-background', 'default-size' ) );

		if ( ! in_array( $size, array( 'auto', 'contain', 'cover' ), true ) ) {
			$size = 'auto';
		}

		$size = " background-size: $size;";

		// Background Repeat.
		$repeat = get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );

		if ( ! in_array( $repeat, array( 'repeat-x', 'repeat-y', 'repeat', 'no-repeat' ), true ) ) {
			$repeat = 'repeat';
		}

		$repeat = " background-repeat: $repeat;";

		// Background Scroll.
		$attachment = get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );

		if ( 'fixed' !== $attachment ) {
			$attachment = 'scroll';
		}

		$attachment = " background-attachment: $attachment;";

		$style .= $image . $position . $size . $repeat . $attachment;
	}
	?>
<style<?php echo $type_attr; ?> id="custom-background-css">
body.custom-background { <?php echo trim( $style ); ?> }
</style>
	<?php
}

/**
 * Renders the Custom CSS style element.
 *
 *
 */
function gc_custom_css_cb() {
	$styles = gc_get_custom_css();
	if ( $styles || is_customize_preview() ) :
		$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';
		?>
		<style<?php echo $type_attr; ?> id="gc-custom-css">
			<?php
			// Note that esc_html() cannot be used because `div &gt; span` is not interpreted properly.
			echo strip_tags( $styles );
			?>
		</style>
		<?php
	endif;
}

/**
 * Fetches the `custom_css` post for a given theme.
 *
 *
 *
 * @param string $stylesheet Optional. A theme object stylesheet name. Defaults to the active theme.
 * @return GC_Post|null The custom_css post or null if none exists.
 */
function gc_get_custom_css_post( $stylesheet = '' ) {
	if ( empty( $stylesheet ) ) {
		$stylesheet = get_stylesheet();
	}

	$custom_css_query_vars = array(
		'post_type'              => 'custom_css',
		'post_status'            => get_post_stati(),
		'name'                   => sanitize_title( $stylesheet ),
		'posts_per_page'         => 1,
		'no_found_rows'          => true,
		'cache_results'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'lazy_load_term_meta'    => false,
	);

	$post = null;
	if ( get_stylesheet() === $stylesheet ) {
		$post_id = get_theme_mod( 'custom_css_post_id' );

		if ( $post_id > 0 && get_post( $post_id ) ) {
			$post = get_post( $post_id );
		}

		// `-1` indicates no post exists; no query necessary.
		if ( ! $post && -1 !== $post_id ) {
			$query = new GC_Query( $custom_css_query_vars );
			$post  = $query->post;
			/*
			 * Cache the lookup. See gc_update_custom_css_post().
			 * @todo This should get cleared if a custom_css post is added/removed.
			 */
			set_theme_mod( 'custom_css_post_id', $post ? $post->ID : -1 );
		}
	} else {
		$query = new GC_Query( $custom_css_query_vars );
		$post  = $query->post;
	}

	return $post;
}

/**
 * Fetches the saved Custom CSS content for rendering.
 *
 *
 *
 * @param string $stylesheet Optional. A theme object stylesheet name. Defaults to the active theme.
 * @return string The Custom CSS Post content.
 */
function gc_get_custom_css( $stylesheet = '' ) {
	$css = '';

	if ( empty( $stylesheet ) ) {
		$stylesheet = get_stylesheet();
	}

	$post = gc_get_custom_css_post( $stylesheet );
	if ( $post ) {
		$css = $post->post_content;
	}

	/**
	 * Filters the Custom CSS Output into the <head>.
	 *
	 *
	 * @param string $css        CSS pulled in from the Custom CSS post type.
	 * @param string $stylesheet The theme stylesheet name.
	 */
	$css = apply_filters( 'gc_get_custom_css', $css, $stylesheet );

	return $css;
}

/**
 * Updates the `custom_css` post for a given theme.
 *
 * Inserts a `custom_css` post when one doesn't yet exist.
 *
 *
 *
 * @param string $css CSS, stored in `post_content`.
 * @param array  $args {
 *     Args.
 *
 *     @type string $preprocessed Optional. Pre-processed CSS, stored in `post_content_filtered`.
 *                                Normally empty string.
 *     @type string $stylesheet   Optional. Stylesheet (child theme) to update.
 *                                Defaults to active theme/stylesheet.
 * }
 * @return GC_Post|GC_Error Post on success, error on failure.
 */
function gc_update_custom_css_post( $css, $args = array() ) {
	$args = gc_parse_args(
		$args,
		array(
			'preprocessed' => '',
			'stylesheet'   => get_stylesheet(),
		)
	);

	$data = array(
		'css'          => $css,
		'preprocessed' => $args['preprocessed'],
	);

	/**
	 * Filters the `css` (`post_content`) and `preprocessed` (`post_content_filtered`) args
	 * for a `custom_css` post being updated.
	 *
	 * This filter can be used by plugin that offer CSS pre-processors, to store the original
	 * pre-processed CSS in `post_content_filtered` and then store processed CSS in `post_content`.
	 * When used in this way, the `post_content_filtered` should be supplied as the setting value
	 * instead of `post_content` via a the `customize_value_custom_css` filter, for example:
	 *
	 * <code>
	 * add_filter( 'customize_value_custom_css', function( $value, $setting ) {
	 *     $post = gc_get_custom_css_post( $setting->stylesheet );
	 *     if ( $post && ! empty( $post->post_content_filtered ) ) {
	 *         $css = $post->post_content_filtered;
	 *     }
	 *     return $css;
	 * }, 10, 2 );
	 * </code>
	 *
	 * @param array $data {
	 *     Custom CSS data.
	 *
	 *     @type string $css          CSS stored in `post_content`.
	 *     @type string $preprocessed Pre-processed CSS stored in `post_content_filtered`.
	 *                                Normally empty string.
	 * }
	 * @param array $args {
	 *     The args passed into `gc_update_custom_css_post()` merged with defaults.
	 *
	 *     @type string $css          The original CSS passed in to be updated.
	 *     @type string $preprocessed The original preprocessed CSS passed in to be updated.
	 *     @type string $stylesheet   The stylesheet (theme) being updated.
	 * }
	 */
	$data = apply_filters( 'update_custom_css_data', $data, array_merge( $args, compact( 'css' ) ) );

	$post_data = array(
		'post_title'            => $args['stylesheet'],
		'post_name'             => sanitize_title( $args['stylesheet'] ),
		'post_type'             => 'custom_css',
		'post_status'           => 'publish',
		'post_content'          => $data['css'],
		'post_content_filtered' => $data['preprocessed'],
	);

	// Update post if it already exists, otherwise create a new one.
	$post = gc_get_custom_css_post( $args['stylesheet'] );
	if ( $post ) {
		$post_data['ID'] = $post->ID;
		$r               = gc_update_post( gc_slash( $post_data ), true );
	} else {
		$r = gc_insert_post( gc_slash( $post_data ), true );

		if ( ! is_gc_error( $r ) ) {
			if ( get_stylesheet() === $args['stylesheet'] ) {
				set_theme_mod( 'custom_css_post_id', $r );
			}

			// Trigger creation of a revision. This should be removed once #30854 is resolved.
			if ( 0 === count( gc_get_post_revisions( $r ) ) ) {
				gc_save_post_revision( $r );
			}
		}
	}

	if ( is_gc_error( $r ) ) {
		return $r;
	}
	return get_post( $r );
}

/**
 * Adds callback for custom TinyMCE editor stylesheets.
 *
 * The parameter $stylesheet is the name of the stylesheet, relative to
 * the theme root. It also accepts an array of stylesheets.
 * It is optional and defaults to 'editor-style.css'.
 *
 * This function automatically adds another stylesheet with -rtl prefix, e.g. editor-style-rtl.css.
 * If that file doesn't exist, it is removed before adding the stylesheet(s) to TinyMCE.
 * If an array of stylesheets is passed to add_editor_style(),
 * RTL is only added for the first stylesheet.
 *
 * Since version 3.4 the TinyMCE body has .rtl CSS class.
 * It is a better option to use that class and add any RTL styles to the main stylesheet.
 *
 *
 *
 * @global array $editor_styles
 *
 * @param array|string $stylesheet Optional. Stylesheet name or array thereof, relative to theme root.
 *                                 Defaults to 'editor-style.css'
 */
function add_editor_style( $stylesheet = 'editor-style.css' ) {
	global $editor_styles;

	add_theme_support( 'editor-style' );

	$editor_styles = (array) $editor_styles;
	$stylesheet    = (array) $stylesheet;

	if ( is_rtl() ) {
		$rtl_stylesheet = str_replace( '.css', '-rtl.css', $stylesheet[0] );
		$stylesheet[]   = $rtl_stylesheet;
	}

	$editor_styles = array_merge( $editor_styles, $stylesheet );
}

/**
 * Removes all visual editor stylesheets.
 *
 *
 *
 * @global array $editor_styles
 *
 * @return bool True on success, false if there were no stylesheets to remove.
 */
function remove_editor_styles() {
	if ( ! current_theme_supports( 'editor-style' ) ) {
		return false;
	}
	_remove_theme_support( 'editor-style' );
	if ( is_admin() ) {
		$GLOBALS['editor_styles'] = array();
	}
	return true;
}

/**
 * Retrieves any registered editor stylesheet URLs.
 *
 *
 *
 * @global array $editor_styles Registered editor stylesheets
 *
 * @return string[] If registered, a list of editor stylesheet URLs.
 */
function get_editor_stylesheets() {
	$stylesheets = array();
	// Load editor_style.css if the active theme supports it.
	if ( ! empty( $GLOBALS['editor_styles'] ) && is_array( $GLOBALS['editor_styles'] ) ) {
		$editor_styles = $GLOBALS['editor_styles'];

		$editor_styles = array_unique( array_filter( $editor_styles ) );
		$style_uri     = get_stylesheet_directory_uri();
		$style_dir     = get_stylesheet_directory();

		// Support externally referenced styles (like, say, fonts).
		foreach ( $editor_styles as $key => $file ) {
			if ( preg_match( '~^(https?:)?//~', $file ) ) {
				$stylesheets[] = esc_url_raw( $file );
				unset( $editor_styles[ $key ] );
			}
		}

		// Look in a parent theme first, that way child theme CSS overrides.
		if ( is_child_theme() ) {
			$template_uri = get_template_directory_uri();
			$template_dir = get_template_directory();

			foreach ( $editor_styles as $key => $file ) {
				if ( $file && file_exists( "$template_dir/$file" ) ) {
					$stylesheets[] = "$template_uri/$file";
				}
			}
		}

		foreach ( $editor_styles as $file ) {
			if ( $file && file_exists( "$style_dir/$file" ) ) {
				$stylesheets[] = "$style_uri/$file";
			}
		}
	}

	/**
	 * Filters the array of URLs of stylesheets applied to the editor.
	 *
	 *
	 * @param string[] $stylesheets Array of URLs of stylesheets to be applied to the editor.
	 */
	return apply_filters( 'editor_stylesheets', $stylesheets );
}

/**
 * Expands a theme's starter content configuration using core-provided data.
 *
 *
 *
 * @return array Array of starter content.
 */
function get_theme_starter_content() {
	$theme_support = get_theme_support( 'starter-content' );
	if ( is_array( $theme_support ) && ! empty( $theme_support[0] ) && is_array( $theme_support[0] ) ) {
		$config = $theme_support[0];
	} else {
		$config = array();
	}

	$core_content = array(
		'widgets'   => array(
			'text_business_info' => array(
				'text',
				array(
					'title'  => _x( '联系我们', 'Theme starter content' ),
					'text'   => implode(
						'',
						array(
							'<strong>' . _x( '地址', 'Theme starter content' ) . "</strong>\n",
							_x( '北清路123号', 'Theme starter content' ) . "\n",
							_x( '北京市海淀区，邮编：100001', 'Theme starter content' ) . "\n\n",
							'<strong>' . _x( '营业时间', 'Theme starter content' ) . "</strong>\n",
							_x( '星期一&mdash;五：9:00&ndash;17:00', 'Theme starter content' ) . "\n",
							_x( '星期六&mdash;日：11:00&ndash;15:00', 'Theme starter content' ),
						)
					),
					'filter' => true,
					'visual' => true,
				),
			),
			'text_about'         => array(
				'text',
				array(
					'title'  => _x( '关于本站', 'Theme starter content' ),
					'text'   => _x( '这里也许是个介绍您自己的好地方，也能介绍您的站点或放进一些工作人员名单。', 'Theme starter content' ),
					'filter' => true,
					'visual' => true,
				),
			),
			'archives'           => array(
				'archives',
				array(
					'title' => _x( '归档', 'Theme starter content' ),
				),
			),
			'calendar'           => array(
				'calendar',
				array(
					'title' => _x( '日历', 'Theme starter content' ),
				),
			),
			'categories'         => array(
				'categories',
				array(
					'title' => _x( '分类', 'Theme starter content' ),
				),
			),
			'meta'               => array(
				'meta',
				array(
					'title' => _x( '其他', 'Theme starter content' ),
				),
			),
			'recent-comments'    => array(
				'recent-comments',
				array(
					'title' => _x( '近期评论', 'Theme starter content' ),
				),
			),
			'recent-posts'       => array(
				'recent-posts',
				array(
					'title' => _x( '近期文章', 'Theme starter content' ),
				),
			),
			'search'             => array(
				'search',
				array(
					'title' => _x( '搜索', 'Theme starter content' ),
				),
			),
		),
		'nav_menus' => array(
			'link_home'       => array(
				'type'  => 'custom',
				'title' => _x( '首页', 'Theme starter content' ),
				'url'   => home_url( '/' ),
			),
			'page_home'       => array( // Deprecated in favor of 'link_home'.
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{home}}',
			),
			'page_about'      => array(
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{about}}',
			),
			'page_blog'       => array(
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{blog}}',
			),
			'page_news'       => array(
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{news}}',
			),
			'page_contact'    => array(
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{contact}}',
			),

			'link_email'      => array(
				'title' => _x( '电子邮箱', 'Theme starter content' ),
				'url'   => 'mailto:gechiui@example.com',
			),
			'link_github'     => array(
				'title' => _x( 'GitHub', 'Theme starter content' ),
				'url'   => 'https://github.com/gechiui/',
			),
		),
		'posts'     => array(
			'home'             => array(
				'post_type'    => 'page',
				'post_title'   => _x( '首页', 'Theme starter content' ),
				'post_content' => sprintf(
					"<!-- gc:paragraph -->\n<p>%s</p>\n<!-- /gc:paragraph -->",
					_x( '欢迎来到您的站点！这是您的主页，也就是大多数访客第一次造访时看到的页面。', 'Theme starter content' )
				),
			),
			'about'            => array(
				'post_type'    => 'page',
				'post_title'   => _x( '关于', 'Theme starter content' ),
				'post_content' => sprintf(
					"<!-- gc:paragraph -->\n<p>%s</p>\n<!-- /gc:paragraph -->",
					_x( '您也许是位创作者，想要在这里介绍自己和自己的作品；或者您是一位商务人士，想在这里谈谈您的业务。', 'Theme starter content' )
				),
			),
			'contact'          => array(
				'post_type'    => 'page',
				'post_title'   => _x( '联系', 'Theme starter content' ),
				'post_content' => sprintf(
					"<!-- gc:paragraph -->\n<p>%s</p>\n<!-- /gc:paragraph -->",
					_x( '这个页面包含了一些基本的联系资料，像是地址和联系电话。您也可以尝试使用插件增加联系表单。', 'Theme starter content' )
				),
			),
			'blog'             => array(
				'post_type'  => 'page',
				'post_title' => _x( '博客', 'Theme starter content' ),
			),
			'news'             => array(
				'post_type'  => 'page',
				'post_title' => _x( '新闻', 'Theme starter content' ),
			),

			'homepage-section' => array(
				'post_type'    => 'page',
				'post_title'   => _x( '主页章节', 'Theme starter content' ),
				'post_content' => sprintf(
					"<!-- gc:paragraph -->\n<p>%s</p>\n<!-- /gc:paragraph -->",
					_x( '这是首页章节的范例。首页章节可以是除了首页本身以外的任何其他页面，包括显示最新博客文章的页面。', 'Theme starter content' )
				),
			),
		),
	);

	$content = array();

	foreach ( $config as $type => $args ) {
		switch ( $type ) {
			// Use options and theme_mods as-is.
			case 'options':
			case 'theme_mods':
				$content[ $type ] = $config[ $type ];
				break;

			// Widgets are grouped into sidebars.
			case 'widgets':
				foreach ( $config[ $type ] as $sidebar_id => $widgets ) {
					foreach ( $widgets as $id => $widget ) {
						if ( is_array( $widget ) ) {

							// Item extends core content.
							if ( ! empty( $core_content[ $type ][ $id ] ) ) {
								$widget = array(
									$core_content[ $type ][ $id ][0],
									array_merge( $core_content[ $type ][ $id ][1], $widget ),
								);
							}

							$content[ $type ][ $sidebar_id ][] = $widget;
						} elseif ( is_string( $widget )
							&& ! empty( $core_content[ $type ] )
							&& ! empty( $core_content[ $type ][ $widget ] )
						) {
							$content[ $type ][ $sidebar_id ][] = $core_content[ $type ][ $widget ];
						}
					}
				}
				break;

			// And nav menu items are grouped into nav menus.
			case 'nav_menus':
				foreach ( $config[ $type ] as $nav_menu_location => $nav_menu ) {

					// Ensure nav menus get a name.
					if ( empty( $nav_menu['name'] ) ) {
						$nav_menu['name'] = $nav_menu_location;
					}

					$content[ $type ][ $nav_menu_location ]['name'] = $nav_menu['name'];

					foreach ( $nav_menu['items'] as $id => $nav_menu_item ) {
						if ( is_array( $nav_menu_item ) ) {

							// Item extends core content.
							if ( ! empty( $core_content[ $type ][ $id ] ) ) {
								$nav_menu_item = array_merge( $core_content[ $type ][ $id ], $nav_menu_item );
							}

							$content[ $type ][ $nav_menu_location ]['items'][] = $nav_menu_item;
						} elseif ( is_string( $nav_menu_item )
							&& ! empty( $core_content[ $type ] )
							&& ! empty( $core_content[ $type ][ $nav_menu_item ] )
						) {
							$content[ $type ][ $nav_menu_location ]['items'][] = $core_content[ $type ][ $nav_menu_item ];
						}
					}
				}
				break;

			// Attachments are posts but have special treatment.
			case 'attachments':
				foreach ( $config[ $type ] as $id => $item ) {
					if ( ! empty( $item['file'] ) ) {
						$content[ $type ][ $id ] = $item;
					}
				}
				break;

			// All that's left now are posts (besides attachments).
			// Not a default case for the sake of clarity and future work.
			case 'posts':
				foreach ( $config[ $type ] as $id => $item ) {
					if ( is_array( $item ) ) {

						// Item extends core content.
						if ( ! empty( $core_content[ $type ][ $id ] ) ) {
							$item = array_merge( $core_content[ $type ][ $id ], $item );
						}

						// Enforce a subset of fields.
						$content[ $type ][ $id ] = gc_array_slice_assoc(
							$item,
							array(
								'post_type',
								'post_title',
								'post_excerpt',
								'post_name',
								'post_content',
								'menu_order',
								'comment_status',
								'thumbnail',
								'template',
							)
						);
					} elseif ( is_string( $item ) && ! empty( $core_content[ $type ][ $item ] ) ) {
						$content[ $type ][ $item ] = $core_content[ $type ][ $item ];
					}
				}
				break;
		}
	}

	/**
	 * Filters the expanded array of starter content.
	 *
	 *
	 * @param array $content Array of starter content.
	 * @param array $config  Array of theme-specific starter content configuration.
	 */
	return apply_filters( 'get_theme_starter_content', $content, $config );
}

/**
 * Registers theme support for a given feature.
 *
 * Must be called in the theme's functions.php file to work.
 * If attached to a hook, it must be {@see 'after_setup_theme'}.
 * The {@see 'init'} hook may be too late for some features.
 *
 * Example usage:
 *
 *     add_theme_support( 'title-tag' );
 *     add_theme_support( 'custom-logo', array(
 *         'height' => 480,
 *         'width'  => 720,
 *     ) );
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *              `disable-custom-font-sizes`, `editor-color-palette`, `editor-font-sizes`,
 *              `editor-styles`, and `gc-block-styles` features were added.
 *
 *
 *              by adding it to the function signature.
 *
 *
 *
 *
 *
 * @global array $_gc_theme_features
 *
 * @param string $feature The feature being added. Likely core values include:
 *                          - 'admin-bar'
 *                          - 'align-wide'
 *                          - 'automatic-feed-links'
 *                          - 'core-block-patterns'
 *                          - 'custom-background'
 *                          - 'custom-header'
 *                          - 'custom-line-height'
 *                          - 'custom-logo'
 *                          - 'customize-selective-refresh-widgets'
 *                          - 'custom-spacing'
 *                          - 'custom-units'
 *                          - 'dark-editor-style'
 *                          - 'disable-custom-colors'
 *                          - 'disable-custom-font-sizes'
 *                          - 'editor-color-palette'
 *                          - 'editor-gradient-presets'
 *                          - 'editor-font-sizes'
 *                          - 'editor-styles'
 *                          - 'featured-content'
 *                          - 'html5'
 *                          - 'menus'
 *                          - 'post-formats'
 *                          - 'post-thumbnails'
 *                          - 'responsive-embeds'
 *                          - 'starter-content'
 *                          - 'title-tag'
 *                          - 'gc-block-styles'
 *                          - 'widgets'
 *                          - 'widgets-block-editor'
 * @param mixed  ...$args Optional extra arguments to pass along with certain features.
 * @return void|false Void on success, false on failure.
 */
function add_theme_support( $feature, ...$args ) {
	global $_gc_theme_features;

	if ( ! $args ) {
		$args = true;
	}

	switch ( $feature ) {
		case 'post-thumbnails':
			// All post types are already supported.
			if ( true === get_theme_support( 'post-thumbnails' ) ) {
				return;
			}

			/*
			 * Merge post types with any that already declared their support
			 * for post thumbnails.
			 */
			if ( isset( $args[0] ) && is_array( $args[0] ) && isset( $_gc_theme_features['post-thumbnails'] ) ) {
				$args[0] = array_unique( array_merge( $_gc_theme_features['post-thumbnails'][0], $args[0] ) );
			}

			break;

		case 'post-formats':
			if ( isset( $args[0] ) && is_array( $args[0] ) ) {
				$post_formats = get_post_format_slugs();
				unset( $post_formats['standard'] );

				$args[0] = array_intersect( $args[0], array_keys( $post_formats ) );
			} else {
				_doing_it_wrong(
					"add_theme_support( 'post-formats' )",
					__( '您需要传入一个文章形式数组。' ),
					'5.6.0'
				);
				return false;
			}
			break;

		case 'html5':
			// You can't just pass 'html5', you need to pass an array of types.
			if ( empty( $args[0] ) ) {
				// Build an array of types for back-compat.
				$args = array( 0 => array( 'comment-list', 'comment-form', 'search-form' ) );
			} elseif ( ! isset( $args[0] ) || ! is_array( $args[0] ) ) {
				_doing_it_wrong(
					"add_theme_support( 'html5' )",
					__( '您需要传入类型数组。' ),
					'3.6.1'
				);
				return false;
			}

			// Calling 'html5' again merges, rather than overwrites.
			if ( isset( $_gc_theme_features['html5'] ) ) {
				$args[0] = array_merge( $_gc_theme_features['html5'][0], $args[0] );
			}
			break;

		case 'custom-logo':
			if ( true === $args ) {
				$args = array( 0 => array() );
			}
			$defaults = array(
				'width'                => null,
				'height'               => null,
				'flex-width'           => false,
				'flex-height'          => false,
				'header-text'          => '',
				'unlink-homepage-logo' => false,
			);
			$args[0]  = gc_parse_args( array_intersect_key( $args[0], $defaults ), $defaults );

			// Allow full flexibility if no size is specified.
			if ( is_null( $args[0]['width'] ) && is_null( $args[0]['height'] ) ) {
				$args[0]['flex-width']  = true;
				$args[0]['flex-height'] = true;
			}
			break;

		case 'custom-header-uploads':
			return add_theme_support( 'custom-header', array( 'uploads' => true ) );

		case 'custom-header':
			if ( true === $args ) {
				$args = array( 0 => array() );
			}

			$defaults = array(
				'default-image'          => '',
				'random-default'         => false,
				'width'                  => 0,
				'height'                 => 0,
				'flex-height'            => false,
				'flex-width'             => false,
				'default-text-color'     => '',
				'header-text'            => true,
				'uploads'                => true,
				'gc-head-callback'       => '',
				'admin-head-callback'    => '',
				'admin-preview-callback' => '',
				'video'                  => false,
				'video-active-callback'  => 'is_front_page',
			);

			$jit = isset( $args[0]['__jit'] );
			unset( $args[0]['__jit'] );

			// Merge in data from previous add_theme_support() calls.
			// The first value registered wins. (A child theme is set up first.)
			if ( isset( $_gc_theme_features['custom-header'] ) ) {
				$args[0] = gc_parse_args( $_gc_theme_features['custom-header'][0], $args[0] );
			}

			// Load in the defaults at the end, as we need to insure first one wins.
			// This will cause all constants to be defined, as each arg will then be set to the default.
			if ( $jit ) {
				$args[0] = gc_parse_args( $args[0], $defaults );
			}

			/*
			 * If a constant was defined, use that value. Otherwise, define the constant to ensure
			 * the constant is always accurate (and is not defined later,  overriding our value).
			 * As stated above, the first value wins.
			 * Once we get to gc_loaded (just-in-time), define any constants we haven't already.
			 * Constants are lame. Don't reference them. This is just for backward compatibility.
			 */

			if ( defined( 'NO_HEADER_TEXT' ) ) {
				$args[0]['header-text'] = ! NO_HEADER_TEXT;
			} elseif ( isset( $args[0]['header-text'] ) ) {
				define( 'NO_HEADER_TEXT', empty( $args[0]['header-text'] ) );
			}

			if ( defined( 'HEADER_IMAGE_WIDTH' ) ) {
				$args[0]['width'] = (int) HEADER_IMAGE_WIDTH;
			} elseif ( isset( $args[0]['width'] ) ) {
				define( 'HEADER_IMAGE_WIDTH', (int) $args[0]['width'] );
			}

			if ( defined( 'HEADER_IMAGE_HEIGHT' ) ) {
				$args[0]['height'] = (int) HEADER_IMAGE_HEIGHT;
			} elseif ( isset( $args[0]['height'] ) ) {
				define( 'HEADER_IMAGE_HEIGHT', (int) $args[0]['height'] );
			}

			if ( defined( 'HEADER_TEXTCOLOR' ) ) {
				$args[0]['default-text-color'] = HEADER_TEXTCOLOR;
			} elseif ( isset( $args[0]['default-text-color'] ) ) {
				define( 'HEADER_TEXTCOLOR', $args[0]['default-text-color'] );
			}

			if ( defined( 'HEADER_IMAGE' ) ) {
				$args[0]['default-image'] = HEADER_IMAGE;
			} elseif ( isset( $args[0]['default-image'] ) ) {
				define( 'HEADER_IMAGE', $args[0]['default-image'] );
			}

			if ( $jit && ! empty( $args[0]['default-image'] ) ) {
				$args[0]['random-default'] = false;
			}

			// If headers are supported, and we still don't have a defined width or height,
			// we have implicit flex sizes.
			if ( $jit ) {
				if ( empty( $args[0]['width'] ) && empty( $args[0]['flex-width'] ) ) {
					$args[0]['flex-width'] = true;
				}
				if ( empty( $args[0]['height'] ) && empty( $args[0]['flex-height'] ) ) {
					$args[0]['flex-height'] = true;
				}
			}

			break;

		case 'custom-background':
			if ( true === $args ) {
				$args = array( 0 => array() );
			}

			$defaults = array(
				'default-image'          => '',
				'default-preset'         => 'default',
				'default-position-x'     => 'left',
				'default-position-y'     => 'top',
				'default-size'           => 'auto',
				'default-repeat'         => 'repeat',
				'default-attachment'     => 'scroll',
				'default-color'          => '',
				'gc-head-callback'       => '_custom_background_cb',
				'admin-head-callback'    => '',
				'admin-preview-callback' => '',
			);

			$jit = isset( $args[0]['__jit'] );
			unset( $args[0]['__jit'] );

			// Merge in data from previous add_theme_support() calls. The first value registered wins.
			if ( isset( $_gc_theme_features['custom-background'] ) ) {
				$args[0] = gc_parse_args( $_gc_theme_features['custom-background'][0], $args[0] );
			}

			if ( $jit ) {
				$args[0] = gc_parse_args( $args[0], $defaults );
			}

			if ( defined( 'BACKGROUND_COLOR' ) ) {
				$args[0]['default-color'] = BACKGROUND_COLOR;
			} elseif ( isset( $args[0]['default-color'] ) || $jit ) {
				define( 'BACKGROUND_COLOR', $args[0]['default-color'] );
			}

			if ( defined( 'BACKGROUND_IMAGE' ) ) {
				$args[0]['default-image'] = BACKGROUND_IMAGE;
			} elseif ( isset( $args[0]['default-image'] ) || $jit ) {
				define( 'BACKGROUND_IMAGE', $args[0]['default-image'] );
			}

			break;

		// Ensure that 'title-tag' is accessible in the admin.
		case 'title-tag':
			// Can be called in functions.php but must happen before gc_loaded, i.e. not in header.php.
			if ( did_action( 'gc_loaded' ) ) {
				_doing_it_wrong(
					"add_theme_support( 'title-tag' )",
					sprintf(
						/* translators: 1: title-tag, 2: gc_loaded */
						__( '对%1$s主题的支持应在%2$s钩子函数之前注册。' ),
						'<code>title-tag</code>',
						'<code>gc_loaded</code>'
					),
					'4.1.0'
				);

				return false;
			}
	}

	$_gc_theme_features[ $feature ] = $args;
}

/**
 * Registers the internal custom header and background routines.
 *
 *
 * @access private
 *
 * @global Custom_Image_Header $custom_image_header
 * @global Custom_Background   $custom_background
 */
function _custom_header_background_just_in_time() {
	global $custom_image_header, $custom_background;

	if ( current_theme_supports( 'custom-header' ) ) {
		// In case any constants were defined after an add_custom_image_header() call, re-run.
		add_theme_support( 'custom-header', array( '__jit' => true ) );

		$args = get_theme_support( 'custom-header' );
		if ( $args[0]['gc-head-callback'] ) {
			add_action( 'gc_head', $args[0]['gc-head-callback'] );
		}

		if ( is_admin() ) {
			require_once ABSPATH . 'gc-admin/includes/class-custom-image-header.php';
			$custom_image_header = new Custom_Image_Header( $args[0]['admin-head-callback'], $args[0]['admin-preview-callback'] );
		}
	}

	if ( current_theme_supports( 'custom-background' ) ) {
		// In case any constants were defined after an add_custom_background() call, re-run.
		add_theme_support( 'custom-background', array( '__jit' => true ) );

		$args = get_theme_support( 'custom-background' );
		add_action( 'gc_head', $args[0]['gc-head-callback'] );

		if ( is_admin() ) {
			require_once ABSPATH . 'gc-admin/includes/class-custom-background.php';
			$custom_background = new Custom_Background( $args[0]['admin-head-callback'], $args[0]['admin-preview-callback'] );
		}
	}
}

/**
 * Adds CSS to hide header text for custom logo, based on Customizer setting.
 *
 *
 * @access private
 */
function _custom_logo_header_styles() {
	if ( ! current_theme_supports( 'custom-header', 'header-text' )
		&& get_theme_support( 'custom-logo', 'header-text' )
		&& ! get_theme_mod( 'header_text', true )
	) {
		$classes = (array) get_theme_support( 'custom-logo', 'header-text' );
		$classes = array_map( 'sanitize_html_class', $classes );
		$classes = '.' . implode( ', .', $classes );

		$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';
		?>
		<!-- Custom Logo: hide header text -->
		<style id="custom-logo-css"<?php echo $type_attr; ?>>
			<?php echo $classes; ?> {
				position: absolute;
				clip: rect(1px, 1px, 1px, 1px);
			}
		</style>
		<?php
	}
}

/**
 * Gets the theme support arguments passed when registering that support.
 *
 * Example usage:
 *
 *     get_theme_support( 'custom-logo' );
 *     get_theme_support( 'custom-header', 'width' );
 *
 *
 *
 *              by adding it to the function signature.
 *
 * @global array $_gc_theme_features
 *
 * @param string $feature The feature to check. See add_theme_support() for the list
 *                        of possible values.
 * @param mixed  ...$args Optional extra arguments to be checked against certain features.
 * @return mixed The array of extra arguments or the value for the registered feature.
 */
function get_theme_support( $feature, ...$args ) {
	global $_gc_theme_features;

	if ( ! isset( $_gc_theme_features[ $feature ] ) ) {
		return false;
	}

	if ( ! $args ) {
		return $_gc_theme_features[ $feature ];
	}

	switch ( $feature ) {
		case 'custom-logo':
		case 'custom-header':
		case 'custom-background':
			if ( isset( $_gc_theme_features[ $feature ][0][ $args[0] ] ) ) {
				return $_gc_theme_features[ $feature ][0][ $args[0] ];
			}
			return false;

		default:
			return $_gc_theme_features[ $feature ];
	}
}

/**
 * Allows a theme to de-register its support of a certain feature
 *
 * Should be called in the theme's functions.php file. Generally would
 * be used for child themes to override support from the parent theme.
 *
 *
 *
 * @see add_theme_support()
 *
 * @param string $feature The feature being removed. See add_theme_support() for the list
 *                        of possible values.
 * @return bool|void Whether feature was removed.
 */
function remove_theme_support( $feature ) {
	// Do not remove internal registrations that are not used directly by themes.
	if ( in_array( $feature, array( 'editor-style', 'widgets', 'menus' ), true ) ) {
		return false;
	}

	return _remove_theme_support( $feature );
}

/**
 * Do not use. Removes theme support internally without knowledge of those not used
 * by themes directly.
 *
 * @access private
 *
 * @global array               $_gc_theme_features
 * @global Custom_Image_Header $custom_image_header
 * @global Custom_Background   $custom_background
 *
 * @param string $feature The feature being removed. See add_theme_support() for the list
 *                        of possible values.
 * @return bool True if support was removed, false if the feature was not registered.
 */
function _remove_theme_support( $feature ) {
	global $_gc_theme_features;

	switch ( $feature ) {
		case 'custom-header-uploads':
			if ( ! isset( $_gc_theme_features['custom-header'] ) ) {
				return false;
			}
			add_theme_support( 'custom-header', array( 'uploads' => false ) );
			return; // Do not continue - custom-header-uploads no longer exists.
	}

	if ( ! isset( $_gc_theme_features[ $feature ] ) ) {
		return false;
	}

	switch ( $feature ) {
		case 'custom-header':
			if ( ! did_action( 'gc_loaded' ) ) {
				break;
			}
			$support = get_theme_support( 'custom-header' );
			if ( isset( $support[0]['gc-head-callback'] ) ) {
				remove_action( 'gc_head', $support[0]['gc-head-callback'] );
			}
			if ( isset( $GLOBALS['custom_image_header'] ) ) {
				remove_action( 'admin_menu', array( $GLOBALS['custom_image_header'], 'init' ) );
				unset( $GLOBALS['custom_image_header'] );
			}
			break;

		case 'custom-background':
			if ( ! did_action( 'gc_loaded' ) ) {
				break;
			}
			$support = get_theme_support( 'custom-background' );
			if ( isset( $support[0]['gc-head-callback'] ) ) {
				remove_action( 'gc_head', $support[0]['gc-head-callback'] );
			}
			remove_action( 'admin_menu', array( $GLOBALS['custom_background'], 'init' ) );
			unset( $GLOBALS['custom_background'] );
			break;
	}

	unset( $_gc_theme_features[ $feature ] );

	return true;
}

/**
 * Checks a theme's support for a given feature.
 *
 * Example usage:
 *
 *     current_theme_supports( 'custom-logo' );
 *     current_theme_supports( 'html5', 'comment-form' );
 *
 *
 *
 *              by adding it to the function signature.
 *
 * @global array $_gc_theme_features
 *
 * @param string $feature The feature being checked. See add_theme_support() for the list
 *                        of possible values.
 * @param mixed  ...$args Optional extra arguments to be checked against certain features.
 * @return bool True if the active theme supports the feature, false otherwise.
 */
function current_theme_supports( $feature, ...$args ) {
	global $_gc_theme_features;

	if ( 'custom-header-uploads' === $feature ) {
		return current_theme_supports( 'custom-header', 'uploads' );
	}

	if ( ! isset( $_gc_theme_features[ $feature ] ) ) {
		return false;
	}

	// If no args passed then no extra checks need be performed.
	if ( ! $args ) {
		return true;
	}

	switch ( $feature ) {
		case 'post-thumbnails':
			/*
			 * post-thumbnails can be registered for only certain content/post types
			 * by passing an array of types to add_theme_support().
			 * If no array was passed, then any type is accepted.
			 */
			if ( true === $_gc_theme_features[ $feature ] ) {  // Registered for all types.
				return true;
			}
			$content_type = $args[0];
			return in_array( $content_type, $_gc_theme_features[ $feature ][0], true );

		case 'html5':
		case 'post-formats':
			/*
			 * Specific post formats can be registered by passing an array of types
			 * to add_theme_support().
			 *
			 * Specific areas of HTML5 support *must* be passed via an array to add_theme_support().
			 */
			$type = $args[0];
			return in_array( $type, $_gc_theme_features[ $feature ][0], true );

		case 'custom-logo':
		case 'custom-header':
		case 'custom-background':
			// Specific capabilities can be registered by passing an array to add_theme_support().
			return ( isset( $_gc_theme_features[ $feature ][0][ $args[0] ] ) && $_gc_theme_features[ $feature ][0][ $args[0] ] );
	}

	/**
	 * Filters whether the active theme supports a specific feature.
	 *
	 * The dynamic portion of the hook name, `$feature`, refers to the specific
	 * theme feature. See add_theme_support() for the list of possible values.
	 *
	 *
	 * @param bool   $supports Whether the active theme supports the given feature. Default true.
	 * @param array  $args     Array of arguments for the feature.
	 * @param string $feature  The theme feature.
	 */
	return apply_filters( "current_theme_supports-{$feature}", true, $args, $_gc_theme_features[ $feature ] ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
}

/**
 * Checks a theme's support for a given feature before loading the functions which implement it.
 *
 *
 *
 * @param string $feature The feature being checked. See add_theme_support() for the list
 *                        of possible values.
 * @param string $include Path to the file.
 * @return bool True if the active theme supports the supplied feature, false otherwise.
 */
function require_if_theme_supports( $feature, $include ) {
	if ( current_theme_supports( $feature ) ) {
		require $include;
		return true;
	}
	return false;
}

/**
 * Registers a theme feature for use in add_theme_support().
 *
 * This does not indicate that the active theme supports the feature, it only describes
 * the feature's supported options.
 *
 *
 *
 * @see add_theme_support()
 *
 * @global array $_gc_registered_theme_features
 *
 * @param string $feature The name uniquely identifying the feature. See add_theme_support()
 *                        for the list of possible values.
 * @param array  $args {
 *     Data used to describe the theme.
 *
 *     @type string     $type         The type of data associated with this feature.
 *                                    Valid values are 'string', 'boolean', 'integer',
 *                                    'number', 'array', and 'object'. Defaults to 'boolean'.
 *     @type bool       $variadic     Does this feature utilize the variadic support
 *                                    of add_theme_support(), or are all arguments specified
 *                                    as the second parameter. Must be used with the "array" type.
 *     @type string     $description  A short description of the feature. Included in
 *                                    the Themes REST API schema. Intended for developers.
 *     @type bool|array $show_in_rest {
 *         Whether this feature should be included in the Themes REST API endpoint.
 *         Defaults to not being included. When registering an 'array' or 'object' type,
 *         this argument must be an array with the 'schema' key.
 *
 *         @type array    $schema           Specifies the JSON Schema definition describing
 *                                          the feature. If any objects in the schema do not include
 *                                          the 'additionalProperties' keyword, it is set to false.
 *         @type string   $name             An alternate name to be used as the property name
 *                                          in the REST API.
 *         @type callable $prepare_callback A function used to format the theme support in the REST API.
 *                                          Receives the raw theme support value.
 *      }
 * }
 * @return true|GC_Error True if the theme feature was successfully registered, a GC_Error object if not.
 */
function register_theme_feature( $feature, $args = array() ) {
	global $_gc_registered_theme_features;

	if ( ! is_array( $_gc_registered_theme_features ) ) {
		$_gc_registered_theme_features = array();
	}

	$defaults = array(
		'type'         => 'boolean',
		'variadic'     => false,
		'description'  => '',
		'show_in_rest' => false,
	);

	$args = gc_parse_args( $args, $defaults );

	if ( true === $args['show_in_rest'] ) {
		$args['show_in_rest'] = array();
	}

	if ( is_array( $args['show_in_rest'] ) ) {
		$args['show_in_rest'] = gc_parse_args(
			$args['show_in_rest'],
			array(
				'schema'           => array(),
				'name'             => $feature,
				'prepare_callback' => null,
			)
		);
	}

	if ( ! in_array( $args['type'], array( 'string', 'boolean', 'integer', 'number', 'array', 'object' ), true ) ) {
		return new GC_Error(
			'invalid_type',
			__( '特征“type”不是有效的JSON Schema类型。' )
		);
	}

	if ( true === $args['variadic'] && 'array' !== $args['type'] ) {
		return new GC_Error(
			'variadic_must_be_array',
			__( '如需注册“variadic”主题功能，其“type”必须为一个“array”。' )
		);
	}

	if ( false !== $args['show_in_rest'] && in_array( $args['type'], array( 'array', 'object' ), true ) ) {
		if ( ! is_array( $args['show_in_rest'] ) || empty( $args['show_in_rest']['schema'] ) ) {
			return new GC_Error(
				'missing_schema',
				__( '如需注册“array”或“object”功能以显示于REST API中，还需要定义此功能的模式描述。' )
			);
		}

		if ( 'array' === $args['type'] && ! isset( $args['show_in_rest']['schema']['items'] ) ) {
			return new GC_Error(
				'missing_schema_items',
				__( '如需注册“array”功能，此功能的模式描述必须包含“items”关键字。' )
			);
		}

		if ( 'object' === $args['type'] && ! isset( $args['show_in_rest']['schema']['properties'] ) ) {
			return new GC_Error(
				'missing_schema_properties',
				__( '如需注册“object”功能，此功能的模式描述必须包含“properties”关键字。' )
			);
		}
	}

	if ( is_array( $args['show_in_rest'] ) ) {
		if ( isset( $args['show_in_rest']['prepare_callback'] )
			&& ! is_callable( $args['show_in_rest']['prepare_callback'] )
		) {
			return new GC_Error(
				'invalid_rest_prepare_callback',
				sprintf(
					/* translators: %s: prepare_callback */
					__( '“%s”必须是可调用的函数。' ),
					'prepare_callback'
				)
			);
		}

		$args['show_in_rest']['schema'] = gc_parse_args(
			$args['show_in_rest']['schema'],
			array(
				'description' => $args['description'],
				'type'        => $args['type'],
				'default'     => false,
			)
		);

		if ( is_bool( $args['show_in_rest']['schema']['default'] )
			&& ! in_array( 'boolean', (array) $args['show_in_rest']['schema']['type'], true )
		) {
			// Automatically include the "boolean" type when the default value is a boolean.
			$args['show_in_rest']['schema']['type'] = (array) $args['show_in_rest']['schema']['type'];
			array_unshift( $args['show_in_rest']['schema']['type'], 'boolean' );
		}

		$args['show_in_rest']['schema'] = rest_default_additional_properties_to_false( $args['show_in_rest']['schema'] );
	}

	$_gc_registered_theme_features[ $feature ] = $args;

	return true;
}

/**
 * Gets the list of registered theme features.
 *
 *
 *
 * @global array $_gc_registered_theme_features
 *
 * @return array[] List of theme features, keyed by their name.
 */
function get_registered_theme_features() {
	global $_gc_registered_theme_features;

	if ( ! is_array( $_gc_registered_theme_features ) ) {
		return array();
	}

	return $_gc_registered_theme_features;
}

/**
 * Gets the registration config for a theme feature.
 *
 *
 *
 * @global array $_gc_registered_theme_features
 *
 * @param string $feature The feature name. See add_theme_support() for the list
 *                        of possible values.
 * @return array|null The registration args, or null if the feature was not registered.
 */
function get_registered_theme_feature( $feature ) {
	global $_gc_registered_theme_features;

	if ( ! is_array( $_gc_registered_theme_features ) ) {
		return null;
	}

	return isset( $_gc_registered_theme_features[ $feature ] ) ? $_gc_registered_theme_features[ $feature ] : null;
}

/**
 * Checks an attachment being deleted to see if it's a header or background image.
 *
 * If true it removes the theme modification which would be pointing at the deleted
 * attachment.
 *
 * @access private
 *
 *
 *
 *
 * @param int $id The attachment ID.
 */
function _delete_attachment_theme_mod( $id ) {
	$attachment_image = gc_get_attachment_url( $id );
	$header_image     = get_header_image();
	$background_image = get_background_image();
	$custom_logo_id   = get_theme_mod( 'custom_logo' );

	if ( $custom_logo_id && $custom_logo_id == $id ) {
		remove_theme_mod( 'custom_logo' );
		remove_theme_mod( 'header_text' );
	}

	if ( $header_image && $header_image == $attachment_image ) {
		remove_theme_mod( 'header_image' );
		remove_theme_mod( 'header_image_data' );
	}

	if ( $background_image && $background_image == $attachment_image ) {
		remove_theme_mod( 'background_image' );
	}
}

/**
 * Checks if a theme has been changed and runs 'after_switch_theme' hook on the next GC load.
 *
 * See {@see 'after_switch_theme'}.
 *
 *
 */
function check_theme_switched() {
	$stylesheet = get_option( 'theme_switched' );

	if ( $stylesheet ) {
		$old_theme = gc_get_theme( $stylesheet );

		// Prevent widget & menu mapping from running since Customizer already called it up front.
		if ( get_option( 'theme_switched_via_customizer' ) ) {
			remove_action( 'after_switch_theme', '_gc_menus_changed' );
			remove_action( 'after_switch_theme', '_gc_sidebars_changed' );
			update_option( 'theme_switched_via_customizer', false );
		}

		if ( $old_theme->exists() ) {
			/**
			 * Fires on the first GC load after a theme switch if the old theme still exists.
			 *
			 * This action fires multiple times and the parameters differs
			 * according to the context, if the old theme exists or not.
			 * If the old theme is missing, the parameter will be the slug
			 * of the old theme.
			 *
		
			 *
			 * @param string   $old_name  Old theme name.
			 * @param GC_Theme $old_theme GC_Theme instance of the old theme.
			 */
			do_action( 'after_switch_theme', $old_theme->get( 'Name' ), $old_theme );
		} else {
			/** This action is documented in gc-includes/theme.php */
			do_action( 'after_switch_theme', $stylesheet, $old_theme );
		}

		flush_rewrite_rules();

		update_option( 'theme_switched', false );
	}
}

/**
 * Includes and instantiates the GC_Customize_Manager class.
 *
 * Loads the Customizer at plugins_loaded when accessing the customize.php admin
 * page or when any request includes a gc_customize=on param or a customize_changeset
 * param (a UUID). This param is a signal for whether to bootstrap the Customizer when
 * GeChiUI is loading, especially in the Customizer preview
 * or when making Customizer Ajax requests for widgets or menus.
 *
 *
 *
 * @global GC_Customize_Manager $gc_customize
 */
function _gc_customize_include() {

	$is_customize_admin_page = ( is_admin() && 'customize.php' === basename( $_SERVER['PHP_SELF'] ) );
	$should_include          = (
		$is_customize_admin_page
		||
		( isset( $_REQUEST['gc_customize'] ) && 'on' === $_REQUEST['gc_customize'] )
		||
		( ! empty( $_GET['customize_changeset_uuid'] ) || ! empty( $_POST['customize_changeset_uuid'] ) )
	);

	if ( ! $should_include ) {
		return;
	}

	/*
	 * Note that gc_unslash() is not being used on the input vars because it is
	 * called before gc_magic_quotes() gets called. Besides this fact, none of
	 * the values should contain any characters needing slashes anyway.
	 */
	$keys       = array(
		'changeset_uuid',
		'customize_changeset_uuid',
		'customize_theme',
		'theme',
		'customize_messenger_channel',
		'customize_autosaved',
	);
	$input_vars = array_merge(
		gc_array_slice_assoc( $_GET, $keys ),
		gc_array_slice_assoc( $_POST, $keys )
	);

	$theme             = null;
	$autosaved         = null;
	$messenger_channel = null;

	// Value false indicates UUID should be determined after_setup_theme
	// to either re-use existing saved changeset or else generate a new UUID if none exists.
	$changeset_uuid = false;

	// Set initially fo false since defaults to true for back-compat;
	// can be overridden via the customize_changeset_branching filter.
	$branching = false;

	if ( $is_customize_admin_page && isset( $input_vars['changeset_uuid'] ) ) {
		$changeset_uuid = sanitize_key( $input_vars['changeset_uuid'] );
	} elseif ( ! empty( $input_vars['customize_changeset_uuid'] ) ) {
		$changeset_uuid = sanitize_key( $input_vars['customize_changeset_uuid'] );
	}

	// Note that theme will be sanitized via GC_Theme.
	if ( $is_customize_admin_page && isset( $input_vars['theme'] ) ) {
		$theme = $input_vars['theme'];
	} elseif ( isset( $input_vars['customize_theme'] ) ) {
		$theme = $input_vars['customize_theme'];
	}

	if ( ! empty( $input_vars['customize_autosaved'] ) ) {
		$autosaved = true;
	}

	if ( isset( $input_vars['customize_messenger_channel'] ) ) {
		$messenger_channel = sanitize_key( $input_vars['customize_messenger_channel'] );
	}

	/*
	 * Note that settings must be previewed even outside the customizer preview
	 * and also in the customizer pane itself. This is to enable loading an existing
	 * changeset into the customizer. Previewing the settings only has to be prevented
	 * here in the case of a customize_save action because this will cause GC to think
	 * there is nothing changed that needs to be saved.
	 */
	$is_customize_save_action = (
		gc_doing_ajax()
		&&
		isset( $_REQUEST['action'] )
		&&
		'customize_save' === gc_unslash( $_REQUEST['action'] )
	);
	$settings_previewed       = ! $is_customize_save_action;

	require_once ABSPATH . GCINC . '/class-gc-customize-manager.php';
	$GLOBALS['gc_customize'] = new GC_Customize_Manager(
		compact(
			'changeset_uuid',
			'theme',
			'messenger_channel',
			'settings_previewed',
			'autosaved',
			'branching'
		)
	);
}

/**
 * Publishes a snapshot's changes.
 *
 *
 * @access private
 *
 * @global gcdb                 $gcdb         GeChiUI database abstraction object.
 * @global GC_Customize_Manager $gc_customize Customizer instance.
 *
 * @param string  $new_status     New post status.
 * @param string  $old_status     Old post status.
 * @param GC_Post $changeset_post Changeset post object.
 */
function _gc_customize_publish_changeset( $new_status, $old_status, $changeset_post ) {
	global $gc_customize, $gcdb;

	$is_publishing_changeset = (
		'customize_changeset' === $changeset_post->post_type
		&&
		'publish' === $new_status
		&&
		'publish' !== $old_status
	);
	if ( ! $is_publishing_changeset ) {
		return;
	}

	if ( empty( $gc_customize ) ) {
		require_once ABSPATH . GCINC . '/class-gc-customize-manager.php';
		$gc_customize = new GC_Customize_Manager(
			array(
				'changeset_uuid'     => $changeset_post->post_name,
				'settings_previewed' => false,
			)
		);
	}

	if ( ! did_action( 'customize_register' ) ) {
		/*
		 * When running from CLI or Cron, the customize_register action will need
		 * to be triggered in order for core, themes, and plugins to register their
		 * settings. Normally core will add_action( 'customize_register' ) at
		 * priority 10 to register the core settings, and if any themes/plugins
		 * also add_action( 'customize_register' ) at the same priority, they
		 * will have a $gc_customize with those settings registered since they
		 * call add_action() afterward, normally. However, when manually doing
		 * the customize_register action after the setup_theme, then the order
		 * will be reversed for two actions added at priority 10, resulting in
		 * the core settings no longer being available as expected to themes/plugins.
		 * So the following manually calls the method that registers the core
		 * settings up front before doing the action.
		 */
		remove_action( 'customize_register', array( $gc_customize, 'register_controls' ) );
		$gc_customize->register_controls();

		/** This filter is documented in /gc-includes/class-gc-customize-manager.php */
		do_action( 'customize_register', $gc_customize );
	}
	$gc_customize->_publish_changeset_values( $changeset_post->ID );

	/*
	 * Trash the changeset post if revisions are not enabled. Unpublished
	 * changesets by default get garbage collected due to the auto-draft status.
	 * When a changeset post is published, however, it would no longer get cleaned
	 * out. This is a problem when the changeset posts are never displayed anywhere,
	 * since they would just be endlessly piling up. So here we use the revisions
	 * feature to indicate whether or not a published changeset should get trashed
	 * and thus garbage collected.
	 */
	if ( ! gc_revisions_enabled( $changeset_post ) ) {
		$gc_customize->trash_changeset_post( $changeset_post->ID );
	}
}

/**
 * Filters changeset post data upon insert to ensure post_name is intact.
 *
 * This is needed to prevent the post_name from being dropped when the post is
 * transitioned into pending status by a contributor.
 *
 *
 *
 * @see gc_insert_post()
 *
 * @param array $post_data          An array of slashed post data.
 * @param array $supplied_post_data An array of sanitized, but otherwise unmodified post data.
 * @return array Filtered data.
 */
function _gc_customize_changeset_filter_insert_post_data( $post_data, $supplied_post_data ) {
	if ( isset( $post_data['post_type'] ) && 'customize_changeset' === $post_data['post_type'] ) {

		// Prevent post_name from being dropped, such as when contributor saves a changeset post as pending.
		if ( empty( $post_data['post_name'] ) && ! empty( $supplied_post_data['post_name'] ) ) {
			$post_data['post_name'] = $supplied_post_data['post_name'];
		}
	}
	return $post_data;
}

/**
 * Adds settings for the customize-loader script.
 *
 *
 */
function _gc_customize_loader_settings() {
	$admin_origin = parse_url( admin_url() );
	$home_origin  = parse_url( home_url() );
	$cross_domain = ( strtolower( $admin_origin['host'] ) != strtolower( $home_origin['host'] ) );

	$browser = array(
		'mobile' => gc_is_mobile(),
		'ios'    => gc_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] ),
	);

	$settings = array(
		'url'           => esc_url( admin_url( 'customize.php' ) ),
		'isCrossDomain' => $cross_domain,
		'browser'       => $browser,
		'l10n'          => array(
			'saveAlert'       => __( '离开这个页面，您所做的更改将丢失。' ),
			'mainIframeTitle' => __( '定制器' ),
		),
	);

	$script = 'var _gcCustomizeLoaderSettings = ' . gc_json_encode( $settings ) . ';';

	$gc_scripts = gc_scripts();
	$data       = $gc_scripts->get_data( 'customize-loader', 'data' );
	if ( $data ) {
		$script = "$data\n$script";
	}

	$gc_scripts->add_data( 'customize-loader', 'data', $script );
}

/**
 * Returns a URL to load the Customizer.
 *
 *
 *
 * @param string $stylesheet Optional. Theme to customize. Defaults to active theme.
 *                           The theme's stylesheet will be urlencoded if necessary.
 * @return string
 */
function gc_customize_url( $stylesheet = '' ) {
	$url = admin_url( 'customize.php' );
	if ( $stylesheet ) {
		$url .= '?theme=' . urlencode( $stylesheet );
	}
	return esc_url( $url );
}

/**
 * Prints a script to check whether or not the Customizer is supported,
 * and apply either the no-customize-support or customize-support class
 * to the body.
 *
 * This function MUST be called inside the body tag.
 *
 * Ideally, call this function immediately after the body tag is opened.
 * This prevents a flash of unstyled content.
 *
 * It is also recommended that you add the "no-customize-support" class
 * to the body tag by default.
 *
 *
 *
 *
 */
function gc_customize_support_script() {
	$admin_origin = parse_url( admin_url() );
	$home_origin  = parse_url( home_url() );
	$cross_domain = ( strtolower( $admin_origin['host'] ) != strtolower( $home_origin['host'] ) );
	$type_attr    = current_theme_supports( 'html5', 'script' ) ? '' : ' type="text/javascript"';
	?>
	<script<?php echo $type_attr; ?>>
		(function() {
			var request, b = document.body, c = 'className', cs = 'customize-support', rcs = new RegExp('(^|\\s+)(no-)?'+cs+'(\\s+|$)');

	<?php	if ( $cross_domain ) : ?>
			request = (function(){ var xhr = new XMLHttpRequest(); return ('withCredentials' in xhr); })();
	<?php	else : ?>
			request = true;
	<?php	endif; ?>

			b[c] = b[c].replace( rcs, ' ' );
			// The customizer requires postMessage and CORS (if the site is cross domain).
			b[c] += ( window.postMessage && request ? ' ' : ' no-' ) + cs;
		}());
	</script>
	<?php
}

/**
 * Whether the site is being previewed in the Customizer.
 *
 *
 *
 * @global GC_Customize_Manager $gc_customize Customizer instance.
 *
 * @return bool True if the site is being previewed in the Customizer, false otherwise.
 */
function is_customize_preview() {
	global $gc_customize;

	return ( $gc_customize instanceof GC_Customize_Manager ) && $gc_customize->is_preview();
}

/**
 * Makes sure that auto-draft posts get their post_date bumped or status changed
 * to draft to prevent premature garbage-collection.
 *
 * When a changeset is updated but remains an auto-draft, ensure the post_date
 * for the auto-draft posts remains the same so that it will be
 * garbage-collected at the same time by `gc_delete_auto_drafts()`. Otherwise,
 * if the changeset is updated to be a draft then update the posts
 * to have a far-future post_date so that they will never be garbage collected
 * unless the changeset post itself is deleted.
 *
 * When a changeset is updated to be a persistent draft or to be scheduled for
 * publishing, then transition any dependent auto-drafts to a draft status so
 * that they likewise will not be garbage-collected but also so that they can
 * be edited in the admin before publishing since there is not yet a post/page
 * editing flow in the Customizer. See #39752.
 *
 * @link https://core.trac.gechiui.com/ticket/39752
 *
 *
 * @access private
 * @see gc_delete_auto_drafts()
 *
 * @global gcdb $gcdb GeChiUI database abstraction object.
 *
 * @param string   $new_status Transition to this post status.
 * @param string   $old_status Previous post status.
 * @param \GC_Post $post       Post data.
 */
function _gc_keep_alive_customize_changeset_dependent_auto_drafts( $new_status, $old_status, $post ) {
	global $gcdb;
	unset( $old_status );

	// Short-circuit if not a changeset or if the changeset was published.
	if ( 'customize_changeset' !== $post->post_type || 'publish' === $new_status ) {
		return;
	}

	$data = json_decode( $post->post_content, true );
	if ( empty( $data['nav_menus_created_posts']['value'] ) ) {
		return;
	}

	/*
	 * Actually, in lieu of keeping alive, trash any customization drafts here if the changeset itself is
	 * getting trashed. This is needed because when a changeset transitions to a draft, then any of the
	 * dependent auto-draft post/page stubs will also get transitioned to customization drafts which
	 * are then visible in the GC Admin. We cannot wait for the deletion of the changeset in which
	 * _gc_delete_customize_changeset_dependent_auto_drafts() will be called, since they need to be
	 * trashed to remove from visibility immediately.
	 */
	if ( 'trash' === $new_status ) {
		foreach ( $data['nav_menus_created_posts']['value'] as $post_id ) {
			if ( ! empty( $post_id ) && 'draft' === get_post_status( $post_id ) ) {
				gc_trash_post( $post_id );
			}
		}
		return;
	}

	$post_args = array();
	if ( 'auto-draft' === $new_status ) {
		/*
		 * Keep the post date for the post matching the changeset
		 * so that it will not be garbage-collected before the changeset.
		 */
		$post_args['post_date'] = $post->post_date; // Note gc_delete_auto_drafts() only looks at this date.
	} else {
		/*
		 * Since the changeset no longer has an auto-draft (and it is not published)
		 * it is now a persistent changeset, a long-lived draft, and so any
		 * associated auto-draft posts should likewise transition into having a draft
		 * status. These drafts will be treated differently than regular drafts in
		 * that they will be tied to the given changeset. The publish meta box is
		 * replaced with a notice about how the post is part of a set of customized changes
		 * which will be published when the changeset is published.
		 */
		$post_args['post_status'] = 'draft';
	}

	foreach ( $data['nav_menus_created_posts']['value'] as $post_id ) {
		if ( empty( $post_id ) || 'auto-draft' !== get_post_status( $post_id ) ) {
			continue;
		}
		$gcdb->update(
			$gcdb->posts,
			$post_args,
			array( 'ID' => $post_id )
		);
		clean_post_cache( $post_id );
	}
}

/**
 * Creates the initial theme features when the 'setup_theme' action is fired.
 *
 * See {@see 'setup_theme'}.
 *
 *
 */
function create_initial_theme_features() {
	register_theme_feature(
		'align-wide',
		array(
			'description'  => __( '主题是否使用宽对齐CSS类。' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'automatic-feed-links',
		array(
			'description'  => __( '是否将文章和评论RSS feed链接加入头部。' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'custom-background',
		array(
			'description'  => __( '如果可由主题定义，便自定义背景。' ),
			'type'         => 'object',
			'show_in_rest' => array(
				'schema' => array(
					'properties' => array(
						'default-image'      => array(
							'type'   => 'string',
							'format' => 'uri',
						),
						'default-preset'     => array(
							'type' => 'string',
							'enum' => array(
								'default',
								'fill',
								'fit',
								'repeat',
								'custom',
							),
						),
						'default-position-x' => array(
							'type' => 'string',
							'enum' => array(
								'left',
								'center',
								'right',
							),
						),
						'default-position-y' => array(
							'type' => 'string',
							'enum' => array(
								'left',
								'center',
								'right',
							),
						),
						'default-size'       => array(
							'type' => 'string',
							'enum' => array(
								'auto',
								'contain',
								'cover',
							),
						),
						'default-repeat'     => array(
							'type' => 'string',
							'enum' => array(
								'repeat-x',
								'repeat-y',
								'repeat',
								'no-repeat',
							),
						),
						'default-attachment' => array(
							'type' => 'string',
							'enum' => array(
								'scroll',
								'fixed',
							),
						),
						'default-color'      => array(
							'type' => 'string',
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'custom-header',
		array(
			'description'  => __( '如果可由主题定义，便自定义页眉。' ),
			'type'         => 'object',
			'show_in_rest' => array(
				'schema' => array(
					'properties' => array(
						'default-image'      => array(
							'type'   => 'string',
							'format' => 'uri',
						),
						'random-default'     => array(
							'type' => 'boolean',
						),
						'width'              => array(
							'type' => 'integer',
						),
						'height'             => array(
							'type' => 'integer',
						),
						'flex-height'        => array(
							'type' => 'boolean',
						),
						'flex-width'         => array(
							'type' => 'boolean',
						),
						'default-text-color' => array(
							'type' => 'string',
						),
						'header-text'        => array(
							'type' => 'boolean',
						),
						'uploads'            => array(
							'type' => 'boolean',
						),
						'video'              => array(
							'type' => 'boolean',
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'custom-logo',
		array(
			'type'         => 'object',
			'description'  => __( '如果可由主题定义，便自定义 logo。' ),
			'show_in_rest' => array(
				'schema' => array(
					'properties' => array(
						'width'                => array(
							'type' => 'integer',
						),
						'height'               => array(
							'type' => 'integer',
						),
						'flex-width'           => array(
							'type' => 'boolean',
						),
						'flex-height'          => array(
							'type' => 'boolean',
						),
						'header-text'          => array(
							'type'  => 'array',
							'items' => array(
								'type' => 'string',
							),
						),
						'unlink-homepage-logo' => array(
							'type' => 'boolean',
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'customize-selective-refresh-widgets',
		array(
			'description'  => __( '主题是否对由定制器管理的挂件启用选择性刷新。' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'dark-editor-style',
		array(
			'description'  => __( '此主题是否使用深色编辑器UI。' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'disable-custom-colors',
		array(
			'description'  => __( '主题是否禁用自定义颜色。' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'disable-custom-font-sizes',
		array(
			'description'  => __( '主题是否禁用自定义字号。' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'disable-custom-gradients',
		array(
			'description'  => __( '主题是否禁用自定义渐变色。' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'editor-color-palette',
		array(
			'type'         => 'array',
			'description'  => __( '如果可由主题定义，便自定义调色盘。' ),
			'show_in_rest' => array(
				'schema' => array(
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'name'  => array(
								'type' => 'string',
							),
							'slug'  => array(
								'type' => 'string',
							),
							'color' => array(
								'type' => 'string',
							),
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'editor-font-sizes',
		array(
			'type'         => 'array',
			'description'  => __( '如果可由主题定义，便自定义字体大小。' ),
			'show_in_rest' => array(
				'schema' => array(
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'name' => array(
								'type' => 'string',
							),
							'size' => array(
								'type' => 'number',
							),
							'slug' => array(
								'type' => 'string',
							),
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'editor-gradient-presets',
		array(
			'type'         => 'array',
			'description'  => __( '如果可由主题定义，便自定义预设渐变色。' ),
			'show_in_rest' => array(
				'schema' => array(
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'name'     => array(
								'type' => 'string',
							),
							'gradient' => array(
								'type' => 'string',
							),
							'slug'     => array(
								'type' => 'string',
							),
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'editor-styles',
		array(
			'description'  => __( '此主题是否使用编辑器样式CSS wrapper。' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'html5',
		array(
			'type'         => 'array',
			'description'  => __( '允许搜索表单、评论表单、评论列表、相册和说明文字使用HTML5标签。' ),
			'show_in_rest' => array(
				'schema' => array(
					'items' => array(
						'type' => 'string',
						'enum' => array(
							'search-form',
							'comment-form',
							'comment-list',
							'gallery',
							'caption',
							'script',
							'style',
						),
					),
				),
			),
		)
	);
	register_theme_feature(
		'post-formats',
		array(
			'type'         => 'array',
			'description'  => __( '支持的文章形式。' ),
			'show_in_rest' => array(
				'name'             => 'formats',
				'schema'           => array(
					'items'   => array(
						'type' => 'string',
						'enum' => get_post_format_slugs(),
					),
					'default' => array( 'standard' ),
				),
				'prepare_callback' => static function ( $formats ) {
					$formats = is_array( $formats ) ? array_values( $formats[0] ) : array();
					$formats = array_merge( array( 'standard' ), $formats );

					return $formats;
				},
			),
		)
	);
	register_theme_feature(
		'post-thumbnails',
		array(
			'type'         => 'array',
			'description'  => __( '支持缩略图的文章类型，如果支持所有的文章类型，则为true。' ),
			'show_in_rest' => array(
				'type'   => array( 'boolean', 'array' ),
				'schema' => array(
					'items' => array(
						'type' => 'string',
					),
				),
			),
		)
	);
	register_theme_feature(
		'responsive-embeds',
		array(
			'description'  => __( '主题是否支持响应式嵌入内容。' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'title-tag',
		array(
			'description'  => __( '主题是否可以管理文档标题标签。' ),
			'show_in_rest' => true,
		)
	);
	register_theme_feature(
		'gc-block-styles',
		array(
			'description'  => __( '此主题是否在查看时默认使用GeChiUI区块样式。' ),
			'show_in_rest' => true,
		)
	);
}

/**
 * Returns whether the active theme is a block-based theme or not.
 *
 *
 *
 * @return boolean Whether the active theme is a block-based theme or not.
 */
function gc_is_block_theme() {
	return gc_get_theme()->is_block_theme();
}

/**
 * Adds default theme supports for block themes when the 'setup_theme' action fires.
 *
 * See {@see 'setup_theme'}.
 *
 *
 * @access private
 */
function _add_default_theme_supports() {
	if ( ! gc_is_block_theme() ) {
		return;
	}

	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	/*
	 * Makes block themes support HTML5 by default for the comment block and search form
	 * (which use default template functions) and `[caption]` and `[gallery]` shortcodes.
	 * Other blocks contain their own HTML5 markup.
	 */
	add_theme_support( 'html5', array( 'comment-form', 'comment-list', 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'automatic-feed-links' );

	add_filter( 'should_load_separate_core_block_assets', '__return_true' );
}
