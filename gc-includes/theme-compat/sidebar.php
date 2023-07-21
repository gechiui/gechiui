<?php
/**
 * @package GeChiUI
 * @subpackage Theme_Compat
 * @deprecated 3.0.0
 *
 * This file is here for backward compatibility with old themes and will be removed in a future version.
 */
_deprecated_file(
	/* translators: %s: Template name. */
	sprintf( __( '没有%s的主题' ), basename( __FILE__ ) ),
	'3.0.0',
	null,
	/* translators: %s: Template name. */
	sprintf( __( '请在您的主题中包含一个%s模板。' ), basename( __FILE__ ) )
);
?>
	<div id="sidebar" role="complementary">
		<ul>
			<?php
			/* Widgetized sidebar, if you have the plugin installed. */
			if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar() ) :
				?>
			<li>
				<?php get_search_form(); ?>
			</li>

			<!-- Author information is disabled per default. Uncomment and fill in your details if you want to use it.
			<li><h2><?php _e( '作者' ); ?></h2>
			<p>A little something about you, the author. Nothing lengthy, just an overview.</p>
			</li>
			-->

				<?php
				if ( is_404() || is_category() || is_day() || is_month() ||
				is_year() || is_search() || is_paged() ) :
					?>
			<li>

					<?php if ( is_404() ) : /* If this is a 404 page */ ?>
			<?php elseif ( is_category() ) : /* If this is a category archive */ ?>
				<p>
				<?php
					printf(
						/* translators: %s: Category name. */
						__( '您现在正在浏览%s的分类归档。' ),
						single_cat_title( '', false )
					);
				?>
				</p>

			<?php elseif ( is_day() ) : /* If this is a daily archive */ ?>
				<p>
				<?php
					printf(
						/* translators: 1: Site link, 2: Archive date. */
						__( '您正在浏览%1$s博客在%2$s的归档。' ),
						sprintf( '<a href="%1$s/">%2$s</a>', get_bloginfo( 'url' ), get_bloginfo( 'name' ) ),
						get_the_time( __( 'Y年n月j日' ) )
					);
				?>
				</p>

			<?php elseif ( is_month() ) : /* If this is a monthly archive */ ?>
				<p>
				<?php
					printf(
						/* translators: 1: Site link, 2: Archive month. */
						__( '您正在浏览%1$s博客的%2$s归档。' ),
						sprintf( '<a href="%1$s/">%2$s</a>', get_bloginfo( 'url' ), get_bloginfo( 'name' ) ),
						get_the_time( __( 'Y年n月' ) )
					);
				?>
				</p>

			<?php elseif ( is_year() ) : /* If this is a yearly archive */ ?>
				<p>
				<?php
					printf(
						/* translators: 1: Site link, 2: Archive year. */
						__( '您正在浏览%1$s博客的%2$s年归档。' ),
						sprintf( '<a href="%1$s/">%2$s</a>', get_bloginfo( 'url' ), get_bloginfo( 'name' ) ),
						get_the_time( 'Y' )
					);
				?>
				</p>

			<?php elseif ( is_search() ) : /* If this is a search result */ ?>
				<p>
				<?php
					printf(
						/* translators: 1: Site link, 2: Search query. */
						__( '您在%1$s博客归档中搜索了<strong>“%2$s”</strong>。如果您没有在搜索结果中找到有用的内容，请试试这些链接。' ),
						sprintf( '<a href="%1$s/">%2$s</a>', get_bloginfo( 'url' ), get_bloginfo( 'name' ) ),
						esc_html( get_search_query() )
					);
				?>
				</p>

			<?php elseif ( isset( $_GET['paged'] ) && ! empty( $_GET['paged'] ) ) : /* If this set is paginated */ ?>
				<p>
				<?php
					printf(
						/* translators: %s: Site link. */
						__( '您正在浏览%s博客归档。' ),
						sprintf( '<a href="%1$s/">%2$s</a>', get_bloginfo( 'url' ), get_bloginfo( 'name' ) )
					);
				?>
				</p>

			<?php endif; ?>

			</li>
			<?php endif; ?>
		</ul>
		<ul role="navigation">
				<?php gc_list_pages( 'title_li=<h2>' . __( '页面' ) . '</h2>' ); ?>

			<li><h2><?php _e( '归档' ); ?></h2>
				<ul>
				<?php gc_get_archives( array( 'type' => 'monthly' ) ); ?>
				</ul>
			</li>

				<?php
				gc_list_categories(
					array(
						'show_count' => 1,
						'title_li'   => '<h2>' . __( '分类' ) . '</h2>',
					)
				);
				?>
		</ul>
		<ul>
				<?php if ( is_home() || is_page() ) { /* If this is the frontpage */ ?>
					<?php gc_list_bookmarks(); ?>

				<li><h2><?php _e( '功能' ); ?></h2>
				<ul>
					<?php gc_register(); ?>
					<li><?php gc_loginout(); ?></li>
					<?php gc_meta(); ?>
				</ul>
				</li>
			<?php } ?>

			<?php endif; /* ! dynamic_sidebar() */ ?>
		</ul>
	</div>
