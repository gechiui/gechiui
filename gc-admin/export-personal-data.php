<?php
/**
 * Privacy tools, Export Personal Data screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'export_others_personal_data' ) ) {
	gc_die( __( '抱歉，您不能从此系统导出个人数据。' ) );
}

// Contextual help - choose Help on the top right of admin panel to preview this.
get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
					'<p>' . __( '您可以在此界面管理个人数据导出请求。' ) . '</p>' .
					'<p>' . __( '世界各地许多地区的隐私法要求企业、网站或系统提供其所收集的有关个人的所有数据，这项规定有时也被称为“数据携带权”。' ) . '</p>' .
					'<p>' . __( '该工具通过所提供的电子邮箱关联存储在GeChiUI中的数据，包括个人资料数据和评论。' ) . '</p>' .
					'<p><strong>' . __( '注意：由于此工具仅收集来自 GeChiUI 及相关插件的数据，您可能需要做更多的工作来完成抹除请求。 您还应该删除由您的企业或网站使用的任何第三方服务收集或存储的任何数据。' ) . '</strong></p>',
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => 'default-data',
		'title'   => __( '默认导出数据' ),
		'content' =>
					'<p>' . __( '由GeChiUI收集并显示在导出文件中的个人数据包括：' ) . '</p>' .
					'<p>' . __( '<strong>个人资料信息</strong>——用户的电子邮箱、用户名、显示名、昵称、姓氏、名字、个人说明和注册日期。' ) . '</p>' .
					'<p>' . __( '<strong>社群活动位置</strong>——用于仪表盘小工具中显示即将举行的社群聚会的用户IP地址。' ) . '</p>' .
					'<p>' . __( '<strong>会话令牌</strong>——用户登录信息、IP地址、过期日期、用户代理（浏览器/操作系统）和上次登录时间。' ) . '</p>' .
					'<p>' . __( '<strong>评论</strong>——对于用户的任何评论，其电子邮箱、IP地址、用户代理（浏览器/操作系统）、日期/时间、评论内容和内容URL。' ) . '</p>' .
					'<p>' . __( '<strong>媒体</strong>——用户上传的所有媒体文件的URL列表。' ) . '</p>',
	)
);

$privacy_policy_guide = '<p>' . sprintf(
	/* translators: %s: URL to Privacy Policy Guide screen. */
	__( '如果不确定，请查看插件文档或与插件作者联系，以了解插件是否收集数据及是否支持个人数据导出器工具。此信息可在《<a href="%s">隐私政策指南</a>》中找到。' ),
	admin_url( 'options-privacy.php?tab=policyguide' )
) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'plugin-data',
		'title'   => __( '插件数据' ),
		'content' =>
					'<p>' . __( '许多插件可以在GeChiUI数据库中收集、存储个人数据，或通过远程方式收集、存储。任何导出个人数据请求都应该包括来自插件的数据。' ) . '</p>' .
					'<p>' . __( '插件制作者可以<a href="https://developer.gechiui.com/plugins/privacy/adding-the-personal-data-exporter-to-your-plugin/" target="_blank">了解更多有关如何将个人数据导出器添加到插件的信息</a>。' ) . '</p>' .
					$privacy_policy_guide,
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/tools-export-personal-data-screen/">导出个人数据文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

// Handle list table actions.
_gc_personal_data_handle_actions();

// Cleans up failed and expired requests before displaying the list table.
_gc_personal_data_cleanup_requests();

gc_enqueue_script( 'privacy-tools' );

add_screen_option(
	'per_page',
	array(
		'default' => 20,
		'option'  => 'export_personal_data_requests_per_page',
	)
);

$_list_table_args = array(
	'plural'   => 'privacy_requests',
	'singular' => 'privacy_request',
);

$requests_table = _get_list_table( 'GC_Privacy_Data_Export_Requests_List_Table', $_list_table_args );

$requests_table->screen->set_screen_reader_content(
	array(
		'heading_views'      => __( '筛选导出个人数据列表' ),
		'heading_pagination' => __( '导出个人数据列表导航' ),
		'heading_list'       => __( '导出个人数据列表' ),
	)
);

$requests_table->process_bulk_action();
$requests_table->prepare_items();

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap nosubsub">
	<div class="page-header">
		<h2 class="header-title"><?php esc_html_e( '导出个人数据' ); ?></h2>
		<p><?php _e( '此工具可以导出给定用户的已知数据并打包为.zip文件，有助于系统所有者遵守当地法律法规。' ); ?></p>
	</div>

	<form action="<?php echo esc_url( admin_url( 'export-personal-data.php' ) ); ?>" method="post" class="gc-privacy-request-form">
	<div class="card">
	    <div class="card-header">
	        <h4 class="card-title"><?php esc_html_e( '添加数据导出请求' ); ?></h4>
	    </div>
	    <div class="card-body">
			<div class="gc-privacy-request-form-field">
			<table class="form-table">
					<tr>
						<th scope="row">
							<label for="username_or_email_for_privacy_request"><?php esc_html_e( '用户名或电子邮箱' ); ?></label>
						</th>
						<td>
							<input type="text" required class="regular-text ltr" id="username_or_email_for_privacy_request" name="username_or_email_for_privacy_request" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e( '确认邮件' ); ?>
						</th>
						<td>
							<label for="send_confirmation_email">
								<input type="checkbox" name="send_confirmation_email" id="send_confirmation_email" value="1" checked="checked" />
								<?php _e( '发送个人数据导出确认邮件。' ); ?>
							</label>
						</td>
					</tr>
				</table>
				<p class="submit">
					<?php submit_button( __( '发送请求' ), 'primary', 'submit', false ); ?>
				</p>
			</div>
			<?php gc_nonce_field( 'personal-data-request' ); ?>
			<input type="hidden" name="action" value="add_export_personal_data_request" />
			<input type="hidden" name="type_of_action" value="export_personal_data" />
		</div>
	</div>
	</form>

	<?php $requests_table->views(); ?>

	<form class="search-form gc-clearfix">
		<?php $requests_table->search_box( __( '搜索请求' ), 'requests' ); ?>
		<input type="hidden" name="filter-status" value="<?php echo isset( $_REQUEST['filter-status'] ) ? esc_attr( sanitize_text_field( $_REQUEST['filter-status'] ) ) : ''; ?>" />
		<input type="hidden" name="orderby" value="<?php echo isset( $_REQUEST['orderby'] ) ? esc_attr( sanitize_text_field( $_REQUEST['orderby'] ) ) : ''; ?>" />
		<input type="hidden" name="order" value="<?php echo isset( $_REQUEST['order'] ) ? esc_attr( sanitize_text_field( $_REQUEST['order'] ) ) : ''; ?>" />
	</form>

	<form method="post">
		<?php
		$requests_table->display();
		$requests_table->embed_scripts();
		?>
	</form>
</div>

<?php
require_once ABSPATH . 'gc-admin/admin-footer.php';
