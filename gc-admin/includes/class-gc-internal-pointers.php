<?php
/**
 * Administration API: GC_Internal_Pointers class
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Core class used to implement an internal admin pointers API.
 *
 */
#[AllowDynamicProperties]
final class GC_Internal_Pointers {
	/**
	 * Initializes the new feature pointers.
	 *
	 *
	 * All pointers can be disabled using the following:
	 *     remove_action( 'admin_enqueue_scripts', array( 'GC_Internal_Pointers', 'enqueue_scripts' ) );
	 *
	 * Individual pointers (e.g. gc390_widgets) can be disabled using the following:
	 *
	 *    function yourprefix_remove_pointers() {
	 *        remove_action(
	 *            'admin_print_footer_scripts',
	 *            array( 'GC_Internal_Pointers', 'pointer_gc390_widgets' )
	 *        );
	 *    }
	 *    add_action( 'admin_enqueue_scripts', 'yourprefix_remove_pointers', 11 );
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public static function enqueue_scripts( $hook_suffix ) {
		/*
		 * Register feature pointers
		 *
		 * Format:
		 *     array(
		 *         hook_suffix => pointer callback
		 *     )
		 *
		 * Example:
		 *     array(
		 *         'themes.php' => 'gc390_widgets'
		 *     )
		 */
		$registered_pointers = array(
			// None currently.
		);

		// Check if screen related pointer is registered.
		if ( empty( $registered_pointers[ $hook_suffix ] ) ) {
			return;
		}

		$pointers = (array) $registered_pointers[ $hook_suffix ];

		/*
		 * Specify required capabilities for feature pointers
		 *
		 * Format:
		 *     array(
		 *         pointer callback => Array of required capabilities
		 *     )
		 *
		 * Example:
		 *     array(
		 *         'gc390_widgets' => array( 'edit_theme_options' )
		 *     )
		 */
		$caps_required = array(
			// None currently.
		);

		// Get dismissed pointers.
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_gc_pointers', true ) );

		$got_pointers = false;
		foreach ( array_diff( $pointers, $dismissed ) as $pointer ) {
			if ( isset( $caps_required[ $pointer ] ) ) {
				foreach ( $caps_required[ $pointer ] as $cap ) {
					if ( ! current_user_can( $cap ) ) {
						continue 2;
					}
				}
			}

			// Bind pointer print function.
			add_action( 'admin_print_footer_scripts', array( 'GC_Internal_Pointers', 'pointer_' . $pointer ) );
			$got_pointers = true;
		}

		if ( ! $got_pointers ) {
			return;
		}

		// Add pointers script and style to queue.
		gc_enqueue_style( 'gc-pointer' );
		gc_enqueue_script( 'gc-pointer' );
	}

	/**
	 * Prints the pointer JavaScript data.
	 *
	 *
	 * @param string $pointer_id The pointer ID.
	 * @param string $selector The HTML elements, on which the pointer should be attached.
	 * @param array  $args Arguments to be passed to the pointer JS (see gc-pointer.js).
	 */
	private static function print_js( $pointer_id, $selector, $args ) {
		if ( empty( $pointer_id ) || empty( $selector ) || empty( $args ) || empty( $args['content'] ) ) {
			return;
		}

		?>
		<script type="text/javascript">
		(function($){
			var options = <?php echo gc_json_encode( $args ); ?>, setup;

			if ( ! options )
				return;

			options = $.extend( options, {
				close: function() {
					$.post( ajaxurl, {
						pointer: '<?php echo $pointer_id; ?>',
						action: 'dismiss-gc-pointer'
					});
				}
			});

			setup = function() {
				$('<?php echo $selector; ?>').first().pointer( options ).pointer('open');
			};

			if ( options.position && options.position.defer_loading )
				$(window).bind( 'load.gc-pointers', setup );
			else
				$( function() {
					setup();
				} );

		})( jQuery );
		</script>
		<?php
	}

	public static function pointer_gc330_toolbar() {}
	public static function pointer_gc330_media_uploader() {}
	public static function pointer_gc330_saving_widgets() {}
	public static function pointer_gc340_customize_current_theme_link() {}
	public static function pointer_gc340_choose_image_from_library() {}
	public static function pointer_gc350_media() {}
	public static function pointer_gc360_revisions() {}
	public static function pointer_gc360_locks() {}
	public static function pointer_gc390_widgets() {}
	public static function pointer_gc410_dfw() {}
	public static function pointer_gc496_privacy() {}

	/**
	 * Prevents new users from seeing existing 'new feature' pointers.
	 *
	 *
	 * @param int $user_id User ID.
	 */
	public static function dismiss_pointers_for_new_users( $user_id ) {
		add_user_meta( $user_id, 'dismissed_gc_pointers', '' );
	}
}
