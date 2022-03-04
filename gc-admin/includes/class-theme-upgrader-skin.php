<?php
/**
 * Upgrader API: Theme_Upgrader_Skin class
 *
 * @package GeChiUI
 * @subpackage Upgrader
 *
 */

/**
 * Theme Upgrader Skin for GeChiUI Theme Upgrades.
 *
 *
 *
 *
 * @see GC_Upgrader_Skin
 */
class Theme_Upgrader_Skin extends GC_Upgrader_Skin {

	/**
	 * Holds the theme slug in the Theme Directory.
	 *
	 *
	 * @var string
	 */
	public $theme = '';

	/**
	 * Constructor.
	 *
	 * Sets up the theme upgrader skin.
	 *
	 *
	 * @param array $args Optional. The theme upgrader skin arguments to
	 *                    override default options. Default empty array.
	 */
	public function __construct( $args = array() ) {
		$defaults = array(
			'url'   => '',
			'theme' => '',
			'nonce' => '',
			'title' => __( '升级主题' ),
		);
		$args     = gc_parse_args( $args, $defaults );

		$this->theme = $args['theme'];

		parent::__construct( $args );
	}

	/**
	 * Action to perform following a single theme update.
	 *
	 */
	public function after() {
		$this->decrement_update_count( 'theme' );

		$update_actions = array();
		$theme_info     = $this->upgrader->theme_info();
		if ( $theme_info ) {
			$name       = $theme_info->display( 'Name' );
			$stylesheet = $this->upgrader->result['destination_name'];
			$template   = $theme_info->get_template();

			$activate_link = add_query_arg(
				array(
					'action'     => 'activate',
					'template'   => urlencode( $template ),
					'stylesheet' => urlencode( $stylesheet ),
				),
				admin_url( 'themes.php' )
			);
			$activate_link = gc_nonce_url( $activate_link, 'switch-theme_' . $stylesheet );

			$customize_url = add_query_arg(
				array(
					'theme'  => urlencode( $stylesheet ),
					'return' => urlencode( admin_url( 'themes.php' ) ),
				),
				admin_url( 'customize.php' )
			);

			if ( get_stylesheet() === $stylesheet ) {
				if ( current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
					$update_actions['preview'] = sprintf(
						'<a href="%s" class="hide-if-no-customize load-customize">' .
						'<span aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
						esc_url( $customize_url ),
						__( '自定义' ),
						/* translators: %s: Theme name. */
						sprintf( __( '自定义“%s”' ), $name )
					);
				}
			} elseif ( current_user_can( 'switch_themes' ) ) {
				if ( current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
					$update_actions['preview'] = sprintf(
						'<a href="%s" class="hide-if-no-customize load-customize">' .
						'<span aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
						esc_url( $customize_url ),
						__( '实时预览' ),
						/* translators: %s: Theme name. */
						sprintf( __( '实时预览“%s”' ), $name )
					);
				}

				$update_actions['activate'] = sprintf(
					'<a href="%s" class="activatelink">' .
					'<span aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
					esc_url( $activate_link ),
					__( '启用' ),
					/* translators: %s: Theme name. */
					sprintf( _x( '启用“%s”', 'theme' ), $name )
				);
			}

			if ( ! $this->result || is_gc_error( $this->result ) || is_network_admin() ) {
				unset( $update_actions['preview'], $update_actions['activate'] );
			}
		}

		$update_actions['themes_page'] = sprintf(
			'<a href="%s" target="_parent">%s</a>',
			self_admin_url( 'themes.php' ),
			__( '转到“主题”页面' )
		);

		/**
		 * Filters the list of action links available following a single theme update.
		 *
		 *
		 * @param string[] $update_actions Array of theme action links.
		 * @param string   $theme          Theme directory name.
		 */
		$update_actions = apply_filters( 'update_theme_complete_actions', $update_actions, $this->theme );

		if ( ! empty( $update_actions ) ) {
			$this->feedback( implode( ' | ', (array) $update_actions ) );
		}
	}
}
