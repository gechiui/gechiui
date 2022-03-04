<?php
/**
 * GeChiUI Administration Screen API.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

/**
 * Get the column headers for a screen
 *
 *
 *
 * @param string|GC_Screen $screen The screen you want the headers for
 * @return string[] The column header labels keyed by column ID.
 */
function get_column_headers( $screen ) {
	static $column_headers = array();

	if ( is_string( $screen ) ) {
		$screen = convert_to_screen( $screen );
	}

	if ( ! isset( $column_headers[ $screen->id ] ) ) {
		/**
		 * Filters the column headers for a list table on a specific screen.
		 *
		 * The dynamic portion of the hook name, `$screen->id`, refers to the
		 * ID of a specific screen. For example, the screen ID for the Posts
		 * list table is edit-post, so the filter for that screen would be
		 * manage_edit-post_columns.
		 *
		 *
		 * @param string[] $columns The column header labels keyed by column ID.
		 */
		$column_headers[ $screen->id ] = apply_filters( "manage_{$screen->id}_columns", array() );
	}

	return $column_headers[ $screen->id ];
}

/**
 * Get a list of hidden columns.
 *
 *
 *
 * @param string|GC_Screen $screen The screen you want the hidden columns for
 * @return string[] Array of IDs of hidden columns.
 */
function get_hidden_columns( $screen ) {
	if ( is_string( $screen ) ) {
		$screen = convert_to_screen( $screen );
	}

	$hidden = get_user_option( 'manage' . $screen->id . 'columnshidden' );

	$use_defaults = ! is_array( $hidden );

	if ( $use_defaults ) {
		$hidden = array();

		/**
		 * Filters the default list of hidden columns.
		 *
		 *
		 * @param string[]  $hidden Array of IDs of columns hidden by default.
		 * @param GC_Screen $screen GC_Screen object of the current screen.
		 */
		$hidden = apply_filters( 'default_hidden_columns', $hidden, $screen );
	}

	/**
	 * Filters the list of hidden columns.
	 *
	 *
	 * @param string[]  $hidden       Array of IDs of hidden columns.
	 * @param GC_Screen $screen       GC_Screen object of the current screen.
	 * @param bool      $use_defaults Whether to show the default columns.
	 */
	return apply_filters( 'hidden_columns', $hidden, $screen, $use_defaults );
}

/**
 * Prints the meta box preferences for screen meta.
 *
 *
 *
 * @global array $gc_meta_boxes
 *
 * @param GC_Screen $screen
 */
function meta_box_prefs( $screen ) {
	global $gc_meta_boxes;

	if ( is_string( $screen ) ) {
		$screen = convert_to_screen( $screen );
	}

	if ( empty( $gc_meta_boxes[ $screen->id ] ) ) {
		return;
	}

	$hidden = get_hidden_meta_boxes( $screen );

	foreach ( array_keys( $gc_meta_boxes[ $screen->id ] ) as $context ) {
		foreach ( array( 'high', 'core', 'default', 'low' ) as $priority ) {
			if ( ! isset( $gc_meta_boxes[ $screen->id ][ $context ][ $priority ] ) ) {
				continue;
			}

			foreach ( $gc_meta_boxes[ $screen->id ][ $context ][ $priority ] as $box ) {
				if ( false === $box || ! $box['title'] ) {
					continue;
				}

				// Submit box cannot be hidden.
				if ( 'submitdiv' === $box['id'] || 'linksubmitdiv' === $box['id'] ) {
					continue;
				}

				$widget_title = $box['title'];

				if ( is_array( $box['args'] ) && isset( $box['args']['__widget_basename'] ) ) {
					$widget_title = $box['args']['__widget_basename'];
				}

				$is_hidden = in_array( $box['id'], $hidden, true );

				printf(
					'<label for="%1$s-hide"><input class="hide-postbox-tog" name="%1$s-hide" type="checkbox" id="%1$s-hide" value="%1$s" %2$s />%3$s</label>',
					esc_attr( $box['id'] ),
					checked( $is_hidden, false, false ),
					$widget_title
				);
			}
		}
	}
}

/**
 * Gets an array of IDs of hidden meta boxes.
 *
 *
 *
 * @param string|GC_Screen $screen Screen identifier
 * @return string[] IDs of hidden meta boxes.
 */
function get_hidden_meta_boxes( $screen ) {
	if ( is_string( $screen ) ) {
		$screen = convert_to_screen( $screen );
	}

	$hidden = get_user_option( "metaboxhidden_{$screen->id}" );

	$use_defaults = ! is_array( $hidden );

	// Hide slug boxes by default.
	if ( $use_defaults ) {
		$hidden = array();

		if ( 'post' === $screen->base ) {
			if ( in_array( $screen->post_type, array( 'post', 'page', 'attachment' ), true ) ) {
				$hidden = array( 'slugdiv', 'trackbacksdiv', 'postcustom', 'postexcerpt', 'commentstatusdiv', 'commentsdiv', 'authordiv', 'revisionsdiv' );
			} else {
				$hidden = array( 'slugdiv' );
			}
		}

		/**
		 * Filters the default list of hidden meta boxes.
		 *
		 *
		 * @param string[]  $hidden An array of IDs of meta boxes hidden by default.
		 * @param GC_Screen $screen GC_Screen object of the current screen.
		 */
		$hidden = apply_filters( 'default_hidden_meta_boxes', $hidden, $screen );
	}

	/**
	 * Filters the list of hidden meta boxes.
	 *
	 *
	 * @param string[]  $hidden       An array of IDs of hidden meta boxes.
	 * @param GC_Screen $screen       GC_Screen object of the current screen.
	 * @param bool      $use_defaults Whether to show the default meta boxes.
	 *                                Default true.
	 */
	return apply_filters( 'hidden_meta_boxes', $hidden, $screen, $use_defaults );
}

/**
 * Register and configure an admin screen option
 *
 *
 *
 * @param string $option An option name.
 * @param mixed  $args   Option-dependent arguments.
 */
function add_screen_option( $option, $args = array() ) {
	$current_screen = get_current_screen();

	if ( ! $current_screen ) {
		return;
	}

	$current_screen->add_option( $option, $args );
}

/**
 * Get the current screen object
 *
 *
 *
 * @global GC_Screen $current_screen GeChiUI current screen object.
 *
 * @return GC_Screen|null Current screen object or null when screen not defined.
 */
function get_current_screen() {
	global $current_screen;

	if ( ! isset( $current_screen ) ) {
		return null;
	}

	return $current_screen;
}

/**
 * Set the current screen object
 *
 *
 *
 * @param string|GC_Screen $hook_name Optional. The hook name (also known as the hook suffix) used to determine the screen,
 *                                    or an existing screen object.
 */
function set_current_screen( $hook_name = '' ) {
	GC_Screen::get( $hook_name )->set_current_screen();
}
