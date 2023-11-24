<?php
/**
 * About This Version administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

// Used in the HTML title tag.
/* translators: Page title of the About GeChiUI page in the admin. */
$title = _x( '关于', 'page title' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

require_once ABSPATH . 'gc-admin/admin-header.php';
?>
	<div class="about__container">

		<div class="about__section">
			<h3>
				<?php _e( 'GeChiUI 后台开发框架' ); ?>
			</h3>
			<p>当前版本：<?php echo  get_bloginfo( 'version', 'display' ); ?></p>
			<p>
				<?php _e( '版权所有 2023 格尺科技，保留所有权利。' ); ?>
			</p>
			<p>
				<a href="privacy.php"><?php _e( '隐私条款' ); ?></a>
			</p>
		</div>
	</div>

<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>