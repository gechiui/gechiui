<?php
/**
 * Toolbar API: GC_Admin_Bar class
 *
 * @package GeChiUI
 * @subpackage Toolbar
 */

/**
 * Core class used to implement the Toolbar API.
 *
 */
#[AllowDynamicProperties]
class GC_Admin_Bar {
	private $nodes = array();
	private $bound = false;
	public $user;

	/**
	 * Deprecated menu property.
	 *
	 * @deprecated 3.3.0 Modify admin bar nodes with GC_Admin_Bar::get_node(),
	 *                   GC_Admin_Bar::add_node(), and GC_Admin_Bar::remove_node().
	 * @var array
	 */
	public $menu = array();

	/**
	 * Initializes the admin bar.
	 *
	 */
	public function initialize() {
		$this->user = new stdClass();

		if ( is_user_logged_in() ) {
			/* Populate settings we need for the menu based on the current user. */
			$this->user->blogs = get_blogs_of_user( get_current_user_id() );
			if ( is_multisite() ) {
				$this->user->active_blog    = get_active_blog_for_user( get_current_user_id() );
				$this->user->domain         = empty( $this->user->active_blog ) ? user_admin_url() : trailingslashit( get_home_url( $this->user->active_blog->blog_id ) );
				$this->user->account_domain = $this->user->domain;
			} else {
				$this->user->active_blog    = $this->user->blogs[ get_current_blog_id() ];
				$this->user->domain         = trailingslashit( home_url() );
				$this->user->account_domain = $this->user->domain;
			}
		}

		add_action( 'gc_head', 'gc_admin_bar_header' );

		add_action( 'admin_head', 'gc_admin_bar_header' );

		if ( current_theme_supports( 'admin-bar' ) ) {
			/**
			 * To remove the default padding styles from GeChiUI for the Toolbar, use the following code:
			 * add_theme_support( 'admin-bar', array( 'callback' => '__return_false' ) );
			 */
			$admin_bar_args  = get_theme_support( 'admin-bar' );
			$header_callback = $admin_bar_args[0]['callback'];
		}

		if ( empty( $header_callback ) ) {
			$header_callback = '_admin_bar_bump_cb';
		}

		add_action( 'gc_head', $header_callback );

		gc_enqueue_script( 'admin-bar' );
		gc_enqueue_style( 'admin-bar' );

		/**
		 * Fires after GC_Admin_Bar is initialized.
		 *
		 * @since 3.1.0
		 */
		do_action( 'admin_bar_init' );
	}

	/**
	 * Adds a node (menu item) to the admin bar menu.
	 *
	 *
	 * @param array $node The attributes that define the node.
	 */
	public function add_menu( $node ) {
		$this->add_node( $node );
	}

	/**
	 * Removes a node from the admin bar.
	 *
	 *
	 * @param string $id The menu slug to remove.
	 */
	public function remove_menu( $id ) {
		$this->remove_node( $id );
	}

	/**
	 * Adds a node to the menu.
	 *
	 * @since 4.5.0 Added the ability to pass 'lang' and 'dir' meta data.
	 *
	 * @param array $args {
	 *     Arguments for adding a node.
	 *
	 *     @type string $id     ID of the item.
	 *     @type string $title  Title of the node.
	 *     @type string $parent Optional. ID of the parent node.
	 *     @type string $href   Optional. Link for the item.
	 *     @type bool   $group  Optional. Whether or not the node is a group. Default false.
	 *     @type array  $meta   Meta data including the following keys: 'html', 'class', 'rel', 'lang', 'dir',
	 *                          'onclick', 'target', 'title', 'tabindex'. Default empty.
	 * }
	 */
	public function add_node( $args ) {
		// Shim for old method signature: add_node( $parent_id, $menu_obj, $args ).
		if ( func_num_args() >= 3 && is_string( $args ) ) {
			$args = array_merge( array( 'parent' => $args ), func_get_arg( 2 ) );
		}

		if ( is_object( $args ) ) {
			$args = get_object_vars( $args );
		}

		// Ensure we have a valid title.
		if ( empty( $args['id'] ) ) {
			if ( empty( $args['title'] ) ) {
				return;
			}

			_doing_it_wrong( __METHOD__, __( '菜单ID不能为空。' ), '3.3.0' );
			// Deprecated: Generate an ID from the title.
			$args['id'] = esc_attr( sanitize_title( trim( $args['title'] ) ) );
		}

		$defaults = array(
			'id'     => false,
			'title'  => false,
			'parent' => false,
			'href'   => false,
			'group'  => false,
			'meta'   => array(),
		);

		// If the node already exists, keep any data that isn't provided.
		$maybe_defaults = $this->get_node( $args['id'] );
		if ( $maybe_defaults ) {
			$defaults = get_object_vars( $maybe_defaults );
		}

		// Do the same for 'meta' items.
		if ( ! empty( $defaults['meta'] ) && ! empty( $args['meta'] ) ) {
			$args['meta'] = gc_parse_args( $args['meta'], $defaults['meta'] );
		}

		$args = gc_parse_args( $args, $defaults );

		$back_compat_parents = array(
			'my-account-with-avatar' => array( 'my-account', '3.3' ),
			'my-blogs'               => array( 'my-sites', '3.3' ),
		);

		if ( isset( $back_compat_parents[ $args['parent'] ] ) ) {
			list( $new_parent, $version ) = $back_compat_parents[ $args['parent'] ];
			_deprecated_argument( __METHOD__, $version, sprintf( 'Use <code>%s</code> as the parent for the <code>%s</code> admin bar node instead of <code>%s</code>.', $new_parent, $args['id'], $args['parent'] ) );
			$args['parent'] = $new_parent;
		}

		$this->_set_node( $args );
	}

	/**
	 *
	 * @param array $args
	 */
	final protected function _set_node( $args ) {
		$this->nodes[ $args['id'] ] = (object) $args;
	}

	/**
	 * Gets a node.
	 *
	 *
	 * @param string $id
	 * @return object|void Node.
	 */
	final public function get_node( $id ) {
		$node = $this->_get_node( $id );
		if ( $node ) {
			return clone $node;
		}
	}

	/**
	 *
	 * @param string $id
	 * @return object|void
	 */
	final protected function _get_node( $id ) {
		if ( $this->bound ) {
			return;
		}

		if ( empty( $id ) ) {
			$id = 'root';
		}

		if ( isset( $this->nodes[ $id ] ) ) {
			return $this->nodes[ $id ];
		}
	}

	/**
	 *
	 * @return array|void
	 */
	final public function get_nodes() {
		$nodes = $this->_get_nodes();
		if ( ! $nodes ) {
			return;
		}

		foreach ( $nodes as &$node ) {
			$node = clone $node;
		}
		return $nodes;
	}

	/**
	 *
	 * @return array|void
	 */
	final protected function _get_nodes() {
		if ( $this->bound ) {
			return;
		}

		return $this->nodes;
	}

	/**
	 * Adds a group to a toolbar menu node.
	 *
	 * Groups can be used to organize toolbar items into distinct sections of a toolbar menu.
	 *
	 *
	 * @param array $args {
	 *     Array of arguments for adding a group.
	 *
	 *     @type string $id     ID of the item.
	 *     @type string $parent Optional. ID of the parent node. Default 'root'.
	 *     @type array  $meta   Meta data for the group including the following keys:
	 *                         'class', 'onclick', 'target', and 'title'.
	 * }
	 */
	final public function add_group( $args ) {
		$args['group'] = true;

		$this->add_node( $args );
	}

	/**
	 * Remove a node.
	 *
	 *
	 * @param string $id The ID of the item.
	 */
	public function remove_node( $id ) {
		$this->_unset_node( $id );
	}

	/**
	 *
	 * @param string $id
	 */
	final protected function _unset_node( $id ) {
		unset( $this->nodes[ $id ] );
	}

	/**
	 */
	public function render() {
		$root = $this->_bind();
		if ( $root ) {
			$this->_render( $root );
		}
	}

	/**
	 *
	 * @return object|void
	 */
	final protected function _bind() {
		if ( $this->bound ) {
			return;
		}

		/*
		 * Add the root node.
		 * Clear it first, just in case. Don't mess with The Root.
		 */
		$this->remove_node( 'root' );
		$this->add_node(
			array(
				'id'    => 'root',
				'group' => false,
			)
		);

		// Normalize nodes: define internal 'children' and 'type' properties.
		foreach ( $this->_get_nodes() as $node ) {
			$node->children = array();
			$node->type     = ( $node->group ) ? 'group' : 'item';
			unset( $node->group );

			// The Root wants your orphans. No lonely items allowed.
			if ( ! $node->parent ) {
				$node->parent = 'root';
			}
		}

		foreach ( $this->_get_nodes() as $node ) {
			if ( 'root' === $node->id ) {
				continue;
			}

			// Fetch the parent node. If it isn't registered, ignore the node.
			$parent = $this->_get_node( $node->parent );
			if ( ! $parent ) {
				continue;
			}

			// Generate the group class (we distinguish between top level and other level groups).
			$group_class = ( 'root' === $node->parent ) ? 'ab-top-menu' : 'ab-submenu';

			if ( 'group' === $node->type ) {
				if ( empty( $node->meta['class'] ) ) {
					$node->meta['class'] = $group_class;
				} else {
					$node->meta['class'] .= ' ' . $group_class;
				}
			}

			// Items in items aren't allowed. Wrap nested items in 'default' groups.
			if ( 'item' === $parent->type && 'item' === $node->type ) {
				$default_id = $parent->id . '-default';
				$default    = $this->_get_node( $default_id );

				/*
				 * The default group is added here to allow groups that are
				 * added before standard menu items to render first.
				 */
				if ( ! $default ) {
					/*
					 * Use _set_node because add_node can be overloaded.
					 * Make sure to specify default settings for all properties.
					 */
					$this->_set_node(
						array(
							'id'       => $default_id,
							'parent'   => $parent->id,
							'type'     => 'group',
							'children' => array(),
							'meta'     => array(
								'class' => $group_class,
							),
							'title'    => false,
							'href'     => false,
						)
					);
					$default            = $this->_get_node( $default_id );
					$parent->children[] = $default;
				}
				$parent = $default;

				/*
				 * Groups in groups aren't allowed. Add a special 'container' node.
				 * The container will invisibly wrap both groups.
				 */
			} elseif ( 'group' === $parent->type && 'group' === $node->type ) {
				$container_id = $parent->id . '-container';
				$container    = $this->_get_node( $container_id );

				// We need to create a container for this group, life is sad.
				if ( ! $container ) {
					/*
					 * Use _set_node because add_node can be overloaded.
					 * Make sure to specify default settings for all properties.
					 */
					$this->_set_node(
						array(
							'id'       => $container_id,
							'type'     => 'container',
							'children' => array( $parent ),
							'parent'   => false,
							'title'    => false,
							'href'     => false,
							'meta'     => array(),
						)
					);

					$container = $this->_get_node( $container_id );

					// Link the container node if a grandparent node exists.
					$grandparent = $this->_get_node( $parent->parent );

					if ( $grandparent ) {
						$container->parent = $grandparent->id;

						$index = array_search( $parent, $grandparent->children, true );
						if ( false === $index ) {
							$grandparent->children[] = $container;
						} else {
							array_splice( $grandparent->children, $index, 1, array( $container ) );
						}
					}

					$parent->parent = $container->id;
				}

				$parent = $container;
			}

			// Update the parent ID (it might have changed).
			$node->parent = $parent->id;

			// Add the node to the tree.
			$parent->children[] = $node;
		}

		$root        = $this->_get_node( 'root' );
		$this->bound = true;
		return $root;
	}

	/**
	 *
	 * @param object $root
	 */
	final protected function _render( $root ) {
		global $current_screen, $gc_roles, $menu, $submenu;
		/*
		 * Add browser classes.
		 * We have to do this here since admin bar shows on the front end.
		 */
		if( empty($gc_roles) )
			$gc_roles = new GC_Roles();

		$class = 'nojq nojs';
		if ( gc_is_mobile() ) {
			$class .= ' mobile';
		}
		$user_id = get_current_user_id();
        $current_user = get_userdata($user_id);

        //用户角色名称

        $role_name = translate_user_role( $gc_roles->roles[ array_shift($current_user->roles) ]['name'] );

        //用户的SaaS列表
        $blogs = get_blogs_of_user( $user_id );
        
        //加载头部样式
        if(!isset($current_screen)){
        	gc_enqueue_style( 'admin-bar' );
        }

		?>
        <!-- Header START -->
        <div class="header" id="gcadminbar">
            <div class="logo logo-dark dropdown dropdown-animated scale-right"> 
                 <a href="javascript:void(0);" data-toggle="modal" data-target="#adminmenu_map">
                    <img src="<?echo assets_url('/images/logo/logo.png')?>" alt="Logo"> 
                    <img class="logo-fold" src="<?echo assets_url('/images/logo/logo-fold.png')?>" alt="Logo"> 
                </a>
            </div>
             
            <div class="logo logo-white"> <a href="<?php echo esc_url( admin_url() ); ?>" data-toggle="modal"> <img src="<?echo assets_url('/images/logo/logo-white.png')?>" alt="Logo"> <img class="logo-fold" src="<?echo assets_url('/images/logo/logo-fold-white.png')?>" alt="Logo"> </a> </div>
            <div class="nav-wrap">
                <ul class="nav-left">
                <?php if(is_admin()) : ?>
                    <li class="desktop-toggle"> <a href="javascript:void(0);"> <i class="anticon"></i> </a> </li>
                    <li class="mobile-toggle"> <a href="javascript:void(0);"> <i class="anticon"></i> </a> </li>
                    <?php if(is_multisite()) :?>
                    <li class="dropdown dropdown-animated scale-right">
                        <div class="pointer" data-toggle="dropdown">
                            <p class="m-b-0 text-dark font-size-13"> <i class="anticon anticon-global m-l-20"></i> <?php _e( '我的系统'); ?></p>
                        </div>
                        <div class="p-b-15 p-t-20 dropdown-menu">
                            <?php
                            foreach ( $blogs as $user_blog ) {
                                switch_to_blog( $user_blog->userblog_id );
                                ?>
                            <div class="dropdown-item d-block p-h-15 p-v-10">
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="m-r-20">
                                    <a href="<?php echo esc_url( admin_url() ); ?>"> 
                                    <i class="anticon opacity-04 font-size-16 anticon-dashboard"></i> 
                                    <span><?php echo $user_blog->blogname; ?></span> 
                                    </a>
                                </p>
                                <p><a href="<?php echo esc_url( home_url() ); ?>"><?php _e( '预览'); ?> <i class="anticon anticon-double-right"></i></a></p>
                            </div>
                            </div>
                            <?php
                                restore_current_blog();
                            }
                            ?>
                        </div>
                    </li>
                    <?php endif;?>
                    <li id="gc-admin-bar-site-name" class="m-l-20">
                        <a href="<?php echo get_home_url(); ?>" class="text-dark m-b-0 font-size-13">
                            <i class="anticon anticon-home"></i> <?php echo get_bloginfo( 'name' ); ?>
                        </a>
                    </li>
                    <?php
                        //系统更新通知
                        $update_data = gc_get_update_data(); 
                        if ( current_user_can( 'update_core' ) && $update_data['counts']['total'] ) :
                        $update_url = is_multisite() ? network_admin_url('/update-core.php') : admin_url('/update-core.php');
                    ?>
                    <li id="gc-admin-bar-updates" class="m-l-20">
                        <a href="<?php echo $update_url; ?>" class="ab-item text-dark m-b-0 font-size-13">
                            <i class="ab-icon anticon anticon-sync" aria-hidden="true"></i> 
                            <span class="ab-label" aria-hidden="true"><?php echo number_format_i18n( $update_data['counts']['total'] ); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php
//                        foreach ( $root->children as $group ) {
//                            $this->_render_group( $group );
//                        }
                        ?>
                <?php else: ?>
                    <span class="m-l-20"><a href="<?php echo esc_url( get_dashboard_url() ); ?>"><i class="anticon anticon-dashboard"></i> <?php _e( '仪表盘'); ?></a></span>
                <?php endif;?>
                </ul>
                <ul class="nav-right">
                    <li class="dropdown dropdown-animated scale-left">
                        <?php if(isset($current_screen)):?>
                        <div class="pointer" data-toggle="dropdown">
                            <a class="m-b-0 m-r-20 text-dark font-size-13"> <i class="anticon anticon-user"></i> <?php echo  sprintf( __( '您好，%s') ,$current_user->display_name); ?></a>
                        </div>
                        <div class="p-b-15 p-t-20 dropdown-menu pop-profile">
                            <div class="p-h-20 p-b-15 m-b-10 border-bottom">
                                <div class="d-flex m-r-50">
                                    <div class="avatar avatar-icon avatar-blue"> 
                                    	<?php
                                    	$local_avatars = get_user_meta( $user_id, 'simple_local_avatar', true );
        
								        if ( empty( $local_avatars ) || empty( $local_avatars['full'] ) )
								            echo '<i class="anticon anticon-user"></i> ';
								        else
                                    	 	echo get_avatar( $user_id, 40 ); 
                                    	?>
                                    </div>
                                    <div class="m-l-10">
                                        <p class="m-b-0 text-dark font-weight-semibold"><?php echo $current_user->display_name; ?></p>
                                        <p class="m-b-0 opacity-07"><span class="badge badge-pill badge-warning"><?php echo $role_name;?></span></p>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo get_dashboard_url( $user_id, 'profile.php' ); ?>" class="dropdown-item d-block p-h-15 p-v-10">
                            <div class="d-flex align-items-center justify-content-between">
                                <div> <i class="anticon opacity-04 font-size-16 anticon-lock"></i> <span><?php _e( '编辑个人资料' ); ?></span> </div>
                                <i class="anticon font-size-10 anticon-right"></i> </div>
                            </a>
                            <a href="<?php echo esc_url( gc_logout_url() ); ?>" class="dropdown-item d-block p-h-15 p-v-10">
                            <div class="d-flex align-items-center justify-content-between">
                                <div> <i class="anticon opacity-04 font-size-16 anticon-logout"></i> <span class="m-l-10"><?php _e( '注销' ); ?></span> </div>
                            </div>
                            </a> 
                        </div>
                        <?php else: ?>
                        <p class="m-r-20 text-dark font-size-13"> 
                            <a href="<?php echo get_dashboard_url( $user_id, 'profile.php' ); ?>" class="p-h-15 p-v-10"><i class="anticon anticon-user"></i> <?php echo  sprintf( __( '您好，%s') ,$current_user->display_name); ?></a>
                            | <a href="<?php echo esc_url( gc_logout_url() ); ?>" class="p-h-15 p-v-10"><i class="anticon anticon-logout"></i> <?php _e( '注销' ); ?></a>
                        </p>
                        <?php endif; ?>
                    </li>
                    <?php
            if(is_admin()) :
                if ( $current_screen->show_screen_options() ) : ?>
                    <li> <a href="javascript:void(0);" data-toggle="modal" data-target="#screen-options-wrap"> <i class="anticon anticon-setting"></i> </a> </li>
                    <?php
                endif;
                if ( $current_screen->get_help_tabs() ) :
                    ?>
                    <li> <a href="javascript:void(0);" data-toggle="modal" data-target="#contextual-help-wrap"> <i class="anticon anticon-question-circle"></i> </a> </li>
                    <?php 
                endif; 
            endif; ?>
                    
                </ul>
            </div>
        </div>
        <!-- 导航地图 Start-->
        <?php if(isset($current_screen)):?>
        <div class="modal modal-left fade search" id="adminmenu_map">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header justify-content-between align-items-center">
                        <h5 class="modal-title"><?php is_network_admin() ? _e('SaaS后台') :  _e('产品与服务');  ?></h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <i class="anticon anticon-close"></i>
                        </button>
                    </div>
                    <div class="modal-body scrollable">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="header-menu-columns">
                                    <?php  _gc_menu_gcadminbar( $menu, $submenu ); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="d-md-block d-none border-left col-1"></div>
                                    <div class="col">
                                    <?php
                                    foreach ( $root->children as $group ) {
                                        $this->_render_group( $group );
                                    }
                                    ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <!-- 导航地图 End-->
		<?php
	}

	/**
	 *
	 * @param object $node
	 */
	final protected function _render_container( $node ) {
		if ( 'container' !== $node->type || empty( $node->children ) ) {
			return;
		}

		echo '<div id="' . esc_attr( 'gc-admin-bar-' . $node->id ) . '" class="ab-group-container">';
		foreach ( $node->children as $group ) {
			$this->_render_group( $group );
		}
		echo '</div>';
	}

	/**
	 *
	 * @param object $node
	 */
	final protected function _render_group( $node ) {
		if ( 'container' === $node->type ) {
			$this->_render_container( $node );
			return;
		}
		if ( 'group' !== $node->type || empty( $node->children ) ) {
			return;
		}

		if ( ! empty( $node->meta['class'] ) ) {
			$class = ' class="' . esc_attr( trim( $node->meta['class'] ) ) . '"';
		} else {
			$class = '';
		}

		echo "<ul id='" . esc_attr( 'gc-admin-bar-' . $node->id ) . "'$class>";
		foreach ( $node->children as $item ) {
			$this->_render_item( $item );
		}
		echo '</ul>';
	}

	/**
	 *
	 * @param object $node
	 */
	final protected function _render_item( $node ) {
		if ( 'item' !== $node->type ) {
			return;
		}

		$is_parent             = ! empty( $node->children );
		$has_link              = ! empty( $node->href );
		$is_root_top_item      = 'root-default' === $node->parent;
		$is_top_secondary_item = 'top-secondary' === $node->parent;

		// Allow only numeric values, then casted to integers, and allow a tabindex value of `0` for a11y.
		$tabindex        = ( isset( $node->meta['tabindex'] ) && is_numeric( $node->meta['tabindex'] ) ) ? (int) $node->meta['tabindex'] : '';
		$aria_attributes = ( '' !== $tabindex ) ? ' tabindex="' . $tabindex . '"' : '';

		$menuclass = 'm-b-10';
		$arrow     = '';

		if ( $is_parent ) {
			$menuclass        = 'm-b-20 menupop ';
			$aria_attributes .= ' aria-haspopup="true"';
		}

		if ( ! empty( $node->meta['class'] ) ) {
			$menuclass .= $node->meta['class'];
		}

		// Print the arrow icon for the menu children with children.
		if ( ! $is_root_top_item && ! $is_top_secondary_item && $is_parent ) {
			$arrow = '<span class="gc-admin-bar-arrow" aria-hidden="true"></span>';
		}

		if ( $menuclass ) {
			$menuclass = ' class="' . esc_attr( trim( $menuclass ) ) . '"';
		}

		echo "<div id='" . esc_attr( 'gc-admin-bar-' . $node->id ) . "'$menuclass>"; //一级DIV nav-item

		if ( $has_link ) {
			$attributes = array( 'onclick', 'target', 'title', 'rel', 'lang', 'dir' );
			echo "<a class='ab-item'$aria_attributes href='" . esc_url( $node->href ) . "'";
		} else {
			$attributes = array( 'onclick', 'target', 'title', 'rel', 'lang', 'dir' );
			echo '<div class="ab-item ab-empty-item"' . $aria_attributes;
		}

		foreach ( $attributes as $attribute ) {
			if ( empty( $node->meta[ $attribute ] ) ) {
				continue;
			}

			if ( 'onclick' === $attribute ) {
				echo " $attribute='" . esc_js( $node->meta[ $attribute ] ) . "'";
			} else {
				echo " $attribute='" . esc_attr( $node->meta[ $attribute ] ) . "'";
			}
		}
        if ( $is_parent ) {
            echo "><h5>{$arrow}{$node->title}</h5>";
        }else{
		    echo ">{$arrow}{$node->title}";
        }

		if ( $has_link ) {
			echo '</a>';
		} else {
			echo '</div>';
		}

		if ( $is_parent ) {
			echo '<div class="ab-sub-wrapper">';
			foreach ( $node->children as $group ) {
				$this->_render_group( $group );
			}
			echo '</div>';
		}

		if ( ! empty( $node->meta['html'] ) ) {
			echo $node->meta['html'];
		}

		echo '</div>';
	}

	/**
	 * Renders toolbar items recursively.
	 *
	 * @deprecated 3.3.0 Use GC_Admin_Bar::_render_item() or GC_Admin_bar::render() instead.
	 * @see GC_Admin_Bar::_render_item()
	 * @see GC_Admin_Bar::render()
	 *
	 * @param string $id    Unused.
	 * @param object $node
	 */
	public function recursive_render( $id, $node ) {
		_deprecated_function( __METHOD__, '3.3.0', 'GC_Admin_bar::render(), GC_Admin_Bar::_render_item()' );
		$this->_render_item( $node );
	}

	/**
	 * Adds menus to the admin bar.
	 *
	 * @since 6.3
	 */
	public function add_menus() {
		// User-related, aligned right.
		// gongenlin

		// add_action( 'admin_bar_menu', 'gc_admin_bar_my_account_menu', 0 ); //个人信息菜单
		// add_action( 'admin_bar_menu', 'gc_admin_bar_search_menu', 4 );
		// add_action( 'admin_bar_menu', 'gc_admin_bar_my_account_item', 7 );  //您好，**部分
		add_action( 'admin_bar_menu', 'gc_admin_bar_recovery_mode_menu', 8 );  //恢复模式功能组

		if ( !is_network_admin() && current_user_can( 'manage_network' ) ) {
			add_action( 'admin_bar_menu', 'gc_admin_bar_network_menu', 10 ); // 超级管理员组菜单
		}

		// Site-related.
		// add_action( 'admin_bar_menu', 'gc_admin_bar_sidebar_toggle', 0 ); //显示一个菜单按钮，用于移动端
		
		add_action( 'admin_bar_menu', 'gc_admin_bar_my_sites_menu', 20 );  // 我的GC多系统菜单

		// add_action( 'admin_bar_menu', 'gc_admin_bar_site_menu', 30 );  //当前系统链接
		// add_action( 'admin_bar_menu', 'gc_admin_bar_edit_site_menu', 40 ); // 编辑系统（new）
		add_action( 'admin_bar_menu', 'gc_admin_bar_customize_menu', 40 );  //自定义菜单
		// add_action( 'admin_bar_menu', 'gc_admin_bar_updates_menu', 50 );  //升级链接

		// Content-related.
		// if ( ! is_network_admin() && ! is_user_admin() ) {
			// add_action( 'admin_bar_menu', 'gc_admin_bar_comments_menu', 60 ); //显示评论
			// add_action( 'admin_bar_menu', 'gc_admin_bar_new_content_menu', 70 );  //常用功能
		// }
		add_action( 'admin_bar_menu', 'gc_admin_bar_edit_menu', 80 );
		add_action( 'admin_bar_menu', 'gc_admin_bar_gc_menu', 200 );  //关于格尺，系列的菜单

		// add_action( 'admin_bar_menu', 'gc_admin_bar_add_secondary_groups', 200 );

		/**
		 * Fires after menus are added to the menu bar.
		 *
		 * @since 3.1.0
		 */
		do_action( 'add_admin_bar_menus' );
	}
}
