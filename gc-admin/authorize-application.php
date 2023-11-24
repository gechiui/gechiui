<?php
/**
 * Authorize Application Screen
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

$error        = null;
$new_password = '';

// This is the no-js fallback script. Generally this will all be handled by `auth-app.js`.
if ( isset( $_POST['action'] ) && 'authorize_appkey' === $_POST['action'] ) {
	check_admin_referer( 'authorize_appkey' );

	$success_url = $_POST['success_url'];
	$reject_url  = $_POST['reject_url'];
	$app_name    = $_POST['app_name'];
	$app_id      = $_POST['app_id'];
	$redirect    = '';

	if ( isset( $_POST['reject'] ) ) {
		if ( $reject_url ) {
			$redirect = $reject_url;
		} else {
			$redirect = admin_url();
		}
	} elseif ( isset( $_POST['approve'] ) ) {
		$created = GC_AppKeys::create_new_appkey(
			get_current_user_id(),
			array(
				'name'   => $app_name,
				'app_id' => $app_id,
			)
		);

		if ( is_gc_error( $created ) ) {
			$error = $created;
		} else {
			list( $new_password ) = $created;

			if ( $success_url ) {
				$redirect = add_query_arg(
					array(
						'site_url'   => urlencode( site_url() ),
						'user_login' => urlencode( gc_get_current_user()->user_login ),
						'password'   => urlencode( $new_password ),
					),
					$success_url
				);
			}
		}
	}

	if ( $redirect ) {
		// Explicitly not using gc_safe_redirect b/c sends to arbitrary domain.
		gc_redirect( $redirect );
		exit;
	}
}

// Used in the HTML title tag.
$title = __( '应用程序授权' );

$app_name    = ! empty( $_REQUEST['app_name'] ) ? $_REQUEST['app_name'] : '';
$app_id      = ! empty( $_REQUEST['app_id'] ) ? $_REQUEST['app_id'] : '';
$success_url = ! empty( $_REQUEST['success_url'] ) ? $_REQUEST['success_url'] : null;

if ( ! empty( $_REQUEST['reject_url'] ) ) {
	$reject_url = $_REQUEST['reject_url'];
} elseif ( $success_url ) {
	$reject_url = add_query_arg( 'success', 'false', $success_url );
} else {
	$reject_url = null;
}

$user = gc_get_current_user();

$request  = compact( 'app_name', 'app_id', 'success_url', 'reject_url' );
$is_valid = gc_is_authorize_appkey_request_valid( $request, $user );

if ( is_gc_error( $is_valid ) ) {
	gc_die(
		__( '这个授权应用程序请求不被允许。' ) . ' ' . implode( ' ', $is_valid->get_error_messages() ),
		__( '无法授权此应用程序' )
	);
}

if ( gc_is_site_protected_by_basic_auth( 'front' ) ) {
	gc_die(
		__( '您的系统可能正在使用基本身份验证功能，此功能目前与Appkey不兼容。' ),
		__( '无法授权此应用程序' ),
		array(
			'response'  => 501,
			'link_text' => __( '返回' ),
			'link_url'  => $reject_url ? add_query_arg( 'error', 'disabled', $reject_url ) : admin_url(),
		)
	);
}

if ( ! gc_is_appkeys_available_for_user( $user ) ) {
	if ( gc_is_appkeys_available() ) {
		$message = __( '您的账户无法使用Appkey。请联系系统管理员以获得协助。' );
	} else {
		$message = __( 'Appkey不可用。' );
	}

	gc_die(
		$message,
		__( '无法授权此应用程序' ),
		array(
			'response'  => 501,
			'link_text' => __( '返回' ),
			'link_url'  => $reject_url ? add_query_arg( 'error', 'disabled', $reject_url ) : admin_url(),
		)
	);
}

gc_enqueue_script( 'auth-app' );
gc_localize_script(
	'auth-app',
	'authApp',
	array(
		'site_url'   => site_url(),
		'user_login' => $user->user_login,
		'success'    => $success_url,
		'reject'     => $reject_url ? $reject_url : admin_url(),
	)
);

require_once ABSPATH . 'gc-admin/admin-header.php';

?>
<div class="wrap">
	<div class="page-header"><h2 class="header-title"><?php echo esc_html( $title ); ?></h2></div>

	<?php if ( is_gc_error( $error ) ) : 
		echo setting_error($error->get_error_message(), 'danger');
	 endif; ?>

	<div class="card auth-app-card">
		<h4><?php _e( '一个应用程序想连接到您的帐户。' ); ?></h4>
		<?php if ( $app_name ) : ?>
			<p>
				<?php
				printf(
					/* translators: %s: Application name. */
					__( '您想授予名为 %s 的应用程序访问您帐户的权限吗？ 仅当您信任该应用程序时才应执行此操作。' ),
					'<strong>' . esc_html( $app_name ) . '</strong>'
				);
				?>
			</p>
		<?php else : ?>
			<p><?php _e( '您想授予该应用程序访问您帐户的权限吗？ 仅当您信任该应用程序时才应执行此操作。' ); ?></p>
		<?php endif; ?>

		<?php
		if ( is_multisite() ) {
			$blogs       = get_blogs_of_user( $user->ID, true );
			$blogs_count = count( $blogs );
			if ( $blogs_count > 1 ) {
				?>
				<p>
					<?php
					printf(
						/* translators: 1: URL to my-sites.php, 2: Number of sites the user has. */
						_n(
							'这将授予对<a href="%1$s">此SaaS平台安装中您有权限的 %2$s 个系统的访问权限</a>。',
							'这将授予对<a href="%1$s">此SaaS平台安装中您有权限的 %2$s 个系统的访问权限</a>。',
							$blogs_count
						),
						admin_url( 'my-sites.php' ),
						number_format_i18n( $blogs_count )
					);
					?>
				</p>
				<?php
			}
		}
		?>

		<?php if ( $new_password ) : ?>
			<div class="alert alert-success notice-alt below-h2">
				<p class="appkey-display">
					<label for="new-appkey-value">
						<?php
						printf(
							/* translators: %s: Application name. */
							esc_html__( '%s的新Appkey为：' ),
							'<strong>' . esc_html( $app_name ) . '</strong>'
						);
						?>
					</label>
					<input id="new-appkey-value" type="text" class="code" readonly="readonly" value="<?php esc_attr( GC_AppKeys::chunk_password( $new_password ) ); ?>" />
				</p>
				<p><?php _e( '确保将其保存在安全的位置。 您将无法检索它。' ); ?></p>
			</div>

			<?php
			/**
			 * Fires in the Authorize AppKey new password section in the no-JS version.
			 *
			 * In most cases, this should be used in combination with the {@see 'gc_appkeys_approve_app_request_success'}
			 * action to ensure that both the JS and no-JS variants are handled.
			 *
		
		
			 *
			 * @param string  $new_password The newly generated appkey.
			 * @param array   $request      The array of request data. All arguments are optional and may be empty.
			 * @param GC_User $user         The user authorizing the application.
			 */
			do_action( 'gc_authorize_appkey_form_approved_no_js', $new_password, $request, $user );
			?>
		<?php else : ?>
			<form action="<?php echo esc_url( admin_url( 'authorize-application.php' ) ); ?>" method="post" class="form-wrap">
				<?php gc_nonce_field( 'authorize_appkey' ); ?>
				<input type="hidden" name="action" value="authorize_appkey" />
				<input type="hidden" name="app_id" value="<?php echo esc_attr( $app_id ); ?>" />
				<input type="hidden" name="success_url" value="<?php echo esc_url( $success_url ); ?>" />
				<input type="hidden" name="reject_url" value="<?php echo esc_url( $reject_url ); ?>" />

				<div class="form-field">
					<label for="app_name"><?php _e( '新的Appkey名称' ); ?></label>
					<input type="text" id="app_name" name="app_name" value="<?php echo esc_attr( $app_name ); ?>" required />
				</div>

				<?php
				/**
				 * Fires in the Authorize AppKey form before the submit buttons.
				 *
			
				 *
				 * @param array   $request {
				 *     The array of request data. All arguments are optional and may be empty.
				 *
				 *     @type string $app_name    The suggested name of the application.
				 *     @type string $success_url The url the user will be redirected to after approving the application.
				 *     @type string $reject_url  The url the user will be redirected to after rejecting the application.
				 * }
				 * @param GC_User $user The user authorizing the application.
				 */
				do_action( 'gc_authorize_appkey_form', $request, $user );
				?>

				<?php
				submit_button(
					__( '是，核准此连接' ),
					'primary',
					'approve',
					false,
					array(
						'aria-describedby' => 'description-approve',
					)
				);
				?>
				<p class="description" id="description-approve">
					<?php
					if ( $success_url ) {
						printf(
							/* translators: %s: The URL the user is being redirected to. */
							__( '您将被跳转到%s' ),
							'<strong><code>' . esc_html(
								add_query_arg(
									array(
										'site_url'   => site_url(),
										'user_login' => $user->user_login,
										'password'   => '[------]',
									),
									$success_url
								)
							) . '</code></strong>'
						);
					} else {
						_e( '您将获得密码以手动输入相关应用程序。' );
					}
					?>
				</p>

				<?php
				submit_button(
					__( '否，不核准此连接' ),
					'primary tone',
					'reject',
					false,
					array(
						'aria-describedby' => 'description-reject',
					)
				);
				?>
				<p class="description" id="description-reject">
					<?php
					if ( $reject_url ) {
						printf(
							/* translators: %s: The URL the user is being redirected to. */
							__( '您将被跳转到%s' ),
							'<strong><code>' . esc_html( $reject_url ) . '</code></strong>'
						);
					} else {
						_e( '您将返回到 GeChiUI 仪表盘，并且不会进行任何更改。' );
					}
					?>
				</p>
			</form>
		<?php endif; ?>
	</div>
</div>
<?php

require_once ABSPATH . 'gc-admin/admin-footer.php';
