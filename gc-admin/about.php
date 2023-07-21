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

		<div class="about__header">
			<div class="about__header-title">
				<h1>
					<?php _e( 'GeChiUI' ); ?>
					<span class="screen-reader-text"><?php echo $display_version; ?></span>
				</h1>
			</div>

			<div class="about__header-text">
				<?php _e( '按需使用区块构建站点' ); ?>
			</div>

			<nav class="about__header-navigation nav-tab-wrapper gc-clearfix" aria-label="<?php esc_attr_e( '次要菜单' ); ?>">
				<a href="about.php" class="nav-tab nav-tab-active" aria-current="page"><?php _e( '更新内容' ); ?></a>
				<a href="freedoms.php" class="nav-tab"><?php _e( '您的自由' ); ?></a>
				<a href="privacy.php" class="nav-tab"><?php _e( '隐私' ); ?></a>
			</nav>
		</div>

		<hr class="is-large" />

		<div class="about__section">
			<h2 class="aligncenter">
				<?php _e( '全站编辑功能正式上线' ); ?>
			</h2>
			<p class="aligncenter is-subheading">
				<?php _e( '全站编辑功能使您能够在 GeChiUI 管理仪表盘中对整个站点的外观进行控制。' ); ?>
			</p>
		</div>

		<hr />

		<div class="about__section has-2-columns has-gutters is-wider-left">
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="<?php echo esc_url( assets_url( '/images/gcoa.png' ) ); ?>" alt="" />
			</div>
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( '格尺OA办公主题' ); ?>
				</h3>
				<p>
					<?php _e( '这是 GeChiUI 有史以来的第一个默认区块主题。 它不仅是一个新的默认主题， 也是使用 GeChiUI 主题的一种全新方式。' ); ?>
				</p>
				<p>
					<?php _e( '区块主题为您提供了大量的视觉化选择，从配色方案到字体组合在到页面模板和图片筛选，全部都可通过站点编辑器进行变更。由于只需要在一个地方进行修改，您可以为格尺OA办公主题提供与自有品牌或其他网站相同的外观和风格，或将您站点的外观转向另一方向。' ); ?>
				</p>
				<?php if ( current_user_can( 'switch_themes' ) ) : ?>
				<p>
					<?php
					printf(
						/* translators: %s: Link to Themes screen. */
						__( '格尺OA办公主题已可供使用，此主题会与 GeChiUI 6.0 一并安装，可在<a href="%s">已安装的主题</a>页面中找到。' ),
						admin_url( 'themes.php' )
					);
					?>
				</p>
				<?php endif; ?>
			</div>
		</div>

		<div class="about__section has-2-columns has-gutters is-wider-right">
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( '构建站点，创意无限' ); ?>
				</h3>
				<p>
					<?php _e( '随着格尺OA办公主题的发布，更多支持全站编辑的区块主题也会陆续上架到主题目录中，并等待用户进行深入探索。敬请期待将来发布的更多区块主题。' ); ?>
				</p>
				<p>
					<?php _e( '使用任意一个区块主题后，您便不再需要外观定制器；取而代之的是站点编辑器中、样式界面中的全部功能。使用这些区块就像在飞鸟主题中一样，您可以按需为站点构建合适的外观和风格，并在流畅、顺手的界面中使用所需的工具完成工作。' ); ?>
				</p>
			</div>
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.9/global-styles.png" alt="" />
			</div>
		</div>

		<div class="about__section has-2-columns has-gutters is-wider-left">
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.9/navigation-block.png" alt="" />
			</div>
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( '导航区块' ); ?>
				</h3>
				<p>
					<?php _e( '此次用户体验的核心，将区块引入到站点导航栏。' ); ?>
				</p>
				<p>
					<?php _e( '全新的导航区块提供多种选项供您选择，例如始终展开的响应式菜单或适应用户屏幕尺寸的菜单。无论创建哪种菜单，即使是使用全新模板或切换主题后，都可以在所需的位置重复使用。' ); ?>
				</p>
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section">
			<h2 class="aligncenter">
				<?php _e( '更多改进和更新' ); ?>
			</h2>
			<p class="aligncenter is-subheading">
				<?php _e( '您喜欢写博文或创作内容吗？新调整的发布流程可帮助您说得更多、更快。' ); ?>
			</p>
		</div>

		<hr />

		<div class="about__section has-2-columns has-gutters is-wider-left">
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.9/block-controls.png" alt="" />
			</div>
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( '更细致的区块控制' ); ?>
				</h3>
				<p>
					<?php _e( 'GeChiUI 5.9 具有新的排版工具、灵活的布局控制以及对间距、边框等细节的精细控制，不仅有助于您设计外观，甚至能够对所需细节进行调整。' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns has-gutters is-wider-right">
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( '区块样板的力量' ); ?>
				</h3>
				<p>
					<?php _e( 'GeChiUI 样板目录是各种区块样板的家园，这些样板能够为您节省时间并增加站点的核心功能，您可以根据需要对这些区块样板进行编辑。若需要为当前的主题更换不同的页眉或页脚，只需点击几下便可焕然一新。' ); ?>
				</p>
				<p>
					<?php _e( '通过具有全屏视图的区块样板浏览程序，您便可聚焦到精细的细节，从而能够轻松的比较并从中选择所需的区块样板。' ); ?>
				</p>
			</div>
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.9/pattern-explorer.png" alt="" />
			</div>
		</div>

		<div class="about__section has-2-columns has-gutters is-wider-left">
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.9/list-view.png" alt="" />
			</div>
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( '经过改进的列表视图' ); ?>
				</h3>
				<p>
					<?php _e( '5.9 版本中的列表视图可让您通过拖放区块内容的方式，将区块移至所需的位置。 管理复杂文件变得更简单，简易的控制项让您在构建站点时能够对区段进行展开和折叠，并将可帮助用户浏览页面的 HTML 锚点添加至区块中。' ); ?>
				</p>
			</div>
		</div>

		<div class="about__section has-2-columns has-gutters is-wider-right">
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( '更好用的画廊区块' ); ?>
				</h3>
				<p>
					<?php _e( '使用在图片区块中处理图片的相同方式处理画廊区块中的每张图片。' ); ?>
				</p>
				<p>
					<?php _e( '在画廊中，每张图片的样式都与其他图片不同（例如不同的剪裁或双色调），或使其完全相同，亦可通过拖放的方式来改变布局。' ); ?>
				</p>
			</div>
			<div class="column about__image is-vertically-aligned-center is-edge-to-edge">
				<img src="https://s.w.org/images/core/5.9/gallery-block.png" alt="" />
			</div>
		</div>

		<hr class="is-large" />

		<div class="about__section">
			<h2 class="aligncenter" style="margin-bottom:0;">
				<?php
				printf(
					/* translators: %s: Version number. */
					__( 'GeChiUI %s 开发者注事' ),
					$display_version
				);
				?>
			</h2>
		</div>

		<div class="about__section has-gutters has-2-columns">
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( '引入区块主题' ); ?>
				</h3>
				<p>
					<?php
					printf(
						/* translators: %s: Block-based themes dev note link. */
						__( '构建主题的全新方式，便是使用区块定义整个站点布局模板的区块主题，而 HTML 和 theme.json 中的自定义样式定义了全新的模板和模板组件。 预了解详情，请参阅 <a href="%s">区块主题开发说明</a>。' ),
						'https://make.gechiui.com/core/2022/01/04/block-themes-a-new-way-to-build-themes-in-gechiui-5-9/'
					);
					?>
				</p>
			</div>
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( '区块支持多重样式' ); ?>
				</h3>
				<p>
					<?php
					printf(
						/* translators: %s: Multiple stylesheets dev note link. */
						__( '现在，您可为每个区块注册一个以上的样式表，一可应用此特性在您自行编写的区块间共享样式，或者为个别区块加载样式，且只有在区块被使用时，样式才会被加载。 了解关于 <a href="%s">在单个区块中使用多重样式</a>的更多信息。' ),
						'https://make.gechiui.com/core/2021/12/15/using-multiple-stylesheets-per-block/'
					);
					?>
				</p>
			</div>
		</div>
		<div class="about__section has-gutters has-2-columns">
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( '区块‑层级锁定' ); ?>
				</h3>
				<p>
					<?php _e( '至此版本开始，您可将区块样板中的任意区块进行锁定，只需在 block.json 的设置中加入 lock 属性，让样板的其他部分自由适应站点内容。' ); ?>
				</p>
			</div>
			<div class="column is-edge-to-edge">
				<h3>
					<?php _e( '经过重构的画廊区块' ); ?>
				</h3>
				<p>
					<?php
					printf(
						/* translators: %s: Gallery Refactor dev note link. */
						__( '前文列出的对画廊区块的更改是一个近乎完全重构的结果，在开发与画廊区块功能相关的插件或主题前，请务必阅读<a href="%s">画廊区块兼容性开发说明</a>。' ),
						'https://make.gechiui.com/core/2021/08/20/gallery-block-refactor-dev-note/'
					);
					?>
				</p>
			</div>
		</div>

		<hr class="is-large" />

		<hr class="is-large" />

		<div class="about__section">
			<div class="column">
				<h3><?php _e( '查阅详解指南以了解更多信息' ); ?></h3>
				<p>
					<?php
					printf(
						/* translators: %s: GeChiUI 5.9 Field Guide link. */
						__( '请查阅最新版本的 GeChiUI 详解指南。这份详解指南强调了您可能需要了解的每个更改的开发者注意事项。《<a href="%s">GeChiUI 5.9 详解指南</a>》' ),
						'https://make.gechiui.com/core/2022/01/10/gechiui-5-9-field-guide/'
					);
					?>
				</p>
			</div>
		</div>

		<hr />

		<div class="return-to-dashboard">
			<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
				<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
					<?php is_multisite() ? _e( '转到“更新”页面' ) : _e( '转到“仪表盘” &rarr; “更新”页面' ); ?>
				</a> |
			<?php endif; ?>
			<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? _e( '转到“仪表盘” &rarr; “首页”页面' ) : _e( '转到“仪表盘”页面' ); ?></a>
		</div>
	</div>

<?php require_once ABSPATH . 'gc-admin/admin-footer.php'; ?>

<?php

// These are strings we may use to describe maintenance/security releases, where we aim for no new strings.
return;

__( '维护更新' );
__( '维护更新' );

__( '安全更新' );
__( '安全更新' );

__( '维护和安全更新' );
__( '维护和安全更新' );

/* translators: %s: GeChiUI version number. */
__( '<strong>版本%s</strong>修正了一个安全问题。' );
/* translators: %s: GeChiUI version number. */
__( '<strong>版本%s</strong>修正了一些安全问题。' );

/* translators: 1: GeChiUI version number, 2: Plural number of bugs. */
_n_noop(
	'<strong>%1$s版本</strong>修复了%2$s个问题。',
	'<strong>%1$s版本</strong>修复了%2$s个问题。'
);

/* translators: 1: GeChiUI version number, 2: Plural number of bugs. Singular security issue. */
_n_noop(
	'<strong>%1$s版本</strong>修补了安全漏洞、修正了%2$s个问题。',
	'<strong>%1$s版本</strong>修补了安全漏洞、修正了%2$s个问题。'
);

/* translators: 1: GeChiUI version number, 2: Plural number of bugs. More than one security issue. */
_n_noop(
	'<strong>%1$s版本</strong>修补了安全漏洞、修正了%2$s个问题。',
	'<strong>%1$s版本</strong>修补了安全漏洞、修正了%2$s个问题。'
);

/* translators: %s: Documentation URL. */
__( '欲了解更多，参见<a href="%s">发行注记</a>。' );
