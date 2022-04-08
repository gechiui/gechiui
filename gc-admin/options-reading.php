<?php
/**
 * Reading settings administration panel.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/** GeChiUI Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_options' ) ) {
	gc_die( __( '抱歉，您不能管理此站点的选项。' ) );
}

// Used in the HTML title tag.
$title       = __( '阅读设置' );
$parent_file = 'options-general.php';

add_action( 'admin_head', 'options_reading_add_js' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( '概述' ),
		'content' => '<p>' . __( '本页面提供有关站点内容显示方式的选项。' ) . '</p>' .
			'<p>' . sprintf(
				/* translators: %s: URL to create a new page. */
				__( '您可以选择在您站点的主页上显示什么。可以是按时间降序排列的文章（传统博客），也可以是固定/静态页面。要设置静态主页，您需要创建两个<a href="%s">页面</a>，其中一个会变成主页，而另一个将会显示您的文章。' ),
				'post-new.php?post_type=page'
			) . '</p>' .
			'<p>' . sprintf(
				/* translators: %s: Documentation URL. */
				__( '您还可以控制您的内容在 RSS feeds 中的显示方式，包括要显示的最大文章数量、是否显示全文或摘要。<a href="%s">了解有关 feeds 的更多信息</a>。' ),
				__( 'https://www.gechiui.com/support/gechiui-feeds/' )
			) . '</p>' .
			'<p>' . __( '调整完成后，记得点击页面下方“保存更改”按钮使设置生效。' ) . '</p>',
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => 'site-visibility',
		'title'   => has_action( 'blog_privacy_selector' ) ? __( '站点可见性' ) : __( '对搜索引擎的可见性' ),
		'content' => '<p>' . __( '您可以决定是否让索引工具、搜索引擎爬虫、ping服务访问您的站点。如果您不希望它们访问您的站点，请选择“建议搜索引擎不索引本站点”，并点击下方的“保存更改”。' ) . '</p>' .
			'<p>' . __( '请注意，即使设置为不鼓励搜索引擎进行索引，您的站点在网络上仍然是可见的，且并非所有搜索引擎都遵守此请求。' ) . '</p>' .
			'<p>' . __( '当该项设置生效时，仪表盘中的“概况”模块将提醒您“自动建议搜索引擎不抓取”。您的站点在屏蔽搜索引擎期间无法被搜索引擎收录。' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( '更多信息：' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/settings-reading-screen/">阅读设置文档</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://www.gechiui.com/support/">支持</a>' ) . '</p>'
);

require_once ABSPATH . 'gc-admin/admin-header.php';
?>

<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<form method="post" action="options.php">
<?php
settings_fields( 'reading' );

if ( ! in_array( get_option( 'blog_charset' ), array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ), true ) ) {
	add_settings_field( 'blog_charset', __( '页面和feed编码' ), 'options_reading_blog_charset', 'reading', 'default', array( 'label_for' => 'blog_charset' ) );
}
?>

<?php if ( ! get_pages() ) : ?>
<input name="show_on_front" type="hidden" value="posts" />
<table class="form-table" role="presentation">
	<?php
	if ( 'posts' !== get_option( 'show_on_front' ) ) :
		update_option( 'show_on_front', 'posts' );
	endif;

else :
	if ( 'page' === get_option( 'show_on_front' ) && ! get_option( 'page_on_front' ) && ! get_option( 'page_for_posts' ) ) {
		update_option( 'show_on_front', 'posts' );
	}
	?>
<table class="form-table" role="presentation">
<tr>
<th scope="row"><?php _e( '您的主页显示' ); ?></th>
<td id="front-static-pages"><fieldset><legend class="screen-reader-text"><span><?php _e( '您的主页显示' ); ?></span></legend>
	<p><label>
		<input name="show_on_front" type="radio" value="posts" class="tog" <?php checked( 'posts', get_option( 'show_on_front' ) ); ?> />
		<?php _e( '您的最新文章' ); ?>
	</label>
	</p>
	<p><label>
		<input name="show_on_front" type="radio" value="page" class="tog" <?php checked( 'page', get_option( 'show_on_front' ) ); ?> />
		<?php
		printf(
			/* translators: %s: URL to Pages screen. */
			__( '一个<a href="%s">静态页面</a>（在下方选择）' ),
			'edit.php?post_type=page'
		);
		?>
	</label>
	</p>
<ul>
	<li><label for="page_on_front">
	<?php
	printf(
		/* translators: %s: Select field to choose the front page. */
		__( '主页：%s' ),
		gc_dropdown_pages(
			array(
				'name'              => 'page_on_front',
				'echo'              => 0,
				'show_option_none'  => __( '&mdash;选择&mdash;' ),
				'option_none_value' => '0',
				'selected'          => get_option( 'page_on_front' ),
			)
		)
	);
	?>
</label></li>
	<li><label for="page_for_posts">
	<?php
	printf(
		/* translators: %s: Select field to choose the page for posts. */
		__( '文章页：%s' ),
		gc_dropdown_pages(
			array(
				'name'              => 'page_for_posts',
				'echo'              => 0,
				'show_option_none'  => __( '&mdash;选择&mdash;' ),
				'option_none_value' => '0',
				'selected'          => get_option( 'page_for_posts' ),
			)
		)
	);
	?>
</label></li>
</ul>
	<?php if ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) === get_option( 'page_on_front' ) ) : ?>
	<div id="front-page-warning" class="notice notice-warning inline"><p><?php _e( '<strong>警告：</strong>二者不能为同一页面！' ); ?></p></div>
	<?php endif; ?>
	<?php if ( get_option( 'gc_page_for_privacy_policy' ) === get_option( 'page_for_posts' ) || get_option( 'gc_page_for_privacy_policy' ) === get_option( 'page_on_front' ) ) : ?>
	<div id="privacy-policy-page-warning" class="notice notice-warning inline"><p><?php _e( '<strong>警告：</strong>这些页面不应该与您的隐私政策页面相同！' ); ?></p></div>
	<?php endif; ?>
</fieldset></td>
</tr>
<?php endif; ?>
<tr>
<th scope="row"><label for="posts_per_page"><?php _e( '博客页面至多显示' ); ?></label></th>
<td>
<input name="posts_per_page" type="number" step="1" min="1" id="posts_per_page" value="<?php form_option( 'posts_per_page' ); ?>" class="small-text" /> <?php _e( '篇文章' ); ?>
</td>
</tr>
<tr>
<th scope="row"><label for="posts_per_rss"><?php _e( 'Feed中显示最近' ); ?></label></th>
<td><input name="posts_per_rss" type="number" step="1" min="1" id="posts_per_rss" value="<?php form_option( 'posts_per_rss' ); ?>" class="small-text" /> <?php _e( '个项目' ); ?></td>
</tr>
<tr>
<th scope="row"><?php _e( '对于feed中的每篇文章，包含' ); ?> </th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( '对于feed中的每篇文章，包含' ); ?> </span></legend>
	<p>
		<label><input name="rss_use_excerpt" type="radio" value="0" <?php checked( 0, get_option( 'rss_use_excerpt' ) ); ?>	/> <?php _e( '全文' ); ?></label><br />
		<label><input name="rss_use_excerpt" type="radio" value="1" <?php checked( 1, get_option( 'rss_use_excerpt' ) ); ?> /> <?php _e( '摘要' ); ?></label>
	</p>
	<p class="description">
		<?php
		printf(
			/* translators: %s: Documentation URL. */
			__( '您的主题决定浏览器如何显示内容。<a href="%s">了解关于feeds的信息</a>。' ),
			__( 'https://www.gechiui.com/support/gechiui-feeds/' )
		);
		?>
	</p>
</fieldset></td>
</tr>

<tr class="option-site-visibility">
<th scope="row"><?php has_action( 'blog_privacy_selector' ) ? _e( '站点可见性' ) : _e( '对搜索引擎的可见性' ); ?> </th>
<td><fieldset><legend class="screen-reader-text"><span><?php has_action( 'blog_privacy_selector' ) ? _e( '站点可见性' ) : _e( '对搜索引擎的可见性' ); ?> </span></legend>
<?php if ( has_action( 'blog_privacy_selector' ) ) : ?>
	<input id="blog-public" type="radio" name="blog_public" value="1" <?php checked( '1', get_option( 'blog_public' ) ); ?> />
	<label for="blog-public"><?php _e( '允许搜索引擎索引本站点' ); ?></label><br/>
	<input id="blog-norobots" type="radio" name="blog_public" value="0" <?php checked( '0', get_option( 'blog_public' ) ); ?> />
	<label for="blog-norobots"><?php _e( '建议搜索引擎不索引本站点' ); ?></label>
	<p class="description"><?php _e( '注意：这些设置并不能彻底防止搜索引擎访问您的站点——具体行为还取决于它们是否遵循您的要求。' ); ?></p>
	<?php
	/**
	 * Enable the legacy '站点可见性' privacy options.
	 *
	 * By default the privacy options form displays a single checkbox to 'discourage' search
	 * engines from indexing the site. Hooking to this action serves a dual purpose:
	 *
	 * 1. Disable the single checkbox in favor of a multiple-choice list of radio buttons.
	 * 2. Open the door to adding additional radio button choices to the list.
	 *
	 * Hooking to this action also converts the '对搜索引擎的可见性' heading to the more
	 * open-ended '站点可见性' heading.
	 *
	 */
	do_action( 'blog_privacy_selector' );
	?>
<?php else : ?>
	<label for="blog_public"><input name="blog_public" type="checkbox" id="blog_public" value="0" <?php checked( '0', get_option( 'blog_public' ) ); ?> />
	<?php _e( '建议搜索引擎不索引本站点' ); ?></label>
	<p class="description"><?php _e( '搜索引擎将本着自觉自愿的原则对待GeChiUI提出的请求。并不是所有搜索引擎都会遵守这类请求。' ); ?></p>
<?php endif; ?>
</fieldset></td>
</tr>

<?php do_settings_fields( 'reading', 'default' ); ?>
</table>

<?php do_settings_sections( 'reading' ); ?>

<?php submit_button(); ?>
</form>
</div>
<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>
