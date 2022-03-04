<?php
/**
 * List Table API: GC_Plugin_Install_List_Table class
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Core class used to implement displaying plugins to install in a list table.
 *
 * @access private
 *
 * @see GC_List_Table
 */
class GC_Plugin_Install_List_Table extends GC_List_Table {

	public $order   = 'ASC';
	public $orderby = null;
	public $groups  = array();

	private $error;

	/**
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can( 'install_plugins' );
	}

	/**
	 * Return the list of known plugins.
	 *
	 * Uses the transient data from the updates API to determine the known
	 * installed plugins.
	 * 
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_installed_plugins() {
		$plugins = array();

		$plugin_info = get_site_transient( 'update_plugins' );
		if ( isset( $plugin_info->no_update ) ) {
			foreach ( $plugin_info->no_update as $plugin ) {
				if ( isset( $plugin->slug ) ) {
					$plugin->upgrade          = false;
					$plugins[ $plugin->slug ] = $plugin;
				}
			}
		}

		if ( isset( $plugin_info->response ) ) {
			foreach ( $plugin_info->response as $plugin ) {
				if ( isset( $plugin->slug ) ) {
					$plugin->upgrade          = true;
					$plugins[ $plugin->slug ] = $plugin;
				}
			}
		}

		return $plugins;
	}

	/**
	 * Return a list of slugs of installed plugins, if known.
	 *
	 * Uses the transient data from the updates API to determine the slugs of
	 * known installed plugins. This might be better elsewhere, perhaps even
	 * within get_plugins().
	 * 
	 *
	 * @return array
	 */
	protected function get_installed_plugin_slugs() {
		return array_keys( $this->get_installed_plugins() );
	}

	/**
	 * @global array  $tabs
	 * @global string $tab
	 * @global int    $paged
	 * @global string $type
	 * @global string $term
	 */
	public function prepare_items() {
		include_once ABSPATH . 'gc-admin/includes/plugin-install.php';

		global $tabs, $tab, $paged, $type, $term;

		gc_reset_vars( array( 'tab' ) );

		$paged = $this->get_pagenum();

		$per_page = 36;

		// These are the tabs which are shown on the page.
		$tabs = array();

		if ( 'search' === $tab ) {
			$tabs['search'] = __( '搜索结果' );
		}

		if ( 'beta' === $tab || false !== strpos( get_bloginfo( 'version' ), '-' ) ) {
			$tabs['beta'] = _x( 'Beta测试', 'Plugin Installer' );
		}

        $tabs['featured']     = _x( '特色', 'Plugin Installer' );
        $tabs['free']     = _x( '免费', 'Plugin Installer' );
        $tabs['all']     = _x( '全部', 'Plugin Installer' );
        $tabs['professionals']   = _x( '专业版', 'Plugin Installer' );

		if ( current_user_can( 'upload_plugins' ) ) {
			// No longer a real tab. Here for filter compatibility.
			// Gets skipped in get_views().
			$tabs['upload'] = __( '上传插件' );
		}

		$nonmenu_tabs = array( 'plugin-information' ); // Valid actions to perform which do not have a Menu item.

		/**
		 * Filters the tabs shown on the Add Plugins screen.
		 *
		 * @since 2.7.0
		 *
		 * @param string[] $tabs The tabs shown on the Add Plugins screen. Defaults include
		 *                       'featured', 'free', 'all', 'professionals', and 'upload'.
		 */
		$tabs = apply_filters( 'install_plugins_tabs', $tabs );

		/**
		 * Filters tabs not associated with a menu item on the Add Plugins screen.
		 *
		 * @since 2.7.0
		 *
		 * @param string[] $nonmenu_tabs The tabs that don't have a menu item on the Add Plugins screen.
		 */
		$nonmenu_tabs = apply_filters( 'install_plugins_nonmenu_tabs', $nonmenu_tabs );

		// If a non-valid menu tab has been selected, And it's not a non-menu action.
		if ( empty( $tab ) || ( ! isset( $tabs[ $tab ] ) && ! in_array( $tab, (array) $nonmenu_tabs, true ) ) ) {
			$tab = key( $tabs );
		}

		$installed_plugins = $this->get_installed_plugins();

		$args = array(
			'page'     => $paged,
			'per_page' => $per_page,
			// Send the locale to the API so it can provide context-sensitive results.
			'locale'   => get_user_locale(),
		);

		switch ( $tab ) {
			case 'search':
				$type = isset( $_REQUEST['type'] ) ? gc_unslash( $_REQUEST['type'] ) : 'term';
				$term = isset( $_REQUEST['s'] ) ? gc_unslash( $_REQUEST['s'] ) : '';

				switch ( $type ) {
					case 'tag':
						$args['tag'] = sanitize_title_with_dashes( $term );
						break;
					case 'term':
						$args['search'] = $term;
						break;
					case 'author':
						$args['author'] = $term;
						break;
				}

				break;

			case 'featured':
			case 'new':
			case 'beta':
         case 'all':
         case 'free':
				$args['browse'] = $tab;
				break;
                
         case 'professionals':
                $args['browse'] = $tab;
				$action = 'save_gcorg_username_' . get_current_user_id();
				if ( isset( $_GET['_gcnonce'] ) && gc_verify_nonce( gc_unslash( $_GET['_gcnonce'] ), $action ) ) {
                    $user = array();
                    if( isset( $_GET['username'] ) ) {
					   $user['username'] = gc_unslash( $_GET['username'] ) ;
                    }
                    if( isset( $_GET['appkey'] ) ) {
					   $user['appkey'] = gc_unslash( $_GET['appkey'] ) ;
                    }
					// If the save url parameter is passed with a falsey value, don't save the professional user.
					if ( ! isset( $_GET['save'] ) || $_GET['save'] ) {
						update_user_meta( get_current_user_id(), 'gcorg_professionals', $user );
					}
				} else {
					$user = get_user_option( 'gcorg_professionals' );
				}
				if ( $user ) {
					$args['username'] = $user['username'];
                    $args['appkey'] = $user['appkey'];
				} else {
					$args = false;
				}
                
				add_action( 'install_plugins_professionals', 'install_plugins_professionals_form', 9, 0 );
				break;
                
			default:
				$args = false;
				break;
		}

		/**
		 * Filters API request arguments for each Add Plugins screen tab.
		 *
		 * The dynamic portion of the hook name, `$tab`, refers to the plugin install tabs.
		 *
		 * Possible hook names include:
		 *
		 *  - `install_plugins_table_api_args_professionals`
		 *  - `install_plugins_table_api_args_featured`
		 *  - `install_plugins_table_api_args_free`
		 *  - `install_plugins_table_api_args_all`
		 *  - `install_plugins_table_api_args_upload`
		 *
		 * @since 3.7.0
		 *
		 * @param array|false $args Plugin install API arguments.
		 */
		$args = apply_filters( "install_plugins_table_api_args_{$tab}", $args );
		if ( ! $args ) {
			return;
		}

		$api = plugins_api( 'query_plugins', $args );
		if ( is_gc_error( $api ) ) {
			$this->error = $api;
			return;
		}

		$this->items = $api->plugins;

		if ( $this->orderby ) {
			uasort( $this->items, array( $this, 'order_callback' ) );
		}
        
		$this->set_pagination_args(
			array(
				'total_items' => $api->info['results'],
				'per_page'    => $args['per_page'],
			)
		);

		if ( isset( $api->info['groups'] ) ) {
			$this->groups = $api->info['groups'];
		}

		if ( $installed_plugins ) {
			$js_plugins = array_fill_keys(
				array( 'all', 'search', 'active', 'inactive', 'recently_activated', 'mustuse', 'dropins' ),
				array()
			);

			$js_plugins['all'] = array_values( gc_list_pluck( $installed_plugins, 'plugin' ) );
			$upgrade_plugins   = gc_filter_object_list( $installed_plugins, array( 'upgrade' => true ), 'and', 'plugin' );

			if ( $upgrade_plugins ) {
				$js_plugins['upgrade'] = array_values( $upgrade_plugins );
			}

			gc_localize_script(
				'updates',
				'_gcUpdatesItemCounts',
				array(
					'plugins' => $js_plugins,
					'totals'  => gc_get_update_data(),
				)
			);
		}
	}

	/**
	 */
	public function no_items() {
		if ( isset( $this->error ) ) { ?>
			<div class="inline error"><p><?php echo $this->error->get_error_message(); ?></p>
				<p class="hide-if-no-js"><button class="button try-again"><?php _e( '重试' ); ?></button></p>
			</div>
		<?php } else { ?>
			<div class="no-plugin-results"><?php _e( '未找到插件。试试其他搜索条件。' ); ?></div>
			<?php
		}
	}

	/**
	 * @global array $tabs
	 * @global string $tab
	 *
	 * @return array
	 */
	protected function get_views() {
		global $tabs, $tab;

		$display_tabs = array();
		foreach ( (array) $tabs as $action => $text ) {
			$current_link_attributes                     = ( $action === $tab ) ? ' class="current" aria-current="page"' : '';
			$href                                        = self_admin_url( 'plugin-install.php?tab=' . $action );
			$display_tabs[ 'plugin-install-' . $action ] = "<a href='$href'$current_link_attributes>$text</a>";
		}
		// No longer a real tab.
		unset( $display_tabs['plugin-install-upload'] );

		return $display_tabs;
	}

	/**
	 * Override parent views so we can use the filter bar display.
	 */
	public function views() {
		$views = $this->get_views();

		/** This filter is documented in gc-admin/inclues/class-gc-list-table.php */
		$views = apply_filters( "views_{$this->screen->id}", $views );

		$this->screen->render_screen_reader_content( 'heading_views' );
		?>
<div class="gc-filter">
	<ul class="filter-links">
		<?php
		if ( ! empty( $views ) ) {
			foreach ( $views as $class => $view ) {
				$views[ $class ] = "\t<li class='$class'>$view";
			}
			echo implode( " </li>\n", $views ) . "</li>\n";
		}
		?>
	</ul>

		<?php install_search_form(); ?>
</div>
		<?php
	}

	/**
	 * Displays the plugin install table.
	 *
	 * Overrides the parent display() method to provide a different container.
	 * 
	 */
	public function display() { 
		$singular = $this->_args['singular'];

		$data_attr = '';

		if ( $singular ) {
			$data_attr = " data-gc-lists='list:$singular'";
		}

		$this->display_tablenav( 'top' );

		?>
<div class="gc-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
		<?php
		$this->screen->render_screen_reader_content( 'heading_list' );
		?>
	<div id="the-list"<?php echo $data_attr; ?>>
		<?php $this->display_rows_or_placeholder(); ?>
	</div>
</div>
		<?php
		$this->display_tablenav( 'bottom' );
	}

	/**
	 * @global string $tab
	 *
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {

		if ( 'top' === $which ) {
			gc_referer_field();
			?>
			<div class="tablenav top">
				<div class="alignleft actions">
					<?php
					/**
					 * Fires before the Plugin Install table header pagination is displayed.
					 *
					 * @since 2.7.0
					 */
					do_action( 'install_plugins_table_header' );
					?>
				</div>
				<?php $this->pagination( $which ); ?>
				<br class="clear" />
			</div>
		<?php } else { ?>
			<div class="tablenav bottom">
				<?php $this->pagination( $which ); ?>
				<br class="clear" />
			</div>
			<?php
		}
	}

	/**
	 * @return array
	 */
	protected function get_table_classes() {
		return array( 'widefat', $this->_args['plural'] );
	}

	/**
	 * @return array
	 */
	public function get_columns() {
		return array();
	}

	/**
	 * @param object $plugin_a
	 * @param object $plugin_b
	 * @return int
	 */
	private function order_callback( $plugin_a, $plugin_b ) {
		$orderby = $this->orderby;
		if ( ! isset( $plugin_a->$orderby, $plugin_b->$orderby ) ) {
			return 0;
		}

		$a = $plugin_a->$orderby;
		$b = $plugin_b->$orderby;

		if ( $a === $b ) {
			return 0;
		}

		if ( 'DESC' === $this->order ) {
			return ( $a < $b ) ? 1 : -1;
		} else {
			return ( $a < $b ) ? -1 : 1;
		}
	}

	public function display_rows() {
		$plugins_allowedtags = array(
			'a'       => array(
				'href'   => array(),
				'title'  => array(),
				'target' => array(),
			),
			'abbr'    => array( 'title' => array() ),
			'acronym' => array( 'title' => array() ),
			'code'    => array(),
			'pre'     => array(),
			'em'      => array(),
			'strong'  => array(),
			'ul'      => array(),
			'ol'      => array(),
			'li'      => array(),
			'p'       => array(),
			'br'      => array(),
		);

		$plugins_group_titles = array(
			'Performance' => _x( '性能', 'Plugin installer group title' ),
			'Social'      => _x( '社交', 'Plugin installer group title' ),
			'Tools'       => _x( '工具', 'Plugin installer group title' ),
		);

		$group = null;

		foreach ( (array) $this->items as $plugin ) {
			if ( is_object( $plugin ) ) {
				$plugin = (array) $plugin;
			}

			// Display the group heading if there is one.
			if ( isset( $plugin['group'] ) && $plugin['group'] !== $group ) {
				if ( isset( $this->groups[ $plugin['group'] ] ) ) {
					$group_name = $this->groups[ $plugin['group'] ];
					if ( isset( $plugins_group_titles[ $group_name ] ) ) {
						$group_name = $plugins_group_titles[ $group_name ];
					}
				} else {
					$group_name = $plugin['group'];
				}

				// Starting a new group, close off the divs of the last one.
				if ( ! empty( $group ) ) {
					echo '</div></div>';
				}

				echo '<div class="plugin-group"><h3>' . esc_html( $group_name ) . '</h3>';
				// Needs an extra wrapping div for nth-child selectors to work.
				echo '<div class="plugin-items">';

				$group = $plugin['group'];
			}

			$title = gc_kses( $plugin['name'], $plugins_allowedtags );

			// Remove any HTML from the description.
			$description = strip_tags( $plugin['short_description'] );
			$version     = gc_kses( $plugin['version'], $plugins_allowedtags );

			$name = strip_tags( $title . ' ' . $version );

			$author = gc_kses( $plugin['author'], $plugins_allowedtags );
			if ( ! empty( $author ) ) {
				/* translators: %s: Plugin author. */
				$author = ' <cite>' . sprintf( __( '由 %s' ), $author ) . '</cite>';
			}

			$requires_php = isset( $plugin['requires_php'] ) ? $plugin['requires_php'] : null;
			$requires_gc  = isset( $plugin['requires'] ) ? $plugin['requires'] : null;

			$compatible_php = is_php_version_compatible( $requires_php );
			$compatible_gc  = is_gc_version_compatible( $requires_gc );
			$tested_gc      = ( empty( $plugin['tested'] ) || version_compare( get_bloginfo( 'version' ), $plugin['tested'], '<=' ) );

			$action_links = array();

			if ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) {
				$status = install_plugin_install_status( $plugin );

				switch ( $status['status'] ) {
                    case 'install': 
						if ( $status['url'] ) {
							if ( $compatible_php && $compatible_gc ) {
                                 $action_links[] = sprintf(
                                    '<a class="install-now button" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
                                    esc_attr( $plugin['slug'] ),
                                    esc_url( $status['url'] ),
                                    /* translators: %s: Plugin name and version. */
                                    esc_attr( sprintf( _x( '立即安装%s', 'plugin' ), $name ) ),
                                    esc_attr( $name ),
                                    __( '立即安装' )
                                );
							} else {
								$action_links[] = sprintf(
									'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
									_x( '未能安装', 'plugin' )
								);
							}
						}
						break;

					case 'update_available': 
						if ( $status['url'] ) {
							if ( $compatible_php && $compatible_gc ) {
								$action_links[] = sprintf(
									'<a class="update-now button aria-button-if-js" data-plugin="%s" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
									esc_attr( $status['file'] ),
									esc_attr( $plugin['slug'] ),
									esc_url( $status['url'] ),
									/* translators: %s: Plugin name and version. */
									esc_attr( sprintf( _x( '立即更新 %s', 'plugin' ), $name ) ),
									esc_attr( $name ),
									__( '立即更新' )
								);
							} else {
								$action_links[] = sprintf(
									'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
									_x( '未能更新', 'plugin' )
								);
							}
						}
						break;

					case 'latest_installed':
					case 'newer_installed':
						if ( is_plugin_active( $status['file'] ) ) {
							$action_links[] = sprintf(
								'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
								_x( '已启用', 'plugin' )
							);
						} elseif ( current_user_can( 'activate_plugin', $status['file'] ) ) {
							$button_text = __( '启用' );
							/* translators: %s: Plugin name. */
							$button_label = _x( '启用%s', 'plugin' );
							$activate_url = add_query_arg(
								array(
									'_gcnonce' => gc_create_nonce( 'activate-plugin_' . $status['file'] ),
									'action'   => 'activate',
									'plugin'   => $status['file'],
								),
								network_admin_url( 'plugins.php' )
							);

							if ( is_network_admin() ) {
								$button_text = __( '在站点网络中启用' );
								/* translators: %s: Plugin name. */
								$button_label = _x( '在站点网络中启用%s', 'plugin' );
								$activate_url = add_query_arg( array( 'networkwide' => 1 ), $activate_url );
							}

							$action_links[] = sprintf(
								'<a href="%1$s" class="button activate-now" aria-label="%2$s">%3$s</a>',
								esc_url( $activate_url ),
								esc_attr( sprintf( $button_label, $plugin['name'] ) ),
								$button_text
							);
						} else {
							$action_links[] = sprintf(
								'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
								_x( '已安装', 'plugin' )
							);
						}
						break;
				}
			}
            
           if (  isset( $plugin['donate_link'] ) && ! empty($plugin['donate_link']) ) {
                $action_links[] = sprintf(
                    '<a href="%s" target="_blank" aria-label="%s" data-title="%s">%s</a>',
                    esc_url( $plugin['donate_link'] ),
                    /* translators: %s: Plugin name and version. */
                    esc_attr( sprintf( __( '关于%s专业版' ), $name ) ),
                    esc_attr( $name ),
                    __( '获取专业版 &#187;' )
                );
            }

			$details_link = self_admin_url(
				'plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin['slug'] .
				'&amp;TB_iframe=true&amp;width=600&amp;height=550'
			);

			if ( ! empty( $plugin['icons']['svg'] ) ) {
				$plugin_icon_url = $plugin['icons']['svg'];
			} elseif ( ! empty( $plugin['icons']['2x'] ) ) {
				$plugin_icon_url = $plugin['icons']['2x'];
			} elseif ( ! empty( $plugin['icons']['1x'] ) ) {
				$plugin_icon_url = $plugin['icons']['1x'];
			} else {
				$plugin_icon_url = $plugin['icons']['default'];
			}

			/**
			 * Filters the install action links for a plugin.
			 *
			 * @since 2.7.0
			 *
			 * @param string[] $action_links An array of plugin action links. Defaults are links to Details and Install Now.
			 * @param array    $plugin       The plugin currently being listed.
			 */
			$action_links = apply_filters( 'plugin_install_action_links', $action_links, $plugin );

			$last_updated_timestamp = strtotime( $plugin['last_updated'] );
			?>
		<div class="plugin-card plugin-card-<?php echo sanitize_html_class( $plugin['slug'] ); ?>">
			<?php
			if ( ! $compatible_php || ! $compatible_gc ) {
				echo '<div class="notice inline notice-error notice-alt"><p>';
				if ( ! $compatible_php && ! $compatible_gc ) {
					_e( '此插件不能与您的GeChiUI和PHP版本相兼容。' );
					if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
						printf(
							/* translators: 1: URL to GeChiUI Updates screen, 2: URL to Update PHP page. */
							' ' . __( '<a href="%1$s">请更新GeChiUI</a>，并<a href="%2$s">查阅如何更新PHP</a>。' ),
							self_admin_url( 'update-core.php' ),
							esc_url( gc_get_update_php_url() )
						);
						gc_update_php_annotation( '</p><p><em>', '</em>' );
					} elseif ( current_user_can( 'update_core' ) ) {
						printf(
							/* translators: %s: URL to GeChiUI Updates screen. */
							' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
							self_admin_url( 'update-core.php' )
						);
					} elseif ( current_user_can( 'update_php' ) ) {
						printf(
							/* translators: %s: URL to Update PHP page. */
							' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
							esc_url( gc_get_update_php_url() )
						);
						gc_update_php_annotation( '</p><p><em>', '</em>' );
					}
				} elseif ( ! $compatible_gc ) {
					_e( '此插件不能与您的GeChiUI版本相兼容。' );
					if ( current_user_can( 'update_core' ) ) {
						printf(
							/* translators: %s: URL to GeChiUI Updates screen. */
							' ' . __( '<a href="%s">请更新GeChiUI</a>。' ),
							self_admin_url( 'update-core.php' )
						);
					}
				} elseif ( ! $compatible_php ) {
					_e( '此插件不能与您的PHP版本相兼容。' );
					if ( current_user_can( 'update_php' ) ) {
						printf(
							/* translators: %s: URL to Update PHP page. */
							' ' . __( '<a href="%s">查阅如何更新PHP</a>。' ),
							esc_url( gc_get_update_php_url() )
						);
						gc_update_php_annotation( '</p><p><em>', '</em>' );
					}
				}
				echo '</p></div>';
			}
			?>
			<div class="plugin-card-top">
				<div class="name column-name">
					<h3>
						<a href="<?php echo esc_url( $details_link ); ?>" class="thickbox open-plugin-details-modal">
						<?php echo $title; ?>
						</a>
					</h3>
				</div>
				<div class="action-links">
					<?php
					if ( $action_links ) {
						echo '<ul class="plugin-action-buttons"><li>' . implode( '</li><li>', $action_links ) . '</li></ul>';
					}
					?>
				</div>
				<div class="desc column-description">
					<p><?php echo $description; ?></p>
				</div>
			</div>
			<div class="plugin-card-bottom">
				<div class="vers column-rating">
					<span class="num-ratings" aria-hidden="true"><?php _e( '版本：' ); ?><?php echo $version; ?></span>
				</div>
				<div class="column-updated">
					<strong><?php _e( '最近更新：' ); ?></strong>
					<?php
						/* translators: %s: Human-readable time difference. */
						printf( __( '%s前' ), human_time_diff( $last_updated_timestamp ) );
					?>
				</div>
				<div class="column-downloaded">
				</div>
				<div class="column-compatibility">
					<?php
					if ( ! $tested_gc ) {
						echo '<span class="compatibility-untested">' . __( '未在您的GeChiUI版本中测试' ) . '</span>';
					} elseif ( ! $compatible_gc ) {
						echo '<span class="compatibility-incompatible">' . __( '该插件<strong>不兼容</strong>于您当前使用的GeChiUI版本' ) . '</span>';
					} else {
						echo '<span class="compatibility-compatible">' . __( '该插件<strong>兼容</strong>于您当前使用的GeChiUI版本' ) . '</span>';
					}
					?>
				</div>
			</div>
		</div>
			<?php
		}

		// Close off the group divs of the last one.
		if ( ! empty( $group ) ) {
			echo '</div></div>';
		}
	}
}
