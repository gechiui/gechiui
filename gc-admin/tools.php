<?php
/**
 * Tools Administration Screen.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

if ( isset( $_GET['page'] ) && ! empty( $_POST ) ) {
	// Ensure POST-ing to `tools.php?page=export_personal_data` and `tools.php?page=remove_personal_data`
	// continues to work after creating the new files for exporting and erasing of personal data.
	if ( 'export_personal_data' === $_GET['page'] ) {
		require_once ABSPATH . 'gc-admin/export-personal-data.php';
		return;
	} elseif ( 'remove_personal_data' === $_GET['page'] ) {
		require_once ABSPATH . 'gc-admin/erase-personal-data.php';
		return;
	}
}

// The privacy policy guide used to be outputted from here. Since GC 5.3 it is in gc-admin/privacy-policy-guide.php.
if ( isset( $_GET['gc-privacy-policy-guide'] ) ) {
	require_once dirname( __DIR__ ) . '/gc-load.php';
	gc_redirect( admin_url( 'options-privacy.php?tab=policyguide' ), 301 );
	exit;
} elseif ( isset( $_GET['page'] ) ) {
	// These were also moved to files in GC 5.3.
	if ( 'export_personal_data' === $_GET['page'] ) {
		require_once dirname( __DIR__ ) . '/gc-load.php';
		gc_redirect( admin_url( 'export-personal-data.php' ), 301 );
		exit;
	} elseif ( 'remove_personal_data' === $_GET['page'] ) {
		require_once dirname( __DIR__ ) . '/gc-load.php';
		gc_redirect( admin_url( 'erase-personal-data.php' ), 301 );
		exit;
	}
}

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

// Used in the HTML title tag.
$title = __( '工具' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'converter',
		'title'   => __( '分类与标签转换器' ),
		'content' => '<p>' . __( '分类之间可以有层级关系，您可以在一个分类下安排其他子分类。标签则没有层级关系，不可嵌套。有时候，人们刚开始写文章时，会先选择分类、标签其中之一来使用，然后发现另一种或许更加适合自己。' ) . '</p>' .
		'<p>' . __( '本界面上的“分类与标签转换器”链接可将您带到“导入”界面，您可在该界面安装对应的转换器插件。安装完成后，点击“启用插件并运行导入工具”，您就可以进行分类和标签的双向转换了。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/tools-screen/">工具文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

require_once ABSPATH . 'gc-admin/admin-header.php';

?>
<div class="wrap">
<div class="page-header"><h2 class="header-title"><?php echo esc_html( $title ); ?></h2></div>
<?php

if ( current_user_can( 'import' ) ) :
	$cats = get_taxonomy( 'category' );
	$tags = get_taxonomy( 'post_tag' );
	if ( current_user_can( $cats->cap->manage_terms ) || current_user_can( $tags->cap->manage_terms ) ) :
		?>
		<div class="card">
			<div class="card-body">
				<h4><?php _e( '分类与标签转换器' ); ?></h4>
				<p>
				<?php
					printf(
						/* translators: %s: URL to Import screen. */
						__( '如果您想将分类转为标签（或者反过来），可以选用“导入”页面上的<a href="%s">分类与标签转换器</a>来实现。' ),
						'import.php'
					);
				?>
				</p>
			</div>
		</div>
		<?php
	endif;
endif;

/**
 * Fires at the end of the Tools Administration screen.
 *
 */
do_action( 'tool_box' );

?>
</div>
<?php

require_once ABSPATH . 'gc-admin/admin-footer.php';
