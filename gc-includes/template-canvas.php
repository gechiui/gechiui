<?php
/**
 * Template canvas file to render the current 'gc_template'.
 *
 * @package GeChiUI
 */

/*
 * Get the template HTML.
 * This needs to run before <head> so that blocks can add scripts and styles in gc_head().
 */
$template_html = get_the_block_template_html();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<?php gc_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php gc_body_open(); ?>

<?php echo $template_html; // phpcs:ignore GeChiUI.Security.EscapeOutput ?>

<?php gc_footer(); ?>
</body>
</html>
