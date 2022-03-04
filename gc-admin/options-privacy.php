<?php
/**
 * Privacy Settings Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_privacy_options' ) ) {
	gc_die( __( '抱歉，您不能管理此站点的隐私选项。' ) );
}

if ( isset( $_GET['tab'] ) && 'policyguide' === $_GET['tab'] ) {
	require_once dirname( __FILE__ ) . '/privacy-policy-guide.php';
	return;
}

add_filter(
	'admin_body_class',
	static function( $body_class ) {
		$body_class .= ' privacy-settings ';

		return $body_class;
	}
);

$action = isset( $_POST['action'] ) ? $_POST['action'] : '';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' =>
				'<p>' . __( '“隐私”界面可让您建立新的隐私政策页面进行显示，亦可选择现有的隐私政策页面。' ) . '</p>' .
				'<p>' . __( '此页面包含可帮助您编写隐私政策的说明建议。 您有责任正确使用这些资源提供您的隐私政策所需的信息，并确保这些信息有效且准确。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/settings-privacy-screen/">隐私设置文档</a>' ) . '</p>'
);

if ( ! empty( $action ) ) {
	check_admin_referer( $action );

	if ( 'set-privacy-page' === $action ) {
		$privacy_policy_page_id = isset( $_POST['page_for_privacy_policy'] ) ? (int) $_POST['page_for_privacy_policy'] : 0;
		update_option( 'gc_page_for_privacy_policy', $privacy_policy_page_id );

		$privacy_page_updated_message = __( '已成功更新隐私政策页面。' );

		if ( $privacy_policy_page_id ) {
			/*
			 * Don't always link to the menu customizer:
			 *
			 * - Unpublished pages can't be selected by default.
			 * - `GC_Customize_Nav_Menus::__construct()` checks the user's capabilities.
			 * - Themes might not "officially" support menus.
			 */
			if (
				'publish' === get_post_status( $privacy_policy_page_id )
				&& current_user_can( 'edit_theme_options' )
				&& current_theme_supports( 'menus' )
			) {
				$privacy_page_updated_message = sprintf(
					/* translators: %s: URL to Customizer -> Menus. */
					__( '隐私政策页面设置更新成功。请记得<a href="%s">更新您的菜单</a>！' ),
					esc_url( add_query_arg( 'autofocus[panel]', 'nav_menus', admin_url( 'customize.php' ) ) )
				);
			}
		}

		add_settings_error( 'page_for_privacy_policy', 'page_for_privacy_policy', $privacy_page_updated_message, 'success' );
	} elseif ( 'create-privacy-page' === $action ) {

		if ( ! class_exists( 'GC_Privacy_Policy_Content' ) ) {
			require_once ABSPATH . 'gc-admin/includes/class-gc-privacy-policy-content.php';
		}

		$privacy_policy_page_content = GC_Privacy_Policy_Content::get_default_content();
		$privacy_policy_page_id      = gc_insert_post(
			array(
				'post_title'   => __( '隐私政策' ),
				'post_status'  => 'draft',
				'post_type'    => 'page',
				'post_content' => $privacy_policy_page_content,
			),
			true
		);

		if ( is_gc_error( $privacy_policy_page_id ) ) {
			add_settings_error(
				'page_for_privacy_policy',
				'page_for_privacy_policy',
				__( '无法创建隐私政策页面。' ),
				'error'
			);
		} else {
			update_option( 'gc_page_for_privacy_policy', $privacy_policy_page_id );

			gc_redirect( admin_url( 'post.php?post=' . $privacy_policy_page_id . '&action=edit' ) );
			exit;
		}
	}
}

// If a Privacy Policy page ID is available, make sure the page actually exists. If not, display an error.
$privacy_policy_page_exists = false;
$privacy_policy_page_id     = (int) get_option( 'gc_page_for_privacy_policy' );

if ( ! empty( $privacy_policy_page_id ) ) {

	$privacy_policy_page = get_post( $privacy_policy_page_id );

	if ( ! $privacy_policy_page instanceof GC_Post ) {
		add_settings_error(
			'page_for_privacy_policy',
			'page_for_privacy_policy',
			__( '当前选择的隐私政策页面不存在。请创建或选择一个新的页面。' ),
			'error'
		);
	} else {
		if ( 'trash' === $privacy_policy_page->post_status ) {
			add_settings_error(
				'page_for_privacy_policy',
				'page_for_privacy_policy',
				sprintf(
					/* translators: %s: URL to Pages Trash. */
					__( '当前选择的隐私政策页面在回收站内。请创建或选择一个新的隐私政策页面，或<a href="%s">恢复当前页面</a>。' ),
					'edit.php?post_status=trash&post_type=page'
				),
				'error'
			);
		} else {
			$privacy_policy_page_exists = true;
		}
	}
}

$parent_file = 'options-general.php';

gc_enqueue_script( 'privacy-tools' );

require_once ABSPATH . 'gc-admin/admin-header.php';

?>
<div class="privacy-settings-header">
	<div class="privacy-settings-title-section">
		<h1>
			<?php _e( '隐私' ); ?>
		</h1>
	</div>

	<nav class="privacy-settings-tabs-wrapper hide-if-no-js" aria-label="<?php esc_attr_e( '次要菜单' ); ?>">
		<a href="<?php echo esc_url( admin_url( 'options-privacy.php' ) ); ?>" class="privacy-settings-tab active" aria-current="true">
			<?php
			/* translators: Tab heading for Site Health Status page. */
			_ex( '设置', '隐私设置' );
			?>
		</a>

		<a href="<?php echo esc_url( admin_url( 'options-privacy.php?tab=policyguide' ) ); ?>" class="privacy-settings-tab">
			<?php
			/* translators: Tab heading for Site Health Status page. */
			_ex( '隐私指南', '隐私设置' );
			?>
		</a>
	</nav>
</div>

<hr class="gc-header-end">

<div class="notice notice-error hide-if-js">
	<p><?php _e( '隐私设置需要JavaScript支持。' ); ?></p>
</div>

<div class="privacy-settings-body hide-if-no-js">
	<h2><?php _e( '隐私设置' ); ?></h2>
	<p>
		<?php _e( '作为网站所有者，您可能需要遵守国内或国际隐私法律。例如，您可能需要创建并展示隐私政策。' ); ?>
		<?php _e( '如果您已经创建了隐私政策页面，请在下方选择；否则，请创建一个新页面。' ); ?>
	</p>
	<p>
		<?php _e( '创建的新页面将包含对您隐私政策的说明和建议。' ); ?>
		<?php _e( '但是，您有责任正确使用这些资源，以提供您的隐私政策所需的信息，确保信息的时效性和准确性。' ); ?>
	</p>
	<p>
		<?php _e( '在您设置了隐私政策页面之后，我们建议您编辑该页。' ); ?>
		<?php _e( '我们也建议您不时回顾您的隐私政策，尤其是在安装或更新任何主题或插件之后，这两者可能会更改或提供新的隐私建议，您可能需要考虑将其加入隐私政策中。' ); ?>
	</p>
	<p>
		<?php
		if ( $privacy_policy_page_exists ) {
			$edit_href = add_query_arg(
				array(
					'post'   => $privacy_policy_page_id,
					'action' => 'edit',
				),
				admin_url( 'post.php' )
			);
			$view_href = get_permalink( $privacy_policy_page_id );
			?>
				<strong>
				<?php
				if ( 'publish' === get_post_status( $privacy_policy_page_id ) ) {
					printf(
						/* translators: 1: URL to edit Privacy Policy page, 2: URL to view Privacy Policy page. */
						__( '<a href="%1$s">编辑</a>或<a href="%2$s">查看</a>您的隐私政策页面内容。' ),
						esc_url( $edit_href ),
						esc_url( $view_href )
					);
				} else {
					printf(
						/* translators: 1: URL to edit Privacy Policy page, 2: URL to preview Privacy Policy page. */
						__( '<a href="%1$s">编辑</a>或<a href="%2$s">预览</a>您的隐私政策页面内容。' ),
						esc_url( $edit_href ),
						esc_url( $view_href )
					);
				}
				?>
				</strong>
			<?php
		}
		printf(
			/* translators: 1: Privacy Policy guide URL, 2: Additional link attributes, 3: Accessibility text. */
			__( '创建您的隐私政策页面时如需帮助，<a href="%1$s" %2$s>请查阅我们的《隐私政策指南》%3$s</a>，了解隐私政策页面应包含哪些内容，并查阅您所安装的插件、主题所推荐的隐私政策内容。' ),
			esc_url( admin_url( 'options-privacy.php?tab=policyguide' ) ),
			'',
			''
		);
		?>
	</p>
	<hr>
	<?php
	$has_pages = (bool) get_posts(
		array(
			'post_type'      => 'page',
			'posts_per_page' => 1,
			'post_status'    => array(
				'publish',
				'draft',
			),
		)
	);
	?>
	<table class="form-table tools-privacy-policy-page" role="presentation">
		<tr>
			<th scope="row">
				<label for="create-page">
				<?php
				if ( $has_pages ) {
					_e( '创建新的隐私政策页面' );
				} else {
					_e( '没有页面。' );
				}
				?>
				</label>
			</th>
			<td>
				<form class="gc-create-privacy-page" method="post" action="">
					<input type="hidden" name="action" value="create-privacy-page" />
					<?php
					gc_nonce_field( 'create-privacy-page' );
					submit_button( __( '创建' ), 'secondary', 'submit', false, array( 'id' => 'create-page' ) );
					?>
				</form>
			</td>
		</tr>
		<?php if ( $has_pages ) : ?>
		<tr>
			<th scope="row">
				<label for="page_for_privacy_policy">
					<?php
					if ( $privacy_policy_page_exists ) {
						_e( '更改您的隐私政策页面' );
					} else {
						_e( '选择隐私政策页面' );
					}
					?>
				</label>
			</th>
			<td>
				<form method="post" action="">
					<input type="hidden" name="action" value="set-privacy-page" />
					<?php
					gc_dropdown_pages(
						array(
							'name'              => 'page_for_privacy_policy',
							'show_option_none'  => __( '&mdash;选择&mdash;' ),
							'option_none_value' => '0',
							'selected'          => $privacy_policy_page_id,
							'post_status'       => array( 'draft', 'publish' ),
						)
					);

					gc_nonce_field( 'set-privacy-page' );

					submit_button( __( '使用本页' ), 'primary', 'submit', false, array( 'id' => 'set-page' ) );
					?>
				</form>
			</td>
		</tr>
		<?php endif; ?>
	</table>
</div>
<?php

require_once ABSPATH . 'gc-admin/admin-footer.php';
