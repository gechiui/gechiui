<?php
/**
 * Contains the post embed header template
 *
 * When a post is embedded in an iframe, this file is used to create the header output
 * if the active theme does not include a header-embed.php template.
 *
 * @package GeChiUI
 * @subpackage Theme_Compat
 *
 */

if ( ! headers_sent() ) {
	header( 'X-GC-embed: true' );
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<title><?php echo gc_get_document_title(); ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<?php
	/**
	 * Prints scripts or data in the embed template head tag.
	 *
	 */
	do_action( 'embed_head' );
	?>
</head>
<body <?php body_class(); ?>>
