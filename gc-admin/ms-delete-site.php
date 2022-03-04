<?php
/**
 * Multisite delete site panel.
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

require_once __DIR__ . '/admin.php';

if ( ! is_multisite() ) {
	gc_die( __( '未启用多站点支持。' ) );
}

if ( ! current_user_can( 'delete_site' ) ) {
	gc_die( __( '抱歉，您不能删除此站点。' ) );
}

if ( isset( $_GET['h'] ) && '' !== $_GET['h'] && false !== get_option( 'delete_blog_hash' ) ) {
	if ( hash_equals( get_option( 'delete_blog_hash' ), $_GET['h'] ) ) {
		gcmu_delete_blog( get_current_blog_id() );
		gc_die(
			sprintf(
				/* translators: %s: Network title. */
				__( '感谢您使用%s，您的站点已被删除。祝您生活愉快，后会有期。' ),
				get_network()->site_name
			)
		);
	} else {
		gc_die( __( '抱歉，您点击的链接已经失效。请选择另一选项。' ) );
	}
}

$blog = get_site();
$user = gc_get_current_user();

// Used in the HTML title tag.
$title       = __( '删除站点' );
$parent_file = 'tools.php';

require_once ABSPATH . 'gc-admin/admin-header.php';

echo '<div class="wrap">';
echo '<h1>' . esc_html( $title ) . '</h1>';

if ( isset( $_POST['action'] ) && 'deleteblog' === $_POST['action'] && isset( $_POST['confirmdelete'] ) && '1' === $_POST['confirmdelete'] ) {
	check_admin_referer( 'delete-blog' );

	$hash = gc_generate_password( 20, false );
	update_option( 'delete_blog_hash', $hash );

	$url_delete = esc_url( admin_url( 'ms-delete-site.php?h=' . $hash ) );

	$switched_locale = switch_to_locale( get_locale() );

	/* translators: Do not translate USERNAME, URL_DELETE, SITENAME, SITEURL: those are placeholders. */
	$content = __(
		"你好，####USERNAME####：

您最近点击了‘删除站点’ 链接并填写

表格在那一页上。


如果你真的想删除你的网站，点击下面的链接。你不会的

再次被要求确认，因此只有在您绝对确定的情况下才单击此链接：

###URL_DELETE###

如果您删除您的网站，请考虑在这里打开一个新的网站

将来的某个时候！（但请记住您当前的网站和用户名。）

都永远消失了。）



感谢您使用该网站，

全部在####SITENAME###

###SITEURL###"
	);
	/**
	 * Filters the text for the email sent to the site admin when a request to delete a site in a Multisite network is submitted.
	 *
	 *
	 * @param string $content The email text.
	 */
	$content = apply_filters( 'delete_site_email_content', $content );

	$content = str_replace( '###USERNAME###', $user->user_login, $content );
	$content = str_replace( '###URL_DELETE###', $url_delete, $content );
	$content = str_replace( '###SITENAME###', get_network()->site_name, $content );
	$content = str_replace( '###SITEURL###', network_home_url(), $content );

	gc_mail(
		get_option( 'admin_email' ),
		sprintf(
			/* translators: %s: Site title. */
			__( '[%s] 删除我的站点' ),
			gc_specialchars_decode( get_option( 'blogname' ) )
		),
		$content
	);

	if ( $switched_locale ) {
		restore_previous_locale();
	}
	?>

	<p><?php _e( '谢谢。请检查您收到的邮件来获得链接以确认此操作。您的站点在该链接被点击后才会被删除。' ); ?></p>

	<?php
} else {
	?>
	<p>
	<?php
		printf(
			/* translators: %s: Network title. */
			__( '如果您不想再使用%s站点了，您可以通过下面的表单来删除它。当您点击<strong>“永久删除我的站点”</strong>之后，我们将发送一封确认邮件。请点击确认邮件中的链接来删除您的站点。' ),
			get_network()->site_name
		);
	?>
	</p>
	<p><?php _e( '请注意，删除后数据不可恢复。' ); ?></p>

	<form method="post" name="deletedirect">
		<?php gc_nonce_field( 'delete-blog' ); ?>
		<input type="hidden" name="action" value="deleteblog" />
		<p><input id="confirmdelete" type="checkbox" name="confirmdelete" value="1" /> <label for="confirmdelete"><strong>
		<?php
			printf(
				/* translators: %s: Site address. */
				__( "我确定要永久删除我的站点，并且我了解我不能再将其恢复，同时我知晓我今后也将无法再使用%s了。" ),
				$blog->domain . $blog->path
			);
		?>
		</strong></label></p>
		<?php submit_button( __( '永久删除我的站点' ) ); ?>
	</form>
	<?php
}
echo '</div>';

require_once ABSPATH . 'gc-admin/admin-footer.php';
