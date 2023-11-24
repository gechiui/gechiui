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
	gc_die( __( '未启用多系统支持。' ) );
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
		gc_die( __( '您选择的主系统不存在。' ) );
	}
}

// Used in the HTML title tag.
$title       = __( '我的系统' );
$parent_file = 'index.php';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
			'<p>' . __( '本页面向用户展示他们在本SaaS平台中拥有的全部系统。用户可以设置一个主系统。用户可以使用系统名称下方的链接来访问系统的前端或仪表盘。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.gechiui.com/Dashboard_My_Sites_Screen">我的系统文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

$primary_blog = get_user_meta( $current_user->ID, 'primary_blog', true );

// 添加表格CSS样式和JS脚本
show_dataTable( '#data-table' );

if ( $updated ) {
	$message = __( '设置已保存。' );
	add_settings_error( 'general', 'settings_updated', $message, 'success' );
}

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
	<div class="page-header">
		<h2 class="header-title"><?php echo esc_html( $title ); ?></h2>
		<?php
		if ( in_array( get_site_option( 'registration' ), array( 'all', 'blog' ), true ) ) {
			/** This filter is documented in gc-login.php */
			$sign_up_url = apply_filters( 'gc_signup_location', network_site_url( 'gc-signup.php' ) );
			printf( ' <a href="%s" class="btn btn-primary btn-tone btn-sm">%s</a>', esc_url( $sign_up_url ), esc_html_x( '添加新系统', 'site' ) );
		}
		?>
	</div>
<?php
if ( empty( $blogs ) ) :
	echo '<p>';
	_e( '您需要先成为至少一个系统的成员才能使用本页面。' );
	echo '</p>';
else :
	?>

<form id="myblogs" method="post">
	<?php
	
	/**
	 * Fires before the sites list on the My Sites screen.
	 *
	 * @since 3.0.0
	 */
	do_action( 'myblogs_allblogs_options' );
	?>
    <div class="card m-t-30">
        <div class="card-body">
            <table id="data-table" class="table table-hover">
                <thead>
                    <tr>
                        <th><?php _e('系统ID'); ?></th>
                        <th><?php _e('系统名称'); ?></th>
                        <th><?php _e('域名'); ?></th>
                        <th><?php _e('操作'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
			    foreach ( $blogs as $user_blog ) {
					switch_to_blog( $user_blog->userblog_id );
			        $txt = $primary_blog == $user_blog->userblog_id ? ' '.__('主系统') : '';
					echo '<tr>';
			        echo "<td>{$user_blog->userblog_id}{$txt}</td>";
					echo "<td>{$user_blog->blogname}</td>";
			        echo "<td>{$user_blog->domain}{$user_blog->path}</td>";

					$actions = "<a href='" . esc_url( home_url() ) . "'>" . __( '访问' ) . '</a>';

					if ( current_user_can( 'read' ) ) {
						$actions .= " | <a href='" . esc_url( admin_url() ) . "'>" . __( '仪表盘' ) . '</a>';
					}
			        $url = gc_nonce_url( 'my-sites.php?action=updateblogsettings&primary_blog=' . $user_blog->userblog_id , 'update-my-sites');
			        $actions .= " | <a href='" . esc_url( $url ) . "'>".__('主系统')."</a>";

					/**
					 * Filters the row links displayed for each site on the My Sites screen.
					 *
					 * @since MU (3.0.0)
					 *
					 * @param string $actions   The HTML site link markup.
					 * @param object $user_blog An object containing the site data.
					 */
					$actions = apply_filters( 'myblogs_blog_actions', $actions, $user_blog );

					echo "<td>" . $actions . '</td>';

					/** This filter is documented in gc-admin/my-sites.php */
					echo apply_filters( 'myblogs_options', '', $user_blog );

					echo '</tr>';

					restore_current_blog();
				}
				?>    
                </tbody>
                </table>
            </div>
        </div>
	</form>
<?php endif; ?>
	</div>
<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';