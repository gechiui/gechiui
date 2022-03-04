<?php
/**
 * List Table API: GC_Theme_Install_List_Table class
 *
 * @package GeChiUI
 * @subpackage Administration
 *
 */

/**
 * Core class used to implement displaying themes to install in a list table.
 *
 *
 * @access private
 *
 * @see GC_Themes_List_Table
 */
class GC_Theme_Install_List_Table extends GC_Themes_List_Table {

	public $features = array();

	/**
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can( 'install_themes' );
	}

	/**
	 * @global array  $tabs
	 * @global string $tab
	 * @global int    $paged
	 * @global string $type
	 * @global array  $theme_field_defaults
	 */
	public function prepare_items() {
		require ABSPATH . 'gc-admin/includes/theme-install.php';

		global $tabs, $tab, $paged, $type, $theme_field_defaults;
		gc_reset_vars( array( 'tab' ) );

		$search_terms  = array();
		$search_string = '';
		if ( ! empty( $_REQUEST['s'] ) ) {
			$search_string = strtolower( gc_unslash( $_REQUEST['s'] ) );
			$search_terms  = array_unique( array_filter( array_map( 'trim', explode( ',', $search_string ) ) ) );
		}

		if ( ! empty( $_REQUEST['features'] ) ) {
			$this->features = $_REQUEST['features'];
		}

		$paged = $this->get_pagenum();

		$per_page = 36;

		// These are the tabs which are shown on the page,
		$tabs              = array();
		$tabs['dashboard'] = __( '搜索' );
		if ( 'search' === $tab ) {
			$tabs['search'] = __( '搜索结果' );
		}
		$tabs['upload']   = __( '上传' );
		$tabs['featured'] = _x( '特色', 'themes' );
		//$tabs['popular']  = _x( '热门', 'themes' );
		$tabs['new']     = _x( '最新', 'themes' );
		$tabs['updated'] = _x( '最近更新', 'themes' );

		$nonmenu_tabs = array( 'theme-information' ); // Valid actions to perform which do not have a Menu item.

		/** This filter is documented in gc-admin/theme-install.php */
		$tabs = apply_filters( 'install_themes_tabs', $tabs );

		/**
		 * Filters tabs not associated with a menu item on the Install Themes screen.
		 *
		 *
		 * @param string[] $nonmenu_tabs The tabs that don't have a menu item on
		 *                               the Install Themes screen.
		 */
		$nonmenu_tabs = apply_filters( 'install_themes_nonmenu_tabs', $nonmenu_tabs );

		// If a non-valid menu tab has been selected, And it's not a non-menu action.
		if ( empty( $tab ) || ( ! isset( $tabs[ $tab ] ) && ! in_array( $tab, (array) $nonmenu_tabs, true ) ) ) {
			$tab = key( $tabs );
		}

		$args = array(
			'page'     => $paged,
			'per_page' => $per_page,
			'fields'   => $theme_field_defaults,
		);

		switch ( $tab ) {
			case 'search':
				$type = isset( $_REQUEST['type'] ) ? gc_unslash( $_REQUEST['type'] ) : 'term';
				switch ( $type ) {
					case 'tag':
						$args['tag'] = array_map( 'sanitize_key', $search_terms );
						break;
					case 'term':
						$args['search'] = $search_string;
						break;
					case 'author':
						$args['author'] = $search_string;
						break;
				}

				if ( ! empty( $this->features ) ) {
					$args['tag']      = $this->features;
					$_REQUEST['s']    = implode( ',', $this->features );
					$_REQUEST['type'] = 'tag';
				}

				add_action( 'install_themes_table_header', 'install_theme_search_form', 10, 0 );
				break;

			case 'featured':
				// case 'popular':
			case 'new':
			case 'updated':
				$args['browse'] = $tab;
				break;

			default:
				$args = false;
				break;
		}

		/**
		 * Filters API request arguments for each Install Themes screen tab.
		 *
		 * The dynamic portion of the hook name, `$tab`, refers to the theme install
		 * tab.
		 *
		 * Possible hook names include:
		 *
		 *  - `install_themes_table_api_args_dashboard`
		 *  - `install_themes_table_api_args_featured`
		 *  - `install_themes_table_api_args_new`
		 *  - `install_themes_table_api_args_search`
		 *  - `install_themes_table_api_args_updated`
		 *  - `install_themes_table_api_args_upload`
		 *
		 *
		 * @param array|false $args Theme install API arguments.
		 */
		$args = apply_filters( "install_themes_table_api_args_{$tab}", $args );

		if ( ! $args ) {
			return;
		}

		$api = themes_api( 'query_themes', $args );

		if ( is_gc_error( $api ) ) {
			gc_die( '<p>' . $api->get_error_message() . '</p> <p><a href="#" onclick="document.location.reload(); return false;">' . __( '重试' ) . '</a></p>' );
		}

		$this->items = $api->themes;

		$this->set_pagination_args(
			array(
				'total_items'     => $api->info['results'],
				'per_page'        => $args['per_page'],
				'infinite_scroll' => true,
			)
		);
	}

	/**
	 */
	public function no_items() {
		_e( '没有符合要求的主题。' );
	}

	/**
	 * @global array $tabs
	 * @global string $tab
	 * @return array
	 */
	protected function get_views() {
		global $tabs, $tab;

		$display_tabs = array();
		foreach ( (array) $tabs as $action => $text ) {
			$current_link_attributes                    = ( $action === $tab ) ? ' class="current" aria-current="page"' : '';
			$href                                       = self_admin_url( 'theme-install.php?tab=' . $action );
			$display_tabs[ 'theme-install-' . $action ] = "<a href='$href'$current_link_attributes>$text</a>";
		}

		return $display_tabs;
	}

	/**
	 * Displays the theme install table.
	 *
	 * Overrides the parent display() method to provide a different container.
	 *
	 */
	public function display() {
		gc_nonce_field( 'fetch-list-' . get_class( $this ), '_ajax_fetch_list_nonce' );
		?>
		<div class="tablenav top themes">
			<div class="alignleft actions">
				<?php
				/**
				 * Fires in the Install Themes list table header.
				 *
			
				 */
				do_action( 'install_themes_table_header' );
				?>
			</div>
			<?php $this->pagination( 'top' ); ?>
			<br class="clear" />
		</div>

		<div id="availablethemes">
			<?php $this->display_rows_or_placeholder(); ?>
		</div>

		<?php
		$this->tablenav( 'bottom' );
	}

	/**
	 */
	public function display_rows() {
		$themes = $this->items;
		foreach ( $themes as $theme ) {
			?>
				<div class="available-theme installable-theme">
				<?php
					$this->single_row( $theme );
				?>
				</div>
			<?php
		} // End foreach $theme_names.

		$this->theme_installer();
	}

	/**
	 * Prints a theme from the www.GeChiUI.com API.
	 *
	 *
	 * @global array $themes_allowedtags
	 *
	 * @param stdClass $theme {
	 *     An object that contains theme data returned by the www.GeChiUI.com API.
	 *
	 *     @type string $name           Theme name, e.g. '默认主题-蜂鸟'.
	 *     @type string $slug           Theme slug, e.g. 'defaultbird'.
	 *     @type string $version        Theme version, e.g. '1.1'.
	 *     @type string $author         Theme author username, e.g. 'gechiui'.
	 *     @type string $preview_url    Preview URL, e.g. 'https://2021.gechiui.net/'.
	 *     @type string $screenshot_url Screenshot URL, e.g. 'https://www.gechiui.com/themes/defaultbird/'.
	 *     @type float  $rating         Rating score.
	 *     @type int    $num_ratings    The number of ratings.
	 *     @type string $homepage       Theme homepage, e.g. 'https://www.gechiui.com/themes/defaultbird/'.
	 *     @type string $description    Theme description.
	 *     @type string $download_link  Theme ZIP download URL.
	 * }
	 */
	public function single_row( $theme ) {
		global $themes_allowedtags;

		if ( empty( $theme ) ) {
			return;
		}

		$name   = gc_kses( $theme->name, $themes_allowedtags );
		$author = gc_kses( $theme->author, $themes_allowedtags );

		/* translators: %s: Theme name. */
		$preview_title = sprintf( __( '预览“%s”' ), $name );
		$preview_url   = add_query_arg(
			array(
				'tab'   => 'theme-information',
				'theme' => $theme->slug,
			),
			self_admin_url( 'theme-install.php' )
		);

		$actions = array();

		$install_url = add_query_arg(
			array(
				'action' => 'install-theme',
				'theme'  => $theme->slug,
			),
			self_admin_url( 'update.php' )
		);

		$update_url = add_query_arg(
			array(
				'action' => 'upgrade-theme',
				'theme'  => $theme->slug,
			),
			self_admin_url( 'update.php' )
		);

		$status = $this->_get_theme_status( $theme );

		switch ( $status ) {
			case 'update_available':
				$actions[] = sprintf(
					'<a class="install-now" href="%s" title="%s">%s</a>',
					esc_url( gc_nonce_url( $update_url, 'upgrade-theme_' . $theme->slug ) ),
					/* translators: %s: Theme version. */
					esc_attr( sprintf( __( '更新到%s版本' ), $theme->version ) ),
					__( '更新' )
				);
				break;
			case 'newer_installed':
			case 'latest_installed':
				$actions[] = sprintf(
					'<span class="install-now" title="%s">%s</span>',
					esc_attr__( '当前安装的已经是该主题的最新版本' ),
					_x( '已安装', 'theme' )
				);
				break;
			case 'install':
			default:
				$actions[] = sprintf(
					'<a class="install-now" href="%s" title="%s">%s</a>',
					esc_url( gc_nonce_url( $install_url, 'install-theme_' . $theme->slug ) ),
					/* translators: %s: Theme name. */
					esc_attr( sprintf( _x( '安装%s', 'theme' ), $name ) ),
					__( '立即安装' )
				);
				break;
		}

		$actions[] = sprintf(
			'<a class="install-theme-preview" href="%s" title="%s">%s</a>',
			esc_url( $preview_url ),
			/* translators: %s: Theme name. */
			esc_attr( sprintf( __( '预览%s' ), $name ) ),
			__( '预览' )
		);

		/**
		 * Filters the install action links for a theme in the Install Themes list table.
		 *
		 *
		 * @param string[] $actions An array of theme action links. Defaults are
		 *                          links to Install Now, Preview, and Details.
		 * @param stdClass $theme   An object that contains theme data returned by the
		 *                          www.GeChiUI.com API.
		 */
		$actions = apply_filters( 'theme_install_actions', $actions, $theme );

		?>
		<a class="screenshot install-theme-preview" href="<?php echo esc_url( $preview_url ); ?>" title="<?php echo esc_attr( $preview_title ); ?>">
			<img src="<?php echo esc_url( $theme->screenshot_url ); ?>" width="150" alt="" />
		</a>

		<h3><?php echo $name; ?></h3>
		<div class="theme-author">
		<?php
			/* translators: %s: Theme author. */
			printf( __( '作者：%s' ), $author );
		?>
		</div>

		<div class="action-links">
			<ul>
				<?php foreach ( $actions as $action ) : ?>
					<li><?php echo $action; ?></li>
				<?php endforeach; ?>
				<li class="hide-if-no-js"><a href="#" class="theme-detail"><?php _e( '详情' ); ?></a></li>
			</ul>
		</div>

		<?php
		$this->install_theme_info( $theme );
	}

	/**
	 * Prints the wrapper for the theme installer.
	 */
	public function theme_installer() {
		?>
		<div id="theme-installer" class="gc-full-overlay expanded">
			<div class="gc-full-overlay-sidebar">
				<div class="gc-full-overlay-header">
					<a href="#" class="close-full-overlay button"><?php _e( '关闭' ); ?></a>
					<span class="theme-install"></span>
				</div>
				<div class="gc-full-overlay-sidebar-content">
					<div class="install-theme-info"></div>
				</div>
				<div class="gc-full-overlay-footer">
					<button type="button" class="collapse-sidebar button" aria-expanded="true" aria-label="<?php esc_attr_e( '折叠边栏' ); ?>">
						<span class="collapse-sidebar-arrow"></span>
						<span class="collapse-sidebar-label"><?php _e( '收起' ); ?></span>
					</button>
				</div>
			</div>
			<div class="gc-full-overlay-main"></div>
		</div>
		<?php
	}

	/**
	 * Prints the wrapper for the theme installer with a provided theme's data.
	 * Used to make the theme installer work for no-js.
	 *
	 * @param stdClass $theme A www.GeChiUI.com Theme API object.
	 */
	public function theme_installer_single( $theme ) {
		?>
		<div id="theme-installer" class="gc-full-overlay single-theme">
			<div class="gc-full-overlay-sidebar">
				<?php $this->install_theme_info( $theme ); ?>
			</div>
			<div class="gc-full-overlay-main">
				<iframe src="<?php echo esc_url( $theme->preview_url ); ?>"></iframe>
			</div>
		</div>
		<?php
	}

	/**
	 * Prints the info for a theme (to be used in the theme installer modal).
	 *
	 * @global array $themes_allowedtags
	 *
	 * @param stdClass $theme A www.GeChiUI.com Theme API object.
	 */
	public function install_theme_info( $theme ) {
		global $themes_allowedtags;

		if ( empty( $theme ) ) {
			return;
		}

		$name   = gc_kses( $theme->name, $themes_allowedtags );
		$author = gc_kses( $theme->author, $themes_allowedtags );

		$install_url = add_query_arg(
			array(
				'action' => 'install-theme',
				'theme'  => $theme->slug,
			),
			self_admin_url( 'update.php' )
		);

		$update_url = add_query_arg(
			array(
				'action' => 'upgrade-theme',
				'theme'  => $theme->slug,
			),
			self_admin_url( 'update.php' )
		);

		$status = $this->_get_theme_status( $theme );

		?>
		<div class="install-theme-info">
		<?php
		switch ( $status ) {
			case 'update_available':
				printf(
					'<a class="theme-install button button-primary" href="%s" title="%s">%s</a>',
					esc_url( gc_nonce_url( $update_url, 'upgrade-theme_' . $theme->slug ) ),
					/* translators: %s: Theme version. */
					esc_attr( sprintf( __( '更新到%s版本' ), $theme->version ) ),
					__( '更新' )
				);
				break;
			case 'newer_installed':
			case 'latest_installed':
				printf(
					'<span class="theme-install" title="%s">%s</span>',
					esc_attr__( '当前安装的已经是该主题的最新版本' ),
					_x( '已安装', 'theme' )
				);
				break;
			case 'install':
			default:
				printf(
					'<a class="theme-install button button-primary" href="%s">%s</a>',
					esc_url( gc_nonce_url( $install_url, 'install-theme_' . $theme->slug ) ),
					__( '安装' )
				);
				break;
		}
		?>
			<h3 class="theme-name"><?php echo $name; ?></h3>
			<span class="theme-by">
			<?php
				/* translators: %s: Theme author. */
				printf( __( '作者：%s' ), $author );
			?>
			</span>
			<?php if ( isset( $theme->screenshot_url ) ) : ?>
				<img class="theme-screenshot" src="<?php echo esc_url( $theme->screenshot_url ); ?>" alt="" />
			<?php endif; ?>
			<div class="theme-details">
				<?php
				gc_star_rating(
					array(
						'rating' => $theme->rating,
						'type'   => 'percent',
						'number' => $theme->num_ratings,
					)
				);
				?>
				<div class="theme-version">
					<strong><?php _e( '版本：' ); ?> </strong>
					<?php echo gc_kses( $theme->version, $themes_allowedtags ); ?>
				</div>
				<div class="theme-description">
					<?php echo gc_kses( $theme->description, $themes_allowedtags ); ?>
				</div>
			</div>
			<input class="theme-preview-url" type="hidden" value="<?php echo esc_url( $theme->preview_url ); ?>" />
		</div>
		<?php
	}

	/**
	 * Send required variables to JavaScript land
	 *
	 *
	 * @global string $tab  Current tab within Themes->Install screen
	 * @global string $type Type of search.
	 *
	 * @param array $extra_args Unused.
	 */
	public function _js_vars( $extra_args = array() ) {
		global $tab, $type;
		parent::_js_vars( compact( 'tab', 'type' ) );
	}

	/**
	 * Check to see if the theme is already installed.
	 *
	 *
	 * @param stdClass $theme A www.GeChiUI.com Theme API object.
	 * @return string Theme status.
	 */
	private function _get_theme_status( $theme ) {
		$status = 'install';

		$installed_theme = gc_get_theme( $theme->slug );
		if ( $installed_theme->exists() ) {
			if ( version_compare( $installed_theme->get( 'Version' ), $theme->version, '=' ) ) {
				$status = 'latest_installed';
			} elseif ( version_compare( $installed_theme->get( 'Version' ), $theme->version, '>' ) ) {
				$status = 'newer_installed';
			} else {
				$status = 'update_available';
			}
		}

		return $status;
	}
}
