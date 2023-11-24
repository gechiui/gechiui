<?php
/**
 * Edit Site Info Administration Screen
 *
 * @package GeChiUI
 * @subpackage Multisite
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_sites' ) ) {
	gc_die( __( '抱歉，您不能编辑此系统。' ) );
}

get_current_screen()->add_help_tab( get_site_screen_help_tab_args() );
get_current_screen()->set_help_sidebar( get_site_screen_help_sidebar_content() );

$id = isset( $_REQUEST['id'] ) ? (int) $_REQUEST['id'] : 0;

if ( ! $id ) {
	gc_die( __( '系统ID无效。' ) );
}

$details = get_site( $id );
if ( ! $details ) {
	gc_die( __( '请求的系统不存在。' ) );
}

if ( ! can_edit_network( $details->site_id ) ) {
	gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
}

$parsed_scheme = parse_url( $details->siteurl, PHP_URL_SCHEME );
$is_main_site  = is_main_site( $id );

if ( isset( $_REQUEST['action'] ) && 'update-site' === $_REQUEST['action'] ) {
	check_admin_referer( 'edit-site' );

	switch_to_blog( $id );

	// Rewrite rules can't be flushed during switch to blog.
	delete_option( 'rewrite_rules' );

	$blog_data           = gc_unslash( $_POST['blog'] );
	$blog_data['scheme'] = $parsed_scheme;

	if ( $is_main_site ) {
		// On the network's main site, don't allow the domain or path to change.
		$blog_data['domain'] = $details->domain;
		$blog_data['path']   = $details->path;
	} else {
		// For any other site, the scheme, domain, and path can all be changed. We first
		// need to ensure a scheme has been provided, otherwise fallback to the existing.
		$new_url_scheme = parse_url( $blog_data['url'], PHP_URL_SCHEME );

		if ( ! $new_url_scheme ) {
			$blog_data['url'] = esc_url( $parsed_scheme . '://' . $blog_data['url'] );
		}
		$update_parsed_url = parse_url( $blog_data['url'] );

		// If a path is not provided, use the default of `/`.
		if ( ! isset( $update_parsed_url['path'] ) ) {
			$update_parsed_url['path'] = '/';
		}

		$blog_data['scheme'] = $update_parsed_url['scheme'];
		$blog_data['domain'] = $update_parsed_url['host'];
		$blog_data['path']   = $update_parsed_url['path'];
	}

	$existing_details     = get_site( $id );
	$blog_data_checkboxes = array( 'public', 'archived', 'spam', 'mature', 'deleted' );

	foreach ( $blog_data_checkboxes as $c ) {
		if ( ! in_array( (int) $existing_details->$c, array( 0, 1 ), true ) ) {
			$blog_data[ $c ] = $existing_details->$c;
		} else {
			$blog_data[ $c ] = isset( $_POST['blog'][ $c ] ) ? 1 : 0;
		}
	}

	update_blog_details( $id, $blog_data );

	// Maybe update home and siteurl options.
	$new_details = get_site( $id );

	$old_home_url    = trailingslashit( esc_url( get_option( 'home' ) ) );
	$old_home_parsed = parse_url( $old_home_url );

	if ( $old_home_parsed['host'] === $existing_details->domain && $old_home_parsed['path'] === $existing_details->path ) {
		$new_home_url = untrailingslashit( sanitize_url( $blog_data['scheme'] . '://' . $new_details->domain . $new_details->path ) );
		update_option( 'home', $new_home_url );
	}

	$old_site_url    = trailingslashit( esc_url( get_option( 'siteurl' ) ) );
	$old_site_parsed = parse_url( $old_site_url );

	if ( $old_site_parsed['host'] === $existing_details->domain && $old_site_parsed['path'] === $existing_details->path ) {
		$new_site_url = untrailingslashit( sanitize_url( $blog_data['scheme'] . '://' . $new_details->domain . $new_details->path ) );
		update_option( 'siteurl', $new_site_url );
	}

	restore_current_blog();
	gc_redirect(
		add_query_arg(
			array(
				'update' => 'updated',
				'id'     => $id,
			),
			'site-info.php'
		)
	);
	exit;
}

if ( isset( $_GET['update'] ) ) {
	$messages = array();
	if ( 'updated' === $_GET['update'] ) {
		$messages[] = __( '系统信息已更新。' );
	}
}

// Used in the HTML title tag.
/* translators: %s: Site title. */
$title = sprintf( __( '编辑系统：%s' ), esc_html( $details->blogname ) );

$parent_file  = 'sites.php';
$submenu_file = 'sites.php';

network_edit_site_nav(
	array(
		'blog_id'  => $id,
		'selected' => 'site-info',
	)
);

if ( ! empty( $messages ) ) {
	foreach ( $messages as $msg ) {
		add_settings_error( 'general', 'message', $msg, 'success' );
	}
}

network_edit_site_nav(
	array(
		'blog_id'  => $id,
		'selected' => 'site-info',
	)
);

if ( ! empty( $messages ) ) {
	foreach ( $messages as $msg ) {
		echo setting_error( $msg, 'success', 'message' );
	}
}

require_once ABSPATH . 'gc-admin/admin-header.php';

?>

<div class="wrap">
<div class="page-header"><h2 id="edit-site" class="header-title"><?php echo esc_html( $title ); ?></h2></div>
<p class="edit-site-actions"><a href="<?php echo esc_url( get_home_url( $id, '/' ) ); ?>"><?php _e( '访问' ); ?></a> | <a href="<?php echo esc_url( get_admin_url( $id ) ); ?>"><?php _e( '仪表盘' ); ?></a></p>

<form method="post" action="site-info.php?action=update-site">
	<?php gc_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
	<table class="form-table" role="presentation">
		<?php
		// The main site of the network should not be updated on this page.
		if ( $is_main_site ) :
			?>
		<tr class="form-field">
			<th scope="row"><?php _e( '系统地址（URL）' ); ?></th>
			<td><?php echo esc_url( $parsed_scheme . '://' . $details->domain . $details->path ); ?></td>
		</tr>
			<?php
			// For any other site, the scheme, domain, and path can all be changed.
		else :
			?>
		<tr class="form-field form-required">
			<th scope="row"><label for="url"><?php _e( '系统地址（URL）' ); ?></label></th>
			<td><input name="blog[url]" type="text" id="url" value="<?php echo $parsed_scheme . '://' . esc_attr( $details->domain ) . esc_attr( $details->path ); ?>" /></td>
		</tr>
		<?php endif; ?>

		<tr class="form-field">
			<th scope="row"><label for="blog_registered"><?php _ex( 'Registered', 'site' ); ?></label></th>
			<td><input name="blog[registered]" type="text" id="blog_registered" value="<?php echo esc_attr( $details->registered ); ?>" /></td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="blog_last_updated"><?php _e( '上次更新' ); ?></label></th>
			<td><input name="blog[last_updated]" type="text" id="blog_last_updated" value="<?php echo esc_attr( $details->last_updated ); ?>" /></td>
		</tr>
		<?php
		$attribute_fields = array( 'public' => _x( '公开', 'site' ) );
		if ( ! $is_main_site ) {
			$attribute_fields['archived'] = __( '已归档' );
			$attribute_fields['spam']     = _x( '垃圾', 'site' );
			$attribute_fields['deleted']  = __( '已删除' );
		}
		$attribute_fields['mature'] = __( '成人' );
		?>
		<tr>
			<th scope="row"><?php _e( '属性' ); ?></th>
			<td>
			<fieldset>
			<legend class="screen-reader-text">
				<?php
				/* translators: Hidden accessibility text. */
				_e( '设置系统属性' );
				?>
			</legend>
			<?php foreach ( $attribute_fields as $field_key => $field_label ) : ?>
				<label><input type="checkbox" name="blog[<?php echo $field_key; ?>]" value="1" <?php checked( (bool) $details->$field_key, true ); ?> <?php disabled( ! in_array( (int) $details->$field_key, array( 0, 1 ), true ) ); ?> />
				<?php echo $field_label; ?></label><br />
			<?php endforeach; ?>
			<fieldset>
			</td>
		</tr>
	</table>

	<?php
	/**
	 * Fires at the end of the site info form in network admin.
	 *
	 * @since 5.6.0
	 *
	 * @param int $id The site ID.
	 */
	do_action( 'network_site_info_form', $id );

	submit_button();
	?>
</form>

</div>
<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';
