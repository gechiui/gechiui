<?php
/**
 * GeChiUI Options Header.
 *
 * Displays updated message, if updated variable is part of the URL query.
 *
 * @package GeChiUI
 * @subpackage Administration
 */

gc_reset_vars( array( 'action' ) );

if ( isset( $_GET['updated'] ) && isset( $_GET['page'] ) ) {
	// For back-compat with plugins that don't use the Settings API and just set updated=1 in the redirect.
	add_settings_error( 'general', 'settings_updated', __( '设置已保存。' ), 'success' );
}
