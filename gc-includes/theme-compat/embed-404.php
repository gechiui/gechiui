<?php
/**
 * Contains the post embed content template part
 *
 * When a post is embedded in an iframe, this file is used to create the content template part
 * output if the active theme does not include an embed-404.php template.
 *
 * @package GeChiUI
 * @subpackage Theme_Compat
 *
 */
?>
<div class="gc-embed">
	<p class="gc-embed-heading"><?php _e( '很抱歉，未能找到此嵌入。' ); ?></p>

	<div class="gc-embed-excerpt">
		<p>
			<?php
			printf(
				/* translators: %s: A link to the embedded site. */
				__( '看起来在这个位置什么都没找到，试试直接访问%s？' ),
				'<strong><a href="' . esc_url( home_url() ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a></strong>'
			);
			?>
		</p>
	</div>

	<?php
	/** This filter is documented in gc-includes/theme-compat/embed-content.php */
	do_action( 'embed_content' );
	?>

	<div class="gc-embed-footer">
		<?php the_embed_site_title(); ?>
	</div>
</div>
