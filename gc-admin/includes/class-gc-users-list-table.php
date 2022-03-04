<?php
/**
 * List Table API: GC_Users_List_Table class
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

/**
 * Core class used to implement displaying users in a list table.
 *
 *
 * @access private
 *
 * @see GC_List_Table
 */
class GC_Users_List_Table extends GC_List_Table {

	/**
	 * Site ID to generate the Users list table for.
	 *
	 * @var int
	 */
	public $site_id;

	/**
	 * Whether or not the current Users list table is for Multisite.
	 *
	 * @var bool
	 */
	public $is_site_users;

	/**
	 * Constructor.
	 *
	 *
	 * @see GC_List_Table::__construct() for more information on default arguments.
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		parent::__construct(
			array(
				'singular' => 'user',
				'plural'   => 'users',
				'screen'   => isset( $args['screen'] ) ? $args['screen'] : null,
			)
		);

		$this->is_site_users = 'site-users-network' === $this->screen->id;

		if ( $this->is_site_users ) {
			$this->site_id = isset( $_REQUEST['id'] ) ? (int) $_REQUEST['id'] : 0;
		}
	}

	/**
	 * Check the current user's permissions.
	 *
	 *
	 * @return bool
	 */
	public function ajax_user_can() {
		if ( $this->is_site_users ) {
			return current_user_can( 'manage_sites' );
		} else {
			return current_user_can( 'list_users' );
		}
	}

	/**
	 * Prepare the users list for display.
	 *
	 *
	 * @global string $role
	 * @global string $usersearch
	 */
	public function prepare_items() {
		global $role, $usersearch;

		$usersearch = isset( $_REQUEST['s'] ) ? gc_unslash( trim( $_REQUEST['s'] ) ) : '';

		$role = isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : '';

		$per_page       = ( $this->is_site_users ) ? 'site_users_network_per_page' : 'users_per_page';
		$users_per_page = $this->get_items_per_page( $per_page );

		$paged = $this->get_pagenum();

		if ( 'none' === $role ) {
			$args = array(
				'number'  => $users_per_page,
				'offset'  => ( $paged - 1 ) * $users_per_page,
				'include' => gc_get_users_with_no_role( $this->site_id ),
				'search'  => $usersearch,
				'fields'  => 'all_with_meta',
			);
		} else {
			$args = array(
				'number' => $users_per_page,
				'offset' => ( $paged - 1 ) * $users_per_page,
				'role'   => $role,
				'search' => $usersearch,
				'fields' => 'all_with_meta',
			);
		}

		if ( '' !== $args['search'] ) {
			$args['search'] = '*' . $args['search'] . '*';
		}

		if ( $this->is_site_users ) {
			$args['blog_id'] = $this->site_id;
		}

		if ( isset( $_REQUEST['orderby'] ) ) {
			$args['orderby'] = $_REQUEST['orderby'];
		}

		if ( isset( $_REQUEST['order'] ) ) {
			$args['order'] = $_REQUEST['order'];
		}

		/**
		 * Filters the query arguments used to retrieve users for the current users list table.
		 *
		 *
		 * @param array $args Arguments passed to GC_User_Query to retrieve items for the current
		 *                    users list table.
		 */
		$args = apply_filters( 'users_list_table_query_args', $args );

		// Query the user IDs for this page.
		$gc_user_search = new GC_User_Query( $args );

		$this->items = $gc_user_search->get_results();

		$this->set_pagination_args(
			array(
				'total_items' => $gc_user_search->get_total(),
				'per_page'    => $users_per_page,
			)
		);
	}

	/**
	 * Output 'no users' message.
	 *
	 */
	public function no_items() {
		_e( '找不到用户。' );
	}

	/**
	 * Return an associative array listing all the views that can be used
	 * with this table.
	 *
	 * Provides a list of roles and user count for that role for easy
	 * Filtersing of the user table.
	 *
	 *
	 * @global string $role
	 *
	 * @return string[] An array of HTML links keyed by their view.
	 */
	protected function get_views() {
		global $role;

		$gc_roles = gc_roles();

		if ( $this->is_site_users ) {
			$url = 'site-users.php?id=' . $this->site_id;
			switch_to_blog( $this->site_id );
			$users_of_blog = count_users( 'time', $this->site_id );
			restore_current_blog();
		} else {
			$url           = 'users.php';
			$users_of_blog = count_users();
		}

		$total_users = $users_of_blog['total_users'];
		$avail_roles =& $users_of_blog['avail_roles'];
		unset( $users_of_blog );

		$current_link_attributes = empty( $role ) ? ' class="current" aria-current="page"' : '';

		$role_links        = array();
		$role_links['all'] = sprintf(
			'<a href="%s"%s>%s</a>',
			$url,
			$current_link_attributes,
			sprintf(
				/* translators: %s: Number of users. */
				_nx(
					'全部<span class="count">（%s）</span>',
					'全部<span class="count">（%s）</span>',
					$total_users,
					'users'
				),
				number_format_i18n( $total_users )
			)
		);

		foreach ( $gc_roles->get_names() as $this_role => $name ) {
			if ( ! isset( $avail_roles[ $this_role ] ) ) {
				continue;
			}

			$current_link_attributes = '';

			if ( $this_role === $role ) {
				$current_link_attributes = ' class="current" aria-current="page"';
			}

			$name = translate_user_role( $name );
			$name = sprintf(
				/* translators: 1: User role name, 2: Number of users. */
				__( '%1$s<span class="count">（%2$s）</span>' ),
				$name,
				number_format_i18n( $avail_roles[ $this_role ] )
			);

			$role_links[ $this_role ] = "<a href='" . esc_url( add_query_arg( 'role', $this_role, $url ) ) . "'$current_link_attributes>$name</a>";
		}

		if ( ! empty( $avail_roles['none'] ) ) {

			$current_link_attributes = '';

			if ( 'none' === $role ) {
				$current_link_attributes = ' class="current" aria-current="page"';
			}

			$name = __( '无角色' );
			$name = sprintf(
				/* translators: 1: User role name, 2: Number of users. */
				__( '%1$s<span class="count">（%2$s）</span>' ),
				$name,
				number_format_i18n( $avail_roles['none'] )
			);

			$role_links['none'] = "<a href='" . esc_url( add_query_arg( 'role', 'none', $url ) ) . "'$current_link_attributes>$name</a>";
		}

		return $role_links;
	}

	/**
	 * Retrieve an associative array of bulk actions available on this table.
	 *
	 *
	 * @return array Array of bulk action labels keyed by their action.
	 */
	protected function get_bulk_actions() {
		$actions = array();

		if ( is_multisite() ) {
			if ( current_user_can( 'remove_users' ) ) {
				$actions['remove'] = __( '移除' );
			}
		} else {
			if ( current_user_can( 'delete_users' ) ) {
				$actions['delete'] = __( '删除' );
			}
		}

		// Add a password reset link to the bulk actions dropdown.
		if ( current_user_can( 'edit_users' ) ) {
			$actions['resetpassword'] = __( '发送密码重置邮件' );
		}

		return $actions;
	}

	/**
	 * Output the controls to allow user roles to be changed in bulk.
	 *
	 *
	 * @param string $which Whether this is being invoked above ("top")
	 *                      or below the table ("bottom").
	 */
	protected function extra_tablenav( $which ) {
		$id        = 'bottom' === $which ? 'new_role2' : 'new_role';
		$button_id = 'bottom' === $which ? 'changeit2' : 'changeit';
		?>
	<div class="alignleft actions">
		<?php if ( current_user_can( 'promote_users' ) && $this->has_items() ) : ?>
		<label class="screen-reader-text" for="<?php echo $id; ?>"><?php _e( '将角色变更为&hellip;' ); ?></label>
		<select name="<?php echo $id; ?>" id="<?php echo $id; ?>">
			<option value=""><?php _e( '将角色变更为&hellip;' ); ?></option>
			<?php gc_dropdown_roles(); ?>
			<option value="none"><?php _e( '—这个站点没有任何用户角色—' ); ?></option>
		</select>
			<?php
			submit_button( __( '更改' ), '', $button_id, false );
		endif;

		/**
		 * Fires just before the closing div containing the bulk role-change controls
		 * in the Users list table.
		 *
		 *
		 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
		 */
		do_action( 'restrict_manage_users', $which );
		?>
		</div>
		<?php
		/**
		 * Fires immediately following the closing "actions" div in the tablenav for the users
		 * list table.
		 *
		 *
		 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
		 */
		do_action( 'manage_users_extra_tablenav', $which );
	}

	/**
	 * Capture the bulk action required, and return it.
	 *
	 * Overridden from the base class implementation to capture
	 * the role change drop-down.
	 *
	 *
	 * @return string The bulk action required.
	 */
	public function current_action() {
		if ( isset( $_REQUEST['changeit'] ) && ! empty( $_REQUEST['new_role'] ) ) {
			return 'promote';
		}

		return parent::current_action();
	}

	/**
	 * Get a list of columns for the list table.
	 *
	 *
	 * @return string[] Array of column titles keyed by their column name.
	 */
	public function get_columns() {
		$c = array(
			'cb'       => '<input type="checkbox" />',
			'username' => __( '用户名' ),
			'name'     => __( '显示名称' ),
			'email'    => __( '电子邮箱' ),
			'role'     => __( '角色' ),
			'posts'    => _x( '文章', 'post type general name' ),
		);

		if ( $this->is_site_users ) {
			unset( $c['posts'] );
		}

		return $c;
	}

	/**
	 * Get a list of sortable columns for the list table.
	 *
	 *
	 * @return array Array of sortable columns.
	 */
	protected function get_sortable_columns() {
		$c = array(
			'username' => 'login',
			'email'    => 'email',
		);

		return $c;
	}

	/**
	 * Generate the list table rows.
	 *
	 */
	public function display_rows() {
		// Query the post counts for this page.
		if ( ! $this->is_site_users ) {
			$post_counts = count_many_users_posts( array_keys( $this->items ) );
		}

		foreach ( $this->items as $userid => $user_object ) {
			echo "\n\t" . $this->single_row( $user_object, '', '', isset( $post_counts ) ? $post_counts[ $userid ] : 0 );
		}
	}

	/**
	 * Generate HTML for a single row on the users.php admin panel.
	 *
	 *
	 * @param GC_User $user_object The current user object.
	 * @param string  $style       Deprecated. Not used.
	 * @param string  $role        Deprecated. Not used.
	 * @param int     $numposts    Optional. Post count to display for this user. Defaults
	 *                             to zero, as in, a new user has made zero posts.
	 * @return string Output for a single row.
	 */
	public function single_row( $user_object, $style = '', $role = '', $numposts = 0 ) {
		if ( ! ( $user_object instanceof GC_User ) ) {
			$user_object = get_userdata( (int) $user_object );
		}
		$user_object->filter = 'display';
		$email               = $user_object->user_email;

		if ( $this->is_site_users ) {
			$url = "site-users.php?id={$this->site_id}&amp;";
		} else {
			$url = 'users.php?';
		}

		$user_roles = $this->get_role_list( $user_object );

		// Set up the hover actions for this user.
		$actions     = array();
		$checkbox    = '';
		$super_admin = '';

		if ( is_multisite() && current_user_can( 'manage_network_users' ) ) {
			if ( in_array( $user_object->user_login, get_super_admins(), true ) ) {
				$super_admin = ' &mdash; ' . __( '超级管理员' );
			}
		}

		// Check if the user for this row is editable.
		if ( current_user_can( 'list_users' ) ) {
			// Set up the user editing link.
			$edit_link = esc_url(
				add_query_arg(
					'gc_http_referer',
					urlencode( gc_unslash( $_SERVER['REQUEST_URI'] ) ),
					get_edit_user_link( $user_object->ID )
				)
			);

			if ( current_user_can( 'edit_user', $user_object->ID ) ) {
				$edit            = "<strong><a href=\"{$edit_link}\">{$user_object->user_login}</a>{$super_admin}</strong><br />";
				$actions['edit'] = '<a href="' . $edit_link . '">' . __( '编辑' ) . '</a>';
			} else {
				$edit = "<strong>{$user_object->user_login}{$super_admin}</strong><br />";
			}

			if ( ! is_multisite()
				&& get_current_user_id() !== $user_object->ID
				&& current_user_can( 'delete_user', $user_object->ID )
			) {
				$actions['delete'] = "<a class='submitdelete' href='" . gc_nonce_url( "users.php?action=delete&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( '删除' ) . '</a>';
			}

			if ( is_multisite()
				&& current_user_can( 'remove_user', $user_object->ID )
			) {
				$actions['remove'] = "<a class='submitdelete' href='" . gc_nonce_url( $url . "action=remove&amp;user=$user_object->ID", 'bulk-users' ) . "'>" . __( '移除' ) . '</a>';
			}

			// Add a link to the user's author archive, if not empty.
			$author_posts_url = get_author_posts_url( $user_object->ID );
			if ( $author_posts_url ) {
				$actions['view'] = sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					esc_url( $author_posts_url ),
					/* translators: %s: Author's display name. */
					esc_attr( sprintf( __( '阅读%s的文章' ), $user_object->display_name ) ),
					__( '查看' )
				);
			}

			// Add a link to send the user a reset password link by email.
			if ( get_current_user_id() !== $user_object->ID
				&& current_user_can( 'edit_user', $user_object->ID )
			) {
				$actions['resetpassword'] = "<a class='resetpassword' href='" . gc_nonce_url( "users.php?action=resetpassword&amp;users=$user_object->ID", 'bulk-users' ) . "'>" . __( '发送密码重置邮件' ) . '</a>';
			}

			/**
			 * Filters the action links displayed under each user in the Users list table.
			 *
		
			 *
			 * @param string[] $actions     An array of action links to be displayed.
			 *                              Default 'Edit', 'Delete' for single site, and
			 *                              'Edit', 'Remove' for Multisite.
			 * @param GC_User  $user_object GC_User object for the currently listed user.
			 */
			$actions = apply_filters( 'user_row_actions', $actions, $user_object );

			// Role classes.
			$role_classes = esc_attr( implode( ' ', array_keys( $user_roles ) ) );

			// Set up the checkbox (because the user is editable, otherwise it's empty).
			$checkbox = sprintf(
				'<label class="screen-reader-text" for="user_%1$s">%2$s</label>' .
				'<input type="checkbox" name="users[]" id="user_%1$s" class="%3$s" value="%1$s" />',
				$user_object->ID,
				/* translators: %s: User login. */
				sprintf( __( '选择%s' ), $user_object->user_login ),
				$role_classes
			);

		} else {
			$edit = "<strong>{$user_object->user_login}{$super_admin}</strong>";
		}

		$avatar = get_avatar( $user_object->ID, 32 );

		// Comma-separated list of user roles.
		$roles_list = implode( ', ', $user_roles );

		$r = "<tr id='user-$user_object->ID'>";

		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$classes = "$column_name column-$column_name";
			if ( $primary === $column_name ) {
				$classes .= ' has-row-actions column-primary';
			}
			if ( 'posts' === $column_name ) {
				$classes .= ' num'; // Special case for that column.
			}

			if ( in_array( $column_name, $hidden, true ) ) {
				$classes .= ' hidden';
			}

			$data = 'data-colname="' . esc_attr( gc_strip_all_tags( $column_display_name ) ) . '"';

			$attributes = "class='$classes' $data";

			if ( 'cb' === $column_name ) {
				$r .= "<th scope='row' class='check-column'>$checkbox</th>";
			} else {
				$r .= "<td $attributes>";
				switch ( $column_name ) {
					case 'username':
						$r .= "$avatar $edit";
						break;
					case 'name':
						if ( $user_object->first_name && $user_object->last_name ) {
							$r .= "$user_object->first_name $user_object->last_name";
						} elseif ( $user_object->first_name ) {
							$r .= $user_object->first_name;
						} elseif ( $user_object->last_name ) {
							$r .= $user_object->last_name;
						} else {
							$r .= sprintf(
								'<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">%s</span>',
								_x( '未知', 'name' )
							);
						}
						break;
					case 'email':
						$r .= "<a href='" . esc_url( "mailto:$email" ) . "'>$email</a>";
						break;
					case 'role':
						$r .= esc_html( $roles_list );
						break;
					case 'posts':
						if ( $numposts > 0 ) {
							$r .= sprintf(
								'<a href="%s" class="edit"><span aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
								"edit.php?author={$user_object->ID}",
								$numposts,
								sprintf(
									/* translators: %s: Number of posts. */
									_n( '此作者有%s篇文章', '此作者有%s篇文章', $numposts ),
									number_format_i18n( $numposts )
								)
							);
						} else {
							$r .= 0;
						}
						break;
					default:
						/**
						 * Filters the display output of custom columns in the Users list table.
						 *
					
						 *
						 * @param string $output      Custom column output. Default empty.
						 * @param string $column_name Column name.
						 * @param int    $user_id     ID of the currently-listed user.
						 */
						$r .= apply_filters( 'manage_users_custom_column', '', $column_name, $user_object->ID );
				}

				if ( $primary === $column_name ) {
					$r .= $this->row_actions( $actions );
				}
				$r .= '</td>';
			}
		}
		$r .= '</tr>';

		return $r;
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 *
	 * @return string Name of the default primary column, in this case, 'username'.
	 */
	protected function get_default_primary_column_name() {
		return 'username';
	}

	/**
	 * Returns an array of translated user role names for a given user object.
	 *
	 *
	 * @param GC_User $user_object The GC_User object.
	 * @return string[] An array of user role names keyed by role.
	 */
	protected function get_role_list( $user_object ) {
		$gc_roles = gc_roles();

		$role_list = array();

		foreach ( $user_object->roles as $role ) {
			if ( isset( $gc_roles->role_names[ $role ] ) ) {
				$role_list[ $role ] = translate_user_role( $gc_roles->role_names[ $role ] );
			}
		}

		if ( empty( $role_list ) ) {
			$role_list['none'] = _x( '无', 'no user roles' );
		}

		/**
		 * Filters the returned array of translated role names for a user.
		 *
		 *
		 * @param string[] $role_list   An array of translated user role names keyed by role.
		 * @param GC_User  $user_object A GC_User object.
		 */
		return apply_filters( 'get_role_list', $role_list, $user_object );
	}

}
