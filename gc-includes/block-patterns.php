<?php
/**
 * Register the block patterns and block patterns categories
 *
 * @package GeChiUI
 * @since 5.5.0
 */

add_theme_support( 'core-block-patterns' );

/**
 * Registers the core block patterns and categories.
 *
 * @since 5.5.0
 * @since 6.3.0 Added source to core block patterns.
 * @access private
 */
function _register_core_block_patterns_and_categories() {
	$should_register_core_patterns = get_theme_support( 'core-block-patterns' );

	if ( $should_register_core_patterns ) {
		$core_block_patterns = array(
			'query-standard-posts',
			'query-medium-posts',
			'query-small-posts',
			'query-grid-posts',
			'query-large-title-posts',
			'query-offset-posts',
			'social-links-shared-background-color',
		);

		foreach ( $core_block_patterns as $core_block_pattern ) {
			$pattern           = require __DIR__ . '/block-patterns/' . $core_block_pattern . '.php';
			$pattern['source'] = 'core';
			register_block_pattern( 'core/' . $core_block_pattern, $pattern );
		}
	}

	register_block_pattern_category( 'banner', array( 'label' => _x( '横幅', 'Block pattern category' ) ) );
	register_block_pattern_category(
		'buttons',
		array(
			'label'       => _x( '多个按钮', 'Block pattern category' ),
			'description' => __( '包含按钮和号召性用语的样板。' ),
		)
	);
	register_block_pattern_category(
		'columns',
		array(
			'label'       => _x( '栏目', 'Block pattern category' ),
			'description' => __( '具有更多复杂布局的多列样板。' ),
		)
	);
	register_block_pattern_category(
		'text',
		array(
			'label'       => _x( '文字', 'Block pattern category' ),
			'description' => __( '主要包含文本的样板。' ),
		)
	);
	register_block_pattern_category(
		'query',
		array(
			'label'       => _x( '搜索', 'Block pattern category' ),
			'description' => __( '以列表、网格或其他布局显示您搜索的文章。' ),
		)
	);
	register_block_pattern_category(
		'featured',
		array(
			'label'       => _x( '特色', 'Block pattern category' ),
			'description' => __( '一套精心挑选的高质量样板。' ),
		)
	);
	register_block_pattern_category(
		'call-to-action',
		array(
			'label'       => _x( '行为召唤', 'Block pattern category' ),
			'description' => __( '用于触发特定操作的部分。' ),
		)
	);
	register_block_pattern_category(
		'team',
		array(
			'label'       => _x( '团队', 'Block pattern category' ),
			'description' => __( '展示您团队成员的多种设计。' ),
		)
	);
	register_block_pattern_category(
		'testimonials',
		array(
			'label'       => _x( '用户评价', 'Block pattern category' ),
			'description' => __( '分享有关您品牌或业务的评论和反馈。' ),
		)
	);
	register_block_pattern_category(
		'services',
		array(
			'label'       => _x( '服务项目', 'Block pattern category' ),
			'description' => __( '简要描述您的业务内容，以及您可以提供哪些帮助。' ),
		)
	);
	register_block_pattern_category(
		'contact',
		array(
			'label'       => _x( '联系方式', 'Block pattern category' ),
			'description' => __( '显示您的联系信息。' ),
		)
	);
	register_block_pattern_category(
		'about',
		array(
			'label'       => _x( '关于我们', 'Block pattern category' ),
			'description' => __( '自我介绍。' ),
		)
	);
	register_block_pattern_category(
		'portfolio',
		array(
			'label'       => _x( '作品集', 'Block pattern category' ),
			'description' => __( '展示您的最新作品。' ),
		)
	);
	register_block_pattern_category(
		'gallery',
		array(
			'label'       => _x( '图库', 'Block pattern category' ),
			'description' => __( '用于显示图片的不同排版布局。' ),
		)
	);
	register_block_pattern_category(
		'media',
		array(
			'label'       => _x( '媒体', 'Block pattern category' ),
			'description' => __( '包含视频或音频的不同布局。' ),
		)
	);
	register_block_pattern_category(
		'posts',
		array(
			'label'       => _x( '文章', 'Block pattern category' ),
			'description' => __( '以列表、网格或其他布局显示您的最新文章。' ),
		)
	);
	register_block_pattern_category(
		'footer',
		array(
			'label'       => _x( '页脚', 'Block pattern category' ),
			'description' => __( '显示系统信息和系统导航的多种页脚设计。' ),
		)
	);
	register_block_pattern_category(
		'header',
		array(
			'label'       => _x( '页眉', 'Block pattern category' ),
			'description' => __( '显示系统标题和系统导航的多种页眉设计。' ),
		)
	);
}

/**
 * Normalize the pattern properties to camelCase.
 *
 * The API's format is snake_case, `register_block_pattern()` expects camelCase.
 *
 * @since 6.2.0
 * @access private
 *
 * @param array $pattern Pattern as returned from the Pattern Directory API.
 * @return array Normalized pattern.
 */
function gc_normalize_remote_block_pattern( $pattern ) {
	if ( isset( $pattern['block_types'] ) ) {
		$pattern['blockTypes'] = $pattern['block_types'];
		unset( $pattern['block_types'] );
	}

	if ( isset( $pattern['viewport_width'] ) ) {
		$pattern['viewportWidth'] = $pattern['viewport_width'];
		unset( $pattern['viewport_width'] );
	}

	return (array) $pattern;
}

/**
 * Register Core's official patterns from www.gechiui.com/patterns.
 *
 * @since 5.8.0
 * @since 5.9.0 The $current_screen argument was removed.
 * @since 6.2.0 Normalize the pattern from the API (snake_case) to the
 *              format expected by `register_block_pattern` (camelCase).
 * @since 6.3.0 Add 'pattern-directory/core' to the pattern's 'source'.
 *
 * @param GC_Screen $deprecated Unused. Formerly the screen that the current request was triggered from.
 */
function _load_remote_block_patterns( $deprecated = null ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '5.9.0' );
		$current_screen = $deprecated;
		if ( ! $current_screen->is_block_editor ) {
			return;
		}
	}

	$supports_core_patterns = get_theme_support( 'core-block-patterns' );

	/**
	 * Filter to disable remote block patterns.
	 *
	 * @since 5.8.0
	 *
	 * @param bool $should_load_remote
	 */
	$should_load_remote = apply_filters( 'should_load_remote_block_patterns', true );

	if ( $supports_core_patterns && $should_load_remote ) {
		$request         = new GC_REST_Request( 'GET', '/gc/v2/pattern-directory/patterns' );
		$core_keyword_id = 11; // 11 is the ID for "core".
		$request->set_param( 'keyword', $core_keyword_id );
		$response = rest_do_request( $request );
		if ( $response->is_error() ) {
			return;
		}
		$patterns = $response->get_data();

		foreach ( $patterns as $pattern ) {
			$pattern['source']  = 'pattern-directory/core';
			$normalized_pattern = gc_normalize_remote_block_pattern( $pattern );
			$pattern_name       = 'core/' . sanitize_title( $normalized_pattern['title'] );
			register_block_pattern( $pattern_name, $normalized_pattern );
		}
	}
}

/**
 * Register `Featured` (category) patterns from www.gechiui.com/patterns.
 *
 * @since 5.9.0
 * @since 6.2.0 Normalized the pattern from the API (snake_case) to the
 *              format expected by `register_block_pattern()` (camelCase).
 * @since 6.3.0 Add 'pattern-directory/featured' to the pattern's 'source'.
 */
function _load_remote_featured_patterns() {
	$supports_core_patterns = get_theme_support( 'core-block-patterns' );

	/** This filter is documented in gc-includes/block-patterns.php */
	$should_load_remote = apply_filters( 'should_load_remote_block_patterns', true );

	if ( ! $should_load_remote || ! $supports_core_patterns ) {
		return;
	}

	$request         = new GC_REST_Request( 'GET', '/gc/v2/pattern-directory/patterns' );
	$featured_cat_id = 26; // This is the `Featured` category id from pattern directory.
	$request->set_param( 'category', $featured_cat_id );
	$response = rest_do_request( $request );
	if ( $response->is_error() ) {
		return;
	}
	$patterns = $response->get_data();
	$registry = GC_Block_Patterns_Registry::get_instance();
	foreach ( $patterns as $pattern ) {
		$pattern['source']  = 'pattern-directory/featured';
		$normalized_pattern = gc_normalize_remote_block_pattern( $pattern );
		$pattern_name       = sanitize_title( $normalized_pattern['title'] );
		// Some patterns might be already registered as core patterns with the `core` prefix.
		$is_registered = $registry->is_registered( $pattern_name ) || $registry->is_registered( "core/$pattern_name" );
		if ( ! $is_registered ) {
			register_block_pattern( $pattern_name, $normalized_pattern );
		}
	}
}

/**
 * Registers patterns from Pattern Directory provided by a theme's
 * `theme.json` file.
 *
 * @since 6.0.0
 * @since 6.2.0 Normalized the pattern from the API (snake_case) to the
 *              format expected by `register_block_pattern()` (camelCase).
 * @since 6.3.0 Add 'pattern-directory/theme' to the pattern's 'source'.
 * @access private
 */
function _register_remote_theme_patterns() {
	/** This filter is documented in gc-includes/block-patterns.php */
	if ( ! apply_filters( 'should_load_remote_block_patterns', true ) ) {
		return;
	}

	if ( ! gc_theme_has_theme_json() ) {
		return;
	}

	$pattern_settings = gc_get_theme_directory_pattern_slugs();
	if ( empty( $pattern_settings ) ) {
		return;
	}

	$request         = new GC_REST_Request( 'GET', '/gc/v2/pattern-directory/patterns' );
	$request['slug'] = $pattern_settings;
	$response        = rest_do_request( $request );
	if ( $response->is_error() ) {
		return;
	}
	$patterns          = $response->get_data();
	$patterns_registry = GC_Block_Patterns_Registry::get_instance();
	foreach ( $patterns as $pattern ) {
		$pattern['source']  = 'pattern-directory/theme';
		$normalized_pattern = gc_normalize_remote_block_pattern( $pattern );
		$pattern_name       = sanitize_title( $normalized_pattern['title'] );
		// Some patterns might be already registered as core patterns with the `core` prefix.
		$is_registered = $patterns_registry->is_registered( $pattern_name ) || $patterns_registry->is_registered( "core/$pattern_name" );
		if ( ! $is_registered ) {
			register_block_pattern( $pattern_name, $normalized_pattern );
		}
	}
}

/**
 * Register any patterns that the active theme may provide under its
 * `./patterns/` directory. Each pattern is defined as a PHP file and defines
 * its metadata using plugin-style headers. The minimum required definition is:
 *
 *     /**
 *      * Title: My Pattern
 *      * Slug: my-theme/my-pattern
 *      *
 * The output of the PHP source corresponds to the content of the pattern, e.g.:
 *
 *     <main><p><?php echo "Hello"; ?></p></main>
 *
 * If applicable, this will collect from both parent and child theme.
 *
 * Other settable fields include:
 *
 *   - Description
 *   - Viewport Width
 *   - Inserter         (yes/no)
 *   - Categories       (comma-separated values)
 *   - Keywords         (comma-separated values)
 *   - Block Types      (comma-separated values)
 *   - Post Types       (comma-separated values)
 *   - Template Types   (comma-separated values)
 *
 * @since 6.0.0
 * @since 6.1.0 The `postTypes` property was added.
 * @since 6.2.0 The `templateTypes` property was added.
 * @access private
 */
function _register_theme_block_patterns() {
	$default_headers = array(
		'title'         => 'Title',
		'slug'          => 'Slug',
		'description'   => 'Description',
		'viewportWidth' => 'Viewport Width',
		'inserter'      => 'Inserter',
		'categories'    => 'Categories',
		'keywords'      => 'Keywords',
		'blockTypes'    => 'Block Types',
		'postTypes'     => 'Post Types',
		'templateTypes' => 'Template Types',
	);

	/*
	 * Register patterns for the active theme. If the theme is a child theme,
	 * let it override any patterns from the parent theme that shares the same slug.
	 */
	$themes     = array();
	$stylesheet = get_stylesheet();
	$template   = get_template();
	if ( $stylesheet !== $template ) {
		$themes[] = gc_get_theme( $stylesheet );
	}
	$themes[] = gc_get_theme( $template );

	foreach ( $themes as $theme ) {
		$dirpath = $theme->get_stylesheet_directory() . '/patterns/';
		if ( ! is_dir( $dirpath ) || ! is_readable( $dirpath ) ) {
			continue;
		}
		if ( file_exists( $dirpath ) ) {
			$files = glob( $dirpath . '*.php' );
			if ( $files ) {
				foreach ( $files as $file ) {
					$pattern_data = get_file_data( $file, $default_headers );

					if ( empty( $pattern_data['slug'] ) ) {
						_doing_it_wrong(
							'_register_theme_block_patterns',
							sprintf(
								/* translators: %s: file name. */
								__( '无法注册文件“%s”为区块样板（缺少“别名”字段）' ),
								$file
							),
							'6.0.0'
						);
						continue;
					}

					if ( ! preg_match( '/^[A-z0-9\/_-]+$/', $pattern_data['slug'] ) ) {
						_doing_it_wrong(
							'_register_theme_block_patterns',
							sprintf(
								/* translators: %1s: file name; %2s: slug value found. */
								__( '无法注册文件“%1$s”为区块样板（无效的别名“%2$s”）' ),
								$file,
								$pattern_data['slug']
							),
							'6.0.0'
						);
					}

					if ( GC_Block_Patterns_Registry::get_instance()->is_registered( $pattern_data['slug'] ) ) {
						continue;
					}

					// Title is a required property.
					if ( ! $pattern_data['title'] ) {
						_doing_it_wrong(
							'_register_theme_block_patterns',
							sprintf(
								/* translators: %1s: file name; %2s: slug value found. */
								__( '无法注册文件“%s”为区块样板（缺少“标题”字段）' ),
								$file
							),
							'6.0.0'
						);
						continue;
					}

					// For properties of type array, parse data as comma-separated.
					foreach ( array( 'categories', 'keywords', 'blockTypes', 'postTypes', 'templateTypes' ) as $property ) {
						if ( ! empty( $pattern_data[ $property ] ) ) {
							$pattern_data[ $property ] = array_filter(
								preg_split(
									'/[\s,]+/',
									(string) $pattern_data[ $property ]
								)
							);
						} else {
							unset( $pattern_data[ $property ] );
						}
					}

					// Parse properties of type int.
					foreach ( array( 'viewportWidth' ) as $property ) {
						if ( ! empty( $pattern_data[ $property ] ) ) {
							$pattern_data[ $property ] = (int) $pattern_data[ $property ];
						} else {
							unset( $pattern_data[ $property ] );
						}
					}

					// Parse properties of type bool.
					foreach ( array( 'inserter' ) as $property ) {
						if ( ! empty( $pattern_data[ $property ] ) ) {
							$pattern_data[ $property ] = in_array(
								strtolower( $pattern_data[ $property ] ),
								array( 'yes', 'true' ),
								true
							);
						} else {
							unset( $pattern_data[ $property ] );
						}
					}

					// Translate the pattern metadata.
					$text_domain = $theme->get( 'TextDomain' );
					//phpcs:ignore GeChiUI.GC.I18n.NonSingularStringLiteralText, GeChiUI.GC.I18n.NonSingularStringLiteralContext, GeChiUI.GC.I18n.NonSingularStringLiteralDomain, GeChiUI.GC.I18n.LowLevelTranslationFunction
					$pattern_data['title'] = translate_with_gettext_context( $pattern_data['title'], 'Pattern title', $text_domain );
					if ( ! empty( $pattern_data['description'] ) ) {
						//phpcs:ignore GeChiUI.GC.I18n.NonSingularStringLiteralText, GeChiUI.GC.I18n.NonSingularStringLiteralContext, GeChiUI.GC.I18n.NonSingularStringLiteralDomain, GeChiUI.GC.I18n.LowLevelTranslationFunction
						$pattern_data['description'] = translate_with_gettext_context( $pattern_data['description'], 'Pattern description', $text_domain );
					}

					// The actual pattern content is the output of the file.
					ob_start();
					include $file;
					$pattern_data['content'] = ob_get_clean();
					if ( ! $pattern_data['content'] ) {
						continue;
					}

					register_block_pattern( $pattern_data['slug'], $pattern_data );
				}
			}
		}
	}
}
add_action( 'init', '_register_theme_block_patterns' );
