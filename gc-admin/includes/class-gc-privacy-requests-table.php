<?php
/**
 * List Table API: GC_Privacy_Requests_Table class
 *
 * @package GeChiUI
 * @subpackage Administration
 */

abstract class GC_Privacy_Requests_Table extends GC_List_Table {

	/**
	 * Action name for the requests this table will work with. Classes
	 * which inherit from GC_Privacy_Requests_Table should define this.
	 *
	 * Example: 'export_personal_data'.
	 *
	 * @since 4.9.6
	 *
	 * @var string $request_type Name of action.
	 */
	protected $request_type = 'INVALID';

	/**
	 * Post type to be used.
	 *
	 * @since 4.9.6
	 *
	 * @var string $post_type The post type.
	 */
	protected $post_type = 'INVALID';

	/**
	 * Gets columns to show in the list table.
	 *
	 * @since 4.9.6
	 *
	 * @return string[] Array of column titles keyed by their column name.
	 */
	public function get_columns() {
		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'email'             => __( '请求者' ),
			'status'            => __( '状态' ),
			'created_timestamp' => __( '已请求' ),
			'next_steps'        => __( '下一步' ),
		);
		return $columns;
	}

	/**
	 * Normalizes the admin URL to the current page (by request_type).
	 *
	 * @since 5.3.0
	 *
	 * @return string URL to the current admin page.
	 */
	protected function get_admin_url() {
		$pagenow = str_replace( '_', '-', $this->request_type );

		if ( 'remove-personal-data' === $pagenow ) {
			$pagenow = 'erase-personal-data';
		}

		return admin_url( $pagenow . '.php' );
	}

	/**
	 * Gets a list of sortable columns.
	 *
	 * @since 4.9.6
	 *
	 * @return array Default sortable columns.
	 */
	protected function get_sortable_columns() {
		/*
		 * The initial sorting is by 'Requested' (post_date) and descending.
		 * With initial sorting, the first click on 'Requested' should be ascending.
		 * With 'Requester' sorting active, the next click on 'Requested' should be descending.
		 */
		$desc_first = isset( $_GET['orderby'] );

		return array(
			'email'             => 'requester',
			'created_timestamp' => array( 'requested', $desc_first ),
		);
	}

	/**
	 * Returns the default primary column.
	 *
	 * @since 4.9.6
	 *
	 * @return string Default primary column name.
	 */
	protected function get_default_primary_column_name() {
		return 'email';
	}

	/**
	 * Counts the number of requests for each status.
	 *
	 * @since 4.9.6
	 *
	 * @global gcdb $gcdb GeChiUI database abstraction object.
	 *
	 * @return object Number of posts for each status.
	 */
	protected function get_request_counts() {
		global $gcdb;

		$cache_key = $this->post_type . '-' . $this->request_type;
		$counts    = gc_cache_get( $cache_key, 'counts' );

		if ( false !== $counts ) {
			return $counts;
		}

		$query = "
			SELECT post_status, COUNT( * ) AS num_posts
			FROM {$gcdb->posts}
			WHERE post_type = %s
			AND post_name = %s
			GROUP BY post_status";

		$results = (array) $gcdb->get_results( $gcdb->prepare( $query, $this->post_type, $this->request_type ), ARRAY_A );
		$counts  = array_fill_keys( get_post_stati(), 0 );

		foreach ( $results as $row ) {
			$counts[ $row['post_status'] ] = $row['num_posts'];
		}

		$counts = (object) $counts;
		gc_cache_set( $cache_key, $counts, 'counts' );

		return $counts;
	}

	/**
	 * Gets an associative array ( id => link ) with the list of views available on this table.
	 *
	 * @since 4.9.6
	 *
	 * @return string[] An array of HTML links keyed by their view.
	 */
	protected function get_views() {
		$current_status = isset( $_REQUEST['filter-status'] ) ? sanitize_text_field( $_REQUEST['filter-status'] ) : '';
		$statuses       = _gc_privacy_statuses();
		$views          = array();
		$counts         = $this->get_request_counts();
		$total_requests = absint( array_sum( (array) $counts ) );

		// Normalized admin URL.
		$admin_url = $this->get_admin_url();

		$status_label = sprintf(
			/* translators: %s: Number of requests. */
			_nx(
				'全部<span class="count">（%s）</span>',
				'全部<span class="count">（%s）</span>',
				$total_requests,
				'requests'
			),
			number_format_i18n( $total_requests )
		);

		$views['all'] = array(
			'url'     => esc_url( $admin_url ),
			'label'   => $status_label,
			'current' => empty( $current_status ),
		);

		foreach ( $statuses as $status => $label ) {
			$post_status = get_post_status_object( $status );
			if ( ! $post_status ) {
				continue;
			}

			$total_status_requests = absint( $counts->{$status} );

			if ( ! $total_status_requests ) {
				continue;
			}

			$status_label = sprintf(
				translate_nooped_plural( $post_status->label_count, $total_status_requests ),
				number_format_i18n( $total_status_requests )
			);

			$status_link = add_query_arg( 'filter-status', $status, $admin_url );

			$views[ $status ] = array(
				'url'     => esc_url( $status_link ),
				'label'   => $status_label,
				'current' => $status === $current_status,
			);
		}

		return $this->get_views_links( $views );
	}

	/**
	 * Gets bulk actions.
	 *
	 * @since 4.9.6
	 *
	 * @return array Array of bulk action labels keyed by their action.
	 */
	protected function get_bulk_actions() {
		return array(
			'resend'   => __( '重发确认请求' ),
			'complete' => __( '将请求标记为已完成' ),
			'delete'   => __( '删除请求' ),
		);
	}

	/**
	 * Process bulk actions.
	 *
	 * @since 4.9.6
	 * @since 5.6.0 Added support for the `complete` action.
	 */
	public function process_bulk_action() {
		$action      = $this->current_action();
		$request_ids = isset( $_REQUEST['request_id'] ) ? gc_parse_id_list( gc_unslash( $_REQUEST['request_id'] ) ) : array();

		if ( empty( $request_ids ) ) {
			return;
		}

		$count    = 0;
		$failures = 0;

		check_admin_referer( 'bulk-privacy_requests' );

		switch ( $action ) {
			case 'resend':
				foreach ( $request_ids as $request_id ) {
					$resend = _gc_privacy_resend_request( $request_id );

					if ( $resend && ! is_gc_error( $resend ) ) {
						$count++;
					} else {
						$failures++;
					}
				}

				if ( $failures ) {
					add_settings_error(
						'bulk_action',
						'bulk_action',
						sprintf(
							/* translators: %d: Number of requests. */
							_n(
								'%d个确认请求无法重新发送。',
								'%d个确认请求无法重新发送。',
								$failures
							),
							$failures
						),
						'danger'
					);
				}

				if ( $count ) {
					add_settings_error(
						'bulk_action',
						'bulk_action',
						sprintf(
							/* translators: %d: Number of requests. */
							_n(
								'%d个确认请求已成功发送。',
								'%d个确认请求已成功发送。',
								$count
							),
							$count
						),
						'success'
					);
				}

				break;

			case 'complete':
				foreach ( $request_ids as $request_id ) {
					$result = _gc_privacy_completed_request( $request_id );

					if ( $result && ! is_gc_error( $result ) ) {
						$count++;
					}
				}

				add_settings_error(
					'bulk_action',
					'bulk_action',
					sprintf(
						/* translators: %d: Number of requests. */
						_n(
							'%d个请求已标记为完成。',
							'%d个请求已标记为完成。',
							$count
						),
						$count
					),
					'success'
				);
				break;

			case 'delete':
				foreach ( $request_ids as $request_id ) {
					if ( gc_delete_post( $request_id, true ) ) {
						$count++;
					} else {
						$failures++;
					}
				}

				if ( $failures ) {
					add_settings_error(
						'bulk_action',
						'bulk_action',
						sprintf(
							/* translators: %d: Number of requests. */
							_n(
								'%d个请求无法删除。',
								'%d个请求无法删除。',
								$failures
							),
							$failures
						),
						'danger'
					);
				}

				if ( $count ) {
					add_settings_error(
						'bulk_action',
						'bulk_action',
						sprintf(
							/* translators: %d: Number of requests. */
							_n(
								'%d个请求已成功删除。',
								'%d个请求已成功删除。',
								$count
							),
							$count
						),
						'success'
					);
				}

				break;
		}
	}

	/**
	 * Prepares items to output.
	 *
	 * @since 4.9.6
	 * @since 5.1.0 Added support for column sorting.
	 */
	public function prepare_items() {
		$this->items    = array();
		$posts_per_page = $this->get_items_per_page( $this->request_type . '_requests_per_page' );
		$args           = array(
			'post_type'      => $this->post_type,
			'post_name__in'  => array( $this->request_type ),
			'posts_per_page' => $posts_per_page,
			'offset'         => isset( $_REQUEST['paged'] ) ? max( 0, absint( $_REQUEST['paged'] ) - 1 ) * $posts_per_page : 0,
			'post_status'    => 'any',
			's'              => isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '',
		);

		$orderby_mapping = array(
			'requester' => 'post_title',
			'requested' => 'post_date',
		);

		if ( isset( $_REQUEST['orderby'] ) && isset( $orderby_mapping[ $_REQUEST['orderby'] ] ) ) {
			$args['orderby'] = $orderby_mapping[ $_REQUEST['orderby'] ];
		}

		if ( isset( $_REQUEST['order'] ) && in_array( strtoupper( $_REQUEST['order'] ), array( 'ASC', 'DESC' ), true ) ) {
			$args['order'] = strtoupper( $_REQUEST['order'] );
		}

		if ( ! empty( $_REQUEST['filter-status'] ) ) {
			$filter_status       = isset( $_REQUEST['filter-status'] ) ? sanitize_text_field( $_REQUEST['filter-status'] ) : '';
			$args['post_status'] = $filter_status;
		}

		$requests_query = new GC_Query( $args );
		$requests       = $requests_query->posts;

		foreach ( $requests as $request ) {
			$this->items[] = gc_get_user_request( $request->ID );
		}

		$this->items = array_filter( $this->items );

		$this->set_pagination_args(
			array(
				'total_items' => $requests_query->found_posts,
				'per_page'    => $posts_per_page,
			)
		);
	}

	/**
	 * Returns the markup for the Checkbox column.
	 *
	 * @since 4.9.6
	 *
	 * @param GC_User_Request $item Item being shown.
	 * @return string Checkbox column markup.
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<label class="label-covers-full-cell" for="requester_%1$s"><span class="screen-reader-text">%2$s</span></label>' .
			'<input type="checkbox" name="request_id[]" id="requester_%1$s" value="%1$s" /><span class="spinner"></span>',
			esc_attr( $item->ID ),
			/* translators: Hidden accessibility text. %s: Email address. */
			sprintf( __( '选择%s' ), $item->email )
		);
	}

	/**
	 * Status column.
	 *
	 * @since 4.9.6
	 *
	 * @param GC_User_Request $item Item being shown.
	 * @return string Status column markup.
	 */
	public function column_status( $item ) {
		$status        = get_post_status( $item->ID );
		$status_object = get_post_status_object( $status );

		if ( ! $status_object || empty( $status_object->label ) ) {
			return '-';
		}

		$timestamp = false;

		switch ( $status ) {
			case 'request-confirmed':
				$timestamp = $item->confirmed_timestamp;
				break;
			case 'request-completed':
				$timestamp = $item->completed_timestamp;
				break;
		}

		echo '<span class="status-label status-' . esc_attr( $status ) . '">';
		echo esc_html( $status_object->label );

		if ( $timestamp ) {
			echo ' (' . $this->get_timestamp_as_date( $timestamp ) . ')';
		}

		echo '</span>';
	}

	/**
	 * Converts a timestamp for display.
	 *
	 * @since 4.9.6
	 *
	 * @param int $timestamp Event timestamp.
	 * @return string Human readable date.
	 */
	protected function get_timestamp_as_date( $timestamp ) {
		if ( empty( $timestamp ) ) {
			return '';
		}

		$time_diff = time() - $timestamp;

		if ( $time_diff >= 0 && $time_diff < DAY_IN_SECONDS ) {
			/* translators: %s: Human-readable time difference. */
			return sprintf( __( '%s前' ), human_time_diff( $timestamp ) );
		}

		return date_i18n( get_option( 'date_format' ), $timestamp );
	}

	/**
	 * Handles the default column.
	 *
	 * @since 4.9.6
	 * @since 5.7.0 Added `manage_{$this->screen->id}_custom_column` action.
	 *
	 * @param GC_User_Request $item        Item being shown.
	 * @param string          $column_name Name of column being shown.
	 */
	public function column_default( $item, $column_name ) {
		/**
		 * Fires for each custom column of a specific request type in the Requests list table.
		 *
		 * Custom columns are registered using the {@see 'manage_export-personal-data_columns'}
		 * and the {@see 'manage_erase-personal-data_columns'} filters.
		 *
		 * @since 5.7.0
		 *
		 * @param string          $column_name The name of the column to display.
		 * @param GC_User_Request $item        The item being shown.
		 */
		do_action( "manage_{$this->screen->id}_custom_column", $column_name, $item );
	}

	/**
	 * Returns the markup for the Created timestamp column. Overridden by children.
	 *
	 * @since 5.7.0
	 *
	 * @param GC_User_Request $item Item being shown.
	 * @return string Human readable date.
	 */
	public function column_created_timestamp( $item ) {
		return $this->get_timestamp_as_date( $item->created_timestamp );
	}

	/**
	 * Actions column. Overridden by children.
	 *
	 * @since 4.9.6
	 *
	 * @param GC_User_Request $item Item being shown.
	 * @return string Email column markup.
	 */
	public function column_email( $item ) {
		return sprintf( '<a href="%1$s">%2$s</a> %3$s', esc_url( 'mailto:' . $item->email ), $item->email, $this->row_actions( array() ) );
	}

	/**
	 * Returns the markup for the next steps column. Overridden by children.
	 *
	 * @since 4.9.6
	 *
	 * @param GC_User_Request $item Item being shown.
	 */
	public function column_next_steps( $item ) {}

	/**
	 * Generates content for a single row of the table,
	 *
	 * @since 4.9.6
	 *
	 * @param GC_User_Request $item The current item.
	 */
	public function single_row( $item ) {
		$status = $item->status;

		echo '<tr id="request-' . esc_attr( $item->ID ) . '" class="status-' . esc_attr( $status ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Embeds scripts used to perform actions. Overridden by children.
	 *
	 * @since 4.9.6
	 */
	public function embed_scripts() {}
}
