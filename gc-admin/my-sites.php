<?php
/**
 * My Sites dashboard.
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

require_once __DIR__ . '/admin.php';

if ( ! is_multisite() ) {
	gc_die( __( '未启用多站点支持。' ) );
}

if ( ! current_user_can( 'read' ) ) {
	gc_die( __( '抱歉，您不能访问此页面。' ) );
}

$action = isset( $_POST['action'] ) ? $_POST['action'] : 'splash';

$blogs = get_blogs_of_user( $current_user->ID );

$updated = false;
if ( 'updateblogsettings' === $action && isset( $_POST['primary_blog'] ) ) {
	check_admin_referer( 'update-my-sites' );

	$blog = get_site( (int) $_POST['primary_blog'] );
	if ( $blog && isset( $blog->domain ) ) {
		update_user_meta( $current_user->ID, 'primary_blog', (int) $_POST['primary_blog'] );
		$updated = true;
	} else {
		gc_die( __( '您选择的主站点不存在。' ) );
	}
}

// Used in the HTML title tag.
$title       = __( '我的站点' );
$parent_file = 'index.php';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
			'<p>' . __( '本页面向用户展示他们在本站点网络中拥有的全部站点。用户可以设置一个主站点。用户可以使用站点名称下方的链接来访问站点的前端或仪表盘。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.gechiui.com/Dashboard_My_Sites_Screen">我的站点文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

require_once ABSPATH . 'gc-admin/admin-header.php';

if ( $updated ) { ?>
	<div id="message" class="updated notice is-dismissible"><p><strong><?php _e( '设置已保存。' ); ?></strong></p></div>
<?php } ?>

<div class="wrap">
<h1 class="gc-heading-inline">
<?php
echo esc_html( $title );
?>
</h1>

<?php
if ( in_array( get_site_option( 'registration' ), array( 'all', 'blog' ), true ) ) {
	/** This filter is documented in gc-login.php */
	$sign_up_url = apply_filters( 'gc_signup_location', network_site_url( 'gc-signup.php' ) );
	printf( ' <a href="%s" class="page-title-action">%s</a>', esc_url( $sign_up_url ), esc_html_x( '添加新站点', 'site' ) );
}

if ( empty( $blogs ) ) :
	echo '<p>';
	_e( '您需要先成为至少一个站点的成员才能使用本页面。' );
	echo '</p>';
else :
	?>

<hr class="gc-header-end">

<form id="myblogs" method="post">
	<?php
	choose_primary_blog();
	/**
	 * Fires before the sites list on the My Sites screen.
	 *
	 */
	do_action( 'myblogs_allblogs_options' );
	?>
	<br clear="all" />
	<ul class="my-sites striped">
	<?php
	/**
	 * Enable the Global Settings section on the My Sites screen.
	 *
	 * By default, the Global Settings section is hidden. Passing a non-empty
	 * string to this filter will enable the section, and allow new settings
	 * to be added, either globally or for specific sites.
	 *
	 * @since MU
	 *
	 * @param string $settings_html The settings HTML markup. Default empty.
	 * @param string $context       Context of the setting (global or site-specific). Default 'global'.
	 */
	$settings_html = apply_filters( 'myblogs_options', '', 'global' );

	if ( $settings_html ) {
		echo '<h3>' . __( '全局设置' ) . '</h3>';
		echo $settings_html;
	}

	reset( $blogs );

	foreach ( $blogs as $user_blog ) {
		switch_to_blog( $user_blog->userblog_id );

		echo '<li>';
		echo "<h3>{$user_blog->blogname}</h3>";

		$actions = "<a href='" . esc_url( home_url() ) . "'>" . __( '访问' ) . '</a>';

		if ( current_user_can( 'read' ) ) {
			$actions .= " | <a href='" . esc_url( admin_url() ) . "'>" . __( '仪表盘' ) . '</a>';
		}

		/**
		 * Filters the row links displayed for each site on the My Sites screen.
		 *
		 * @since MU
		 *
		 * @param string $actions   The HTML site link markup.
		 * @param object $user_blog An object containing the site data.
		 */
		$actions = apply_filters( 'myblogs_blog_actions', $actions, $user_blog );

		echo "<p class='my-sites-actions'>" . $actions . '</p>';

		/** This filter is documented in gc-admin/my-sites.php */
		echo apply_filters( 'myblogs_options', '', $user_blog );

		echo '</li>';

		restore_current_blog();
	}
	?>
	</ul>
	<?php
	if ( count( $blogs ) > 1 || has_action( 'myblogs_allblogs_options' ) || has_filter( 'myblogs_options' ) ) {
		?>
		<input type="hidden" name="action" value="updateblogsettings" />
		<?php
		gc_nonce_field( 'update-my-sites' );
		submit_button();
	}
	?>
	</form>
<?php endif; ?>
	</div>
<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';
