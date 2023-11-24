<?php
/**
 * Edit Tags Administration Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! $taxnow ) {
	gc_die( __( '无效分类法。' ) );
}

$tax = get_taxonomy( $taxnow );

if ( ! $tax ) {
	gc_die( __( '无效分类法。' ) );
}

if ( ! in_array( $tax->name, get_taxonomies( array( 'show_ui' => true ) ), true ) ) {
	gc_die( __( '抱歉，您不能编辑此分类法中的项目。' ) );
}

if ( ! current_user_can( $tax->cap->manage_terms ) ) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能管理此分类法中的项目。' ) . '</p>',
		403
	);
}

/**
 * $post_type is set when the GC_Terms_List_Table instance is created
 *
 * @global string $post_type
 */
global $post_type;

$gc_list_table = _get_list_table( 'GC_Terms_List_Table' );
$pagenum       = $gc_list_table->get_pagenum();

$title = $tax->labels->name;

if ( 'post' !== $post_type ) {
	$parent_file  = ( 'attachment' === $post_type ) ? 'upload.php' : "edit.php?post_type=$post_type";
	$submenu_file = "edit-tags.php?taxonomy=$taxonomy&amp;post_type=$post_type";
} elseif ( 'link_category' === $tax->name ) {
	$parent_file  = 'link-manager.php';
	$submenu_file = 'edit-tags.php?taxonomy=link_category';
} else {
	$parent_file  = 'edit.php';
	$submenu_file = "edit-tags.php?taxonomy=$taxonomy";
}

add_screen_option(
	'per_page',
	array(
		'default' => 20,
		'option'  => 'edit_' . $tax->name . '_per_page',
	)
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_pagination' => $tax->labels->items_list_navigation,
		'heading_list'       => $tax->labels->items_list,
	)
);

$location = false;
$referer  = gc_get_referer();
if ( ! $referer ) { // For POST requests.
	$referer = gc_unslash( $_SERVER['REQUEST_URI'] );
}
$referer = remove_query_arg( array( '_gc_http_referer', '_gcnonce', 'error', 'message', 'paged' ), $referer );
switch ( $gc_list_table->current_action() ) {

	case 'add-tag':
		check_admin_referer( 'add-tag', '_gcnonce_add-tag' );

		if ( ! current_user_can( $tax->cap->edit_terms ) ) {
			gc_die(
				'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
				'<p>' . __( '抱歉，您不能在此分类法中创建项目。' ) . '</p>',
				403
			);
		}

		$ret = gc_insert_term( $_POST['tag-name'], $taxonomy, $_POST );
		if ( $ret && ! is_gc_error( $ret ) ) {
			$location = add_query_arg( 'message', 1, $referer );
		} else {
			$location = add_query_arg(
				array(
					'error'   => true,
					'message' => 4,
				),
				$referer
			);
		}

		break;

	case 'delete':
		if ( ! isset( $_REQUEST['tag_ID'] ) ) {
			break;
		}

		$tag_ID = (int) $_REQUEST['tag_ID'];
		check_admin_referer( 'delete-tag_' . $tag_ID );

		if ( ! current_user_can( 'delete_term', $tag_ID ) ) {
			gc_die(
				'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
				'<p>' . __( '很抱歉，您不允许删除此项目。' ) . '</p>',
				403
			);
		}

		gc_delete_term( $tag_ID, $taxonomy );

		$location = add_query_arg( 'message', 2, $referer );

		// When deleting a term, prevent the action from redirecting back to a term that no longer exists.
		$location = remove_query_arg( array( 'tag_ID', 'action' ), $location );

		break;

	case 'bulk-delete':
		check_admin_referer( 'bulk-tags' );

		if ( ! current_user_can( $tax->cap->delete_terms ) ) {
			gc_die(
				'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
				'<p>' . __( '很抱歉，您不允许删除这些项目。' ) . '</p>',
				403
			);
		}

		$tags = (array) $_REQUEST['delete_tags'];
		foreach ( $tags as $tag_ID ) {
			gc_delete_term( $tag_ID, $taxonomy );
		}

		$location = add_query_arg( 'message', 6, $referer );

		break;

	case 'edit':
		if ( ! isset( $_REQUEST['tag_ID'] ) ) {
			break;
		}

		$term_id = (int) $_REQUEST['tag_ID'];
		$term    = get_term( $term_id );

		if ( ! $term instanceof GC_Term ) {
			gc_die( __( '您试图编辑一个不存在的项目。也许它被删除了？' ) );
		}

		gc_redirect( sanitize_url( get_edit_term_link( $term_id, $taxonomy, $post_type ) ) );
		exit;

	case 'editedtag':
		$tag_ID = (int) $_POST['tag_ID'];
		check_admin_referer( 'update-tag_' . $tag_ID );

		if ( ! current_user_can( 'edit_term', $tag_ID ) ) {
			gc_die(
				'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
				'<p>' . __( '很抱歉，不允许您编辑此项目。' ) . '</p>',
				403
			);
		}

		$tag = get_term( $tag_ID, $taxonomy );
		if ( ! $tag ) {
			gc_die( __( '您试图编辑一个不存在的项目。也许它被删除了？' ) );
		}

		$ret = gc_update_term( $tag_ID, $taxonomy, $_POST );

		if ( $ret && ! is_gc_error( $ret ) ) {
			$location = add_query_arg( 'message', 3, $referer );
		} else {
			$location = add_query_arg(
				array(
					'error'   => true,
					'message' => 5,
				),
				$referer
			);
		}
		break;
	default:
		if ( ! $gc_list_table->current_action() || ! isset( $_REQUEST['delete_tags'] ) ) {
			break;
		}
		check_admin_referer( 'bulk-tags' );

		$screen = get_current_screen()->id;
		$tags   = (array) $_REQUEST['delete_tags'];

		/** This action is documented in gc-admin/edit.php */
		$location = apply_filters( "handle_bulk_actions-{$screen}", $location, $gc_list_table->current_action(), $tags ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
		break;
}

if ( ! $location && ! empty( $_REQUEST['_gc_http_referer'] ) ) {
	$location = remove_query_arg( array( '_gc_http_referer', '_gcnonce' ), gc_unslash( $_SERVER['REQUEST_URI'] ) );
}

if ( $location ) {
	if ( $pagenum > 1 ) {
		$location = add_query_arg( 'paged', $pagenum, $location ); // $pagenum takes care of $total_pages.
	}

	/**
	 * Filters the taxonomy redirect destination URL.
	 *
	 * @since 4.6.0
	 *
	 * @param string      $location The destination URL.
	 * @param GC_Taxonomy $tax      The taxonomy object.
	 */
	gc_redirect( apply_filters( 'redirect_term_location', $location, $tax ) );
	exit;
}

$gc_list_table->prepare_items();
$total_pages = $gc_list_table->get_pagination_arg( 'total_pages' );

if ( $pagenum > $total_pages && $total_pages > 0 ) {
	gc_redirect( add_query_arg( 'paged', $total_pages ) );
	exit;
}

gc_enqueue_script( 'admin-tags' );
if ( current_user_can( $tax->cap->edit_terms ) ) {
	gc_enqueue_script( 'inline-edit-tax' );
}

if ( 'category' === $taxonomy || 'link_category' === $taxonomy || 'post_tag' === $taxonomy ) {
	$help = '';
	if ( 'category' === $taxonomy ) {
		$help = '<p>' . sprintf(
			/* translators: %s: URL to Writing Settings screen. */
			__( '您可以使用分类来定义您系统的分区结构，并可以按不同的主题组织相关的文章。文章的默认分类为“未分类”，您可在<a href="%s">撰写设置</a>中修改它。' ),
			'options-writing.php'
		) . '</p>';
	} elseif ( 'link_category' === $taxonomy ) {
		$help = '<p>' . __( '您可以使用“链接分类”创建一组链接。链接分类的名字必须是唯一的，且链接分类和文章分类相对独立。' ) . '</p>';
	} else {
		$help = '<p>' . __( '您可为文章指定一些关键词，这些关键词叫做<strong>标签</strong>。与分类不同的是，标签没有层级关系，换句话说就是标签之间没有关联。' ) . '</p>';
	}

	if ( 'link_category' === $taxonomy ) {
		$help .= '<p>' . __( '您可以通过“批量操作”来一次删除多个链接分类，但是删除操作并不影响分类中的链接。其下链接将被自动移至默认的链接分类。' ) . '</p>';
	} else {
		$help .= '<p>' . __( '分类和标签的区别是什么呢？通常来说，标签是临时安排的一些关键词，用来标记文章中的关键信息（名字、题目等），而其他文章或许也会拥有这个标签；分类则是预先确定的内容分区。若将您的系统比做一本书，那么分类就是书的目录，标签则是目录中索引的术语。' ) . '</p>';
	}

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'overview',
			'title'   => __( '概述' ),
			'content' => $help,
		)
	);

	if ( 'category' === $taxonomy || 'post_tag' === $taxonomy ) {
		if ( 'category' === $taxonomy ) {
			$help = '<p>' . __( '在此页面添加新分类时，您将填写以下字段：' ) . '</p>';
		} else {
			$help = '<p>' . __( '在此页面添加新标签时，您将填写以下字段：' ) . '</p>';
		}

		$help .= '<ul>' .
		'<li>' . __( '<strong>名称</strong>——此分类在系统上的显示名称。' ) . '</li>';

		$help .= '<li>' . __( '<strong>别名</strong>——“别名”是在URL中使用的代号，它可以令URL更美观。别名通常使用小写字母，只能包含字母、数字和连字符。' ) . '</li>';

		if ( 'category' === $taxonomy ) {
			$help .= '<li>' . __( '<strong>父极分类</strong>与标签不同，它可以有层级结构。您可以有一个名为“音乐”的分类，在该分类下可以有名为“流行”和“古典”的子分类（完全可选）。要创建子分类，只需从父级分类下拉菜单中选择一个分类即可。' ) . '</li>';
		}

		$help .= '<li>' . __( '<strong>描述</strong>——“描述”中的内容默认不会显示，但部分主题中可能会显示。' ) . '</li>' .
		'</ul>' .
		'<p>' . __( '在“显示选项”中，您可以调整每页显示的标签数量、隐藏或显示表格中的一些栏目。' ) . '</p>';

		get_current_screen()->add_help_tab(
			array(
				'id'      => 'adding-terms',
				'title'   => 'category' === $taxonomy ? __( '添加分类' ) : __( '标签的添加' ),
				'content' => $help,
			)
		);
	}

	$help = '<p><strong>' . __( '更多信息：' ) . '</strong></p>';

	if ( 'category' === $taxonomy ) {
		$help .= '<p>' . __( '<a href="https://www.gechiui.com/support/posts-categories-screen/">分类文档</a>' ) . '</p>';
	} elseif ( 'link_category' === $taxonomy ) {
		$help .= '<p>' . __( '<a href="https://codex.gechiui.com/Links_Link_Categories_Screen">链接分类文档</a>' ) . '</p>';
	} else {
		$help .= '<p>' . __( '<a href="https://www.gechiui.com/support/posts-tags-screen/">Tags标签文档</a>' ) . '</p>';
	}

	$help .= '<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>';

	get_current_screen()->set_help_sidebar( $help );

	unset( $help );
}

// Also used by the Edit Tag form.
require_once ABSPATH . 'gc-admin/includes/edit-tag-messages.php';
if ( $message ) {
	$class = ( isset( $msg ) && 5 === $msg ) ? 'danger' : 'success';
	add_settings_error( 'general', 'settings_updated', $message, $class );
	$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'message', 'error' ), $_SERVER['REQUEST_URI'] );
}
require_once ABSPATH . 'gc-admin/admin-header.php';


if ( is_plugin_active( 'gccat2tag-importer/gccat2tag-importer.php' ) ) {
	$import_link = admin_url( 'admin.php?import=gccat2tag' );
} else {
	$import_link = admin_url( 'import.php' );
}

?>

<div class="wrap nosubsub">
<div class="page-header"><h2 class="header-title"><?php echo esc_html( $title ); ?></h2></div>

<?php
if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
	echo '<span class="subtitle">';
	printf(
		/* translators: %s: Search query. */
		__( '搜索词：%s' ),
		'<strong>' . esc_html( gc_unslash( $_REQUEST['s'] ) ) . '</strong>'
	);
	echo '</span>';
}
?>

<div id="ajax-response"></div>

<form class="search-form gc-clearfix" method="get">
<input type="hidden" name="taxonomy" value="<?php echo esc_attr( $taxonomy ); ?>" />
<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>" />

<?php $gc_list_table->search_box( $tax->labels->search_items, 'tag' ); ?>

</form>

<?php
$can_edit_terms = current_user_can( $tax->cap->edit_terms );

if ( $can_edit_terms ) {
	?>
<div id="col-container" class="gc-clearfix">

<div id="col-left">
<div class="col-wrap">

	<?php
	if ( 'category' === $taxonomy ) {
		/**
		 * Fires before the Add Category form.
		 *
		 * @since 2.1.0
		 * @deprecated 3.0.0 Use {@see '{$taxonomy}_pre_add_form'} instead.
		 *
		 * @param object $arg Optional arguments cast to an object.
		 */
		do_action_deprecated( 'add_category_form_pre', array( (object) array( 'parent' => 0 ) ), '3.0.0', '{$taxonomy}_pre_add_form' );
	} elseif ( 'link_category' === $taxonomy ) {
		/**
		 * Fires before the link category form.
		 *
		 * @since 2.3.0
		 * @deprecated 3.0.0 Use {@see '{$taxonomy}_pre_add_form'} instead.
		 *
		 * @param object $arg Optional arguments cast to an object.
		 */
		do_action_deprecated( 'add_link_category_form_pre', array( (object) array( 'parent' => 0 ) ), '3.0.0', '{$taxonomy}_pre_add_form' );
	} else {
		/**
		 * Fires before the Add Tag form.
		 *
		 * @since 2.5.0
		 * @deprecated 3.0.0 Use {@see '{$taxonomy}_pre_add_form'} instead.
		 *
		 * @param string $taxonomy The taxonomy slug.
		 */
		do_action_deprecated( 'add_tag_form_pre', array( $taxonomy ), '3.0.0', '{$taxonomy}_pre_add_form' );
	}

	/**
	 * Fires before the Add Term form for all taxonomies.
	 *
	 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
	 *
	 * Possible hook names include:
	 *
	 *  - `category_pre_add_form`
	 *  - `post_tag_pre_add_form`
	 *
	 * @since 3.0.0
	 *
	 * @param string $taxonomy The taxonomy slug.
	 */
	do_action( "{$taxonomy}_pre_add_form", $taxonomy );
	?>

<div class="form-wrap">
<h2><?php echo $tax->labels->add_new_item; ?></h2>
<form id="addtag" method="post" action="edit-tags.php" class="validate"
	<?php
	/**
	 * Fires inside the Add Tag form tag.
	 *
	 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
	 *
	 * Possible hook names include:
	 *
	 *  - `category_term_new_form_tag`
	 *  - `post_tag_term_new_form_tag`
	 *
	 * @since 3.7.0
	 */
	do_action( "{$taxonomy}_term_new_form_tag" );
	?>
>
<input type="hidden" name="action" value="add-tag" />
<input type="hidden" name="screen" value="<?php echo esc_attr( $current_screen->id ); ?>" />
<input type="hidden" name="taxonomy" value="<?php echo esc_attr( $taxonomy ); ?>" />
<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>" />
	<?php gc_nonce_field( 'add-tag', '_gcnonce_add-tag' ); ?>

<div class="form-field form-required term-name-wrap">
	<label for="tag-name"><?php _ex( '名称', 'term name' ); ?></label>
	<input name="tag-name" id="tag-name" type="text" value="" size="40" aria-required="true" aria-describedby="name-description" />
	<p id="name-description"><?php echo $tax->labels->name_field_description; ?></p>
</div>
<div class="form-field term-slug-wrap">
	<label for="tag-slug"><?php _e( '别名' ); ?></label>
	<input name="slug" id="tag-slug" type="text" value="" size="40" aria-describedby="slug-description" />
	<p id="slug-description"><?php echo $tax->labels->slug_field_description; ?></p>
</div>
	<?php if ( is_taxonomy_hierarchical( $taxonomy ) ) : ?>
<div class="form-field term-parent-wrap">
	<label for="parent"><?php echo esc_html( $tax->labels->parent_item ); ?></label>
		<?php
		$dropdown_args = array(
			'hide_empty'       => 0,
			'hide_if_empty'    => false,
			'taxonomy'         => $taxonomy,
			'name'             => 'parent',
			'orderby'          => 'name',
			'hierarchical'     => true,
			'show_option_none' => __( '无' ),
		);

		/**
		 * Filters the taxonomy parent drop-down on the Edit Term page.
		 *
		 * @since 3.7.0
		 * @since 4.2.0 Added `$context` parameter.
		 *
		 * @param array  $dropdown_args {
		 *     An array of taxonomy parent drop-down arguments.
		 *
		 *     @type int|bool $hide_empty       Whether to hide terms not attached to any posts. Default 0.
		 *     @type bool     $hide_if_empty    Whether to hide the drop-down if no terms exist. Default false.
		 *     @type string   $taxonomy         The taxonomy slug.
		 *     @type string   $name             Value of the name attribute to use for the drop-down select element.
		 *                                      Default 'parent'.
		 *     @type string   $orderby          The field to order by. Default 'name'.
		 *     @type bool     $hierarchical     Whether the taxonomy is hierarchical. Default true.
		 *     @type string   $show_option_none Label to display if there are no terms. Default 'None'.
		 * }
		 * @param string $taxonomy The taxonomy slug.
		 * @param string $context  Filter context. Accepts 'new' or 'edit'.
		 */
		$dropdown_args = apply_filters( 'taxonomy_parent_dropdown_args', $dropdown_args, $taxonomy, 'new' );

		$dropdown_args['aria_describedby'] = 'parent-description';

		gc_dropdown_categories( $dropdown_args );
		?>
		<?php if ( 'category' === $taxonomy ) : ?>
		<p id="parent-description"><?php _e( '分类和标签不同，它可以有层级关系。您可以有一个名为“音乐”的分类，在该分类下可以有名为“流行”和“古典”的子分类（完全可选）。' ); ?></p>
	<?php else : ?>
		<p id="parent-description"><?php echo $tax->labels->parent_field_description; ?></p>
	<?php endif; ?>
</div>
	<?php endif; // is_taxonomy_hierarchical() ?>
<div class="form-field term-description-wrap">
	<label for="tag-description"><?php _e( '描述' ); ?></label>
	<textarea name="description" id="tag-description" rows="5" cols="40" aria-describedby="description-description"></textarea>
	<p id="description-description"><?php echo $tax->labels->desc_field_description; ?></p>
</div>

	<?php
	if ( ! is_taxonomy_hierarchical( $taxonomy ) ) {
		/**
		 * Fires after the Add Tag form fields for non-hierarchical taxonomies.
		 *
		 * @since 3.0.0
		 *
		 * @param string $taxonomy The taxonomy slug.
		 */
		do_action( 'add_tag_form_fields', $taxonomy );
	}

	/**
	 * Fires after the Add Term form fields.
	 *
	 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
	 *
	 * Possible hook names include:
	 *
	 *  - `category_add_form_fields`
	 *  - `post_tag_add_form_fields`
	 *
	 * @since 3.0.0
	 *
	 * @param string $taxonomy The taxonomy slug.
	 */
	do_action( "{$taxonomy}_add_form_fields", $taxonomy );
	?>
	<p class="submit">
		<?php submit_button( $tax->labels->add_new_item, 'primary', 'submit', false ); ?>
		<span class="spinner"></span>
	</p>
	<?php
	if ( 'category' === $taxonomy ) {
		/**
		 * Fires at the end of the Edit Category form.
		 *
		 * @since 2.1.0
		 * @deprecated 3.0.0 Use {@see '{$taxonomy}_add_form'} instead.
		 *
		 * @param object $arg Optional arguments cast to an object.
		 */
		do_action_deprecated( 'edit_category_form', array( (object) array( 'parent' => 0 ) ), '3.0.0', '{$taxonomy}_add_form' );
	} elseif ( 'link_category' === $taxonomy ) {
		/**
		 * Fires at the end of the Edit Link form.
		 *
		 * @since 2.3.0
		 * @deprecated 3.0.0 Use {@see '{$taxonomy}_add_form'} instead.
		 *
		 * @param object $arg Optional arguments cast to an object.
		 */
		do_action_deprecated( 'edit_link_category_form', array( (object) array( 'parent' => 0 ) ), '3.0.0', '{$taxonomy}_add_form' );
	} else {
		/**
		 * Fires at the end of the Add Tag form.
		 *
		 * @deprecated 3.0.0 Use {@see '{$taxonomy}_add_form'} instead.
		 *
		 * @param string $taxonomy The taxonomy slug.
		 */
		do_action_deprecated( 'add_tag_form', array( $taxonomy ), '3.0.0', '{$taxonomy}_add_form' );
	}

	/**
	 * Fires at the end of the Add Term form for all taxonomies.
	 *
	 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
	 *
	 * Possible hook names include:
	 *
	 *  - `category_add_form`
	 *  - `post_tag_add_form`
	 *
	 * @since 3.0.0
	 *
	 * @param string $taxonomy The taxonomy slug.
	 */
	do_action( "{$taxonomy}_add_form", $taxonomy );
	?>
</form></div>
</div>
</div><!-- /col-left -->

<div id="col-right">
<div class="col-wrap">
<?php } ?>

<?php $gc_list_table->views(); ?>

<form id="posts-filter" method="post">
<input type="hidden" name="taxonomy" value="<?php echo esc_attr( $taxonomy ); ?>" />
<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>" />

<?php $gc_list_table->display(); ?>

</form>

<?php if ( 'category' === $taxonomy ) : ?>
<div class="form-wrap edit-term-notes">
<p>
	<?php
	printf(
		/* translators: %s: Default category. */
		__( '删除分类不会删除分类中的文章。然而，仅隶属于已删除分类的文章将会分入默认分类%s中。默认分类不能被删除。' ),
		/** This filter is documented in gc-includes/category-template.php */
		'<strong>' . apply_filters( 'the_category', get_cat_name( get_option( 'default_category' ) ), '', '' ) . '</strong>'
	);
	?>
</p>
	<?php if ( current_user_can( 'import' ) ) : ?>
	<p>
		<?php
		printf(
			/* translators: %s: URL to Categories to Tags Converter tool. */
			__( '分类可以选择性的转换成标签，请使用<a href="%s">分类与标签转换器</a>。' ),
			esc_url( $import_link )
		);
		?>
	</p>
	<?php endif; ?>
</div>
<?php elseif ( 'post_tag' === $taxonomy && current_user_can( 'import' ) ) : ?>
<div class="form-wrap edit-term-notes">
<p>
	<?php
	printf(
		/* translators: %s: URL to Categories to Tags Converter tool. */
		__( '标签可以选择性地转换成分类，请使用<a href="%s">分类与标签转换器</a>。' ),
		esc_url( $import_link )
	);
	?>
	</p>
</div>
	<?php
endif;

/**
 * Fires after the taxonomy list table.
 *
 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
 *
 * Possible hook names include:
 *
 *  - `after-category-table`
 *  - `after-post_tag-table`
 *
 * @since 3.0.0
 *
 * @param string $taxonomy The taxonomy name.
 */
do_action( "after-{$taxonomy}-table", $taxonomy );  // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

if ( $can_edit_terms ) {
	?>
</div>
</div><!-- /col-right -->

</div><!-- /col-container -->
<?php } ?>

</div><!-- /wrap -->

<?php if ( ! gc_is_mobile() ) : ?>
<script type="text/javascript">
try{document.forms.addtag['tag-name'].focus();}catch(e){}
</script>
	<?php
endif;

$gc_list_table->inline_edit();

require_once ABSPATH . 'gc-admin/admin-footer.php';
