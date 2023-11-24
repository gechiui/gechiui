<?php
/**
 * Upgrader API: Plugin_Upgrader_Skin class
 *
 * @package GeChiUI
 * @subpackage Upgrader
 */

/**
 * Plugin Upgrader Skin for GeChiUI Plugin Upgrades.
 * Moved to its own file from gc-admin/includes/class-gc-upgrader-skins.php.
 *
 * @see GC_Upgrader_Skin
 */
class Plugin_Upgrader_Skin extends GC_Upgrader_Skin {

	/**
	 * Holds the plugin slug in the Plugin Directory.
	 *
	 *
	 * @var string
	 */
	public $plugin = '';

	/**
	 * Whether the plugin is active.
	 *
	 *
	 * @var bool
	 */
	public $plugin_active = false;

	/**
	 * Whether the plugin is active for the entire network.
	 *
	 *
	 * @var bool
	 */
	public $plugin_network_active = false;

	/**
	 * Constructor.
	 *
	 * Sets up the plugin upgrader skin.
	 *
	 *
	 * @param array $args Optional. The plugin upgrader skin arguments to
	 *                    override default options. Default empty array.
	 */
	public function __construct( $args = array() ) {
		$defaults = array(
			'url'    => '',
			'plugin' => '',
			'nonce'  => '',
			'title'  => __( '升级插件' ),
		);
		$args     = gc_parse_args( $args, $defaults );

		$this->plugin = $args['plugin'];

		$this->plugin_active         = is_plugin_active( $this->plugin );
		$this->plugin_network_active = is_plugin_active_for_network( $this->plugin );

		parent::__construct( $args );
	}

	/**
	 * Performs an action following a single plugin update.
	 *
	 */
	public function after() {
		$this->plugin = $this->upgrader->plugin_info();
		if ( ! empty( $this->plugin ) && ! is_gc_error( $this->result ) && $this->plugin_active ) {
			// Currently used only when JS is off for a single plugin update?
			printf(
				'<iframe title="%s" style="border:0;overflow:hidden" width="100%%" height="170" src="%s"></iframe>',
				esc_attr__( '更新进度' ),
				gc_nonce_url( 'update.php?action=activate-plugin&networkwide=' . $this->plugin_network_active . '&plugin=' . urlencode( $this->plugin ), 'activate-plugin_' . $this->plugin )
			);
		}

		$this->decrement_update_count( 'plugin' );

		$update_actions = array(
			'activate_plugin' => sprintf(
				'<a href="%s" target="_parent">%s</a>',
				gc_nonce_url( 'plugins.php?action=activate&amp;plugin=' . urlencode( $this->plugin ), 'activate-plugin_' . $this->plugin ),
				__( '启用插件' )
			),
			'plugins_page'    => sprintf(
				'<a href="%s" target="_parent">%s</a>',
				self_admin_url( 'plugins.php' ),
				__( '转到“插件”页面' )
			),
		);

		if ( $this->plugin_active || ! $this->result || is_gc_error( $this->result ) || ! current_user_can( 'activate_plugin', $this->plugin ) ) {
			unset( $update_actions['activate_plugin'] );
		}

		/**
		 * Filters the list of action links available following a single plugin update.
		 *
		 * @param string[] $update_actions Array of plugin action links.
		 * @param string   $plugin         Path to the plugin file relative to the plugins directory.
		 */
		$update_actions = apply_filters( 'update_plugin_complete_actions', $update_actions, $this->plugin );

		if ( ! empty( $update_actions ) ) {
			$this->feedback( implode( ' | ', (array) $update_actions ) );
		}
	}
}
