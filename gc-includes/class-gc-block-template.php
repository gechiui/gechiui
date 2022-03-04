<?php
/**
 * Blocks API: GC_Block_Template class
 *
 * @package GeChiUI
 *
 */

/**
 * Class representing a block template.
 *
 *
 */
class GC_Block_Template {

	/**
	 * Type: gc_template.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Theme.
	 *
	 * @var string
	 */
	public $theme;

	/**
	 * Template slug.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * ID.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Title.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Content.
	 *
	 * @var string
	 */
	public $content = '';

	/**
	 * Description.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Source of the content. `theme` and `custom` is used for now.
	 *
	 * @var string
	 */
	public $source = 'theme';

	/**
	 * Origin of the content when the content has been customized.
	 * When customized, origin takes on the value of source and source becomes
	 * 'custom'.
	 *
	 * @var string
	 */
	public $origin;

	/**
	 * Post ID.
	 *
	 * @var int|null
	 */
	public $gc_id;

	/**
	 * Template Status.
	 *
	 * @var string
	 */
	public $status;

	/**
	 * Whether a template is, or is based upon, an existing template file.
	 *
	 * @var bool
	 */
	public $has_theme_file;

	/**
	 * 模板是否为自定义模板。
	 *
	 *
	 * @var bool
	 */
	public $is_custom = true;

	/**
	 * Author.
	 *
	 * A value of 0 means no author.
	 *
	 * @var int
	 */
	public $author;

	/**
	 * Post types.
	 *
	 * @var array
	 */
	public $post_types;

	/**
	 * Area.
	 *
	 * @var string
	 */
	public $area;
}
