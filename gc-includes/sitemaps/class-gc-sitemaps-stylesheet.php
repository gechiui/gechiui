<?php
/**
 * Sitemaps: GC_Sitemaps_Stylesheet class
 *
 * This class provides the XSL stylesheets to style all sitemaps.
 *
 * @package GeChiUI
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Stylesheet provider class.
 *
 * @since 5.5.0
 */
#[AllowDynamicProperties]
class GC_Sitemaps_Stylesheet {
	/**
	 * Renders the XSL stylesheet depending on whether it's the sitemap index or not.
	 *
	 * @param string $type Stylesheet type. Either 'sitemap' or 'index'.
	 */
	public function render_stylesheet( $type ) {
		header( 'Content-Type: application/xml; charset=UTF-8' );

		if ( 'sitemap' === $type ) {
			// phpcs:ignore GeChiUI.Security.EscapeOutput.OutputNotEscaped -- All content escaped below.
			echo $this->get_sitemap_stylesheet();
		}

		if ( 'index' === $type ) {
			// phpcs:ignore GeChiUI.Security.EscapeOutput.OutputNotEscaped -- All content escaped below.
			echo $this->get_sitemap_index_stylesheet();
		}

		exit;
	}

	/**
	 * Returns the escaped XSL for all sitemaps, except index.
	 *
	 * @since 5.5.0
	 */
	public function get_sitemap_stylesheet() {
		$css         = $this->get_stylesheet_css();
		$title       = esc_xml( __( 'XML系统地图' ) );
		$description = esc_xml( __( '此XML系统地图是由GeChiUI生成的，以使您的内容在搜索引擎中更加可见。' ) );
		$learn_more  = sprintf(
			'<a href="%s">%s</a>',
			esc_url( __( 'https://www.sitemaps.org/' ) ),
			esc_xml( __( '了解有关XML系统地图的更多信息。' ) )
		);

		$text = sprintf(
			/* translators: %s: Number of URLs. */
			esc_xml( __( '此XML系统地图中的URL数：%s。' ) ),
			'<xsl:value-of select="count( sitemap:urlset/sitemap:url )" />'
		);

		$lang       = get_language_attributes( 'html' );
		$url        = esc_xml( __( 'URL' ) );
		$lastmod    = esc_xml( __( '最后修改' ) );
		$changefreq = esc_xml( __( '更改频率' ) );
		$priority   = esc_xml( __( '优先级' ) );

		$xsl_content = <<<XSL
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
		version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
		exclude-result-prefixes="sitemap"
		>

	<xsl:output method="html" encoding="UTF-8" indent="yes" />

	<!--
	  Set variables for whether lastmod, changefreq or priority occur for any url in the sitemap.
	  We do this up front because it can be expensive in a large sitemap.
	  -->
	<xsl:variable name="has-lastmod"    select="count( /sitemap:urlset/sitemap:url/sitemap:lastmod )"    />
	<xsl:variable name="has-changefreq" select="count( /sitemap:urlset/sitemap:url/sitemap:changefreq )" />
	<xsl:variable name="has-priority"   select="count( /sitemap:urlset/sitemap:url/sitemap:priority )"   />

	<xsl:template match="/">
		<html {$lang}>
			<head>
				<title>{$title}</title>
				<style>
					{$css}
				</style>
			</head>
			<body>
				<div id="sitemap">
					<div id="sitemap__header">
						<h1>{$title}</h1>
						<p>{$description}</p>
						<p>{$learn_more}</p>
					</div>
					<div id="sitemap__content">
						<p class="text">{$text}</p>
						<table id="sitemap__table">
							<thead>
								<tr>
									<th class="loc">{$url}</th>
									<xsl:if test="\$has-lastmod">
										<th class="lastmod">{$lastmod}</th>
									</xsl:if>
									<xsl:if test="\$has-changefreq">
										<th class="changefreq">{$changefreq}</th>
									</xsl:if>
									<xsl:if test="\$has-priority">
										<th class="priority">{$priority}</th>
									</xsl:if>
								</tr>
							</thead>
							<tbody>
								<xsl:for-each select="sitemap:urlset/sitemap:url">
									<tr>
										<td class="loc"><a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc" /></a></td>
										<xsl:if test="\$has-lastmod">
											<td class="lastmod"><xsl:value-of select="sitemap:lastmod" /></td>
										</xsl:if>
										<xsl:if test="\$has-changefreq">
											<td class="changefreq"><xsl:value-of select="sitemap:changefreq" /></td>
										</xsl:if>
										<xsl:if test="\$has-priority">
											<td class="priority"><xsl:value-of select="sitemap:priority" /></td>
										</xsl:if>
									</tr>
								</xsl:for-each>
							</tbody>
						</table>
					</div>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>

XSL;

		/**
		 * Filters the content of the sitemap stylesheet.
		 *
		 * @since 5.5.0
		 *
		 * @param string $xsl_content Full content for the XML stylesheet.
		 */
		return apply_filters( 'gc_sitemaps_stylesheet_content', $xsl_content );
	}

	/**
	 * Returns the escaped XSL for the index sitemaps.
	 *
	 * @since 5.5.0
	 */
	public function get_sitemap_index_stylesheet() {
		$css         = $this->get_stylesheet_css();
		$title       = esc_xml( __( 'XML系统地图' ) );
		$description = esc_xml( __( '此XML系统地图是由GeChiUI生成的，以使您的内容在搜索引擎中更加可见。' ) );
		$learn_more  = sprintf(
			'<a href="%s">%s</a>',
			esc_url( __( 'https://www.sitemaps.org/' ) ),
			esc_xml( __( '了解有关XML系统地图的更多信息。' ) )
		);

		$text = sprintf(
			/* translators: %s: Number of URLs. */
			esc_xml( __( '此XML系统地图中的URL数：%s。' ) ),
			'<xsl:value-of select="count( sitemap:sitemapindex/sitemap:sitemap )" />'
		);

		$lang    = get_language_attributes( 'html' );
		$url     = esc_xml( __( 'URL' ) );
		$lastmod = esc_xml( __( '最后修改' ) );

		$xsl_content = <<<XSL
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
		version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
		exclude-result-prefixes="sitemap"
		>

	<xsl:output method="html" encoding="UTF-8" indent="yes" />

	<!--
	  Set variables for whether lastmod occurs for any sitemap in the index.
	  We do this up front because it can be expensive in a large sitemap.
	  -->
	<xsl:variable name="has-lastmod" select="count( /sitemap:sitemapindex/sitemap:sitemap/sitemap:lastmod )" />

	<xsl:template match="/">
		<html {$lang}>
			<head>
				<title>{$title}</title>
				<style>
					{$css}
				</style>
			</head>
			<body>
				<div id="sitemap">
					<div id="sitemap__header">
						<h1>{$title}</h1>
						<p>{$description}</p>
						<p>{$learn_more}</p>
					</div>
					<div id="sitemap__content">
						<p class="text">{$text}</p>
						<table id="sitemap__table">
							<thead>
								<tr>
									<th class="loc">{$url}</th>
									<xsl:if test="\$has-lastmod">
										<th class="lastmod">{$lastmod}</th>
									</xsl:if>
								</tr>
							</thead>
							<tbody>
								<xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
									<tr>
										<td class="loc"><a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc" /></a></td>
										<xsl:if test="\$has-lastmod">
											<td class="lastmod"><xsl:value-of select="sitemap:lastmod" /></td>
										</xsl:if>
									</tr>
								</xsl:for-each>
							</tbody>
						</table>
					</div>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>

XSL;

		/**
		 * Filters the content of the sitemap index stylesheet.
		 *
		 * @since 5.5.0
		 *
		 * @param string $xsl_content Full content for the XML stylesheet.
		 */
		return apply_filters( 'gc_sitemaps_stylesheet_index_content', $xsl_content );
	}

	/**
	 * Gets the CSS to be included in sitemap XSL stylesheets.
	 *
	 * @since 5.5.0
	 *
	 * @return string The CSS.
	 */
	public function get_stylesheet_css() {
		$text_align = is_rtl() ? 'right' : 'left';

		$css = <<<EOF

					body {
						font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
						color: #444;
					}

					#sitemap {
						max-width: 980px;
						margin: 0 auto;
					}

					#sitemap__table {
						width: 100%;
						border: solid 1px #ccc;
						border-collapse: collapse;
					}

			 		#sitemap__table tr td.loc {
						/*
						 * URLs should always be LTR.
						 * See https://core.trac.gechiui.com/ticket/16834
						 * and https://core.trac.gechiui.com/ticket/49949
						 */
						direction: ltr;
					}

					#sitemap__table tr th {
						text-align: {$text_align};
					}

					#sitemap__table tr td,
					#sitemap__table tr th {
						padding: 10px;
					}

					#sitemap__table tr:nth-child(odd) td {
						background-color: #eee;
					}

					a:hover {
						text-decoration: none;
					}

EOF;

		/**
		 * Filters the CSS only for the sitemap stylesheet.
		 *
		 * @since 5.5.0
		 *
		 * @param string $css CSS to be applied to default XSL file.
		 */
		return apply_filters( 'gc_sitemaps_stylesheet_css', $css );
	}
}
