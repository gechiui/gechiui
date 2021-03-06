<?php
/**
 * Post advanced form for inclusion in the administration panels.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @global string       $post_type
 * @global GC_Post_Type $post_type_object
 * @global GC_Post      $post             Global post object.
 */
global $post_type, $post_type_object, $post;

// Flag that we're not loading the block editor.
$current_screen = get_current_screen();
$current_screen->is_block_editor( false );

if ( is_multisite() ) {
	add_action( 'admin_footer', '_admin_notice_post_locked' );
} else {
	$check_users = get_users(
		array(
			'fields' => 'ID',
			'number' => 2,
		)
	);

	if ( count( $check_users ) > 1 ) {
		add_action( 'admin_footer', '_admin_notice_post_locked' );
	}

	unset( $check_users );
}

gc_enqueue_script( 'post' );

$_gc_editor_expand   = false;
$_content_editor_dfw = false;

if ( post_type_supports( $post_type, 'editor' )
	&& ! gc_is_mobile()
	&& ! ( $is_IE && preg_match( '/MSIE [5678]/', $_SERVER['HTTP_USER_AGENT'] ) )
) {
	/**
	 * Filters whether to enable the 'expand' functionality in the post editor.
	 *
	 *
	 * @param bool   $expand    Whether to enable the 'expand' functionality. Default true.
	 * @param string $post_type Post type.
	 */
	if ( apply_filters( 'gc_editor_expand', true, $post_type ) ) {
		gc_enqueue_script( 'editor-expand' );
		$_content_editor_dfw = true;
		$_gc_editor_expand   = ( 'on' === get_user_setting( 'editor_expand', 'on' ) );
	}
}

if ( gc_is_mobile() ) {
	gc_enqueue_script( 'jquery-touch-punch' );
}

/**
 * Post ID global
 *
 * @name $post_ID
 * @var int
 */
$post_ID = isset( $post_ID ) ? (int) $post_ID : 0;
$user_ID = isset( $user_ID ) ? (int) $user_ID : 0;
$action  = isset( $action ) ? $action : '';

if ( (int) get_option( 'page_for_posts' ) === $post->ID && empty( $post->post_content ) ) {
	add_action( 'edit_form_after_title', '_gc_posts_page_notice' );
	remove_post_type_support( $post_type, 'editor' );
}

$thumbnail_support = current_theme_supports( 'post-thumbnails', $post_type ) && post_type_supports( $post_type, 'thumbnail' );
if ( ! $thumbnail_support && 'attachment' === $post_type && $post->post_mime_type ) {
	if ( gc_attachment_is( 'audio', $post ) ) {
		$thumbnail_support = post_type_supports( 'attachment:audio', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:audio' );
	} elseif ( gc_attachment_is( 'video', $post ) ) {
		$thumbnail_support = post_type_supports( 'attachment:video', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:video' );
	}
}

if ( $thumbnail_support ) {
	add_thickbox();
	gc_enqueue_media( array( 'post' => $post->ID ) );
}

// Add the local autosave notice HTML.
add_action( 'admin_footer', '_local_storage_notice' );

/*
 * @todo Document the $messages array(s).
 */
$permalink = get_permalink( $post->ID );
if ( ! $permalink ) {
	$permalink = '';
}

$messages = array();

$preview_post_link_html   = '';
$scheduled_post_link_html = '';
$view_post_link_html      = '';

$preview_page_link_html   = '';
$scheduled_page_link_html = '';
$view_page_link_html      = '';

$preview_url = get_preview_post_link( $post );

$viewable = is_post_type_viewable( $post_type_object );

if ( $viewable ) {

	// Preview post link.
	$preview_post_link_html = sprintf(
		' <a target="_blank" href="%1$s">%2$s</a>',
		esc_url( $preview_url ),
		__( '????????????' )
	);

	// Scheduled post preview link.
	$scheduled_post_link_html = sprintf(
		' <a target="_blank" href="%1$s">%2$s</a>',
		esc_url( $permalink ),
		__( '????????????' )
	);

	// View post link.
	$view_post_link_html = sprintf(
		' <a href="%1$s">%2$s</a>',
		esc_url( $permalink ),
		__( '????????????' )
	);

	// Preview page link.
	$preview_page_link_html = sprintf(
		' <a target="_blank" href="%1$s">%2$s</a>',
		esc_url( $preview_url ),
		__( '????????????' )
	);

	// Scheduled page preview link.
	$scheduled_page_link_html = sprintf(
		' <a target="_blank" href="%1$s">%2$s</a>',
		esc_url( $permalink ),
		__( '????????????' )
	);

	// View page link.
	$view_page_link_html = sprintf(
		' <a href="%1$s">%2$s</a>',
		esc_url( $permalink ),
		__( '????????????' )
	);

}

$scheduled_date = sprintf(
	/* translators: Publish box date string. 1: Date, 2: Time. */
	__( '%1$s %2$s' ),
	/* translators: Publish box date format, see https://www.php.net/manual/datetime.format.php */
	date_i18n( _x( 'Y???n???j???', 'publish box date format' ), strtotime( $post->post_date ) ),
	/* translators: Publish box time format, see https://www.php.net/manual/datetime.format.php */
	date_i18n( _x( 'H:i', 'publish box time format' ), strtotime( $post->post_date ) )
);

$messages['post']       = array(
	0  => '', // Unused. Messages start at index 1.
	1  => __( '??????????????????' ) . $view_post_link_html,
	2  => __( '???????????????????????????' ),
	3  => __( '???????????????????????????' ),
	4  => __( '??????????????????' ),
	/* translators: %s: Date and time of the revision. */
	5  => isset( $_GET['revision'] ) ? sprintf( __( '?????????????????????%s????????????' ), gc_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	6  => __( '??????????????????' ) . $view_post_link_html,
	7  => __( '??????????????????' ),
	8  => __( '??????????????????' ) . $preview_post_link_html,
	/* translators: %s: Scheduled date for the post. */
	9  => sprintf( __( '?????????????????????%s???' ), '<strong>' . $scheduled_date . '</strong>' ) . $scheduled_post_link_html,
	10 => __( '????????????????????????' ) . $preview_post_link_html,
);
$messages['page']       = array(
	0  => '', // Unused. Messages start at index 1.
	1  => __( '??????????????????' ) . $view_page_link_html,
	2  => __( '???????????????????????????' ),
	3  => __( '???????????????????????????' ),
	4  => __( '??????????????????' ),
	/* translators: %s: Date and time of the revision. */
	5  => isset( $_GET['revision'] ) ? sprintf( __( '????????????%s???????????????????????????' ), gc_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	6  => __( '??????????????????' ) . $view_page_link_html,
	7  => __( '??????????????????' ),
	8  => __( '??????????????????' ) . $preview_page_link_html,
	/* translators: %s: Scheduled date for the page. */
	9  => sprintf( __( '?????????????????????%s???' ), '<strong>' . $scheduled_date . '</strong>' ) . $scheduled_page_link_html,
	10 => __( '????????????????????????' ) . $preview_page_link_html,
);
$messages['attachment'] = array_fill( 1, 10, __( '????????????????????????' ) ); // Hack, for now.

/**
 * Filters the post updated messages.
 *
 *
 *
 * @param array[] $messages Post updated messages. For defaults see `$messages` declarations above.
 */
$messages = apply_filters( 'post_updated_messages', $messages );

$message = false;
if ( isset( $_GET['message'] ) ) {
	$_GET['message'] = absint( $_GET['message'] );
	if ( isset( $messages[ $post_type ][ $_GET['message'] ] ) ) {
		$message = $messages[ $post_type ][ $_GET['message'] ];
	} elseif ( ! isset( $messages[ $post_type ] ) && isset( $messages['post'][ $_GET['message'] ] ) ) {
		$message = $messages['post'][ $_GET['message'] ];
	}
}

$notice     = false;
$form_extra = '';
if ( 'auto-draft' === $post->post_status ) {
	if ( 'edit' === $action ) {
		$post->post_title = '';
	}
	$autosave    = false;
	$form_extra .= "<input type='hidden' id='auto_draft' name='auto_draft' value='1' />";
} else {
	$autosave = gc_get_post_autosave( $post->ID );
}

$form_action  = 'editpost';
$nonce_action = 'update-post_' . $post->ID;
$form_extra  .= "<input type='hidden' id='post_ID' name='post_ID' value='" . esc_attr( $post->ID ) . "' />";

// Detect if there exists an autosave newer than the post and if that autosave is different than the post.
if ( $autosave && mysql2date( 'U', $autosave->post_modified_gmt, false ) > mysql2date( 'U', $post->post_modified_gmt, false ) ) {
	foreach ( _gc_post_revision_fields( $post ) as $autosave_field => $_autosave_field ) {
		if ( normalize_whitespace( $autosave->$autosave_field ) !== normalize_whitespace( $post->$autosave_field ) ) {
			$notice = sprintf(
				/* translators: %s: URL to view the autosave. */
				__( '???????????????????????????????????????????????????????????? <a href="%s">??????????????????</a>' ),
				get_edit_post_link( $autosave->ID )
			);
			break;
		}
	}
	// If this autosave isn't different from the current post, begone.
	if ( ! $notice ) {
		gc_delete_post_revision( $autosave->ID );
	}
	unset( $autosave_field, $_autosave_field );
}

$post_type_object = get_post_type_object( $post_type );

// All meta boxes should be defined and added before the first do_meta_boxes() call (or potentially during the do_meta_boxes action).
require_once ABSPATH . 'gc-admin/includes/meta-boxes.php';

register_and_do_post_meta_boxes( $post );

add_screen_option(
	'layout_columns',
	array(
		'max'     => 2,
		'default' => 2,
	)
);

if ( 'post' === $post_type ) {
	$customize_display = '<p>' . __( '????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????Trackback?????????????????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'customize-display',
			'title'   => __( '?????????????????????' ),
			'content' => $customize_display,
		)
	);

	$title_and_editor  = '<p>' . __( '<strong>??????</strong>????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>';
	$title_and_editor .= '<p>' . __( '<strong>???????????????</strong>???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>';
	$title_and_editor .= '<p>' . __( '??????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>';
	$title_and_editor .= '<p>' . __( '????????????????????????????????????????????????HTML???????????????????????????????????????????????????????????????????????????&lt;p&gt;???&lt;br&gt;??????????????????????????????????????????????????????????????????????????????????????????&lt;br&gt;????????????????????????????????????&lt;p&gt;?????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>';
	$title_and_editor .= '<p>' . __( '????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>';
	$title_and_editor .= '<p>' . __( '??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>';
	$title_and_editor .= '<p>' . sprintf(
		/* translators: %s: Alt + F10 */
		__( '??????????????????????????????????????????????????????????????????????????????%s?????????????????????' ),
		'<kbd>Alt + F10</kbd>'
	) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'title-post-editor',
			'title'   => __( '????????????????????????' ),
			'content' => $title_and_editor,
		)
	);

	get_current_screen()->set_help_sidebar(
		'<p>' . sprintf(
			/* translators: %s: URL to Press This bookmarklet. */
			__( '???????????????<a href="%s">??????????????????</a>??????????????????' ),
			'tools.php'
		) . '</p>' .
			'<p><strong>' . __( '???????????????' ) . '</strong></p>' .
			'<p>' . __( '<a href="https://www.gechiui.com/support/gechiui-editor/">???????????????????????????</a>' ) . '</p>' .
			'<p>' . __( '<a href="https://www.gechiui.com/support/">??????</a>' ) . '</p>'
	);
} elseif ( 'page' === $post_type ) {
	$about_pages = '<p>' . __( '?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>' .
		'<p>' . __( '????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????/?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'about-pages',
			'title'   => __( '????????????' ),
			'content' => $about_pages,
		)
	);

	get_current_screen()->set_help_sidebar(
		'<p><strong>' . __( '???????????????' ) . '</strong></p>' .
			'<p>' . __( '<a href="https://www.gechiui.com/support/pages-add-new-screen/">?????????????????????</a>' ) . '</p>' .
			'<p>' . __( '<a href="https://www.gechiui.com/support/pages-screen/">??????????????????</a>' ) . '</p>' .
			'<p>' . __( '<a href="https://www.gechiui.com/support/">??????</a>' ) . '</p>'
	);
} elseif ( 'attachment' === $post_type ) {
	get_current_screen()->add_help_tab(
		array(
			'id'      => 'overview',
			'title'   => __( '??????' ),
			'content' =>
				'<p>' . __( '?????????????????????????????????????????????????????????' ) . '</p>' .
				'<p>' . __( '??????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>' .
				'<p>' . __( '?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>' .
				'<p>' . __( '??????????????????????????????????????????????????????' ) . '</p>',
		)
	);

	get_current_screen()->set_help_sidebar(
		'<p><strong>' . __( '???????????????' ) . '</strong></p>' .
		'<p>' . __( '<a href="https://www.gechiui.com/support/edit-media/">??????????????????</a>' ) . '</p>' .
		'<p>' . __( '<a href="https://www.gechiui.com/support/">??????</a>' ) . '</p>'
	);
}

if ( 'post' === $post_type || 'page' === $post_type ) {
	$inserting_media  = '<p>' . __( '?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>';
	$inserting_media .= '<p>' . __( '???????????????????????????????????????Twitter???YouTube???Flickr???????????????????????????????????????URL??????????????????????????????????????????????????????<a href="https://www.gechiui.com/support/embeds/">?????????????????????????????????</a>???' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'inserting-media',
			'title'   => __( '???????????????' ),
			'content' => $inserting_media,
		)
	);
}

if ( 'post' === $post_type ) {
	$publish_box  = '<p>' . __( '?????????????????????????????????????????????' ) . '</p>';
	$publish_box .= '<ul><li>' .
		__( '<strong>??????</strong>????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ) .
	'</li>';

	if ( current_theme_supports( 'post-formats' ) && post_type_supports( 'post', 'post-formats' ) ) {
		$publish_box .= '<li>' . __( '<strong>??????</strong>????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????10????????????????????????????????????????????????<a href="https://www.gechiui.com/support/post-formats/#supported-formats">?????????????????????????????????????????????</a>???' ) . '</li>';
	}

	if ( current_theme_supports( 'post-thumbnails' ) && post_type_supports( 'post', 'thumbnail' ) ) {
		$publish_box .= '<li>' . sprintf(
			/* translators: %s: Featured image. */
			__( '<strong>%s</strong>?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ),
			esc_html( $post_type_object->labels->featured_image )
		) . '</li>';
	}

	$publish_box .= '</ul>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'publish-box',
			'title'   => __( '????????????' ),
			'content' => $publish_box,
		)
	);

	$discussion_settings  = '<p>' . __( '<strong>??????Trackback</strong>??????Trackback??????????????????????????????????????????????????????????????????????????????????????????Trackback???URL?????????????????????????????????GeChiUI????????????????????????????????????????????????????????????Pingback???????????????' ) . '</p>';
	$discussion_settings .= '<p>' . __( '<strong>??????</strong>???????????????????????????????????????ping??????????????????????????????????????????????????????????????????????????????' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'discussion-settings',
			'title'   => __( '????????????' ),
			'content' => $discussion_settings,
		)
	);
} elseif ( 'page' === $post_type ) {
	$page_attributes = '<p>' . __( '<strong>??????</strong>?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>' .
		'<p>' . __( '<strong>??????</strong>?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????' ) . '</p>' .
		'<p>' . __( '<strong>??????</strong>????????????????????????????????????????????????????????????????????????>????????????????????????1??????????????????2???????????????????????????????????????????????????' ) . '</p>';

	get_current_screen()->add_help_tab(
		array(
			'id'      => 'page-attributes',
			'title'   => __( '????????????' ),
			'content' => $page_attributes,
		)
	);
}

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
<h1 class="gc-heading-inline">
<?php
echo esc_html( $title );
?>
</h1>

<?php
if ( isset( $post_new_file ) && current_user_can( $post_type_object->cap->create_posts ) ) {
	echo ' <a href="' . esc_url( admin_url( $post_new_file ) ) . '" class="page-title-action">' . esc_html( $post_type_object->labels->add_new ) . '</a>';
}
?>

<hr class="gc-header-end">

<?php if ( $notice ) : ?>
<div id="notice" class="notice notice-warning"><p id="has-newer-autosave"><?php echo $notice; ?></p></div>
<?php endif; ?>
<?php if ( $message ) : ?>
<div id="message" class="updated notice notice-success is-dismissible"><p><?php echo $message; ?></p></div>
<?php endif; ?>
<div id="lost-connection-notice" class="error hidden">
	<p><span class="spinner"></span> <?php _e( '<strong>???????????????</strong>?????????????????????????????????????????????' ); ?>
	<span class="hide-if-no-sessionstorage"><?php _e( '???????????????????????????????????????????????????????????????' ); ?></span>
	</p>
</div>
<form name="post" action="post.php" method="post" id="post"
<?php
/**
 * Fires inside the post editor form tag.
 *
 *
 *
 * @param GC_Post $post Post object.
 */
do_action( 'post_edit_form_tag', $post );

$referer = gc_get_referer();
?>
>
<?php gc_nonce_field( $nonce_action ); ?>
<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID; ?>" />
<input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action ); ?>" />
<input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr( $form_action ); ?>" />
<input type="hidden" id="post_author" name="post_author" value="<?php echo esc_attr( $post->post_author ); ?>" />
<input type="hidden" id="post_type" name="post_type" value="<?php echo esc_attr( $post_type ); ?>" />
<input type="hidden" id="original_post_status" name="original_post_status" value="<?php echo esc_attr( $post->post_status ); ?>" />
<input type="hidden" id="referredby" name="referredby" value="<?php echo $referer ? esc_url( $referer ) : ''; ?>" />
<?php if ( ! empty( $active_post_lock ) ) { ?>
<input type="hidden" id="active_post_lock" value="<?php echo esc_attr( implode( ':', $active_post_lock ) ); ?>" />
	<?php
}
if ( 'draft' !== get_post_status( $post ) ) {
	gc_original_referer_field( true, 'previous' );
}

echo $form_extra;

gc_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
gc_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
?>

<?php
/**
 * Fires at the beginning of the edit form.
 *
 * At this point, the required hidden fields and nonces have already been output.
 *
 *
 *
 * @param GC_Post $post Post object.
 */
do_action( 'edit_form_top', $post );
?>

<div id="poststuff">
<div id="post-body" class="metabox-holder columns-<?php echo ( 1 === get_current_screen()->get_columns() ) ? '1' : '2'; ?>">
<div id="post-body-content">

<?php if ( post_type_supports( $post_type, 'title' ) ) { ?>
<div id="titlediv">
<div id="titlewrap">
	<?php
	/**
	 * Filters the title field placeholder text.
	 *
	 *
	 * @param string  $text Placeholder text. Default '????????????'.
	 * @param GC_Post $post Post object.
	 */
	$title_placeholder = apply_filters( 'enter_title_here', __( '????????????' ), $post );
	?>
	<label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo $title_placeholder; ?></label>
	<input type="text" name="post_title" size="30" value="<?php echo esc_attr( $post->post_title ); ?>" id="title" spellcheck="true" autocomplete="off" />
</div>
	<?php
	/**
	 * Fires before the permalink field in the edit form.
	 *
	 *
	 * @param GC_Post $post Post object.
	 */
	do_action( 'edit_form_before_permalink', $post );
	?>
<div class="inside">
	<?php
	if ( $viewable ) :
		$sample_permalink_html = $post_type_object->public ? get_sample_permalink_html( $post->ID ) : '';

		// As of 4.4, the Get Shortlink button is hidden by default.
		if ( has_filter( 'pre_get_shortlink' ) || has_filter( 'get_shortlink' ) ) {
			$shortlink = gc_get_shortlink( $post->ID, 'post' );

			if ( ! empty( $shortlink ) && $shortlink !== $permalink && home_url( '?page_id=' . $post->ID ) !== $permalink ) {
				$sample_permalink_html .= '<input id="shortlink" type="hidden" value="' . esc_attr( $shortlink ) . '" />' .
					'<button type="button" class="button button-small" onclick="prompt(&#39;URL:&#39;, jQuery(\'#shortlink\').val());">' .
					__( '?????????????????????' ) .
					'</button>';
			}
		}

		if ( $post_type_object->public
			&& ! ( 'pending' === get_post_status( $post ) && ! current_user_can( $post_type_object->cap->publish_posts ) )
		) {
			$has_sample_permalink = $sample_permalink_html && 'auto-draft' !== $post->post_status;
			?>
	<div id="edit-slug-box" class="hide-if-no-js">
			<?php
			if ( $has_sample_permalink ) {
				echo $sample_permalink_html;
			}
			?>
	</div>
			<?php
		}
endif;
	?>
</div>
	<?php
	gc_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );
	?>
</div><!-- /titlediv -->
	<?php
}
/**
 * Fires after the title field.
 *
 *
 *
 * @param GC_Post $post Post object.
 */
do_action( 'edit_form_after_title', $post );

if ( post_type_supports( $post_type, 'editor' ) ) {
	$_gc_editor_expand_class = '';
	if ( $_gc_editor_expand ) {
		$_gc_editor_expand_class = ' gc-editor-expand';
	}
	?>
<div id="postdivrich" class="postarea<?php echo $_gc_editor_expand_class; ?>">

	<?php
	gc_editor(
		$post->post_content,
		'content',
		array(
			'_content_editor_dfw' => $_content_editor_dfw,
			'drag_drop_upload'    => true,
			'tabfocus_elements'   => 'content-html,save-post',
			'editor_height'       => 300,
			'tinymce'             => array(
				'resize'                  => false,
				'gc_autoresize_on'        => $_gc_editor_expand,
				'add_unload_trigger'      => false,
				'gc_keep_scroll_position' => ! $is_IE,
			),
		)
	);
	?>
<table id="post-status-info"><tbody><tr>
	<td id="gc-word-count" class="hide-if-no-js">
	<?php
	printf(
		/* translators: %s: Number of words. */
		__( '???????????????%s' ),
		'<span class="word-count">0</span>'
	);
	?>
	</td>
	<td class="autosave-info">
	<span class="autosave-message">&nbsp;</span>
	<?php
	if ( 'auto-draft' !== $post->post_status ) {
		echo '<span id="last-edit">';
		$last_user = get_userdata( get_post_meta( $post->ID, '_edit_last', true ) );
		if ( $last_user ) {
			/* translators: 1: Name of most recent post author, 2: Post edited date, 3: Post edited time. */
			printf( __( '?????????%1$s?????????%2$s%3$s' ), esc_html( $last_user->display_name ), mysql2date( __( 'Y???n???j???' ), $post->post_modified ), mysql2date( __( 'ag:i' ), $post->post_modified ) );
		} else {
			/* translators: 1: Post edited date, 2: Post edited time. */
			printf( __( '???????????????%1$s %2$s' ), mysql2date( __( 'Y???n???j???' ), $post->post_modified ), mysql2date( __( 'ag:i' ), $post->post_modified ) );
		}
		echo '</span>';
	}
	?>
	</td>
	<td id="content-resize-handle" class="hide-if-no-js"><br /></td>
</tr></tbody></table>

</div>
	<?php
}
/**
 * Fires after the content editor.
 *
 *
 *
 * @param GC_Post $post Post object.
 */
do_action( 'edit_form_after_editor', $post );
?>
</div><!-- /post-body-content -->

<div id="postbox-container-1" class="postbox-container">
<?php

if ( 'page' === $post_type ) {
	/**
	 * Fires before meta boxes with 'side' context are output for the 'page' post type.
	 *
	 * The submitpage box is a meta box with 'side' context, so this hook fires just before it is output.
	 *
	 *
	 * @param GC_Post $post Post object.
	 */
	do_action( 'submitpage_box', $post );
} else {
	/**
	 * Fires before meta boxes with 'side' context are output for all post types other than 'page'.
	 *
	 * The submitpost box is a meta box with 'side' context, so this hook fires just before it is output.
	 *
	 *
	 * @param GC_Post $post Post object.
	 */
	do_action( 'submitpost_box', $post );
}


do_meta_boxes( $post_type, 'side', $post );

?>
</div>
<div id="postbox-container-2" class="postbox-container">
<?php

do_meta_boxes( null, 'normal', $post );

if ( 'page' === $post_type ) {
	/**
	 * Fires after 'normal' context meta boxes have been output for the 'page' post type.
	 *
	 *
	 * @param GC_Post $post Post object.
	 */
	do_action( 'edit_page_form', $post );
} else {
	/**
	 * Fires after 'normal' context meta boxes have been output for all post types other than 'page'.
	 *
	 *
	 * @param GC_Post $post Post object.
	 */
	do_action( 'edit_form_advanced', $post );
}


do_meta_boxes( null, 'advanced', $post );

?>
</div>
<?php
/**
 * Fires after all meta box sections have been output, before the closing #post-body div.
 *
 *
 *
 * @param GC_Post $post Post object.
 */
do_action( 'dbx_post_sidebar', $post );

?>
</div><!-- /post-body -->
<br class="clear" />
</div><!-- /poststuff -->
</form>
</div>

<?php
if ( post_type_supports( $post_type, 'comments' ) ) {
	gc_comment_reply();
}
?>

<?php if ( ! gc_is_mobile() && post_type_supports( $post_type, 'title' ) && '' === $post->post_title ) : ?>
<script type="text/javascript">
try{document.post.title.focus();}catch(e){}
</script>
<?php endif; ?>
