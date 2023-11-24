<?php
/**
 * Press This Display and Handler.
 *
 * @package GeChiUI
 * @subpackage Press_This
 */

define( 'IFRAME_REQUEST', true );

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

function gc_load_press_this() {
	$plugin_slug = 'press-this';
	$plugin_file = 'press-this/press-this-plugin.php';

	if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( get_post_type_object( 'post' )->cap->create_posts ) ) {
		gc_die(
			__( '抱歉，您不能为此用户创建文章。' ),
			__( '您需要更高级别的权限。' ),
			403
		);
	} elseif ( is_plugin_active( $plugin_file ) ) {
		include GC_PLUGIN_DIR . '/press-this/class-gc-press-this-plugin.php';
		$gc_press_this = new GC_Press_This_Plugin();
		$gc_press_this->html();
	} elseif ( current_user_can( 'activate_plugins' ) ) {
		if ( file_exists( GC_PLUGIN_DIR . '/' . $plugin_file ) ) {
			$url    = gc_nonce_url(
				add_query_arg(
					array(
						'action' => 'activate',
						'plugin' => $plugin_file,
						'from'   => 'press-this',
					),
					admin_url( 'plugins.php' )
				),
				'activate-plugin_' . $plugin_file
			);
			$action = sprintf(
				'<a href="%1$s" aria-label="%2$s">%2$s</a>',
				esc_url( $url ),
				__( '启用“快速发布”' )
			);
		} else {
			if ( is_main_site() ) {
				$url    = gc_nonce_url(
					add_query_arg(
						array(
							'action' => 'install-plugin',
							'plugin' => $plugin_slug,
							'from'   => 'press-this',
						),
						self_admin_url( 'update.php' )
					),
					'install-plugin_' . $plugin_slug
				);
				$action = sprintf(
					'<a href="%1$s" class="install-now" data-slug="%2$s" data-name="%2$s" aria-label="%3$s">%3$s</a>',
					esc_url( $url ),
					esc_attr( $plugin_slug ),
					__( '立即安装' )
				);
			} else {
				$action = sprintf(
					/* translators: %s: URL to Press This bookmarklet on the main site. */
					__( '“快速发布”并未安装，请从<a href="%s">主系统</a>安装“按这里”。' ),
					get_admin_url( get_current_network_id(), 'press-this.php' )
				);
			}
		}
		gc_die(
			__( '需要安装“快速发布”插件。' ) . '<br />' . $action,
			__( '需要安装' ),
			200
		);
	} else {
		gc_die(
			__( '“快速发布”不可用，请联系您的系统管理员。' ),
			__( '需要安装' ),
			200
		);
	}
}

gc_load_press_this();
