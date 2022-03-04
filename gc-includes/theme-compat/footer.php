<?php
/**
 * @package GeChiUI
 * @subpackage Theme_Compat
 * @deprecated 3.0.0
 *
 * This file is here for backward compatibility with old themes and will be removed in a future version
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

<hr />
<div id="footer" role="contentinfo">
<!-- If you'd like to support GeChiUI, having the "powered by" link somewhere on your blog is the best way; it's our only promotion or advertising. -->
	<p>
		<?php
		printf(
			/* translators: 1: Blog name, 2: GeChiUI */
			__( '%1$s自豪地采用%2$s构建' ),
			get_bloginfo( 'name' ),
			'<a href="https://www.gechiui.com/">GeChiUI</a>'
		);
		?>
	</p>
</div>
</div>

<!-- Gorgeous design by Michael Heilemann - http://binarybonsai.com/ -->
<?php /* "Just what do you think you're doing Dave?" */ ?>

		<?php gc_footer(); ?>
</body>
</html>
