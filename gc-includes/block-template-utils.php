<?php
/**
 * Utilities used to fetch and create templates and template parts.
 *
 * @package GeChiUI
 *
 */

// Define constants for supported gc_template_part_area taxonomy.
if ( ! defined( 'GC_TEMPLATE_PART_AREA_HEADER' ) ) {
	define( 'GC_TEMPLATE_PART_AREA_HEADER', 'header' );
}
if ( ! defined( 'GC_TEMPLATE_PART_AREA_FOOTER' ) ) {
	define( 'GC_TEMPLATE_PART_AREA_FOOTER', 'footer' );
}
if ( ! defined( 'GC_TEMPLATE_PART_AREA_SIDEBAR' ) ) {
	define( 'GC_TEMPLATE_PART_AREA_SIDEBAR', 'sidebar' );
}
if ( ! defined( 'GC_TEMPLATE_PART_AREA_UNCATEGORIZED' ) ) {
	define( 'GC_TEMPLATE_PART_AREA_UNCATEGORIZED', 'uncategorized' );
}

/**
 * For backward compatibility reasons,
 * block themes might be using block-templates or block-template-parts,
 * this function ensures we fallback to these folders properly.
 *
 *
 *
 * @param string $theme_stylesheet The stylesheet. Default is to leverage the main theme root.
 *
 * @return string[] {
 *     Folder names used by block themes.
 *
 *     @type string $gc_template      Theme-relative directory name for block templates.
 *     @type string $gc_template_part Theme-relative directory name for block template parts.
 * }
 */
function get_block_theme_folders( $theme_stylesheet = null ) {
	$theme_name = null === $theme_stylesheet ? get_stylesheet() : $theme_stylesheet;
	$root_dir   = get_theme_root( $theme_name );
	$theme_dir  = "$root_dir/$theme_name";

	if ( file_exists( $theme_dir . '/block-templates' ) || file_exists( $theme_dir . '/block-template-parts' ) ) {
		return array(
			'gc_template'      => 'block-templates',
			'gc_template_part' => 'block-template-parts',
		);
	}

	return array(
		'gc_template'      => 'templates',
		'gc_template_part' => 'parts',
	);
}

/**
 * Returns a filtered list of allowed area values for template parts.
 *
 *
 *
 * @return array The supported template part area values.
 */
function get_allowed_block_template_part_areas() {
	$default_area_definitions = array(
		array(
			'area'        => GC_TEMPLATE_PART_AREA_UNCATEGORIZED,
			'label'       => __( '常规' ),
			'description' => __(
				'常规模板通常执行特定的作用，如显示文章内容，而不与任何特定区域挂钩。'
			),
			'icon'        => 'layout',
			'area_tag'    => 'div',
		),
		array(
			'area'        => GC_TEMPLATE_PART_AREA_HEADER,
			'label'       => __( '页眉' ),
			'description' => __(
				'页眉模板定义一个页面的区域，通常包含标题、 logo 和主导航。'
			),
			'icon'        => 'header',
			'area_tag'    => 'header',
		),
		array(
			'area'        => GC_TEMPLATE_PART_AREA_FOOTER,
			'label'       => __( '页脚' ),
			'description' => __(
				'页脚模板定义一个页面的区域，通常包含站点信息、社交链接或额外区块的组合。'
			),
			'icon'        => 'footer',
			'area_tag'    => 'footer',
		),
	);

	/**
	 * Filters the list of allowed template part area values.
	 *
	 *
	 * @param array $default_area_definitions An array of supported area objects.
	 */
	return apply_filters( 'default_gc_template_part_areas', $default_area_definitions );
}


/**
 * Returns a filtered list of default template types, containing their
 * localized titles and descriptions.
 *
 *
 *
 * @return array The default template types.
 */
function get_default_block_template_types() {
	$default_template_types = array(
		'index'          => array(
			'title'       => _x( '索引', 'Template name' ),
			'description' => __( '显示文章。' ),
		),
		'home'           => array(
			'title'       => _x( '主页', 'Template name' ),
			'description' => __( '显示为站点主页，或在设置了静态主页时显示为“文章”页。' ),
		),
		'front-page'     => array(
			'title'       => _x( '首页', 'Template name' ),
			'description' => __( '显示站点主页。' ),
		),
		'singular'       => array(
			'title'       => _x( '单数', 'Template name' ),
			'description' => __( '显示单篇文章或页面。' ),
		),
		'single'         => array(
			'title'       => _x( '文章页面', 'Template name' ),
			'description' => __( '显示单篇文章。' ),
		),
		'page'           => array(
			'title'       => _x( '页面', 'Template name' ),
			'description' => __( '显示单个页面。' ),
		),
		'archive'        => array(
			'title'       => _x( '归档', 'Template name' ),
			'description' => __( '显示文章分类、标签及其他归档。' ),
		),
		'author'         => array(
			'title'       => _x( '作者', 'Template name' ),
			'description' => __( '显示某个作者撰写的最新文章。' ),
		),
		'category'       => array(
			'title'       => _x( '分类', 'Template name' ),
			'description' => __( '显示某个文章分类中的最新文章。' ),
		),
		'taxonomy'       => array(
			'title'       => _x( '分类法', 'Template name' ),
			'description' => __( '显示来自某个文章分类法的最新文章。' ),
		),
		'date'           => array(
			'title'       => _x( '日期', 'Template name' ),
			'description' => __( '显示特定日期的文章。' ),
		),
		'tag'            => array(
			'title'       => _x( '标签', 'Template name' ),
			'description' => __( '显示带有某个标签的最新文章。' ),
		),
		'attachment'     => array(
			'title'       => __( '媒体' ),
			'description' => __( '显示某个媒体项目或附件。' ),
		),
		'search'         => array(
			'title'       => _x( '搜索', 'Template name' ),
			'description' => __( '用于显示搜索结果的模板。' ),
		),
		'privacy-policy' => array(
			'title'       => __( '隐私政策' ),
			'description' => __( '显示隐私政策页面。' ),
		),
		'404'            => array(
			'title'       => _x( '404', 'Template name' ),
			'description' => __( '未找到内容时显示。' ),
		),
	);

	/**
	 * Filters the list of template types.
	 *
	 *
	 * @param array $default_template_types An array of template types, formatted as [ slug => [ title, description ] ].
	 */
	return apply_filters( 'default_template_types', $default_template_types );
}

/**
 * Checks whether the input 'area' is a supported value.
 * Returns the input if supported, otherwise returns the 'uncategorized' value.
 *
 *
 * @access private
 *
 * @param string $type Template part area name.
 *
 * @return string Input if supported, else the uncategorized value.
 */
function _filter_block_template_part_area( $type ) {
	$allowed_areas = array_map(
		static function ( $item ) {
			return $item['area'];
		},
		get_allowed_block_template_part_areas()
	);
	if ( in_array( $type, $allowed_areas, true ) ) {
		return $type;
	}

	$warning_message = sprintf(
		/* translators: %1$s: Template area type, %2$s: the uncategorized template area value. */
		__( '”%1$s”不是支持的gc_template_part区域值，已被添加为“%2$s”。' ),
		$type,
		GC_TEMPLATE_PART_AREA_UNCATEGORIZED
	);
	trigger_error( $warning_message, E_USER_NOTICE );
	return GC_TEMPLATE_PART_AREA_UNCATEGORIZED;
}

/**
 * Finds all nested template part file paths in a theme's directory.
 *
 *
 * @access private
 *
 * @param string $base_directory The theme's file path.
 * @return array A list of paths to all template part files.
 */
function _get_block_templates_paths( $base_directory ) {
	$path_list = array();
	if ( file_exists( $base_directory ) ) {
		$nested_files      = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $base_directory ) );
		$nested_html_files = new RegexIterator( $nested_files, '/^.+\.html$/i', RecursiveRegexIterator::GET_MATCH );
		foreach ( $nested_html_files as $path => $file ) {
			$path_list[] = $path;
		}
	}
	return $path_list;
}

/**
 * Retrieves the template file from the theme for a given slug.
 *
 *
 * @access private
 *
 * @param string $template_type 'gc_template' or 'gc_template_part'.
 * @param string $slug          Template slug.
 *
 * @return array|null Template.
 */
function _get_block_template_file( $template_type, $slug ) {
	if ( 'gc_template' !== $template_type && 'gc_template_part' !== $template_type ) {
		return null;
	}

	$themes = array(
		get_stylesheet() => get_stylesheet_directory(),
		get_template()   => get_template_directory(),
	);
	foreach ( $themes as $theme_slug => $theme_dir ) {
		$template_base_paths = get_block_theme_folders( $theme_slug );
		$file_path           = $theme_dir . '/' . $template_base_paths[ $template_type ] . '/' . $slug . '.html';
		if ( file_exists( $file_path ) ) {
			$new_template_item = array(
				'slug'  => $slug,
				'path'  => $file_path,
				'theme' => $theme_slug,
				'type'  => $template_type,
			);

			if ( 'gc_template_part' === $template_type ) {
				return _add_block_template_part_area_info( $new_template_item );
			}

			if ( 'gc_template' === $template_type ) {
				return _add_block_template_info( $new_template_item );
			}

			return $new_template_item;
		}
	}

	return null;
}

/**
 * Retrieves the template files from the theme.
 *
 *
 * @access private
 *
 * @param string $template_type 'gc_template' or 'gc_template_part'.
 *
 * @return array Template.
 */
function _get_block_templates_files( $template_type ) {
	if ( 'gc_template' !== $template_type && 'gc_template_part' !== $template_type ) {
		return null;
	}

	$themes         = array(
		get_stylesheet() => get_stylesheet_directory(),
		get_template()   => get_template_directory(),
	);
	$template_files = array();
	foreach ( $themes as $theme_slug => $theme_dir ) {
		$template_base_paths  = get_block_theme_folders( $theme_slug );
		$theme_template_files = _get_block_templates_paths( $theme_dir . '/' . $template_base_paths[ $template_type ] );
		foreach ( $theme_template_files as $template_file ) {
			$template_base_path = $template_base_paths[ $template_type ];
			$template_slug      = substr(
				$template_file,
				// Starting position of slug.
				strpos( $template_file, $template_base_path . DIRECTORY_SEPARATOR ) + 1 + strlen( $template_base_path ),
				// Subtract ending '.html'.
				-5
			);
			$new_template_item = array(
				'slug'  => $template_slug,
				'path'  => $template_file,
				'theme' => $theme_slug,
				'type'  => $template_type,
			);

			if ( 'gc_template_part' === $template_type ) {
				$template_files[] = _add_block_template_part_area_info( $new_template_item );
			}

			if ( 'gc_template' === $template_type ) {
				$template_files[] = _add_block_template_info( $new_template_item );
			}
		}
	}

	return $template_files;
}

/**
 * Attempts to add custom template information to the template item.
 *
 *
 * @access private
 *
 * @param array $template_item Template to add information to (requires 'slug' field).
 * @return array Template item.
 */
function _add_block_template_info( $template_item ) {
	if ( ! GC_Theme_JSON_Resolver::theme_has_support() ) {
		return $template_item;
	}

	$theme_data = GC_Theme_JSON_Resolver::get_theme_data()->get_custom_templates();
	if ( isset( $theme_data[ $template_item['slug'] ] ) ) {
		$template_item['title']     = $theme_data[ $template_item['slug'] ]['title'];
		$template_item['postTypes'] = $theme_data[ $template_item['slug'] ]['postTypes'];
	}

	return $template_item;
}

/**
 * Attempts to add the template part's area information to the input template.
 *
 *
 * @access private
 *
 * @param array $template_info Template to add information to (requires 'type' and 'slug' fields).
 *
 * @return array Template info.
 */
function _add_block_template_part_area_info( $template_info ) {
	if ( GC_Theme_JSON_Resolver::theme_has_support() ) {
		$theme_data = GC_Theme_JSON_Resolver::get_theme_data()->get_template_parts();
	}

	if ( isset( $theme_data[ $template_info['slug'] ]['area'] ) ) {
		$template_info['title'] = $theme_data[ $template_info['slug'] ]['title'];
		$template_info['area']  = _filter_block_template_part_area( $theme_data[ $template_info['slug'] ]['area'] );
	} else {
		$template_info['area'] = GC_TEMPLATE_PART_AREA_UNCATEGORIZED;
	}

	return $template_info;
}

/**
 * Returns an array containing the references of
 * the passed blocks and their inner blocks.
 *
 *
 * @access private
 *
 * @param array $blocks array of blocks.
 *
 * @return array block references to the passed blocks and their inner blocks.
 */
function _flatten_blocks( &$blocks ) {
	$all_blocks = array();
	$queue      = array();
	foreach ( $blocks as &$block ) {
		$queue[] = &$block;
	}

	while ( count( $queue ) > 0 ) {
		$block = &$queue[0];
		array_shift( $queue );
		$all_blocks[] = &$block;

		if ( ! empty( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as &$inner_block ) {
				$queue[] = &$inner_block;
			}
		}
	}

	return $all_blocks;
}

/**
 * Parses gc_template content and injects the active theme's
 * stylesheet as a theme attribute into each gc_template_part
 *
 *
 * @access private
 *
 * @param string $template_content serialized gc_template content.
 *
 * @return string Updated 'gc_template' content.
 */
function _inject_theme_attribute_in_block_template_content( $template_content ) {
	$has_updated_content = false;
	$new_content         = '';
	$template_blocks     = parse_blocks( $template_content );

	$blocks = _flatten_blocks( $template_blocks );
	foreach ( $blocks as &$block ) {
		if (
			'core/template-part' === $block['blockName'] &&
			! isset( $block['attrs']['theme'] )
		) {
			$block['attrs']['theme'] = gc_get_theme()->get_stylesheet();
			$has_updated_content     = true;
		}
	}

	if ( $has_updated_content ) {
		foreach ( $template_blocks as &$block ) {
			$new_content .= serialize_block( $block );
		}

		return $new_content;
	}

	return $template_content;
}

/**
 * Parses a block template and removes the theme attribute from each template part.
 *
 *
 * @access private
 *
 * @param string $template_content Serialized block template content.
 * @return string Updated block template content.
 */
function _remove_theme_attribute_in_block_template_content( $template_content ) {
	$has_updated_content = false;
	$new_content         = '';
	$template_blocks     = parse_blocks( $template_content );

	$blocks = _flatten_blocks( $template_blocks );
	foreach ( $blocks as $key => $block ) {
		if ( 'core/template-part' === $block['blockName'] && isset( $block['attrs']['theme'] ) ) {
			unset( $blocks[ $key ]['attrs']['theme'] );
			$has_updated_content = true;
		}
	}

	if ( ! $has_updated_content ) {
		return $template_content;
	}

	foreach ( $template_blocks as $block ) {
		$new_content .= serialize_block( $block );
	}

	return $new_content;
}

/**
 * Build a unified template object based on a theme file.
 *
 *
 * @access private
 *
 * @param array  $template_file Theme file.
 * @param string $template_type 'gc_template' or 'gc_template_part'.
 *
 * @return GC_Block_Template Template.
 */
function _build_block_template_result_from_file( $template_file, $template_type ) {
	$default_template_types = get_default_block_template_types();
	$template_content       = file_get_contents( $template_file['path'] );
	$theme                  = gc_get_theme()->get_stylesheet();

	$template                 = new GC_Block_Template();
	$template->id             = $theme . '//' . $template_file['slug'];
	$template->theme          = $theme;
	$template->content        = _inject_theme_attribute_in_block_template_content( $template_content );
	$template->slug           = $template_file['slug'];
	$template->source         = 'theme';
	$template->type           = $template_type;
	$template->title          = ! empty( $template_file['title'] ) ? $template_file['title'] : $template_file['slug'];
	$template->status         = 'publish';
	$template->has_theme_file = true;
	$template->is_custom      = true;

	if ( 'gc_template' === $template_type && isset( $default_template_types[ $template_file['slug'] ] ) ) {
		$template->description = $default_template_types[ $template_file['slug'] ]['description'];
		$template->title       = $default_template_types[ $template_file['slug'] ]['title'];
		$template->is_custom   = false;
	}

	if ( 'gc_template' === $template_type && isset( $template_file['postTypes'] ) ) {
		$template->post_types = $template_file['postTypes'];
	}

	if ( 'gc_template_part' === $template_type && isset( $template_file['area'] ) ) {
		$template->area = $template_file['area'];
	}

	return $template;
}

/**
 * Build a unified template object based a post Object.
 *
 *
 * @access private
 *
 * @param GC_Post $post Template post.
 *
 * @return GC_Block_Template|GC_Error Template.
 */
function _build_block_template_result_from_post( $post ) {
	$default_template_types = get_default_block_template_types();
	$terms                  = get_the_terms( $post, 'gc_theme' );

	if ( is_gc_error( $terms ) ) {
		return $terms;
	}

	if ( ! $terms ) {
		return new GC_Error( 'template_missing_theme', __( '没有为此模板定义主题。' ) );
	}

	$theme          = $terms[0]->name;
	$has_theme_file = gc_get_theme()->get_stylesheet() === $theme &&
		null !== _get_block_template_file( $post->post_type, $post->post_name );

	$origin = get_post_meta( $post->ID, 'origin', true );

	$template                 = new GC_Block_Template();
	$template->gc_id          = $post->ID;
	$template->id             = $theme . '//' . $post->post_name;
	$template->theme          = $theme;
	$template->content        = $post->post_content;
	$template->slug           = $post->post_name;
	$template->source         = 'custom';
	$template->origin         = ! empty( $origin ) ? $origin : null;
	$template->type           = $post->post_type;
	$template->description    = $post->post_excerpt;
	$template->title          = $post->post_title;
	$template->status         = $post->post_status;
	$template->has_theme_file = $has_theme_file;
	$template->is_custom      = true;
	$template->author         = $post->post_author;

	if ( 'gc_template' === $post->post_type && isset( $default_template_types[ $template->slug ] ) ) {
		$template->is_custom = false;
	}

	if ( 'gc_template_part' === $post->post_type ) {
		$type_terms = get_the_terms( $post, 'gc_template_part_area' );
		if ( ! is_gc_error( $type_terms ) && false !== $type_terms ) {
			$template->area = $type_terms[0]->name;
		}
	}

	return $template;
}

/**
 * Retrieves a list of unified template objects based on a query.
 *
 *
 *
 * @param array  $query {
 *     Optional. Arguments to retrieve templates.
 *
 *     @type array  $slug__in  List of slugs to include.
 *     @type int    $gc_id     Post ID of customized template.
 *     @type string $area      A 'gc_template_part_area' taxonomy value to filter by (for gc_template_part template type only).
 *     @type string $post_type 要获取模板的文章类型。
 * }
 * @param string $template_type 'gc_template' or 'gc_template_part'.
 *
 * @return array Templates.
 */
function get_block_templates( $query = array(), $template_type = 'gc_template' ) {
	/**
	 * Filters the block templates array before the query takes place.
	 *
	 * Return a non-null value to bypass the GeChiUI queries.
	 *
	 *
	 * @param GC_Block_Template[]|null $block_templates Return an array of block templates to short-circuit the default query,
	 *                                                  or null to allow GC to run it's normal queries.
	 * @param array  $query {
	 *     Optional. Arguments to retrieve templates.
	 *
	 *     @type array  $slug__in List of slugs to include.
	 *     @type int    $gc_id Post ID of customized template.
	 *     @type string $post_type 要获取模板的文章类型。
	 * }
	 * @param string $template_type gc_template or gc_template_part.
	 */
	$templates = apply_filters( 'pre_get_block_templates', null, $query, $template_type );
	if ( ! is_null( $templates ) ) {
		return $templates;
	}

	$post_type     = isset( $query['post_type'] ) ? $query['post_type'] : '';
	$gc_query_args = array(
		'post_status'    => array( 'auto-draft', 'draft', 'publish' ),
		'post_type'      => $template_type,
		'posts_per_page' => -1,
		'no_found_rows'  => true,
		'tax_query'      => array(
			array(
				'taxonomy' => 'gc_theme',
				'field'    => 'name',
				'terms'    => gc_get_theme()->get_stylesheet(),
			),
		),
	);

	if ( 'gc_template_part' === $template_type && isset( $query['area'] ) ) {
		$gc_query_args['tax_query'][]           = array(
			'taxonomy' => 'gc_template_part_area',
			'field'    => 'name',
			'terms'    => $query['area'],
		);
		$gc_query_args['tax_query']['relation'] = 'AND';
	}

	if ( isset( $query['slug__in'] ) ) {
		$gc_query_args['post_name__in'] = $query['slug__in'];
	}

	// This is only needed for the regular templates/template parts post type listing and editor.
	if ( isset( $query['gc_id'] ) ) {
		$gc_query_args['p'] = $query['gc_id'];
	} else {
		$gc_query_args['post_status'] = 'publish';
	}

	$template_query = new GC_Query( $gc_query_args );
	$query_result   = array();
	foreach ( $template_query->posts as $post ) {
		$template = _build_block_template_result_from_post( $post );

		if ( is_gc_error( $template ) ) {
			continue;
		}

		if ( $post_type && ! $template->is_custom ) {
			continue;
		}

		$query_result[] = $template;
	}

	if ( ! isset( $query['gc_id'] ) ) {
		$template_files = _get_block_templates_files( $template_type );
		foreach ( $template_files as $template_file ) {
			$template = _build_block_template_result_from_file( $template_file, $template_type );

			if ( $post_type && ! $template->is_custom ) {
				continue;
			}

			if ( $post_type &&
				isset( $template->post_types ) &&
				! in_array( $post_type, $template->post_types, true )
			) {
				continue;
			}

			$is_not_custom   = false === array_search(
				gc_get_theme()->get_stylesheet() . '//' . $template_file['slug'],
				array_column( $query_result, 'id' ),
				true
			);
			$fits_slug_query =
				! isset( $query['slug__in'] ) || in_array( $template_file['slug'], $query['slug__in'], true );
			$fits_area_query =
				! isset( $query['area'] ) || $template_file['area'] === $query['area'];
			$should_include  = $is_not_custom && $fits_slug_query && $fits_area_query;
			if ( $should_include ) {
				$query_result[] = $template;
			}
		}
	}

	/**
	 * Filters the array of queried block templates array after they've been fetched.
	 *
	 *
	 * @param GC_Block_Template[] $query_result Array of found block templates.
	 * @param array  $query {
	 *     Optional. Arguments to retrieve templates.
	 *
	 *     @type array  $slug__in List of slugs to include.
	 *     @type int    $gc_id Post ID of customized template.
	 * }
	 * @param string $template_type gc_template or gc_template_part.
	 */
	return apply_filters( 'get_block_templates', $query_result, $query, $template_type );
}

/**
 * Retrieves a single unified template object using its id.
 *
 *
 *
 * @param string $id            Template unique identifier (example: theme_slug//template_slug).
 * @param string $template_type Optional. Template type: `'gc_template'` or '`gc_template_part'`.
 *                              Default `'gc_template'`.
 *
 * @return GC_Block_Template|null Template.
 */
function get_block_template( $id, $template_type = 'gc_template' ) {
	/**
	 *Filters the block template object before the query takes place.
	 *
	 * Return a non-null value to bypass the GeChiUI queries.
	 *
	 *
	 * @param GC_Block_Template|null $block_template Return block template object to short-circuit the default query,
	 *                                               or null to allow GC to run its normal queries.
	 * @param string $id                             Template unique identifier (example: theme_slug//template_slug).
	 * @param string $template_type                  Template type: `'gc_template'` or '`gc_template_part'`.
	 */
	$block_template = apply_filters( 'pre_get_block_template', null, $id, $template_type );
	if ( ! is_null( $block_template ) ) {
		return $block_template;
	}

	$parts = explode( '//', $id, 2 );
	if ( count( $parts ) < 2 ) {
		return null;
	}
	list( $theme, $slug ) = $parts;
	$gc_query_args        = array(
		'post_name__in'  => array( $slug ),
		'post_type'      => $template_type,
		'post_status'    => array( 'auto-draft', 'draft', 'publish', 'trash' ),
		'posts_per_page' => 1,
		'no_found_rows'  => true,
		'tax_query'      => array(
			array(
				'taxonomy' => 'gc_theme',
				'field'    => 'name',
				'terms'    => $theme,
			),
		),
	);
	$template_query       = new GC_Query( $gc_query_args );
	$posts                = $template_query->posts;

	if ( count( $posts ) > 0 ) {
		$template = _build_block_template_result_from_post( $posts[0] );

		if ( ! is_gc_error( $template ) ) {
			return $template;
		}
	}

	$block_template = get_block_file_template( $id, $template_type );

	/**
	 * Filters the queried block template object after it's been fetched.
	 *
	 *
	 * @param GC_Block_Template|null $block_template The found block template, or null if there isn't one.
	 * @param string                 $id             Template unique identifier (example: theme_slug//template_slug).
	 * @param array                  $template_type  Template type: `'gc_template'` or '`gc_template_part'`.
	 */
	return apply_filters( 'get_block_template', $block_template, $id, $template_type );
}

/**
 * Retrieves a single unified template object using its id.
 *
 *
 *
 * @param string $id            Template unique identifier (example: theme_slug//template_slug).
 * @param string $template_type Optional. Template type: `'gc_template'` or '`gc_template_part'`.
 *                              Default `'gc_template'`.
 * @return GC_Block_Template|null The found block template, or null if there isn't one.
 */
function get_block_file_template( $id, $template_type = 'gc_template' ) {
	/**
	 * Filters the block templates array before the query takes place.
	 *
	 * Return a non-null value to bypass the GeChiUI queries.
	 *
	 *
	 * @param GC_Block_Template|null $block_template Return block template object to short-circuit the default query,
	 *                                               or null to allow GC to run its normal queries.
	 * @param string                 $id             Template unique identifier (example: theme_slug//template_slug).
	 * @param string                 $template_type  Template type: `'gc_template'` or '`gc_template_part'`.
	 */
	$block_template = apply_filters( 'pre_get_block_file_template', null, $id, $template_type );
	if ( ! is_null( $block_template ) ) {
		return $block_template;
	}

	$parts = explode( '//', $id, 2 );
	if ( count( $parts ) < 2 ) {
		/** This filter is documented in gc-includes/block-template-utils.php */
		return apply_filters( 'get_block_file_template', null, $id, $template_type );
	}
	list( $theme, $slug ) = $parts;

	if ( gc_get_theme()->get_stylesheet() !== $theme ) {
		/** This filter is documented in gc-includes/block-template-utils.php */
		return apply_filters( 'get_block_file_template', null, $id, $template_type );
	}

	$template_file = _get_block_template_file( $template_type, $slug );
	if ( null === $template_file ) {
		/** This filter is documented in gc-includes/block-template-utils.php */
		return apply_filters( 'get_block_file_template', null, $id, $template_type );
	}

	$block_template = _build_block_template_result_from_file( $template_file, $template_type );

	/**
	 * Filters the array of queried block templates array after they've been fetched.
	 *
	 *
	 * @param GC_Block_Template|null $block_template The found block template, or null if there is none.
	 * @param string                 $id             Template unique identifier (example: theme_slug//template_slug).
	 * @param string                 $template_type  Template type: `'gc_template'` or '`gc_template_part'`.
	 */
	return apply_filters( 'get_block_file_template', $block_template, $id, $template_type );
}

/**
 * Print a template-part.
 *
 *
 *
 * @param string $part The template-part to print. Use "header" or "footer".
 */
function block_template_part( $part ) {
	$template_part = get_block_template( get_stylesheet() . '//' . $part, 'gc_template_part' );
	if ( ! $template_part || empty( $template_part->content ) ) {
		return;
	}
	echo do_blocks( $template_part->content );
}

/**
 * Print the header template-part.
 *
 *
 */
function block_header_area() {
	block_template_part( 'header' );
}

/**
 * Print the footer template-part.
 *
 *
 */
function block_footer_area() {
	block_template_part( 'footer' );
}

/**
 * Creates an export of the current templates and
 * template parts from the site editor at the
 * specified path in a ZIP file.
 *
 *
 *
 * @return GC_Error|string Path of the ZIP file or error on failure.
 */
function gc_generate_block_templates_export_file() {
	if ( ! class_exists( 'ZipArchive' ) ) {
		return new GC_Error( 'missing_zip_package', __( '不支持 Zip 导出。' ) );
	}

	$obscura  = gc_generate_password( 12, false, false );
	$filename = get_temp_dir() . 'edit-site-export-' . $obscura . '.zip';

	$zip = new ZipArchive();
	if ( true !== $zip->open( $filename, ZipArchive::CREATE ) ) {
		return new GC_Error( 'unable_to_create_zip', __( '无法打开导出文件（归档）进行写入。' ) );
	}

	$zip->addEmptyDir( 'theme' );
	$zip->addEmptyDir( 'theme/templates' );
	$zip->addEmptyDir( 'theme/parts' );

	// Load templates into the zip file.
	$templates = get_block_templates();
	foreach ( $templates as $template ) {
		$template->content = _remove_theme_attribute_in_block_template_content( $template->content );

		$zip->addFromString(
			'theme/templates/' . $template->slug . '.html',
			$template->content
		);
	}

	// Load template parts into the zip file.
	$template_parts = get_block_templates( array(), 'gc_template_part' );
	foreach ( $template_parts as $template_part ) {
		$zip->addFromString(
			'theme/parts/' . $template_part->slug . '.html',
			$template_part->content
		);
	}

	// Save changes to the zip file.
	$zip->close();

	return $filename;
}
