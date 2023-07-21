<?php
/**
 * GeChiUI scripts and styles default loader.
 *
 * Several constants are used to manage the loading, concatenating and compression of scripts and CSS:
 * define('SCRIPT_DEBUG', true); loads the development (non-minified) versions of all scripts and CSS, and disables compression and concatenation,
 * define('CONCATENATE_SCRIPTS', false); disables compression and concatenation of scripts and CSS,
 * define('COMPRESS_SCRIPTS', false); disables compression of scripts,
 * define('COMPRESS_CSS', false); disables compression of CSS,
 * define('ENFORCE_GZIP', true); forces gzip for compression (default is deflate).
 *
 * The globals $concatenate_scripts, $compress_scripts and $compress_css can be set by plugins
 * to temporarily override the above settings. Also a compression test is run once and the result is saved
 * as option 'can_compress_scripts' (0/1). The test will run again if that option is deleted.
 *
 * @package GeChiUI
 */

/** GeChiUI Dependency Class */
require ABSPATH . GCINC . '/class-gc-dependency.php';

/** GeChiUI Dependencies Class */
require ABSPATH . GCINC . '/class.gc-dependencies.php';

/** GeChiUI Scripts Class */
require ABSPATH . GCINC . '/class.gc-scripts.php';

/** GeChiUI Scripts Functions */
require ABSPATH . GCINC . '/functions.gc-scripts.php';

/** GeChiUI Styles Class */
require ABSPATH . GCINC . '/class.gc-styles.php';

/** GeChiUI Styles Functions */
require ABSPATH . GCINC . '/functions.gc-styles.php';

/**
 * Registers TinyMCE scripts.
 *
 *
 *
 * @global string $tinymce_version
 * @global bool   $concatenate_scripts
 * @global bool   $compress_scripts
 *
 * @param GC_Scripts $scripts            GC_Scripts object.
 * @param bool       $force_uncompressed Whether to forcibly prevent gzip compression. Default false.
 */
function gc_register_tinymce_scripts( $scripts, $force_uncompressed = false ) {
	global $tinymce_version, $concatenate_scripts, $compress_scripts;

	$suffix     = gc_scripts_get_suffix();
	$dev_suffix = gc_scripts_get_suffix( 'dev' );

	script_concat_settings();

	$compressed = $compress_scripts && $concatenate_scripts && isset( $_SERVER['HTTP_ACCEPT_ENCODING'] )
		&& false !== stripos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) && ! $force_uncompressed;

	// Load tinymce.js when running from /src, otherwise load gc-tinymce.js.gz (in production)
	// or tinymce.min.js (when SCRIPT_DEBUG is true).
	if ( $compressed ) {
		$scripts->add( 'gc-tinymce', '/vendors/tinymce/gc-tinymce.js', array(), $tinymce_version );
	} else {
		$scripts->add( 'gc-tinymce-root', "/vendors/tinymce/tinymce$dev_suffix.js", array(), $tinymce_version );
		$scripts->add( 'gc-tinymce', "/vendors/tinymce/plugins/compat3x/plugin$dev_suffix.js", array( 'gc-tinymce-root' ), $tinymce_version );
	}

	$scripts->add( 'gc-tinymce-lists', "/vendors/tinymce/plugins/lists/plugin$suffix.js", array( 'gc-tinymce' ), $tinymce_version );
}

/**
 * Registers all the GeChiUI vendor scripts that are in the standardized
 * `js/dist/vendor/` location.
 *
 * For the order of `$scripts->add` see `gc_default_scripts`.
 *
 *
 *
 * @global GC_Locale $gc_locale GeChiUI date and time locale object.
 *
 * @param GC_Scripts $scripts GC_Scripts object.
 */
function gc_default_packages_vendor( $scripts ) {
	global $gc_locale;

	$suffix = gc_scripts_get_suffix();

	$vendor_scripts = array(
		'react'       => array( 'gc-polyfill' ),
		'react-dom'   => array( 'react' ),
		'regenerator-runtime',
		'moment',
		'lodash',
		'gc-polyfill-fetch',
		'gc-polyfill-formdata',
		'gc-polyfill-node-contains',
		'gc-polyfill-url',
		'gc-polyfill-dom-rect',
		'gc-polyfill-element-closest',
		'gc-polyfill-object-fit',
		'gc-polyfill' => array( 'regenerator-runtime' ),
	);

	$vendor_scripts_versions = array(
		'react'                       => '17.0.1',
		'react-dom'                   => '17.0.1',
		'regenerator-runtime'         => '0.13.9',
		'moment'                      => '2.29.1',
		'lodash'                      => '4.17.19',
		'gc-polyfill-fetch'           => '3.6.2',
		'gc-polyfill-formdata'        => '4.0.0',
		'gc-polyfill-node-contains'   => '3.105.0',
		'gc-polyfill-url'             => '3.6.4',
		'gc-polyfill-dom-rect'        => '3.104.0',
		'gc-polyfill-element-closest' => '2.0.2',
		'gc-polyfill-object-fit'      => '2.3.5',
		'gc-polyfill'                 => '3.15.0',
	);

	foreach ( $vendor_scripts as $handle => $dependencies ) {
		if ( is_string( $dependencies ) ) {
			$handle       = $dependencies;
			$dependencies = array();
		}

		$path    = "/js/dist/vendor/$handle$suffix.js";
		$version = $vendor_scripts_versions[ $handle ];

		$scripts->add( $handle, $path, $dependencies, $version, 1 );
	}

	did_action( 'init' ) && $scripts->add_inline_script( 'lodash', 'window.lodash = _.noConflict();' );

	did_action( 'init' ) && $scripts->add_inline_script(
		'moment',
		sprintf(
			"moment.updateLocale( '%s', %s );",
			get_user_locale(),
			gc_json_encode(
				array(
					'months'         => array_values( $gc_locale->month ),
					'monthsShort'    => array_values( $gc_locale->month_abbrev ),
					'weekdays'       => array_values( $gc_locale->weekday ),
					'weekdaysShort'  => array_values( $gc_locale->weekday_abbrev ),
					'week'           => array(
						'dow' => (int) get_option( 'start_of_week', 0 ),
					),
					'longDateFormat' => array(
						'LT'   => get_option( 'time_format', __( 'ag:i' ) ),
						'LTS'  => null,
						'L'    => null,
						'LL'   => get_option( 'date_format', __( 'Y年n月j日' ) ),
						'LLL'  => __( 'Y年n月j日ag:i' ),
						'LLLL' => null,
					),
				)
			)
		),
		'after'
	);
}

/**
 * Returns contents of an inline script used in appending polyfill scripts for
 * browsers which fail the provided tests. The provided array is a mapping from
 * a condition to verify feature support to its polyfill script handle.
 *
 *
 *
 * @param GC_Scripts $scripts GC_Scripts object.
 * @param array      $tests   Features to detect.
 * @return string Conditional polyfill inline script.
 */
function gc_get_script_polyfill( $scripts, $tests ) {
	$polyfill = '';
	foreach ( $tests as $test => $handle ) {
		if ( ! array_key_exists( $handle, $scripts->registered ) ) {
			continue;
		}

		$src = $scripts->registered[ $handle ]->src;
		$ver = $scripts->registered[ $handle ]->ver;

		if ( ! preg_match( '|^(https?:)?//|', $src ) && ! ( $scripts->content_url && 0 === strpos( $src, $scripts->content_url ) ) ) {
			$src = $scripts->base_url . $src;
		}

		if ( ! empty( $ver ) ) {
			$src = add_query_arg( 'ver', $ver, $src );
		}

		/** This filter is documented in gc-includes/class.gc-scripts.php */
		$src = esc_url( apply_filters( 'script_loader_src', $src, $handle ) );

		if ( ! $src ) {
			continue;
		}

		$polyfill .= (
			// Test presence of feature...
			'( ' . $test . ' ) || ' .
			/*
			 * ...appending polyfill on any failures. Cautious viewers may balk
			 * at the `document.write`. Its caveat of synchronous mid-stream
			 * blocking write is exactly the behavior we need though.
			 */
			'document.write( \'<script src="' .
			$src .
			'"></scr\' + \'ipt>\' );'
		);
	}

	return $polyfill;
}

/**
 * Registers all the GeChiUI packages scripts that are in the standardized
 * `js/dist/` location.
 *
 * For the order of `$scripts->add` see `gc_default_scripts`.
 *
 *
 *
 * @param GC_Scripts $scripts GC_Scripts object.
 */
function gc_default_packages_scripts( $scripts ) {
	$suffix = gc_scripts_get_suffix();

	/*
	 * Expects multidimensional array like:
	 *
	 *     'a11y.js' => array('dependencies' => array(...), 'version' => '...'),
	 *     'annotations.js' => array('dependencies' => array(...), 'version' => '...'),
	 *     'api-fetch.js' => array(...
	 */
	$assets = include ABSPATH . 'assets/assets/script-loader-packages.php';

	foreach ( $assets as $package_name => $package_data ) {
		$basename = basename( $package_name, '.js' );
		$handle   = 'gc-' . $basename;
		$path     = "/js/dist/{$basename}{$suffix}.js";

		if ( ! empty( $package_data['dependencies'] ) ) {
			$dependencies = $package_data['dependencies'];
		} else {
			$dependencies = array();
		}

		// Add dependencies that cannot be detected and generated by build tools.
		switch ( $handle ) {
			case 'gc-block-library':
				array_push( $dependencies, 'editor' );
				break;
			case 'gc-edit-post':
				array_push( $dependencies, 'media-models', 'media-views', 'postbox', 'gc-dom-ready' );
				break;
		}

		$scripts->add( $handle, $path, $dependencies, $package_data['version'], 1 );

		if ( in_array( 'gc-i18n', $dependencies, true ) ) {
			$scripts->set_translations( $handle );
		}

		/*
		 * Manually set the text direction localization after gc-i18n is printed.
		 * This ensures that gc.i18n.isRTL() returns true in RTL languages.
		 * We cannot use $scripts->set_translations( 'gc-i18n' ) to do this
		 * because GeChiUI prints a script's translations *before* the script,
		 * which means, in the case of gc-i18n, that gc.i18n.setLocaleData()
		 * is called before gc.i18n is defined.
		 */
		if ( 'gc-i18n' === $handle ) {
			$ltr    = _x( 'ltr', '文本方向' );
			$script = sprintf( "gc.i18n.setLocaleData( { 'text direction\u0004ltr': [ '%s' ] } );", $ltr );
			$scripts->add_inline_script( $handle, $script, 'after' );
		}
	}
}

/**
 * Adds inline scripts required for the GeChiUI JavaScript packages.
 *
 *
 *
 * @global GC_Locale $gc_locale GeChiUI date and time locale object.
 *
 * @param GC_Scripts $scripts GC_Scripts object.
 */
function gc_default_packages_inline_scripts( $scripts ) {
	global $gc_locale;

	if ( isset( $scripts->registered['gc-api-fetch'] ) ) {
		$scripts->registered['gc-api-fetch']->deps[] = 'gc-hooks';
	}
	$scripts->add_inline_script(
		'gc-api-fetch',
		sprintf(
			'gc.apiFetch.use( gc.apiFetch.createRootURLMiddleware( "%s" ) );',
			esc_url_raw( get_rest_url() )
		),
		'after'
	);
	$scripts->add_inline_script(
		'gc-api-fetch',
		implode(
			"\n",
			array(
				sprintf(
					'gc.apiFetch.nonceMiddleware = gc.apiFetch.createNonceMiddleware( "%s" );',
					gc_installing() ? '' : gc_create_nonce( 'gc_rest' )
				),
				'gc.apiFetch.use( gc.apiFetch.nonceMiddleware );',
				'gc.apiFetch.use( gc.apiFetch.mediaUploadMiddleware );',
				sprintf(
					'gc.apiFetch.nonceEndpoint = "%s";',
					admin_url( 'admin-ajax.php?action=rest-nonce' )
				),
			)
		),
		'after'
	);
	$scripts->add_inline_script(
		'gc-data',
		implode(
			"\n",
			array(
				'( function() {',
				'	var userId = ' . get_current_user_ID() . ';',
				'	var storageKey = "GC_DATA_USER_" + userId;',
				'	gc.data',
				'		.use( gc.data.plugins.persistence, { storageKey: storageKey } );',
				'	gc.data.plugins.persistence.__unstableMigrate( { storageKey: storageKey } );',
				'} )();',
			)
		)
	);

	// Calculate the timezone abbr (EDT, PST) if possible.
	$timezone_string = get_option( 'timezone_string', 'UTC' );
	$timezone_abbr   = '';

	if ( ! empty( $timezone_string ) ) {
		$timezone_date = new DateTime( 'now', new DateTimeZone( $timezone_string ) );
		$timezone_abbr = $timezone_date->format( 'T' );
	}

	$scripts->add_inline_script(
		'gc-date',
		sprintf(
			'gc.date.setSettings( %s );',
			gc_json_encode(
				array(
					'l10n'     => array(
						'locale'        => get_user_locale(),
						'months'        => array_values( $gc_locale->month ),
						'monthsShort'   => array_values( $gc_locale->month_abbrev ),
						'weekdays'      => array_values( $gc_locale->weekday ),
						'weekdaysShort' => array_values( $gc_locale->weekday_abbrev ),
						'meridiem'      => (object) $gc_locale->meridiem,
						'relative'      => array(
							/* translators: %s: Duration. */
							'future' => __( '%s后' ),
							/* translators: %s: Duration. */
							'past'   => __( '%s前' ),
						),
					),
					'formats'  => array(
						/* translators: Time format, see https://www.php.net/manual/datetime.format.php */
						'time'                => get_option( 'time_format', __( 'ag:i' ) ),
						/* translators: Date format, see https://www.php.net/manual/datetime.format.php */
						'date'                => get_option( 'date_format', __( 'Y年n月j日' ) ),
						/* translators: Date/Time format, see https://www.php.net/manual/datetime.format.php */
						'datetime'            => __( 'Y年n月j日ag:i' ),
						/* translators: Abbreviated date/time format, see https://www.php.net/manual/datetime.format.php */
						'datetimeAbbreviated' => __( 'Y年n月j日H:i' ),
					),
					'timezone' => array(
						'offset' => get_option( 'gmt_offset', 0 ),
						'string' => $timezone_string,
						'abbr'   => $timezone_abbr,
					),
				)
			)
		),
		'after'
	);

	// Loading the old editor and its config to ensure the classic block works as expected.
	$scripts->add_inline_script(
		'editor',
		'window.gc.oldEditor = window.gc.editor;',
		'after'
	);

	/*
	 * gc-editor module is exposed as window.gc.editor.
	 * Problem: there is quite some code expecting window.gc.oldEditor object available under window.gc.editor.
	 * Solution: fuse the two objects together to maintain backward compatibility.
	 * For more context, see https://github.com/GeChiUI/gutenberg/issues/33203.
	 */
	$scripts->add_inline_script(
		'gc-editor',
		'Object.assign( window.gc.editor, window.gc.oldEditor );',
		'after'
	);
}

/**
 * Adds inline scripts required for the TinyMCE in the block editor.
 *
 * These TinyMCE init settings are used to extend and override the default settings
 * from `_GC_Editors::default_settings()` for the Classic block.
 *
 *
 *
 * @global GC_Scripts $gc_scripts
 */
function gc_tinymce_inline_scripts() {
	global $gc_scripts;

	/** This filter is documented in gc-includes/class-gc-editor.php */
	$editor_settings = apply_filters( 'gc_editor_settings', array( 'tinymce' => true ), 'classic-block' );

	$tinymce_plugins = array(
		'charmap',
		'colorpicker',
		'hr',
		'lists',
		'media',
		'paste',
		'tabfocus',
		'textcolor',
		'fullscreen',
		'gechiui',
		'gcautoresize',
		'gceditimage',
		'gcemoji',
		'gcgallery',
		'gclink',
		'gcdialogs',
		'gctextpattern',
		'gcview',
	);

	/** This filter is documented in gc-includes/class-gc-editor.php */
	$tinymce_plugins = apply_filters( 'tiny_mce_plugins', $tinymce_plugins, 'classic-block' );
	$tinymce_plugins = array_unique( $tinymce_plugins );

	$disable_captions = false;
	// Runs after `tiny_mce_plugins` but before `mce_buttons`.
	/** This filter is documented in gc-admin/includes/media.php */
	if ( apply_filters( 'disable_captions', '' ) ) {
		$disable_captions = true;
	}

	$toolbar1 = array(
		'formatselect',
		'bold',
		'italic',
		'bullist',
		'numlist',
		'blockquote',
		'alignleft',
		'aligncenter',
		'alignright',
		'link',
		'unlink',
		'gc_more',
		'spellchecker',
		'gc_add_media',
		'gc_adv',
	);

	/** This filter is documented in gc-includes/class-gc-editor.php */
	$toolbar1 = apply_filters( 'mce_buttons', $toolbar1, 'classic-block' );

	$toolbar2 = array(
		'strikethrough',
		'hr',
		'forecolor',
		'pastetext',
		'removeformat',
		'charmap',
		'outdent',
		'indent',
		'undo',
		'redo',
		'gc_help',
	);

	/** This filter is documented in gc-includes/class-gc-editor.php */
	$toolbar2 = apply_filters( 'mce_buttons_2', $toolbar2, 'classic-block' );
	/** This filter is documented in gc-includes/class-gc-editor.php */
	$toolbar3 = apply_filters( 'mce_buttons_3', array(), 'classic-block' );
	/** This filter is documented in gc-includes/class-gc-editor.php */
	$toolbar4 = apply_filters( 'mce_buttons_4', array(), 'classic-block' );
	/** This filter is documented in gc-includes/class-gc-editor.php */
	$external_plugins = apply_filters( 'mce_external_plugins', array(), 'classic-block' );

	$tinymce_settings = array(
		'plugins'              => implode( ',', $tinymce_plugins ),
		'toolbar1'             => implode( ',', $toolbar1 ),
		'toolbar2'             => implode( ',', $toolbar2 ),
		'toolbar3'             => implode( ',', $toolbar3 ),
		'toolbar4'             => implode( ',', $toolbar4 ),
		'external_plugins'     => gc_json_encode( $external_plugins ),
		'classic_block_editor' => true,
	);

	if ( $disable_captions ) {
		$tinymce_settings['gceditimage_disable_captions'] = true;
	}

	if ( ! empty( $editor_settings['tinymce'] ) && is_array( $editor_settings['tinymce'] ) ) {
		array_merge( $tinymce_settings, $editor_settings['tinymce'] );
	}

	/** This filter is documented in gc-includes/class-gc-editor.php */
	$tinymce_settings = apply_filters( 'tiny_mce_before_init', $tinymce_settings, 'classic-block' );

	// Do "by hand" translation from PHP array to js object.
	// Prevents breakage in some custom settings.
	$init_obj = '';
	foreach ( $tinymce_settings as $key => $value ) {
		if ( is_bool( $value ) ) {
			$val       = $value ? 'true' : 'false';
			$init_obj .= $key . ':' . $val . ',';
			continue;
		} elseif ( ! empty( $value ) && is_string( $value ) && (
			( '{' === $value[0] && '}' === $value[ strlen( $value ) - 1 ] ) ||
			( '[' === $value[0] && ']' === $value[ strlen( $value ) - 1 ] ) ||
			preg_match( '/^\(?function ?\(/', $value ) ) ) {
			$init_obj .= $key . ':' . $value . ',';
			continue;
		}
		$init_obj .= $key . ':"' . $value . '",';
	}

	$init_obj = '{' . trim( $init_obj, ' ,' ) . '}';

	$script = 'window.gcEditorL10n = {
		tinymce: {
			baseURL: ' . gc_json_encode( '/vendors/tinymce' ) . ',
			suffix: ' . ( SCRIPT_DEBUG ? '""' : '".min"' ) . ',
			settings: ' . $init_obj . ',
		}
	}';

	$gc_scripts->add_inline_script( 'gc-block-library', $script, 'before' );
}

/**
 * Registers all the GeChiUI packages scripts.
 *
 *
 *
 * @param GC_Scripts $scripts GC_Scripts object.
 */
function gc_default_packages( $scripts ) {
	gc_default_packages_vendor( $scripts );
	gc_register_tinymce_scripts( $scripts );
	gc_default_packages_scripts( $scripts );

	if ( did_action( 'init' ) ) {
		gc_default_packages_inline_scripts( $scripts );
	}
}

/**
 * Returns the suffix that can be used for the scripts.
 *
 * There are two suffix types, the normal one and the dev suffix.
 *
 *
 *
 * @param string $type The type of suffix to retrieve.
 * @return string The script suffix.
 */
function gc_scripts_get_suffix( $type = '' ) {
	static $suffixes;

	if ( null === $suffixes ) {
		// Include an unmodified $gc_version.
		require ABSPATH . GCINC . '/version.php';

		$develop_src = false !== strpos( $gc_version, '-src' );

		if ( ! defined( 'SCRIPT_DEBUG' ) ) {
			define( 'SCRIPT_DEBUG', $develop_src );
		}
		$suffix     = SCRIPT_DEBUG ? '' : '.min';
		$dev_suffix = $develop_src ? '' : '.min';

		$suffixes = array(
			'suffix'     => $suffix,
			'dev_suffix' => $dev_suffix,
		);
	}

	if ( 'dev' === $type ) {
		return $suffixes['dev_suffix'];
	}

	return $suffixes['suffix'];
}

/**
 * Register all GeChiUI scripts.
 *
 * Localizes some of them.
 * args order: `$scripts->add( 'handle', 'url', 'dependencies', 'query-string', 1 );`
 * when last arg === 1 queues the script for the footer
 *
 *
 *
 * @param GC_Scripts $scripts GC_Scripts object.
 */
function gc_default_scripts( $scripts ) {
	$suffix     = gc_scripts_get_suffix();
	$dev_suffix = gc_scripts_get_suffix( 'dev' );
	$guessurl   = rtrim( assets_url(), '/' );

	if ( ! $guessurl ) {
		$guessed_url = true;
		$guessurl    = gc_guess_url();
	}

	$scripts->base_url        = $guessurl;
	$scripts->content_url     = defined( 'GC_CONTENT_URL' ) ? GC_CONTENT_URL : '';
	$scripts->default_version = get_bloginfo( 'version' );
	//压缩的文件夹，此文件夹开头的js文件压缩到load-scripts.php文件中
	if ( !defined( 'GC_CDN_URL' ) ){
		$scripts->default_dirs    = array( '/js/', '/vendors/' );
	}

	$scripts->add( 'utils', "/js/utils$suffix.js" );
	did_action( 'init' ) && $scripts->localize(
		'utils',
		'userSettings',
		array(
			'url'    => (string) SITECOOKIEPATH,
			'uid'    => (string) get_current_user_id(),
			'time'   => (string) time(),
			'secure' => (string) ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) ),
		)
	);

	// 后台主框架脚本
    $scripts->add( 'gc-bootstrap', "/vendors/bootstrap/bootstrap.bundle$suffix.js", array(), '5.1.3' );
    //自定义滚动条，一个轻量级滚动条插件
    $scripts->add( 'gc-perfect-scrollbar', "/vendors/perfect-scrollbar/perfect-scrollbar$suffix.js", array(), '1.5.5' );
    $scripts->add( 'jquery-validation', "/vendors/jquery-validation/jquery.validate$suffix.js", array( 'jquery'), '1.19.3' );
    $scripts->add( 'app', "/js/app$suffix.js", array( 'jquery', 'gc-bootstrap', 'gc-perfect-scrollbar'), false, 1 );
    
    //在登录页面组中引用 gc_enqueue_script( 'pages-login' );
    $scripts->add( 'pages-login-sms', "/js/login/login_sms$suffix.js", array( 'jquery-validation'), false, 1 );
    $scripts->add( 'pages-login-register-sms', "/js/login/register_sms$suffix.js", array( 'jquery-validation'), false, 1 );

	$scripts->add( 'common', "/js/common$suffix.js", array( 'jquery', 'hoverIntent', 'utils', 'app' ), false, 1 );
	$scripts->set_translations( 'common' );

	$scripts->add( 'gc-sanitize', "/js/gc-sanitize$suffix.js", array(), false, 1 );

	$scripts->add( 'sack', "/vendors/tw-sack$suffix.js", array(), '1.6.1', 1 );

	$scripts->add( 'quicktags', "/js/quicktags$suffix.js", array(), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'quicktags',
		'quicktagsL10n',
		array(
			'closeAllOpenTags'      => __( '关闭所有打开的标签' ),
			'closeTags'             => __( '关闭标签' ),
			'enterURL'              => __( '输入URL' ),
			'enterImageURL'         => __( '输入图片URL' ),
			'enterImageDescription' => __( '输入图片的描述' ),
			'textdirection'         => __( '文本方向' ),
			'toggleTextdirection'   => __( '切换编辑器文本书写方向' ),
			'dfw'                   => __( '免打扰写作模式' ),
			'strong'                => __( '粗体' ),
			'strongClose'           => __( '关闭粗体标签' ),
			'em'                    => __( '斜体' ),
			'emClose'               => __( '关闭斜体标签' ),
			'link'                  => __( '插入链接' ),
			'blockquote'            => __( '段落引用' ),
			'blockquoteClose'       => __( '关闭段落引用标签' ),
			'del'                   => __( '删除的文字（删除线）' ),
			'delClose'              => __( '关闭删除线标签' ),
			'ins'                   => __( '插入的文字' ),
			'insClose'              => __( '关闭插入的文字标签' ),
			'image'                 => __( '插入图片' ),
			'ul'                    => __( '项目符号列表' ),
			'ulClose'               => __( '关闭项目符号列表标签' ),
			'ol'                    => __( '编号列表' ),
			'olClose'               => __( '关闭编号列表标签' ),
			'li'                    => __( '列表项目' ),
			'liClose'               => __( '关闭列表项目标签' ),
			'code'                  => __( '代码' ),
			'codeClose'             => __( '关闭代码标签' ),
			'more'                  => __( '插入“More”标签' ),
		)
	);

	$scripts->add( 'colorpicker', "/vendors/colorpicker$suffix.js", array( 'prototype' ), '3517m' );

	$scripts->add( 'editor', "/js/editor$suffix.js", array( 'utils', 'jquery' ), false, 1 );

	$scripts->add( 'clipboard', "/vendors/clipboard$suffix.js", array(), false, 1 );

	$scripts->add( 'gc-ajax-response', "/js/gc-ajax-response$suffix.js", array( 'jquery' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'gc-ajax-response',
		'gcAjax',
		array(
			'noPerm' => __( '抱歉，您不能这么做。' ),
			'broken' => __( '出现了问题。' ),
		)
	);

	$scripts->add( 'gc-api-request', "/js/api-request$suffix.js", array( 'jquery' ), false, 1 );
	// `gcApiSettings` is also used by `gc-api`, which depends on this script.
	did_action( 'init' ) && $scripts->localize(
		'gc-api-request',
		'gcApiSettings',
		array(
			'root'          => esc_url_raw( get_rest_url() ),
			'nonce'         => gc_installing() ? '' : gc_create_nonce( 'gc_rest' ),
			'versionString' => 'gc/v2/',
		)
	);

	$scripts->add( 'gc-pointer', "/js/gc-pointer$suffix.js", array( 'jquery-ui-core' ), false, 1 );
	$scripts->set_translations( 'gc-pointer' );

	$scripts->add( 'autosave', "/js/autosave$suffix.js", array( 'heartbeat' ), false, 1 );

	$scripts->add( 'heartbeat', "/js/heartbeat$suffix.js", array( 'jquery', 'gc-hooks' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'heartbeat',
		'heartbeatSettings',
		/**
		 * Filters the Heartbeat settings.
		 *
		 *
		 * @param array $settings Heartbeat settings array.
		 */
		apply_filters( 'heartbeat_settings', array() )
	);

	$scripts->add( 'gc-auth-check', "/js/gc-auth-check$suffix.js", array( 'heartbeat' ), false, 1 );
	$scripts->set_translations( 'gc-auth-check' );

	$scripts->add( 'gc-lists', "/js/gc-lists$suffix.js", array( 'gc-ajax-response', 'jquery-color' ), false, 1 );

	// GeChiUI 外部资源
	$scripts->add( 'prototype', "/vendors/prototype/prototype.min.js", array(), '1.7.1' );
	$scripts->add( 'scriptaculous-root', "/vendors/scriptaculous/scriptaculous.min.js", array( 'prototype' ), '1.9.0' );
	$scripts->add( 'scriptaculous-builder', "/vendors/scriptaculous/builder.min.js", array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous-dragdrop', "/vendors/scriptaculous/dragdrop.min.js", array( 'scriptaculous-builder', 'scriptaculous-effects' ), '1.9.0' );
	$scripts->add( 'scriptaculous-effects', "/vendors/scriptaculous/effects.min.js", array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous-slider', "/vendors/scriptaculous/slider.min.js", array( 'scriptaculous-effects' ), '1.9.0' );
	$scripts->add( 'scriptaculous-sound', "/vendors/scriptaculous/sound.min.js", array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous-controls', "/vendors/scriptaculous/controls.min.js", array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous', false, array( 'scriptaculous-dragdrop', 'scriptaculous-slider', 'scriptaculous-controls' ) );

	// Not used in core, replaced by Jcrop.js.
	$scripts->add( 'cropper', '/vendors/crop/cropper.js', array( 'scriptaculous-dragdrop' ) );

	// jQuery.
	// The unminified jquery.js and jquery-migrate.js are included to facilitate debugging.
	$scripts->add( 'jquery', false, array( 'jquery-core', 'jquery-migrate' ), '3.6.0' );
	$scripts->add( 'jquery-core', "/vendors/jquery/jquery$suffix.js", array(), '3.6.0' );
	$scripts->add( 'jquery-migrate', "/vendors/jquery/jquery-migrate$suffix.js", array(), '3.3.2' ); //用于检查jquery升级带来的不兼容，输出警告。直至解决兼容性侯可删除

	// Full jQuery UI.
	// The build process in 1.12.1 has changed significantly.
	// In order to keep backwards compatibility, and to keep the optimized loading,
	// the source files were flattened and included with some modifications for AMD loading.
	// A notable change is that 'jquery-ui-core' now contains 'jquery-ui-position' and 'jquery-ui-widget'.
	$scripts->add( 'jquery-ui-core', "/vendors/jquery/ui/core$suffix.js", array( 'jquery' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-core', "/vendors/jquery/ui/effect$suffix.js", array( 'jquery' ), '1.13.1', 1 );

	$scripts->add( 'jquery-effects-blind', "/vendors/jquery/ui/effect-blind$suffix.js", array( 'jquery-effects-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-bounce', "/vendors/jquery/ui/effect-bounce$suffix.js", array( 'jquery-effects-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-clip', "/vendors/jquery/ui/effect-clip$suffix.js", array( 'jquery-effects-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-drop', "/vendors/jquery/ui/effect-drop$suffix.js", array( 'jquery-effects-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-explode', "/vendors/jquery/ui/effect-explode$suffix.js", array( 'jquery-effects-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-fade', "/vendors/jquery/ui/effect-fade$suffix.js", array( 'jquery-effects-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-fold', "/vendors/jquery/ui/effect-fold$suffix.js", array( 'jquery-effects-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-highlight', "/vendors/jquery/ui/effect-highlight$suffix.js", array( 'jquery-effects-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-puff', "/vendors/jquery/ui/effect-puff$suffix.js", array( 'jquery-effects-core', 'jquery-effects-scale' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-pulsate', "/vendors/jquery/ui/effect-pulsate$suffix.js", array( 'jquery-effects-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-scale', "/vendors/jquery/ui/effect-scale$suffix.js", array( 'jquery-effects-core', 'jquery-effects-size' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-shake', "/vendors/jquery/ui/effect-shake$suffix.js", array( 'jquery-effects-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-size', "/vendors/jquery/ui/effect-size$suffix.js", array( 'jquery-effects-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-slide', "/vendors/jquery/ui/effect-slide$suffix.js", array( 'jquery-effects-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-effects-transfer', "/vendors/jquery/ui/effect-transfer$suffix.js", array( 'jquery-effects-core' ), '1.13.1', 1 );

	// Widgets
	$scripts->add( 'jquery-ui-accordion', "/vendors/jquery/ui/accordion$suffix.js", array( 'jquery-ui-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-autocomplete', "/vendors/jquery/ui/autocomplete$suffix.js", array( 'jquery-ui-menu', 'gc-a11y' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-button', "/vendors/jquery/ui/button$suffix.js", array( 'jquery-ui-core', 'jquery-ui-controlgroup', 'jquery-ui-checkboxradio' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-datepicker', "/vendors/jquery/ui/datepicker$suffix.js", array( 'jquery-ui-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-dialog', "/vendors/jquery/ui/dialog$suffix.js", array( 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-button' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-menu', "/vendors/jquery/ui/menu$suffix.js", array( 'jquery-ui-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-mouse', "/vendors/jquery/ui/mouse$suffix.js", array( 'jquery-ui-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-progressbar', "/vendors/jquery/ui/progressbar$suffix.js", array( 'jquery-ui-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-selectmenu', "/vendors/jquery/ui/selectmenu$suffix.js", array( 'jquery-ui-menu' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-slider', "/vendors/jquery/ui/slider$suffix.js", array( 'jquery-ui-mouse' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-spinner', "/vendors/jquery/ui/spinner$suffix.js", array( 'jquery-ui-button' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-tabs', "/vendors/jquery/ui/tabs$suffix.js", array( 'jquery-ui-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-tooltip', "/vendors/jquery/ui/tooltip$suffix.js", array( 'jquery-ui-core' ), '1.13.1', 1 );

	// New in 1.12.1
	$scripts->add( 'jquery-ui-checkboxradio', "/vendors/jquery/ui/checkboxradio$suffix.js", array( 'jquery-ui-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-controlgroup', "/vendors/jquery/ui/controlgroup$suffix.js", array( 'jquery-ui-core' ), '1.13.1', 1 );

	// Interactions
	$scripts->add( 'jquery-ui-draggable', "/vendors/jquery/ui/draggable$suffix.js", array( 'jquery-ui-mouse' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-droppable', "/vendors/jquery/ui/droppable$suffix.js", array( 'jquery-ui-draggable' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-resizable', "/vendors/jquery/ui/resizable$suffix.js", array( 'jquery-ui-mouse' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-selectable', "/vendors/jquery/ui/selectable$suffix.js", array( 'jquery-ui-mouse' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-sortable', "/vendors/jquery/ui/sortable$suffix.js", array( 'jquery-ui-mouse' ), '1.13.1', 1 );

	// As of 1.12.1 `jquery-ui-position` and `jquery-ui-widget` are part of `jquery-ui-core`.
	// Listed here for back-compat.
	$scripts->add( 'jquery-ui-position', false, array( 'jquery-ui-core' ), '1.13.1', 1 );
	$scripts->add( 'jquery-ui-widget', false, array( 'jquery-ui-core' ), '1.13.1', 1 );

	// Strings for 'jquery-ui-autocomplete' live region messages.
	did_action( 'init' ) && $scripts->localize(
		'jquery-ui-autocomplete',
		'uiAutocompleteL10n',
		array(
			'noResults'    => __( '未找到结果。' ),
			/* translators: Number of results found when using jQuery UI Autocomplete. */
			'oneResult'    => __( '找到1个结果。使用上下方向键来导航。' ),
			/* translators: %d: Number of results found when using jQuery UI Autocomplete. */
			'manyResults'  => __( '找到%d个结果。使用上下方向键来导航。' ),
			'itemSelected' => __( '已选择项目。' ),
		)
	);

	// Deprecated, not used in core, most functionality is included in jQuery 1.3.
	$scripts->add( 'jquery-form', "/vendors/jquery/jquery.form$suffix.js", array( 'jquery' ), '4.3.0', 1 );

	// jQuery plugins.
	$scripts->add( 'jquery-color', '/vendors/jquery/jquery.color.min.js', array( 'jquery' ), '2.2.0', 1 );
	$scripts->add( 'schedule', '/vendors/jquery/jquery.schedule.js', array( 'jquery' ), '20m', 1 );
	$scripts->add( 'jquery-query', '/vendors/jquery/jquery.query.js', array( 'jquery' ), '2.2.3', 1 );
	$scripts->add( 'jquery-serialize-object', '/vendors/jquery/jquery.serialize-object.js', array( 'jquery' ), '0.2-gc', 1 );
	$scripts->add( 'jquery-hotkeys', "/vendors/jquery/jquery.hotkeys$suffix.js", array( 'jquery' ), '0.0.2m', 1 );
	$scripts->add( 'jquery-table-hotkeys', "/vendors/jquery/jquery.table-hotkeys$suffix.js", array( 'jquery', 'jquery-hotkeys' ), false, 1 );
	$scripts->add( 'jquery-touch-punch', '/vendors/jquery/jquery.ui.touch-punch.js', array( 'jquery-ui-core', 'jquery-ui-mouse' ), '0.2.2', 1 );

	// Not used any more, registered for backward compatibility.
	$scripts->add( 'suggest', "/vendors/jquery/suggest$suffix.js", array( 'jquery' ), '1.1-20110113', 1 );

	// Masonry v2 depended on jQuery. v3 does not. The older jquery-masonry handle is a shiv.
	// It sets jQuery as a dependency, as the theme may have been implicitly loading it this way.
	$scripts->add( 'imagesloaded', "/vendors/imagesloaded$suffix.js", array(), '4.1.4', 1 );
	$scripts->add( 'masonry', "/vendors/masonry$suffix.js", array( 'imagesloaded' ), '4.2.2', 1 );
	$scripts->add( 'jquery-masonry', '/vendors/jquery/jquery.masonry.min.js', array( 'jquery', 'masonry' ), '3.1.2b', 1 );

	$scripts->add( 'thickbox', '/vendors/thickbox/thickbox.js', array( 'jquery' ), '3.1-20121105', 1 );
	did_action( 'init' ) && $scripts->localize(
		'thickbox',
		'thickboxL10n',
		array(
			'next'             => __( '下一页 &gt;' ),
			'prev'             => __( '&lt; 上一页' ),
			'image'            => __( '图片' ),
			'of'               => __( '/' ),
			'close'            => __( '关闭' ),
			'noiframes'        => __( '这个功能需要iframe的支持。您可能禁止了iframe的显示，或您的浏览器不支持此功能。' ),
			'loadingAnimation' => assets_url( '/vendors/thickbox/loadingAnimation.gif' ),
		)
	);

	$scripts->add( 'jcrop', '/vendors/jcrop/jquery.Jcrop.min.js', array( 'jquery' ), '0.9.15' );

	$scripts->add( 'swfobject', "/vendors/swfobject$suffix.js", array(), '2.2-20120417' );

	// Error messages for Plupload.
	$uploader_l10n = array(
		'queue_limit_exceeded'      => __( '您向队列中添加的文件过多。' ),
		/* translators: %s: File name. */
		'file_exceeds_size_limit'   => __( '%s超过了站点的最大上传限制。' ),
		'zero_byte_file'            => __( '文件为空，请选择其他文件。' ),
		'invalid_filetype'          => __( '抱歉，您无权上传此文件类型。' ),
		'not_an_image'              => __( '该文件不是图片，请选择其他文件。' ),
		'image_memory_exceeded'     => __( '达到内存限制，请使用小一些的文件。' ),
		'image_dimensions_exceeded' => __( '该文件超过了最大大小，请选择其他文件。' ),
		'default_error'             => __( '上传时发生了错误。请稍后再试。' ),
		'missing_upload_url'        => __( '配置有误。请联系您的服务器管理员。' ),
		'upload_limit_exceeded'     => __( '您只能上传一个文件。' ),
		'http_error'                => __( '从服务器收到预料之外的响应。此文件可能已被成功上传。请检查媒体库或刷新本页。' ),
		'http_error_image'          => __( '服务器无法处理图像。如果服务器繁忙或没有足够的资源来完成任务，就会发生这种情况。上传较小的图像可能会有所帮助。建议的最大尺寸为 2560 像素。' ),
		'upload_failed'             => __( '上传失败。' ),
		/* translators: 1: Opening link tag, 2: Closing link tag. */
		'big_upload_failed'         => __( '请尝试使用%1$s标准的浏览器上传工具%2$s来上传这个文件。' ),
		/* translators: %s: File name. */
		'big_upload_queued'         => __( '%s超出了您浏览器对高级多文件上传工具所做的大小限制。' ),
		'io_error'                  => __( 'IO错误。' ),
		'security_error'            => __( '安全错误。' ),
		'file_cancelled'            => __( '文件已取消。' ),
		'upload_stopped'            => __( '上传停止。' ),
		'dismiss'                   => __( '不再显示' ),
		'crunching'                 => __( '处理中&hellip;' ),
		'deleted'                   => __( '移动至回收站。' ),
		/* translators: %s: File name. */
		'error_uploading'           => __( '“%s”上传失败。' ),
		'unsupported_image'         => __( '此图片无法在网页浏览器中显示。 为了达到最佳效果，请在上传前将其转换为JPEG格式。' ),
		'noneditable_image'         => __( 'web 服务器无法处理该图片，请在上传前将其转换为JPEG或PNG 格式。' ),
		'file_url_copied'           => __( '文件URL已复制至剪贴板' ),
	);

	$scripts->add( 'moxiejs', "/vendors/plupload/moxie$suffix.js", array(), '1.3.5' );
	$scripts->add( 'plupload', "/vendors/plupload/plupload$suffix.js", array( 'moxiejs' ), '2.1.9' );
	// Back compat handles:
	foreach ( array( 'all', 'html5', 'flash', 'silverlight', 'html4' ) as $handle ) {
		$scripts->add( "plupload-$handle", false, array( 'plupload' ), '2.1.1' );
	}

	$scripts->add( 'plupload-handlers', "/vendors/plupload/handlers$suffix.js", array( 'clipboard', 'jquery', 'plupload', 'underscore', 'gc-a11y', 'gc-i18n' ) );
	did_action( 'init' ) && $scripts->localize( 'plupload-handlers', 'pluploadL10n', $uploader_l10n );

	$scripts->add( 'gc-plupload', "/vendors/plupload/gc-plupload$suffix.js", array( 'plupload', 'jquery', 'json2', 'media-models' ), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'gc-plupload', 'pluploadL10n', $uploader_l10n );

	// Keep 'swfupload' for back-compat.
	$scripts->add( 'swfupload', '/vendors/swfupload/swfupload.js', array(), '2201-20110113' );
	$scripts->add( 'swfupload-all', false, array( 'swfupload' ), '2201' );
	$scripts->add( 'swfupload-handlers', "/vendors/swfupload/handlers$suffix.js", array( 'swfupload-all', 'jquery' ), '2201-20110524' );
	did_action( 'init' ) && $scripts->localize( 'swfupload-handlers', 'swfuploadL10n', $uploader_l10n );

	$scripts->add( 'comment-reply', "/js/comment-reply$suffix.js", array(), false, 1 );

	$scripts->add( 'json2', "/vendors/json2$suffix.js", array(), '2015-05-03' );
	did_action( 'init' ) && $scripts->add_data( 'json2', 'conditional', 'lt IE 8' );

	$scripts->add( 'underscore', "/vendors/underscore$dev_suffix.js", array(), '1.13.1', 1 );
	$scripts->add( 'backbone', "/vendors/backbone$dev_suffix.js", array( 'underscore', 'jquery' ), '1.4.0', 1 );

	$scripts->add( 'gc-util', "/js/gc-util$suffix.js", array( 'underscore', 'jquery' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'gc-util',
		'_gcUtilSettings',
		array(
			'ajax' => array(
				'url' => admin_url( 'admin-ajax.php', 'relative' ),
			),
		)
	);

	$scripts->add( 'gc-backbone', "/js/gc-backbone$suffix.js", array( 'backbone', 'gc-util' ), false, 1 );

	$scripts->add( 'revisions', "/js/revisions$suffix.js", array( 'gc-backbone', 'jquery-ui-slider', 'hoverIntent' ), false, 1 );

	$scripts->add( 'imgareaselect', "/vendors/imgareaselect/jquery.imgareaselect$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'mediaelement', false, array( 'jquery', 'mediaelement-core', 'mediaelement-migrate' ), '4.2.16', 1 );
	$scripts->add( 'mediaelement-core', "/vendors/mediaelement/mediaelement-and-player$suffix.js", array(), '4.2.16', 1 );
	$scripts->add( 'mediaelement-migrate', "/vendors/mediaelement/mediaelement-migrate$suffix.js", array(), false, 1 );

	did_action( 'init' ) && $scripts->add_inline_script(
		'mediaelement-core',
		sprintf(
			'var mejsL10n = %s;',
			gc_json_encode(
				array(
					'language' => strtolower( strtok( determine_locale(), '_-' ) ),
					'strings'  => array(
						'mejs.download-file'       => __( '下载文件' ),
						'mejs.install-flash'       => __( '您正在使用的浏览器未安装或启用Flash播放器，请启用您的Flash播放器插件，或从 https://get.adobe.com/flashplayer/ 下载最新版。' ),
						'mejs.fullscreen'          => __( '全屏' ),
						'mejs.play'                => __( '播放' ),
						'mejs.pause'               => __( '暂停' ),
						'mejs.time-slider'         => __( '时间轴' ),
						'mejs.time-help-text'      => __( '使用左/右箭头键来前进一秒，上/下箭头键来前进十秒。' ),
						'mejs.live-broadcast'      => __( '现场直播' ),
						'mejs.volume-help-text'    => __( '使用上/下箭头键来增高或降低音量。' ),
						'mejs.unmute'              => __( '取消静音' ),
						'mejs.mute'                => __( '静音' ),
						'mejs.volume-slider'       => __( '音量' ),
						'mejs.video-player'        => __( '视频播放器' ),
						'mejs.audio-player'        => __( '音频播放器' ),
						'mejs.captions-subtitles'  => __( '说明文字或字幕' ),
						'mejs.captions-chapters'   => __( '章节' ),
						'mejs.none'                => __( '无' ),
						'mejs.afrikaans'           => __( '南非荷兰语' ),
						'mejs.albanian'            => __( '阿尔巴尼亚语' ),
						'mejs.arabic'              => __( '阿拉伯语' ),
						'mejs.belarusian'          => __( '白俄罗斯语' ),
						'mejs.bulgarian'           => __( '保加利亚语' ),
						'mejs.catalan'             => __( '加泰罗尼亚语' ),
						'mejs.chinese'             => __( '中文' ),
						'mejs.chinese-simplified'  => __( '中文（简体）' ),
						'mejs.chinese-traditional' => __( '中文(（繁体）' ),
						'mejs.croatian'            => __( '克罗地亚语' ),
						'mejs.czech'               => __( '捷克语' ),
						'mejs.danish'              => __( '丹麦语' ),
						'mejs.dutch'               => __( '荷兰语' ),
						'mejs.english'             => __( '英语' ),
						'mejs.estonian'            => __( '爱沙尼亚语' ),
						'mejs.filipino'            => __( '菲律宾语' ),
						'mejs.finnish'             => __( '芬兰语' ),
						'mejs.french'              => __( '法语' ),
						'mejs.galician'            => __( '加利西亚语' ),
						'mejs.german'              => __( '德语' ),
						'mejs.greek'               => __( '希腊语' ),
						'mejs.haitian-creole'      => __( '海地克里奥尔语' ),
						'mejs.hebrew'              => __( '希伯来语' ),
						'mejs.hindi'               => __( '印地语' ),
						'mejs.hungarian'           => __( '匈牙利语' ),
						'mejs.icelandic'           => __( '冰岛语' ),
						'mejs.indonesian'          => __( '印度尼西亚语' ),
						'mejs.irish'               => __( '爱尔兰语' ),
						'mejs.italian'             => __( '意大利语' ),
						'mejs.japanese'            => __( '日语' ),
						'mejs.korean'              => __( '韩语' ),
						'mejs.latvian'             => __( '拉脱维亚语' ),
						'mejs.lithuanian'          => __( '立陶宛语' ),
						'mejs.macedonian'          => __( '马其顿语' ),
						'mejs.malay'               => __( '马来语' ),
						'mejs.maltese'             => __( '马耳他语' ),
						'mejs.norwegian'           => __( '挪威语' ),
						'mejs.persian'             => __( '波斯语' ),
						'mejs.polish'              => __( '波兰语' ),
						'mejs.portuguese'          => __( '葡萄牙语' ),
						'mejs.romanian'            => __( '罗马尼亚语' ),
						'mejs.russian'             => __( '俄语' ),
						'mejs.serbian'             => __( '塞尔维亚语' ),
						'mejs.slovak'              => __( '斯洛伐克语' ),
						'mejs.slovenian'           => __( '斯洛文尼亚语' ),
						'mejs.spanish'             => __( '西班牙语' ),
						'mejs.swahili'             => __( '斯瓦希里语' ),
						'mejs.swedish'             => __( '瑞典语' ),
						'mejs.tagalog'             => __( '他加禄语' ),
						'mejs.thai'                => __( '泰语' ),
						'mejs.turkish'             => __( '土耳其语' ),
						'mejs.ukrainian'           => __( '乌克兰语' ),
						'mejs.vietnamese'          => __( '越南语' ),
						'mejs.welsh'               => __( '威尔士语' ),
						'mejs.yiddish'             => __( '意第绪语' ),
					),
				)
			)
		),
		'before'
	);

	$scripts->add( 'mediaelement-vimeo', '/vendors/mediaelement/renderers/vimeo.min.js', array( 'mediaelement' ), '4.2.16', 1 );
	$scripts->add( 'gc-mediaelement', "/vendors/mediaelement/gc-mediaelement$suffix.js", array( 'mediaelement' ), false, 1 );
	$mejs_settings = array(
		'pluginPath'  => assets_url( '/js/mediaelement/', 'relative' ),
		'classPrefix' => 'mejs-',
		'stretching'  => 'responsive',
	);
	did_action( 'init' ) && $scripts->localize(
		'mediaelement',
		'_gcmejsSettings',
		/**
		 * Filters the MediaElement configuration settings.
		 *
		 *
		 * @param array $mejs_settings MediaElement settings array.
		 */
		apply_filters( 'mejs_settings', $mejs_settings )
	);

	$scripts->add( 'gc-codemirror', '/vendors/codemirror/codemirror.min.js', array(), '5.29.1-alpha-ee20357' );
	$scripts->add( 'csslint', '/vendors/codemirror/csslint.js', array(), '1.0.5' );
	$scripts->add( 'esprima', '/vendors/codemirror/esprima.js', array(), '4.0.0' );
	$scripts->add( 'jshint', '/vendors/codemirror/fakejshint.js', array( 'esprima' ), '2.9.5' );
	$scripts->add( 'jsonlint', '/vendors/codemirror/jsonlint.js', array(), '1.6.2' );
	$scripts->add( 'htmlhint', '/vendors/codemirror/htmlhint.js', array(), '0.9.14-xgc' );
	$scripts->add( 'htmlhint-kses', '/vendors/codemirror/htmlhint-kses.js', array( 'htmlhint' ) );
	$scripts->add( 'code-editor', "/js/code-editor$suffix.js", array( 'jquery', 'gc-codemirror', 'underscore' ) );
	$scripts->add( 'gc-theme-plugin-editor', "/js/theme-plugin-editor$suffix.js", array( 'app', 'common', 'gc-util', 'gc-sanitize', 'jquery', 'jquery-ui-core', 'gc-a11y', 'underscore' ) );
	$scripts->set_translations( 'gc-theme-plugin-editor' );

	$scripts->add( 'gc-playlist', "/vendors/mediaelement/gc-playlist$suffix.js", array( 'gc-util', 'backbone', 'mediaelement' ), false, 1 );

	$scripts->add( 'zxcvbn-async', "/js/zxcvbn-async$suffix.js", array(), '1.0' );
	did_action( 'init' ) && $scripts->localize(
		'zxcvbn-async',
		'_zxcvbnSettings',
		array(
			'src' => assets_url( '/vendors/zxcvbn.min.js' ),
		)
	);

	$scripts->add( 'password-strength-meter', "/js/password-strength-meter$suffix.js", array( 'jquery', 'zxcvbn-async' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'password-strength-meter',
		'pwsL10n',
		array(
			'unknown'  => _x( '密码强度未知', 'password strength' ),
			'short'    => _x( '非常弱', 'password strength' ),
			'bad'      => _x( '弱', 'password strength' ),
			'good'     => _x( '中等', 'password strength' ),
			'strong'   => _x( '强', 'password strength' ),
			'mismatch' => _x( '不匹配', 'password mismatch' ),
		)
	);
	$scripts->set_translations( 'password-strength-meter' );

	$scripts->add( 'appkeys', "/js/appkeys$suffix.js", array( 'jquery', 'gc-util', 'gc-api-request', 'gc-date', 'gc-i18n', 'gc-hooks' ), false, 1 );
	$scripts->set_translations( 'appkeys' );

	$scripts->add( 'auth-app', "/js/auth-app$suffix.js", array( 'jquery', 'gc-api-request', 'gc-i18n', 'gc-hooks' ), false, 1 );
	$scripts->set_translations( 'auth-app' );

	$scripts->add( 'user-profile', "/js/user-profile$suffix.js", array( 'jquery', 'password-strength-meter', 'gc-util' ), false, 1 );
	$scripts->set_translations( 'user-profile' );
	$user_id = isset( $_GET['user_id'] ) ? (int) $_GET['user_id'] : 0;
	did_action( 'init' ) && $scripts->localize(
		'user-profile',
		'userProfileL10n',
		array(
			'user_id' => $user_id,
			'nonce'   => gc_installing() ? '' : gc_create_nonce( 'reset-password-for-' . $user_id ),
		)
	);

	$scripts->add( 'language-chooser', "/js/language-chooser$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'user-suggest', "/js/user-suggest$suffix.js", array( 'jquery-ui-autocomplete' ), false, 1 );

	$scripts->add( 'gclink', "/js/gclink$suffix.js", array( 'jquery', 'gc-a11y' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'gclink',
		'gcLinkL10n',
		array(
			'title'          => __( '插入或编辑链接' ),
			'update'         => __( '更新' ),
			'save'           => __( '添加链接' ),
			'noTitle'        => __( '（无标题）' ),
			'noMatchesFound' => __( '未找到结果。' ),
			'linkSelected'   => __( '链接已选择。' ),
			'linkInserted'   => __( '链接已插入。' ),
			/* translators: Minimum input length in characters to start searching posts in the "插入或编辑链接" modal. */
			'minInputLength' => (int) _x( '3', 'minimum input length for searching post links' ),
		)
	);

	$scripts->add( 'gcdialogs', "/js/gcdialog$suffix.js", array( 'jquery-ui-dialog' ), false, 1 );

	$scripts->add( 'word-count', "/js/word-count$suffix.js", array(), false, 1 );

	$scripts->add( 'media-upload', "/js/media-upload$suffix.js", array( 'thickbox', 'shortcode' ), false, 1 );

	$scripts->add( 'hoverIntent', "/vendors/hoverIntent$suffix.js", array( 'jquery' ), '1.10.2', 1 );

	// JS-only version of hoverintent (no dependencies).
	$scripts->add( 'hoverintent-js', '/vendors/hoverintent-js.min.js', array(), '2.2.1', 1 );

	$scripts->add( 'customize-base', "/js/customize-base$suffix.js", array( 'jquery', 'json2', 'underscore' ), false, 1 );
	$scripts->add( 'customize-loader', "/js/customize-loader$suffix.js", array( 'customize-base' ), false, 1 );
	$scripts->add( 'customize-preview', "/js/customize-preview$suffix.js", array( 'gc-a11y', 'customize-base' ), false, 1 );
	$scripts->add( 'customize-models', '/js/customize-models.js', array( 'underscore', 'backbone' ), false, 1 );
	$scripts->add( 'customize-views', '/js/customize-views.js', array( 'jquery', 'underscore', 'imgareaselect', 'customize-models', 'media-editor', 'media-views' ), false, 1 );
	$scripts->add( 'customize-controls', "/js/customize-controls$suffix.js", array( 'customize-base', 'gc-a11y', 'gc-util', 'jquery-ui-core' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'customize-controls',
		'_gcCustomizeControlsL10n',
		array(
			'activate'                => __( '启用并发布' ),
			'save'                    => __( '保存并发布' ), // @todo Remove as not required.
			'publish'                 => __( '发布' ),
			'published'               => __( '已发布' ),
			'saveDraft'               => __( '保存草稿' ),
			'draftSaved'              => __( '草稿已保存' ),
			'updating'                => __( '正在更新' ),
			'schedule'                => _x( '计划', 'customizer changeset action/button label' ),
			'scheduled'               => _x( '定时发布', 'customizer changeset status' ),
			'invalid'                 => __( '无效' ),
			'saveBeforeShare'         => __( '要分享预览，请先保存您的修改。' ),
			'futureDateError'         => __( '您必须提供一个将来的日期来计划发布。' ),
			'saveAlert'               => __( '离开这个页面，您所做的更改将丢失。' ),
			'saved'                   => __( '已保存' ),
			'cancel'                  => __( '取消' ),
			'close'                   => __( '关闭' ),
			'action'                  => __( '操作' ),
			'discardChanges'          => __( '放弃修改' ),
			'cheatin'                 => __( '出现了问题。' ),
			'notAllowedHeading'       => __( '您需要更高级别的权限。' ),
			'notAllowed'              => __( '抱歉，您不能自定义此站点。' ),
			'previewIframeTitle'      => __( '站点预览' ),
			'loginIframeTitle'        => __( '会话已过期' ),
			'collapseSidebar'         => _x( '隐藏控制区', 'label for hide controls button without length constraints' ),
			'expandSidebar'           => _x( '显示控制栏', 'label for hide controls button without length constraints' ),
			'untitledBlogName'        => __( '未命名' ),
			'unknownRequestFail'      => __( '看来出现了问题。请等待几秒并重试。' ),
			'themeDownloading'        => __( '正在下载您的新主题…' ),
			'themePreviewWait'        => __( '正在设置实时预览，请稍等。' ),
			'revertingChanges'        => __( '回滚未发布的修改…' ),
			'trashConfirm'            => __( '您确定要丢弃未发布的修改吗？' ),
			/* translators: %s: Display name of the user who has taken over the changeset in customizer. */
			'takenOverMessage'        => __( '%s已接管并正在定制。' ),
			/* translators: %s: URL to the Customizer to load the autosaved version. */
			'autosaveNotice'          => __( '有比您正在预览的修改更新的自动保存。<a href="%s">恢复自动保存</a>' ),
			'videoHeaderNotice'       => __( '此主题在此页不支持视频页眉。请访问首页或其他支持视频页眉的页面。' ),
			// Used for overriding the file types allowed in Plupload.
			'allowedFiles'            => __( '允许的文件' ),
			'customCssError'          => array(
				/* translators: %d: Error count. */
				'singular' => _n( '在您保存前必须修正%d个错误。', '在您保存前必须修正%d个错误。', 1 ),
				/* translators: %d: Error count. */
				'plural'   => _n( '在您保存前必须修正%d个错误。', '在您保存前必须修正%d个错误。', 2 ),
				// @todo This is lacking, as some languages have a dedicated dual form. For proper handling of plurals in JS, see #20491.
			),
			'pageOnFrontError'        => __( '主页和文章页必须不同。' ),
			'saveBlockedError'        => array(
				/* translators: %s: Number of invalid settings. */
				'singular' => _n( '由于 %s 个设置无效，无法保存。', '由于 %s 个设置无效，无法保存。', 1 ),
				/* translators: %s: Number of invalid settings. */
				'plural'   => _n( '由于 %s 个设置无效，无法保存。', '由于 %s 个设置无效，无法保存。', 2 ),
				// @todo This is lacking, as some languages have a dedicated dual form. For proper handling of plurals in JS, see #20491.
			),
			'scheduleDescription'     => __( '计划您的定制修改来在将来的日期发布。' ),
			'themePreviewUnavailable' => __( '抱歉，您不能在有计划更改或保存的草稿时预览新主题。请发布您的更改或等待其发布后再预览新主题。' ),
			'themeInstallUnavailable' => sprintf(
				/* translators: %s: URL to Add Themes admin screen. */
				__( '因为您的安装需要SFTP凭据，您不能在此安装新主题。请<a href="%s">在管理界面添加主题</a>。' ),
				esc_url( admin_url( 'theme-install.php' ) )
			),
			'publishSettings'         => __( '发布设置' ),
			'invalidDate'             => __( '无效日期。' ),
			'invalidValue'            => __( '无效值。' ),
			'blockThemeNotification'  => sprintf(
				/* translators: 1: Link to Site Editor documentation on HelpHub, 2: HTML button. */
				__( '万岁！您的主题支持使用块进行完整的站点编辑。 <a href="%1$s">告诉我更多</a>. %2$s' ),
				__( 'https://www.gechiui.com/support/article/site-editor/' ),
				sprintf(
					'<button type="button" data-action="%1$s" class="button switch-to-editor">%2$s</button>',
					esc_url( admin_url( 'site-editor.php' ) ),
					__( '使用网站编辑器' )
				)
			),
		)
	);
	$scripts->add( 'customize-selective-refresh', "/js/customize-selective-refresh$suffix.js", array( 'jquery', 'gc-util', 'customize-preview' ), false, 1 );

	$scripts->add( 'customize-widgets', "/js/customize-widgets$suffix.js", array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-droppable', 'gc-backbone', 'customize-controls' ), false, 1 );
	$scripts->add( 'customize-preview-widgets', "/js/customize-preview-widgets$suffix.js", array( 'jquery', 'gc-util', 'customize-preview', 'customize-selective-refresh' ), false, 1 );

	$scripts->add( 'customize-nav-menus', "/js/customize-nav-menus$suffix.js", array( 'jquery', 'gc-backbone', 'customize-controls', 'accordion', 'nav-menu', 'gc-sanitize' ), false, 1 );
	$scripts->add( 'customize-preview-nav-menus', "/js/customize-preview-nav-menus$suffix.js", array( 'jquery', 'gc-util', 'customize-preview', 'customize-selective-refresh' ), false, 1 );

	$scripts->add( 'gc-custom-header', "/js/gc-custom-header$suffix.js", array( 'gc-a11y' ), false, 1 );

	$scripts->add( 'accordion', "/js/accordion$suffix.js", array( 'jquery' ), false, 1 );

	$scripts->add( 'shortcode', "/js/shortcode$suffix.js", array( 'underscore' ), false, 1 );
	$scripts->add( 'media-models', "/js/media-models$suffix.js", array( 'gc-backbone' ), false, 1 );
	did_action( 'init' ) && $scripts->localize(
		'media-models',
		'_gcMediaModelsL10n',
		array(
			'settings' => array(
				'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
				'post'    => array( 'id' => 0 ),
			),
		)
	);

	$scripts->add( 'gc-embed', "/js/gc-embed$suffix.js", array(), false, 1 );

	// To enqueue media-views or media-editor, call gc_enqueue_media().
	// Both rely on numerous settings, styles, and templates to operate correctly.
	$scripts->add( 'media-views', "/js/media-views$suffix.js", array( 'utils', 'media-models', 'gc-plupload', 'jquery-ui-sortable', 'gc-mediaelement', 'gc-api-request', 'gc-a11y', 'clipboard' ), false, 1 );
	$scripts->set_translations( 'media-views' );

	$scripts->add( 'media-editor', "/js/media-editor$suffix.js", array( 'shortcode', 'media-views' ), false, 1 );
	$scripts->set_translations( 'media-editor' );
	$scripts->add( 'media-audiovideo', "/js/media-audiovideo$suffix.js", array( 'media-editor' ), false, 1 );
	$scripts->add( 'mce-view', "/js/mce-view$suffix.js", array( 'shortcode', 'jquery', 'media-views', 'media-audiovideo' ), false, 1 );

	$scripts->add( 'gc-api', "/js/gc-api$suffix.js", array( 'jquery', 'backbone', 'underscore', 'gc-api-request' ), false, 1 );

	if ( is_admin() ) {
		$scripts->add( 'admin-tags', "/js/tags$suffix.js", array( 'jquery', 'gc-ajax-response' ), false, 1 );
		$scripts->set_translations( 'admin-tags' );

		$scripts->add( 'admin-comments', "/js/edit-comments$suffix.js", array( 'gc-lists', 'quicktags', 'jquery-query' ), false, 1 );
		$scripts->set_translations( 'admin-comments' );
		did_action( 'init' ) && $scripts->localize(
			'admin-comments',
			'adminCommentsSettings',
			array(
				'hotkeys_highlight_first' => isset( $_GET['hotkeys_highlight_first'] ),
				'hotkeys_highlight_last'  => isset( $_GET['hotkeys_highlight_last'] ),
			)
		);

		$scripts->add( 'postbox', "/js/postbox$suffix.js", array( 'jquery-ui-sortable', 'gc-a11y' ), false, 1 );
		$scripts->set_translations( 'postbox' );

		$scripts->add( 'tags-box', "/js/tags-box$suffix.js", array( 'jquery', 'tags-suggest' ), false, 1 );
		$scripts->set_translations( 'tags-box' );

		$scripts->add( 'tags-suggest', "/js/tags-suggest$suffix.js", array( 'jquery-ui-autocomplete', 'gc-a11y' ), false, 1 );
		$scripts->set_translations( 'tags-suggest' );

		$scripts->add( 'post', "/js/post$suffix.js", array( 'suggest', 'gc-lists', 'postbox', 'tags-box', 'underscore', 'word-count', 'gc-a11y', 'gc-sanitize', 'clipboard' ), false, 1 );
		$scripts->set_translations( 'post' );

		$scripts->add( 'editor-expand', "/js/editor-expand$suffix.js", array( 'jquery', 'underscore' ), false, 1 );

		$scripts->add( 'link', "/js/link$suffix.js", array( 'gc-lists', 'postbox' ), false, 1 );

		$scripts->add( 'comment', "/js/comment$suffix.js", array( 'jquery', 'postbox' ), false, 1 );
		$scripts->set_translations( 'comment' );

		$scripts->add( 'admin-gallery', "/js/gallery$suffix.js", array( 'jquery-ui-sortable' ) );

		$scripts->add( 'admin-widgets', "/js/widgets$suffix.js", array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'gc-a11y' ), false, 1 );
		$scripts->set_translations( 'admin-widgets' );

		$scripts->add( 'media-widgets', "/js/widgets/media-widgets$suffix.js", array( 'jquery', 'media-models', 'media-views', 'gc-api-request' ) );
		$scripts->add_inline_script( 'media-widgets', 'gc.mediaWidgets.init();', 'after' );

		$scripts->add( 'media-audio-widget', "/js/widgets/media-audio-widget$suffix.js", array( 'media-widgets', 'media-audiovideo' ) );
		$scripts->add( 'media-image-widget', "/js/widgets/media-image-widget$suffix.js", array( 'media-widgets' ) );
		$scripts->add( 'media-gallery-widget', "/js/widgets/media-gallery-widget$suffix.js", array( 'media-widgets' ) );
		$scripts->add( 'media-video-widget', "/js/widgets/media-video-widget$suffix.js", array( 'media-widgets', 'media-audiovideo', 'gc-api-request' ) );
		$scripts->add( 'text-widgets', "/js/widgets/text-widgets$suffix.js", array( 'jquery', 'backbone', 'editor', 'gc-util', 'gc-a11y' ) );
		$scripts->add( 'custom-html-widgets', "/js/widgets/custom-html-widgets$suffix.js", array( 'jquery', 'backbone', 'gc-util', 'jquery-ui-core', 'gc-a11y' ) );

		$scripts->add( 'theme', "/js/theme$suffix.js", array( 'gc-backbone', 'gc-a11y', 'customize-base' ), false, 1 );

		$scripts->add( 'inline-edit-post', "/js/inline-edit-post$suffix.js", array( 'jquery', 'tags-suggest', 'gc-a11y' ), false, 1 );
		$scripts->set_translations( 'inline-edit-post' );

		$scripts->add( 'inline-edit-tax', "/js/inline-edit-tax$suffix.js", array( 'jquery', 'gc-a11y' ), false, 1 );
		$scripts->set_translations( 'inline-edit-tax' );

		$scripts->add( 'plugin-install', "/js/plugin-install$suffix.js", array( 'jquery', 'jquery-ui-core', 'thickbox' ), false, 1 );
		$scripts->set_translations( 'plugin-install' );

		$scripts->add( 'site-health', "/js/site-health$suffix.js", array( 'clipboard', 'jquery', 'gc-util', 'gc-a11y', 'gc-api-request', 'gc-url', 'gc-i18n', 'gc-hooks' ), false, 1 );
		$scripts->set_translations( 'site-health' );

		$scripts->add( 'privacy-tools', "/js/privacy-tools$suffix.js", array( 'jquery', 'gc-a11y' ), false, 1 );
		$scripts->set_translations( 'privacy-tools' );

		$scripts->add( 'updates', "/js/updates$suffix.js", array( 'app', 'common', 'jquery', 'gc-util', 'gc-a11y', 'gc-sanitize' ), false, 1 );
		$scripts->set_translations( 'updates' );
		did_action( 'init' ) && $scripts->localize(
			'updates',
			'_gcUpdatesSettings',
			array(
				'ajax_nonce' => gc_installing() ? '' : gc_create_nonce( 'updates' ),
			)
		);

		$scripts->add( 'farbtastic', "/vendors/farbtastic$suffix.js", array( 'jquery' ), '1.2' );

		$scripts->add( 'iris', '/vendors/iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), '1.1.1', 1 );
		$scripts->add( 'gc-color-picker', "/js/color-picker$suffix.js", array( 'iris' ), false, 1 );
		$scripts->set_translations( 'gc-color-picker' );

		$scripts->add( 'dashboard', "/js/dashboard$suffix.js", array( 'jquery', 'admin-comments', 'postbox', 'gc-util', 'gc-a11y', 'gc-date' ), false, 1 );
		$scripts->set_translations( 'dashboard' );

		$scripts->add( 'list-revisions', "/js/gc-list-revisions$suffix.js" );

		$scripts->add( 'media-grid', "/js/media-grid$suffix.js", array( 'media-editor' ), false, 1 );
		$scripts->add( 'media', "/js/media$suffix.js", array( 'jquery', 'clipboard', 'gc-i18n', 'gc-a11y' ), false, 1 );
		$scripts->set_translations( 'media' );

		$scripts->add( 'image-edit', "/js/image-edit$suffix.js", array( 'jquery', 'jquery-ui-core', 'json2', 'imgareaselect', 'gc-a11y' ), false, 1 );
		$scripts->set_translations( 'image-edit' );

		$scripts->add( 'set-post-thumbnail', "/js/set-post-thumbnail$suffix.js", array( 'jquery' ), false, 1 );
		$scripts->set_translations( 'set-post-thumbnail' );

		/*
		 * Navigation Menus: Adding underscore as a dependency to utilize _.debounce
		 * see https://core.trac.gechiui.com/ticket/42321
		 */
		$scripts->add( 'nav-menu', "/js/nav-menu$suffix.js", array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'gc-lists', 'postbox', 'json2', 'underscore' ) );
		$scripts->set_translations( 'nav-menu' );

		$scripts->add( 'custom-header', '/js/custom-header.js', array( 'jquery-masonry' ), false, 1 );
		$scripts->add( 'custom-background', "/js/custom-background$suffix.js", array( 'gc-color-picker', 'media-views' ), false, 1 );
		$scripts->add( 'media-gallery', "/js/media-gallery$suffix.js", array( 'jquery' ), false, 1 );

		$scripts->add( 'svg-painter', '/js/svg-painter.js', array( 'jquery' ), false, 1 );
	}
}

/**
 * Assign default styles to $styles object.
 *
 * Nothing is returned, because the $styles parameter is passed by reference.
 * Meaning that whatever object is passed will be updated without having to
 * reassign the variable that was passed back to the same value. This saves
 * memory.
 *
 * Adding default styles is not the only task, it also assigns the base_url
 * property, the default version, and text direction for the object.
 *
 *
 *
 * @global array $editor_styles
 *
 * @param GC_Styles $styles
 */
function gc_default_styles( $styles ) {
	global $editor_styles;

	// Include an unmodified $gc_version.
	require ABSPATH . GCINC . '/version.php';

	if ( ! defined( 'SCRIPT_DEBUG' ) ) {
		define( 'SCRIPT_DEBUG', false !== strpos( $gc_version, '-src' ) );
	}

	$guessurl = rtrim( assets_url(), '/' );

	if ( ! $guessurl ) {
		$guessurl = gc_guess_url();
	}

	$styles->base_url        = $guessurl;
	$styles->content_url     = defined( 'GC_CONTENT_URL' ) ? GC_CONTENT_URL : '';
	$styles->default_version = get_bloginfo( 'version' );
	$styles->text_direction  = function_exists( 'is_rtl' ) && is_rtl() ? 'rtl' : 'ltr';
	//压缩的文件夹，此文件夹开头的CSS文件压缩到load-styles.php文件中
	$styles->default_dirs    = array( '/css/', '/vendors/' );


	/*
	 * translators: If there are characters in your language that are not supported
	 * by Open Sans, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off' ) ) {
		$subsets = 'latin,latin-ext';

		/*
		 * translators: To add an additional Open Sans character subset specific to your language,
		 * translate this to 'greek', 'cyrillic' or 'vietnamese'. Do not translate into your own language.
		 */
		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)' );

		if ( 'cyrillic' === $subset ) {
			$subsets .= ',cyrillic,cyrillic-ext';
		} elseif ( 'greek' === $subset ) {
			$subsets .= ',greek,greek-ext';
		} elseif ( 'vietnamese' === $subset ) {
			$subsets .= ',vietnamese';
		}
	}

	// Register a stylesheet for the selected admin color scheme.
	$styles->add( 'colors', true, array( 'gc-admin', 'buttons' ) );

	$suffix = SCRIPT_DEBUG ? '' : '.min';

	// Admin CSS.
	$styles->add( 'perfect-scrollbar', "/vendors/perfect-scrollbar/css/perfect-scrollbar$suffix.css" );  /*轻量级滚动条样式*/
    $styles->add( 'app', "/css/app$suffix.css" , array( 'perfect-scrollbar' ));
    $styles->add( 'admin-bar', "/css/admin-bar$suffix.css" );
	$styles->add( 'common', "/css/common$suffix.css" );
	$styles->add( 'forms', "/css/forms$suffix.css" );
	$styles->add( 'dashboard', "/css/dashboard$suffix.css" );
	$styles->add( 'list-tables', "/css/list-tables$suffix.css" );
	$styles->add( 'edit', "/css/edit$suffix.css" );
	$styles->add( 'revisions', "/css/revisions$suffix.css" );
	$styles->add( 'media', "/css/media$suffix.css" );
	$styles->add( 'themes', "/css/themes$suffix.css" );
	$styles->add( 'about', "/css/about$suffix.css" );
	$styles->add( 'nav-menus', "/css/nav-menus$suffix.css" );
	$styles->add( 'widgets', "/css/widgets$suffix.css", array( 'gc-pointer' ) );
	$styles->add( 'site-icon', "/css/site-icon$suffix.css" );
	$styles->add( 'l10n', "/css/l10n$suffix.css" );
	$styles->add( 'code-editor', "/css/code-editor$suffix.css", array( 'gc-codemirror' ) );
	$styles->add( 'site-health', "/css/site-health$suffix.css" );

	$styles->add( 'gc-admin', false, array( 'dashicons', 'app', 'common', 'forms', 'dashboard', 'list-tables', 'edit', 'revisions', 'media', 'themes', 'about', 'nav-menus', 'widgets', 'site-icon', 'l10n' ) );

	$styles->add( 'login', "/css/login$suffix.css", array( 'dashicons', 'buttons', 'forms', 'l10n' ) );
	$styles->add( 'install', "/css/install$suffix.css", array( 'dashicons', 'buttons', 'forms', 'l10n' ) );
	$styles->add( 'gc-color-picker', "/css/color-picker$suffix.css" );
	$styles->add( 'customize-controls', "/css/customize-controls$suffix.css", array( 'gc-admin', 'colors', 'imgareaselect' ) );
	$styles->add( 'customize-widgets', "/css/customize-widgets$suffix.css", array( 'gc-admin', 'colors' ) );
	$styles->add( 'customize-nav-menus', "/css/customize-nav-menus$suffix.css", array( 'gc-admin', 'colors' ) );

	// Common dependencies.
	$styles->add( 'buttons', "/css/buttons$suffix.css" );
	$styles->add( 'dashicons', "/css/dashicons$suffix.css" );

	// Includes CSS.
	$styles->add( 'gc-auth-check', "/css/gc-auth-check$suffix.css", array( 'dashicons' ) );
	$styles->add( 'editor-buttons', "/css/editor$suffix.css", array( 'dashicons' ) );
	$styles->add( 'media-views', "/css/media-views$suffix.css", array( 'buttons', 'dashicons', 'gc-mediaelement' ) );
	$styles->add( 'gc-pointer', "/css/gc-pointer$suffix.css", array( 'dashicons' ) );
	$styles->add( 'customize-preview', "/css/customize-preview$suffix.css", array( 'dashicons' ) );
	$styles->add( 'gc-embed-template-ie', "/css/gc-embed-template-ie$suffix.css" );
	$styles->add_data( 'gc-embed-template-ie', 'conditional', 'lte IE 8' );

	// External libraries and friends.
	$styles->add( 'imgareaselect', '/vendors/imgareaselect/imgareaselect.css', array(), '0.9.8' );
	$styles->add( 'gc-jquery-ui-dialog', "/css/jquery-ui-dialog$suffix.css", array( 'dashicons' ) );
	$styles->add( 'mediaelement', '/vendors/mediaelement/mediaelementplayer-legacy.min.css', array(), '4.2.16' );
	$styles->add( 'gc-mediaelement', "/vendors/mediaelement/gc-mediaelement$suffix.css", array( 'mediaelement' ) );
	$styles->add( 'thickbox', '/vendors/thickbox/thickbox.css', array( 'dashicons' ) );
	$styles->add( 'gc-codemirror', '/vendors/codemirror/codemirror.min.css', array(), '5.29.1-alpha-ee20357' );

	// Deprecated CSS.
	$styles->add( 'deprecated-media', "/css/deprecated-media$suffix.css" );
	$styles->add( 'farbtastic', "/css/farbtastic$suffix.css", array(), '1.3u1' );
	$styles->add( 'jcrop', '/vendors/jcrop/jquery.Jcrop.min.css', array(), '0.9.15' );
	$styles->add( 'colors-fresh', false, array( 'gc-admin', 'buttons' ) ); // Old handle.


	$block_library_theme_path = "/css/dist/block-library/theme$suffix.css";
	$styles->add( 'gc-block-library-theme', $block_library_theme_path );
	$styles->add_data( 'gc-block-library-theme', 'path', ABSPATH . 'assets/' . $block_library_theme_path );

	$styles->add(
		'gc-reset-editor-styles',
		"/css/dist/block-library/reset$suffix.css",
		array( 'app', 'common', 'forms' ) // Make sure the reset is loaded after the default GC Admin styles.
	);

	$styles->add(
		'gc-editor-classic-layout-styles',
		"/css/dist/edit-post/classic$suffix.css",
		array()
	);

	$gc_edit_blocks_dependencies = array(
		'gc-components',
		'gc-editor',
		// This need to be added before the block library styles,
		// The block library styles override the "reset" styles.
		'gc-reset-editor-styles',
		'gc-block-library',
		'gc-reusable-blocks',
	);

	// Only load the default layout and margin styles for themes without theme.json file.
	if ( ! GC_Theme_JSON_Resolver::theme_has_support() ) {
		$gc_edit_blocks_dependencies[] = 'gc-editor-classic-layout-styles';
	}

	if ( ! is_array( $editor_styles ) || count( $editor_styles ) === 0 ) {
		// Include opinionated block styles if no $editor_styles are declared, so the editor never appears broken.
		$gc_edit_blocks_dependencies[] = 'gc-block-library-theme';
	}

	$styles->add(
		'gc-edit-blocks',
		"/css/dist/block-library/editor$suffix.css",
		$gc_edit_blocks_dependencies
	);

	$package_styles = array(
		'block-editor'         => array( 'gc-components' ),
		'block-library'        => array(),
		'block-directory'      => array(),
		'components'           => array(),
		'edit-post'            => array(
			'gc-components',
			'gc-block-editor',
			'gc-editor',
			'gc-edit-blocks',
			'gc-block-library',
			'gc-nux',
		),
		'editor'               => array(
			'gc-components',
			'gc-block-editor',
			'gc-nux',
			'gc-reusable-blocks',
		),
		'format-library'       => array(),
		'list-reusable-blocks' => array( 'gc-components' ),
		'reusable-blocks'      => array( 'gc-components' ),
		'nux'                  => array( 'gc-components' ),
		'widgets'              => array(
			'gc-components',
		),
		'edit-widgets'         => array(
			'gc-widgets',
			'gc-block-editor',
			'gc-edit-blocks',
			'gc-block-library',
			'gc-reusable-blocks',
		),
		'customize-widgets'    => array(
			'gc-widgets',
			'gc-block-editor',
			'gc-edit-blocks',
			'gc-block-library',
			'gc-reusable-blocks',
		),
		'edit-site'            => array(
			'gc-components',
			'gc-block-editor',
			'gc-edit-blocks',
		),
	);

	foreach ( $package_styles as $package => $dependencies ) {
		$handle = 'gc-' . $package;
		$path   = "/css/dist/$package/style$suffix.css";

		if ( 'block-library' === $package && gc_should_load_separate_core_block_assets() ) {
			$path = "/css/dist/$package/common$suffix.css";
		}
		$styles->add( $handle, $path, $dependencies );
		$styles->add_data( $handle, 'path', ABSPATH . 'assets/' . $path );
	}

	// RTL CSS.
	$rtl_styles = array(
		// Admin CSS.
		'app',
		'common',
		'forms',
		'dashboard',
		'list-tables',
		'edit',
		'revisions',
		'media',
		'themes',
		'about',
		'nav-menus',
		'widgets',
		'site-icon',
		'l10n',
		'install',
		'gc-color-picker',
		'customize-controls',
		'customize-widgets',
		'customize-nav-menus',
		'customize-preview',
		'login',
		'site-health',
		// Includes CSS.
		'buttons',
		'gc-auth-check',
		'editor-buttons',
		'media-views',
		'gc-pointer',
		'gc-jquery-ui-dialog',
		// Package styles.
		'gc-reset-editor-styles',
		'gc-editor-classic-layout-styles',
		'gc-block-library-theme',
		'gc-edit-blocks',
		'gc-block-editor',
		'gc-block-library',
		'gc-block-directory',
		'gc-components',
		'gc-customize-widgets',
		'gc-edit-post',
		'gc-edit-site',
		'gc-edit-widgets',
		'gc-editor',
		'gc-format-library',
		'gc-list-reusable-blocks',
		'gc-reusable-blocks',
		'gc-nux',
		'gc-widgets',
		// Deprecated CSS.
		'deprecated-media',
		'farbtastic',
	);

	foreach ( $rtl_styles as $rtl_style ) {
		$styles->add_data( $rtl_style, 'rtl', 'replace' );
		if ( $suffix ) {
			$styles->add_data( $rtl_style, 'suffix', $suffix );
		}
	}
}

/**
 * Reorder JavaScript scripts array to place prototype before jQuery.
 *
 *
 *
 * @param array $js_array JavaScript scripts array
 * @return array Reordered array, if needed.
 */
function gc_prototype_before_jquery( $js_array ) {
	$prototype = array_search( 'prototype', $js_array, true );

	if ( false === $prototype ) {
		return $js_array;
	}

	$jquery = array_search( 'jquery', $js_array, true );

	if ( false === $jquery ) {
		return $js_array;
	}

	if ( $prototype < $jquery ) {
		return $js_array;
	}

	unset( $js_array[ $prototype ] );

	array_splice( $js_array, $jquery, 0, 'prototype' );

	return $js_array;
}

/**
 * Load localized data on print rather than initialization.
 *
 * These localizations require information that may not be loaded even by init.
 *
 *
 */
function gc_just_in_time_script_localization() {

	gc_localize_script(
		'autosave',
		'autosaveL10n',
		array(
			'autosaveInterval' => AUTOSAVE_INTERVAL,
			'blog_id'          => get_current_blog_id(),
		)
	);

	gc_localize_script(
		'mce-view',
		'mceViewL10n',
		array(
			'shortcodes' => ! empty( $GLOBALS['shortcode_tags'] ) ? array_keys( $GLOBALS['shortcode_tags'] ) : array(),
		)
	);

	gc_localize_script(
		'word-count',
		'wordCountL10n',
		array(
			/*
			 * translators: If your word count is based on single characters (e.g. East Asian characters),
			 * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
			 * Do not translate into your own language.
			 */
			'type'       => _x( 'characters_excluding_spaces', 'Word count type. Do not translate!' ),
			'shortcodes' => ! empty( $GLOBALS['shortcode_tags'] ) ? array_keys( $GLOBALS['shortcode_tags'] ) : array(),
		)
	);
}

/**
 * Localizes the jQuery UI datepicker.
 *
 *
 *
 * @link https://api.jqueryui.com/datepicker/#options
 *
 * @global GC_Locale $gc_locale GeChiUI date and time locale object.
 */
function gc_localize_jquery_ui_datepicker() {
	global $gc_locale;

	if ( ! gc_script_is( 'jquery-ui-datepicker', 'enqueued' ) ) {
		return;
	}

	// Convert the PHP date format into jQuery UI's format.
	$datepicker_date_format = str_replace(
		array(
			'd',
			'j',
			'l',
			'z', // Day.
			'F',
			'M',
			'n',
			'm', // Month.
			'Y',
			'y', // Year.
		),
		array(
			'dd',
			'd',
			'DD',
			'o',
			'MM',
			'M',
			'm',
			'mm',
			'yy',
			'y',
		),
		get_option( 'date_format' )
	);

	$datepicker_defaults = gc_json_encode(
		array(
			'closeText'       => __( '关闭' ),
			'currentText'     => __( '今天' ),
			'monthNames'      => array_values( $gc_locale->month ),
			'monthNamesShort' => array_values( $gc_locale->month_abbrev ),
			'nextText'        => __( '下一步' ),
			'prevText'        => __( '上一步' ),
			'dayNames'        => array_values( $gc_locale->weekday ),
			'dayNamesShort'   => array_values( $gc_locale->weekday_abbrev ),
			'dayNamesMin'     => array_values( $gc_locale->weekday_initial ),
			'dateFormat'      => $datepicker_date_format,
			'firstDay'        => absint( get_option( 'start_of_week' ) ),
			'isRTL'           => $gc_locale->is_rtl(),
		)
	);

	gc_add_inline_script( 'jquery-ui-datepicker', "jQuery(function(jQuery){jQuery.datepicker.setDefaults({$datepicker_defaults});});" );
}

/**
 * Localizes community events data that needs to be passed to dashboard.js.
 *
 *
 */
function gc_localize_community_events() {
	if ( ! gc_script_is( 'dashboard' ) ) {
		return;
	}

	require_once ABSPATH . 'gc-admin/includes/class-gc-community-events.php';

	$user_id            = get_current_user_id();
	$saved_location     = get_user_option( 'community-events-location', $user_id );
	$saved_ip_address   = isset( $saved_location['ip'] ) ? $saved_location['ip'] : false;
	$current_ip_address = GC_Community_Events::get_unsafe_client_ip();

	/*
	 * If the user's location is based on their IP address, then update their
	 * location when their IP address changes. This allows them to see events
	 * in their current city when travelling. Otherwise, they would always be
	 * shown events in the city where they were when they first loaded the
	 * Dashboard, which could have been months or years ago.
	 */
	if ( $saved_ip_address && $current_ip_address && $current_ip_address !== $saved_ip_address ) {
		$saved_location['ip'] = $current_ip_address;
		update_user_meta( $user_id, 'community-events-location', $saved_location );
	}

	$events_client = new GC_Community_Events( $user_id, $saved_location );

	gc_localize_script(
		'dashboard',
		'communityEventsData',
		array(
			'nonce'       => gc_create_nonce( 'community_events' ),
			'cache'       => $events_client->get_cached_events(),
			'time_format' => get_option( 'time_format' ),
		)
	);
}

/**
 * Administration Screen CSS for changing the styles.
 *
 * If installing the 'gc-admin/' directory will be replaced with './'.
 *
 * The $_gc_admin_css_colors global manages the Administration Screens CSS
 * stylesheet that is loaded. The option that is set is 'admin_color' and is the
 * color and key for the array. The value for the color key is an object with
 * a 'url' parameter that has the URL path to the CSS file.
 *
 * The query from $src parameter will be appended to the URL that is given from
 * the $_gc_admin_css_colors array value URL.
 *
 *
 *
 * @global array $_gc_admin_css_colors
 *
 * @param string $src    Source URL.
 * @param string $handle Either 'colors' or 'colors-rtl'.
 * @return string|false URL path to CSS stylesheet for Administration Screens.
 */
function gc_style_loader_src( $src, $handle ) {
	global $_gc_admin_css_colors;

	if ( gc_installing() ) {
		return preg_replace( '#^gc-admin/#', './', $src );
	}

	if ( 'colors' === $handle ) {
		return false;
		// $color = get_user_option( 'admin_color' );

		// if ( empty( $color ) || ! isset( $_gc_admin_css_colors[ $color ] ) ) {
		// 	$color = 'fresh';
		// }

		// $color = $_gc_admin_css_colors[ $color ];
		// $url   = $color->url;

		// if ( ! $url ) {
		// 	return false;
		// }

		// $parsed = parse_url( $src );
		// if ( isset( $parsed['query'] ) && $parsed['query'] ) {
		// 	gc_parse_str( $parsed['query'], $qv );
		// 	$url = add_query_arg( $qv, $url );
		// }

		// return $url;
	}

	return $src;
}

/**
 * Prints the script queue in the HTML head on admin pages.
 *
 * Postpones the scripts that were queued for the footer.
 * print_footer_scripts() is called in the footer to print these scripts.
 *
 *
 *
 * @see gc_print_scripts()
 *
 * @global bool $concatenate_scripts
 *
 * @return array
 */
function print_head_scripts() {
	global $concatenate_scripts;

	if ( ! did_action( 'gc_print_scripts' ) ) {
		/** This action is documented in gc-includes/functions.gc-scripts.php */
		do_action( 'gc_print_scripts' );
	}

	$gc_scripts = gc_scripts();

	script_concat_settings();
	$gc_scripts->do_concat = $concatenate_scripts;
	$gc_scripts->do_head_items();

	/**
	 * Filters whether to print the head scripts.
	 *
	 *
	 * @param bool $print Whether to print the head scripts. Default true.
	 */
	if ( apply_filters( 'print_head_scripts', true ) ) {
		_print_scripts();
	}

	$gc_scripts->reset();
	return $gc_scripts->done;
}

/**
 * Prints the scripts that were queued for the footer or too late for the HTML head.
 *
 *
 *
 * @global GC_Scripts $gc_scripts
 * @global bool       $concatenate_scripts
 *
 * @return array
 */
function print_footer_scripts() {
	global $gc_scripts, $concatenate_scripts;

	if ( ! ( $gc_scripts instanceof GC_Scripts ) ) {
		return array(); // No need to run if not instantiated.
	}
	script_concat_settings();
	$gc_scripts->do_concat = $concatenate_scripts;
	$gc_scripts->do_footer_items();

	/**
	 * Filters whether to print the footer scripts.
	 *
	 *
	 * @param bool $print Whether to print the footer scripts. Default true.
	 */
	if ( apply_filters( 'print_footer_scripts', true ) ) {
		_print_scripts();
	}

	$gc_scripts->reset();
	return $gc_scripts->done;
}

/**
 * Print scripts (internal use only)
 *
 * @ignore
 *
 * @global GC_Scripts $gc_scripts
 * @global bool       $compress_scripts
 */
function _print_scripts() {
	global $gc_scripts, $compress_scripts;

	$zip = $compress_scripts ? 1 : 0;
	if ( $zip && defined( 'ENFORCE_GZIP' ) && ENFORCE_GZIP ) {
		$zip = 'gzip';
	}

	$concat    = trim( $gc_scripts->concat, ', ' );
	$type_attr = current_theme_supports( 'html5', 'script' ) ? '' : " type='text/javascript'";

	if ( $concat ) {
		if ( ! empty( $gc_scripts->print_code ) ) {
			echo "\n<script{$type_attr}>\n";
			echo "/* <![CDATA[ */\n"; // Not needed in HTML 5.
			echo $gc_scripts->print_code;
			echo "/* ]]> */\n";
			echo "</script>\n";
		}

		$concat       = str_split( $concat, 128 );
		$concatenated = '';

		foreach ( $concat as $key => $chunk ) {
			$concatenated .= "&load%5Bchunk_{$key}%5D={$chunk}";
		}

		$src = site_url( "/gc-admin/load-scripts.php?c={$zip}" . $concatenated . '&ver=' . $gc_scripts->default_version );
		echo "<script{$type_attr} src='" . esc_attr( $src ) . "'></script>\n";
	}

	if ( ! empty( $gc_scripts->print_html ) ) {
		echo $gc_scripts->print_html;
	}
}

/**
 * Prints the script queue in the HTML head on the front end.
 *
 * Postpones the scripts that were queued for the footer.
 * gc_print_footer_scripts() is called in the footer to print these scripts.
 *
 *
 *
 * @global GC_Scripts $gc_scripts
 *
 * @return array
 */
function gc_print_head_scripts() {
	global $gc_scripts;

	if ( ! did_action( 'gc_print_scripts' ) ) {
		/** This action is documented in gc-includes/functions.gc-scripts.php */
		do_action( 'gc_print_scripts' );
	}

	if ( ! ( $gc_scripts instanceof GC_Scripts ) ) {
		return array(); // No need to run if nothing is queued.
	}

	return print_head_scripts();
}

/**
 * Private, for use in *_footer_scripts hooks
 *
 *
 */
function _gc_footer_scripts() {
	print_late_styles();
	print_footer_scripts();
}

/**
 * Hooks to print the scripts and styles in the footer.
 *
 *
 */
function gc_print_footer_scripts() {
	/**
	 * Fires when footer scripts are printed.
	 *
	 */
	do_action( 'gc_print_footer_scripts' );
}

/**
 * Wrapper for do_action( 'gc_enqueue_scripts' ).
 *
 * Allows plugins to queue scripts for the front end using gc_enqueue_script().
 * Runs first in gc_head() where all is_home(), is_page(), etc. functions are available.
 *
 *
 */
function gc_enqueue_scripts() {
	/**
	 * Fires when scripts and styles are enqueued.
	 *
	 */
	do_action( 'gc_enqueue_scripts' );
}

/**
 * Prints the styles queue in the HTML head on admin pages.
 *
 *
 *
 * @global bool $concatenate_scripts
 *
 * @return array
 */
function print_admin_styles() {
	global $concatenate_scripts;

	$gc_styles = gc_styles();

	script_concat_settings();
	$gc_styles->do_concat = $concatenate_scripts;
	$gc_styles->do_items( false );

	/**
	 * Filters whether to print the admin styles.
	 *
	 *
	 * @param bool $print Whether to print the admin styles. Default true.
	 */
	if ( apply_filters( 'print_admin_styles', true ) ) {
		_print_styles();
	}

	$gc_styles->reset();
	return $gc_styles->done;
}

/**
 * Prints the styles that were queued too late for the HTML head.
 *
 *
 *
 * @global GC_Styles $gc_styles
 * @global bool      $concatenate_scripts
 *
 * @return array|void
 */
function print_late_styles() {
	global $gc_styles, $concatenate_scripts;

	if ( ! ( $gc_styles instanceof GC_Styles ) ) {
		return;
	}

	script_concat_settings();
	$gc_styles->do_concat = $concatenate_scripts;
	$gc_styles->do_footer_items();

	/**
	 * Filters whether to print the styles queued too late for the HTML head.
	 *
	 *
	 * @param bool $print Whether to print the 'late' styles. Default true.
	 */
	if ( apply_filters( 'print_late_styles', true ) ) {
		_print_styles();
	}

	$gc_styles->reset();
	return $gc_styles->done;
}

/**
 * Print styles (internal use only)
 *
 * @ignore
 *
 *
 * @global bool $compress_css
 */
function _print_styles() {
	global $compress_css;

	$gc_styles = gc_styles();

	$zip = $compress_css ? 1 : 0;
	if ( $zip && defined( 'ENFORCE_GZIP' ) && ENFORCE_GZIP ) {
		$zip = 'gzip';
	}

	$concat    = trim( $gc_styles->concat, ', ' );
	$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

	if ( $concat ) {
		$dir = $gc_styles->text_direction;
		$ver = $gc_styles->default_version;

		$concat       = str_split( $concat, 128 );
		$concatenated = '';

		foreach ( $concat as $key => $chunk ) {
			$concatenated .= "&load%5Bchunk_{$key}%5D={$chunk}";
		}

		$href = site_url( "/gc-admin/load-styles.php?c={$zip}&dir={$dir}" . $concatenated . '&ver=' . $ver );
		echo "<link rel='stylesheet' href='" . esc_attr( $href ) . "'{$type_attr} media='all' />\n";

		if ( ! empty( $gc_styles->print_code ) ) {
			echo "<style{$type_attr}>\n";
			echo $gc_styles->print_code;
			echo "\n</style>\n";
		}
	}

	if ( ! empty( $gc_styles->print_html ) ) {
		echo $gc_styles->print_html;
	}
}

/**
 * Determine the concatenation and compression settings for scripts and styles.
 *
 *
 *
 * @global bool $concatenate_scripts
 * @global bool $compress_scripts
 * @global bool $compress_css
 */
function script_concat_settings() {
	global $concatenate_scripts, $compress_scripts, $compress_css;

	$compressed_output = ( ini_get( 'zlib.output_compression' ) || 'ob_gzhandler' === ini_get( 'output_handler' ) );

	$can_compress_scripts = ! gc_installing() && get_site_option( 'can_compress_scripts' );

	if ( ! isset( $concatenate_scripts ) ) {
		$concatenate_scripts = defined( 'CONCATENATE_SCRIPTS' ) ? CONCATENATE_SCRIPTS : true;
		if ( ( ! is_admin() && ! did_action( 'login_init' ) ) || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) {
			$concatenate_scripts = false;
		}
	}

	if ( ! isset( $compress_scripts ) ) {
		$compress_scripts = defined( 'COMPRESS_SCRIPTS' ) ? COMPRESS_SCRIPTS : true;
		if ( $compress_scripts && ( ! $can_compress_scripts || $compressed_output ) ) {
			$compress_scripts = false;
		}
	}

	if ( ! isset( $compress_css ) ) {
		$compress_css = defined( 'COMPRESS_CSS' ) ? COMPRESS_CSS : true;
		if ( $compress_css && ( ! $can_compress_scripts || $compressed_output ) ) {
			$compress_css = false;
		}
	}
}

/**
 * Handles the enqueueing of block scripts and styles that are common to both
 * the editor and the front-end.
 *
 *
 */
function gc_common_block_scripts_and_styles() {
	if ( is_admin() && ! gc_should_load_block_editor_scripts_and_styles() ) {
		return;
	}

	gc_enqueue_style( 'gc-block-library' );

	if ( current_theme_supports( 'gc-block-styles' ) ) {
		if ( gc_should_load_separate_core_block_assets() ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'css' : 'min.css';
			$files  = glob( ABSPATH . "assets/blocks/**/theme.$suffix" );
			foreach ( $files as $path ) {
				$block_name = basename( dirname( $path ) );
				if ( is_rtl() && file_exists( ABSPATH . "assets/blocks/$block_name/theme-rtl.$suffix" ) ) {
					$path = ABSPATH . "assets/blocks/$block_name/theme-rtl.$suffix";
				}
				gc_add_inline_style( "gc-block-{$block_name}", file_get_contents( $path ) );
			}
		} else {
			gc_enqueue_style( 'gc-block-library-theme' );
		}
	}

	/**
	 * Fires after enqueuing block assets for both editor and front-end.
	 *
	 * Call `add_action` on any hook before 'gc_enqueue_scripts'.
	 *
	 * In the function call you supply, simply use `gc_enqueue_script` and
	 * `gc_enqueue_style` to add your functionality to the Gutenberg editor.
	 *
	 */
	do_action( 'enqueue_block_assets' );
}

/**
 * Enqueues the global styles defined via theme.json.
 *
 *
 */
function gc_enqueue_global_styles() {
	$separate_assets  = gc_should_load_separate_core_block_assets();
	$is_block_theme   = gc_is_block_theme();
	$is_classic_theme = ! $is_block_theme;

	/*
	 * Global styles should be printed in the head when loading all styles combined.
	 * The footer should only be used to print global styles for classic themes with separate core assets enabled.
	 *
	 * See https://core.trac.gechiui.com/ticket/53494.
	 */
	if (
		( $is_block_theme && doing_action( 'gc_footer' ) ) ||
		( $is_classic_theme && doing_action( 'gc_footer' ) && ! $separate_assets ) ||
		( $is_classic_theme && doing_action( 'gc_enqueue_scripts' ) && $separate_assets )
	) {

		return;
	}

	$stylesheet = gc_get_global_stylesheet();

	if ( empty( $stylesheet ) ) {
		return;
	}

	gc_register_style( 'global-styles', false, array(), true, true );
	gc_add_inline_style( 'global-styles', $stylesheet );
	gc_enqueue_style( 'global-styles' );
}

/**
 * Render the SVG filters supplied by theme.json.
 *
 * Note that this doesn't render the per-block user-defined
 * filters which are handled by gc_render_duotone_support,
 * but it should be rendered before the filtered content
 * in the body to satisfy Safari's rendering quirks.
 *
 * @since 5.9.1
 */
function gc_global_styles_render_svg_filters() {
	/*
	 * When calling via the in_admin_header action, we only want to render the
	 * SVGs on block editor pages.
	 */
	if (
		is_admin() &&
		! get_current_screen()->is_block_editor()
	) {
		return;
	}

	$filters = gc_get_global_styles_svg_filters();
	if ( ! empty( $filters ) ) {
		echo $filters;
	}
}

/**
 * Checks if the editor scripts and styles for all registered block types
 * should be enqueued on the current screen.
 *
 *
 *
 * @global GC_Screen $current_screen GeChiUI current screen object.
 *
 * @return bool Whether scripts and styles should be enqueued.
 */
function gc_should_load_block_editor_scripts_and_styles() {
	global $current_screen;

	$is_block_editor_screen = ( $current_screen instanceof GC_Screen ) && $current_screen->is_block_editor();

	/**
	 * Filters the flag that decides whether or not block editor scripts and styles
	 * are going to be enqueued on the current screen.
	 *
	 *
	 * @param bool $is_block_editor_screen Current value of the flag.
	 */
	return apply_filters( 'should_load_block_editor_scripts_and_styles', $is_block_editor_screen );
}

/**
 * Checks whether separate styles should be loaded for core blocks on-render.
 *
 * When this function returns true, other functions ensure that core blocks
 * only load their assets on-render, and each block loads its own, individual
 * assets. Third-party blocks only load their assets when rendered.
 *
 * When this function returns false, all core block assets are loaded regardless
 * of whether they are rendered in a page or not, because they are all part of
 * the `block-library/style.css` file. Assets for third-party blocks are always
 * enqueued regardless of whether they are rendered or not.
 *
 * This only affects front end and not the block editor screens.
 *
 * @see gc_enqueue_registered_block_scripts_and_styles()
 * @see register_block_style_handle()
 *
 *
 *
 * @return bool Whether separate assets will be loaded.
 */
function gc_should_load_separate_core_block_assets() {
	if ( is_admin() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return false;
	}

	/**
	 * Filters whether block styles should be loaded separately.
	 *
	 * Returning false loads all core block assets, regardless of whether they are rendered
	 * in a page or not. Returning true loads core block assets only when they are rendered.
	 *
	 *
	 * @param bool $load_separate_assets Whether separate assets will be loaded.
	 *                                   Default false (all block assets are loaded, even when not used).
	 */
	return apply_filters( 'should_load_separate_core_block_assets', false );
}

/**
 * Enqueues registered block scripts and styles, depending on current rendered
 * context (only enqueuing editor scripts while in context of the editor).
 *
 *
 *
 * @global GC_Screen $current_screen GeChiUI current screen object.
 */
function gc_enqueue_registered_block_scripts_and_styles() {
	global $current_screen;

	if ( gc_should_load_separate_core_block_assets() ) {
		return;
	}

	$load_editor_scripts = is_admin() && gc_should_load_block_editor_scripts_and_styles();

	$block_registry = GC_Block_Type_Registry::get_instance();
	foreach ( $block_registry->get_all_registered() as $block_name => $block_type ) {
		// Front-end styles.
		if ( ! empty( $block_type->style ) ) {
			gc_enqueue_style( $block_type->style );
		}

		// Front-end script.
		if ( ! empty( $block_type->script ) ) {
			gc_enqueue_script( $block_type->script );
		}

		// Editor styles.
		if ( $load_editor_scripts && ! empty( $block_type->editor_style ) ) {
			gc_enqueue_style( $block_type->editor_style );
		}

		// Editor script.
		if ( $load_editor_scripts && ! empty( $block_type->editor_script ) ) {
			gc_enqueue_script( $block_type->editor_script );
		}
	}
}

/**
 * Function responsible for enqueuing the styles required for block styles functionality on the editor and on the frontend.
 *
 *
 *
 * @global GC_Styles $gc_styles
 */
function enqueue_block_styles_assets() {
	global $gc_styles;

	$block_styles = GC_Block_Styles_Registry::get_instance()->get_all_registered();

	foreach ( $block_styles as $block_name => $styles ) {
		foreach ( $styles as $style_properties ) {
			if ( isset( $style_properties['style_handle'] ) ) {

				// If the site loads separate styles per-block, enqueue the stylesheet on render.
				if ( gc_should_load_separate_core_block_assets() ) {
					add_filter(
						'render_block',
						function( $html, $block ) use ( $block_name, $style_properties ) {
							if ( $block['blockName'] === $block_name ) {
								gc_enqueue_style( $style_properties['style_handle'] );
							}
							return $html;
						},
						10,
						2
					);
				} else {
					gc_enqueue_style( $style_properties['style_handle'] );
				}
			}
			if ( isset( $style_properties['inline_style'] ) ) {

				// Default to "gc-block-library".
				$handle = 'gc-block-library';

				// If the site loads separate styles per-block, check if the block has a stylesheet registered.
				if ( gc_should_load_separate_core_block_assets() ) {
					$block_stylesheet_handle = generate_block_asset_handle( $block_name, 'style' );

					if ( isset( $gc_styles->registered[ $block_stylesheet_handle ] ) ) {
						$handle = $block_stylesheet_handle;
					}
				}

				// Add inline styles to the calculated handle.
				gc_add_inline_style( $handle, $style_properties['inline_style'] );
			}
		}
	}
}

/**
 * Function responsible for enqueuing the assets required for block styles functionality on the editor.
 *
 *
 */
function enqueue_editor_block_styles_assets() {
	$block_styles = GC_Block_Styles_Registry::get_instance()->get_all_registered();

	$register_script_lines = array( '( function() {' );
	foreach ( $block_styles as $block_name => $styles ) {
		foreach ( $styles as $style_properties ) {
			$block_style = array(
				'name'  => $style_properties['name'],
				'label' => $style_properties['label'],
			);
			if ( isset( $style_properties['is_default'] ) ) {
				$block_style['isDefault'] = $style_properties['is_default'];
			}
			$register_script_lines[] = sprintf(
				'	gc.blocks.registerBlockStyle( \'%s\', %s );',
				$block_name,
				gc_json_encode( $block_style )
			);
		}
	}
	$register_script_lines[] = '} )();';
	$inline_script           = implode( "\n", $register_script_lines );

	gc_register_script( 'gc-block-styles', false, array( 'gc-blocks' ), true, true );
	gc_add_inline_script( 'gc-block-styles', $inline_script );
	gc_enqueue_script( 'gc-block-styles' );
}

/**
 * Enqueues the assets required for the block directory within the block editor.
 *
 *
 */
function gc_enqueue_editor_block_directory_assets() {
	gc_enqueue_script( 'gc-block-directory' );
	gc_enqueue_style( 'gc-block-directory' );
}

/**
 * Enqueues the assets required for the format library within the block editor.
 *
 *
 */
function gc_enqueue_editor_format_library_assets() {
	gc_enqueue_script( 'gc-format-library' );
	gc_enqueue_style( 'gc-format-library' );
}

/**
 * Sanitizes an attributes array into an attributes string to be placed inside a `<script>` tag.
 *
 * Automatically injects type attribute if needed.
 * Used by {@see gc_get_script_tag()} and {@see gc_get_inline_script_tag()}.
 *
 *
 *
 * @param array $attributes Key-value pairs representing `<script>` tag attributes.
 * @return string String made of sanitized `<script>` tag attributes.
 */
function gc_sanitize_script_attributes( $attributes ) {
	$html5_script_support = ! is_admin() && ! current_theme_supports( 'html5', 'script' );
	$attributes_string    = '';

	// If HTML5 script tag is supported, only the attribute name is added
	// to $attributes_string for entries with a boolean value, and that are true.
	foreach ( $attributes as $attribute_name => $attribute_value ) {
		if ( is_bool( $attribute_value ) ) {
			if ( $attribute_value ) {
				$attributes_string .= $html5_script_support ? sprintf( ' %1$s="%2$s"', esc_attr( $attribute_name ), esc_attr( $attribute_name ) ) : ' ' . esc_attr( $attribute_name );
			}
		} else {
			$attributes_string .= sprintf( ' %1$s="%2$s"', esc_attr( $attribute_name ), esc_attr( $attribute_value ) );
		}
	}

	return $attributes_string;
}

/**
 * Formats `<script>` loader tags.
 *
 * It is possible to inject attributes in the `<script>` tag via the {@see 'gc_script_attributes'} filter.
 * Automatically injects type attribute if needed.
 *
 *
 *
 * @param array $attributes Key-value pairs representing `<script>` tag attributes.
 * @return string String containing `<script>` opening and closing tags.
 */
function gc_get_script_tag( $attributes ) {
	if ( ! isset( $attributes['type'] ) && ! is_admin() && ! current_theme_supports( 'html5', 'script' ) ) {
		$attributes['type'] = 'text/javascript';
	}
	/**
	 * Filters attributes to be added to a script tag.
	 *
	 *
	 * @param array $attributes Key-value pairs representing `<script>` tag attributes.
	 *                          Only the attribute name is added to the `<script>` tag for
	 *                          entries with a boolean value, and that are true.
	 */
	$attributes = apply_filters( 'gc_script_attributes', $attributes );

	return sprintf( "<script%s></script>\n", gc_sanitize_script_attributes( $attributes ) );
}

/**
 * Prints formatted `<script>` loader tag.
 *
 * It is possible to inject attributes in the `<script>` tag via the  {@see 'gc_script_attributes'}  filter.
 * Automatically injects type attribute if needed.
 *
 *
 *
 * @param array $attributes Key-value pairs representing `<script>` tag attributes.
 */
function gc_print_script_tag( $attributes ) {
	echo gc_get_script_tag( $attributes );
}

/**
 * Wraps inline JavaScript in `<script>` tag.
 *
 * It is possible to inject attributes in the `<script>` tag via the  {@see 'gc_script_attributes'}  filter.
 * Automatically injects type attribute if needed.
 *
 *
 *
 * @param string $javascript Inline JavaScript code.
 * @param array  $attributes Optional. Key-value pairs representing `<script>` tag attributes.
 * @return string String containing inline JavaScript code wrapped around `<script>` tag.
 */
function gc_get_inline_script_tag( $javascript, $attributes = array() ) {
	if ( ! isset( $attributes['type'] ) && ! is_admin() && ! current_theme_supports( 'html5', 'script' ) ) {
		$attributes['type'] = 'text/javascript';
	}
	/**
	 * Filters attributes to be added to a script tag.
	 *
	 *
	 * @param array  $attributes Key-value pairs representing `<script>` tag attributes.
	 *                           Only the attribute name is added to the `<script>` tag for
	 *                           entries with a boolean value, and that are true.
	 * @param string $javascript Inline JavaScript code.
	 */
	$attributes = apply_filters( 'gc_inline_script_attributes', $attributes, $javascript );

	$javascript = "\n" . trim( $javascript, "\n\r " ) . "\n";

	return sprintf( "<script%s>%s</script>\n", gc_sanitize_script_attributes( $attributes ), $javascript );
}

/**
 * Prints inline JavaScript wrapped in `<script>` tag.
 *
 * It is possible to inject attributes in the `<script>` tag via the  {@see 'gc_script_attributes'}  filter.
 * Automatically injects type attribute if needed.
 *
 *
 *
 * @param string $javascript Inline JavaScript code.
 * @param array  $attributes Optional. Key-value pairs representing `<script>` tag attributes.
 */
function gc_print_inline_script_tag( $javascript, $attributes = array() ) {
	echo gc_get_inline_script_tag( $javascript, $attributes );
}

/**
 * Allows small styles to be inlined.
 *
 * This improves performance and sustainability, and is opt-in. Stylesheets can opt in
 * by adding `path` data using `gc_style_add_data`, and defining the file's absolute path:
 *
 *     gc_style_add_data( $style_handle, 'path', $file_path );
 *
 *
 *
 * @global GC_Styles $gc_styles
 */
function gc_maybe_inline_styles() {
	global $gc_styles;

	$total_inline_limit = 20000;
	/**
	 * The maximum size of inlined styles in bytes.
	 *
	 *
	 * @param int $total_inline_limit The file-size threshold, in bytes. Default 20000.
	 */
	$total_inline_limit = apply_filters( 'styles_inline_size_limit', $total_inline_limit );

	$styles = array();

	// Build an array of styles that have a path defined.
	foreach ( $gc_styles->queue as $handle ) {
		if ( gc_styles()->get_data( $handle, 'path' ) && file_exists( $gc_styles->registered[ $handle ]->extra['path'] ) ) {
			$styles[] = array(
				'handle' => $handle,
				'src'    => $gc_styles->registered[ $handle ]->src,
				'path'   => $gc_styles->registered[ $handle ]->extra['path'],
				'size'   => filesize( $gc_styles->registered[ $handle ]->extra['path'] ),
			);
		}
	}

	if ( ! empty( $styles ) ) {
		// Reorder styles array based on size.
		usort(
			$styles,
			static function( $a, $b ) {
				return ( $a['size'] <= $b['size'] ) ? -1 : 1;
			}
		);

		/*
		 * The total inlined size.
		 *
		 * On each iteration of the loop, if a style gets added inline the value of this var increases
		 * to reflect the total size of inlined styles.
		 */
		$total_inline_size = 0;

		// Loop styles.
		foreach ( $styles as $style ) {

			// Size check. Since styles are ordered by size, we can break the loop.
			if ( $total_inline_size + $style['size'] > $total_inline_limit ) {
				break;
			}

			// Get the styles if we don't already have them.
			$style['css'] = file_get_contents( $style['path'] );

			// Check if the style contains relative URLs that need to be modified.
			// URLs relative to the stylesheet's path should be converted to relative to the site's root.
			$style['css'] = _gc_normalize_relative_css_links( $style['css'], $style['src'] );

			// Set `src` to `false` and add styles inline.
			$gc_styles->registered[ $style['handle'] ]->src = false;
			if ( empty( $gc_styles->registered[ $style['handle'] ]->extra['after'] ) ) {
				$gc_styles->registered[ $style['handle'] ]->extra['after'] = array();
			}
			array_unshift( $gc_styles->registered[ $style['handle'] ]->extra['after'], $style['css'] );

			// Add the styles size to the $total_inline_size var.
			$total_inline_size += (int) $style['size'];
		}
	}
}

/**
 * Make URLs relative to the GeChiUI installation.
 *
 *
 * @access private
 *
 * @param string $css            The CSS to make URLs relative to the GeChiUI installation.
 * @param string $stylesheet_url The URL to the stylesheet.
 *
 * @return string The CSS with URLs made relative to the GeChiUI installation.
 */
function _gc_normalize_relative_css_links( $css, $stylesheet_url ) {
	$has_src_results = preg_match_all( '#url\s*\(\s*[\'"]?\s*([^\'"\)]+)#', $css, $src_results );
	if ( $has_src_results ) {
		// Loop through the URLs to find relative ones.
		foreach ( $src_results[1] as $src_index => $src_result ) {
			// Skip if this is an absolute URL.
			if ( 0 === strpos( $src_result, 'http' ) || 0 === strpos( $src_result, '//' ) ) {
				continue;
			}

			// Skip if the URL is an HTML ID.
			if ( str_starts_with( $src_result, '#' ) ) {
				continue;
			}

			// Skip if the URL is a data URI.
			if ( str_starts_with( $src_result, 'data:' ) ) {
				continue;
			}

			// Build the absolute URL.
			$absolute_url = dirname( $stylesheet_url ) . '/' . $src_result;
			$absolute_url = str_replace( '/./', '/', $absolute_url );
			// Convert to URL related to the site root.
			$relative_url = gc_make_link_relative( $absolute_url );

			// Replace the URL in the CSS.
			$css = str_replace(
				$src_results[0][ $src_index ],
				str_replace( $src_result, $relative_url, $src_results[0][ $src_index ] ),
				$css
			);
		}
	}

	return $css;
}

/**
 * Inject the block editor assets that need to be loaded into the editor's iframe as an inline script.
 *
 *
 */
function gc_add_iframed_editor_assets_html() {
	global $pagenow;

	if ( ! gc_should_load_block_editor_scripts_and_styles() ) {
		return;
	}

	$script_handles = array();
	$style_handles  = array(
		'gc-block-editor',
		'gc-block-library',
		'gc-block-library-theme',
		'gc-edit-blocks',
	);

	if ( 'widgets.php' === $pagenow || 'customize.php' === $pagenow ) {
		$style_handles[] = 'gc-widgets';
		$style_handles[] = 'gc-edit-widgets';
	}

	$block_registry = GC_Block_Type_Registry::get_instance();

	foreach ( $block_registry->get_all_registered() as $block_type ) {
		if ( ! empty( $block_type->style ) ) {
			$style_handles[] = $block_type->style;
		}

		if ( ! empty( $block_type->editor_style ) ) {
			$style_handles[] = $block_type->editor_style;
		}

		if ( ! empty( $block_type->script ) ) {
			$script_handles[] = $block_type->script;
		}
	}

	$style_handles = array_unique( $style_handles );
	$done          = gc_styles()->done;

	ob_start();

	// We do not need reset styles for the iframed editor.
	gc_styles()->done = array( 'gc-reset-editor-styles' );
	gc_styles()->do_items( $style_handles );
	gc_styles()->done = $done;

	$styles = ob_get_clean();

	$script_handles = array_unique( $script_handles );
	$done           = gc_scripts()->done;

	ob_start();

	gc_scripts()->done = array();
	gc_scripts()->do_items( $script_handles );
	gc_scripts()->done = $done;

	$scripts = ob_get_clean();

	$editor_assets = gc_json_encode(
		array(
			'styles'  => $styles,
			'scripts' => $scripts,
		)
	);

	echo "<script>window.__editorAssets = $editor_assets</script>";
}

/**
 * Function that enqueues the CSS Custom Properties coming from theme.json.
 *
 *
 */
function gc_enqueue_global_styles_css_custom_properties() {
	gc_register_style( 'global-styles-css-custom-properties', false, array(), true, true );
	gc_add_inline_style( 'global-styles-css-custom-properties', gc_get_global_stylesheet( array( 'variables' ) ) );
	gc_enqueue_style( 'global-styles-css-custom-properties' );
}

/**
 * This function takes care of adding inline styles
 * in the proper place, depending on the theme in use.
 *
 * @since 5.9.1
 *
 * For block themes, it's loaded in the head.
 * For classic ones, it's loaded in the body
 * because the gc_head action happens before
 * the render_block.
 *
 * @link https://core.trac.wordpress.org/ticket/53494.
 *
 * @param string $style String containing the CSS styles to be added.
 */
function gc_enqueue_block_support_styles( $style ) {
	$action_hook_name = 'gc_footer';
	if ( gc_is_block_theme() ) {
		$action_hook_name = 'gc_head';
	}
	add_action(
		$action_hook_name,
		static function () use ( $style ) {
			echo "<style>$style</style>\n";
		}
	);
}

