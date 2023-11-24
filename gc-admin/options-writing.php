<?php
/**
 * Writing settings administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_options' ) ) {
	gc_die( __( '抱歉，您不能管理此系统的选项。' ) );
}

// Used in the HTML title tag.
$title       = __( '撰写设置' );
$parent_file = 'options-general.php';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => '<p>' . __( '您可以在本页面设置撰写文章的方式。顶端的几个选项是有关在GeChiUI仪表盘的编辑选项；剩余的项目与通过其他发布方式有关。欲了解关于其他发布方式的信息，请参考文档链接。' ) . '</p>' .
			'<p>' . __( '调整完成后，记得点击页面下方“保存更改”按钮使设置生效。' ) . '</p>',
	)
);

/** This filter is documented in gc-admin/options.php */
if ( apply_filters( 'enable_post_by_email_configuration', true ) ) {
	get_current_screen()->add_help_tab(
		array(
			'id'      => 'options-postemail',
			'title'   => __( '通过电子邮件发布文章' ),
			'content' => '<p>' . __( '通过电子邮件发布文章的设置能让您通过电子邮件将您文章的内容发送到GeChiUI系统。您必须设置一个支持POP3的秘密电子邮箱账户才能使用此功能。该邮箱接收到的任何邮件都将被作为文章发布，所以将此地址保密是一个好主意。' ) . '</p>',
		)
	);
}

/** This filter is documented in gc-admin/options-writing.php */
if ( apply_filters( 'enable_update_services_configuration', true ) ) {
	get_current_screen()->add_help_tab(
		array(
			'id'      => 'options-services',
			'title'   => __( '更新服务' ),
			'content' => '<p>' . __( '如有需要，GeChiUI可自动在您发布新文章时通知若干网络服务。' ) . '</p>',
		)
	);
}

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/settings-writing-screen/">写作设置文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
<div class="page-header"><h2 class="header-title"><?php echo esc_html( $title ); ?></h2></div>
<form method="post" action="options.php">
	<?php settings_fields( 'writing' ); ?>
	<div class="card">
		<div class="card-body">
			<table class="form-table" role="presentation">
			<?php if ( get_site_option( 'initial_db_version' ) < 32453 ) : ?>
			<tr>
			<th scope="row"><?php _e( '格式' ); ?></th>
			<td><fieldset><legend class="screen-reader-text"><span><?php _e( '格式' ); ?></span></legend>
			<label for="use_smilies">
			<input name="use_smilies" type="checkbox" id="use_smilies" value="1" <?php checked( '1', get_option( 'use_smilies' ) ); ?> />
				<?php _e( '转换如<code>:-)</code>、<code>:-P</code>等文字表情符号为图片' ); ?></label><br />
			<label for="use_balanceTags"><input name="use_balanceTags" type="checkbox" id="use_balanceTags" value="1" <?php checked( '1', get_option( 'use_balanceTags' ) ); ?> /> <?php _e( '让GeChiUI自动校正嵌套错误的XHTML代码' ); ?></label>
			</fieldset></td>
			</tr>
			<?php endif; ?>
			<tr>
			<th scope="row"><label for="default_category"><?php _e( '默认文章分类' ); ?></label></th>
			<td>
			<?php
			gc_dropdown_categories(
				array(
					'hide_empty'   => 0,
					'name'         => 'default_category',
					'orderby'      => 'name',
					'selected'     => get_option( 'default_category' ),
					'hierarchical' => true,
				)
			);
			?>
			</td>
			</tr>
			<?php
			$post_formats = get_post_format_strings();
			unset( $post_formats['standard'] );
			?>
			<tr>
			<th scope="row"><label for="default_post_format"><?php _e( '默认文章形式' ); ?></label></th>
			<td>
				<select name="default_post_format" id="default_post_format">
					<option value="0"><?php echo get_post_format_string( 'standard' ); ?></option>
			<?php foreach ( $post_formats as $format_slug => $format_name ) : ?>
					<option<?php selected( get_option( 'default_post_format' ), $format_slug ); ?> value="<?php echo esc_attr( $format_slug ); ?>"><?php echo esc_html( $format_name ); ?></option>
			<?php endforeach; ?>
				</select>
			</td>
			</tr>
			<?php
			if ( get_option( 'link_manager_enabled' ) ) :
				?>
			<tr>
			<th scope="row"><label for="default_link_category"><?php _e( '默认链接分类' ); ?></label></th>
			<td>
				<?php
				gc_dropdown_categories(
					array(
						'hide_empty'   => 0,
						'name'         => 'default_link_category',
						'orderby'      => 'name',
						'selected'     => get_option( 'default_link_category' ),
						'hierarchical' => true,
						'taxonomy'     => 'link_category',
					)
				);
				?>
			</td>
			</tr>
			<?php endif; ?>

			<?php
			do_settings_fields( 'writing', 'default' );
			do_settings_fields( 'writing', 'remote_publishing' ); // A deprecated section.
			?>
			</table>
		</div>
	</div>

	<?php
	/** This filter is documented in gc-admin/options.php */
	if ( apply_filters( 'enable_post_by_email_configuration', true ) ) {
		?>
	<div class="card">
	    <div class="card-header">
	        <h4 class="card-title"><?php _e( '通过电子邮件发布文章' ); ?></h4>
	    </div>
	     <div class="card-body">
			<p>
				<?php
				printf(
					/* translators: 1, 2, 3: Examples of random email addresses. */
					__( '若您想通过发送邮件的方式在GeChiUI上发布文章，则必须设置一个具有POP3访问权限的秘密电子邮箱。鉴于该邮箱接收到的任何信件都将被发布为文章，您最好将此地址保密。以下是一些随机字符串供您使用：%1$s、%2$s、%3$s。' ),
					sprintf( '<kbd class="badge badge-pill badge-geekblue">%s</kbd>', gc_generate_password( 8, false ) ),
					sprintf( '<kbd class="badge badge-pill badge-geekblue">%s</kbd>', gc_generate_password( 8, false ) ),
					sprintf( '<kbd class="badge badge-pill badge-geekblue">%s</kbd>', gc_generate_password( 8, false ) )
				);
				?>
			</p>

			<table class="form-table" role="presentation">
			<tr>
			<th scope="row"><label for="mailserver_url"><?php _e( '邮件服务器' ); ?></label></th>
			<td><input name="mailserver_url" type="text" id="mailserver_url" value="<?php form_option( 'mailserver_url' ); ?>" class="regular-text code" />
			<label for="mailserver_port"><?php _e( '端口' ); ?></label>
			<input name="mailserver_port" type="text" id="mailserver_port" value="<?php form_option( 'mailserver_port' ); ?>" class="small-text" />
			</td>
			</tr>
			<tr>
			<th scope="row"><label for="mailserver_login"><?php _e( '登录名' ); ?></label></th>
			<td><input name="mailserver_login" type="text" id="mailserver_login" value="<?php form_option( 'mailserver_login' ); ?>" class="regular-text ltr" /></td>
			</tr>
			<tr>
			<th scope="row"><label for="mailserver_pass"><?php _e( '密码' ); ?></label></th>
			<td>
			<input name="mailserver_pass" type="text" id="mailserver_pass" value="<?php form_option( 'mailserver_pass' ); ?>" class="regular-text ltr" />
			</td>
			</tr>
			<tr>
			<th scope="row"><label for="default_email_category"><?php _e( '默认邮件发表分类' ); ?></label></th>
			<td>
				<?php
				gc_dropdown_categories(
					array(
						'hide_empty'   => 0,
						'name'         => 'default_email_category',
						'orderby'      => 'name',
						'selected'     => get_option( 'default_email_category' ),
						'hierarchical' => true,
					)
				);
				?>
			</td>
			</tr>
				<?php do_settings_fields( 'writing', 'post_via_email' ); ?>
			</table>
		</div>
	</div>
	<?php } ?>

	<?php
	/**
	 * Filters whether to enable the Update Services section in the Writing settings screen.
	 *
	 *
	 * @param bool $enable Whether to enable the Update Services settings area. Default true.
	 */
	if ( apply_filters( 'enable_update_services_configuration', true ) ) {
	?>
	<div class="card">
	    <div class="card-header">
	        <h4 class="card-title"><?php _e( '更新服务' ); ?></h4>
	    </div>
	     <div class="card-body">
			<?php if ( 1 == get_option( 'blog_public' ) ) : ?>

			<p><label for="ping_sites">
				<?php
				printf(
					/* translators: %s: Documentation URL. */
					__( '在您发表新文章时，GeChiUI会自动通知系统更新服务。要获取更多资讯，请参见Codex上的<a href="%s">更新服务文档</a>。请以换行分隔多个服务URL。' ),
					__( 'https://www.gechiui.com/support/update-services/' )
				);
				?>
			</label></p>

			<textarea name="ping_sites" id="ping_sites" class="large-text code" rows="3"><?php echo esc_textarea( get_option( 'ping_sites' ) ); ?></textarea>

			<?php else : ?>

			<p>
				<?php
				printf(
					/* translators: 1: Documentation URL, 2: URL to Reading Settings screen. */
					__( '基于您系统的<a href="%2$s">可见性设置</a>，GeChiUI不会通知任何<a href="%1$s">更新服务</a>。' ),
					__( 'https://www.gechiui.com/support/update-services/' ),
					'options-reading.php'
				);
				?>
			</p>
			<?php endif; ?>
		 </div>
	</div>
	<?php } // enable_update_services_configuration ?>

	<?php do_settings_sections( 'writing' ); ?>

	<?php submit_button(); ?>

</form>
</div>

<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
