<?php
/**
 * Upgrader API: Plugin_Installer_Skin class
 *
 * @package GeChiUI
 * @subpackage Upgrader
 *
 */

/**
 * Plugin Installer Skin for GeChiUI Plugin Installer.
 *
 *
 *
 *
 * @see GC_Upgrader_Skin
 */
class Plugin_Installer_Skin extends GC_Upgrader_Skin {
	public $api;
	public $type;
	public $url;
	public $overwrite;

	private $is_downgrading = false;

	/**
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		$defaults = array(
			'type'      => 'web',
			'url'       => '',
			'plugin'    => '',
			'nonce'     => '',
			'title'     => '',
			'overwrite' => '',
		);
		$args     = gc_parse_args( $args, $defaults );

		$this->type      = $args['type'];
		$this->url       = $args['url'];
		$this->api       = isset( $args['api'] ) ? $args['api'] : array();
		$this->overwrite = $args['overwrite'];

		parent::__construct( $args );
	}

	/**
	 * Action to perform before installing a plugin.
	 *
	 */
	public function before() {
		if ( ! empty( $this->api ) ) {
			$this->upgrader->strings['process_success'] = sprintf(
				$this->upgrader->strings['process_success_specific'],
				$this->api->name,
				$this->api->version
			);
		}
	}

	/**
	 * Hides the `process_failed` error when updating a plugin by uploading a zip file.
	 *
	 *
	 * @param GC_Error $gc_error GC_Error object.
	 * @return bool
	 */
	public function hide_process_failed( $gc_error ) {
		if (
			'upload' === $this->type &&
			'' === $this->overwrite &&
			$gc_error->get_error_code() === 'folder_exists'
		) {
			return true;
		}

		return false;
	}

	/**
	 * Action to perform following a plugin install.
	 *
	 */
	public function after() {
		// Check if the plugin can be overwritten and output the HTML.
		if ( $this->do_overwrite() ) {
			return;
		}

		$plugin_file = $this->upgrader->plugin_info();

		$install_actions = array();

		$from = isset( $_GET['from'] ) ? gc_unslash( $_GET['from'] ) : 'plugins';

		if ( 'import' === $from ) {
			$install_actions['activate_plugin'] = sprintf(
				'<a class="button button-primary" href="%s" target="_parent">%s</a>',
				gc_nonce_url( 'plugins.php?action=activate&amp;from=import&amp;plugin=' . urlencode( $plugin_file ), 'activate-plugin_' . $plugin_file ),
				__( '启用插件并运行导入工具' )
			);
		} elseif ( 'press-this' === $from ) {
			$install_actions['activate_plugin'] = sprintf(
				'<a class="button button-primary" href="%s" target="_parent">%s</a>',
				gc_nonce_url( 'plugins.php?action=activate&amp;from=press-this&amp;plugin=' . urlencode( $plugin_file ), 'activate-plugin_' . $plugin_file ),
				__( '启用插件并转到“快速发布”页面' )
			);
		} else {
			$install_actions['activate_plugin'] = sprintf(
				'<a class="button button-primary" href="%s" target="_parent">%s</a>',
				gc_nonce_url( 'plugins.php?action=activate&amp;plugin=' . urlencode( $plugin_file ), 'activate-plugin_' . $plugin_file ),
				__( '启用插件' )
			);
		}

		if ( is_multisite() && current_user_can( 'manage_network_plugins' ) ) {
			$install_actions['network_activate'] = sprintf(
				'<a class="button button-primary" href="%s" target="_parent">%s</a>',
				gc_nonce_url( 'plugins.php?action=activate&amp;networkwide=1&amp;plugin=' . urlencode( $plugin_file ), 'activate-plugin_' . $plugin_file ),
				__( '在站点网络中启用' )
			);
			unset( $install_actions['activate_plugin'] );
		}

		if ( 'import' === $from ) {
			$install_actions['importers_page'] = sprintf(
				'<a href="%s" target="_parent">%s</a>',
				admin_url( 'import.php' ),
				__( '转到“导入”页面' )
			);
		} elseif ( 'web' === $this->type ) {
			$install_actions['plugins_page'] = sprintf(
				'<a href="%s" target="_parent">%s</a>',
				self_admin_url( 'plugin-install.php' ),
				__( '转到“插件安装器”页面' )
			);
		} elseif ( 'upload' === $this->type && 'plugins' === $from ) {
			$install_actions['plugins_page'] = sprintf(
				'<a href="%s">%s</a>',
				self_admin_url( 'plugin-install.php' ),
				__( '转到“插件安装器”页面' )
			);
		} else {
			$install_actions['plugins_page'] = sprintf(
				'<a href="%s" target="_parent">%s</a>',
				self_admin_url( 'plugins.php' ),
				__( '转到“插件”页面' )
			);
		}

		if ( ! $this->result || is_gc_error( $this->result ) ) {
			unset( $install_actions['activate_plugin'], $install_actions['network_activate'] );
		} elseif ( ! current_user_can( 'activate_plugin', $plugin_file ) || is_plugin_active( $plugin_file ) ) {
			unset( $install_actions['activate_plugin'] );
		}

		/**
		 * Filters the list of action links available following a single plugin installation.
		 *
		 *
		 * @param string[] $install_actions Array of plugin action links.
		 * @param object   $api             Object containing www.GeChiUI.com API plugin data. Empty
		 *                                  for non-API installs, such as when a plugin is installed
		 *                                  via upload.
		 * @param string   $plugin_file     Path to the plugin file relative to the plugins directory.
		 */
		$install_actions = apply_filters( 'install_plugin_complete_actions', $install_actions, $this->api, $plugin_file );

		if ( ! empty( $install_actions ) ) {
			$this->feedback( implode( ' ', (array) $install_actions ) );
		}
	}

	/**
	 * Check if the plugin can be overwritten and output the HTML for overwriting a plugin on upload.
	 *
	 *
	 * @return bool Whether the plugin can be overwritten and HTML was outputted.
	 */
	private function do_overwrite() {
		if ( 'upload' !== $this->type || ! is_gc_error( $this->result ) || 'folder_exists' !== $this->result->get_error_code() ) {
			return false;
		}

		$folder = $this->result->get_error_data( 'folder_exists' );
		$folder = ltrim( substr( $folder, strlen( GC_PLUGIN_DIR ) ), '/' );

		$current_plugin_data = false;
		$all_plugins         = get_plugins();

		foreach ( $all_plugins as $plugin => $plugin_data ) {
			if ( strrpos( $plugin, $folder ) !== 0 ) {
				continue;
			}

			$current_plugin_data = $plugin_data;
		}

		$new_plugin_data = $this->upgrader->new_plugin_data;

		if ( ! $current_plugin_data || ! $new_plugin_data ) {
			return false;
		}

		echo '<h2 class="update-from-upload-heading">' . esc_html__( '该插件已安装。' ) . '</h2>';

		$this->is_downgrading = version_compare( $current_plugin_data['Version'], $new_plugin_data['Version'], '>' );

		$rows = array(
			'Name'        => __( '插件名称' ),
			'Version'     => __( '版本' ),
			'Author'      => __( '作者' ),
			'RequiresGC'  => __( '所需的GeChiUI版本' ),
			'RequiresPHP' => __( '所需的PHP版本' ),
		);

		$table  = '<table class="update-from-upload-comparison"><tbody>';
		$table .= '<tr><th></th><th>' . esc_html_x( '当前', 'plugin' ) . '</th>';
		$table .= '<th>' . esc_html_x( '插件已上传', 'plugin' ) . '</th></tr>';

		$is_same_plugin = true; // Let's consider only these rows.

		foreach ( $rows as $field => $label ) {
			$old_value = ! empty( $current_plugin_data[ $field ] ) ? (string) $current_plugin_data[ $field ] : '-';
			$new_value = ! empty( $new_plugin_data[ $field ] ) ? (string) $new_plugin_data[ $field ] : '-';

			$is_same_plugin = $is_same_plugin && ( $old_value === $new_value );

			$diff_field   = ( 'Version' !== $field && $new_value !== $old_value );
			$diff_version = ( 'Version' === $field && $this->is_downgrading );

			$table .= '<tr><td class="name-label">' . $label . '</td><td>' . gc_strip_all_tags( $old_value ) . '</td>';
			$table .= ( $diff_field || $diff_version ) ? '<td class="warning">' : '<td>';
			$table .= gc_strip_all_tags( $new_value ) . '</td></tr>';
		}

		$table .= '</tbody></table>';

		/**
		 * Filters the compare table output for overwriting a plugin package on upload.
		 *
		 *
		 * @param string $table               The output table with Name, Version, Author, RequiresGC, and RequiresPHP info.
		 * @param array  $current_plugin_data Array with current plugin data.
		 * @param array  $new_plugin_data     Array with uploaded plugin data.
		 */
		echo apply_filters( 'install_plugin_overwrite_comparison', $table, $current_plugin_data, $new_plugin_data );

		$install_actions = array();
		$can_update      = true;

		$blocked_message  = '<p>' . esc_html__( '插件无法更新，原因如下：' ) . '</p>';
		$blocked_message .= '<ul class="ul-disc">';

		$requires_php = isset( $new_plugin_data['RequiresPHP'] ) ? $new_plugin_data['RequiresPHP'] : null;
		$requires_gc  = isset( $new_plugin_data['RequiresGC'] ) ? $new_plugin_data['RequiresGC'] : null;

		if ( ! is_php_version_compatible( $requires_php ) ) {
			$error = sprintf(
				/* translators: 1: Current PHP version, 2: Version required by the uploaded plugin. */
				__( '您的服务器PHP版本为%1$s，然而上传的插件要求版本为%2$s。' ),
				phpversion(),
				$requires_php
			);

			$blocked_message .= '<li>' . esc_html( $error ) . '</li>';
			$can_update       = false;
		}

		if ( ! is_gc_version_compatible( $requires_gc ) ) {
			$error = sprintf(
				/* translators: 1: Current GeChiUI version, 2: Version required by the uploaded plugin. */
				__( '您的GeChiUI版本为%1$s，但上传插件的GeChiUI版本要求为%2$s。' ),
				get_bloginfo( 'version' ),
				$requires_gc
			);

			$blocked_message .= '<li>' . esc_html( $error ) . '</li>';
			$can_update       = false;
		}

		$blocked_message .= '</ul>';

		if ( $can_update ) {
			if ( $this->is_downgrading ) {
				$warning = sprintf(
					/* translators: %s: Documentation URL. */
					__( '您正在上传当前插件的旧版本。您可以继续安装旧版本，但请确保已事先<a href="%s">备份您网站的数据库和文件</a>。' ),
					__( 'https://www.gechiui.com/support/gechiui-backups/' )
				);
			} else {
				$warning = sprintf(
					/* translators: %s: Documentation URL. */
					__( '您即将升级插件。请确保已事先<a href="%s">备份您的数据库与文件</a>。' ),
					__( 'https://www.gechiui.com/support/gechiui-backups/' )
				);
			}

			echo '<p class="update-from-upload-notice">' . $warning . '</p>';

			$overwrite = $this->is_downgrading ? 'downgrade-plugin' : 'update-plugin';

			$install_actions['overwrite_plugin'] = sprintf(
				'<a class="button button-primary update-from-upload-overwrite" href="%s" target="_parent">%s</a>',
				gc_nonce_url( add_query_arg( 'overwrite', $overwrite, $this->url ), 'plugin-upload' ),
				_x( '使用“上传的插件版本”代替“当前的插件版本”。', 'plugin' )
			);
		} else {
			echo $blocked_message;
		}

		$cancel_url = add_query_arg( 'action', 'upload-plugin-cancel-overwrite', $this->url );

		$install_actions['plugins_page'] = sprintf(
			'<a class="button" href="%s">%s</a>',
			gc_nonce_url( $cancel_url, 'plugin-upload-cancel-overwrite' ),
			__( '取消并返回' )
		);

		/**
		 * Filters the list of action links available following a single plugin installation failure
		 * when overwriting is allowed.
		 *
		 *
		 * @param string[] $install_actions Array of plugin action links.
		 * @param object   $api             Object containing www.GeChiUI.com API plugin data.
		 * @param array    $new_plugin_data Array with uploaded plugin data.
		 */
		$install_actions = apply_filters( 'install_plugin_overwrite_actions', $install_actions, $this->api, $new_plugin_data );

		if ( ! empty( $install_actions ) ) {
			printf(
				'<p class="update-from-upload-expired hidden">%s</p>',
				__( '原上传文件已过期。请返回并重新上传该文件。' )
			);
			echo '<p class="update-from-upload-actions">' . implode( ' ', (array) $install_actions ) . '</p>';
		}

		return true;
	}
}
