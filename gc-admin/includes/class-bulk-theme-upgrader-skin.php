<?php
/**
 * Upgrader API: Bulk_Plugin_Upgrader_Skin class
 *
 * @package GeChiUI
 * @subpackage Upgrader
 *
 */

/**
 * Bulk Theme Upgrader Skin for GeChiUI Theme Upgrades.
 *
 *
 *
 *
 * @see Bulk_Upgrader_Skin
 */
class Bulk_Theme_Upgrader_Skin extends Bulk_Upgrader_Skin {
	public $theme_info = array(); // Theme_Upgrader::bulk_upgrade() will fill this in.

	public function add_strings() {
		parent::add_strings();
		/* translators: 1: Theme name, 2: Number of the theme, 3: Total number of themes being updated. */
		$this->upgrader->strings['skin_before_update_header'] = __( '正在更新主题%1$s（%2$d/%3$d）' );
	}

	/**
	 * @param string $title
	 */
	public function before( $title = '' ) {
		parent::before( $this->theme_info->display( 'Name' ) );
	}

	/**
	 * @param string $title
	 */
	public function after( $title = '' ) {
		parent::after( $this->theme_info->display( 'Name' ) );
		$this->decrement_update_count( 'theme' );
	}

	/**
	 */
	public function bulk_footer() {
		parent::bulk_footer();

		$update_actions = array(
			'themes_page'  => sprintf(
				'<a href="%s" target="_parent">%s</a>',
				self_admin_url( 'themes.php' ),
				__( '转到“主题”页面' )
			),
			'updates_page' => sprintf(
				'<a href="%s" target="_parent">%s</a>',
				self_admin_url( 'update-core.php' ),
				__( '转到“GeChiUI更新”页面' )
			),
		);

		if ( ! current_user_can( 'switch_themes' ) && ! current_user_can( 'edit_theme_options' ) ) {
			unset( $update_actions['themes_page'] );
		}

		/**
		 * Filters the list of action links available following bulk theme updates.
		 *
		 *
		 * @param string[] $update_actions Array of theme action links.
		 * @param GC_Theme $theme_info     Theme object for the last-updated theme.
		 */
		$update_actions = apply_filters( 'update_bulk_theme_complete_actions', $update_actions, $this->theme_info );

		if ( ! empty( $update_actions ) ) {
			$this->feedback( implode( ' | ', (array) $update_actions ) );
		}
	}
}
