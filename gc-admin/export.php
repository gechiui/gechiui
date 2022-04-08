<?php
/**
 * GeChiUI Export Administration Screen
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** Load GeChiUI Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'export' ) ) {
	gc_die( __( '抱歉，您不能导出此站点的内容。' ) );
}

/** Load GeChiUI export API */
require_once ABSPATH . 'gc-admin/includes/export.php';

// Used in the HTML title tag.
$title = __( '导出' );

/**
 * Display JavaScript on the page.
 *
 *
 */
function export_add_js() {
	?>
<script type="text/javascript">
	jQuery( function($) {
		var form = $('#export-filters'),
			filters = form.find('.export-filters');
		filters.hide();
		form.find('input:radio').on( 'change', function() {
			filters.slideUp('fast');
			switch ( $(this).val() ) {
				case 'attachment': $('#attachment-filters').slideDown(); break;
				case 'posts': $('#post-filters').slideDown(); break;
				case 'pages': $('#page-filters').slideDown(); break;
			}
		});
	} );
</script>
	<?php
}
add_action( 'admin_head', 'export_add_js' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => '<p>' . __( '您可以导出整个站点的内容，然后在另一个GeChiUI乃至其他平台的站点中导入。GeChiUI将导出一个WXR格式的XML文件，其中可储存文章、页面、评论、自定义字段、分类和标签。通过筛选，您可以只导出一部分内容，比如某个分类下的文章、某段时间的文章、某位作者的文章、某个状态的文章等。' ) . '</p>' .
			'<p>' . __( '生成的WXR文件可在其他GeChiUI站点或其他支持该格式的博客软件中使用。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/tools-export-screen/">导出文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

// If the 'download' URL parameter is set, a WXR export file is baked and returned.
if ( isset( $_GET['download'] ) ) {
	$args = array();

	if ( ! isset( $_GET['content'] ) || 'all' === $_GET['content'] ) {
		$args['content'] = 'all';
	} elseif ( 'posts' === $_GET['content'] ) {
		$args['content'] = 'post';

		if ( $_GET['cat'] ) {
			$args['category'] = (int) $_GET['cat'];
		}

		if ( $_GET['post_author'] ) {
			$args['author'] = (int) $_GET['post_author'];
		}

		if ( $_GET['post_start_date'] || $_GET['post_end_date'] ) {
			$args['start_date'] = $_GET['post_start_date'];
			$args['end_date']   = $_GET['post_end_date'];
		}

		if ( $_GET['post_status'] ) {
			$args['status'] = $_GET['post_status'];
		}
	} elseif ( 'pages' === $_GET['content'] ) {
		$args['content'] = 'page';

		if ( $_GET['page_author'] ) {
			$args['author'] = (int) $_GET['page_author'];
		}

		if ( $_GET['page_start_date'] || $_GET['page_end_date'] ) {
			$args['start_date'] = $_GET['page_start_date'];
			$args['end_date']   = $_GET['page_end_date'];
		}

		if ( $_GET['page_status'] ) {
			$args['status'] = $_GET['page_status'];
		}
	} elseif ( 'attachment' === $_GET['content'] ) {
		$args['content'] = 'attachment';

		if ( $_GET['attachment_start_date'] || $_GET['attachment_end_date'] ) {
			$args['start_date'] = $_GET['attachment_start_date'];
			$args['end_date']   = $_GET['attachment_end_date'];
		}
	} else {
		$args['content'] = $_GET['content'];
	}

	/**
	 * Filters the export args.
	 *
	 *
	 * @param array $args The arguments to send to the exporter.
	 */
	$args = apply_filters( 'export_args', $args );

	export_gc( $args );
	die();
}

require_once ABSPATH . 'gc-admin/admin-header.php';

/**
 * Create the date options fields for exporting a given post type.
 *
 * @global gcdb      $gcdb      GeChiUI database abstraction object.
 * @global GC_Locale $gc_locale GeChiUI date and time locale object.
 *
 *
 *
 * @param string $post_type The post type. Default 'post'.
 */
function export_date_options( $post_type = 'post' ) {
	global $gcdb, $gc_locale;

	$months = $gcdb->get_results(
		$gcdb->prepare(
			"
		SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
		FROM $gcdb->posts
		WHERE post_type = %s AND post_status != 'auto-draft'
		ORDER BY post_date DESC
			",
			$post_type
		)
	);

	$month_count = count( $months );
	if ( ! $month_count || ( 1 === $month_count && 0 === (int) $months[0]->month ) ) {
		return;
	}

	foreach ( $months as $date ) {
		if ( 0 === (int) $date->year ) {
			continue;
		}

		$month = zeroise( $date->month, 2 );
		echo '<option value="' . $date->year . '-' . $month . '">' . $gc_locale->get_month( $month ) . ' ' . $date->year . '</option>';
	}
}
?>

<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<p><?php _e( '在您点击下面的按钮后，GeChiUI会创建一个XML文件，供您保存到计算机中。' ); ?></p>
<p><?php _e( '我们称这种格式为GeChiUI eXtended RSS或WXR，其中包含您的全部文章、页面、评论、自定义字段、分类和标签等内容。' ); ?></p>
<p><?php _e( '保存完下载的文件后，便可以在其他GeChiUI站点中使用“导入”功能进行内容导入。' ); ?></p>

<h2><?php _e( '选择导出的内容' ); ?></h2>
<form method="get" id="export-filters">
<fieldset>
<legend class="screen-reader-text"><?php _e( '要导出的内容' ); ?></legend>
<input type="hidden" name="download" value="true" />
<p><label><input type="radio" name="content" value="all" checked="checked" aria-describedby="all-content-desc" /> <?php _e( '所有内容' ); ?></label></p>
<p class="description" id="all-content-desc"><?php _e( '选择此项，则将包含您站点的所有文章、页面、评论、自定义字段、条目信息（分类和标签等）、导航菜单以及自定义文章。' ); ?></p>

<p><label><input type="radio" name="content" value="posts" /> <?php _ex( '文章', 'post type general name' ); ?></label></p>
<ul id="post-filters" class="export-filters">
	<li>
		<label><span class="label-responsive"><?php _e( '分类：' ); ?></span>
		<?php gc_dropdown_categories( array( 'show_option_all' => __( '全部' ) ) ); ?>
		</label>
	</li>
	<li>
		<label><span class="label-responsive"><?php _e( '作者：' ); ?></span>
		<?php
		$authors = $gcdb->get_col( "SELECT DISTINCT post_author FROM {$gcdb->posts} WHERE post_type = 'post'" );
		gc_dropdown_users(
			array(
				'include'         => $authors,
				'name'            => 'post_author',
				'multi'           => true,
				'show_option_all' => __( '全部' ),
				'show'            => 'display_name_with_login',
			)
		);
		?>
		</label>
	</li>
	<li>
		<fieldset>
		<legend class="screen-reader-text"><?php _e( '日期范围：' ); ?></legend>
		<label for="post-start-date" class="label-responsive"><?php _e( '开始日期：' ); ?></label>
		<select name="post_start_date" id="post-start-date">
			<option value="0"><?php _e( '&mdash;选择&mdash;' ); ?></option>
			<?php export_date_options(); ?>
		</select>
		<label for="post-end-date" class="label-responsive"><?php _e( '结束日期：' ); ?></label>
		<select name="post_end_date" id="post-end-date">
			<option value="0"><?php _e( '&mdash;选择&mdash;' ); ?></option>
			<?php export_date_options(); ?>
		</select>
		</fieldset>
	</li>
	<li>
		<label for="post-status" class="label-responsive"><?php _e( '状态：' ); ?></label>
		<select name="post_status" id="post-status">
			<option value="0"><?php _e( '全部' ); ?></option>
			<?php
			$post_stati = get_post_stati( array( 'internal' => false ), 'objects' );
			foreach ( $post_stati as $status ) :
				?>
			<option value="<?php echo esc_attr( $status->name ); ?>"><?php echo esc_html( $status->label ); ?></option>
			<?php endforeach; ?>
		</select>
	</li>
</ul>

<p><label><input type="radio" name="content" value="pages" /> <?php _e( '页面' ); ?></label></p>
<ul id="page-filters" class="export-filters">
	<li>
		<label><span class="label-responsive"><?php _e( '作者：' ); ?></span>
		<?php
		$authors = $gcdb->get_col( "SELECT DISTINCT post_author FROM {$gcdb->posts} WHERE post_type = 'page'" );
		gc_dropdown_users(
			array(
				'include'         => $authors,
				'name'            => 'page_author',
				'multi'           => true,
				'show_option_all' => __( '全部' ),
				'show'            => 'display_name_with_login',
			)
		);
		?>
		</label>
	</li>
	<li>
		<fieldset>
		<legend class="screen-reader-text"><?php _e( '日期范围：' ); ?></legend>
		<label for="page-start-date" class="label-responsive"><?php _e( '开始日期：' ); ?></label>
		<select name="page_start_date" id="page-start-date">
			<option value="0"><?php _e( '&mdash;选择&mdash;' ); ?></option>
			<?php export_date_options( 'page' ); ?>
		</select>
		<label for="page-end-date" class="label-responsive"><?php _e( '结束日期：' ); ?></label>
		<select name="page_end_date" id="page-end-date">
			<option value="0"><?php _e( '&mdash;选择&mdash;' ); ?></option>
			<?php export_date_options( 'page' ); ?>
		</select>
		</fieldset>
	</li>
	<li>
		<label for="page-status" class="label-responsive"><?php _e( '状态：' ); ?></label>
		<select name="page_status" id="page-status">
			<option value="0"><?php _e( '全部' ); ?></option>
			<?php foreach ( $post_stati as $status ) : ?>
			<option value="<?php echo esc_attr( $status->name ); ?>"><?php echo esc_html( $status->label ); ?></option>
			<?php endforeach; ?>
		</select>
	</li>
</ul>

<?php
foreach ( get_post_types(
	array(
		'_builtin'   => false,
		'can_export' => true,
	),
	'objects'
) as $post_type ) :
	?>
<p><label><input type="radio" name="content" value="<?php echo esc_attr( $post_type->name ); ?>" /> <?php echo esc_html( $post_type->label ); ?></label></p>
<?php endforeach; ?>

<p><label><input type="radio" name="content" value="attachment" /> <?php _e( '媒体' ); ?></label></p>
<ul id="attachment-filters" class="export-filters">
	<li>
		<fieldset>
		<legend class="screen-reader-text"><?php _e( '日期范围：' ); ?></legend>
		<label for="attachment-start-date" class="label-responsive"><?php _e( '开始日期：' ); ?></label>
		<select name="attachment_start_date" id="attachment-start-date">
			<option value="0"><?php _e( '&mdash;选择&mdash;' ); ?></option>
			<?php export_date_options( 'attachment' ); ?>
		</select>
		<label for="attachment-end-date" class="label-responsive"><?php _e( '结束日期：' ); ?></label>
		<select name="attachment_end_date" id="attachment-end-date">
			<option value="0"><?php _e( '&mdash;选择&mdash;' ); ?></option>
			<?php export_date_options( 'attachment' ); ?>
		</select>
		</fieldset>
	</li>
</ul>

</fieldset>
<?php
/**
 * Fires at the end of the export filters form.
 *
 *
 */
do_action( 'export_filters' );
?>

<?php submit_button( __( '下载导出的文件' ) ); ?>
</form>
</div>

<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
