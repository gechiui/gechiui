<?php
/**
 * Upgrader API: Theme_Installer_Skin class
 *
 * @package GeChiUI
 * @subpackage Upgrader
 *
 */

/**
 * Theme Installer Skin for the GeChiUI Theme Installer.
 *
 *
 *
 *
 * @see GC_Upgrader_Skin
 */
class Theme_Installer_Skin extends GC_Upgrader_Skin {
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
			'theme'     => '',
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
	 * Action to perform before installing a theme.
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
	 * Hides the `process_failed` error when updating a theme by uploading a zip file.
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
	 * Action to perform following a single theme install.
	 *
	 */
	public function after() {
		if ( $this->do_overwrite() ) {
			return;
		}

		if ( empty( $this->upgrader->result['destination_name'] ) ) {
			return;
		}

		$theme_info = $this->upgrader->theme_info();
		if ( empty( $theme_info ) ) {
			return;
		}

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

		$install_actions = array();

		if ( current_user_can( 'edit_theme_options' ) && current_user_can( 'customize' ) ) {
			$customize_url = add_query_arg(
				array(
					'theme'  => urlencode( $stylesheet ),
					'return' => urlencode( admin_url( 'web' === $this->type ? 'theme-install.php' : 'themes.php' ) ),
				),
				admin_url( 'customize.php' )
			);

			$install_actions['preview'] = sprintf(
				'<a href="%s" class="hide-if-no-customize load-customize">' .
				'<span aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
				esc_url( $customize_url ),
				__( '实时预览' ),
				/* translators: %s: Theme name. */
				sprintf( __( '实时预览“%s”' ), $name )
			);
		}

		$install_actions['activate'] = sprintf(
			'<a href="%s" class="activatelink">' .
			'<span aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
			esc_url( $activate_link ),
			__( '启用' ),
			/* translators: %s: Theme name. */
			sprintf( _x( '启用“%s”', 'theme' ), $name )
		);

		if ( is_network_admin() && current_user_can( 'manage_network_themes' ) ) {
			$install_actions['network_enable'] = sprintf(
				'<a href="%s" target="_parent">%s</a>',
				esc_url( gc_nonce_url( 'themes.php?action=enable&amp;theme=' . urlencode( $stylesheet ), 'enable-theme_' . $stylesheet ) ),
				__( '在站点网络中启用' )
			);
		}

		if ( 'web' === $this->type ) {
			$install_actions['themes_page'] = sprintf(
				'<a href="%s" target="_parent">%s</a>',
				self_admin_url( 'theme-install.php' ),
				__( '转到“主题安装器”页面' )
			);
		} elseif ( current_user_can( 'switch_themes' ) || current_user_can( 'edit_theme_options' ) ) {
			$install_actions['themes_page'] = sprintf(
				'<a href="%s" target="_parent">%s</a>',
				self_admin_url( 'themes.php' ),
				__( '转到“主题”页面' )
			);
		}

		if ( ! $this->result || is_gc_error( $this->result ) || is_network_admin() || ! current_user_can( 'switch_themes' ) ) {
			unset( $install_actions['activate'], $install_actions['preview'] );
		} elseif ( get_option( 'template' ) === $stylesheet ) {
			unset( $install_actions['activate'] );
		}

		/**
		 * Filters the list of action links available following a single theme installation.
		 *
		 *
		 * @param string[] $install_actions Array of theme action links.
		 * @param object   $api             Object containing www.GeChiUI.com API theme data.
		 * @param string   $stylesheet      Theme directory name.
		 * @param GC_Theme $theme_info      Theme object.
		 */
		$install_actions = apply_filters( 'install_theme_complete_actions', $install_actions, $this->api, $stylesheet, $theme_info );
		if ( ! empty( $install_actions ) ) {
			$this->feedback( implode( ' | ', (array) $install_actions ) );
		}
	}

	/**
	 * Check if the theme can be overwritten and output the HTML for overwriting a theme on upload.
	 *
	 *
	 * @return bool Whether the theme can be overwritten and HTML was outputted.
	 */
	private function do_overwrite() {
		if ( 'upload' !== $this->type || ! is_gc_error( $this->result ) || 'folder_exists' !== $this->result->get_error_code() ) {
			return false;
		}

		$folder = $this->result->get_error_data( 'folder_exists' );
		$folder = rtrim( $folder, '/' );

		$current_theme_data = false;
		$all_themes         = gc_get_themes( array( 'errors' => null ) );

		foreach ( $all_themes as $theme ) {
			$stylesheet_dir = gc_normalize_path( $theme->get_stylesheet_directory() );

			if ( rtrim( $stylesheet_dir, '/' ) !== $folder ) {
				continue;
			}

			$current_theme_data = $theme;
		}

		$new_theme_data = $this->upgrader->new_theme_data;

		if ( ! $current_theme_data || ! $new_theme_data ) {
			return false;
		}

		echo '<h2 class="update-from-upload-heading">' . esc_html__( '该主题已安装。' ) . '</h2>';

		// Check errors for active theme.
		if ( is_gc_error( $current_theme_data->errors() ) ) {
			$this->feedback( 'current_theme_has_errors', $current_theme_data->errors()->get_error_message() );
		}

		$this->is_downgrading = version_compare( $current_theme_data['Version'], $new_theme_data['Version'], '>' );

		$is_invalid_parent = false;
		if ( ! empty( $new_theme_data['Template'] ) ) {
			$is_invalid_parent = ! in_array( $new_theme_data['Template'], array_keys( $all_themes ), true );
		}

		$rows = array(
			'Name'        => __( '主题名称' ),
			'Version'     => __( '版本' ),
			'Author'      => __( '作者' ),
			'RequiresGC'  => __( '所需的GeChiUI版本' ),
			'RequiresPHP' => __( '所需的PHP版本' ),
			'Template'    => __( '父主题' ),
		);

		$table  = '<table class="update-from-upload-comparison"><tbody>';
		$table .= '<tr><th></th><th>' . esc_html_x( '启用', 'theme' ) . '</th><th>' . esc_html_x( '主题已上传', 'theme' ) . '</th></tr>';

		$is_same_theme = true; // Let's consider only these rows.

		foreach ( $rows as $field => $label ) {
			$old_value = $current_theme_data->display( $field, false );
			$old_value = $old_value ? (string) $old_value : '-';

			$new_value = ! empty( $new_theme_data[ $field ] ) ? (string) $new_theme_data[ $field ] : '-';

			if ( $old_value === $new_value && '-' === $new_value && 'Template' === $field ) {
				continue;
			}

			$is_same_theme = $is_same_theme && ( $old_value === $new_value );

			$diff_field     = ( 'Version' !== $field && $new_value !== $old_value );
			$diff_version   = ( 'Version' === $field && $this->is_downgrading );
			$invalid_parent = false;

			if ( 'Template' === $field && $is_invalid_parent ) {
				$invalid_parent = true;
				$new_value     .= ' ' . __( '（未找到）' );
			}

			$table .= '<tr><td class="name-label">' . $label . '</td><td>' . gc_strip_all_tags( $old_value ) . '</td>';
			$table .= ( $diff_field || $diff_version || $invalid_parent ) ? '<td class="warning">' : '<td>';
			$table .= gc_strip_all_tags( $new_value ) . '</td></tr>';
		}

		$table .= '</tbody></table>';

		/**
		 * Filters the compare table output for overwriting a theme package on upload.
		 *
		 *
		 * @param string   $table              The output table with Name, Version, Author, RequiresGC, and RequiresPHP info.
		 * @param GC_Theme $current_theme_data Active theme data.
		 * @param array    $new_theme_data     Array with uploaded theme data.
		 */
		echo apply_filters( 'install_theme_overwrite_comparison', $table, $current_theme_data, $new_theme_data );

		$install_actions = array();
		$can_update      = true;

		$blocked_message  = '<p>' . esc_html__( '主题无法更新，原因如下：' ) . '</p>';
		$blocked_message .= '<ul class="ul-disc">';

		$requires_php = isset( $new_theme_data['RequiresPHP'] ) ? $new_theme_data['RequiresPHP'] : null;
		$requires_gc  = isset( $new_theme_data['RequiresGC'] ) ? $new_theme_data['RequiresGC'] : null;

		if ( ! is_php_version_compatible( $requires_php ) ) {
			$error = sprintf(
				/* translators: 1: Current PHP version, 2: Version required by the uploaded theme. */
				__( '您的服务器PHP版本为%1$s，然而上传的主题要求版本为%2$s。' ),
				phpversion(),
				$requires_php
			);

			$blocked_message .= '<li>' . esc_html( $error ) . '</li>';
			$can_update       = false;
		}

		if ( ! is_gc_version_compatible( $requires_gc ) ) {
			$error = sprintf(
				/* translators: 1: Current GeChiUI version, 2: Version required by the uploaded theme. */
				__( '当前GeChiUI版本为%1$s，但是该上传主题要求版本为%2$s。' ),
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
					__( '您正在上传旧版本的活动主题。您可以继续安装旧版本，但请确保<a href="%s">备份数据库和文件</a>。' ),
					__( 'https://www.gechiui.com/support/gechiui-backups/' )
				);
			} else {
				$warning = sprintf(
					/* translators: %s: Documentation URL. */
					__( '正在升级一款主题。请确保事先<a href="%s">备份你的数据库和文件</a>。' ),
					__( 'https://www.gechiui.com/support/gechiui-backups/' )
				);
			}

			echo '<p class="update-from-upload-notice">' . $warning . '</p>';

			$overwrite = $this->is_downgrading ? 'downgrade-theme' : 'update-theme';

			$install_actions['overwrite_theme'] = sprintf(
				'<a class="button button-primary update-from-upload-overwrite" href="%s" target="_parent">%s</a>',
				gc_nonce_url( add_query_arg( 'overwrite', $overwrite, $this->url ), 'theme-upload' ),
				_x( '已上传并启用', 'theme' )
			);
		} else {
			echo $blocked_message;
		}

		$cancel_url = add_query_arg( 'action', 'upload-theme-cancel-overwrite', $this->url );

		$install_actions['themes_page'] = sprintf(
			'<a class="button" href="%s" target="_parent">%s</a>',
			gc_nonce_url( $cancel_url, 'theme-upload-cancel-overwrite' ),
			__( '取消并返回' )
		);

		/**
		 * Filters the list of action links available following a single theme installation failure
		 * when overwriting is allowed.
		 *
		 *
		 * @param string[] $install_actions Array of theme action links.
		 * @param object   $api             Object containing www.GeChiUI.com API theme data.
		 * @param array    $new_theme_data  Array with uploaded theme data.
		 */
		$install_actions = apply_filters( 'install_theme_overwrite_actions', $install_actions, $this->api, $new_theme_data );

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
