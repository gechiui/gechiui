<?php
/**
 * Toolbar API: Top-level Toolbar functionality
 *
 * @package GeChiUI
 * @subpackage Toolbar
 */

/**
 * Instantiates the admin bar object and set it up as a global for access elsewhere.
 *
 * UNHOOKING THIS FUNCTION WILL NOT PROPERLY REMOVE THE ADMIN BAR.
 * For that, use show_admin_bar(false) or the {@see 'show_admin_bar'} filter.
 *
 * @access private
 *
 * @global GC_Admin_Bar $gc_admin_bar
 *
 * @return bool Whether the admin bar was successfully initialized.
 */
function _gc_admin_bar_init() {
	global $gc_admin_bar;

	if ( ! is_admin_bar_showing() ) {
		return false;
	}

	/* Load the admin bar class code ready for instantiation */
	require_once ABSPATH . GCINC . '/class-gc-admin-bar.php';

	/* Instantiate the admin bar */

	/**
	 * Filters the admin bar class to instantiate.
	 *
	 *
	 * @param string $gc_admin_bar_class Admin bar class to use. Default 'GC_Admin_Bar'.
	 */
	$admin_bar_class = apply_filters( 'gc_admin_bar_class', 'GC_Admin_Bar' );
	if ( class_exists( $admin_bar_class ) ) {
		$gc_admin_bar = new $admin_bar_class();
	} else {
		return false;
	}

	$gc_admin_bar->initialize();
	$gc_admin_bar->add_menus();

	return true;
}

/**
 * Renders the admin bar to the page based on the $gc_admin_bar->menu member var.
 *
 * This is called very early on the {@see 'gc_body_open'} action so that it will render
 * before anything else being added to the page body.
 *
 * For backward compatibility with themes not using the 'gc_body_open' action,
 * the function is also called late on {@see 'gc_footer'}.
 *
 * It includes the {@see 'admin_bar_menu'} action which should be used to hook in and
 * add new menus to the admin bar. That way you can be sure that you are adding at most
 * optimal point, right before the admin bar is rendered. This also gives you access to
 * the `$post` global, among others.
 *
 * @since 5.4.0 Called on 'gc_body_open' action first, with 'gc_footer' as a fallback.
 *
 * @global GC_Admin_Bar $gc_admin_bar
 */
function gc_admin_bar_render() {
	global $gc_admin_bar;
	static $rendered = false;

	if ( $rendered ) {
		return;
	}

	if ( ! is_admin_bar_showing() || ! is_object( $gc_admin_bar ) ) {
		return;
	}

	/**
	 * Loads all necessary admin bar items.
	 *
	 * This is the hook used to add, remove, or manipulate admin bar items.
	 *
	 *
	 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance, passed by reference.
	 */
	do_action_ref_array( 'admin_bar_menu', array( &$gc_admin_bar ) );

	/**
	 * Fires before the admin bar is rendered.
	 *
	 */
	do_action( 'gc_before_admin_bar_render' );

	$gc_admin_bar->render();

	/**
	 * Fires after the admin bar is rendered.
	 *
	 */
	do_action( 'gc_after_admin_bar_render' );

	$rendered = true;
}

/**
 * Adds the GeChiUI logo menu.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_gc_menu( $gc_admin_bar ) {
	if ( current_user_can( 'read' ) ) {
		$about_url = self_admin_url( 'about.php' );
	} elseif ( is_multisite() ) {
		$about_url = get_dashboard_url( get_current_user_id(), 'about.php' );
	} else {
		$about_url = false;
	}

	$gc_logo_menu_args = array(
		'id'    => 'about',
		'title' => '<h5>' . __( '关于' ) . '</h5>',
		'href'  => $about_url,
	);

	// Set tabindex="0" to make sub menus accessible when no URL is available.
	if ( ! $about_url ) {
		$gc_logo_menu_args['meta'] = array(
			'tabindex' => 0,
		);
	}

	$gc_admin_bar->add_node( $gc_logo_menu_args );

	if ( $about_url ) {
		// Add "About GeChiUI" link.
		$gc_admin_bar->add_node(
			array(
				'parent' => 'about',
				'id'     => 'about-gechiui',
				'title'  => __( '关于GeChiUI' ),
				'href'   => $about_url,
			)
		);
	}

	// Add www.GeChiUI.com link.
	$gc_admin_bar->add_node(
		array(
			'parent' => 'about',
			'id'     => 'gcorg',
			'title'  => __( 'www.GeChiUI.com' ),
			'href'   => __( 'https://www.gechiui.com/' ),
		)
	);

	// Add documentation link.
	$gc_admin_bar->add_node(
		array(
			'parent' => 'about',
			'id'     => 'documentation',
			'title'  => __( '文档' ),
			'href'   => __( 'https://www.gechiui.com/support/' ),
		)
	);
}

/**
 * Adds the sidebar toggle button.
 *
 * @since 3.8.0
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_sidebar_toggle( $gc_admin_bar ) {
	if ( is_admin() ) {
		$gc_admin_bar->add_node(
			array(
				'id'    => 'menu-toggle',
				'title' => '<span class="ab-icon" aria-hidden="true"></span><span class="screen-reader-text">' .
						/* translators: Hidden accessibility text. */
						__( '菜单' ) .
					'</span>',
				'href'  => '#',
			)
		);
	}
}

/**
 * Adds the "My Account" item.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_my_account_item( $gc_admin_bar ) {
	$user_id      = get_current_user_id();
	$current_user = gc_get_current_user();

	if ( ! $user_id ) {
		return;
	}

	if ( current_user_can( 'read' ) ) {
		$profile_url = get_edit_profile_url( $user_id );
	} elseif ( is_multisite() ) {
		$profile_url = get_dashboard_url( $user_id, 'profile.php' );
	} else {
		$profile_url = false;
	}

	$avatar = get_avatar( $user_id, 26 );
	/* translators: %s: Current user's display name. */
	$howdy = sprintf( __( '您好，%s' ), '<span class="display-name">' . $current_user->display_name . '</span>' );
	$class = empty( $avatar ) ? '' : 'with-avatar';

	$gc_admin_bar->add_node(
		array(
			'id'     => 'my-account',
			'parent' => 'top-secondary',
			'title'  => $howdy . $avatar,
			'href'   => $profile_url,
			'meta'   => array(
				'class' => $class,
			),
		)
	);
}

/**
 * Adds the "My Account" submenu items.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_my_account_menu( $gc_admin_bar ) {
	$user_id      = get_current_user_id();
	$current_user = gc_get_current_user();

	if ( ! $user_id ) {
		return;
	}

	if ( current_user_can( 'read' ) ) {
		$profile_url = get_edit_profile_url( $user_id );
	} elseif ( is_multisite() ) {
		$profile_url = get_dashboard_url( $user_id, 'profile.php' );
	} else {
		$profile_url = false;
	}

	$gc_admin_bar->add_group(
		array(
			'parent' => 'my-account',
			'id'     => 'user-actions',
		)
	);

	$user_info  = get_avatar( $user_id, 64 );
	$user_info .= "<span class='display-name'>{$current_user->display_name}</span>";

	if ( $current_user->display_name !== $current_user->user_login ) {
		$user_info .= "<span class='username'>{$current_user->user_login}</span>";
	}

	$gc_admin_bar->add_node(
		array(
			'parent' => 'user-actions',
			'id'     => 'user-info',
			'title'  => $user_info,
			'href'   => $profile_url,
			'meta'   => array(
				'tabindex' => -1,
			),
		)
	);

	if ( false !== $profile_url ) {
		$gc_admin_bar->add_node(
			array(
				'parent' => 'user-actions',
				'id'     => 'edit-profile',
				'title'  => __( '编辑个人资料' ),
				'href'   => $profile_url,
			)
		);
	}

	$gc_admin_bar->add_node(
		array(
			'parent' => 'user-actions',
			'id'     => 'logout',
			'title'  => __( '注销' ),
			'href'   => gc_logout_url(),
		)
	);
}

/**
 * Adds the "Site Name" menu.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_site_menu( $gc_admin_bar ) {
	// Don't show for logged out users.
	if ( ! is_user_logged_in() ) {
		return;
	}

	// Show only when the user is a member of this site, or they're a super admin.
	if ( ! is_user_member_of_blog() && ! current_user_can( 'manage_network' ) ) {
		return;
	}

	$blogname = get_bloginfo( 'name' );

	if ( ! $blogname ) {
		$blogname = preg_replace( '#^(https?://)?(www.)?#', '', get_home_url() );
	}

	if ( is_network_admin() ) {
		/* translators: %s: Site title. */
		$blogname = sprintf( __( 'SaaS后台：%s' ), esc_html( get_network()->site_name ) );
	} elseif ( is_user_admin() ) {
		/* translators: %s: Site title. */
		$blogname = sprintf( __( '用户仪表盘：%s' ), esc_html( get_network()->site_name ) );
	}

	$title = gc_html_excerpt( $blogname, 40, '&hellip;' );

	$gc_admin_bar->add_node(
		array(
			'id'    => 'site-name',
			'title' => $title,
			'href'  => ( is_admin() || ! current_user_can( 'read' ) ) ? home_url( '/' ) : admin_url(),
		)
	);

	// Create submenu items.

	if ( is_admin() ) {
		// Add an option to visit the site.
		$gc_admin_bar->add_node(
			array(
				'parent' => 'site-name',
				'id'     => 'view-site',
				'title'  => __( '查看系统' ),
				'href'   => home_url( '/' ),
			)
		);

		if ( is_blog_admin() && is_multisite() && current_user_can( 'manage_sites' ) ) {
			$gc_admin_bar->add_node(
				array(
					'parent' => 'site-name',
					'id'     => 'edit-site',
					'title'  => __( '编辑系统' ),
					'href'   => network_admin_url( 'site-info.php?id=' . get_current_blog_id() ),
				)
			);
		}
	} elseif ( current_user_can( 'read' ) ) {
		// We're on the front end, link to the Dashboard.
		$gc_admin_bar->add_node(
			array(
				'parent' => 'site-name',
				'id'     => 'dashboard',
				'title'  => __( '仪表盘' ),
				'href'   => admin_url(),
			)
		);

		// Add the appearance submenu items.
		gc_admin_bar_appearance_menu( $gc_admin_bar );
	}
}

/**
 * Adds the "编辑系统" link to the Toolbar.
 *
 * @since 5.9.0
 *
 * @global string $_gc_current_template_id
 * @since 6.3.0 Added `$_gc_current_template_id` global for editing of current template directly from the admin bar.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_edit_site_menu( $gc_admin_bar ) {
	global $_gc_current_template_id;

	// Don't show if a block theme is not activated.
	if ( ! gc_is_block_theme() ) {
		return;
	}

	// Don't show for users who can't edit theme options or when in the admin.
	if ( ! current_user_can( 'edit_theme_options' ) || is_admin() ) {
		return;
	}

	$gc_admin_bar->add_node(
		array(
			'id'    => 'site-editor',
			'title' => __( '编辑系统' ),
			'href'  => add_query_arg(
				array(
					'postType' => 'gc_template',
					'postId'   => $_gc_current_template_id,
				),
				admin_url( 'site-editor.php' )
			),
		)
	);
}

/**
 * Adds the "Customize" link to the Toolbar.
 *
 * @since 4.3.0
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 * @global GC_Customize_Manager $gc_customize
 */
function gc_admin_bar_customize_menu( $gc_admin_bar ) {
	global $gc_customize;

	// Don't show if a block theme is activated and no plugins use the customizer.
	if ( gc_is_block_theme() && ! has_action( 'customize_register' ) ) {
		return;
	}

	// Don't show for users who can't access the customizer or when in the admin.
	if ( ! current_user_can( 'customize' ) || is_admin() ) {
		return;
	}

	// Don't show if the user cannot edit a given customize_changeset post currently being previewed.
	if ( is_customize_preview() && $gc_customize->changeset_post_id()
		&& ! current_user_can( get_post_type_object( 'customize_changeset' )->cap->edit_post, $gc_customize->changeset_post_id() )
	) {
		return;
	}

	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	if ( is_customize_preview() && $gc_customize->changeset_uuid() ) {
		$current_url = remove_query_arg( 'customize_changeset_uuid', $current_url );
	}

	$customize_url = add_query_arg( 'url', urlencode( $current_url ), gc_customize_url() );
	if ( is_customize_preview() ) {
		$customize_url = add_query_arg( array( 'changeset_uuid' => $gc_customize->changeset_uuid() ), $customize_url );
	}

	$gc_admin_bar->add_node(
		array(
			'id'    => 'customize',
			'title' => __( '自定义' ),
			'href'  => $customize_url,
			'meta'  => array(
				'class' => 'hide-if-no-customize',
			),
		)
	);
	add_action( 'gc_before_admin_bar_render', 'gc_customize_support_script' );
}

/**
 * Add the "My Sites/[Site Name]" menu and all submenus.
 *
 *
 * @param GC_Admin_Bar $gc_admin_bar
 */
function gc_admin_bar_network_menu( $gc_admin_bar ) {
	// Don't show for logged out users or single site mode.
	if ( ! is_user_logged_in() || ! is_multisite() ) {
		return;
	}

	// Show only when the user has at least one site, or they're a super admin.
	if ( count( $gc_admin_bar->user->blogs ) < 1 && ! current_user_can( 'manage_network' ) ) {
		return;
	}


	if ( current_user_can( 'manage_network' ) ) {
		$gc_admin_bar->add_node(
			array(
				'id'     => 'network-admin',
				'title'  => __( 'SaaS后台' ),
				'href'   => network_admin_url(),
			)
		);

		$gc_admin_bar->add_node(
			array(
				'parent' => 'network-admin',
				'id'     => 'network-admin-d',
				'title'  => __( '仪表盘' ),
				'href'   => network_admin_url(),
			)
		);
        $update_data = gc_get_update_data();
        $gc_admin_bar->add_node(
			array(
				'parent' => 'network-admin',
				'id'     => 'network-admin-update-core',
				'title'  => sprintf(
                    __( '更新%s' ),
                    sprintf(
                        '<span class="update-plugins count-%s"><div class="avatar avatar-text bg-danger avatar-xs m-l-5">%s</div></span>',
                        $update_data['counts']['total'],
                        number_format_i18n( $update_data['counts']['total'] )
                    )
                ),
				'href'   => network_admin_url( 'update-core.php' ),
			)
		);

		if ( current_user_can( 'manage_sites' ) ) {
			$gc_admin_bar->add_node(
				array(
					'parent' => 'network-admin',
					'id'     => 'network-admin-s',
					'title'  => __( '多系统' ),
					'href'   => network_admin_url( 'sites.php' ),
				)
			);
		}

		if ( current_user_can( 'manage_network_users' ) ) {
			$gc_admin_bar->add_node(
				array(
					'parent' => 'network-admin',
					'id'     => 'network-admin-u',
					'title'  => __( '用户' ),
					'href'   => network_admin_url( 'users.php' ),
				)
			);
		}

		if ( current_user_can( 'manage_network_themes' ) ) {
			$gc_admin_bar->add_node(
				array(
					'parent' => 'network-admin',
					'id'     => 'network-admin-t',
					'title'  => __( '主题' ),
					'href'   => network_admin_url( 'themes.php' ),
				)
			);
		}

		if ( current_user_can( 'manage_network_plugins' ) ) {
			$gc_admin_bar->add_node(
				array(
					'parent' => 'network-admin',
					'id'     => 'network-admin-p',
					'title'  => __( '插件' ),
					'href'   => network_admin_url( 'plugins.php' ),
				)
			);
		}

		if ( current_user_can( 'manage_network_options' ) ) {
			$gc_admin_bar->add_node(
				array(
					'parent' => 'network-admin',
					'id'     => 'network-admin-o',
					'title'  => __( '设置' ),
					'href'   => network_admin_url( 'settings.php' ),
				)
			);
		}
	}
}

/**
 * Adds the "My Sites/[Site Name]" menu and all submenus.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_my_sites_menu( $gc_admin_bar ) {
	// Don't show for logged out users or single site mode.
	if ( ! is_user_logged_in() || ! is_multisite() ) {
		return;
	}

	// Show only when the user has at least one site, or they're a super admin.
	if ( count( $gc_admin_bar->user->blogs ) < 1 && ! current_user_can( 'manage_network' ) ) {
		return;
	}

	if ( $gc_admin_bar->user->active_blog ) {
		$my_sites_url = get_admin_url( $gc_admin_bar->user->active_blog->blog_id, 'my-sites.php' );
	} else {
		$my_sites_url = admin_url( 'my-sites.php' );
	}

	// Add site links.
    $gc_admin_bar->add_node(
		array(
			'id'    => 'my-sites',
			'title' => __( '我的系统' ),
			'href'  => $my_sites_url,
		)
	);

	foreach ( (array) $gc_admin_bar->user->blogs as $blog ) {
		switch_to_blog( $blog->userblog_id );

		if ( has_site_icon() ) {
			$blavatar = sprintf(
				'<img class="blavatar" src="%s" srcset="%s 2x" alt="" width="16" height="16" />',
				esc_url( get_site_icon_url( 16 ) ),
				esc_url( get_site_icon_url( 32 ) )
			);
		} else {
			$blavatar = '<div class="blavatar"></div>';
		}

		$blogname = $blog->blogname;

		if ( ! $blogname ) {
			$blogname = preg_replace( '#^(https?://)?(www.)?#', '', get_home_url() );
		}

		$menu_id = 'blog-' . $blog->userblog_id;

		if ( current_user_can( 'read' ) ) {
			$gc_admin_bar->add_node(
				array(
					'parent' => 'my-sites',
					'id'     => $menu_id,
					'title'  => $blavatar . $blogname,
					'href'   => admin_url(),
				)
			);
		} else {
			$gc_admin_bar->add_node(
				array(
					'parent' => 'my-sites',
					'id'     => $menu_id,
					'title'  => $blavatar . $blogname,
					'href'   => home_url(),
				)
			);
		}

		restore_current_blog();
	}
}

/**
 * Provides a shortlink.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_shortlink_menu( $gc_admin_bar ) {
	$short = gc_get_shortlink( 0, 'query' );
	$id    = 'get-shortlink';

	if ( empty( $short ) ) {
		return;
	}

	$html = '<input class="shortlink-input" type="text" readonly="readonly" value="' . esc_attr( $short ) . '" aria-label="' . __( '短链接' ) . '" />';

	$gc_admin_bar->add_node(
		array(
			'id'    => $id,
			'title' => __( '短链接' ),
			'href'  => $short,
			'meta'  => array( 'html' => $html ),
		)
	);
}

/**
 * Provides an edit link for posts and terms.
 *
 * @since 5.5.0 Added a "查看文章" link on Comments screen for a single post.
 *
 * @global GC_Term  $tag
 * @global GC_Query $gc_the_query GeChiUI Query object.
 * @global int      $user_id      The ID of the user being edited. Not to be confused with the
 *                                global $user_ID, which contains the ID of the current user.
 * @global int      $post_id      The ID of the post when editing comments for a single post.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_edit_menu( $gc_admin_bar ) {
	global $tag, $gc_the_query, $user_id, $post_id;

	if ( is_admin() ) {
		$current_screen   = get_current_screen();
		$post             = get_post();
		$post_type_object = null;

		if ( 'post' === $current_screen->base ) {
			$post_type_object = get_post_type_object( $post->post_type );
		} elseif ( 'edit' === $current_screen->base ) {
			$post_type_object = get_post_type_object( $current_screen->post_type );
		} elseif ( 'edit-comments' === $current_screen->base && $post_id ) {
			$post = get_post( $post_id );
			if ( $post ) {
				$post_type_object = get_post_type_object( $post->post_type );
			}
		}

		if ( ( 'post' === $current_screen->base || 'edit-comments' === $current_screen->base )
			&& 'add' !== $current_screen->action
			&& ( $post_type_object )
			&& current_user_can( 'read_post', $post->ID )
			&& ( $post_type_object->public )
			&& ( $post_type_object->show_in_admin_bar ) ) {
			if ( 'draft' === $post->post_status ) {
				$preview_link = get_preview_post_link( $post );
				$gc_admin_bar->add_node(
					array(
						'id'    => 'preview',
						'title' => $post_type_object->labels->view_item,
						'href'  => esc_url( $preview_link ),
						'meta'  => array( 'target' => 'gc-preview-' . $post->ID ),
					)
				);
			} else {
				$gc_admin_bar->add_node(
					array(
						'id'    => 'view',
						'title' => $post_type_object->labels->view_item,
						'href'  => get_permalink( $post->ID ),
					)
				);
			}
		} elseif ( 'edit' === $current_screen->base
			&& ( $post_type_object )
			&& ( $post_type_object->public )
			&& ( $post_type_object->show_in_admin_bar )
			&& ( get_post_type_archive_link( $post_type_object->name ) )
			&& ! ( 'post' === $post_type_object->name && 'posts' === get_option( 'show_on_front' ) ) ) {
			$gc_admin_bar->add_node(
				array(
					'id'    => 'archive',
					'title' => $post_type_object->labels->view_items,
					'href'  => get_post_type_archive_link( $current_screen->post_type ),
				)
			);
		} elseif ( 'term' === $current_screen->base && isset( $tag ) && is_object( $tag ) && ! is_gc_error( $tag ) ) {
			$tax = get_taxonomy( $tag->taxonomy );
			if ( is_term_publicly_viewable( $tag ) ) {
				$gc_admin_bar->add_node(
					array(
						'id'    => 'view',
						'title' => $tax->labels->view_item,
						'href'  => get_term_link( $tag ),
					)
				);
			}
		} elseif ( 'user-edit' === $current_screen->base && isset( $user_id ) ) {
			$user_object = get_userdata( $user_id );
			$view_link   = get_author_posts_url( $user_object->ID );
			if ( $user_object->exists() && $view_link ) {
				$gc_admin_bar->add_node(
					array(
						'id'    => 'view',
						'title' => __( '查看用户' ),
						'href'  => $view_link,
					)
				);
			}
		}
	} else {
		$current_object = $gc_the_query->get_queried_object();

		if ( empty( $current_object ) ) {
			return;
		}

		if ( ! empty( $current_object->post_type ) ) {
			$post_type_object = get_post_type_object( $current_object->post_type );
			$edit_post_link   = get_edit_post_link( $current_object->ID );
			if ( $post_type_object
				&& $edit_post_link
				&& current_user_can( 'edit_post', $current_object->ID )
				&& $post_type_object->show_in_admin_bar ) {
				$gc_admin_bar->add_node(
					array(
						'id'    => 'edit',
						'title' => $post_type_object->labels->edit_item,
						'href'  => $edit_post_link,
					)
				);
			}
		} elseif ( ! empty( $current_object->taxonomy ) ) {
			$tax            = get_taxonomy( $current_object->taxonomy );
			$edit_term_link = get_edit_term_link( $current_object->term_id, $current_object->taxonomy );
			if ( $tax && $edit_term_link && current_user_can( 'edit_term', $current_object->term_id ) ) {
				$gc_admin_bar->add_node(
					array(
						'id'    => 'edit',
						'title' => $tax->labels->edit_item,
						'href'  => $edit_term_link,
					)
				);
			}
		} elseif ( is_a( $current_object, 'GC_User' ) && current_user_can( 'edit_user', $current_object->ID ) ) {
			$edit_user_link = get_edit_user_link( $current_object->ID );
			if ( $edit_user_link ) {
				$gc_admin_bar->add_node(
					array(
						'id'    => 'edit',
						'title' => __( '编辑用户' ),
						'href'  => $edit_user_link,
					)
				);
			}
		}
	}
}

/**
 * Add "Add New" menu.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_new_content_menu( $gc_admin_bar ) {
	$actions = array();

	$cpts = (array) get_post_types( array( 'show_in_admin_bar' => true ), 'objects' );

	if ( isset( $cpts['post'] ) && current_user_can( $cpts['post']->cap->create_posts ) ) {
		$actions['post-new.php'] = array( $cpts['post']->labels->name_admin_bar, 'new-post' );
	}

	if ( isset( $cpts['attachment'] ) && current_user_can( 'upload_files' ) ) {
		$actions['media-new.php'] = array( $cpts['attachment']->labels->name_admin_bar, 'new-media' );
	}

	if ( current_user_can( 'manage_links' ) ) {
		$actions['link-add.php'] = array( _x( '链接', 'add new from admin bar' ), 'new-link' );
	}

	if ( isset( $cpts['page'] ) && current_user_can( $cpts['page']->cap->create_posts ) ) {
		$actions['post-new.php?post_type=page'] = array( $cpts['page']->labels->name_admin_bar, 'new-page' );
	}

	unset( $cpts['post'], $cpts['page'], $cpts['attachment'] );

	// Add any additional custom post types.
	foreach ( $cpts as $cpt ) {
		if ( ! current_user_can( $cpt->cap->create_posts ) ) {
			continue;
		}

		$key             = 'post-new.php?post_type=' . $cpt->name;
		$actions[ $key ] = array( $cpt->labels->name_admin_bar, 'new-' . $cpt->name );
	}
	// Avoid clash with parent node and a 'content' post type.
	if ( isset( $actions['post-new.php?post_type=content'] ) ) {
		$actions['post-new.php?post_type=content'][1] = 'add-new-content';
	}

	if ( current_user_can( 'create_users' ) || ( is_multisite() && current_user_can( 'promote_users' ) ) ) {
		$actions['user-new.php'] = array( _x( '用户', 'add new from admin bar' ), 'new-user' );
	}

	if ( ! $actions ) {
		return;
	}

	$title = '<span class="ab-icon" aria-hidden="true"></span><span class="ab-label">' . _x( '新建', 'admin bar menu group label' ) . '</span>';

	$gc_admin_bar->add_node(
		array(
			'id'    => 'new-content',
			'title' => $title,
			'href'  => admin_url( current( array_keys( $actions ) ) ),
		)
	);

	foreach ( $actions as $link => $action ) {
		list( $title, $id ) = $action;

		$gc_admin_bar->add_node(
			array(
				'parent' => 'new-content',
				'id'     => $id,
				'title'  => $title,
				'href'   => admin_url( $link ),
			)
		);
	}
}

/**
 * Adds edit comments link with awaiting moderation count bubble.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_comments_menu( $gc_admin_bar ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$awaiting_mod  = gc_count_comments();
	$awaiting_mod  = $awaiting_mod->moderated;
	$awaiting_text = sprintf(
		/* translators: Hidden accessibility text. %s: Number of comments. */
		_n( '%s条评论待审', '%s条评论待审', $awaiting_mod ),
		number_format_i18n( $awaiting_mod )
	);

	$icon   = '<span class="ab-icon" aria-hidden="true"></span>';
	$title  = '<span class="ab-label awaiting-mod pending-count count-' . $awaiting_mod . '" aria-hidden="true">' . number_format_i18n( $awaiting_mod ) . '</span>';
	$title .= '<span class="screen-reader-text comments-in-moderation-text">' . $awaiting_text . '</span>';

	$gc_admin_bar->add_node(
		array(
			'id'    => 'comments',
			'title' => $icon . $title,
			'href'  => admin_url( 'edit-comments.php' ),
		)
	);
}

/**
 * Adds appearance submenu items to the "Site Name" menu.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_appearance_menu( $gc_admin_bar ) {
	$gc_admin_bar->add_group(
		array(
			'parent' => 'site-name',
			'id'     => 'appearance',
		)
	);

	if ( current_user_can( 'switch_themes' ) ) {
		$gc_admin_bar->add_node(
			array(
				'parent' => 'appearance',
				'id'     => 'themes',
				'title'  => __( '主题' ),
				'href'   => admin_url( 'themes.php' ),
			)
		);
	}

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	if ( current_theme_supports( 'widgets' ) ) {
		$gc_admin_bar->add_node(
			array(
				'parent' => 'appearance',
				'id'     => 'widgets',
				'title'  => __( '小工具' ),
				'href'   => admin_url( 'widgets.php' ),
			)
		);
	}

	if ( current_theme_supports( 'menus' ) || current_theme_supports( 'widgets' ) ) {
		$gc_admin_bar->add_node(
			array(
				'parent' => 'appearance',
				'id'     => 'menus',
				'title'  => __( '菜单' ),
				'href'   => admin_url( 'nav-menus.php' ),
			)
		);
	}

	if ( current_theme_supports( 'custom-background' ) ) {
		$gc_admin_bar->add_node(
			array(
				'parent' => 'appearance',
				'id'     => 'background',
				'title'  => _x( '背景', 'custom background' ),
				'href'   => admin_url( 'themes.php?page=custom-background' ),
				'meta'   => array(
					'class' => 'hide-if-customize',
				),
			)
		);
	}

	if ( current_theme_supports( 'custom-header' ) ) {
		$gc_admin_bar->add_node(
			array(
				'parent' => 'appearance',
				'id'     => 'header',
				'title'  => _x( '页眉', 'custom image header' ),
				'href'   => admin_url( 'themes.php?page=custom-header' ),
				'meta'   => array(
					'class' => 'hide-if-customize',
				),
			)
		);
	}

}

/**
 * Provides an update link if theme/plugin/core updates are available.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_updates_menu( $gc_admin_bar ) {

	$update_data = gc_get_update_data();

	if ( ! $update_data['counts']['total'] ) {
		return;
	}

	$updates_text = sprintf(
		/* translators: Hidden accessibility text. %s: Total number of updates available. */
		_n( '有 %s 个更新可用', '有 %s 个更新可用', $update_data['counts']['total'] ),
		number_format_i18n( $update_data['counts']['total'] )
	);

	$icon   = '<span class="ab-icon" aria-hidden="true"></span>';
	$title  = '<span class="ab-label" aria-hidden="true">' . number_format_i18n( $update_data['counts']['total'] ) . '</span>';
	$title .= '<span class="screen-reader-text updates-available-text">' . $updates_text . '</span>';

	$gc_admin_bar->add_node(
		array(
			'id'    => 'updates',
			'title' => $icon . $title,
			'href'  => network_admin_url( 'update-core.php' ),
		)
	);
}

/**
 * Adds search form.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_search_menu( $gc_admin_bar ) {
	if ( is_admin() ) {
		return;
	}

	$form  = '<form action="' . esc_url( home_url( '/' ) ) . '" method="get" id="adminbarsearch">';
	$form .= '<input class="adminbar-input" name="s" id="adminbar-search" type="text" value="" maxlength="150" />';
	$form .= '<label for="adminbar-search" class="screen-reader-text">' .
			/* translators: Hidden accessibility text. */
			__( '搜索' ) .
		'</label>';
	$form .= '<input type="submit" class="adminbar-button" value="' . __( '搜索' ) . '" />';
	$form .= '</form>';

	$gc_admin_bar->add_node(
		array(
			'parent' => 'top-secondary',
			'id'     => 'search',
			'title'  => $form,
			'meta'   => array(
				'class'    => 'admin-bar-search',
				'tabindex' => -1,
			),
		)
	);
}

/**
 * Adds a link to exit recovery mode when Recovery Mode is active.
 *
 * @since 5.2.0
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_recovery_mode_menu( $gc_admin_bar ) {
	if ( ! gc_is_recovery_mode() ) {
		return;
	}

	$url = gc_login_url();
	$url = add_query_arg( 'action', GC_Recovery_Mode::EXIT_ACTION, $url );
	$url = gc_nonce_url( $url, GC_Recovery_Mode::EXIT_ACTION );

	$gc_admin_bar->add_node(
		array(
			'parent' => 'top-secondary',
			'id'     => 'recovery-mode',
			'title'  => __( '退出恢复模式' ),
			'href'   => $url,
		)
	);
}

/**
 * Adds secondary menus.
 *
 * @param GC_Admin_Bar $gc_admin_bar The GC_Admin_Bar instance.
 */
function gc_admin_bar_add_secondary_groups( $gc_admin_bar ) {
	$gc_admin_bar->add_group(
		array(
			'id'   => 'top-secondary',
			'meta' => array(
				'class' => 'ab-top-secondary',
			),
		)
	);

	$gc_admin_bar->add_group(
		array(
			'parent' => 'gc-logo',
			'id'     => 'gc-logo-external',
			'meta'   => array(
				'class' => 'ab-sub-secondary',
			),
		)
	);
}

/**
 * Prints style and scripts for the admin bar.
 *
 */
function gc_admin_bar_header() {
	$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';
	?>
<style<?php echo $type_attr; ?> media="print">#gcadminbar { display:none; }</style>
	<?php
}

/**
 * Prints default admin bar callback.
 *
 */
function _admin_bar_bump_cb() {
	$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';
	?>
<style<?php echo $type_attr; ?> media="screen">
	html { margin-top: 32px !important; }
	@media screen and ( max-width: 782px ) {
		html { margin-top: 46px !important; }
	}
</style>
	<?php
}

/**
 * Sets the display status of the admin bar.
 *
 * This can be called immediately upon plugin load. It does not need to be called
 * from a function hooked to the {@see 'init'} action.
 *
 * @global bool $show_admin_bar
 *
 * @param bool $show Whether to allow the admin bar to show.
 */
function show_admin_bar( $show ) {
	global $show_admin_bar;
	$show_admin_bar = (bool) $show;
}

/**
 * Determines whether the admin bar should be showing.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.gechiui.com/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @global bool   $show_admin_bar
 * @global string $pagenow        The filename of the current screen.
 *
 * @return bool Whether the admin bar should be showing.
 */
function is_admin_bar_showing() {
	global $show_admin_bar, $pagenow;

	// For all these types of requests, we never want an admin bar.
	if ( defined( 'XMLRPC_REQUEST' ) || defined( 'DOING_AJAX' ) || defined( 'IFRAME_REQUEST' ) || gc_is_json_request() ) {
		return false;
	}

	if ( is_embed() ) {
		return false;
	}

	// Integrated into the admin.
	if ( is_admin() ) {
		return true;
	}

	if ( ! isset( $show_admin_bar ) ) {
		if ( ! is_user_logged_in() || 'gc-login.php' === $pagenow ) {
			$show_admin_bar = false;
		} else {
			$show_admin_bar = _get_admin_bar_pref();
		}
	}

	/**
	 * Filters whether to show the admin bar.
	 *
	 * Returning false to this hook is the recommended way to hide the admin bar.
	 * The user's display preference is used for logged in users.
	 *
	 *
	 * @param bool $show_admin_bar Whether the admin bar should be shown. Default false.
	 */
	$show_admin_bar = apply_filters( 'show_admin_bar', $show_admin_bar );

	return $show_admin_bar;
}

/**
 * Retrieves the admin bar display preference of a user.
 *
 * @access private
 *
 * @param string $context Context of this preference check. Defaults to 'front'. The 'admin'
 *                        preference is no longer used.
 * @param int    $user    Optional. ID of the user to check, defaults to 0 for current user.
 * @return bool Whether the admin bar should be showing for this user.
 */
function _get_admin_bar_pref( $context = 'front', $user = 0 ) {
	$pref = get_user_option( "show_admin_bar_{$context}", $user );
	if ( false === $pref ) {
		return true;
	}

	return 'true' === $pref;
}
