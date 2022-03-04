<?php
/**
 * Upgrader API: Bulk_Plugin_Upgrader_Skin class
 *
 * @package GeChiUI
 * @subpackage Upgrader
 *
 */

/**
 * Bulk Plugin Upgrader Skin for GeChiUI Plugin Upgrades.
 *
 *
 *
 *
 * @see Bulk_Upgrader_Skin
 */
class Bulk_Plugin_Upgrader_Skin extends Bulk_Upgrader_Skin {
	public $plugin_info = array(); // Plugin_Upgrader::bulk_upgrade() will fill this in.

	public function add_strings() {
		parent::add_strings();
		/* translators: 1: Plugin name, 2: Number of the plugin, 3: Total number of plugins being updated. */
		$this->upgrader->strings['skin_before_update_header'] = __( '正在升级%1$s插件（%2$d/%3$d）' );
	}

	/**
	 * @param string $title
	 */
	public function before( $title = '' ) {
		parent::before( $this->plugin_info['Title'] );
	}

	/**
	 * @param string $title
	 */
	public function after( $title = '' ) {
		parent::after( $this->plugin_info['Title'] );
		$this->decrement_update_count( 'plugin' );
	}

	/**
	 */
	public function bulk_footer() {
		parent::bulk_footer();

		$update_actions = array(
			'plugins_page' => sprintf(
				'<a href="%s" target="_parent">%s</a>',
				self_admin_url( 'plugins.php' ),
				__( '转到“插件”页面' )
			),
			'updates_page' => sprintf(
				'<a href="%s" target="_parent">%s</a>',
				self_admin_url( 'update-core.php' ),
				__( '转到“GeChiUI更新”页面' )
			),
		);

		if ( ! current_user_can( 'activate_plugins' ) ) {
			unset( $update_actions['plugins_page'] );
		}

		/**
		 * Filters the list of action links available following bulk plugin updates.
		 *
		 *
		 * @param string[] $update_actions Array of plugin action links.
		 * @param array    $plugin_info    Array of information for the last-updated plugin.
		 */
		$update_actions = apply_filters( 'update_bulk_plugins_complete_actions', $update_actions, $this->plugin_info );

		if ( ! empty( $update_actions ) ) {
			$this->feedback( implode( ' | ', (array) $update_actions ) );
		}
	}
}
