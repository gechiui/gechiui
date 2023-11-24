<?php
/**
 * List Table API: GC_Comments_List_Table class
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Core class used to implement displaying comments in a list table.
 *
 * @see GC_List_Table
 */
class GC_Comments_List_Table extends GC_List_Table {

	public $checkbox = true;

	public $pending_count = array();

	public $extra_items;

	private $user_can;

	/**
	 * Constructor.
	 *
	 *
	 * @see GC_List_Table::__construct() for more information on default arguments.
	 *
	 * @global int $post_id
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		global $post_id;

		$post_id = isset( $_REQUEST['p'] ) ? absint( $_REQUEST['p'] ) : 0;

		if ( get_option( 'show_avatars' ) ) {
			add_filter( 'comment_author', array( $this, 'floated_admin_avatar' ), 10, 2 );
		}

		parent::__construct(
			array(
				'plural'   => 'comments',
				'singular' => 'comment',
				'ajax'     => true,
				'screen'   => isset( $args['screen'] ) ? $args['screen'] : null,
			)
		);
	}

	/**
	 * Adds avatars to comment author names.
	 *
	 *
	 * @param string $name       Comment author name.
	 * @param int    $comment_id Comment ID.
	 * @return string Avatar with the user name.
	 */
	public function floated_admin_avatar( $name, $comment_id ) {
		$comment = get_comment( $comment_id );
		$avatar  = get_avatar( $comment, 32, 'mystery' );
		return "$avatar $name";
	}

	/**
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * @global string $mode           List table view mode.
	 * @global int    $post_id
	 * @global string $comment_status
	 * @global string $comment_type
	 * @global string $search
	 */
	public function prepare_items() {
		global $mode, $post_id, $comment_status, $comment_type, $search;

		if ( ! empty( $_REQUEST['mode'] ) ) {
			$mode = 'excerpt' === $_REQUEST['mode'] ? 'excerpt' : 'list';
			set_user_setting( 'posts_list_mode', $mode );
		} else {
			$mode = get_user_setting( 'posts_list_mode', 'list' );
		}

		$comment_status = isset( $_REQUEST['comment_status'] ) ? $_REQUEST['comment_status'] : 'all';

		if ( ! in_array( $comment_status, array( 'all', 'mine', 'moderated', 'approved', 'spam', 'trash' ), true ) ) {
			$comment_status = 'all';
		}

		$comment_type = ! empty( $_REQUEST['comment_type'] ) ? $_REQUEST['comment_type'] : '';

		$search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : '';

		$post_type = ( isset( $_REQUEST['post_type'] ) ) ? sanitize_key( $_REQUEST['post_type'] ) : '';

		$user_id = ( isset( $_REQUEST['user_id'] ) ) ? $_REQUEST['user_id'] : '';

		$orderby = ( isset( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : '';
		$order   = ( isset( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : '';

		$comments_per_page = $this->get_per_page( $comment_status );

		$doing_ajax = gc_doing_ajax();

		if ( isset( $_REQUEST['number'] ) ) {
			$number = (int) $_REQUEST['number'];
		} else {
			$number = $comments_per_page + min( 8, $comments_per_page ); // Grab a few extra.
		}

		$page = $this->get_pagenum();

		if ( isset( $_REQUEST['start'] ) ) {
			$start = $_REQUEST['start'];
		} else {
			$start = ( $page - 1 ) * $comments_per_page;
		}

		if ( $doing_ajax && isset( $_REQUEST['offset'] ) ) {
			$start += $_REQUEST['offset'];
		}

		$status_map = array(
			'mine'      => '',
			'moderated' => 'hold',
			'approved'  => 'approve',
			'all'       => '',
		);

		$args = array(
			'status'                    => isset( $status_map[ $comment_status ] ) ? $status_map[ $comment_status ] : $comment_status,
			'search'                    => $search,
			'user_id'                   => $user_id,
			'offset'                    => $start,
			'number'                    => $number,
			'post_id'                   => $post_id,
			'type'                      => $comment_type,
			'orderby'                   => $orderby,
			'order'                     => $order,
			'post_type'                 => $post_type,
			'update_comment_post_cache' => true,
		);

		/**
		 * Filters the arguments for the comment query in the comments list table.
		 *
		 * @since 5.1.0
		 *
		 * @param array $args An array of get_comments() arguments.
		 */
		$args = apply_filters( 'comments_list_table_query_args', $args );

		$_comments = get_comments( $args );

		if ( is_array( $_comments ) ) {
			$this->items       = array_slice( $_comments, 0, $comments_per_page );
			$this->extra_items = array_slice( $_comments, $comments_per_page );

			$_comment_post_ids = array_unique( gc_list_pluck( $_comments, 'comment_post_ID' ) );

			$this->pending_count = get_pending_comments_num( $_comment_post_ids );
		}

		$total_comments = get_comments(
			array_merge(
				$args,
				array(
					'count'  => true,
					'offset' => 0,
					'number' => 0,
				)
			)
		);

		$this->set_pagination_args(
			array(
				'total_items' => $total_comments,
				'per_page'    => $comments_per_page,
			)
		);
	}

	/**
	 * @param string $comment_status
	 * @return int
	 */
	public function get_per_page( $comment_status = 'all' ) {
		$comments_per_page = $this->get_items_per_page( 'edit_comments_per_page' );

		/**
		 * Filters the number of comments listed per page in the comments list table.
		 *
		 * @since 2.6.0
		 *
		 * @param int    $comments_per_page The number of comments to list per page.
		 * @param string $comment_status    The comment status name. Default 'All'.
		 */
		return apply_filters( 'comments_per_page', $comments_per_page, $comment_status );
	}

	/**
	 * @global string $comment_status
	 */
	public function no_items() {
		global $comment_status;

		if ( 'moderated' === $comment_status ) {
			_e( '尚无评论待审。' );
		} elseif ( 'trash' === $comment_status ) {
			_e( '回收站中没有评论。' );
		} else {
			_e( '无评论。' );
		}
	}

	/**
	 * @global int $post_id
	 * @global string $comment_status
	 * @global string $comment_type
	 */
	protected function get_views() {
		global $post_id, $comment_status, $comment_type;

		$status_links = array();
		$num_comments = ( $post_id ) ? gc_count_comments( $post_id ) : gc_count_comments();

		$stati = array(
			/* translators: %s: Number of comments. */
			'all'       => _nx_noop(
				'全部<span class="count">（%s）</span>',
				'全部<span class="count">（%s）</span>',
				'comments'
			), // Singular not used.

			/* translators: %s: Number of comments. */
			'mine'      => _nx_noop(
				'我的<span class="count">（%s）</span>',
				'我的<span class="count">（%s）</span>',
				'comments'
			),

			/* translators: %s: Number of comments. */
			'moderated' => _nx_noop(
				'待审<span class="count">（%s）</span>',
				'待审<span class="count">（%s）</span>',
				'comments'
			),

			/* translators: %s: Number of comments. */
			'approved'  => _nx_noop(
				'已批准<span class="count">（%s）</span>',
				'已批准<span class="count">（%s）</span>',
				'comments'
			),

			/* translators: %s: Number of comments. */
			'spam'      => _nx_noop(
				'垃圾<span class="count">（%s）</span>',
				'垃圾<span class="count">（%s）</span>',
				'comments'
			),

			/* translators: %s: Number of comments. */
			'trash'     => _nx_noop(
				'回收站<span class="count">（%s）</span>',
				'回收站<span class="count">（%s）</span>',
				'comments'
			),
		);

		if ( ! EMPTY_TRASH_DAYS ) {
			unset( $stati['trash'] );
		}

		$link = admin_url( 'edit-comments.php' );

		if ( ! empty( $comment_type ) && 'all' !== $comment_type ) {
			$link = add_query_arg( 'comment_type', $comment_type, $link );
		}

		foreach ( $stati as $status => $label ) {
			if ( 'mine' === $status ) {
				$current_user_id    = get_current_user_id();
				$num_comments->mine = get_comments(
					array(
						'post_id' => $post_id ? $post_id : 0,
						'user_id' => $current_user_id,
						'count'   => true,
					)
				);
				$link               = add_query_arg( 'user_id', $current_user_id, $link );
			} else {
				$link = remove_query_arg( 'user_id', $link );
			}

			if ( ! isset( $num_comments->$status ) ) {
				$num_comments->$status = 10;
			}

			$link = add_query_arg( 'comment_status', $status, $link );

			if ( $post_id ) {
				$link = add_query_arg( 'p', absint( $post_id ), $link );
			}

			/*
			// I toyed with this, but decided against it. Leaving it in here in case anyone thinks it is a good idea. ~ Mark
			if ( !empty( $_REQUEST['s'] ) )
				$link = add_query_arg( 's', esc_attr( gc_unslash( $_REQUEST['s'] ) ), $link );
			*/

			$status_links[ $status ] = array(
				'url'     => esc_url( $link ),
				'label'   => sprintf(
					translate_nooped_plural( $label, $num_comments->$status ),
					sprintf(
						'<span class="%s-count">%s</span>',
						( 'moderated' === $status ) ? 'pending' : $status,
						number_format_i18n( $num_comments->$status )
					)
				),
				'current' => $status === $comment_status,
			);
		}

		/**
		 * Filters the comment status links.
		 *
		 * @since 2.5.0
		 * @since 5.1.0 The 'Mine' link was added.
		 *
		 * @param string[] $status_links An associative array of fully-formed comment status links. Includes 'All', 'Mine',
		 *                              'Pending', 'Approved', 'Spam', and 'Trash'.
		 */
		return apply_filters( 'comment_status_links', $this->get_views_links( $status_links ) );
	}

	/**
	 * @global string $comment_status
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		global $comment_status;

		$actions = array();

		if ( in_array( $comment_status, array( 'all', 'approved' ), true ) ) {
			$actions['unapprove'] = __( '驳回' );
		}

		if ( in_array( $comment_status, array( 'all', 'moderated' ), true ) ) {
			$actions['approve'] = __( '批准' );
		}

		if ( in_array( $comment_status, array( 'all', 'moderated', 'approved', 'trash' ), true ) ) {
			$actions['spam'] = _x( '标记为垃圾评论', 'comment' );
		}

		if ( 'trash' === $comment_status ) {
			$actions['untrash'] = __( '还原' );
		} elseif ( 'spam' === $comment_status ) {
			$actions['unspam'] = _x( '不是垃圾评论', 'comment' );
		}

		if ( in_array( $comment_status, array( 'trash', 'spam' ), true ) || ! EMPTY_TRASH_DAYS ) {
			$actions['delete'] = __( '永久删除' );
		} else {
			$actions['trash'] = __( '移动至回收站' );
		}

		return $actions;
	}

	/**
	 * @global string $comment_status
	 * @global string $comment_type
	 *
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		global $comment_status, $comment_type;
		static $has_items;

		if ( ! isset( $has_items ) ) {
			$has_items = $this->has_items();
		}

		echo '<div class="alignleft actions">';

		if ( 'top' === $which ) {
			ob_start();

			$this->comment_type_dropdown( $comment_type );

			/**
			 * Fires just before the Filter submit button for comment types.
			 *
			 * @since 3.5.0
			 */
			do_action( 'restrict_manage_comments' );

			$output = ob_get_clean();

			if ( ! empty( $output ) && $this->has_items() ) {
				echo $output;
				submit_button( __( '筛选' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
			}
		}

		if ( ( 'spam' === $comment_status || 'trash' === $comment_status ) && $has_items
			&& current_user_can( 'moderate_comments' )
		) {
			gc_nonce_field( 'bulk-destroy', '_destroy_nonce' );
			$title = ( 'spam' === $comment_status ) ? esc_attr__( '清空垃圾' ) : esc_attr__( '清空回收站' );
			submit_button( $title, '', 'delete_all', false );
		}

		/**
		 * Fires after the Filter submit button for comment types.
		 *
		 * @since 2.5.0
		 * @since 5.6.0 The `$which` parameter was added.
		 *
		 * @param string $comment_status The comment status name. Default 'All'.
		 * @param string $which          The location of the extra table nav markup: 'top' or 'bottom'.
		 */
		do_action( 'manage_comments_nav', $comment_status, $which );

		echo '</div>';
	}

	/**
	 * @return string|false
	 */
	public function current_action() {
		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) {
			return 'delete_all';
		}

		return parent::current_action();
	}

	/**
	 * @global int $post_id
	 *
	 * @return string[] Array of column titles keyed by their column name.
	 */
	public function get_columns() {
		global $post_id;

		$columns = array();

		if ( $this->checkbox ) {
			$columns['cb'] = '<input type="checkbox" />';
		}

		$columns['author']  = __( '作者' );
		$columns['comment'] = _x( '评论', 'column name' );

		if ( ! $post_id ) {
			/* translators: Column name or table row header. */
			$columns['response'] = __( '回复至' );
		}

		$columns['date'] = _x( '提交于', 'column name' );

		return $columns;
	}

	/**
	 * Displays a comment type drop-down for filtering on the Comments list table.
	 *
	 * @since 5.5.0
	 * @since 5.6.0 Renamed from `comment_status_dropdown()` to `comment_type_dropdown()`.
	 *
	 * @param string $comment_type The current comment type slug.
	 */
	protected function comment_type_dropdown( $comment_type ) {
		/**
		 * Filters the comment types shown in the drop-down menu on the Comments list table.
		 *
		 * @param string[] $comment_types Array of comment type labels keyed by their name.
		 */
		$comment_types = apply_filters(
			'admin_comment_types_dropdown',
			array(
				'comment' => __( '评论' ),
				'pings'   => __( 'Ping 通告' ),
			)
		);

		if ( $comment_types && is_array( $comment_types ) ) {
			printf(
				'<label class="screen-reader-text" for="filter-by-comment-type">%s</label>',
				/* translators: Hidden accessibility text. */
				__( '按评论类型筛选' )
			);

			echo '<select id="filter-by-comment-type" name="comment_type">';

			printf( "\t<option value=''>%s</option>", __( '全部评论类型' ) );

			foreach ( $comment_types as $type => $label ) {
				if ( get_comments(
					array(
						'number' => 1,
						'type'   => $type,
					)
				) ) {
					printf(
						"\t<option value='%s'%s>%s</option>\n",
						esc_attr( $type ),
						selected( $comment_type, $type, false ),
						esc_html( $label )
					);
				}
			}

			echo '</select>';
		}
	}

	/**
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'author'   => array( 'comment_author', false, __( '作者' ), __( '表格按评论作者排序。' ) ),
			'response' => array( 'comment_post_ID', false, _x( '回应给', 'column name' ), __( '表格按回复文章排序。' ) ),
			'date'     => 'comment_date',
		);
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @since 4.3.0
	 *
	 * @return string Name of the default primary column, in this case, 'comment'.
	 */
	protected function get_default_primary_column_name() {
		return 'comment';
	}

	/**
	 * Displays the comments table.
	 *
	 * Overrides the parent display() method to render extra comments.
	 *
	 */
	public function display() {
		gc_nonce_field( 'fetch-list-' . get_class( $this ), '_ajax_fetch_list_nonce' );
		static $has_items;

		if ( ! isset( $has_items ) ) {
			$has_items = $this->has_items();

			if ( $has_items ) {
				$this->display_tablenav( 'top' );
			}
		}

		$this->screen->render_screen_reader_content( 'heading_list' );

		?>
<table class="gc-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
		<?php
		if ( ! isset( $_GET['orderby'] ) ) {
			// In the initial view, Comments are ordered by comment's date but there's no column for that.
			echo '<caption class="screen-reader-text">' .
			/* translators: Hidden accessibility text. */
			__( '按评论日期降序排列。' ) .
			'</caption>';
		} else {
			$this->print_table_description();
		}
		?>
	<thead>
	<tr>
		<?php $this->print_column_headers(); ?>
	</tr>
	</thead>

	<tbody id="the-comment-list" data-gc-lists="list:comment">
		<?php $this->display_rows_or_placeholder(); ?>
	</tbody>

	<tbody id="the-extra-comment-list" data-gc-lists="list:comment" style="display: none;">
		<?php
			/*
			 * Back up the items to restore after printing the extra items markup.
			 * The extra items may be empty, which will prevent the table nav from displaying later.
			 */
			$items       = $this->items;
			$this->items = $this->extra_items;
			$this->display_rows_or_placeholder();
			$this->items = $items;
		?>
	</tbody>

	<tfoot>
	<tr>
		<?php $this->print_column_headers( false ); ?>
	</tr>
	</tfoot>

</table>
		<?php

		$this->display_tablenav( 'bottom' );
	}

	/**
	 * @global GC_Post    $post    Global post object.
	 * @global GC_Comment $comment Global comment object.
	 *
	 * @param GC_Comment $item
	 */
	public function single_row( $item ) {
		global $post, $comment;

		$comment = $item;

		$the_comment_class = gc_get_comment_status( $comment );

		if ( ! $the_comment_class ) {
			$the_comment_class = '';
		}

		$the_comment_class = implode( ' ', get_comment_class( $the_comment_class, $comment, $comment->comment_post_ID ) );

		if ( $comment->comment_post_ID > 0 ) {
			$post = get_post( $comment->comment_post_ID );
		}

		$this->user_can = current_user_can( 'edit_comment', $comment->comment_ID );

		echo "<tr id='comment-$comment->comment_ID' class='$the_comment_class'>";
		$this->single_row_columns( $comment );
		echo "</tr>\n";

		unset( $GLOBALS['post'], $GLOBALS['comment'] );
	}

	/**
	 * Generates and displays row actions links.
	 *
	 * @since 4.3.0
	 * @since 5.9.0 Renamed `$comment` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @global string $comment_status Status for the current listed comments.
	 *
	 * @param GC_Comment $item        The comment object.
	 * @param string     $column_name Current column name.
	 * @param string     $primary     Primary column name.
	 * @return string Row actions output for comments. An empty string
	 *                if the current column is not the primary column,
	 *                or if the current user cannot edit the comment.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		global $comment_status;

		if ( $primary !== $column_name ) {
			return '';
		}

		if ( ! $this->user_can ) {
			return '';
		}

		// Restores the more descriptive, specific name for use within this method.
		$comment            = $item;
		$the_comment_status = gc_get_comment_status( $comment );

		$output = '';

		$del_nonce     = esc_html( '_gcnonce=' . gc_create_nonce( "delete-comment_$comment->comment_ID" ) );
		$approve_nonce = esc_html( '_gcnonce=' . gc_create_nonce( "approve-comment_$comment->comment_ID" ) );

		$url = "comment.php?c=$comment->comment_ID";

		$approve_url   = esc_url( $url . "&action=approvecomment&$approve_nonce" );
		$unapprove_url = esc_url( $url . "&action=unapprovecomment&$approve_nonce" );
		$spam_url      = esc_url( $url . "&action=spamcomment&$del_nonce" );
		$unspam_url    = esc_url( $url . "&action=unspamcomment&$del_nonce" );
		$trash_url     = esc_url( $url . "&action=trashcomment&$del_nonce" );
		$untrash_url   = esc_url( $url . "&action=untrashcomment&$del_nonce" );
		$delete_url    = esc_url( $url . "&action=deletecomment&$del_nonce" );

		// Preorder it: Approve | Reply | Quick Edit | Edit | Spam | Trash.
		$actions = array(
			'approve'   => '',
			'unapprove' => '',
			'reply'     => '',
			'quickedit' => '',
			'edit'      => '',
			'spam'      => '',
			'unspam'    => '',
			'trash'     => '',
			'untrash'   => '',
			'delete'    => '',
		);

		// Not looking at all comments.
		if ( $comment_status && 'all' !== $comment_status ) {
			if ( 'approved' === $the_comment_status ) {
				$actions['unapprove'] = sprintf(
					'<a href="%s" data-gc-lists="%s" class="vim-u vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
					$unapprove_url,
					"delete:the-comment-list:comment-{$comment->comment_ID}:e7e7d3:action=dim-comment&amp;new=unapproved",
					esc_attr__( '驳回此评论' ),
					__( '驳回' )
				);
			} elseif ( 'unapproved' === $the_comment_status ) {
				$actions['approve'] = sprintf(
					'<a href="%s" data-gc-lists="%s" class="vim-a vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
					$approve_url,
					"delete:the-comment-list:comment-{$comment->comment_ID}:e7e7d3:action=dim-comment&amp;new=approved",
					esc_attr__( '批准此评论' ),
					__( '批准' )
				);
			}
		} else {
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
		}

		if ( 'spam' !== $the_comment_status ) {
			$actions['spam'] = sprintf(
				'<a href="%s" data-gc-lists="%s" class="vim-s vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				$spam_url,
				"delete:the-comment-list:comment-{$comment->comment_ID}::spam=1",
				esc_attr__( '将此评论标记为垃圾' ),
				/* translators: "标记为垃圾评论" link. */
				_x( '垃圾', 'verb' )
			);
		} elseif ( 'spam' === $the_comment_status ) {
			$actions['unspam'] = sprintf(
				'<a href="%s" data-gc-lists="%s" class="vim-z vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				$unspam_url,
				"delete:the-comment-list:comment-{$comment->comment_ID}:66cc66:unspam=1",
				esc_attr__( '从垃圾评论中恢复此评论' ),
				_x( '不是垃圾评论', 'comment' )
			);
		}

		if ( 'trash' === $the_comment_status ) {
			$actions['untrash'] = sprintf(
				'<a href="%s" data-gc-lists="%s" class="vim-z vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				$untrash_url,
				"delete:the-comment-list:comment-{$comment->comment_ID}:66cc66:untrash=1",
				esc_attr__( '从回收站中恢复此评论' ),
				__( '还原' )
			);
		}

		if ( 'spam' === $the_comment_status || 'trash' === $the_comment_status || ! EMPTY_TRASH_DAYS ) {
			$actions['delete'] = sprintf(
				'<a href="%s" data-gc-lists="%s" class="delete vim-d vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				$delete_url,
				"delete:the-comment-list:comment-{$comment->comment_ID}::delete=1",
				esc_attr__( '永久删除此评论' ),
				__( '永久删除' )
			);
		} else {
			$actions['trash'] = sprintf(
				'<a href="%s" data-gc-lists="%s" class="delete vim-d vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				$trash_url,
				"delete:the-comment-list:comment-{$comment->comment_ID}::trash=1",
				esc_attr__( '将此评论移至回收站' ),
				_x( '回收站', 'verb' )
			);
		}

		if ( 'spam' !== $the_comment_status && 'trash' !== $the_comment_status ) {
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				"comment.php?action=editcomment&amp;c={$comment->comment_ID}",
				esc_attr__( '编辑此评论' ),
				__( '编辑' )
			);

			$format = '<button type="button" data-comment-id="%d" data-post-id="%d" data-action="%s" class="%s button-link" aria-expanded="false" aria-label="%s">%s</button>';

			$actions['quickedit'] = sprintf(
				$format,
				$comment->comment_ID,
				$comment->comment_post_ID,
				'edit',
				'vim-q comment-inline',
				esc_attr__( '快速编辑此评论' ),
				__( '快速编辑' )
			);

			$actions['reply'] = sprintf(
				$format,
				$comment->comment_ID,
				$comment->comment_post_ID,
				'replyto',
				'vim-r comment-inline',
				esc_attr__( '回复此评论' ),
				__( '回复' )
			);
		}

		/** This filter is documented in gc-admin/includes/dashboard.php */
		$actions = apply_filters( 'comment_row_actions', array_filter( $actions ), $comment );

		$always_visible = false;

		$mode = get_user_setting( 'posts_list_mode', 'list' );

		if ( 'excerpt' === $mode ) {
			$always_visible = true;
		}

		$output .= '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';

		$i = 0;

		foreach ( $actions as $action => $link ) {
			++$i;

			if ( ( ( 'approve' === $action || 'unapprove' === $action ) && 2 === $i )
				|| 1 === $i
			) {
				$separator = '';
			} else {
				$separator = ' | ';
			}

			// Reply and quickedit need a hide-if-no-js span when not added with Ajax.
			if ( ( 'reply' === $action || 'quickedit' === $action ) && ! gc_doing_ajax() ) {
				$action .= ' hide-if-no-js';
			} elseif ( ( 'untrash' === $action && 'trash' === $the_comment_status )
				|| ( 'unspam' === $action && 'spam' === $the_comment_status )
			) {
				if ( '1' === get_comment_meta( $comment->comment_ID, '_gc_trash_meta_status', true ) ) {
					$action .= ' approve';
				} else {
					$action .= ' unapprove';
				}
			}

			$output .= "<span class='$action'>{$separator}{$link}</span>";
		}

		$output .= '</div>';

		$output .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' .
			/* translators: Hidden accessibility text. */
			__( '显示详情' ) .
		'</span></button>';

		return $output;
	}

	/**
	 * @since 5.9.0 Renamed `$comment` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param GC_Comment $item The comment object.
	 */
	public function column_cb( $item ) {
		// Restores the more descriptive, specific name for use within this method.
		$comment = $item;

		if ( $this->user_can ) {
			?>
		<label class="label-covers-full-cell" for="cb-select-<?php echo $comment->comment_ID; ?>">
			<span class="screen-reader-text">
			<?php
			/* translators: Hidden accessibility text. */
			_e( '选择评论' );
			?>
			</span>
		</label>
		<input id="cb-select-<?php echo $comment->comment_ID; ?>" type="checkbox" name="delete_comments[]" value="<?php echo $comment->comment_ID; ?>" />
			<?php
		}
	}

	/**
	 * @param GC_Comment $comment The comment object.
	 */
	public function column_comment( $comment ) {
		echo '<div class="comment-author">';
			$this->column_author( $comment );
		echo '</div>';

		if ( $comment->comment_parent ) {
			$parent = get_comment( $comment->comment_parent );

			if ( $parent ) {
				$parent_link = esc_url( get_comment_link( $parent ) );
				$name        = get_comment_author( $parent );
				printf(
					/* translators: %s: Comment link. */
					__( '回复给%s。' ),
					'<a href="' . $parent_link . '">' . $name . '</a>'
				);
			}
		}

		comment_text( $comment );

		if ( $this->user_can ) {
			/** This filter is documented in gc-admin/includes/comment.php */
			$comment_content = apply_filters( 'comment_edit_pre', $comment->comment_content );
			?>
		<div id="inline-<?php echo $comment->comment_ID; ?>" class="hidden">
			<textarea class="comment" rows="1" cols="1"><?php echo esc_textarea( $comment_content ); ?></textarea>
			<div class="author-email"><?php echo esc_html( $comment->comment_author_email ); ?></div>
			<div class="author"><?php echo esc_html( $comment->comment_author ); ?></div>
			<div class="author-url"><?php echo esc_url( $comment->comment_author_url ); ?></div>
			<div class="comment_status"><?php echo $comment->comment_approved; ?></div>
		</div>
			<?php
		}
	}

	/**
	 * @global string $comment_status
	 *
	 * @param GC_Comment $comment The comment object.
	 */
	public function column_author( $comment ) {
		global $comment_status;

		$author_url = get_comment_author_url( $comment );

		$author_url_display = untrailingslashit( preg_replace( '|^http(s)?://(www\.)?|i', '', $author_url ) );

		if ( strlen( $author_url_display ) > 50 ) {
			$author_url_display = gc_html_excerpt( $author_url_display, 49, '&hellip;' );
		}

		echo '<strong>';
		comment_author( $comment );
		echo '</strong><br />';

		if ( ! empty( $author_url_display ) ) {
			// Print link to author URL, and disallow referrer information (without using target="_blank").
			printf(
				'<a href="%s" rel="noopener noreferrer">%s</a><br />',
				esc_url( $author_url ),
				esc_html( $author_url_display )
			);
		}

		if ( $this->user_can ) {
			if ( ! empty( $comment->comment_author_email ) ) {
				/** This filter is documented in gc-includes/comment-template.php */
				$email = apply_filters( 'comment_email', $comment->comment_author_email, $comment );

				if ( ! empty( $email ) && '@' !== $email ) {
					printf( '<a href="%1$s">%2$s</a><br />', esc_url( 'mailto:' . $email ), esc_html( $email ) );
				}
			}

			$author_ip = get_comment_author_IP( $comment );

			if ( $author_ip ) {
				$author_ip_url = add_query_arg(
					array(
						's'    => $author_ip,
						'mode' => 'detail',
					),
					admin_url( 'edit-comments.php' )
				);

				if ( 'spam' === $comment_status ) {
					$author_ip_url = add_query_arg( 'comment_status', 'spam', $author_ip_url );
				}

				printf( '<a href="%1$s">%2$s</a>', esc_url( $author_ip_url ), esc_html( $author_ip ) );
			}
		}
	}

	/**
	 * @param GC_Comment $comment The comment object.
	 */
	public function column_date( $comment ) {
		$submitted = sprintf(
			/* translators: 1: Comment date, 2: Comment time. */
			__( '%1$s %2$s' ),
			/* translators: Comment date format. See https://www.php.net/manual/datetime.format.php */
			get_comment_date( __( 'Y-m-d' ), $comment ),
			/* translators: Comment time format. See https://www.php.net/manual/datetime.format.php */
			get_comment_date( __( 'H:i' ), $comment )
		);

		echo '<div class="submitted-on">';

		if ( 'approved' === gc_get_comment_status( $comment ) && ! empty( $comment->comment_post_ID ) ) {
			printf(
				'<a href="%s">%s</a>',
				esc_url( get_comment_link( $comment ) ),
				$submitted
			);
		} else {
			echo $submitted;
		}

		echo '</div>';
	}

	/**
	 * @param GC_Comment $comment The comment object.
	 */
	public function column_response( $comment ) {
		$post = get_post();

		if ( ! $post ) {
			return;
		}

		if ( isset( $this->pending_count[ $post->ID ] ) ) {
			$pending_comments = $this->pending_count[ $post->ID ];
		} else {
			$_pending_count_temp              = get_pending_comments_num( array( $post->ID ) );
			$pending_comments                 = $_pending_count_temp[ $post->ID ];
			$this->pending_count[ $post->ID ] = $pending_comments;
		}

		if ( current_user_can( 'edit_post', $post->ID ) ) {
			$post_link  = "<a href='" . get_edit_post_link( $post->ID ) . "' class='comments-edit-item-link'>";
			$post_link .= esc_html( get_the_title( $post->ID ) ) . '</a>';
		} else {
			$post_link = esc_html( get_the_title( $post->ID ) );
		}

		echo '<div class="response-links">';

		if ( 'attachment' === $post->post_type ) {
			$thumb = gc_get_attachment_image( $post->ID, array( 80, 60 ), true );
			if ( $thumb ) {
				echo $thumb;
			}
		}

		echo $post_link;

		$post_type_object = get_post_type_object( $post->post_type );
		echo "<a href='" . get_permalink( $post->ID ) . "' class='comments-view-item-link'>" . $post_type_object->labels->view_item . '</a>';

		echo '<span class="post-com-count-wrapper post-com-count-', $post->ID, '">';
		$this->comments_bubble( $post->ID, $pending_comments );
		echo '</span> ';

		echo '</div>';
	}

	/**
	 * @since 5.9.0 Renamed `$comment` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param GC_Comment $item        The comment object.
	 * @param string     $column_name The custom column's name.
	 */
	public function column_default( $item, $column_name ) {
		/**
		 * Fires when the default column output is displayed for a single row.
		 *
		 * @since 2.8.0
		 *
		 * @param string $column_name The custom column's name.
		 * @param string $comment_id  The comment ID as a numeric string.
		 */
		do_action( 'manage_comments_custom_column', $column_name, $item->comment_ID );
	}
}
