<?php
/**
 * Helper functions for displaying a list of items in an ajaxified HTML table.
 *
 * @package GeChiUI
 * @subpackage List_Table
 *
 */

/**
 * Fetches an instance of a GC_List_Table class.
 *
 * @access private
 *
 *
 * @global string $hook_suffix
 *
 * @param string $class The type of the list table, which is the class name.
 * @param array  $args  Optional. Arguments to pass to the class. Accepts 'screen'.
 * @return GC_List_Table|false List table object on success, false if the class does not exist.
 */
function _get_list_table( $class, $args = array() ) {
	$core_classes = array(
		// Site Admin.
		'GC_Posts_List_Table'                         => 'posts',
		'GC_Media_List_Table'                         => 'media',
		'GC_Terms_List_Table'                         => 'terms',
		'GC_Users_List_Table'                         => 'users',
		'GC_Comments_List_Table'                      => 'comments',
		'GC_Post_Comments_List_Table'                 => array( 'comments', 'post-comments' ),
		'GC_Links_List_Table'                         => 'links',
		'GC_Plugin_Install_List_Table'                => 'plugin-install',
		'GC_Themes_List_Table'                        => 'themes',
		'GC_Theme_Install_List_Table'                 => array( 'themes', 'theme-install' ),
		'GC_Plugins_List_Table'                       => 'plugins',
		'GC_AppKeys_List_Table'         => 'appkeys',

		// Network Admin.
		'GC_MS_Sites_List_Table'                      => 'ms-sites',
		'GC_MS_Users_List_Table'                      => 'ms-users',
		'GC_MS_Themes_List_Table'                     => 'ms-themes',

		// Privacy requests tables.
		'GC_Privacy_Data_Export_Requests_List_Table'  => 'privacy-data-export-requests',
		'GC_Privacy_Data_Removal_Requests_List_Table' => 'privacy-data-removal-requests',
	);

	if ( isset( $core_classes[ $class ] ) ) {
		foreach ( (array) $core_classes[ $class ] as $required ) {
			require_once ABSPATH . 'gc-admin/includes/class-gc-' . $required . '-list-table.php';
		}

		if ( isset( $args['screen'] ) ) {
			$args['screen'] = convert_to_screen( $args['screen'] );
		} elseif ( isset( $GLOBALS['hook_suffix'] ) ) {
			$args['screen'] = get_current_screen();
		} else {
			$args['screen'] = null;
		}

		return new $class( $args );
	}

	return false;
}

/**
 * Register column headers for a particular screen.
 *
 * @see get_column_headers(), print_column_headers(), get_hidden_columns()
 *
 *
 *
 * @param string    $screen The handle for the screen to register column headers for. This is
 *                          usually the hook name returned by the `add_*_page()` functions.
 * @param string[] $columns An array of columns with column IDs as the keys and translated
 *                          column names as the values.
 */
function register_column_headers( $screen, $columns ) {
	new _GC_List_Table_Compat( $screen, $columns );
}

/**
 * Prints column headers for a particular screen.
 *
 *
 *
 * @param string|GC_Screen $screen  The screen hook name or screen object.
 * @param bool             $with_id Whether to set the ID attribute or not.
 */
function print_column_headers( $screen, $with_id = true ) {
	$gc_list_table = new _GC_List_Table_Compat( $screen );

	$gc_list_table->print_column_headers( $with_id );
}
