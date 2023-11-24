<?php
/**
 * Edit Term Administration Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( empty( $_REQUEST['tag_ID'] ) ) {
	$sendback = admin_url( 'edit-tags.php' );
	if ( ! empty( $taxnow ) ) {
		$sendback = add_query_arg( array( 'taxonomy' => $taxnow ), $sendback );
	}

	if ( 'post' !== get_current_screen()->post_type ) {
		$sendback = add_query_arg( 'post_type', get_current_screen()->post_type, $sendback );
	}

	gc_redirect( esc_url_raw( $sendback ) );
	exit;
}

$tag_ID = absint( $_REQUEST['tag_ID'] );
$tag    = get_term( $tag_ID, $taxnow, OBJECT, 'edit' );

if ( ! $tag instanceof GC_Term ) {
	gc_die( __( '您正在试图编辑一个不存在的条目。它已被删除？' ) );
}

$tax      = get_taxonomy( $tag->taxonomy );
$taxonomy = $tax->name;
$title    = $tax->labels->edit_item;

if ( ! in_array( $taxonomy, get_taxonomies( array( 'show_ui' => true ) ), true )
	|| ! current_user_can( 'edit_term', $tag->term_id )
) {
	gc_die(
		'<h1>' . __( '您需要更高级别的权限。' ) . '</h1>' .
		'<p>' . __( '抱歉，您不能编辑此项目。' ) . '</p>',
		403
	);
}

$post_type = get_current_screen()->post_type;

// Default to the first object_type associated with the taxonomy if no post type was passed.
if ( empty( $post_type ) ) {
	$post_type = reset( $tax->object_type );
}

if ( 'post' !== $post_type ) {
	$parent_file  = ( 'attachment' === $post_type ) ? 'upload.php' : "edit.php?post_type=$post_type";
	$submenu_file = "edit-tags.php?taxonomy=$taxonomy&amp;post_type=$post_type";
} elseif ( 'link_category' === $taxonomy ) {
	$parent_file  = 'link-manager.php';
	$submenu_file = 'edit-tags.php?taxonomy=link_category';
} else {
	$parent_file  = 'edit.php';
	$submenu_file = "edit-tags.php?taxonomy=$taxonomy";
}

get_current_screen()->set_screen_reader_content(
	array(
		'heading_pagination' => $tax->labels->items_list_navigation,
		'heading_list'       => $tax->labels->items_list,
	)
);
gc_enqueue_script( 'admin-tags' );

/**
 * Edit tag form for inclusion in administration panels.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Back compat hooks.
if ( 'category' === $taxonomy ) {
	/**
	 * Fires before the Edit Category form.
	 *
	 * @since 2.1.0
	 * @deprecated 3.0.0 Use {@see '{$taxonomy}_pre_edit_form'} instead.
	 *
	 * @param GC_Term $tag Current category term object.
	 */
	do_action_deprecated( 'edit_category_form_pre', array( $tag ), '3.0.0', '{$taxonomy}_pre_edit_form' );
} elseif ( 'link_category' === $taxonomy ) {
	/**
	 * Fires before the Edit Link Category form.
	 *
	 * @since 2.3.0
	 * @deprecated 3.0.0 Use {@see '{$taxonomy}_pre_edit_form'} instead.
	 *
	 * @param GC_Term $tag Current link category term object.
	 */
	do_action_deprecated( 'edit_link_category_form_pre', array( $tag ), '3.0.0', '{$taxonomy}_pre_edit_form' );
} else {
	/**
	 * Fires before the Edit Tag form.
	 *
	 * @since 2.5.0
	 * @deprecated 3.0.0 Use {@see '{$taxonomy}_pre_edit_form'} instead.
	 *
	 * @param GC_Term $tag Current tag term object.
	 */
	do_action_deprecated( 'edit_tag_form_pre', array( $tag ), '3.0.0', '{$taxonomy}_pre_edit_form' );
}

/**
 * Use with caution, see https://developer.gechiui.com/reference/functions/gc_reset_vars/
 */
gc_reset_vars( array( 'gc_http_referer' ) );

$gc_http_referer = remove_query_arg( array( 'action', 'message', 'tag_ID' ), $gc_http_referer );

// Also used by Edit Tags.
require_once ABSPATH . 'gc-admin/includes/edit-tag-messages.php';

/**
 * Fires before the Edit Term form for all taxonomies.
 *
 * The dynamic portion of the hook name, `$taxonomy`, refers to
 * the taxonomy slug.
 *
 * Possible hook names include:
 *
 *  - `category_pre_edit_form`
 *  - `post_tag_pre_edit_form`
 *
 * @since 3.0.0
 *
 * @param GC_Term $tag      Current taxonomy term object.
 * @param string  $taxonomy Current $taxonomy slug.
 */
do_action( "{$taxonomy}_pre_edit_form", $tag, $taxonomy ); 
if ( $message ) {
	if ( $gc_http_referer ) { 
		$message .= '<a href="'. esc_url( gc_validate_redirect( sanitize_url( $gc_http_referer ), admin_url( 'term.php?taxonomy=' . $taxonomy ) ) ).'">' . esc_html( $tax->labels->back_to_items ) . '</a>';
	}
	$class = ( isset( $msg ) && 5 === $msg ) ? 'danger' : 'success';
	add_settings_error( 'general', 'settings_updated', $message, $class );
}

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
	<div class="page-header"><h2 class="header-title"><?php echo $tax->labels->edit_item; ?></h2></div>

<div id="ajax-response"></div>

<form name="edittag" id="edittag" method="post" action="edit-tags.php" class="validate"
<?php
/**
 * Fires inside the Edit Term form tag.
 *
 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
 *
 * Possible hook names include:
 *
 *  - `category_term_edit_form_tag`
 *  - `post_tag_term_edit_form_tag`
 *
 * @since 3.7.0
 */
do_action( "{$taxonomy}_term_edit_form_tag" );
?>
>
<input type="hidden" name="action" value="editedtag" />
<input type="hidden" name="tag_ID" value="<?php echo esc_attr( $tag_ID ); ?>" />
<input type="hidden" name="taxonomy" value="<?php echo esc_attr( $taxonomy ); ?>" />
<?php
gc_original_referer_field( true, 'previous' );
gc_nonce_field( 'update-tag_' . $tag_ID );

/**
 * Fires at the beginning of the Edit Term form.
 *
 * At this point, the required hidden fields and nonces have already been output.
 *
 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
 *
 * Possible hook names include:
 *
 *  - `category_term_edit_form_top`
 *  - `post_tag_term_edit_form_top`
 *
 * @since 4.5.0
 *
 * @param GC_Term $tag      Current taxonomy term object.
 * @param string  $taxonomy Current $taxonomy slug.
 */
do_action( "{$taxonomy}_term_edit_form_top", $tag, $taxonomy );

$tag_name_value = '';
if ( isset( $tag->name ) ) {
	$tag_name_value = esc_attr( $tag->name );
}
?>
	<table class="form-table" role="presentation">
		<tr class="form-field form-required term-name-wrap">
			<th scope="row"><label for="name"><?php _ex( '名称', 'term name' ); ?></label></th>
			<td><input name="name" id="name" type="text" value="<?php echo $tag_name_value; ?>" size="40" aria-required="true" aria-describedby="name-description" />
			<p class="description" id="name-description"><?php echo $tax->labels->name_field_description; ?></p></td>
		</tr>
		<tr class="form-field term-slug-wrap">
			<th scope="row"><label for="slug"><?php _e( '别名' ); ?></label></th>
			<?php
			/**
			 * Filters the editable slug for a post or term.
			 *
			 * Note: This is a multi-use hook in that it is leveraged both for editable
			 * post URIs and term slugs.
			 *
			 * @since 2.6.0
			 * @since 4.4.0 The `$tag` parameter was added.
			 *
			 * @param string          $slug The editable slug. Will be either a term slug or post URI depending
			 *                              upon the context in which it is evaluated.
			 * @param GC_Term|GC_Post $tag  Term or post object.
			 */
			$slug = isset( $tag->slug ) ? apply_filters( 'editable_slug', $tag->slug, $tag ) : '';
			?>
			<td><input name="slug" id="slug" type="text" value="<?php echo esc_attr( $slug ); ?>" size="40" aria-describedby="slug-description" />
			<p class="description" id="slug-description"><?php echo $tax->labels->slug_field_description; ?></p></td>
		</tr>
<?php if ( is_taxonomy_hierarchical( $taxonomy ) ) : ?>
		<tr class="form-field term-parent-wrap">
			<th scope="row"><label for="parent"><?php echo esc_html( $tax->labels->parent_item ); ?></label></th>
			<td>
				<?php
				$dropdown_args = array(
					'hide_empty'       => 0,
					'hide_if_empty'    => false,
					'taxonomy'         => $taxonomy,
					'name'             => 'parent',
					'orderby'          => 'name',
					'selected'         => $tag->parent,
					'exclude_tree'     => $tag->term_id,
					'hierarchical'     => true,
					'show_option_none' => __( '无' ),
					'aria_describedby' => 'parent-description',
				);

				/** This filter is documented in gc-admin/edit-tags.php */
				$dropdown_args = apply_filters( 'taxonomy_parent_dropdown_args', $dropdown_args, $taxonomy, 'edit' );
				gc_dropdown_categories( $dropdown_args );
				?>
				<?php if ( 'category' === $taxonomy ) : ?>
					<p class="description" id="parent-description"><?php _e( '分类和标签不同，它可以有层级关系。您可以有一个名为“音乐”的分类，在该分类下可以有名为“流行”和“古典”的子分类（完全可选）。' ); ?></p>
				<?php else : ?>
					<p class="description" id="parent-description"><?php echo $tax->labels->parent_field_description; ?></p>
				<?php endif; ?>
			</td>
		</tr>
<?php endif; // is_taxonomy_hierarchical() ?>
		<tr class="form-field term-description-wrap">
			<th scope="row"><label for="description"><?php _e( '描述' ); ?></label></th>
			<td><textarea name="description" id="description" rows="5" cols="50" class="large-text" aria-describedby="description-description"><?php echo $tag->description; // textarea_escaped ?></textarea>
			<p class="description" id="description-description"><?php echo $tax->labels->desc_field_description; ?></p></td>
		</tr>
		<?php
		// Back compat hooks.
		if ( 'category' === $taxonomy ) {
			/**
			 * Fires after the Edit Category form fields are displayed.
			 *
			 * @since 2.9.0
			 * @deprecated 3.0.0 Use {@see '{$taxonomy}_edit_form_fields'} instead.
			 *
			 * @param GC_Term $tag Current category term object.
			 */
			do_action_deprecated( 'edit_category_form_fields', array( $tag ), '3.0.0', '{$taxonomy}_edit_form_fields' );
		} elseif ( 'link_category' === $taxonomy ) {
			/**
			 * Fires after the Edit Link Category form fields are displayed.
			 *
			 * @since 2.9.0
			 * @deprecated 3.0.0 Use {@see '{$taxonomy}_edit_form_fields'} instead.
			 *
			 * @param GC_Term $tag Current link category term object.
			 */
			do_action_deprecated( 'edit_link_category_form_fields', array( $tag ), '3.0.0', '{$taxonomy}_edit_form_fields' );
		} else {
			/**
			 * Fires after the Edit Tag form fields are displayed.
			 *
			 * @since 2.9.0
			 * @deprecated 3.0.0 Use {@see '{$taxonomy}_edit_form_fields'} instead.
			 *
			 * @param GC_Term $tag Current tag term object.
			 */
			do_action_deprecated( 'edit_tag_form_fields', array( $tag ), '3.0.0', '{$taxonomy}_edit_form_fields' );
		}
		/**
		 * Fires after the Edit Term form fields are displayed.
		 *
		 * The dynamic portion of the hook name, `$taxonomy`, refers to
		 * the taxonomy slug.
		 *
		 * Possible hook names include:
		 *
		 *  - `category_edit_form_fields`
		 *  - `post_tag_edit_form_fields`
		 *
		 * @since 3.0.0
		 *
		 * @param GC_Term $tag      Current taxonomy term object.
		 * @param string  $taxonomy Current taxonomy slug.
		 */
		do_action( "{$taxonomy}_edit_form_fields", $tag, $taxonomy );
		?>
	</table>
<?php
// Back compat hooks.
if ( 'category' === $taxonomy ) {
	/** This action is documented in gc-admin/edit-tags.php */
	do_action_deprecated( 'edit_category_form', array( $tag ), '3.0.0', '{$taxonomy}_add_form' );
} elseif ( 'link_category' === $taxonomy ) {
	/** This action is documented in gc-admin/edit-tags.php */
	do_action_deprecated( 'edit_link_category_form', array( $tag ), '3.0.0', '{$taxonomy}_add_form' );
} else {
	/**
	 * Fires at the end of the Edit Term form.
	 *
	 * @since 2.5.0
	 * @deprecated 3.0.0 Use {@see '{$taxonomy}_edit_form'} instead.
	 *
	 * @param GC_Term $tag Current taxonomy term object.
	 */
	do_action_deprecated( 'edit_tag_form', array( $tag ), '3.0.0', '{$taxonomy}_edit_form' );
}
/**
 * Fires at the end of the Edit Term form for all taxonomies.
 *
 * The dynamic portion of the hook name, `$taxonomy`, refers to the taxonomy slug.
 *
 * Possible hook names include:
 *
 *  - `category_edit_form`
 *  - `post_tag_edit_form`
 *
 * @since 3.0.0
 *
 * @param GC_Term $tag      Current taxonomy term object.
 * @param string  $taxonomy Current taxonomy slug.
 */
do_action( "{$taxonomy}_edit_form", $tag, $taxonomy );
?>

<div class="edit-tag-actions">

	<?php submit_button( __( '更新' ), 'primary', null, false ); ?>

	<?php if ( current_user_can( 'delete_term', $tag->term_id ) ) : ?>
		<span id="delete-link">
			<a class="delete" href="<?php echo esc_url( admin_url( gc_nonce_url( "edit-tags.php?action=delete&taxonomy=$taxonomy&tag_ID=$tag->term_id", 'delete-tag_' . $tag->term_id ) ) ); ?>"><?php _e( '删除' ); ?></a>
		</span>
	<?php endif; ?>

</div>

</form>
</div>

<?php if ( ! gc_is_mobile() ) : ?>
<script type="text/javascript">
try{document.forms.edittag.name.focus();}catch(e){}
</script>
	<?php
endif;

require_once ABSPATH . 'gc-admin/admin-footer.php';
