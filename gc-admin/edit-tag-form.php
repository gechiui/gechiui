<?php
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
	 * @deprecated 3.0.0 Use {@see '{$taxonomy}_pre_edit_form'} instead.
	 *
	 * @param GC_Term $tag Current category term object.
	 */
	do_action_deprecated( 'edit_category_form_pre', array( $tag ), '3.0.0', '{$taxonomy}_pre_edit_form' );
} elseif ( 'link_category' === $taxonomy ) {
	/**
	 * Fires before the Edit Link Category form.
	 *
	 * @deprecated 3.0.0 Use {@see '{$taxonomy}_pre_edit_form'} instead.
	 *
	 * @param GC_Term $tag Current link category term object.
	 */
	do_action_deprecated( 'edit_link_category_form_pre', array( $tag ), '3.0.0', '{$taxonomy}_pre_edit_form' );
} else {
	/**
	 * Fires before the Edit Tag form.
	 *
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
 *
 *
 * @param GC_Term $tag      Current taxonomy term object.
 * @param string  $taxonomy Current $taxonomy slug.
 */
do_action( "{$taxonomy}_pre_edit_form", $tag, $taxonomy ); ?>

<div class="wrap">
<h1><?php echo $tax->labels->edit_item; ?></h1>

<?php
$class = ( isset( $msg ) && 5 === $msg ) ? 'error' : 'success';

if ( $message ) {
	?>
<div id="message" class="notice notice-<?php echo $class; ?>">
	<p><strong><?php echo $message; ?></strong></p>
	<?php if ( $gc_http_referer ) { ?>
	<p><a href="<?php echo esc_url( gc_validate_redirect( esc_url_raw( $gc_http_referer ), admin_url( 'term.php?taxonomy=' . $taxonomy ) ) ); ?>">
		<?php echo esc_html( $tax->labels->back_to_items ); ?>
	</a></p>
	<?php } ?>
</div>
	<?php
}
?>

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
 *
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
 *
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
			<td><input name="name" id="name" type="text" value="<?php echo $tag_name_value; ?>" size="40" aria-required="true" />
			<p class="描述"><?php echo $tax->labels->name_field_description; ?></p></td>
		</tr>
<?php if ( ! global_terms_enabled() ) { ?>
		<tr class="form-field term-slug-wrap">
			<th scope="row"><label for="slug"><?php _e( '别名' ); ?></label></th>
			<?php
			/**
			 * Filters the editable slug for a post or term.
			 *
			 * Note: This is a multi-use hook in that it is leveraged both for editable
			 * post URIs and term slugs.
			 *
		
		
			 *
			 * @param string          $slug The editable slug. Will be either a term slug or post URI depending
			 *                              upon the context in which it is evaluated.
			 * @param GC_Term|GC_Post $tag  Term or post object.
			 */
			$slug = isset( $tag->slug ) ? apply_filters( 'editable_slug', $tag->slug, $tag ) : '';
			?>
			<td><input name="slug" id="slug" type="text" value="<?php echo esc_attr( $slug ); ?>" size="40" />
			<p class="描述"><?php echo $tax->labels->slug_field_description; ?></p></td>
		</tr>
<?php } ?>
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
				);

				/** This filter is documented in gc-admin/edit-tags.php */
				$dropdown_args = apply_filters( 'taxonomy_parent_dropdown_args', $dropdown_args, $taxonomy, 'edit' );
				gc_dropdown_categories( $dropdown_args );
				?>
				<?php if ( 'category' === $taxonomy ) : ?>
					<p class="描述"><?php _e( '分类和标签不同，它可以有层级关系。您可以有一个名为“音乐”的分类，在该分类下可以有名为“流行”和“古典”的子分类（完全可选）。' ); ?></p>
				<?php else : ?>
					<p class="描述"><?php echo $tax->labels->parent_field_description; ?></p>
				<?php endif; ?>
			</td>
		</tr>
<?php endif; // is_taxonomy_hierarchical() ?>
		<tr class="form-field term-description-wrap">
			<th scope="row"><label for="描述"><?php _e( '描述' ); ?></label></th>
			<td><textarea name="描述" id="描述" rows="5" cols="50" class="large-text"><?php echo $tag->description; // textarea_escaped ?></textarea>
			<p class="描述"><?php echo $tax->labels->desc_field_description; ?></p></td>
		</tr>
		<?php
		// Back compat hooks.
		if ( 'category' === $taxonomy ) {
			/**
			 * Fires after the Edit Category form fields are displayed.
			 *
		
			 * @deprecated 3.0.0 Use {@see '{$taxonomy}_edit_form_fields'} instead.
			 *
			 * @param GC_Term $tag Current category term object.
			 */
			do_action_deprecated( 'edit_category_form_fields', array( $tag ), '3.0.0', '{$taxonomy}_edit_form_fields' );
		} elseif ( 'link_category' === $taxonomy ) {
			/**
			 * Fires after the Edit Link Category form fields are displayed.
			 *
		
			 * @deprecated 3.0.0 Use {@see '{$taxonomy}_edit_form_fields'} instead.
			 *
			 * @param GC_Term $tag Current link category term object.
			 */
			do_action_deprecated( 'edit_link_category_form_fields', array( $tag ), '3.0.0', '{$taxonomy}_edit_form_fields' );
		} else {
			/**
			 * Fires after the Edit Tag form fields are displayed.
			 *
		
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
 *
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
