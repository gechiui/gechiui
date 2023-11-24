<?php
/**
 * GeChiUI Administration Template Footer
 *
 * @package GeChiUI
 * @subpackage Administration
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @global string $hook_suffix
 */
global $hook_suffix;
?>

<div class="clear"></div></div><!-- gcbody-content -->
<div class="clear"></div></div><!-- gcbody -->
<div class="clear"></div>

<footer id="gcfooter" role="contentinfo">
	<?php
	/**
	 * Fires after the opening tag for the admin footer.
	 *
	 */
	do_action( 'in_admin_footer' );
	?>
	<p id="footer-left" class="alignleft">
		<?php
		$text = sprintf(
			/* translators: %s: https://www.gechiui.com/ */
			__( '感谢使用 <a href="%s">GeChiUI</a> 进行创作。' ),
			__( 'https://www.gechiui.com/' )
		);

		/**
		 * Filters the "Thank you" text displayed in the admin footer.
		 *
		 * @since 2.8.0
		 *
		 * @param string $text The content that will be printed.
		 */
		echo apply_filters( 'admin_footer_text', '<span id="footer-thankyou">' . $text . '</span>' );
		?>
	</p>
	<p id="footer-upgrade" class="alignright m-r-20">
		<?php
		/**
		 * Filters the version/update text displayed in the admin footer.
		 *
		 * GeChiUI prints the current version and update information,
		 * using core_update_footer() at priority 10.
		 *
		 * @since 2.3.0
		 *
		 * @see core_update_footer()
		 *
		 * @param string $content The content that will be printed.
		 */
		echo apply_filters( 'update_footer', '' );
		?>
	</p>
	<div class="clear"></div>
</footer>
</div><!-- gccontent -->
<?php
/**
 * Prints scripts or data before the default footer scripts.
 *
 * @param string $data The data to print.
 */
do_action( 'admin_footer', '' );

/**
 * Prints scripts and data queued for the footer.
 *
 * The dynamic portion of the hook name, `$hook_suffix`,
 * refers to the global hook suffix of the current page.
 *
 */
do_action( "admin_print_footer_scripts-{$hook_suffix}" ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

/**
 * Prints any scripts and data queued for the footer.
 *
 */
do_action( 'admin_print_footer_scripts' );

/**
 * Prints scripts or data after the default footer scripts.
 *
 * The dynamic portion of the hook name, `$hook_suffix`,
 * refers to the global hook suffix of the current page.
 *
 */
do_action( "admin_footer-{$hook_suffix}" ); // phpcs:ignore GeChiUI.NamingConventions.ValidHookName.UseUnderscores

// get_site_option() won't exist when auto upgrading from <= 2.7.
if ( function_exists( 'get_site_option' )
	&& false === get_site_option( 'can_compress_scripts' )
) {
	compression_test();
}

?>

<div class="clear"></div></div><!-- gcwrap -->
<script type="text/javascript">if(typeof gcOnload==='function')gcOnload();</script>
</body>
</html>
