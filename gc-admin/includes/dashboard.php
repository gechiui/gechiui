<?php
/**
 * GeChiUI Dashboard Widget Administration Screen API
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Registers dashboard widgets.
 *
 * Handles POST data, sets up filters.
 *
 *
 *
 * @global array $gc_registered_widgets
 * @global array $gc_registered_widget_controls
 * @global callable[] $gc_dashboard_control_callbacks
 */
function gc_dashboard_setup() {
	global $gc_registered_widgets, $gc_registered_widget_controls, $gc_dashboard_control_callbacks;

	$gc_dashboard_control_callbacks = array();
	$screen                         = get_current_screen();

	/* Register Widgets and Controls */

	$response = gc_check_browser_version();

	if ( $response && $response['upgrade'] ) {
		add_filter( 'postbox_classes_dashboard_dashboard_browser_nag', 'dashboard_browser_nag_class' );

		if ( $response['insecure'] ) {
			gc_add_dashboard_widget( 'dashboard_browser_nag', __( '您正在使用不安全的浏览器！' ), 'gc_dashboard_browser_nag' );
		} else {
			gc_add_dashboard_widget( 'dashboard_browser_nag', __( '您的浏览器版本很低！' ), 'gc_dashboard_browser_nag' );
		}
	}

	// PHP Version.
	$response = gc_check_php_version();

	if ( $response && isset( $response['is_acceptable'] ) && ! $response['is_acceptable']
		&& current_user_can( 'update_php' )
	) {
		add_filter( 'postbox_classes_dashboard_dashboard_php_nag', 'dashboard_php_nag_class' );

		gc_add_dashboard_widget( 'dashboard_php_nag', __( '建议更新PHP版本' ), 'gc_dashboard_php_nag' );
	}

	// Site Health.
	if ( current_user_can( 'view_site_health_checks' ) && ! is_network_admin() ) {
		if ( ! class_exists( 'GC_Site_Health' ) ) {
			require_once ABSPATH . 'gc-admin/includes/class-gc-site-health.php';
		}

		GC_Site_Health::get_instance();

		gc_enqueue_style( 'site-health' );
		gc_enqueue_script( 'site-health' );

		gc_add_dashboard_widget( 'dashboard_site_health', __( '站点健康状态' ), 'gc_dashboard_site_health' );
	}

	// Right Now.
	if ( is_blog_admin() && current_user_can( 'edit_posts' ) ) {
		gc_add_dashboard_widget( 'dashboard_right_now', __( '概览' ), 'gc_dashboard_right_now' );
	}

	if ( is_network_admin() ) {
		gc_add_dashboard_widget( 'network_dashboard_right_now', __( '概况' ), 'gc_network_dashboard_right_now' );
	}

	// Activity Widget.
	if ( is_blog_admin() ) {
		gc_add_dashboard_widget( 'dashboard_activity', __( '动态' ), 'gc_dashboard_site_activity' );
	}

	// QuickPress Widget.
	if ( is_blog_admin() && current_user_can( get_post_type_object( 'post' )->cap->create_posts ) ) {
		$quick_draft_title = sprintf( '<span class="hide-if-no-js">%1$s</span> <span class="hide-if-js">%2$s</span>', __( '快速草稿' ), __( '您最近的草稿' ) );
		gc_add_dashboard_widget( 'dashboard_quick_press', $quick_draft_title, 'gc_dashboard_quick_press' );
	}

	// GeChiUI Events and News.
	gc_add_dashboard_widget( 'dashboard_primary', __( 'GeChiUI活动及新闻' ), 'gc_dashboard_events_news' );

	if ( is_network_admin() ) {

		/**
		 * Fires after core widgets for the Network Admin dashboard have been registered.
		 *
		 */
		do_action( 'gc_network_dashboard_setup' );

		/**
		 * Filters the list of widgets to load for the Network Admin dashboard.
		 *
		 *
		 * @param string[] $dashboard_widgets An array of dashboard widget IDs.
		 */
		$dashboard_widgets = apply_filters( 'gc_network_dashboard_widgets', array() );
	} elseif ( is_user_admin() ) {

		/**
		 * Fires after core widgets for the User Admin dashboard have been registered.
		 *
		 */
		do_action( 'gc_user_dashboard_setup' );

		/**
		 * Filters the list of widgets to load for the User Admin dashboard.
		 *
		 *
		 * @param string[] $dashboard_widgets An array of dashboard widget IDs.
		 */
		$dashboard_widgets = apply_filters( 'gc_user_dashboard_widgets', array() );
	} else {

		/**
		 * Fires after core widgets for the admin dashboard have been registered.
		 *
		 */
		do_action( 'gc_dashboard_setup' );

		/**
		 * Filters the list of widgets to load for the admin dashboard.
		 *
		 *
		 * @param string[] $dashboard_widgets An array of dashboard widget IDs.
		 */
		$dashboard_widgets = apply_filters( 'gc_dashboard_widgets', array() );
	}

	foreach ( $dashboard_widgets as $widget_id ) {
		$name = empty( $gc_registered_widgets[ $widget_id ]['all_link'] ) ? $gc_registered_widgets[ $widget_id ]['name'] : $gc_registered_widgets[ $widget_id ]['name'] . " <a href='{$gc_registered_widgets[$widget_id]['all_link']}' class='edit-box open-box'>" . __( '查看所有' ) . '</a>';
		gc_add_dashboard_widget( $widget_id, $name, $gc_registered_widgets[ $widget_id ]['callback'], $gc_registered_widget_controls[ $widget_id ]['callback'] );
	}

	if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['widget_id'] ) ) {
		check_admin_referer( 'edit-dashboard-widget_' . $_POST['widget_id'], 'dashboard-widget-nonce' );
		ob_start(); // Hack - but the same hack gc-admin/widgets.php uses.
		gc_dashboard_trigger_widget_control( $_POST['widget_id'] );
		ob_end_clean();
		gc_redirect( remove_query_arg( 'edit' ) );
		exit;
	}

	/** This action is documented in gc-admin/includes/meta-boxes.php */
	do_action( 'do_meta_boxes', $screen->id, 'normal', '' );

	/** This action is documented in gc-admin/includes/meta-boxes.php */
	do_action( 'do_meta_boxes', $screen->id, 'side', '' );
}

/**
 * Adds a new dashboard widget.
 *
 *
 *
 *
 * @global callable[] $gc_dashboard_control_callbacks
 *
 * @param string   $widget_id        Widget ID  (used in the 'id' attribute for the widget).
 * @param string   $widget_name      Title of the widget.
 * @param callable $callback         Function that fills the widget with the desired content.
 *                                   The function should echo its output.
 * @param callable $control_callback Optional. Function that outputs controls for the widget. Default null.
 * @param array    $callback_args    Optional. Data that should be set as the $args property of the widget array
 *                                   (which is the second parameter passed to your callback). Default null.
 * @param string   $context          Optional. The context within the screen where the box should display.
 *                                   Accepts 'normal', 'side', 'column3', or 'column4'. Default 'normal'.
 * @param string   $priority         Optional. The priority within the context where the box should show.
 *                                   Accepts 'high', 'core', 'default', or 'low'. Default 'core'.
 */
function gc_add_dashboard_widget( $widget_id, $widget_name, $callback, $control_callback = null, $callback_args = null, $context = 'normal', $priority = 'core' ) {
	global $gc_dashboard_control_callbacks;

	$screen = get_current_screen();

	$private_callback_args = array( '__widget_basename' => $widget_name );

	if ( is_null( $callback_args ) ) {
		$callback_args = $private_callback_args;
	} elseif ( is_array( $callback_args ) ) {
		$callback_args = array_merge( $callback_args, $private_callback_args );
	}

	if ( $control_callback && is_callable( $control_callback ) && current_user_can( 'edit_dashboard' ) ) {
		$gc_dashboard_control_callbacks[ $widget_id ] = $control_callback;

		if ( isset( $_GET['edit'] ) && $widget_id === $_GET['edit'] ) {
			list($url)    = explode( '#', add_query_arg( 'edit', false ), 2 );
			$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( $url ) . '">' . __( '取消' ) . '</a></span>';
			$callback     = '_gc_dashboard_control_callback';
		} else {
			list($url)    = explode( '#', add_query_arg( 'edit', $widget_id ), 2 );
			$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( "$url#$widget_id" ) . '" class="edit-box open-box">' . __( '配置' ) . '</a></span>';
		}
	}

	$side_widgets = array( 'dashboard_quick_press', 'dashboard_primary' );

	if ( in_array( $widget_id, $side_widgets, true ) ) {
		$context = 'side';
	}

	$high_priority_widgets = array( 'dashboard_browser_nag', 'dashboard_php_nag' );

	if ( in_array( $widget_id, $high_priority_widgets, true ) ) {
		$priority = 'high';
	}

	if ( empty( $context ) ) {
		$context = 'normal';
	}

	if ( empty( $priority ) ) {
		$priority = 'core';
	}

	add_meta_box( $widget_id, $widget_name, $callback, $screen, $context, $priority, $callback_args );
}

/**
 * Outputs controls for the current dashboard widget.
 *
 * @access private
 *
 *
 * @param mixed $dashboard
 * @param array $meta_box
 */
function _gc_dashboard_control_callback( $dashboard, $meta_box ) {
	echo '<form method="post" class="dashboard-widget-control-form gc-clearfix">';
	gc_dashboard_trigger_widget_control( $meta_box['id'] );
	gc_nonce_field( 'edit-dashboard-widget_' . $meta_box['id'], 'dashboard-widget-nonce' );
	echo '<input type="hidden" name="widget_id" value="' . esc_attr( $meta_box['id'] ) . '" />';
	submit_button( __( '保存更改' ) );
	echo '</form>';
}

/**
 * Displays the dashboard.
 *
 *
 */
function gc_dashboard() {
	$screen      = get_current_screen();
	$columns     = absint( $screen->get_columns() );
	$columns_css = '';

	if ( $columns ) {
		$columns_css = " columns-$columns";
	}
	?>
<div id="dashboard-widgets" class="metabox-holder<?php echo $columns_css; ?>">
	<div id="postbox-container-1" class="postbox-container">
	<?php do_meta_boxes( $screen->id, 'normal', '' ); ?>
	</div>
	<div id="postbox-container-2" class="postbox-container">
	<?php do_meta_boxes( $screen->id, 'side', '' ); ?>
	</div>
	<div id="postbox-container-3" class="postbox-container">
	<?php do_meta_boxes( $screen->id, 'column3', '' ); ?>
	</div>
	<div id="postbox-container-4" class="postbox-container">
	<?php do_meta_boxes( $screen->id, 'column4', '' ); ?>
	</div>
</div>

	<?php
	gc_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
	gc_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

}

//
// Dashboard Widgets.
//

/**
 * Dashboard widget that displays some basic stats about the site.
 *
 * Formerly '概况'. A streamlined '概览' as of 3.8.
 *
 *
 */
function gc_dashboard_right_now() {
	?>
	<div class="main">
	<ul>
	<?php
	// Posts and Pages.
	foreach ( array( 'post', 'page' ) as $post_type ) {
		$num_posts = gc_count_posts( $post_type );

		if ( $num_posts && $num_posts->publish ) {
			if ( 'post' === $post_type ) {
				/* translators: %s: Number of posts. */
				$text = _n( '%s篇文章', '%s篇文章', $num_posts->publish );
			} else {
				/* translators: %s: Number of pages. */
				$text = _n( '%s个页面', '%s个页面', $num_posts->publish );
			}

			$text             = sprintf( $text, number_format_i18n( $num_posts->publish ) );
			$post_type_object = get_post_type_object( $post_type );

			if ( $post_type_object && current_user_can( $post_type_object->cap->edit_posts ) ) {
				printf( '<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>', $post_type, $text );
			} else {
				printf( '<li class="%1$s-count"><span>%2$s</span></li>', $post_type, $text );
			}
		}
	}

	// Comments.
	$num_comm = gc_count_comments();

	if ( $num_comm && ( $num_comm->approved || $num_comm->moderated ) ) {
		/* translators: %s: Number of comments. */
		$text = sprintf( _n( '%s条评论', '%s条评论', $num_comm->approved ), number_format_i18n( $num_comm->approved ) );
		?>
		<li class="comment-count">
			<a href="edit-comments.php"><?php echo $text; ?></a>
		</li>
		<?php
		$moderated_comments_count_i18n = number_format_i18n( $num_comm->moderated );
		/* translators: %s: Number of comments. */
		$text = sprintf( _n( '%s条评论待审', '%s条评论待审', $num_comm->moderated ), $moderated_comments_count_i18n );
		?>
		<li class="comment-mod-count<?php echo ! $num_comm->moderated ? ' hidden' : ''; ?>">
			<a href="edit-comments.php?comment_status=moderated" class="comments-in-moderation-text"><?php echo $text; ?></a>
		</li>
		<?php
	}

	/**
	 * Filters the array of extra elements to list in the '概览'
	 * dashboard widget.
	 *
	 * Prior to 3.8.0, the widget was named '概况'. Each element
	 * is wrapped in list-item tags on output.
	 *
	 *
	 * @param string[] $items Array of extra '概览' widget items.
	 */
	$elements = apply_filters( 'dashboard_glance_items', array() );

	if ( $elements ) {
		echo '<li>' . implode( "</li>\n<li>", $elements ) . "</li>\n";
	}

	?>
	</ul>
	<?php
	update_right_now_message();

	// Check if search engines are asked not to index this site.
	if ( ! is_network_admin() && ! is_user_admin()
		&& current_user_can( 'manage_options' ) && ! get_option( 'blog_public' )
	) {

		/**
		 * Filters the link title attribute for the '自动建议搜索引擎不抓取'
		 * message displayed in the '概览' dashboard widget.
		 *
		 * Prior to 3.8.0, the widget was named '概况'.
		 *
		 *
		 * @param string $title Default attribute text.
		 */
		$title = apply_filters( 'privacy_on_link_title', '' );

		/**
		 * Filters the link label for the '自动建议搜索引擎不抓取' message
		 * displayed in the '概览' dashboard widget.
		 *
		 * Prior to 3.8.0, the widget was named '概况'.
		 *
		 *
		 * @param string $content Default text.
		 */
		$content = apply_filters( 'privacy_on_link_text', __( '自动建议搜索引擎不抓取' ) );

		$title_attr = '' === $title ? '' : " title='$title'";

		echo "<p class='search-engines-info'><a href='options-reading.php'$title_attr>$content</a></p>";
	}
	?>
	</div>
	<?php
	/*
	 * activity_box_end has a core action, but only prints content when multisite.
	 * Using an output buffer is the only way to really check if anything's displayed here.
	 */
	ob_start();

	/**
	 * Fires at the end of the '概览' dashboard widget.
	 *
	 * Prior to 3.8.0, the widget was named '概况'.
	 *
	 */
	do_action( 'rightnow_end' );

	/**
	 * Fires at the end of the '概览' dashboard widget.
	 *
	 * Prior to 3.8.0, the widget was named '概况'.
	 *
	 */
	do_action( 'activity_box_end' );

	$actions = ob_get_clean();

	if ( ! empty( $actions ) ) :
		?>
	<div class="sub">
		<?php echo $actions; ?>
	</div>
		<?php
	endif;
}

/**
 *
 */
function gc_network_dashboard_right_now() {
	$actions = array();

	if ( current_user_can( 'create_sites' ) ) {
		$actions['create-site'] = '<a href="' . network_admin_url( 'site-new.php' ) . '">' . __( '创建新站点' ) . '</a>';
	}
	if ( current_user_can( 'create_users' ) ) {
		$actions['create-user'] = '<a href="' . network_admin_url( 'user-new.php' ) . '">' . __( '创建新用户' ) . '</a>';
	}

	$c_users = get_user_count();
	$c_blogs = get_blog_count();

	/* translators: %s: Number of users on the network. */
	$user_text = sprintf( _n( '%s个用户', '%s个用户', $c_users ), number_format_i18n( $c_users ) );
	/* translators: %s: Number of sites on the network. */
	$blog_text = sprintf( _n( '%s个站点', '%s个站点', $c_blogs ), number_format_i18n( $c_blogs ) );

	/* translators: 1: Text indicating the number of sites on the network, 2: Text indicating the number of users on the network. */
	$sentence = sprintf( __( '您有%1$s和%2$s。' ), $blog_text, $user_text );

	if ( $actions ) {
		echo '<ul class="subsubsub">';
		foreach ( $actions as $class => $action ) {
			$actions[ $class ] = "\t<li class='$class'>$action";
		}
		echo implode( " |</li>\n", $actions ) . "</li>\n";
		echo '</ul>';
	}
	?>
	<br class="clear" />

	<p class="youhave"><?php echo $sentence; ?></p>


	<?php
		/**
		 * Fires in the Network Admin '概况' dashboard widget
		 * just before the user and site search form fields.
		 *
		 * @since MU
		 */
		do_action( 'gcmuadminresult' );
	?>

	<form action="<?php echo esc_url( network_admin_url( 'users.php' ) ); ?>" method="get">
		<p>
			<label class="screen-reader-text" for="search-users"><?php _e( '搜索用户' ); ?></label>
			<input type="search" name="s" value="" size="30" autocomplete="off" id="search-users" />
			<?php submit_button( __( '搜索用户' ), '', false, false, array( 'id' => 'submit_users' ) ); ?>
		</p>
	</form>

	<form action="<?php echo esc_url( network_admin_url( 'sites.php' ) ); ?>" method="get">
		<p>
			<label class="screen-reader-text" for="search-sites"><?php _e( '搜索站点' ); ?></label>
			<input type="search" name="s" value="" size="30" autocomplete="off" id="search-sites" />
			<?php submit_button( __( '搜索站点' ), '', false, false, array( 'id' => 'submit_sites' ) ); ?>
		</p>
	</form>
	<?php
	/**
	 * Fires at the end of the '概况' widget in the Network Admin dashboard.
	 *
	 * @since MU
	 */
	do_action( 'mu_rightnow_end' );

	/**
	 * Fires at the end of the '概况' widget in the Network Admin dashboard.
	 *
	 * @since MU
	 */
	do_action( 'mu_activity_box_end' );
}

/**
 * The Quick Draft widget display and creation of drafts.
 *
 *
 *
 * @global int $post_ID
 *
 * @param string|false $error_msg Optional. Error message. Default false.
 */
function gc_dashboard_quick_press( $error_msg = false ) {
	global $post_ID;

	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	// Check if a new auto-draft (= no new post_ID) is needed or if the old can be used.
	$last_post_id = (int) get_user_option( 'dashboard_quick_press_last_post_id' ); // Get the last post_ID.

	if ( $last_post_id ) {
		$post = get_post( $last_post_id );

		if ( empty( $post ) || 'auto-draft' !== $post->post_status ) { // auto-draft doesn't exist anymore.
			$post = get_default_post_to_edit( 'post', true );
			update_user_option( get_current_user_id(), 'dashboard_quick_press_last_post_id', (int) $post->ID ); // Save post_ID.
		} else {
			$post->post_title = ''; // Remove the auto draft title.
		}
	} else {
		$post    = get_default_post_to_edit( 'post', true );
		$user_id = get_current_user_id();

		// Don't create an option if this is a super admin who does not belong to this site.
		if ( in_array( get_current_blog_id(), array_keys( get_blogs_of_user( $user_id ) ), true ) ) {
			update_user_option( $user_id, 'dashboard_quick_press_last_post_id', (int) $post->ID ); // Save post_ID.
		}
	}

	$post_ID = (int) $post->ID;
	?>

	<form name="post" action="<?php echo esc_url( admin_url( 'post.php' ) ); ?>" method="post" id="quick-press" class="initial-form hide-if-no-js">

		<?php if ( $error_msg ) : ?>
		<div class="error"><?php echo $error_msg; ?></div>
		<?php endif; ?>

		<div class="input-text-wrap" id="title-wrap">
			<label for="title">
				<?php
				/** This filter is documented in gc-admin/edit-form-advanced.php */
				echo apply_filters( 'enter_title_here', __( '标题' ), $post );
				?>
			</label>
			<input type="text" name="post_title" id="title" autocomplete="off" />
		</div>

		<div class="textarea-wrap" id="description-wrap">
			<label for="content"><?php _e( '内容' ); ?></label>
			<textarea name="content" id="content" placeholder="<?php esc_attr_e( '在想些什么？' ); ?>" class="mceEditor" rows="3" cols="15" autocomplete="off"></textarea>
		</div>

		<p class="submit">
			<input type="hidden" name="action" id="quickpost-action" value="post-quickdraft-save" />
			<input type="hidden" name="post_ID" value="<?php echo $post_ID; ?>" />
			<input type="hidden" name="post_type" value="post" />
			<?php gc_nonce_field( 'add-post' ); ?>
			<?php submit_button( __( '保存草稿' ), 'primary', 'save', false, array( 'id' => 'save-post' ) ); ?>
			<br class="clear" />
		</p>

	</form>
	<?php
	gc_dashboard_recent_drafts();
}

/**
 * Show recent drafts of the user on the dashboard.
 *
 *
 *
 * @param GC_Post[]|false $drafts Optional. Array of posts to display. Default false.
 */
function gc_dashboard_recent_drafts( $drafts = false ) {
	if ( ! $drafts ) {
		$query_args = array(
			'post_type'      => 'post',
			'post_status'    => 'draft',
			'author'         => get_current_user_id(),
			'posts_per_page' => 4,
			'orderby'        => 'modified',
			'order'          => 'DESC',
		);

		/**
		 * Filters the post query arguments for the 'Recent Drafts' dashboard widget.
		 *
		 *
		 * @param array $query_args The query arguments for the 'Recent Drafts' dashboard widget.
		 */
		$query_args = apply_filters( 'dashboard_recent_drafts_query_args', $query_args );

		$drafts = get_posts( $query_args );
		if ( ! $drafts ) {
			return;
		}
	}

	echo '<div class="drafts">';

	if ( count( $drafts ) > 3 ) {
		printf(
			'<p class="view-all"><a href="%s">%s</a></p>' . "\n",
			esc_url( admin_url( 'edit.php?post_status=draft' ) ),
			__( '查看所有草稿' )
		);
	}

	echo '<h2 class="hide-if-no-js">' . __( '您最近的草稿' ) . "</h2>\n";
	echo '<ul>';

	/* translators: Maximum number of words used in a preview of a draft on the dashboard. */
	$draft_length = (int) _x( '10', 'draft_length' );

	$drafts = array_slice( $drafts, 0, 3 );
	foreach ( $drafts as $draft ) {
		$url   = get_edit_post_link( $draft->ID );
		$title = _draft_or_post_title( $draft->ID );

		echo "<li>\n";
		printf(
			'<div class="draft-title"><a href="%s" aria-label="%s">%s</a><time datetime="%s">%s</time></div>',
			esc_url( $url ),
			/* translators: %s: Post title. */
			esc_attr( sprintf( __( '编辑“%s”' ), $title ) ),
			esc_html( $title ),
			get_the_time( 'c', $draft ),
			get_the_time( __( 'Y年n月j日' ), $draft )
		);

		$the_content = gc_trim_words( $draft->post_content, $draft_length );

		if ( $the_content ) {
			echo '<p>' . $the_content . '</p>';
		}
		echo "</li>\n";
	}

	echo "</ul>\n";
	echo '</div>';
}

/**
 * Outputs a row for the Recent Comments widget.
 *
 * @access private
 *
 *
 * @global GC_Comment $comment Global comment object.
 *
 * @param GC_Comment $comment   The current comment.
 * @param bool       $show_date Optional. Whether to display the date.
 */
function _gc_dashboard_recent_comments_row( &$comment, $show_date = true ) {
	$GLOBALS['comment'] = clone $comment;

	if ( $comment->comment_post_ID > 0 ) {
		$comment_post_title = _draft_or_post_title( $comment->comment_post_ID );
		$comment_post_url   = get_the_permalink( $comment->comment_post_ID );
		$comment_post_link  = "<a href='$comment_post_url'>$comment_post_title</a>";
	} else {
		$comment_post_link = '';
	}

	$actions_string = '';
	if ( current_user_can( 'edit_comment', $comment->comment_ID ) ) {
		// Pre-order it: Approve | Reply | Edit | Spam | Trash.
		$actions = array(
			'approve'   => '',
			'unapprove' => '',
			'reply'     => '',
			'edit'      => '',
			'spam'      => '',
			'trash'     => '',
			'delete'    => '',
			'view'      => '',
		);

		$del_nonce     = esc_html( '_gcnonce=' . gc_create_nonce( "delete-comment_$comment->comment_ID" ) );
		$approve_nonce = esc_html( '_gcnonce=' . gc_create_nonce( "approve-comment_$comment->comment_ID" ) );

		$approve_url   = esc_url( "comment.php?action=approvecomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$approve_nonce" );
		$unapprove_url = esc_url( "comment.php?action=unapprovecomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$approve_nonce" );
		$spam_url      = esc_url( "comment.php?action=spamcomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$del_nonce" );
		$trash_url     = esc_url( "comment.php?action=trashcomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$del_nonce" );
		$delete_url    = esc_url( "comment.php?action=deletecomment&p=$comment->comment_post_ID&c=$comment->comment_ID&$del_nonce" );

		$actions['approve'] = sprintf(
			'<a href="%s" data-gc-lists="%s" class="vim-a aria-button-if-js" aria-label="%s">%s</a>',
			$approve_url,
			"dim:the-comment-list:comment-{$comment->comment_ID}:unapproved:e7e7d3:e7e7d3:new=approved",
			esc_attr__( '批准此评论' ),
			__( '批准' )
		);

		$actions['unapprove'] = sprintf(
			'<a href="%s" data-gc-lists="%s" class="vim-u aria-button-if-js" aria-label="%s">%s</a>',
			$unapprove_url,
			"dim:the-comment-list:comment-{$comment->comment_ID}:unapproved:e7e7d3:e7e7d3:new=unapproved",
			esc_attr__( '驳回此评论' ),
			__( '驳回' )
		);

		$actions['edit'] = sprintf(
			'<a href="%s" aria-label="%s">%s</a>',
			"comment.php?action=editcomment&amp;c={$comment->comment_ID}",
			esc_attr__( '编辑此评论' ),
			__( '编辑' )
		);

		$actions['reply'] = sprintf(
			'<button type="button" onclick="window.commentReply && commentReply.open(\'%s\',\'%s\');" class="vim-r button-link hide-if-no-js" aria-label="%s">%s</button>',
			$comment->comment_ID,
			$comment->comment_post_ID,
			esc_attr__( '回复此评论' ),
			__( '回复' )
		);

		$actions['spam'] = sprintf(
			'<a href="%s" data-gc-lists="%s" class="vim-s vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
			$spam_url,
			"delete:the-comment-list:comment-{$comment->comment_ID}::spam=1",
			esc_attr__( '将此评论标记为垃圾' ),
			/* translators: "标记为垃圾评论" link. */
			_x( '垃圾', 'verb' )
		);

		if ( ! EMPTY_TRASH_DAYS ) {
			$actions['delete'] = sprintf(
				'<a href="%s" data-gc-lists="%s" class="delete vim-d vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				$delete_url,
				"delete:the-comment-list:comment-{$comment->comment_ID}::trash=1",
				esc_attr__( '永久删除此评论' ),
				__( '永久删除' )
			);
		} else {
			$actions['trash'] = sprintf(
				'<a href="%s" data-gc-lists="%s" class="delete vim-d vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				$trash_url,
				"delete:the-comment-list:comment-{$comment->comment_ID}::trash=1",
				esc_attr__( '将此评论移至回收站' ),
				_x( '移至回收站', 'verb' )
			);
		}

		$actions['view'] = sprintf(
			'<a class="comment-link" href="%s" aria-label="%s">%s</a>',
			esc_url( get_comment_link( $comment ) ),
			esc_attr__( '查看此评论' ),
			__( '查看' )
		);

		/**
		 * Filters the action links displayed for each comment in the '近期评论'
		 * dashboard widget.
		 *
		 *
		 * @param string[]   $actions An array of comment actions. Default actions include:
		 *                            'Approve', '驳回', 'Edit', 'Reply', 'Spam',
		 *                            'Delete', and 'Trash'.
		 * @param GC_Comment $comment The comment object.
		 */
		$actions = apply_filters( 'comment_row_actions', array_filter( $actions ), $comment );

		$i = 0;

		foreach ( $actions as $action => $link ) {
			++$i;

			if ( ( ( 'approve' === $action || 'unapprove' === $action ) && 2 === $i )
				|| 1 === $i
			) {
				$sep = '';
			} else {
				$sep = ' | ';
			}

			// Reply and quickedit need a hide-if-no-js span.
			if ( 'reply' === $action || 'quickedit' === $action ) {
				$action .= ' hide-if-no-js';
			}

			if ( 'view' === $action && '1' !== $comment->comment_approved ) {
				$action .= ' hidden';
			}

			$actions_string .= "<span class='$action'>$sep$link</span>";
		}
	}
	?>

		<li id="comment-<?php echo $comment->comment_ID; ?>" <?php comment_class( array( 'comment-item', gc_get_comment_status( $comment ) ), $comment ); ?>>

			<?php
			$comment_row_class = '';

			if ( get_option( 'show_avatars' ) ) {
				echo get_avatar( $comment, 50, 'mystery' );
				$comment_row_class .= ' has-avatar';
			}
			?>

			<?php if ( ! $comment->comment_type || 'comment' === $comment->comment_type ) : ?>

			<div class="dashboard-comment-wrap has-row-actions <?php echo $comment_row_class; ?>">
			<p class="comment-meta">
				<?php
				// Comments might not have a post they relate to, e.g. programmatically created ones.
				if ( $comment_post_link ) {
					printf(
						/* translators: 1: Comment author, 2: Post link, 3: Notification if the comment is pending. */
						__( '由%1$s发表在《%2$s》%3$s' ),
						'<cite class="comment-author">' . get_comment_author_link( $comment ) . '</cite>',
						$comment_post_link,
						'<span class="approve">' . __( '[待审]' ) . '</span>'
					);
				} else {
					printf(
						/* translators: 1: Comment author, 2: Notification if the comment is pending. */
						__( '由%1$s %2$s' ),
						'<cite class="comment-author">' . get_comment_author_link( $comment ) . '</cite>',
						'<span class="approve">' . __( '[待审]' ) . '</span>'
					);
				}
				?>
			</p>

				<?php
			else :
				switch ( $comment->comment_type ) {
					case 'pingback':
						$type = __( 'Pingback' );
						break;
					case 'trackback':
						$type = __( 'Trackback' );
						break;
					default:
						$type = ucwords( $comment->comment_type );
				}
				$type = esc_html( $type );
				?>
			<div class="dashboard-comment-wrap has-row-actions">
			<p class="comment-meta">
				<?php
				// Pingbacks, Trackbacks or custom comment types might not have a post they relate to, e.g. programmatically created ones.
				if ( $comment_post_link ) {
					printf(
						/* translators: 1: Type of comment, 2: Post link, 3: Notification if the comment is pending. */
						_x( '%2$s上的%1$s%3$s', 'dashboard' ),
						"<strong>$type</strong>",
						$comment_post_link,
						'<span class="approve">' . __( '[待审]' ) . '</span>'
					);
				} else {
					printf(
						/* translators: 1: Type of comment, 2: Notification if the comment is pending. */
						_x( '%1$s%2$s', 'dashboard' ),
						"<strong>$type</strong>",
						'<span class="approve">' . __( '[待审]' ) . '</span>'
					);
				}
				?>
			</p>
			<p class="comment-author"><?php comment_author_link( $comment ); ?></p>

			<?php endif; // comment_type ?>
			<blockquote><p><?php comment_excerpt( $comment ); ?></p></blockquote>
			<?php if ( $actions_string ) : ?>
			<p class="row-actions"><?php echo $actions_string; ?></p>
			<?php endif; ?>
			</div>
		</li>
	<?php
	$GLOBALS['comment'] = null;
}

/**
 * Callback function for Activity widget.
 *
 *
 */
function gc_dashboard_site_activity() {

	echo '<div id="activity-widget">';

	$future_posts = gc_dashboard_recent_posts(
		array(
			'max'    => 5,
			'status' => 'future',
			'order'  => 'ASC',
			'title'  => __( '即将发布' ),
			'id'     => 'future-posts',
		)
	);
	$recent_posts = gc_dashboard_recent_posts(
		array(
			'max'    => 5,
			'status' => 'publish',
			'order'  => 'DESC',
			'title'  => __( '最近发布' ),
			'id'     => 'published-posts',
		)
	);

	$recent_comments = gc_dashboard_recent_comments();

	if ( ! $future_posts && ! $recent_posts && ! $recent_comments ) {
		echo '<div class="no-activity">';
		echo '<p>' . __( '还没有动态！' ) . '</p>';
		echo '</div>';
	}

	echo '</div>';
}

/**
 * Generates Publishing Soon and Recently Published sections.
 *
 *
 *
 * @param array $args {
 *     An array of query and display arguments.
 *
 *     @type int    $max     Number of posts to display.
 *     @type string $status  Post status.
 *     @type string $order   Designates ascending ('ASC') or descending ('DESC') order.
 *     @type string $title   Section title.
 *     @type string $id      The container id.
 * }
 * @return bool False if no posts were found. True otherwise.
 */
function gc_dashboard_recent_posts( $args ) {
	$query_args = array(
		'post_type'      => 'post',
		'post_status'    => $args['status'],
		'orderby'        => 'date',
		'order'          => $args['order'],
		'posts_per_page' => (int) $args['max'],
		'no_found_rows'  => true,
		'cache_results'  => false,
		'perm'           => ( 'future' === $args['status'] ) ? 'editable' : 'readable',
	);

	/**
	 * Filters the query arguments used for the Recent Posts widget.
	 *
	 *
	 * @param array $query_args The arguments passed to GC_Query to produce the list of posts.
	 */
	$query_args = apply_filters( 'dashboard_recent_posts_query_args', $query_args );

	$posts = new GC_Query( $query_args );

	if ( $posts->have_posts() ) {

		echo '<div id="' . $args['id'] . '" class="activity-block">';

		echo '<h3>' . $args['title'] . '</h3>';

		echo '<ul>';

		$today    = current_time( 'Y-m-d' );
		$tomorrow = current_datetime()->modify( '+1 day' )->format( 'Y-m-d' );
		$year     = current_time( 'Y' );

		while ( $posts->have_posts() ) {
			$posts->the_post();

			$time = get_the_time( 'U' );

			if ( gmdate( 'Y-m-d', $time ) === $today ) {
				$relative = __( '今天' );
			} elseif ( gmdate( 'Y-m-d', $time ) === $tomorrow ) {
				$relative = __( '明天' );
			} elseif ( gmdate( 'Y', $time ) !== $year ) {
				/* translators: Date and time format for recent posts on the dashboard, from a different calendar year, see https://www.php.net/manual/datetime.format.php */
				$relative = date_i18n( __( 'Y-m-d' ), $time );
			} else {
				/* translators: Date and time format for recent posts on the dashboard, see https://www.php.net/manual/datetime.format.php */
				$relative = date_i18n( __( 'm月d日' ), $time );
			}

			// Use the post edit link for those who can edit, the permalink otherwise.
			$recent_post_link = current_user_can( 'edit_post', get_the_ID() ) ? get_edit_post_link() : get_permalink();

			$draft_or_post_title = _draft_or_post_title();
			printf(
				'<li><span>%1$s</span> <a href="%2$s" aria-label="%3$s">%4$s</a></li>',
				/* translators: 1: Relative date, 2: Time. */
				sprintf( _x( '%1$s%2$s', 'dashboard' ), $relative, get_the_time() ),
				$recent_post_link,
				/* translators: %s: Post title. */
				esc_attr( sprintf( __( '编辑“%s”' ), $draft_or_post_title ) ),
				$draft_or_post_title
			);
		}

		echo '</ul>';
		echo '</div>';

	} else {
		return false;
	}

	gc_reset_postdata();

	return true;
}

/**
 * Show Comments section.
 *
 *
 *
 * @param int $total_items Optional. Number of comments to query. Default 5.
 * @return bool False if no comments were found. True otherwise.
 */
function gc_dashboard_recent_comments( $total_items = 5 ) {
	// Select all comment types and filter out spam later for better query performance.
	$comments = array();

	$comments_query = array(
		'number' => $total_items * 5,
		'offset' => 0,
	);

	if ( ! current_user_can( 'edit_posts' ) ) {
		$comments_query['status'] = 'approve';
	}

	while ( count( $comments ) < $total_items && $possible = get_comments( $comments_query ) ) {
		if ( ! is_array( $possible ) ) {
			break;
		}

		foreach ( $possible as $comment ) {
			if ( ! current_user_can( 'read_post', $comment->comment_post_ID ) ) {
				continue;
			}

			$comments[] = $comment;

			if ( count( $comments ) === $total_items ) {
				break 2;
			}
		}

		$comments_query['offset'] += $comments_query['number'];
		$comments_query['number']  = $total_items * 10;
	}

	if ( $comments ) {
		echo '<div id="latest-comments" class="activity-block table-view-list">';
		echo '<h3>' . __( '近期评论' ) . '</h3>';

		echo '<ul id="the-comment-list" data-gc-lists="list:comment">';
		foreach ( $comments as $comment ) {
			_gc_dashboard_recent_comments_row( $comment );
		}
		echo '</ul>';

		if ( current_user_can( 'edit_posts' ) ) {
			echo '<h3 class="screen-reader-text">' . __( '查看更多评论' ) . '</h3>';
			_get_list_table( 'GC_Comments_List_Table' )->views();
		}

		gc_comment_reply( -1, false, 'dashboard', false );
		gc_comment_trashnotice();

		echo '</div>';
	} else {
		return false;
	}
	return true;
}

/**
 * Display generic dashboard RSS widget feed.
 *
 *
 *
 * @param string $widget_id
 */
function gc_dashboard_rss_output( $widget_id ) {
	$widgets = get_option( 'dashboard_widget_options' );
	echo '<div class="rss-widget">';
	gc_widget_rss_output( $widgets[ $widget_id ] );
	echo '</div>';
}

/**
 * Checks to see if all of the feed url in $check_urls are cached.
 *
 * If $check_urls is empty, look for the rss feed url found in the dashboard
 * widget options of $widget_id. If cached, call $callback, a function that
 * echoes out output for this widget. If not cache, echo a "Loading..." stub
 * which is later replaced by Ajax call (see top of /gc-admin/index.php)
 *
 *
 *
 *              by adding it to the function signature.
 *
 * @param string   $widget_id  The widget ID.
 * @param callable $callback   The callback function used to display each feed.
 * @param array    $check_urls RSS feeds.
 * @param mixed    ...$args    Optional additional parameters to pass to the callback function.
 * @return bool True on success, false on failure.
 */
function gc_dashboard_cached_rss_widget( $widget_id, $callback, $check_urls = array(), ...$args ) {
	$loading    = '<p class="widget-loading hide-if-no-js">' . __( '载入中&hellip;' ) . '</p><div class="hide-if-js notice notice-error inline"><p>' . __( '这个小工具需要JavaScript支持。' ) . '</p></div>';
	$doing_ajax = gc_doing_ajax();

	if ( empty( $check_urls ) ) {
		$widgets = get_option( 'dashboard_widget_options' );

		if ( empty( $widgets[ $widget_id ]['url'] ) && ! $doing_ajax ) {
			echo $loading;
			return false;
		}

		$check_urls = array( $widgets[ $widget_id ]['url'] );
	}

	$locale    = get_user_locale();
	$cache_key = 'dash_v2_' . md5( $widget_id . '_' . $locale );
	$output    = get_transient( $cache_key );

	if ( false !== $output ) {
		echo $output;
		return true;
	}

	if ( ! $doing_ajax ) {
		echo $loading;
		return false;
	}

	if ( $callback && is_callable( $callback ) ) {
		array_unshift( $args, $widget_id, $check_urls );
		ob_start();
		call_user_func_array( $callback, $args );
		// Default lifetime in cache of 12 hours (same as the feeds).
		set_transient( $cache_key, ob_get_flush(), 12 * HOUR_IN_SECONDS );
	}

	return true;
}

//
// Dashboard Widgets Controls.
//

/**
 * Calls widget control callback.
 *
 *
 *
 * @global callable[] $gc_dashboard_control_callbacks
 *
 * @param int|false $widget_control_id Optional. Registered widget ID. Default false.
 */
function gc_dashboard_trigger_widget_control( $widget_control_id = false ) {
	global $gc_dashboard_control_callbacks;

	if ( is_scalar( $widget_control_id ) && $widget_control_id
		&& isset( $gc_dashboard_control_callbacks[ $widget_control_id ] )
		&& is_callable( $gc_dashboard_control_callbacks[ $widget_control_id ] )
	) {
		call_user_func(
			$gc_dashboard_control_callbacks[ $widget_control_id ],
			'',
			array(
				'id'       => $widget_control_id,
				'callback' => $gc_dashboard_control_callbacks[ $widget_control_id ],
			)
		);
	}
}

/**
 * The RSS dashboard widget control.
 *
 * Sets up $args to be used as input to gc_widget_rss_form(). Handles POST data
 * from RSS-type widgets.
 *
 *
 *
 * @param string $widget_id
 * @param array  $form_inputs
 */
function gc_dashboard_rss_control( $widget_id, $form_inputs = array() ) {
	$widget_options = get_option( 'dashboard_widget_options' );

	if ( ! $widget_options ) {
		$widget_options = array();
	}

	if ( ! isset( $widget_options[ $widget_id ] ) ) {
		$widget_options[ $widget_id ] = array();
	}

	$number = 1; // Hack to use gc_widget_rss_form().

	$widget_options[ $widget_id ]['number'] = $number;

	if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['widget-rss'][ $number ] ) ) {
		$_POST['widget-rss'][ $number ]         = gc_unslash( $_POST['widget-rss'][ $number ] );
		$widget_options[ $widget_id ]           = gc_widget_rss_process( $_POST['widget-rss'][ $number ] );
		$widget_options[ $widget_id ]['number'] = $number;

		// Title is optional. If black, fill it if possible.
		if ( ! $widget_options[ $widget_id ]['title'] && isset( $_POST['widget-rss'][ $number ]['title'] ) ) {
			$rss = fetch_feed( $widget_options[ $widget_id ]['url'] );
			if ( is_gc_error( $rss ) ) {
				$widget_options[ $widget_id ]['title'] = htmlentities( __( '未知Feed' ) );
			} else {
				$widget_options[ $widget_id ]['title'] = htmlentities( strip_tags( $rss->get_title() ) );
				$rss->__destruct();
				unset( $rss );
			}
		}

		update_option( 'dashboard_widget_options', $widget_options );

		$locale    = get_user_locale();
		$cache_key = 'dash_v2_' . md5( $widget_id . '_' . $locale );
		delete_transient( $cache_key );
	}

	gc_widget_rss_form( $widget_options[ $widget_id ], $form_inputs );
}


/**
 * Renders the Events and News dashboard widget.
 *
 *
 */
function gc_dashboard_events_news() {
	?>

	<div class="gechiui-news hide-if-no-js">
		<?php gc_dashboard_primary(); ?>
	</div>

	<p class="community-events-footer">
		<?php
			printf(
				'<a href="%1$s" target="_blank">%2$s <span class="screen-reader-text">%3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>',
				/* translators: If a Rosetta site exists (e.g. https://es.gechiui.com/news/), then use that. Otherwise, leave untranslated. */
				esc_url( _x( 'https://www.gechiui.com/news/', 'Events and News dashboard widget' ) ),
				__( '新闻' ),
				/* translators: Accessibility text. */
				__( '（在新窗口中打开）' )
			);
		?>
	</p>

	<?php
}

/**
 * Renders the events templates for the Event and News widget.
 *
 *
 */
function gc_print_community_events_templates(){

}

/**
 * 'GeChiUI活动及新闻' dashboard widget.
 *
 *
 *
 */
function gc_dashboard_primary() {
	$feeds = array(
		'news'   => array(

			/**
			 * Filters the primary link URL for the 'GeChiUI活动及新闻' dashboard widget.
			 *
		
			 *
			 * @param string $link The widget's primary link URL.
			 */
			'link'         => apply_filters( 'dashboard_primary_link', __( 'https://www.gechiui.com/news/' ) ),

			/**
			 * Filters the primary feed URL for the 'GeChiUI活动及新闻' dashboard widget.
			 *
		
			 *
			 * @param string $url The widget's primary feed URL.
			 */
			'url'          => apply_filters( 'dashboard_primary_feed', __( 'https://www.gechiui.com/news/feed/' ) ),

			/**
			 * Filters the primary link title for the 'GeChiUI活动及新闻' dashboard widget.
			 *
		
			 *
			 * @param string $title Title attribute for the widget's primary link.
			 */
			'title'        => apply_filters( 'dashboard_primary_title', __( 'GeChiUI 博客' ) ),
			'items'        => 2,
			'show_summary' => 0,
			'show_author'  => 0,
			'show_date'    => 0,
		),
		//如果需要更过类型内容，可以在这里添加
	);

	gc_dashboard_cached_rss_widget( 'dashboard_primary', 'gc_dashboard_primary_output', $feeds );
}

/**
 * Displays the GeChiUI events and news feeds.
 *
 *
 *
 *
 * @param string $widget_id Widget ID.
 * @param array  $feeds     Array of RSS feeds.
 */
function gc_dashboard_primary_output( $widget_id, $feeds ) {
	foreach ( $feeds as $type => $args ) {
		$args['type'] = $type;
		echo '<div class="rss-widget">';
			gc_widget_rss_output( $args['url'], $args );
		echo '</div>';
	}
}

/**
 * Displays file upload quota on dashboard.
 *
 * Runs on the {@see 'activity_box_end'} hook in gc_dashboard_right_now().
 *
 *
 *
 * @return true|void True if not multisite, user can't upload files, or the space check option is disabled.
 */
function gc_dashboard_quota() {
	if ( ! is_multisite() || ! current_user_can( 'upload_files' )
		|| get_site_option( 'upload_space_check_disabled' )
	) {
		return true;
	}

	$quota = get_space_allowed();
	$used  = get_space_used();

	if ( $used > $quota ) {
		$percentused = '100';
	} else {
		$percentused = ( $used / $quota ) * 100;
	}

	$used_class  = ( $percentused >= 70 ) ? ' warning' : '';
	$used        = round( $used, 2 );
	$percentused = number_format( $percentused );

	?>
	<h3 class="mu-storage"><?php _e( '储存空间' ); ?></h3>
	<div class="mu-storage">
	<ul>
		<li class="storage-count">
			<?php
			$text = sprintf(
				/* translators: %s: Number of megabytes. */
				__( '允许使用%s MB存储空间' ),
				number_format_i18n( $quota )
			);
			printf(
				'<a href="%1$s">%2$s <span class="screen-reader-text">(%3$s)</span></a>',
				esc_url( admin_url( 'upload.php' ) ),
				$text,
				__( '管理上传' )
			);
			?>
		</li><li class="storage-count <?php echo $used_class; ?>">
			<?php
			$text = sprintf(
				/* translators: 1: Number of megabytes, 2: Percentage. */
				__( '已使用%1$s MB（%2$s%%）存储空间' ),
				number_format_i18n( $used, 2 ),
				$percentused
			);
			printf(
				'<a href="%1$s" class="musublink">%2$s <span class="screen-reader-text">(%3$s)</span></a>',
				esc_url( admin_url( 'upload.php' ) ),
				$text,
				__( '管理上传' )
			);
			?>
		</li>
	</ul>
	</div>
	<?php
}

/**
 * Displays the browser update nag.
 *
 *
 *
 *
 * @global bool $is_IE
 */
function gc_dashboard_browser_nag() {
	global $is_IE;

	$notice   = '';
	$response = gc_check_browser_version();

	if ( $response ) {
		if ( $is_IE ) {
			$msg = __( 'Internet Explorer 并不能为您带来最佳的 GeChiUI 体验。请换用 Microsoft Edge 或其他更新的浏览器以发挥您站点的最大效益。' );
		} elseif ( $response['insecure'] ) {
			$msg = sprintf(
				/* translators: %s: Browser name and link. */
				__( "看起来您正在使用不安全的%s。使用过时的浏览器会使您的计算机变得不安全。要获得最佳的GeChiUI体验，请升级您的浏览器。" ),
				sprintf( '<a href="%s">%s</a>', esc_url( $response['update_url'] ), esc_html( $response['name'] ) )
			);
		} else {
			$msg = sprintf(
				/* translators: %s: Browser name and link. */
				__( "看起来您正在使用旧版本的%s。要获得最佳的GeChiUI体验，请升级您的浏览器。" ),
				sprintf( '<a href="%s">%s</a>', esc_url( $response['update_url'] ), esc_html( $response['name'] ) )
			);
		}

		$browser_nag_class = '';
		if ( ! empty( $response['img_src'] ) ) {
			$img_src = ( is_ssl() && ! empty( $response['img_src_ssl'] ) ) ? $response['img_src_ssl'] : $response['img_src'];

			$notice           .= '<div class="alignright browser-icon"><img src="' . esc_attr( $img_src ) . '" alt="" /></div>';
			$browser_nag_class = ' has-browser-icon';
		}
		$notice .= "<p class='browser-update-nag{$browser_nag_class}'>{$msg}</p>";

		$browsehappy = 'https://browsehappy.com/';
		$locale      = get_user_locale();
		if ( 'zh_CN' !== $locale ) {
			$browsehappy = add_query_arg( 'locale', $locale, $browsehappy );
		}

		if ( $is_IE ) {
			$msg_browsehappy = sprintf(
				/* translators: %s: Browse Happy URL. */
				__( '了解如何进行<a href="%s" class="update-browser-link">快乐浏览</a>。' ),
				esc_url( $browsehappy )
			);
		} else {
			$msg_browsehappy = sprintf(
				/* translators: 1: Browser update URL, 2: Browser name, 3: Browse Happy URL. */
				__( '<a href="%1$s" class="update-browser-link">升级您的%2$s</a>，或了解一下<a href="%3$s" class="browse-happy-link">先进的浏览器</a>的有关信息' ),
				esc_attr( $response['update_url'] ),
				esc_html( $response['name'] ),
				esc_url( $browsehappy )
			);
		}

		$notice .= '<p>' . $msg_browsehappy . '</p>';
		$notice .= '<p class="hide-if-no-js"><a href="" class="dismiss" aria-label="' . esc_attr__( '关闭浏览器警告面板' ) . '">' . __( '不再显示' ) . '</a></p>';
		$notice .= '<div class="clear"></div>';
	}

	/**
	 * Filters the notice output for the 'Browse Happy' nag meta box.
	 *
	 *
	 * @param string      $notice   The notice content.
	 * @param array|false $response An array containing web browser information, or
	 *                              false on failure. See `gc_check_browser_version()`.
	 */
	echo apply_filters( 'browse-happy-notice', $notice, $response ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores
}

/**
 * Adds an additional class to the browser nag if the current version is insecure.
 *
 *
 *
 * @param string[] $classes Array of meta box classes.
 * @return string[] Modified array of meta box classes.
 */
function dashboard_browser_nag_class( $classes ) {
	$response = gc_check_browser_version();

	if ( $response && $response['insecure'] ) {
		$classes[] = 'browser-insecure';
	}

	return $classes;
}

/**
 * Checks if the user needs a browser update.
 *
 *
 *
 * @return array|false Array of browser data on success, false on failure.
 */
function gc_check_browser_version() {
	if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
		return false;
	}

	$key = md5( $_SERVER['HTTP_USER_AGENT'] );

	$response = get_site_transient( 'browser_' . $key );

	if ( false === $response ) {
		// Include an unmodified $gc_version.
		require ABSPATH . GCINC . '/version.php';

		$url     = 'http://api.gechiui.com/core/browse-happy/1.1/';
		$options = array(
			'body'       => array( 'useragent' => $_SERVER['HTTP_USER_AGENT'] ),
			'user-agent' => 'GeChiUI/' . $gc_version . '; ' . home_url( '/' ),
		);

		if ( gc_http_supports( array( 'ssl' ) ) ) {
			$url = set_url_scheme( $url, 'https' );
		}

		$response = gc_remote_post( $url, $options );

		if ( is_gc_error( $response ) || 200 !== gc_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		/**
		 * Response should be an array with:
		 *  'platform' - string - A user-friendly platform name, if it can be determined
		 *  'name' - string - A user-friendly browser name
		 *  'version' - string - The version of the browser the user is using
		 *  'current_version' - string - The most recent version of the browser
		 *  'upgrade' - boolean - Whether the browser needs an upgrade
		 *  'insecure' - boolean - Whether the browser is deemed insecure
		 *  'update_url' - string - The url to visit to upgrade
		 *  'img_src' - string - An image representing the browser
		 *  'img_src_ssl' - string - An image (over SSL) representing the browser
		 */
		$response = json_decode( gc_remote_retrieve_body( $response ), true );

		if ( ! is_array( $response ) ) {
			return false;
		}

		set_site_transient( 'browser_' . $key, $response, WEEK_IN_SECONDS );
	}

	return $response;
}

/**
 * Displays the PHP update nag.
 *
 *
 */
function gc_dashboard_php_nag() {
	$response = gc_check_php_version();

	if ( ! $response ) {
		return;
	}

	if ( isset( $response['is_secure'] ) && ! $response['is_secure'] ) {
		$msg = sprintf(
			/* translators: %s: The server PHP version. */
			__( '您的站点正在运行不安全的PHP版本（%s），应当进行更新。' ),
			PHP_VERSION
		);
	} else {
		$msg = sprintf(
			/* translators: %s: The server PHP version. */
			__( '您的站点正在运行过时的PHP版本（%s），应当进行更新。' ),
			PHP_VERSION
		);
	}
	?>
	<p><?php echo $msg; ?></p>

	<h3><?php _e( '什么是PHP？为什么它影响我的站点？' ); ?></h3>
	<p>
		<?php
		printf(
			/* translators: %s: The minimum recommended PHP version. */
			__( 'PHP是用以构建及维护GeChiUI的程序语言。较新版本的PHP在设计时以更好的性能表现为前提，所以使用最新的PHP版本会为您的站点带来更好的性能表现。推荐的最低PHP版本是%s。' ),
			$response ? $response['recommended_version'] : ''
		);
		?>
	</p>

	<p class="button-container">
		<?php
		printf(
			'<a class="button button-primary" href="%1$s" target="_blank" rel="noopener">%2$s <span class="screen-reader-text">%3$s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>',
			esc_url( gc_get_update_php_url() ),
			__( '查阅如何更新PHP' ),
			/* translators: Accessibility text. */
			__( '（在新窗口中打开）' )
		);
		?>
	</p>
	<?php

	gc_update_php_annotation();
	gc_direct_php_update_button();
}

/**
 * Adds an additional class to the PHP nag if the current version is insecure.
 *
 *
 *
 * @param string[] $classes Array of meta box classes.
 * @return string[] Modified array of meta box classes.
 */
function dashboard_php_nag_class( $classes ) {
	$response = gc_check_php_version();

	if ( $response && isset( $response['is_secure'] ) && ! $response['is_secure'] ) {
		$classes[] = 'php-insecure';
	}

	return $classes;
}

/**
 * Displays the Site Health Status widget.
 *
 *
 */
function gc_dashboard_site_health() {
	$get_issues = get_transient( 'health-check-site-status-result' );

	$issue_counts = array();

	if ( false !== $get_issues ) {
		$issue_counts = json_decode( $get_issues, true );
	}

	if ( ! is_array( $issue_counts ) || ! $issue_counts ) {
		$issue_counts = array(
			'good'        => 0,
			'recommended' => 0,
			'critical'    => 0,
		);
	}

	$issues_total = $issue_counts['recommended'] + $issue_counts['critical'];
	?>
	<div class="health-check-widget">
		<div class="health-check-widget-title-section site-health-progress-wrapper loading hide-if-no-js">
			<div class="site-health-progress">
				<svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
					<circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
					<circle id="bar" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
				</svg>
			</div>
			<div class="site-health-progress-label">
				<?php if ( false === $get_issues ) : ?>
					<?php _e( '尚无信息…' ); ?>
				<?php else : ?>
					<?php _e( '结果载入中…' ); ?>
				<?php endif; ?>
			</div>
		</div>

		<div class="site-health-details">
			<?php if ( false === $get_issues ) : ?>
				<p>
					<?php
					printf(
						/* translators: %s: URL to Site Health screen. */
						__( '站点健康检查会自动定期运行来取得有关您站点的信息。您也可以<a href="%s">访问站点健康页面</a>来取得有关您站点的信息。' ),
						esc_url( admin_url( 'site-health.php' ) )
					);
					?>
				</p>
			<?php else : ?>
				<p>
					<?php if ( $issues_total <= 0 ) : ?>
						<?php _e( '好样的！您的站点通过了所有健康检查。' ); ?>
					<?php elseif ( 1 === (int) $issue_counts['critical'] ) : ?>
						<?php _e( '您的站点存在关键问题，您应立即解决以提高其性能和安全性。' ); ?>
					<?php elseif ( $issue_counts['critical'] > 1 ) : ?>
						<?php _e( '您的站点有应当立即处理关键问题，这些问题影响您的站点的性能与安全性。' ); ?>
					<?php elseif ( 1 === (int) $issue_counts['recommended'] ) : ?>
						<?php _e( '您的站点健康状况看起来不错，但您仍可以做一些事情来提高其性能和安全性。' ); ?>
					<?php else : ?>
						<?php _e( '您的站点健康没有什么问题，但您仍可以做一些事情来改进您的站点的性能与安全性。' ); ?>
					<?php endif; ?>
				</p>
			<?php endif; ?>

			<?php if ( $issues_total > 0 && false !== $get_issues ) : ?>
				<p>
					<?php
					printf(
						/* translators: 1: Number of issues. 2: URL to Site Health screen. */
						_n(
							'看看<a href="%2$s">站点健康界面</a>上的<strong>%1$d个项目</strong>。',
							'看看<a href="%2$s">站点健康界面</a>上的<strong>%1$d个项目</strong>。',
							$issues_total
						),
						$issues_total,
						esc_url( admin_url( 'site-health.php' ) )
					);
					?>
				</p>
			<?php endif; ?>
		</div>
	</div>

	<?php
}

/**
 * Empty function usable by plugins to output empty dashboard widget (to be populated later by JS).
 *
 *
 */
function gc_dashboard_empty() {}

/**
 * Displays a welcome panel to introduce users to GeChiUI.
 *
 *
 *
 */
function gc_welcome_panel() {
	list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );
	$can_customize           = current_user_can( 'customize' );
	$is_block_theme          = gc_is_block_theme();
	?>
	<div class="welcome-panel-content">
	<div class="welcome-panel-header">
		<h2><?php _e( '欢迎使用GeChiUI！' ); ?></h2>
		<p>
			<a href="<?php echo esc_url( admin_url( 'about.php' ) ); ?>">
			<?php
				/* translators: %s: Current GeChiUI version. */
				printf( __( '详细了解 %s 版本。' ), $display_version );
			?>
			</a>
		</p>
	</div>
	<div class="welcome-panel-column-container">
		<div class="welcome-panel-column">
			<div class="welcome-panel-icon-pages"></div>
			<div class="welcome-panel-column-content">
				<h3><?php _e( '使用区块和区块样板创作丰富的内容' ); ?></h3>
				<p><?php _e( '区块样板是预先配置好的区块布局。通过区块样板获得灵感或在极短时间内创建新页面。' ); ?></p>
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=page' ) ); ?>"><?php _e( '添加新页面' ); ?></a>
			</div>
		</div>
		<div class="welcome-panel-column">
			<div class="welcome-panel-icon-layout"></div>
			<div class="welcome-panel-column-content">
			<?php if ( $is_block_theme ) : ?>
				<h3><?php _e( '使用区块主题定制整个站点' ); ?></h3>
				<p><?php _e( '上至页眉下至页脚，整个站点都可通过区块获区块样板进行设计。' ); ?></p>
				<a href="<?php echo esc_url( admin_url( 'site-editor.php' ) ); ?>"><?php _e( '打开站点编辑器' ); ?></a>
			<?php else : ?>
				<h3><?php _e( '开始定制' ); ?></h3>
				<p><?php _e( '在定制器中配置您站点的 logo、标题、菜单等。' ); ?></p>
				<?php if ( $can_customize ) : ?>
					<a class="load-customize hide-if-no-customize" href="<?php echo gc_customize_url(); ?>"><?php _e( '打开外观定制器' ); ?></a>
				<?php endif; ?>
			<?php endif; ?>
			</div>
		</div>
		<div class="welcome-panel-column">
			<div class="welcome-panel-icon-styles"></div>
			<div class="welcome-panel-column-content">
			<?php if ( $is_block_theme ) : ?>
				<h3><?php _e( '使用样式变更站点的外观和风格' ); ?></h3>
				<p><?php _e( '调整站点或赋予全新外观！ -使用新的调色盘或字体发挥创意！' ); ?></p>
				<a href="<?php echo esc_url( admin_url( 'site-editor.php?styles=open' ) ); ?>"><?php _e( '编辑样式' ); ?></a>
			<?php else : ?>
				<h3><?php _e( '探索构建站点的新方式' ); ?></h3>
				<p><?php _e( '全新的 GeChiUI 主题，可让您按需使用区块和区块样板来构建站点。' ); ?></p>
				<a href="<?php echo esc_url( __( 'https://www.gechiui.com/support/block-themes/' ) ); ?>"><?php _e( '了解区块主题' ); ?></a>
			<?php endif; ?>
			</div>
		</div>
	</div>
	</div>
	<?php
}
