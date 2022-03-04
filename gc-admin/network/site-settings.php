<?php
/**
 * Edit Site Settings Administration Screen
 *
 * @package GeChiUI
 * @subpackage Multisite
 *
 */

/** Load GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_sites' ) ) {
	gc_die( __( '抱歉，您不能编辑此站点。' ) );
}

get_current_screen()->add_help_tab( get_site_screen_help_tab_args() );
get_current_screen()->set_help_sidebar( get_site_screen_help_sidebar_content() );

$id = isset( $_REQUEST['id'] ) ? (int) $_REQUEST['id'] : 0;

if ( ! $id ) {
	gc_die( __( '站点ID无效。' ) );
}

$details = get_site( $id );
if ( ! $details ) {
	gc_die( __( '请求的站点不存在。' ) );
}

if ( ! can_edit_network( $details->site_id ) ) {
	gc_die( __( '抱歉，您不能访问此页面。' ), 403 );
}

$is_main_site = is_main_site( $id );

if ( isset( $_REQUEST['action'] ) && 'update-site' === $_REQUEST['action'] && is_array( $_POST['option'] ) ) {
	check_admin_referer( 'edit-site' );

	switch_to_blog( $id );

	$skip_options = array( 'allowedthemes' ); // Don't update these options since they are handled elsewhere in the form.
	foreach ( (array) $_POST['option'] as $key => $val ) {
		$key = gc_unslash( $key );
		$val = gc_unslash( $val );
		if ( 0 === $key || is_array( $val ) || in_array( $key, $skip_options, true ) ) {
			continue; // Avoids "0 is a protected GC option and may not be modified" error when editing blog options.
		}
		update_option( $key, $val );
	}

	/**
	 * Fires after the site options are updated.
	 *
	 *
	 * @param int $id The ID of the site being updated.
	 */
	do_action( 'gcmu_update_blog_options', $id );

	restore_current_blog();
	gc_redirect(
		add_query_arg(
			array(
				'update' => 'updated',
				'id'     => $id,
			),
			'site-settings.php'
		)
	);
	exit;
}

if ( isset( $_GET['update'] ) ) {
	$messages = array();
	if ( 'updated' === $_GET['update'] ) {
		$messages[] = __( '站点选项已更新。' );
	}
}

// Used in the HTML title tag.
/* translators: %s: Site title. */
$title = sprintf( __( '编辑站点：%s' ), esc_html( $details->blogname ) );

$parent_file  = 'sites.php';
$submenu_file = 'sites.php';

require_once ABSPATH . 'gc-admin/admin-header.php';

?>

<div class="wrap">
<h1 id="edit-site"><?php echo $title; ?></h1>
<p class="edit-site-actions"><a href="<?php echo esc_url( get_home_url( $id, '/' ) ); ?>"><?php _e( '访问' ); ?></a> | <a href="<?php echo esc_url( get_admin_url( $id ) ); ?>"><?php _e( '仪表盘' ); ?></a></p>

<?php

network_edit_site_nav(
	array(
		'blog_id'  => $id,
		'selected' => 'site-settings',
	)
);

if ( ! empty( $messages ) ) {
	foreach ( $messages as $msg ) {
		echo '<div id="message" class="updated notice is-dismissible"><p>' . $msg . '</p></div>';
	}
}
?>
<form method="post" action="site-settings.php?action=update-site">
	<?php gc_nonce_field( 'edit-site' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>" />
	<table class="form-table" role="presentation">
		<?php
		$blog_prefix = $gcdb->get_blog_prefix( $id );
		$sql         = "SELECT * FROM {$blog_prefix}options
			WHERE option_name NOT LIKE %s
			AND option_name NOT LIKE %s";
		$query       = $gcdb->prepare(
			$sql,
			$gcdb->esc_like( '_' ) . '%',
			'%' . $gcdb->esc_like( 'user_roles' )
		);
		$options     = $gcdb->get_results( $query );

		foreach ( $options as $option ) {
			if ( 'default_role' === $option->option_name ) {
				$editblog_default_role = $option->option_value;
			}

			$disabled = false;
			$class    = 'all-options';

			if ( is_serialized( $option->option_value ) ) {
				if ( is_serialized_string( $option->option_value ) ) {
					$option->option_value = esc_html( maybe_unserialize( $option->option_value ) );
				} else {
					$option->option_value = 'SERIALIZED DATA';
					$disabled             = true;
					$class                = 'all-options disabled';
				}
			}

			if ( strpos( $option->option_value, "\n" ) !== false ) {
				?>
				<tr class="form-field">
					<th scope="row"><label for="<?php echo esc_attr( $option->option_name ); ?>"><?php echo ucwords( str_replace( '_', ' ', $option->option_name ) ); ?></label></th>
					<td><textarea class="<?php echo $class; ?>" rows="5" cols="40" name="option[<?php echo esc_attr( $option->option_name ); ?>]" id="<?php echo esc_attr( $option->option_name ); ?>"<?php disabled( $disabled ); ?>><?php echo esc_textarea( $option->option_value ); ?></textarea></td>
				</tr>
				<?php
			} else {
				?>
				<tr class="form-field">
					<th scope="row"><label for="<?php echo esc_attr( $option->option_name ); ?>"><?php echo esc_html( ucwords( str_replace( '_', ' ', $option->option_name ) ) ); ?></label></th>
					<?php if ( $is_main_site && in_array( $option->option_name, array( 'siteurl', 'home' ), true ) ) { ?>
					<td><code><?php echo esc_html( $option->option_value ); ?></code></td>
					<?php } else { ?>
					<td><input class="<?php echo $class; ?>" name="option[<?php echo esc_attr( $option->option_name ); ?>]" type="text" id="<?php echo esc_attr( $option->option_name ); ?>" value="<?php echo esc_attr( $option->option_value ); ?>" size="40" <?php disabled( $disabled ); ?> /></td>
					<?php } ?>
				</tr>
				<?php
			}
		} // End foreach.

		/**
		 * Fires at the end of the Edit Site form, before the submit button.
		 *
		 *
		 * @param int $id Site ID.
		 */
		do_action( 'gcmueditblogaction', $id );
		?>
	</table>
	<?php submit_button(); ?>
</form>

</div>
<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';
